import 'package:file_picker/file_picker.dart';
import 'package:flutter/material.dart';

import '../config/app_config.dart';
import '../config/app_theme.dart';
import '../models/booking.dart';
import '../models/invoice.dart';
import '../models/payment.dart';
import '../models/user.dart';
import '../services/api_service.dart';
import '../utils/formatters.dart';
import '../widgets/app_empty_state.dart';
import '../widgets/app_section_header.dart';
import '../widgets/app_stat_card.dart';
import '../widgets/app_status_badge.dart';
import '../widgets/booking_card.dart';
import 'login_screen.dart';
import 'order_page.dart';
import 'service_catalog_page.dart';

class CustomerDashboard extends StatefulWidget {
  final User initialUser;

  const CustomerDashboard({super.key, required this.initialUser});

  @override
  State<CustomerDashboard> createState() => _CustomerDashboardState();
}

class _CustomerDashboardState extends State<CustomerDashboard> {
  int _selectedIndex = 0;
  late User _user;
  List<Booking> _bookings = [];
  List<Invoice> _invoices = [];
  bool _isLoading = true;
  String _historyFilter = 'all';

  @override
  void initState() {
    super.initState();
    _user = widget.initialUser;
    _loadData();
  }

  Future<void> _loadData() async {
    setState(() => _isLoading = true);
    try {
      final results = await Future.wait([
        ApiService.getProfile(),
        ApiService.getBookings(),
        ApiService.getInvoices(),
      ]);

      if (!mounted) return;
      setState(() {
        _user = results[0] as User;
        _bookings = results[1] as List<Booking>;
        _invoices = results[2] as List<Invoice>;
        _isLoading = false;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() => _isLoading = false);
      _showError(e.toString());
    }
  }

  Future<void> _logout() async {
    await ApiService.logout();
    if (!mounted) return;
    Navigator.pushAndRemoveUntil(
      context,
      MaterialPageRoute(builder: (_) => const LoginScreen()),
      (_) => false,
    );
  }

  void _showError(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: AppColors.danger,
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(AppRadius.sm),
        ),
      ),
    );
  }

  void _showSuccess(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: AppColors.success,
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(AppRadius.sm),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final pages = [
      _homePage(),
      OrderPage(
        onCreated: () {
          _loadData();
          setState(() => _selectedIndex = 0);
        },
      ),
      _historyPage(),
      _profilePage(),
    ];

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: Row(
          children: [
            Container(
              width: 34,
              height: 34,
              decoration: BoxDecoration(
                color: AppColors.primaryLight,
                borderRadius: BorderRadius.circular(AppRadius.sm),
              ),
              child: const Icon(
                Icons.ac_unit_rounded,
                size: 20,
                color: AppColors.primary,
              ),
            ),
            const SizedBox(width: 10),
            const Text('JASAKU'),
          ],
        ),
        actions: [
          IconButton(
            tooltip: 'Perbarui Data',
            onPressed: _loadData,
            icon: const Icon(Icons.refresh_rounded, size: 22),
          ),
          IconButton(
            tooltip: 'Keluar',
            onPressed: _logout,
            icon: const Icon(Icons.logout_rounded, size: 22),
          ),
          const SizedBox(width: 4),
        ],
      ),
      body: _isLoading
          ? const Center(
              child: CircularProgressIndicator(
                valueColor: AlwaysStoppedAnimation(AppColors.primary),
              ),
            )
          : IndexedStack(
              index: _selectedIndex,
              children: pages,
            ),

      bottomNavigationBar: NavigationBar(
        selectedIndex: _selectedIndex,
        onDestinationSelected: (index) => setState(() => _selectedIndex = index),
        destinations: const [
          NavigationDestination(
            icon: Icon(Icons.home_outlined),
            selectedIcon: Icon(Icons.home_rounded),
            label: 'Beranda',
          ),
          NavigationDestination(
            icon: Icon(Icons.add_circle_outline),
            selectedIcon: Icon(Icons.add_circle_rounded),
            label: 'Booking',
          ),
          NavigationDestination(
            icon: Icon(Icons.receipt_long_outlined),
            selectedIcon: Icon(Icons.receipt_long_rounded),
            label: 'Riwayat',
          ),
          NavigationDestination(
            icon: Icon(Icons.person_outline),
            selectedIcon: Icon(Icons.person_rounded),
            label: 'Profil',
          ),
        ],
      ),
    );
  }

  Widget _homePage() {
    final active = _bookings.where((b) => b.isActive).length;
    final completed = _bookings.where((b) => b.isCompleted).length;
    final pendingInvoices =
        _invoices.where((i) => i.status != 'paid').length;

    return RefreshIndicator(
      onRefresh: _loadData,
      color: AppColors.primary,
      child: ListView(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 20),
        children: [
          // Welcome Card
          _welcomeHero(),
          const SizedBox(height: 18),

          // Stat Cards
          Row(
            children: [
              Expanded(
                child: AppStatCard(
                  label: 'Booking Aktif',
                  value: active.toString(),
                  icon: Icons.schedule_rounded,
                  accentColor: AppColors.primary,
                  onTap: () {
                    setState(() {
                      _historyFilter = 'active';
                      _selectedIndex = 2;
                    });
                  },
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: AppStatCard(
                  label: 'Selesai',
                  value: completed.toString(),
                  icon: Icons.check_circle_outline_rounded,
                  accentColor: AppColors.success,
                  onTap: () {
                    setState(() {
                      _historyFilter = 'completed';
                      _selectedIndex = 2;
                    });
                  },
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: AppStatCard(
                  label: 'Tagihan',
                  value: pendingInvoices.toString(),
                  icon: Icons.receipt_outlined,
                  accentColor: AppColors.warning,
                  onTap: () {
                    setState(() {
                      _historyFilter = 'invoices';
                      _selectedIndex = 2;
                    });
                  },
                ),
              ),
            ],
          ),
          const SizedBox(height: 24),

          // Katalog Layanan
          AppSectionHeader(
            title: 'Katalog Layanan',
            actionLabel: 'Lihat Semua',
            onAction: () => _openCatalog(),
          ),
          Row(
            children: [
              Expanded(
                child: _catalogShortcut(
                  icon: Icons.ac_unit_rounded,
                  label: 'Servis AC',
                  category: 'AC',
                  color: AppColors.primary,
                  background: AppColors.primaryLight,
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: _catalogShortcut(
                  icon: Icons.smartphone_rounded,
                  label: 'Servis Gadget',
                  category: 'Gadget',
                  color: AppColors.success,
                  background: AppColors.successLight,
                ),
              ),
            ],
          ),
          const SizedBox(height: 24),

          // Recent Bookings Section
          AppSectionHeader(
            title: 'Booking Terbaru',
            count: _bookings.length,
            actionLabel: _bookings.isNotEmpty ? 'Lihat Semua' : null,
            onAction: () => setState(() => _selectedIndex = 2),
          ),
          if (_bookings.isEmpty)
            AppEmptyState(
              icon: Icons.calendar_month_outlined,
              title: 'Belum Ada Booking',
              description:
                  'Pesan layanan cuci, perbaikan, atau bongkar pasang AC dengan mudah dan transparan.',
              actionLabel: 'Pesan Servis Sekarang',
              onAction: () => setState(() => _selectedIndex = 1),
            )
          else
            ..._bookings.take(5).map(_renderBookingCard),
        ],
      ),
    );
  }

  Widget _qrisCard() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(AppRadius.md),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        children: [
          const Text(
            'Scan QRIS untuk membayar',
            style: TextStyle(
              fontSize: 13,
              fontWeight: FontWeight.w700,
              color: AppColors.textPrimary,
            ),
          ),
          const SizedBox(height: 10),
          Image.network(
            AppConfig.qrisImageUrl,
            height: 220,
            fit: BoxFit.contain,
            errorBuilder: (_, __, ___) => const Padding(
              padding: EdgeInsets.all(12),
              child: Text(
                'Kode QRIS belum tersedia. Hubungi admin JASAKU.',
                textAlign: TextAlign.center,
                style: TextStyle(fontSize: 12, color: AppColors.textMuted),
              ),
            ),
          ),
          const SizedBox(height: 8),
          const Text(
            'Setelah membayar, unggah bukti pembayaran di bawah.',
            textAlign: TextAlign.center,
            style: TextStyle(fontSize: 11, color: AppColors.textMuted),
          ),
        ],
      ),
    );
  }

  void _openCatalog({String category = 'Semua'}) {
    Navigator.of(context).push(
      MaterialPageRoute(
        builder: (_) => ServiceCatalogPage(
          initialCategory: category,
          onCreated: () {
            _loadData();
            setState(() => _selectedIndex = 0);
          },
        ),
      ),
    );
  }

  Widget _catalogShortcut({
    required IconData icon,
    required String label,
    required String category,
    required Color color,
    required Color background,
  }) {
    return InkWell(
      borderRadius: BorderRadius.circular(AppRadius.md),
      onTap: () => _openCatalog(category: category),
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 14),
        decoration: BoxDecoration(
          color: AppColors.surface,
          borderRadius: BorderRadius.circular(AppRadius.md),
          border: Border.all(color: AppColors.border),
        ),
        child: Column(
          children: [
            Container(
              width: 44,
              height: 44,
              decoration: BoxDecoration(
                color: background,
                borderRadius: BorderRadius.circular(AppRadius.md),
              ),
              child: Icon(icon, color: color),
            ),
            const SizedBox(height: 8),
            Text(
              label,
              style: const TextStyle(
                fontSize: 13,
                fontWeight: FontWeight.w600,
                color: AppColors.textPrimary,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _welcomeHero() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(AppRadius.lg),
        border: Border.all(color: AppColors.border, width: 1),
      ),
      child: Row(
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    const Icon(
                      Icons.waving_hand_outlined,
                      size: 16,
                      color: AppColors.primary,
                    ),
                    const SizedBox(width: 6),
                    Text(
                      'Halo, ${_user.name}',
                      style: const TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.w700,
                        color: AppColors.textPrimary,
                        letterSpacing: -0.3,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 6),
                const Text(
                  'AC kurang dingin atau butuh perawatan berkala? Jadwalkan teknisi hari ini.',
                  style: TextStyle(
                    fontSize: 13,
                    color: AppColors.textSecondary,
                    height: 1.35,
                  ),
                ),
                const SizedBox(height: 14),
                SizedBox(
                  height: 38,
                  child: FilledButton.icon(
                    onPressed: () => setState(() => _selectedIndex = 1),
                    icon: const Icon(Icons.add, size: 18),
                    label: const Text(
                      'Pesan Sekarang',
                      style: TextStyle(fontSize: 13),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _historyPage() {
    final filteredBookings = _filteredHistoryBookings();

    return RefreshIndicator(
      onRefresh: _loadData,
      color: AppColors.primary,
      child: ListView(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 20),
        children: [
          const Text(
            'Riwayat Aktivitas',
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.w700,
              color: AppColors.textPrimary,
              letterSpacing: -0.3,
            ),
          ),
          const SizedBox(height: 14),

          // Filter Chips
          SingleChildScrollView(
            scrollDirection: Axis.horizontal,
            child: Row(
              children: [
                _filterChip('all', 'Semua (${_bookings.length})'),
                const SizedBox(width: 8),
                _filterChip('active', 'Aktif'),
                const SizedBox(width: 8),
                _filterChip('completed', 'Selesai'),
                const SizedBox(width: 8),
                _filterChip('cancelled', 'Dibatalkan'),
                const SizedBox(width: 8),
                _filterChip('invoices', 'Tagihan (${_invoices.length})'),
              ],
            ),
          ),
          const SizedBox(height: 18),

          if (_historyFilter == 'invoices') ...[
            if (_invoices.isEmpty)
              const AppEmptyState(
                icon: Icons.receipt_long_outlined,
                title: 'Tidak Ada Tagihan',
                description: 'Semua tagihan layanan AC Anda akan muncul di sini.',
              )
            else
              ..._invoices.map(_invoiceCard),
          ] else ...[
            if (filteredBookings.isEmpty)
              AppEmptyState(
                icon: Icons.event_busy_outlined,
                title: 'Tidak Ada Riwayat',
                description:
                    'Tidak ditemukan data booking untuk filter yang dipilih.',
                actionLabel: _historyFilter != 'all' ? 'Reset Filter' : null,
                onAction: () => setState(() => _historyFilter = 'all'),
              )
            else
              ...filteredBookings.map(_renderBookingCard),
          ],
        ],
      ),
    );
  }

  Widget _filterChip(String filterKey, String label) {
    final isSelected = _historyFilter == filterKey;
    return ChoiceChip(
      label: Text(label),
      selected: isSelected,
      onSelected: (_) => setState(() => _historyFilter = filterKey),
      selectedColor: AppColors.primaryLight,
      labelStyle: TextStyle(
        fontSize: 12,
        fontWeight: isSelected ? FontWeight.w600 : FontWeight.w500,
        color: isSelected ? AppColors.primaryDark : AppColors.textSecondary,
      ),
      backgroundColor: AppColors.surface,
      side: BorderSide(
        color: isSelected ? AppColors.primary : AppColors.border,
        width: 1,
      ),
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(AppRadius.pill),
      ),
    );
  }

  List<Booking> _filteredHistoryBookings() {
    switch (_historyFilter) {
      case 'active':
        return _bookings.where((b) => b.isActive).toList();
      case 'completed':
        return _bookings.where((b) => b.isCompleted).toList();
      case 'cancelled':
        return _bookings.where((b) => b.isCancelled).toList();
      default:
        return _bookings;
    }
  }

  Widget _renderBookingCard(Booking booking) {
    return BookingCard(
      booking: booking,
      actionButtons: [
        if (booking.isPending)
          OutlinedButton(
            onPressed: () => _showCancelBookingDialog(booking),
            style: OutlinedButton.styleFrom(
              foregroundColor: AppColors.danger,
              side: const BorderSide(color: AppColors.dangerLight, width: 1.2),
              minimumSize: const Size(0, 36),
              padding: const EdgeInsets.symmetric(horizontal: 14),
            ),
            child: const Text('Batalkan', style: TextStyle(fontSize: 12)),
          ),
        if (booking.isCompleted) ...[
          const SizedBox(width: 8),
          FilledButton.tonal(
            onPressed: () => _showRatingDialog(booking),
            style: FilledButton.styleFrom(
              backgroundColor: AppColors.primaryLight,
              foregroundColor: AppColors.primaryDark,
              minimumSize: const Size(0, 36),
              padding: const EdgeInsets.symmetric(horizontal: 14),
            ),
            child: Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                const Icon(Icons.star_rounded, size: 16, color: AppColors.warning),
                const SizedBox(width: 4),
                Text(
                  booking.hasRating ? 'Ubah Rating' : 'Beri Rating',
                  style: const TextStyle(fontSize: 12),
                ),
              ],
            ),
          ),
        ],
      ],
    );
  }

  Widget _invoiceCard(Invoice invoice) {
    final service = invoice.booking?['service'];
    final serviceName = service is Map
        ? service['name']?.toString() ?? 'Booking #${invoice.bookingId}'
        : 'Booking #${invoice.bookingId}';

    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  invoice.invoiceNumber,
                  style: const TextStyle(
                    fontSize: 15,
                    fontWeight: FontWeight.w700,
                    color: AppColors.textPrimary,
                  ),
                ),
                AppStatusBadge(
                  status: invoice.hasPendingPayment
                      ? 'pending'
                      : invoice.status,
                  isInvoice: true,
                ),
              ],
            ),
            const SizedBox(height: 6),
            Text(
              serviceName,
              style: const TextStyle(fontSize: 13, color: AppColors.textSecondary),
            ),
            const SizedBox(height: 2),
            Text(
              'Jatuh tempo: ${formatDate(invoice.dueDate)}',
              style: const TextStyle(fontSize: 12, color: AppColors.textMuted),
            ),
            const SizedBox(height: 12),
            const Divider(height: 1),
            const SizedBox(height: 12),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Total Tagihan',
                      style: TextStyle(fontSize: 11, color: AppColors.textMuted),
                    ),
                    Text(
                      formatRupiah(invoice.total),
                      style: const TextStyle(
                        fontSize: 15,
                        fontWeight: FontWeight.w700,
                        color: AppColors.textPrimary,
                      ),
                    ),
                  ],
                ),
                Row(
                  children: [
                    OutlinedButton(
                      onPressed: () => _showPaymentHistoryDialog(invoice),
                      style: OutlinedButton.styleFrom(
                        minimumSize: const Size(0, 36),
                        padding: const EdgeInsets.symmetric(horizontal: 12),
                      ),
                      child: const Text('Riwayat', style: TextStyle(fontSize: 12)),
                    ),
                    if (!invoice.isPaid && !invoice.hasPendingPayment) ...[
                      const SizedBox(width: 8),
                      FilledButton(
                        onPressed: () => _showPaymentDialog(invoice),
                        style: FilledButton.styleFrom(
                          minimumSize: const Size(0, 36),
                          padding: const EdgeInsets.symmetric(horizontal: 14),
                        ),
                        child: const Text('Bayar', style: TextStyle(fontSize: 12)),
                      ),
                    ],
                  ],
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Future<void> _showCancelBookingDialog(Booking booking) async {
    final reasonController = TextEditingController();
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Batalkan Booking'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'Apakah Anda yakin ingin membatalkan booking ${booking.service?.name ?? ''}?',
              style: const TextStyle(fontSize: 13, color: AppColors.textSecondary),
            ),
            const SizedBox(height: 14),
            TextField(
              controller: reasonController,
              maxLines: 2,
              decoration: const InputDecoration(
                labelText: 'Alasan pembatalan',
                hintText: 'Contoh: Ada keperluan mendadak',
              ),
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Kembali'),
          ),
          FilledButton(
            style: FilledButton.styleFrom(backgroundColor: AppColors.danger),
            onPressed: () {
              if (reasonController.text.trim().isEmpty) {
                _showError('Alasan pembatalan wajib diisi.');
                return;
              }
              Navigator.pop(context, true);
            },
            child: const Text('Batalkan'),
          ),
        ],
      ),
    );

    if (confirmed != true) {
      reasonController.dispose();
      return;
    }

    try {
      await ApiService.cancelBooking(booking.id, reason: reasonController.text);
      await _loadData();
      _showSuccess('Booking berhasil dibatalkan.');
    } catch (e) {
      _showError(e.toString());
    } finally {
      reasonController.dispose();
    }
  }

  Future<void> _showPaymentDialog(Invoice invoice) async {
    final amount = TextEditingController(
      text: invoice.remainingAmount > 0
          ? invoice.remainingAmount.toStringAsFixed(0)
          : invoice.total.toStringAsFixed(0),
    );
    final reference = TextEditingController();
    final notes = TextEditingController();
    var method = 'qris';
    PlatformFile? proofFile;
    String? dialogError;
    const maxProofSize = 5 * 1024 * 1024;

    final submitted = await showDialog<bool>(
      context: context,
      builder: (context) => StatefulBuilder(
        builder: (context, setDialogState) {
          void submit() {
            final parsedAmount =
                double.tryParse(amount.text.trim().replaceAll(',', '.')) ?? 0;
            final needsProof = method == 'qris';

            if (parsedAmount <= 0) {
              setDialogState(() => dialogError = 'Nominal pembayaran tidak valid.');
              return;
            }

            if (invoice.remainingAmount > 0 &&
                parsedAmount > invoice.remainingAmount) {
              setDialogState(() => dialogError = 'Nominal melebihi sisa tagihan.');
              return;
            }

            if (needsProof && (proofFile?.path == null || proofFile!.path!.isEmpty)) {
              setDialogState(() => dialogError = 'Bukti pembayaran wajib diunggah.');
              return;
            }

            Navigator.pop(context, true);
          }

          return AlertDialog(
            title: const Text('Konfirmasi Pembayaran'),
            content: SingleChildScrollView(
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  if (dialogError != null) ...[
                    Container(
                      padding: const EdgeInsets.all(8),
                      decoration: BoxDecoration(
                        color: AppColors.dangerLight,
                        borderRadius: BorderRadius.circular(AppRadius.sm),
                      ),
                      child: Text(
                        dialogError!,
                        style: const TextStyle(color: AppColors.danger, fontSize: 12),
                      ),
                    ),
                    const SizedBox(height: 10),
                  ],
                  TextField(
                    controller: amount,
                    keyboardType: TextInputType.number,
                    decoration: const InputDecoration(labelText: 'Nominal Bayar'),
                  ),
                  const SizedBox(height: 12),
                  DropdownButtonFormField<String>(
                    initialValue: method,
                    decoration: const InputDecoration(labelText: 'Metode Pembayaran'),
                    items: const [
                      DropdownMenuItem(value: 'qris', child: Text('QRIS (Scan & Bayar)')),
                      DropdownMenuItem(value: 'cash', child: Text('Bayar Tunai ke Teknisi')),
                    ],
                    onChanged: (value) {
                      if (value != null) {
                        setDialogState(() {
                          method = value;
                          dialogError = null;
                        });
                      }
                    },
                  ),
                  const SizedBox(height: 12),
                  TextField(
                    controller: notes,
                    maxLines: 2,
                    decoration: const InputDecoration(
                      labelText: 'Catatan (Opsional)',
                      hintText: 'Contoh: Pelunasan servis AC kantor',
                    ),
                  ),
                  if (method == 'cash') ...[
                    const SizedBox(height: 12),
                    const Text(
                      'Serahkan uang tunai langsung kepada teknisi saat layanan dilakukan. Pembayaran akan dikonfirmasi oleh admin.',
                      style: TextStyle(
                        fontSize: 12,
                        color: AppColors.textSecondary,
                        height: 1.4,
                      ),
                    ),
                  ],
                  if (method == 'qris') ...[
                    const SizedBox(height: 12),
                    _qrisCard(),
                    const SizedBox(height: 12),
                    TextField(
                      controller: reference,
                      decoration: const InputDecoration(
                        labelText: 'ID Transaksi QRIS (Opsional)',
                        hintText: 'Contoh: TRX-982312',
                      ),
                    ),
                    const SizedBox(height: 12),
                    OutlinedButton.icon(
                      onPressed: () async {
                        final result = await FilePicker.platform.pickFiles(
                          type: FileType.custom,
                          allowedExtensions: ['jpg', 'jpeg', 'png', 'pdf'],
                          withData: false,
                        );
                        if (result == null || result.files.isEmpty) return;

                        final pickedFile = result.files.single;
                        if (pickedFile.size > maxProofSize) {
                          setDialogState(
                              () => dialogError = 'Ukuran bukti maksimal 5MB.');
                          return;
                        }

                        setDialogState(() {
                          proofFile = pickedFile;
                          dialogError = null;
                        });
                      },
                      icon: const Icon(Icons.attach_file, size: 18),
                      label: Text(
                        proofFile == null ? 'Unggah Bukti Bayar' : proofFile!.name,
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                        style: const TextStyle(fontSize: 13),
                      ),
                    ),
                    const SizedBox(height: 4),
                    const Text(
                      'Format: JPG, PNG, atau PDF (Maks. 5MB)',
                      style: TextStyle(fontSize: 11, color: AppColors.textMuted),
                    ),
                  ],
                ],
              ),
            ),
            actions: [
              TextButton(
                onPressed: () => Navigator.pop(context, false),
                child: const Text('Batal'),
              ),
              FilledButton(
                onPressed: submit,
                child: const Text('Kirim Pembayaran'),
              ),
            ],
          );
        },
      ),
    );

    if (submitted != true) {
      amount.dispose();
      reference.dispose();
      notes.dispose();
      return;
    }

    final parsedAmount =
        double.tryParse(amount.text.trim().replaceAll(',', '.')) ?? 0;

    try {
      await ApiService.submitPayment(
        invoiceId: invoice.id,
        amount: parsedAmount,
        paymentMethod: method,
        referenceNumber: reference.text,
        paymentProofPath: method == 'qris' ? proofFile?.path : null,
        notes: notes.text,
      );
      await _loadData();
      _showSuccess('Bukti pembayaran berhasil dikirim untuk verifikasi.');
    } catch (e) {
      _showError(e.toString());
    } finally {
      amount.dispose();
      reference.dispose();
      notes.dispose();
    }
  }

  Future<void> _showPaymentHistoryDialog(Invoice invoice) async {
    try {
      final payments = await ApiService.getInvoicePayments(invoice.id);
      if (!mounted) return;

      await showDialog<void>(
        context: context,
        builder: (context) => AlertDialog(
          title: const Text('Riwayat Pembayaran'),
          content: SizedBox(
            width: double.maxFinite,
            child: payments.isEmpty
                ? const Text(
                    'Belum ada pembayaran yang tercatat.',
                    style: TextStyle(color: AppColors.textSecondary),
                  )
                : ListView(
                    shrinkWrap: true,
                    children: payments.map((Payment p) {
                      return ListTile(
                        contentPadding: EdgeInsets.zero,
                        leading: Container(
                          padding: const EdgeInsets.all(8),
                          decoration: BoxDecoration(
                            color: AppColors.primaryLight,
                            borderRadius: BorderRadius.circular(AppRadius.sm),
                          ),
                          child: const Icon(Icons.payment,
                              size: 18, color: AppColors.primary),
                        ),
                        title: Text(
                          formatRupiah(p.amount),
                          style: const TextStyle(
                            fontSize: 14,
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                        subtitle: Text(
                          '${paymentMethodLabel(p.paymentMethod)} • ${paymentStatusLabel(p.status)}\nRef: ${p.referenceNumber.isEmpty ? '-' : p.referenceNumber}',
                          style: const TextStyle(fontSize: 12),
                        ),
                      );
                    }).toList(),
                  ),
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context),
              child: const Text('Tutup'),
            ),
          ],
        ),
      );
    } catch (e) {
      _showError(e.toString());
    }
  }

  Future<void> _showRatingDialog(Booking booking) async {
    final review = TextEditingController(
      text: booking.rating?['review']?.toString() ?? '',
    );
    var rating = int.tryParse(booking.rating?['rating']?.toString() ?? '') ?? 5;

    final submitted = await showDialog<bool>(
      context: context,
      builder: (context) => StatefulBuilder(
        builder: (context, setDialogState) => AlertDialog(
          title: const Text('Beri Rating Layanan'),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              DropdownButtonFormField<int>(
                initialValue: rating,
                decoration: const InputDecoration(labelText: 'Tingkat Kepuasan'),
                items: const [
                  DropdownMenuItem(value: 5, child: Text('5 - Sangat Memuaskan')),
                  DropdownMenuItem(value: 4, child: Text('4 - Baik')),
                  DropdownMenuItem(value: 3, child: Text('3 - Cukup')),
                  DropdownMenuItem(value: 2, child: Text('2 - Kurang Puas')),
                  DropdownMenuItem(value: 1, child: Text('1 - Buruk')),
                ],
                onChanged: (value) {
                  if (value != null) setDialogState(() => rating = value);
                },
              ),
              const SizedBox(height: 12),
              TextField(
                controller: review,
                maxLines: 3,
                decoration: const InputDecoration(
                  labelText: 'Ulasan Anda',
                  hintText: 'Bagikan pengalaman servis teknisi kami...',
                ),
              ),
            ],
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context, false),
              child: const Text('Batal'),
            ),
            FilledButton(
              onPressed: () => Navigator.pop(context, true),
              child: const Text('Simpan Ulasan'),
            ),
          ],
        ),
      ),
    );

    if (submitted != true) {
      review.dispose();
      return;
    }

    if (review.text.trim().isEmpty) {
      review.dispose();
      _showError('Ulasan wajib diisi.');
      return;
    }

    try {
      await ApiService.submitRating(
        bookingId: booking.id,
        rating: rating,
        review: review.text,
      );
      await _loadData();
      _showSuccess('Terima kasih atas rating dan ulasan Anda.');
    } catch (e) {
      _showError(e.toString());
    } finally {
      review.dispose();
    }
  }

  Widget _profilePage() {
    return RefreshIndicator(
      onRefresh: _loadData,
      color: AppColors.primary,
      child: ListView(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 20),
        children: [
          Card(
            child: Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      CircleAvatar(
                        radius: 28,
                        backgroundColor: AppColors.primaryLight,
                        child: Text(
                          _user.name.isNotEmpty
                              ? _user.name.substring(0, 1).toUpperCase()
                              : 'U',
                          style: const TextStyle(
                            fontSize: 22,
                            fontWeight: FontWeight.w800,
                            color: AppColors.primary,
                          ),
                        ),
                      ),
                      const SizedBox(width: 14),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              _user.name,
                              style: const TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.w700,
                                color: AppColors.textPrimary,
                              ),
                            ),
                            const SizedBox(height: 2),
                            Text(
                              _user.email,
                              style: const TextStyle(
                                fontSize: 13,
                                color: AppColors.textSecondary,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 18),
                  const Divider(height: 1),
                  const SizedBox(height: 14),
                  _infoRow(Icons.phone_outlined,
                      _user.phone.isEmpty ? 'Belum diatur' : _user.phone),
                  _infoRow(Icons.location_on_outlined,
                      _user.address.isEmpty ? 'Belum diatur' : _user.address),
                ],
              ),
            ),
          ),
          const SizedBox(height: 16),
          FilledButton.tonalIcon(
            onPressed: _showEditProfileDialog,
            icon: const Icon(Icons.edit_outlined, size: 18),
            label: const Text('Perbarui Profil'),
          ),
          const SizedBox(height: 10),
          OutlinedButton.icon(
            onPressed: _showChangePasswordDialog,
            icon: const Icon(Icons.lock_outline, size: 18),
            label: const Text('Ganti Password'),
          ),
          const SizedBox(height: 24),
          OutlinedButton.icon(
            onPressed: _logout,
            style: OutlinedButton.styleFrom(
              foregroundColor: AppColors.danger,
              side: const BorderSide(color: AppColors.dangerLight, width: 1.2),
            ),
            icon: const Icon(Icons.logout_rounded, size: 18),
            label: const Text('Keluar dari Akun'),
          ),
        ],
      ),
    );
  }

  Widget _infoRow(IconData icon, String text) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, size: 16, color: AppColors.textMuted),
          const SizedBox(width: 8),
          Expanded(
            child: Text(
              text,
              style: const TextStyle(fontSize: 13, color: AppColors.textSecondary),
            ),
          ),
        ],
      ),
    );
  }

  Future<void> _showEditProfileDialog() async {
    final name = TextEditingController(text: _user.name);
    final email = TextEditingController(text: _user.email);
    final phone = TextEditingController(text: _user.phone);
    final address = TextEditingController(text: _user.address);

    final saved = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Edit Profil'),
        content: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              TextField(
                controller: name,
                decoration: const InputDecoration(labelText: 'Nama Lengkap'),
              ),
              const SizedBox(height: 10),
              TextField(
                controller: email,
                decoration: const InputDecoration(labelText: 'Email'),
              ),
              const SizedBox(height: 10),
              TextField(
                controller: phone,
                decoration: const InputDecoration(labelText: 'Nomor Telepon'),
              ),
              const SizedBox(height: 10),
              TextField(
                controller: address,
                maxLines: 2,
                decoration: const InputDecoration(labelText: 'Alamat'),
              ),
            ],
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Batal'),
          ),
          FilledButton(
            onPressed: () => Navigator.pop(context, true),
            child: const Text('Simpan'),
          ),
        ],
      ),
    );

    if (saved != true) return;

    try {
      final updated = await ApiService.updateProfile(
        name: name.text,
        email: email.text,
        phone: phone.text,
        address: address.text,
      );
      if (!mounted) return;
      setState(() => _user = updated);
      _showSuccess('Profil berhasil diperbarui.');
    } catch (e) {
      _showError(e.toString());
    }
  }

  Future<void> _showChangePasswordDialog() async {
    final current = TextEditingController();
    final password = TextEditingController();

    final saved = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Ganti Password'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            TextField(
              controller: current,
              obscureText: true,
              decoration: const InputDecoration(labelText: 'Password Saat Ini'),
            ),
            const SizedBox(height: 10),
            TextField(
              controller: password,
              obscureText: true,
              decoration: const InputDecoration(labelText: 'Password Baru (min 8 karakter)'),
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Batal'),
          ),
          FilledButton(
            onPressed: () {
              if (password.text.length < 8) {
                _showError('Password minimal 8 karakter.');
                return;
              }
              Navigator.pop(context, true);
            },
            child: const Text('Perbarui'),
          ),
        ],
      ),
    );

    if (saved != true) return;

    try {
      await ApiService.updatePassword(current.text, password.text);
      if (!mounted) return;
      _showSuccess('Password berhasil diperbarui.');
    } catch (e) {
      _showError(e.toString());
    }
  }
}

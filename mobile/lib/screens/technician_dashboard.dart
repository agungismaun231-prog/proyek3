import 'package:flutter/material.dart';

import '../config/app_theme.dart';
import '../models/booking.dart';
import '../models/user.dart';
import '../services/api_service.dart';
import '../utils/formatters.dart';
import '../widgets/app_empty_state.dart';
import '../widgets/app_section_header.dart';
import '../widgets/app_stat_card.dart';
import '../widgets/app_status_badge.dart';
import 'login_screen.dart';

class TechnicianDashboard extends StatefulWidget {
  final User initialUser;

  const TechnicianDashboard({super.key, required this.initialUser});

  @override
  State<TechnicianDashboard> createState() => _TechnicianDashboardState();
}

class _TechnicianDashboardState extends State<TechnicianDashboard> {
  int _selectedIndex = 0;
  late User _user;
  List<Booking> _bookings = [];
  bool _isLoading = true;

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
      ]);

      if (!mounted) return;
      setState(() {
        _user = results[0] as User;
        _bookings = results[1] as List<Booking>;
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
      _activeBookingsPage(),
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
                Icons.engineering_rounded,
                size: 20,
                color: AppColors.primary,
              ),
            ),
            const SizedBox(width: 10),
            const Text('Portal Teknisi'),
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
          : pages[_selectedIndex],
      bottomNavigationBar: NavigationBar(
        selectedIndex: _selectedIndex,
        onDestinationSelected: (index) => setState(() => _selectedIndex = index),
        destinations: const [
          NavigationDestination(
            icon: Icon(Icons.dashboard_outlined),
            selectedIcon: Icon(Icons.dashboard_rounded),
            label: 'Beranda',
          ),
          NavigationDestination(
            icon: Icon(Icons.assignment_outlined),
            selectedIcon: Icon(Icons.assignment_rounded),
            label: 'Tugas Aktif',
          ),
          NavigationDestination(
            icon: Icon(Icons.history_outlined),
            selectedIcon: Icon(Icons.history_rounded),
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
    final active = _bookings.where((b) => b.isActive).toList();
    final completed = _bookings.where((b) => b.isCompleted).length;

    return RefreshIndicator(
      onRefresh: _loadData,
      color: AppColors.primary,
      child: ListView(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 20),
        children: [
          // Technician Profile Banner
          Container(
            padding: const EdgeInsets.all(18),
            decoration: BoxDecoration(
              color: AppColors.surface,
              borderRadius: BorderRadius.circular(AppRadius.lg),
              border: Border.all(color: AppColors.border, width: 1),
            ),
            child: Row(
              children: [
                CircleAvatar(
                  radius: 26,
                  backgroundColor: AppColors.primaryLight,
                  child: Text(
                    _user.name.isNotEmpty
                        ? _user.name.substring(0, 1).toUpperCase()
                        : 'T',
                    style: const TextStyle(
                      fontSize: 20,
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
                          fontSize: 16,
                          fontWeight: FontWeight.w700,
                          color: AppColors.textPrimary,
                        ),
                      ),
                      const SizedBox(height: 2),
                      Text(
                        _user.specialization.isEmpty
                            ? 'Mitra Teknisi AC'
                            : _user.specialization,
                        style: const TextStyle(
                          fontSize: 13,
                          color: AppColors.textSecondary,
                        ),
                      ),
                    ],
                  ),
                ),
                Container(
                  padding:
                      const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    color: AppColors.successLight,
                    borderRadius: BorderRadius.circular(AppRadius.pill),
                  ),
                  child: const Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(Icons.circle, size: 8, color: AppColors.success),
                      SizedBox(width: 5),
                      Text(
                        'Siap Tugas',
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.w600,
                          color: Color(0xFF047857),
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 16),

          // Stat Cards
          Row(
            children: [
              Expanded(
                child: AppStatCard(
                  label: 'Tugas Berjalan',
                  value: active.length.toString(),
                  icon: Icons.pending_actions_rounded,
                  accentColor: AppColors.primary,
                  onTap: () => setState(() => _selectedIndex = 1),
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: AppStatCard(
                  label: 'Servis Selesai',
                  value: completed.toString(),
                  icon: Icons.task_alt_rounded,
                  accentColor: AppColors.success,
                  onTap: () => setState(() => _selectedIndex = 2),
                ),
              ),
            ],
          ),
          const SizedBox(height: 24),

          // Active Task Spotlight
          AppSectionHeader(
            title: 'Tugas Operasional Utama',
            count: active.length,
            actionLabel: active.isNotEmpty ? 'Semua Tugas' : null,
            onAction: () => setState(() => _selectedIndex = 1),
          ),
          if (active.isEmpty)
            const AppEmptyState(
              icon: Icons.task_alt_outlined,
              title: 'Tidak Ada Tugas Aktif',
              description:
                  'Semua pesanan servis telah selesai. Anda siap menerima penugasan pekerjaan berikutnya.',
            )
          else
            ...active.take(3).map(_technicianBookingCard),
        ],
      ),
    );
  }

  Widget _activeBookingsPage() {
    final activeBookings = _bookings.where((b) => b.isActive).toList();

    return RefreshIndicator(
      onRefresh: _loadData,
      color: AppColors.primary,
      child: ListView(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 20),
        children: [
          AppSectionHeader(
            title: 'Daftar Booking Aktif',
            count: activeBookings.length,
          ),
          if (activeBookings.isEmpty)
            const AppEmptyState(
              icon: Icons.assignment_turned_in_outlined,
              title: 'Tidak Ada Booking Aktif',
              description:
                  'Saat ini tidak ada pekerjaan servis yang sedang berjalan.',
            )
          else
            ...activeBookings.map(_technicianBookingCard),
        ],
      ),
    );
  }

  Widget _historyPage() {
    final history = _bookings.where((b) => !b.isActive).toList();

    return RefreshIndicator(
      onRefresh: _loadData,
      color: AppColors.primary,
      child: ListView(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 20),
        children: [
          AppSectionHeader(
            title: 'Riwayat Pekerjaan',
            count: history.length,
          ),
          if (history.isEmpty)
            const AppEmptyState(
              icon: Icons.history_outlined,
              title: 'Riwayat Kosong',
              description: 'Pekerjaan yang telah selesai atau dibatalkan akan muncul di sini.',
            )
          else
            ...history.map(_technicianBookingCard),
        ],
      ),
    );
  }

  Widget _technicianBookingCard(Booking booking) {
    final customerName = booking.customer?['name']?.toString() ?? '-';
    final customerPhone = booking.customer?['phone']?.toString() ?? '-';
    final customerAddress =
        booking.customer?['address']?.toString() ?? 'Alamat belum tersedia';
    final location = booking.serviceLocation.isNotEmpty
        ? booking.serviceLocation
        : customerAddress;
    final invoice = booking.invoice;

    return Card(
      margin: const EdgeInsets.only(bottom: 14),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Header
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Expanded(
                  child: Text(
                    booking.service?.name ?? 'Layanan Servis AC',
                    style: const TextStyle(
                      fontSize: 15,
                      fontWeight: FontWeight.w700,
                      color: AppColors.textPrimary,
                    ),
                  ),
                ),
                const SizedBox(width: 8),
                AppStatusBadge(status: booking.status),
              ],
            ),
            const SizedBox(height: 12),

            // Metadata
            _infoItem(Icons.person_outline, 'Customer: $customerName'),
            if (customerPhone != '-')
              _infoItem(Icons.phone_outlined, 'Kontak: $customerPhone'),
            _infoItem(Icons.calendar_today_outlined,
                'Jadwal: ${formatDate(booking.scheduledDate)}'),
            _infoItem(Icons.location_on_outlined, location, maxLines: 2),
            if (booking.notes.isNotEmpty)
              _infoItem(Icons.notes_outlined, 'Catatan: ${booking.notes}',
                  maxLines: 2),

            const SizedBox(height: 12),
            const Divider(height: 1),
            const SizedBox(height: 12),

            // Price & Payment Info
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Total Pendapatan Layanan',
                      style: TextStyle(fontSize: 11, color: AppColors.textMuted),
                    ),
                    Text(
                      formatRupiah(booking.totalPrice),
                      style: const TextStyle(
                        fontSize: 15,
                        fontWeight: FontWeight.w700,
                        color: AppColors.primary,
                      ),
                    ),
                  ],
                ),
              ],
            ),

            if (invoice != null) ...[
              const SizedBox(height: 10),
              _technicianInvoiceSummary(invoice),
            ],

            // Action Buttons
            if (booking.isActive) ...[
              const SizedBox(height: 14),
              Row(
                mainAxisAlignment: MainAxisAlignment.end,
                children: [
                  if (booking.status == 'pending') ...[
                    OutlinedButton(
                      onPressed: () => _updateStatus(booking, 'cancelled'),
                      style: OutlinedButton.styleFrom(
                        foregroundColor: AppColors.danger,
                        minimumSize: const Size(0, 36),
                        padding: const EdgeInsets.symmetric(horizontal: 14),
                      ),
                      child: const Text('Tolak', style: TextStyle(fontSize: 12)),
                    ),
                    const SizedBox(width: 8),
                    FilledButton(
                      onPressed: () => _updateStatus(booking, 'confirmed'),
                      style: FilledButton.styleFrom(
                        minimumSize: const Size(0, 36),
                        padding: const EdgeInsets.symmetric(horizontal: 16),
                      ),
                      child: const Text('Terima Tugas', style: TextStyle(fontSize: 12)),
                    ),
                  ],
                  if (booking.status == 'confirmed')
                    FilledButton.icon(
                      onPressed: () => _updateStatus(booking, 'en_route'),
                      style: FilledButton.styleFrom(
                        minimumSize: const Size(0, 38),
                        padding: const EdgeInsets.symmetric(horizontal: 16),
                      ),
                      icon: const Icon(Icons.directions_car_outlined, size: 16),
                      label: const Text('Menuju Lokasi', style: TextStyle(fontSize: 13)),
                    ),
                  if (booking.status == 'en_route')
                    FilledButton.icon(
                      onPressed: () => _updateStatus(booking, 'in_progress'),
                      style: FilledButton.styleFrom(
                        minimumSize: const Size(0, 38),
                        padding: const EdgeInsets.symmetric(horizontal: 16),
                      ),
                      icon: const Icon(Icons.play_arrow_outlined, size: 16),
                      label: const Text('Mulai Pengerjaan', style: TextStyle(fontSize: 13)),
                    ),
                  if (booking.status == 'in_progress')
                    FilledButton.icon(
                      onPressed: () => _completeBooking(booking),
                      style: FilledButton.styleFrom(
                        backgroundColor: AppColors.success,
                        minimumSize: const Size(0, 38),
                        padding: const EdgeInsets.symmetric(horizontal: 16),
                      ),
                      icon: const Icon(Icons.check_circle_outline, size: 16),
                      label: const Text('Selesaikan Servis', style: TextStyle(fontSize: 13)),
                    ),
                ],
              ),
            ],
          ],
        ),
      ),
    );
  }

  Widget _infoItem(IconData icon, String text, {int maxLines = 1}) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 6),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, size: 15, color: AppColors.textMuted),
          const SizedBox(width: 8),
          Expanded(
            child: Text(
              text,
              maxLines: maxLines,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(
                fontSize: 13,
                color: AppColors.textSecondary,
                height: 1.25,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _technicianInvoiceSummary(Map<String, dynamic> invoice) {
    final invoiceNumber = invoice['invoice_number']?.toString() ?? 'Invoice';
    final status = invoice['status']?.toString() ?? '';
    final pendingAmount =
        double.tryParse(invoice['pending_amount']?.toString() ?? '0') ?? 0;
    final latestPayment = invoice['latest_payment'];
    final latestPaymentStatus = latestPayment is Map<String, dynamic>
        ? latestPayment['status']?.toString() ?? ''
        : '';
    final latestPaymentId = latestPayment is Map<String, dynamic>
        ? int.tryParse(latestPayment['id']?.toString() ?? '')
        : null;

    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: AppColors.surfaceElevated,
        borderRadius: BorderRadius.circular(AppRadius.sm),
        border: Border.all(color: AppColors.border, width: 1),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                invoiceNumber,
                style: const TextStyle(
                  fontSize: 13,
                  fontWeight: FontWeight.w700,
                  color: AppColors.textPrimary,
                ),
              ),
              AppStatusBadge(
                status: pendingAmount > 0 ? 'pending' : status,
                isInvoice: true,
              ),
            ],
          ),
          if (pendingAmount > 0) ...[
            const SizedBox(height: 4),
            Text(
              'Menunggu verifikasi: ${formatRupiah(pendingAmount)}',
              style: const TextStyle(
                fontSize: 12,
                fontWeight: FontWeight.w600,
                color: Color(0xFFB45309),
              ),
            ),
          ],
          if (latestPaymentId != null && latestPaymentStatus == 'pending_approval') ...[
            const SizedBox(height: 8),
            Row(
              mainAxisAlignment: MainAxisAlignment.end,
              children: [
                OutlinedButton(
                  onPressed: () => _rejectPayment(latestPaymentId),
                  style: OutlinedButton.styleFrom(
                    foregroundColor: AppColors.danger,
                    minimumSize: const Size(0, 32),
                    padding: const EdgeInsets.symmetric(horizontal: 12),
                  ),
                  child: const Text('Tolak', style: TextStyle(fontSize: 12)),
                ),
                const SizedBox(width: 8),
                FilledButton(
                  onPressed: () => _approvePayment(latestPaymentId),
                  style: FilledButton.styleFrom(
                    backgroundColor: AppColors.success,
                    minimumSize: const Size(0, 32),
                    padding: const EdgeInsets.symmetric(horizontal: 12),
                  ),
                  child: const Text('Verifikasi Bayar', style: TextStyle(fontSize: 12)),
                ),
              ],
            ),
          ],
        ],
      ),
    );
  }

  Future<void> _approvePayment(int paymentId) async {
    try {
      await ApiService.approvePayment(paymentId, notes: 'Diverifikasi teknisi');
      await _loadData();
      _showSuccess('Pembayaran berhasil diverifikasi.');
    } catch (e) {
      _showError(e.toString());
    }
  }

  Future<void> _rejectPayment(int paymentId) async {
    final reason = TextEditingController();
    final rejected = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Tolak Bukti Pembayaran'),
        content: TextField(
          controller: reason,
          maxLines: 3,
          decoration: const InputDecoration(
            labelText: 'Alasan Penolakan',
            hintText: 'Contoh: Bukti transfer buram / nominal tidak sesuai',
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Batal'),
          ),
          FilledButton(
            style: FilledButton.styleFrom(backgroundColor: AppColors.danger),
            onPressed: () {
              if (reason.text.trim().isEmpty) {
                _showError('Alasan penolakan wajib diisi.');
                return;
              }
              Navigator.pop(context, true);
            },
            child: const Text('Tolak Pembayaran'),
          ),
        ],
      ),
    );

    if (rejected != true) {
      reason.dispose();
      return;
    }

    try {
      await ApiService.rejectPayment(paymentId, reason: reason.text);
      await _loadData();
      _showSuccess('Pembayaran telah ditolak.');
    } catch (e) {
      _showError(e.toString());
    } finally {
      reason.dispose();
    }
  }

  Future<void> _updateStatus(Booking booking, String status) async {
    try {
      await ApiService.updateBookingStatus(booking.id, status);
      await _loadData();
      _showSuccess('Status booking berhasil diperbarui.');
    } catch (e) {
      _showError(e.toString());
    }
  }

  Future<void> _completeBooking(Booking booking) async {
    final notes = TextEditingController();
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Konfirmasi Selesai Servis'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'Pastikan seluruh pengerjaan AC telah dicek dan berfungsi baik bersama customer.',
              style: TextStyle(fontSize: 13, color: AppColors.textSecondary),
            ),
            const SizedBox(height: 14),
            TextField(
              controller: notes,
              maxLines: 3,
              decoration: const InputDecoration(
                labelText: 'Catatan Pekerjaan (Opsional)',
                hintText: 'Contoh: Cuci indoor/outdoor selesai, tekanan freon normal (75 psi)',
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
            style: FilledButton.styleFrom(backgroundColor: AppColors.success),
            onPressed: () => Navigator.pop(context, true),
            child: const Text('Selesai'),
          ),
        ],
      ),
    );

    if (confirmed != true) {
      notes.dispose();
      return;
    }

    try {
      await ApiService.updateBookingStatus(
        booking.id,
        'completed',
        notes: notes.text.trim().isEmpty ? null : notes.text.trim(),
      );
      await _loadData();
      _showSuccess('Booking berhasil diselesaikan.');
    } catch (e) {
      _showError(e.toString());
    } finally {
      notes.dispose();
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
                              : 'T',
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
                  _infoRow(Icons.build_outlined,
                      _user.specialization.isEmpty ? 'Umum' : _user.specialization),
                ],
              ),
            ),
          ),
          const SizedBox(height: 16),
          FilledButton.tonalIcon(
            onPressed: _showEditProfileDialog,
            icon: const Icon(Icons.edit_outlined, size: 18),
            label: const Text('Perbarui Profil Teknisi'),
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
    final specialization = TextEditingController(text: _user.specialization);

    final saved = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Edit Profil Teknisi'),
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
                decoration: const InputDecoration(labelText: 'Alamat Domisili'),
              ),
              const SizedBox(height: 10),
              TextField(
                controller: specialization,
                decoration: const InputDecoration(labelText: 'Spesialisasi'),
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
        specialization: specialization.text,
      );
      if (!mounted) return;
      setState(() => _user = updated);
      _showSuccess('Profil teknisi berhasil diperbarui.');
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

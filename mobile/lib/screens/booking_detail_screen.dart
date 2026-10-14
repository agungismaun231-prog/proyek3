import 'package:flutter/material.dart';
import '../config/app_theme.dart';
import '../models/booking.dart';
import '../services/api_service.dart';
import '../utils/formatters.dart';
import '../widgets/app_status_badge.dart';

class BookingDetailScreen extends StatefulWidget {
  final Booking booking;

  const BookingDetailScreen({super.key, required this.booking});

  @override
  State<BookingDetailScreen> createState() => _BookingDetailScreenState();
}

class _BookingDetailScreenState extends State<BookingDetailScreen> {
  late Booking _booking;
  bool _isUpdating = false;

  @override
  void initState() {
    super.initState();
    _booking = widget.booking;
  }

  // Dialog Konfirmasi sebelum mengubah status pesanan
  Future<void> _showStatusConfirmDialog({
    required String title,
    required String content,
    required String targetStatus,
    required String successMessage,
  }) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: Text(title),
        content: Text(content),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Batal'),
          ),
          FilledButton(
            onPressed: () => Navigator.pop(context, true),
            child: const Text('Ya, Lanjutkan'),
          ),
        ],
      ),
    );

    if (confirm != true) return;

    setState(() => _isUpdating = true);

    try {
      await ApiService.updateBookingStatus(_booking.id, targetStatus);
      if (!mounted) return;

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(successMessage),
          backgroundColor: AppColors.success,
          behavior: SnackBarBehavior.floating,
        ),
      );

      // Kembali ke halaman sebelumnya dengan membawa sinyal (true) agar dashboard merefresh data
      Navigator.pop(context, true);
    } catch (e) {
      if (!mounted) return;
      setState(() => _isUpdating = false);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(e.toString()),
          backgroundColor: AppColors.danger,
          behavior: SnackBarBehavior.floating,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final customer = _booking.customer ?? {};
    final service = _booking.service;

    final statusLower = _booking.status.toLowerCase().trim();
    final isPending = statusLower == 'pending' || statusLower == 'menunggu';
    final isConfirmed = statusLower == 'confirmed' || statusLower == 'dikonfirmasi';
    final isInProgress = statusLower == 'in_progress' || statusLower == 'sedang dikerjakan';
    final isCompleted = statusLower == 'completed' || statusLower == 'selesai';

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('Detail Pesanan'),
      ),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          // 1. Waktu Jadwal & Status
          Card(
            child: Padding(
              padding: const EdgeInsets.all(16),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'Waktu Jadwal',
                        style: TextStyle(fontSize: 12, color: Colors.grey),
                      ),
                      const SizedBox(height: 4),
                      Row(
                        children: [
                          const Icon(Icons.calendar_today_outlined,
                              size: 16, color: AppColors.primary),
                          const SizedBox(width: 6),
                          Text(
                            formatDate(_booking.scheduledDate),
                            style: const TextStyle(
                              fontSize: 14,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      const Text(
                        'Status Pesanan',
                        style: TextStyle(fontSize: 12, color: Colors.grey),
                      ),
                      const SizedBox(height: 4),
                      AppStatusBadge(status: _booking.status),
                    ],
                  ),
                ],
              ),
            ),
          ),
          const SizedBox(height: 12),

          // 2. Informasi Pelanggan
          Card(
            child: Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Row(
                    children: [
                      Icon(Icons.person_outline, size: 18, color: AppColors.primary),
                      SizedBox(width: 8),
                      Text(
                        'Informasi Pelanggan',
                        style: TextStyle(
                          fontSize: 15,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ],
                  ),
                  const Divider(height: 20),
                  _detailRow('Nama Pelanggan:', customer['name']?.toString() ?? '-'),
                  _detailRow('Email:', customer['email']?.toString() ?? '-'),
                  _detailRow('Nomor Telepon:', customer['phone']?.toString() ?? '-'),
                  _detailRow(
                    'Alamat:',
                    _booking.serviceLocation.isNotEmpty
                        ? _booking.serviceLocation
                        : (customer['address']?.toString() ?? '-'),
                  ),
                ],
              ),
            ),
          ),
          const SizedBox(height: 12),

          // 3. Detail Layanan
          Card(
            child: Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Row(
                    children: [
                      Icon(Icons.build_outlined, size: 18, color: AppColors.primary),
                      SizedBox(width: 8),
                      Text(
                        'Detail Layanan',
                        style: TextStyle(
                          fontSize: 15,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ],
                  ),
                  const Divider(height: 20),
                  _detailRow('Jenis Layanan:', service?.name ?? '-'),
                  _detailRow('Deskripsi:', service?.description ?? '-'),
                  _detailRow('Catatan Pelanggan:', '-'),
                ],
              ),
            ),
          ),
          const SizedBox(height: 12),

          // 4. Informasi Invoice
          Card(
            child: Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Row(
                    children: [
                      Icon(Icons.receipt_long_outlined, size: 18, color: AppColors.primary),
                      SizedBox(width: 8),
                      Text(
                        'Informasi Invoice',
                        style: TextStyle(
                          fontSize: 15,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ],
                  ),
                  const Divider(height: 20),
                  _detailRow('Nomor Invoice:', 'INV-${_booking.id.toString().padLeft(8, '0')}'),
                  _detailRow('Subtotal:', formatRupiah(service?.price ?? 0)),
                  _detailRow('Pajak (PPN):', 'Rp 0'),
                  const Divider(height: 16),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text(
                        'Total:',
                        style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
                      ),
                      Text(
                        formatRupiah(service?.price ?? 0),
                        style: const TextStyle(
                          fontWeight: FontWeight.bold,
                          fontSize: 16,
                          color: AppColors.success,
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ),
          const SizedBox(height: 24),

          // 5. TOMBOL AKSI DINAMIS (KEMBALI + TOMBOL STATUS BERDASARKAN WEB)
          if (_isUpdating)
            const Center(child: CircularProgressIndicator())
          else
            Row(
              children: [
                // Tombol Kembali
                Expanded(
                  child: OutlinedButton.icon(
                    onPressed: () => Navigator.pop(context),
                    icon: const Icon(Icons.arrow_back, size: 16),
                    label: const Text('Kembali'),
                    style: OutlinedButton.styleFrom(
                      padding: const EdgeInsets.symmetric(vertical: 12),
                    ),
                  ),
                ),

                // Tombol Jika Status Pending -> Terima Pesanan
                if (isPending) ...[
                  const SizedBox(width: 10),
                  Expanded(
                    child: FilledButton.icon(
                      onPressed: () => _showStatusConfirmDialog(
                        title: 'Konfirmasi Pesanan',
                        content: 'Apakah Anda yakin ingin menerima pesanan ini?',
                        targetStatus: 'confirmed',
                        successMessage: 'Pesanan berhasil dikonfirmasi!',
                      ),
                      icon: const Icon(Icons.check_circle_outline, size: 16),
                      label: const Text('Terima Pesanan'),
                      style: FilledButton.styleFrom(
                        backgroundColor: Colors.green,
                        padding: const EdgeInsets.symmetric(vertical: 12),
                      ),
                    ),
                  ),
                ],

                // Tombol Jika Status Confirmed -> Tombol Mulai (▶ Mulai)
                if (isConfirmed) ...[
                  const SizedBox(width: 10),
                  Expanded(
                    child: FilledButton.icon(
                      onPressed: () => _showStatusConfirmDialog(
                        title: 'Mulai Pekerjaan',
                        content: 'Apakah Anda yakin ingin mulai mengerjakan pesanan ini?',
                        targetStatus: 'in_progress',
                        successMessage: 'Status diperbarui: Sedang dikerjakan.',
                      ),
                      icon: const Icon(Icons.play_arrow, size: 16),
                      label: const Text('Mulai Kerjakan'),
                      style: FilledButton.styleFrom(
                        backgroundColor: Colors.orange,
                        padding: const EdgeInsets.symmetric(vertical: 12),
                      ),
                    ),
                  ),
                ],

                // Tombol Jika Status In Progress -> Tombol Selesai (✓ Selesai)
                if (isInProgress) ...[
                  const SizedBox(width: 10),
                  Expanded(
                    child: FilledButton.icon(
                      onPressed: () => _showStatusConfirmDialog(
                        title: 'Selesaikan Pekerjaan',
                        content: 'Apakah Anda yakin pekerjaan servis ini sudah selesai?',
                        targetStatus: 'completed',
                        successMessage: 'Pekerjaan berhasil diselesaikan!',
                      ),
                      icon: const Icon(Icons.check, size: 16),
                      label: const Text('Tandai Selesai'),
                      style: FilledButton.styleFrom(
                        backgroundColor: AppColors.success,
                        padding: const EdgeInsets.symmetric(vertical: 12),
                      ),
                    ),
                  ),
                ],

                // Tampilan Jika Sudah Selesai
                if (isCompleted) ...[
                  const SizedBox(width: 10),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                    decoration: BoxDecoration(
                      color: Colors.green.shade50,
                      borderRadius: BorderRadius.circular(8),
                      border: Border.all(color: Colors.green.shade200),
                    ),
                    child: const Row(
                      children: [
                        Icon(Icons.check_circle, size: 16, color: Colors.green),
                        SizedBox(width: 6),
                        Text(
                          'Selesai',
                          style: TextStyle(
                            color: Colors.green,
                            fontWeight: FontWeight.bold,
                            fontSize: 13,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ],
            ),
          const SizedBox(height: 20),
        ],
      ),
    );
  }

  Widget _detailRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 130,
            child: Text(
              label,
              style: const TextStyle(fontSize: 13, color: Colors.grey),
            ),
          ),
          Expanded(
            child: Text(
              value,
              style: const TextStyle(
                fontSize: 13,
                fontWeight: FontWeight.w500,
                color: AppColors.textPrimary,
              ),
            ),
          ),
        ],
      ),
    );
  }

  String formatRupiah(num price) {
    return 'Rp ${price.toStringAsFixed(0).replaceAllMapped(RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'), (Match m) => '${m[1]}.')}';
  }
}

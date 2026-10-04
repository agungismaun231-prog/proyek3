import 'package:flutter/material.dart';

import '../config/app_theme.dart';
import '../models/booking.dart';
import '../models/invoice.dart';
import '../models/user.dart';
import '../services/api_service.dart';
import '../widgets/app_empty_state.dart';
import '../widgets/app_section_header.dart';
import '../widgets/app_stat_card.dart';
import '../widgets/booking_card.dart';
import 'login_screen.dart';

class AdminDashboard extends StatefulWidget {
  final User initialUser;

  const AdminDashboard({super.key, required this.initialUser});

  @override
  State<AdminDashboard> createState() => _AdminDashboardState();
}

class _AdminDashboardState extends State<AdminDashboard> {
  late User _user;
  List<Booking> _bookings = [];
  List<Invoice> _invoices = [];
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
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(e.toString()),
          backgroundColor: AppColors.danger,
          behavior: SnackBarBehavior.floating,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(AppRadius.sm),
          ),
        ),
      );
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

  @override
  Widget build(BuildContext context) {
    final pending =
        _bookings.where((booking) => booking.status == 'pending').length;
    final inProgress = _bookings
        .where((b) => b.status == 'in_progress' || b.status == 'en_route')
        .length;
    final completed =
        _bookings.where((booking) => booking.status == 'completed').length;
    final unpaid =
        _invoices.where((invoice) => invoice.status != 'paid').length;

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
                Icons.admin_panel_settings_rounded,
                size: 20,
                color: AppColors.primary,
              ),
            ),
            const SizedBox(width: 10),
            const Text('Konsol Admin'),
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
          : RefreshIndicator(
              onRefresh: _loadData,
              color: AppColors.primary,
              child: ListView(
                padding:
                    const EdgeInsets.symmetric(horizontal: 16, vertical: 20),
                children: [
                  // Overview Banner
                  Container(
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      color: AppColors.surface,
                      borderRadius: BorderRadius.circular(AppRadius.lg),
                      border: Border.all(color: AppColors.border, width: 1),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text(
                              'Administrator: ${_user.name}',
                              style: const TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.w700,
                                color: AppColors.textPrimary,
                                letterSpacing: -0.2,
                              ),
                            ),
                            Container(
                              padding: const EdgeInsets.symmetric(
                                  horizontal: 10, vertical: 4),
                              decoration: BoxDecoration(
                                color: AppColors.primaryLight,
                                borderRadius:
                                    BorderRadius.circular(AppRadius.pill),
                              ),
                              child: const Text(
                                'Mobile Admin',
                                style: TextStyle(
                                  fontSize: 11,
                                  fontWeight: FontWeight.w600,
                                  color: AppColors.primaryDark,
                                ),
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 6),
                        const Text(
                          'Ringkasan operasional servis AC JASAKU. Gunakan dashboard web untuk manajemen sistem dan konfigurasi tarif penuh.',
                          style: TextStyle(
                            fontSize: 13,
                            color: AppColors.textSecondary,
                            height: 1.35,
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 18),

                  // Capped 2-Column KPI Grid
                  GridView.count(
                    crossAxisCount: 2,
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    mainAxisSpacing: 10,
                    crossAxisSpacing: 10,
                    childAspectRatio: 1.35,
                    children: [
                      AppStatCard(
                        label: 'Total Booking',
                        value: _bookings.length.toString(),
                        icon: Icons.receipt_long_rounded,
                        accentColor: AppColors.primary,
                      ),
                      AppStatCard(
                        label: 'Menunggu',
                        value: pending.toString(),
                        icon: Icons.pending_actions_rounded,
                        accentColor: AppColors.warning,
                      ),
                      AppStatCard(
                        label: 'Dalam Proses',
                        value: inProgress.toString(),
                        icon: Icons.engineering_rounded,
                        accentColor: AppColors.info,
                      ),
                      AppStatCard(
                        label: 'Selesai',
                        value: completed.toString(),
                        icon: Icons.check_circle_outline_rounded,
                        accentColor: AppColors.success,
                      ),
                      AppStatCard(
                        label: 'Total Tagihan',
                        value: _invoices.length.toString(),
                        icon: Icons.request_quote_outlined,
                        accentColor: AppColors.primary,
                      ),
                      AppStatCard(
                        label: 'Belum Bayar',
                        value: unpaid.toString(),
                        icon: Icons.warning_amber_rounded,
                        accentColor: AppColors.danger,
                      ),
                    ],
                  ),
                  const SizedBox(height: 24),

                  // Recent Bookings Feed
                  AppSectionHeader(
                    title: 'Aktivitas Booking Terbaru',
                    count: _bookings.length,
                  ),
                  if (_bookings.isEmpty)
                    const AppEmptyState(
                      icon: Icons.receipt_long_outlined,
                      title: 'Belum Ada Booking',
                      description:
                          'Aktivitas pesanan servis AC pelanggan akan muncul di sini secara langsung.',
                    )
                  else
                    ..._bookings.take(8).map((b) => BookingCard(booking: b)),
                ],
              ),
            ),
    );
  }
}

import 'package:flutter/material.dart';

import '../config/app_theme.dart';
import '../models/booking.dart';
import '../models/user.dart';
import '../services/api_service.dart';
import '../utils/formatters.dart';
import '../widgets/app_empty_state.dart';
import '../widgets/app_status_badge.dart';
import 'booking_detail_screen.dart';
import 'edit_profile_screen.dart';
import 'login_screen.dart';

class TechnicianDashboard extends StatefulWidget {
  final User initialUser;

  const TechnicianDashboard({super.key, required this.initialUser});

  @override
  State<TechnicianDashboard> createState() => _TechnicianDashboardState();
}

class _TechnicianDashboardState extends State<TechnicianDashboard> {
  int _selectedIndex = 0;
  int _activeTabFilter = 0;

  late User _user;
  List<Booking> _bookings = [];
  bool _isLoading = true;

  String _translateStatus(String status) {
    final lower = status.toLowerCase().trim();
    if (lower.contains('tersedia') ||
        (lower.contains('aktif') && !lower.contains('tidak'))) {
      return 'available';
    } else if (lower.contains('sibuk')) {
      return 'busy';
    } else if (lower.contains('tidak aktif') || lower.contains('offline')) {
      return 'offline';
    }
    return lower.isEmpty ? 'available' : lower;
  }

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

  Future<void> _navigateToEditProfile() async {
    final updatedUser = await Navigator.push<User>(
      context,
      MaterialPageRoute(
        builder: (context) => EditProfileScreen(user: _user),
      ),
    );

    if (updatedUser != null) {
      setState(() {
        _user = updatedUser;
      });
      _showSuccess('Profil teknisi berhasil diperbarui.');
    }
  }

  @override
  Widget build(BuildContext context) {
    final pages = [
      _homePage(),
      _incomingOrdersPage(),
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
        onDestinationSelected: (index) =>
            setState(() => _selectedIndex = index),
        destinations: const [
          NavigationDestination(
            icon: Icon(Icons.dashboard_outlined),
            selectedIcon: Icon(Icons.dashboard_rounded),
            label: 'Beranda',
          ),
          NavigationDestination(
            icon: Icon(Icons.assignment_outlined),
            selectedIcon: Icon(Icons.assignment_rounded),
            label: 'Pesanan Masuk',
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

  // --- BERANDA DASHBOARD ---
  Widget _homePage() {
    final totalBooking = _bookings.length;
    final activeBookings = _bookings.where((b) => b.isActive).toList();
    final completedBookings = _bookings.where((b) => b.isCompleted).toList();
    final upcomingBookings = _bookings
        .where((b) => b.status == 'pending' || b.status == 'confirmed')
        .toList();

    return RefreshIndicator(
      onRefresh: _loadData,
      color: AppColors.primary,
      child: ListView(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
        children: [
          Container(
            padding: const EdgeInsets.all(18),
            decoration: BoxDecoration(
              color: const Color(0xFFE65100),
              borderRadius: BorderRadius.circular(10),
            ),
            child: Row(
              children: [
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'Selamat datang, ${_user.name}!',
                        style: const TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                          color: Colors.white,
                        ),
                      ),
                      const SizedBox(height: 6),
                      const Text(
                        'Kelola jadwal kerja dan booking Anda dengan mudah melalui dashboard ini.',
                        style: TextStyle(
                          fontSize: 12.5,
                          color: Colors.white,
                          height: 1.3,
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(width: 8),
                const Icon(
                  Icons.build_outlined,
                  size: 42,
                  color: Colors.white38,
                ),
              ],
            ),
          ),
          const SizedBox(height: 16),
          Row(
            children: [
              Expanded(
                child: _statWebCard(
                  title: 'Total Booking',
                  value: totalBooking.toString(),
                  icon: Icons.calendar_today_outlined,
                  iconColor: Colors.blue,
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: _statWebCard(
                  title: 'Booking Aktif',
                  value: activeBookings.length.toString(),
                  icon: Icons.access_time,
                  iconColor: Colors.orange,
                ),
              ),
            ],
          ),
          const SizedBox(height: 10),
          Row(
            children: [
              Expanded(
                child: _statWebCard(
                  title: 'Selesai',
                  value: completedBookings.length.toString(),
                  icon: Icons.check_circle_outline,
                  iconColor: Colors.green,
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: _ratingWebCard(rating: 0.0),
              ),
            ],
          ),
          const SizedBox(height: 20),
          Container(
            decoration: const BoxDecoration(
              border: Border(
                  bottom: BorderSide(color: Color(0xFFE5E7EB), width: 1.5)),
            ),
            child: SingleChildScrollView(
              scrollDirection: Axis.horizontal,
              child: Row(
                children: [
                  _tabFilterButton(
                    index: 0,
                    icon: Icons.hourglass_top_rounded,
                    label: 'Booking Aktif',
                  ),
                  const SizedBox(width: 12),
                  _tabFilterButton(
                    index: 1,
                    icon: Icons.calendar_month_outlined,
                    label: 'Jadwal Mendatang',
                  ),
                  const SizedBox(width: 12),
                  _tabFilterButton(
                    index: 2,
                    icon: Icons.check_circle_outline,
                    label: 'Selesai',
                  ),
                ],
              ),
            ),
          ),
          const SizedBox(height: 16),
          Builder(
            builder: (context) {
              List<Booking> currentList = [];
              String emptyMessage = '';

              if (_activeTabFilter == 0) {
                currentList = activeBookings;
                emptyMessage =
                'Tidak ada booking aktif saat ini. Selamat beristirahat!';
              } else if (_activeTabFilter == 1) {
                currentList = upcomingBookings;
                emptyMessage = 'Tidak ada jadwal pekerjaan mendatang saat ini.';
              } else {
                currentList = completedBookings;
                emptyMessage = 'Belum ada riwayat pekerjaan yang selesai.';
              }

              if (currentList.isEmpty) {
                return Container(
                  width: double.infinity,
                  padding: const EdgeInsets.symmetric(
                      vertical: 32, horizontal: 20),
                  decoration: BoxDecoration(
                    color: const Color(0xFFE0F7FA),
                    borderRadius: BorderRadius.circular(8),
                    border: Border.all(color: const Color(0xFFB2EBF2)),
                  ),
                  child: Column(
                    children: [
                      const Icon(
                        Icons.info_outline,
                        size: 40,
                        color: Color(0xFF00838F),
                      ),
                      const SizedBox(height: 12),
                      Text(
                        emptyMessage,
                        textAlign: TextAlign.center,
                        style: const TextStyle(
                          fontSize: 13,
                          fontWeight: FontWeight.w500,
                          color: Color(0xFF006064),
                        ),
                      ),
                    ],
                  ),
                );
              }

              return Column(
                children: currentList.map(_technicianBookingCard).toList(),
              );
            },
          ),
          const SizedBox(height: 20),
          const Text(
            'Aksi Cepat',
            style: TextStyle(
              fontSize: 15,
              fontWeight: FontWeight.bold,
              color: Colors.black87,
            ),
          ),
          const SizedBox(height: 10),
          Row(
            children: [
              Expanded(
                child: _quickActionButton(
                  icon: Icons.list_alt_rounded,
                  label: 'Booking',
                  color: AppColors.primary,
                  onTap: () => setState(() => _selectedIndex = 1),
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: _quickActionButton(
                  icon: Icons.person_outline,
                  label: 'Edit Profil',
                  color: Colors.blueGrey,
                  onTap: _navigateToEditProfile,
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: _quickActionButton(
                  icon: Icons.logout_rounded,
                  label: 'Logout',
                  color: AppColors.danger,
                  onTap: _logout,
                ),
              ),
            ],
          ),
          const SizedBox(height: 30),
        ],
      ),
    );
  }

  // --- HALAMAN PESANAN MASUK ---
  Widget _incomingOrdersPage() {
    return RefreshIndicator(
      onRefresh: _loadData,
      color: AppColors.primary,
      child: ListView(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
        children: [
          const Row(
            children: [
              Icon(Icons.assignment_turned_in_outlined,
                  size: 24, color: AppColors.primary),
              SizedBox(width: 8),
              Text(
                'Pesanan Masuk',
                style: TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.bold,
                  color: AppColors.textPrimary,
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),
          if (_bookings.isEmpty)
            const AppEmptyState(
              icon: Icons.inbox_outlined,
              title: 'Belum Ada Pesanan',
              description: 'Pesanan masuk dari pelanggan akan tampil di sini.',
            )
          else
            ..._bookings.map(_incomingOrderCard),
        ],
      ),
    );
  }

  Widget _incomingOrderCard(Booking booking) {
    final customerName = booking.customer?['name']?.toString() ?? '-';
    final customerPhone = booking.customer?['phone']?.toString() ?? '-';
    final customerAddress =
        booking.customer?['address']?.toString() ?? 'Alamat belum tersedia';
    final location = booking.serviceLocation.isNotEmpty
        ? booking.serviceLocation
        : customerAddress;

    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      elevation: 1,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(10),
        side: const BorderSide(color: Color(0xFFE5E7EB)),
      ),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Row(
                  children: [
                    const Icon(Icons.calendar_today_outlined,
                        size: 14, color: AppColors.textMuted),
                    const SizedBox(width: 6),
                    Text(
                      formatDate(booking.scheduledDate),
                      style: const TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w600,
                        color: AppColors.textSecondary,
                      ),
                    ),
                  ],
                ),
                AppStatusBadge(status: booking.status),
              ],
            ),
            const Divider(height: 20, thickness: 0.8),
            Text(
              booking.service?.name ?? 'Layanan Servis',
              style: const TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.bold,
                color: AppColors.textPrimary,
              ),
            ),
            const SizedBox(height: 8),
            _infoRow(Icons.person_outline, 'Pelanggan: $customerName ($customerPhone)'),
            _infoRow(Icons.location_on_outlined, 'Lokasi: $location'),
            const SizedBox(height: 12),
            Row(
              mainAxisAlignment: MainAxisAlignment.end,
              children: [
                IconButton.outlined(
                  onPressed: () async {
                    final result = await Navigator.push<bool>(
                      context,
                      MaterialPageRoute(
                        builder: (context) => BookingDetailScreen(booking: booking),
                      ),
                    );

                    if (result == true) {
                      _loadData();
                    }
                  },
                  icon: const Icon(Icons.remove_red_eye_outlined, size: 18),
                  style: IconButton.styleFrom(
                    foregroundColor: AppColors.primary,
                    side: const BorderSide(color: AppColors.primaryLight),
                  ),
                  tooltip: 'Detail Pesanan',
                ),
              ],
            ),
          ],
        ),
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
          if (history.isEmpty)
            const AppEmptyState(
              icon: Icons.history_outlined,
              title: 'Riwayat Kosong',
              description:
              'Pekerjaan yang telah selesai atau dibatalkan akan muncul di sini.',
            )
          else
            ...history.map(_technicianBookingCard),
        ],
      ),
    );
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
                        backgroundImage: (_user.photoUrl.isNotEmpty)
                            ? NetworkImage(_user.photoUrl.startsWith('http')
                            ? _user.photoUrl
                            : 'http://10.0.2.2:8000/storage/${_user.photoUrl}')
                        as ImageProvider
                            : null,
                        child: _user.photoUrl.isEmpty
                            ? Text(
                          _user.name.isNotEmpty
                              ? _user.name.substring(0, 1).toUpperCase()
                              : 'T',
                          style: const TextStyle(
                            fontSize: 22,
                            fontWeight: FontWeight.w800,
                            color: AppColors.primary,
                          ),
                        )
                            : null,
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
                  _infoRow(
                    Icons.phone_outlined,
                    _user.phone.isEmpty ? 'Belum diatur' : _user.phone,
                  ),
                  _infoRow(
                    Icons.location_on_outlined,
                    _user.address.isEmpty ? 'Belum diatur' : _user.address,
                  ),
                  _infoRow(
                    Icons.build_outlined,
                    _user.specialization.isEmpty
                        ? 'Umum'
                        : _user.specialization,
                  ),
                  const SizedBox(height: 8),
                  _infoRow(
                    Icons.description_outlined,
                    _user.description.isEmpty
                        ? 'Belum ada deskripsi'
                        : _user.description,
                  ),
                  _infoRow(
                    Icons.work_history_outlined,
                    _user.yearsExperience == 0
                        ? 'Pengalaman belum diatur'
                        : '${_user.yearsExperience} Tahun Pengalaman',
                  ),
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      const Icon(Icons.verified_user_outlined,
                          size: 16, color: AppColors.textMuted),
                      const SizedBox(width: 8),
                      const Text(
                        'Status: ',
                        style: TextStyle(
                            fontSize: 13, color: AppColors.textSecondary),
                      ),
                      const SizedBox(width: 4),
                      AppStatusBadge(
                        status: _translateStatus(_user.status),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ),
          const SizedBox(height: 16),
          FilledButton.tonalIcon(
            onPressed: _navigateToEditProfile,
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
              side: const BorderSide(
                color: AppColors.dangerLight,
                width: 1.2,
              ),
            ),
            icon: const Icon(Icons.logout_rounded, size: 18),
            label: const Text('Keluar dari Akun'),
          ),
        ],
      ),
    );
  }

  Widget _statWebCard({
    required String title,
    required String value,
    required IconData icon,
    required Color iconColor,
  }) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(10),
        border: Border.all(color: const Color(0xFFE5E7EB)),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                title,
                style: const TextStyle(
                  fontSize: 12,
                  color: Colors.grey,
                  fontWeight: FontWeight.w500,
                ),
              ),
              const SizedBox(height: 6),
              Text(
                value,
                style: const TextStyle(
                  fontSize: 22,
                  fontWeight: FontWeight.bold,
                  color: Colors.black87,
                ),
              ),
            ],
          ),
          Icon(icon, size: 28, color: iconColor.withOpacity(0.5)),
        ],
      ),
    );
  }

  Widget _ratingWebCard({required double rating}) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(10),
        border: Border.all(color: const Color(0xFFE5E7EB)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Rating Rata-rata',
            style: TextStyle(
              fontSize: 12,
              color: Colors.grey,
              fontWeight: FontWeight.w500,
            ),
          ),
          const SizedBox(height: 6),
          Row(
            children: [
              ...List.generate(
                5,
                    (index) => Icon(
                  index < rating.floor()
                      ? Icons.star
                      : Icons.star_border_rounded,
                  size: 16,
                  color: Colors.amber,
                ),
              ),
              const Spacer(),
              const Icon(Icons.star, size: 24, color: Colors.amber),
            ],
          ),
          const SizedBox(height: 2),
          Text(
            '${rating.toStringAsFixed(1)}/5.0',
            style: const TextStyle(fontSize: 11, color: Colors.grey),
          ),
        ],
      ),
    );
  }

  Widget _tabFilterButton({
    required int index,
    required IconData icon,
    required String label,
  }) {
    final isSelected = _activeTabFilter == index;

    return InkWell(
      onTap: () => setState(() => _activeTabFilter = index),
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 10, horizontal: 8),
        decoration: BoxDecoration(
          border: Border(
            bottom: BorderSide(
              color: isSelected ? AppColors.primary : Colors.transparent,
              width: 2.5,
            ),
          ),
        ),
        child: Row(
          children: [
            Icon(
              icon,
              size: 16,
              color: isSelected ? AppColors.primary : Colors.grey[600],
            ),
            const SizedBox(width: 6),
            Text(
              label,
              style: TextStyle(
                fontSize: 13,
                fontWeight: isSelected ? FontWeight.bold : FontWeight.w500,
                color: isSelected ? AppColors.primary : Colors.grey[700],
              ),
            ),
          ],
        ),
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
                Expanded(
                  child: Text(
                    booking.service?.name ?? 'Layanan Servis',
                    style: const TextStyle(
                      fontSize: 15,
                      fontWeight: FontWeight.w700,
                      color: AppColors.textPrimary,
                    ),
                  ),
                ),
                AppStatusBadge(status: booking.status),
              ],
            ),
            const SizedBox(height: 10),
            _infoRow(Icons.person_outline, 'Customer: $customerName'),
            if (customerPhone != '-')
              _infoRow(Icons.phone_outlined, 'Kontak: $customerPhone'),
            _infoRow(Icons.calendar_today_outlined,
                'Jadwal: ${formatDate(booking.scheduledDate)}'),
            _infoRow(Icons.location_on_outlined, location),
          ],
        ),
      ),
    );
  }

  Widget _infoRow(IconData icon, String text) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 6),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, size: 16, color: AppColors.textMuted),
          const SizedBox(width: 8),
          Expanded(
            child: Text(
              text,
              style: const TextStyle(
                  fontSize: 13, color: AppColors.textSecondary),
            ),
          ),
        ],
      ),
    );
  }

  Widget _quickActionButton({
    required IconData icon,
    required String label,
    required Color color,
    required VoidCallback onTap,
  }) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(10),
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 14, horizontal: 8),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(10),
          border: Border.all(color: const Color(0xFFE5E7EB)),
        ),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(icon, size: 22, color: color),
            const SizedBox(height: 6),
            Text(
              label,
              style: TextStyle(
                fontSize: 12,
                fontWeight: FontWeight.w600,
                color: Colors.grey[800],
              ),
              textAlign: TextAlign.center,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
            ),
          ],
        ),
      ),
    );
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
              decoration:
              const InputDecoration(labelText: 'Password Saat Ini'),
            ),
            const SizedBox(height: 10),
            TextField(
              controller: password,
              obscureText: true,
              decoration: const InputDecoration(
                  labelText: 'Password Baru (min 8 karakter)'),
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

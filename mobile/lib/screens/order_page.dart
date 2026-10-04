import 'package:flutter/material.dart';

import '../config/app_theme.dart';
import '../models/service_item.dart';
import '../models/technician.dart';
import '../services/api_service.dart';
import '../utils/formatters.dart';
import '../widgets/app_empty_state.dart';
import '../widgets/app_section_header.dart';

class OrderPage extends StatefulWidget {
  final VoidCallback? onCreated;
  final int? initialServiceId;
  final Future<List<ServiceItem>> Function()? servicesLoader;
  final Future<List<Technician>> Function()? techniciansLoader;

  const OrderPage({
    super.key,
    this.onCreated,
    this.initialServiceId,
    this.servicesLoader,
    this.techniciansLoader,
  });

  @override
  State<OrderPage> createState() => _OrderPageState();
}

class _OrderPageState extends State<OrderPage> {
  final _notesController = TextEditingController();
  final _locationController = TextEditingController();
  bool _isLoading = true;
  bool _isSubmitting = false;
  List<ServiceItem> _services = [];
  List<Technician> _technicians = [];
  ServiceItem? _selectedService;
  Technician? _selectedTechnician;
  DateTime? _selectedDate;
  TimeOfDay? _selectedTime;

  @override
  void initState() {
    super.initState();
    _loadData();
  }


  @override
  void dispose() {
    _notesController.dispose();
    _locationController.dispose();
    super.dispose();
  }

  Future<void> _loadData() async {
    if (!_isLoading) {
      setState(() => _isLoading = true);
    }
    try {
      final servicesFuture = widget.servicesLoader != null
          ? widget.servicesLoader!()
          : ApiService.getServices();
      final techniciansFuture = widget.techniciansLoader != null
          ? widget.techniciansLoader!()
          : ApiService.getTechnicians();

      final results = await Future.wait([
        servicesFuture,
        techniciansFuture,
      ]);
      if (!mounted) return;
      setState(() {
        _services = results[0] as List<ServiceItem>;
        _technicians = results[1] as List<Technician>;
        if (_services.isNotEmpty && _selectedService == null) {
          _selectedService = _services.firstWhere(
            (s) => s.id == widget.initialServiceId,
            orElse: () => _services.first,
          );
        }
        _isLoading = false;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() => _isLoading = false);
      _showError(e.toString());
    }
  }


  Future<void> _selectDate() async {
    final picked = await showDatePicker(
      context: context,
      initialDate: _selectedDate ?? DateTime.now().add(const Duration(days: 1)),
      firstDate: DateTime.now(),
      lastDate: DateTime.now().add(const Duration(days: 60)),
    );
    if (picked != null) setState(() => _selectedDate = picked);
  }

  Future<void> _selectTime() async {
    final picked = await showTimePicker(
      context: context,
      initialTime: _selectedTime ?? const TimeOfDay(hour: 9, minute: 0),
    );
    if (picked != null) setState(() => _selectedTime = picked);
  }

  Future<void> _submit() async {
    if (_selectedService == null) {
      _showError('Silakan pilih jenis layanan servis AC.');
      return;
    }

    if (_selectedDate == null || _selectedTime == null) {
      _showError('Silakan tentukan tanggal dan waktu servis.');
      return;
    }

    if (_locationController.text.trim().isEmpty) {
      _showError('Alamat lokasi layanan wajib diisi.');
      return;
    }

    final scheduledDate = DateTime(
      _selectedDate!.year,
      _selectedDate!.month,
      _selectedDate!.day,
      _selectedTime!.hour,
      _selectedTime!.minute,
    );

    if (!scheduledDate.isAfter(DateTime.now())) {
      _showError('Jadwal servis harus lebih dari waktu saat ini.');
      return;
    }

    setState(() => _isSubmitting = true);
    try {
      await ApiService.createBooking(
        serviceId: _selectedService!.id,
        technicianId: _selectedTechnician?.id,
        scheduledDate: scheduledDate,
        serviceLocation: _locationController.text.trim(),
        notes: _notesController.text.trim().isEmpty
            ? null
            : _notesController.text.trim(),
      );

      if (!mounted) return;
      setState(() {
        _selectedDate = null;
        _selectedTime = null;
        _locationController.clear();
        _notesController.clear();
      });
      widget.onCreated?.call();
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: const Text('Booking servis berhasil dibuat!'),
          backgroundColor: AppColors.success,
          behavior: SnackBarBehavior.floating,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(AppRadius.sm),
          ),
        ),
      );
    } catch (e) {
      if (!mounted) return;
      _showError(e.toString());
    } finally {
      if (mounted) setState(() => _isSubmitting = false);
    }
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

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return const Center(
        child: CircularProgressIndicator(
          valueColor: AlwaysStoppedAnimation(AppColors.primary),
        ),
      );
    }

    return Column(
      children: [
        Expanded(
          child: RefreshIndicator(
            onRefresh: _loadData,
            color: AppColors.primary,
            child: ListView(
              keyboardDismissBehavior: ScrollViewKeyboardDismissBehavior.onDrag,
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 20),
              children: [
                // Step 1: Services
                const AppSectionHeader(title: '1. Pilih Layanan AC'),
                if (_services.isEmpty)
                  AppEmptyState(
                    icon: Icons.ac_unit_outlined,
                    title: 'Layanan Belum Tersedia',
                    description:
                        'Daftar layanan servis AC sedang diperbarui atau koneksi terganggu.',
                    actionLabel: 'Muat Ulang',
                    onAction: _loadData,
                  )
                else
                  ..._services.map(_renderServiceCard),

                const SizedBox(height: 20),

                // Step 2: Technician
                const AppSectionHeader(title: '2. Pilih Teknisi (Opsional)'),
                DropdownButtonFormField<Technician?>(
                  isExpanded: true,
                  initialValue: _selectedTechnician,
                  decoration: const InputDecoration(
                    labelText: 'Teknisi Servis',
                    prefixIcon: Icon(Icons.person_outline, size: 20),
                  ),
                  items: [
                    const DropdownMenuItem<Technician?>(
                      value: null,
                      child: Text(
                        'Pilih otomatis teknisi terbaik yang tersedia',
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                    ..._technicians.map(
                      (technician) => DropdownMenuItem<Technician?>(
                        value: technician,
                        child: Text(
                          '${technician.name} (${technician.specialization.isEmpty ? 'Umum' : technician.specialization})',
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ),
                  ],
                  onChanged: (value) =>
                      setState(() => _selectedTechnician = value),
                ),
                const SizedBox(height: 20),


                // Step 3: Schedule
                const AppSectionHeader(title: '3. Jadwal Kunjungan'),
                Row(
                  children: [
                    Expanded(
                      child: OutlinedButton.icon(
                        onPressed: _selectDate,
                        style: OutlinedButton.styleFrom(
                          padding: const EdgeInsets.symmetric(
                              horizontal: 12, vertical: 12),
                          side: BorderSide(
                            color: _selectedDate != null
                                ? AppColors.primary
                                : AppColors.border,
                            width: 1.2,
                          ),
                        ),
                        icon: Icon(
                          Icons.calendar_today_outlined,
                          size: 18,
                          color: _selectedDate != null
                              ? AppColors.primary
                              : AppColors.textSecondary,
                        ),
                        label: Text(
                          _selectedDate == null
                              ? 'Pilih Tanggal'
                              : '${_selectedDate!.day}/${_selectedDate!.month}/${_selectedDate!.year}',
                          style: TextStyle(
                            fontSize: 13,
                            color: _selectedDate != null
                                ? AppColors.primaryDark
                                : AppColors.textPrimary,
                            fontWeight: _selectedDate != null
                                ? FontWeight.w600
                                : FontWeight.normal,
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(width: 10),
                    Expanded(
                      child: OutlinedButton.icon(
                        onPressed: _selectTime,
                        style: OutlinedButton.styleFrom(
                          padding: const EdgeInsets.symmetric(
                              horizontal: 12, vertical: 12),
                          side: BorderSide(
                            color: _selectedTime != null
                                ? AppColors.primary
                                : AppColors.border,
                            width: 1.2,
                          ),
                        ),
                        icon: Icon(
                          Icons.schedule_outlined,
                          size: 18,
                          color: _selectedTime != null
                              ? AppColors.primary
                              : AppColors.textSecondary,
                        ),
                        label: Text(
                          _selectedTime == null
                              ? 'Pilih Waktu'
                              : _selectedTime!.format(context),
                          style: TextStyle(
                            fontSize: 13,
                            color: _selectedTime != null
                                ? AppColors.primaryDark
                                : AppColors.textPrimary,
                            fontWeight: _selectedTime != null
                                ? FontWeight.w600
                                : FontWeight.normal,
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 20),

                // Step 4: Location & Notes
                const AppSectionHeader(title: '4. Alamat & Catatan'),
                TextField(
                  controller: _locationController,
                  maxLines: 2,
                  decoration: const InputDecoration(
                    labelText: 'Lokasi / Alamat Lengkap Servis',
                    hintText:
                        'Contoh: Jl. Mawar No. 12, RT 02/05, Rumah pagar hitam',
                    prefixIcon: Icon(Icons.location_on_outlined, size: 20),
                  ),
                ),
                const SizedBox(height: 14),
                TextField(
                  controller: _notesController,
                  maxLines: 2,
                  decoration: const InputDecoration(
                    labelText: 'Catatan Tambahan (Opsional)',
                    hintText:
                        'Contoh: AC Daikin 1 PK di lantai 2, tidak dingin',
                    prefixIcon: Icon(Icons.note_alt_outlined, size: 20),
                  ),
                ),
                const SizedBox(height: 16),
              ],
            ),
          ),
        ),

        // Pinned Bottom Bar
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
          decoration: BoxDecoration(
            color: AppColors.surface,
            border: const Border(
              top: BorderSide(color: AppColors.border, width: 1),
            ),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withValues(alpha: 0.04),
                blurRadius: 10,
                offset: const Offset(0, -4),
              ),
            ],
          ),
          child: SafeArea(
            top: false,
            child: Row(
              children: [
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      const Text(
                        'Perkiraan Biaya',
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.w500,
                          color: AppColors.textMuted,
                        ),
                      ),
                      Text(
                        _selectedService != null
                            ? formatRupiah(_selectedService!.price)
                            : 'Rp 0',
                        style: const TextStyle(
                          fontSize: 17,
                          fontWeight: FontWeight.w800,
                          color: AppColors.primary,
                          letterSpacing: -0.3,
                        ),
                      ),
                    ],
                  ),
                ),
                SizedBox(
                  height: 46,
                  child: FilledButton(
                    onPressed: _isSubmitting ? null : _submit,
                    style: FilledButton.styleFrom(
                      minimumSize: const Size(0, 46),
                      padding: const EdgeInsets.symmetric(horizontal: 24),
                    ),
                    child: _isSubmitting

                        ? const SizedBox(
                            width: 20,
                            height: 20,
                            child: CircularProgressIndicator(
                              strokeWidth: 2,
                              valueColor: AlwaysStoppedAnimation(Colors.white),
                            ),
                          )
                        : const Text('Buat Booking'),
                  ),
                ),
              ],
            ),
          ),
        ),
      ],
    );
  }

  Widget _renderServiceCard(ServiceItem service) {
    final isSelected = _selectedService?.id == service.id;

    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      decoration: BoxDecoration(
        color: isSelected ? AppColors.primaryLight : AppColors.surface,
        borderRadius: BorderRadius.circular(AppRadius.md),
        border: Border.all(
          color: isSelected ? AppColors.primary : AppColors.border,
          width: isSelected ? 1.5 : 1,
        ),
      ),
      child: InkWell(
        onTap: () => setState(() => _selectedService = service),
        borderRadius: BorderRadius.circular(AppRadius.md),
        child: Padding(
          padding: const EdgeInsets.all(14),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                margin: const EdgeInsets.only(top: 2),
                width: 20,
                height: 20,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  border: Border.all(
                    color: isSelected ? AppColors.primary : AppColors.textMuted,
                    width: isSelected ? 6 : 2,
                  ),
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Expanded(
                          child: Text(
                            service.name,
                            style: TextStyle(
                              fontSize: 15,
                              fontWeight: FontWeight.w700,
                              color: isSelected
                                  ? AppColors.primaryDark
                                  : AppColors.textPrimary,
                            ),
                          ),
                        ),
                        Text(
                          formatRupiah(service.price),
                          style: TextStyle(
                            fontSize: 14,
                            fontWeight: FontWeight.w700,
                            color: isSelected
                                ? AppColors.primary
                                : AppColors.textPrimary,
                          ),
                        ),
                      ],
                    ),
                    if (service.description.isNotEmpty) ...[
                      const SizedBox(height: 4),
                      Text(
                        service.description,
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                        style: const TextStyle(
                          fontSize: 12,
                          color: AppColors.textSecondary,
                          height: 1.3,
                        ),
                      ),
                    ],
                    const SizedBox(height: 6),
                    Wrap(
                      spacing: 6,
                      runSpacing: 6,
                      children: [
                        Container(
                          padding: const EdgeInsets.symmetric(
                              horizontal: 8, vertical: 2),
                          decoration: BoxDecoration(
                            color: isSelected
                                ? Colors.white
                                : AppColors.surfaceElevated,
                            borderRadius: BorderRadius.circular(AppRadius.sm),
                          ),
                          child: Text(
                            'Estimasi ${service.durationMinutes} menit',
                            style: const TextStyle(
                              fontSize: 11,
                              fontWeight: FontWeight.w500,
                              color: AppColors.textSecondary,
                            ),
                          ),
                        ),
                        if (service.category.isNotEmpty)
                          Container(
                            padding: const EdgeInsets.symmetric(
                                horizontal: 8, vertical: 2),
                            decoration: BoxDecoration(
                              color: isSelected
                                  ? Colors.white
                                  : AppColors.surfaceElevated,
                              borderRadius: BorderRadius.circular(AppRadius.sm),
                            ),
                            child: Text(
                              service.category,
                              style: const TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.w500,
                                color: AppColors.textSecondary,
                              ),
                            ),
                          ),
                      ],
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

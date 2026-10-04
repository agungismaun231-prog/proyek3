import 'package:flutter/material.dart';

import '../config/app_theme.dart';
import '../models/user.dart';
import '../services/api_service.dart';
import 'technician_dashboard.dart';

class RegisterTechnicianScreen extends StatefulWidget {
  const RegisterTechnicianScreen({super.key});

  @override
  State<RegisterTechnicianScreen> createState() =>
      _RegisterTechnicianScreenState();
}

class _RegisterTechnicianScreenState extends State<RegisterTechnicianScreen> {
  final _formKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final _emailController = TextEditingController();
  final _phoneController = TextEditingController();
  final _addressController = TextEditingController();
  final _specializationController = TextEditingController();
  final _passwordController = TextEditingController();
  final _confirmPasswordController = TextEditingController();
  bool _isLoading = false;
  bool _obscurePassword = true;
  bool _obscureConfirmPassword = true;

  @override
  void dispose() {
    _nameController.dispose();
    _emailController.dispose();
    _phoneController.dispose();
    _addressController.dispose();
    _specializationController.dispose();
    _passwordController.dispose();
    _confirmPasswordController.dispose();
    super.dispose();
  }

  Future<void> _register() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() => _isLoading = true);
    try {
      final result = await ApiService.registerTechnician(
        name: _nameController.text.trim(),
        email: _emailController.text.trim(),
        password: _passwordController.text,
        passwordConfirmation: _confirmPasswordController.text,
        phone: _phoneController.text.trim(),
        address: _addressController.text.trim(),
        specialization: _specializationController.text.trim().isEmpty
            ? 'Spesialis Servis AC'
            : _specializationController.text.trim(),
      );

      if (!mounted) return;
      _goToDashboard(result.user);
    } catch (e) {
      if (!mounted) return;
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
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  void _goToDashboard(User user) {
    Navigator.pushAndRemoveUntil(
      context,
      MaterialPageRoute(builder: (_) => TechnicianDashboard(initialUser: user)),
      (_) => false,
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('Daftar Mitra Teknisi'),
      ),
      body: SafeArea(
        child: Center(
          child: SingleChildScrollView(
            keyboardDismissBehavior: ScrollViewKeyboardDismissBehavior.onDrag,
            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 24),
            child: ConstrainedBox(
              constraints: const BoxConstraints(maxWidth: 520),
              child: Form(
                key: _formKey,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    Container(
                      padding: const EdgeInsets.all(20),
                      decoration: BoxDecoration(
                        color: AppColors.surface,
                        borderRadius: BorderRadius.circular(AppRadius.lg),
                        border: Border.all(color: AppColors.border, width: 1),
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.stretch,
                        children: [
                          const Text(
                            'Informasi Profil Mitra',
                            style: TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.w700,
                              color: AppColors.textPrimary,
                              letterSpacing: -0.2,
                            ),
                          ),
                          const SizedBox(height: 14),
                          TextFormField(
                            controller: _nameController,
                            decoration: const InputDecoration(
                              labelText: 'Nama Lengkap',
                              prefixIcon: Icon(Icons.person_outline, size: 20),
                            ),
                            validator: (v) =>
                                v == null || v.trim().isEmpty ? 'Nama lengkap wajib diisi.' : null,
                          ),
                          const SizedBox(height: 14),
                          TextFormField(
                            controller: _emailController,
                            keyboardType: TextInputType.emailAddress,
                            decoration: const InputDecoration(
                              labelText: 'Email',
                              prefixIcon: Icon(Icons.mail_outline, size: 20),
                            ),
                            validator: _emailValidator,
                          ),
                          const SizedBox(height: 14),
                          TextFormField(
                            controller: _phoneController,
                            keyboardType: TextInputType.phone,
                            decoration: const InputDecoration(
                              labelText: 'Nomor Telepon / WhatsApp',
                              prefixIcon: Icon(Icons.phone_outlined, size: 20),
                            ),
                            validator: (v) =>
                                v == null || v.trim().isEmpty ? 'Nomor telepon wajib diisi.' : null,
                          ),
                          const SizedBox(height: 14),
                          TextFormField(
                            controller: _specializationController,
                            decoration: const InputDecoration(
                              labelText: 'Spesialisasi / Keahlian',
                              hintText: 'Contoh: Cuci AC, Bongkar Pasang, Perbaikan Inverter',
                              prefixIcon: Icon(Icons.build_outlined, size: 20),
                            ),
                          ),
                          const SizedBox(height: 14),
                          TextFormField(
                            controller: _addressController,
                            maxLines: 2,
                            decoration: const InputDecoration(
                              labelText: 'Alamat Domisili Operasional',
                              prefixIcon: Icon(Icons.location_on_outlined, size: 20),
                            ),
                            validator: (v) =>
                                v == null || v.trim().isEmpty ? 'Alamat wajib diisi.' : null,
                          ),
                          const SizedBox(height: 20),
                          const Text(
                            'Keamanan Akun',
                            style: TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.w700,
                              color: AppColors.textPrimary,
                              letterSpacing: -0.2,
                            ),
                          ),
                          const SizedBox(height: 14),
                          TextFormField(
                            controller: _passwordController,
                            obscureText: _obscurePassword,
                            decoration: InputDecoration(
                              labelText: 'Password',
                              prefixIcon: const Icon(Icons.lock_outline, size: 20),
                              suffixIcon: IconButton(
                                icon: Icon(
                                  _obscurePassword
                                      ? Icons.visibility_outlined
                                      : Icons.visibility_off_outlined,
                                  size: 20,
                                ),
                                onPressed: () =>
                                    setState(() => _obscurePassword = !_obscurePassword),
                              ),
                            ),
                            validator: _passwordValidator,
                          ),
                          const SizedBox(height: 14),
                          TextFormField(
                            controller: _confirmPasswordController,
                            obscureText: _obscureConfirmPassword,
                            decoration: InputDecoration(
                              labelText: 'Konfirmasi Password',
                              prefixIcon: const Icon(Icons.lock_outline, size: 20),
                              suffixIcon: IconButton(
                                icon: Icon(
                                  _obscureConfirmPassword
                                      ? Icons.visibility_outlined
                                      : Icons.visibility_off_outlined,
                                  size: 20,
                                ),
                                onPressed: () => setState(() =>
                                    _obscureConfirmPassword = !_obscureConfirmPassword),
                              ),
                            ),
                            validator: (value) {
                              if (value != _passwordController.text) {
                                return 'Konfirmasi password tidak cocok.';
                              }
                              return _passwordValidator(value);
                            },
                          ),
                          const SizedBox(height: 24),
                          FilledButton(
                            onPressed: _isLoading ? null : _register,
                            child: _isLoading
                                ? const SizedBox(
                                    width: 20,
                                    height: 20,
                                    child: CircularProgressIndicator(
                                      strokeWidth: 2,
                                      valueColor: AlwaysStoppedAnimation(Colors.white),
                                    ),
                                  )
                                : const Text('Daftar Mitra Teknisi'),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }

  String? _emailValidator(String? value) {
    if (value == null || value.trim().isEmpty) return 'Email wajib diisi.';
    if (!value.contains('@') || !value.contains('.')) return 'Format email tidak valid.';
    return null;
  }

  String? _passwordValidator(String? value) {
    if (value == null || value.isEmpty) return 'Password wajib diisi.';
    if (value.length < 8) return 'Password minimal 8 karakter.';
    return null;
  }
}

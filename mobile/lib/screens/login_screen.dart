import 'package:flutter/material.dart';
import 'package:google_sign_in/google_sign_in.dart';

import '../config/app_theme.dart';
import '../models/user.dart';
import '../services/api_service.dart';
import 'admin_dashboard.dart';
import 'customer_dashboard.dart';
import 'register_screen.dart';
import 'register_technician_screen.dart';
import 'technician_dashboard.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  bool _isLoading = false;
  bool _obscurePassword = true;

  @override
  void dispose() {
    _emailController.dispose();
    _passwordController.dispose();
    super.dispose();
  }

  Future<void> _login() async {
    if (_emailController.text.trim().isEmpty ||
        _passwordController.text.isEmpty) {
      _showError('Email dan password wajib diisi.');
      return;
    }

    await _runAuth(() =>
        ApiService.login(_emailController.text.trim(), _passwordController.text));
  }

  Future<void> _loginWithGoogle() async {
    try {
      final account = await GoogleSignIn(scopes: ['email']).signIn();
      if (account == null) return;
      final auth = await account.authentication;
      final idToken = auth.idToken;
      if (idToken == null || idToken.isEmpty) {
        _showError('Token Google tidak tersedia. Periksa konfigurasi OAuth.');
        return;
      }

      await _runAuth(() => ApiService.googleLoginMobile(
          account.email, account.displayName ?? 'User', idToken));
    } catch (e) {
      _showError(e.toString());
    }
  }

  Future<void> _runAuth(Future<AuthResult> Function() action) async {
    setState(() => _isLoading = true);

    try {
      final result = await action();
      if (!mounted) return;
      _goToDashboard(result.user);
    } catch (e) {
      if (!mounted) return;
      _showError(e.toString());
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  void _goToDashboard(User user) {
    Navigator.pushReplacement(
      context,
      MaterialPageRoute(
        builder: (_) {
          if (user.isAdmin) {
            return AdminDashboard(initialUser: user);
          }
          if (user.isTechnician) {
            return TechnicianDashboard(initialUser: user);
          }
          return CustomerDashboard(initialUser: user);
        },
      ),
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

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        child: Center(
          child: SingleChildScrollView(
            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 24),
            child: ConstrainedBox(
              constraints: const BoxConstraints(maxWidth: 420),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  // Brand Header
                  Center(
                    child: Container(
                      width: 68,
                      height: 68,
                      decoration: BoxDecoration(
                        color: AppColors.primaryLight,
                        borderRadius: BorderRadius.circular(AppRadius.lg),
                        border: Border.all(
                          color: AppColors.primary.withValues(alpha: 0.15),
                          width: 1,
                        ),
                      ),
                      child: const Icon(
                        Icons.ac_unit_rounded,
                        size: 36,
                        color: AppColors.primary,
                      ),
                    ),
                  ),
                  const SizedBox(height: 16),
                  const Text(
                    'JASAKU',
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      fontSize: 26,
                      fontWeight: FontWeight.w800,
                      color: AppColors.textPrimary,
                      letterSpacing: -0.5,
                    ),
                  ),
                  const SizedBox(height: 6),
                  const Text(
                    'Layanan servis dan perawatan AC profesional',
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      fontSize: 14,
                      color: AppColors.textSecondary,
                    ),
                  ),
                  const SizedBox(height: 28),

                  // Form Container
                  Container(
                    padding: const EdgeInsets.all(24),
                    decoration: BoxDecoration(
                      color: AppColors.surface,
                      borderRadius: BorderRadius.circular(AppRadius.lg),
                      border: Border.all(color: AppColors.border, width: 1),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        const Text(
                          'Masuk ke Akun Anda',
                          style: TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.w700,
                            color: AppColors.textPrimary,
                            letterSpacing: -0.3,
                          ),
                        ),
                        const SizedBox(height: 18),
                        TextField(
                          controller: _emailController,
                          keyboardType: TextInputType.emailAddress,
                          decoration: const InputDecoration(
                            labelText: 'Email',
                            hintText: 'nama@example.com',
                            prefixIcon: Icon(Icons.mail_outline, size: 20),
                          ),
                        ),
                        const SizedBox(height: 16),
                        TextField(
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
                              onPressed: () {
                                setState(() {
                                  _obscurePassword = !_obscurePassword;
                                });
                              },
                            ),
                          ),
                          onSubmitted: (_) => _login(),
                        ),
                        const SizedBox(height: 22),
                        FilledButton(
                          onPressed: _isLoading ? null : _login,
                          child: _isLoading
                              ? const SizedBox(
                                  width: 20,
                                  height: 20,
                                  child: CircularProgressIndicator(
                                    strokeWidth: 2,
                                    valueColor:
                                        AlwaysStoppedAnimation(Colors.white),
                                  ),
                                )
                              : const Text('Masuk'),
                        ),
                        const SizedBox(height: 12),
                        OutlinedButton.icon(
                          onPressed: _isLoading ? null : _loginWithGoogle,
                          icon: const Icon(Icons.g_mobiledata, size: 24),
                          label: const Text('Masuk dengan Google'),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 24),

                  // Registration Links
                  Container(
                    padding: const EdgeInsets.symmetric(
                        horizontal: 16, vertical: 12),
                    decoration: BoxDecoration(
                      color: AppColors.surface,
                      borderRadius: BorderRadius.circular(AppRadius.md),
                      border: Border.all(color: AppColors.border, width: 1),
                    ),
                    child: Column(
                      children: [
                        const Text(
                          'Belum memiliki akun?',
                          style: TextStyle(
                            fontSize: 13,
                            color: AppColors.textSecondary,
                          ),
                        ),
                        const SizedBox(height: 8),
                        Row(
                          children: [
                            Expanded(
                              child: OutlinedButton(
                                onPressed: _isLoading
                                    ? null
                                    : () => Navigator.push(
                                          context,
                                          MaterialPageRoute(
                                            builder: (_) =>
                                                const RegisterScreen(),
                                          ),
                                        ),
                                style: OutlinedButton.styleFrom(
                                  padding: const EdgeInsets.symmetric(
                                      vertical: 10),
                                  minimumSize: const Size(0, 42),
                                ),
                                child: const Text(
                                  'Daftar Customer',
                                  style: TextStyle(fontSize: 13),
                                ),
                              ),
                            ),
                            const SizedBox(width: 10),
                            Expanded(
                              child: OutlinedButton(
                                onPressed: _isLoading
                                    ? null
                                    : () => Navigator.push(
                                          context,
                                          MaterialPageRoute(
                                            builder: (_) =>
                                                const RegisterTechnicianScreen(),
                                          ),
                                        ),
                                style: OutlinedButton.styleFrom(
                                  padding: const EdgeInsets.symmetric(
                                      vertical: 10),
                                  minimumSize: const Size(0, 42),
                                ),
                                child: const Text(
                                  'Daftar Teknisi',
                                  style: TextStyle(fontSize: 13),
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
        ),
      ),
    );
  }
}

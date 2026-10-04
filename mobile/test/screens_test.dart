import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:proyek2/config/app_theme.dart';
import 'package:proyek2/screens/login_screen.dart';
import 'package:proyek2/screens/register_screen.dart';
import 'package:proyek2/screens/register_technician_screen.dart';

void main() {
  group('Authentication Screens UI Tests', () {
    testWidgets('LoginScreen renders brand header, inputs, and action buttons',
        (tester) async {
      await tester.pumpWidget(
        MaterialApp(
          theme: AppTheme.lightTheme,
          home: const LoginScreen(),
        ),
      );

      // Verify brand header
      expect(find.text('JASAKU'), findsOneWidget);
      expect(find.text('Masuk ke Akun Anda'), findsOneWidget);

      // Verify input fields
      expect(find.byType(TextField), findsNWidgets(2));
      expect(find.text('Email'), findsOneWidget);
      expect(find.text('Password'), findsOneWidget);

      // Verify buttons
      expect(find.text('Masuk'), findsOneWidget);
      expect(find.text('Masuk dengan Google'), findsOneWidget);
      expect(find.text('Daftar Customer'), findsOneWidget);
      expect(find.text('Daftar Teknisi'), findsOneWidget);
    });

    testWidgets('RegisterScreen renders customer registration form with validation',
        (tester) async {
      tester.view.physicalSize = const Size(800, 1400);
      addTearDown(tester.view.resetPhysicalSize);

      await tester.pumpWidget(
        MaterialApp(
          theme: AppTheme.lightTheme,
          home: const RegisterScreen(),
        ),
      );

      expect(find.text('Daftar Customer'), findsOneWidget);
      expect(find.text('Informasi Akun'), findsOneWidget);
      expect(find.text('Keamanan'), findsOneWidget);
      expect(find.text('Daftar Akun Customer'), findsOneWidget);

      // Ensure button is visible in scrollable view and tap submit with empty fields
      final submitButton = find.text('Daftar Akun Customer');
      await tester.ensureVisible(submitButton);
      await tester.pumpAndSettle();
      await tester.tap(submitButton);
      await tester.pump();

      expect(find.text('Nama lengkap wajib diisi.'), findsOneWidget);
      expect(find.text('Email wajib diisi.'), findsOneWidget);
    });

    testWidgets(
        'RegisterTechnicianScreen renders technician registration form',
        (tester) async {
      tester.view.physicalSize = const Size(800, 1600);
      addTearDown(tester.view.resetPhysicalSize);

      await tester.pumpWidget(
        MaterialApp(
          theme: AppTheme.lightTheme,
          home: const RegisterTechnicianScreen(),
        ),
      );

      expect(find.text('Daftar Mitra Teknisi'), findsWidgets);
      expect(find.text('Informasi Profil Mitra'), findsOneWidget);
      expect(find.text('Keamanan Akun'), findsOneWidget);

      // Ensure submit button is visible and tap with empty fields
      final submitButton = find.byType(FilledButton);
      await tester.ensureVisible(submitButton);
      await tester.pumpAndSettle();
      await tester.tap(submitButton);
      await tester.pump();

      // Scroll back up to verify validation messages
      await tester.scrollUntilVisible(
        find.text('Nama lengkap wajib diisi.'),
        -300,
        scrollable: find.byType(Scrollable).first,
      );

      expect(find.text('Nama lengkap wajib diisi.'), findsOneWidget);
      expect(find.text('Email wajib diisi.'), findsOneWidget);
    });
  });
}

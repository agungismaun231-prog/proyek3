import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:proyek2/config/app_theme.dart';
import 'package:proyek2/models/service_item.dart';
import 'package:proyek2/models/technician.dart';
import 'package:proyek2/screens/order_page.dart';

void main() {
  group('OrderPage Regression & Layout Tests', () {
    testWidgets('Full OrderPage renders with simulated services and technicians',
        (tester) async {
      tester.view.physicalSize = const Size(800, 1600);
      tester.view.devicePixelRatio = 2.0;
      addTearDown(tester.view.resetPhysicalSize);

      final services = [
        ServiceItem(
          id: 1,
          name: 'Cuci AC Standard',
          description: 'Pembersihan unit indoor dan outdoor',
          price: 75000,
          durationMinutes: 45,
        ),
      ];
      final technicians = [
        Technician(
          id: 1,
          name: 'Ahmad Teknisi',
          email: 'ahmad@example.com',
          phone: '08123456789',
          address: 'Jakarta',
          specialization: 'Cuci AC',
          status: 'available',
          averageRating: 4.8,
          ratingCount: 12,
          description: 'Berpengalaman 5 tahun',
          yearsExperience: 5,
          photoUrl: '',
        ),
      ];

      await tester.pumpWidget(
        MaterialApp(
          theme: AppTheme.lightTheme,
          home: Scaffold(
            body: OrderPage(
              servicesLoader: () async => services,
              techniciansLoader: () async => technicians,
            ),
          ),
        ),
      );

      await tester.pump();
      await tester.pump(const Duration(milliseconds: 100));

      expect(find.text('1. Pilih Layanan AC'), findsOneWidget);
      expect(find.text('Cuci AC Standard'), findsOneWidget);
      expect(find.text('2. Pilih Teknisi (Opsional)'), findsOneWidget);
      expect(find.text('Buat Booking'), findsOneWidget);
      expect(find.text('Rp 75.000'), findsWidgets);
    });

    testWidgets('OrderPage handles empty services with retry action without layout crash',
        (tester) async {
      tester.view.physicalSize = const Size(400, 800);
      tester.view.devicePixelRatio = 1.0;
      addTearDown(tester.view.resetPhysicalSize);

      await tester.pumpWidget(
        MaterialApp(
          theme: AppTheme.lightTheme,
          home: Scaffold(
            body: OrderPage(
              servicesLoader: () async => [],
              techniciansLoader: () async => [],
            ),
          ),
        ),
      );

      await tester.pump();
      await tester.pump(const Duration(milliseconds: 100));

      expect(find.text('Layanan Belum Tersedia'), findsOneWidget);
      expect(find.text('Muat Ulang'), findsOneWidget);
      expect(find.text('Buat Booking'), findsOneWidget);
      expect(find.text('Rp 0'), findsOneWidget);
    });

    testWidgets(
        'OrderPage does not crash on narrow mobile screen (360x780)',
        (tester) async {
      tester.view.physicalSize = const Size(360, 780);
      tester.view.devicePixelRatio = 1.0;
      addTearDown(tester.view.resetPhysicalSize);

      final services = [
        ServiceItem(
          id: 1,
          name: 'Perbaikan Kompresor & Ganti Kapasitor AC Split',
          description: 'Pemeriksaan kelistrikan, penggantian kapasitor kompresor.',
          price: 250000,
          durationMinutes: 90,
        ),
      ];
      final technicians = [
        Technician(
          id: 1,
          name: 'Budi Santoso Teknisi Senior',
          email: 'budi@example.com',
          phone: '08123456789',
          address: 'Jakarta Selatan',
          specialization: 'Perbaikan Kompresor & Kelistrikan AC',
          status: 'available',
          averageRating: 4.9,
          ratingCount: 30,
          description: 'Berpengalaman 10 tahun',
          yearsExperience: 10,
          photoUrl: '',
        ),
      ];

      await tester.pumpWidget(
        MaterialApp(
          theme: AppTheme.lightTheme,
          home: Scaffold(
            body: OrderPage(
              servicesLoader: () async => services,
              techniciansLoader: () async => technicians,
            ),
          ),
        ),
      );

      await tester.pump();
      await tester.pump(const Duration(milliseconds: 100));

      expect(find.text('1. Pilih Layanan AC'), findsOneWidget);
      expect(find.text('2. Pilih Teknisi (Opsional)'), findsOneWidget);
      expect(find.text('Buat Booking'), findsOneWidget);
      expect(find.text('Rp 250.000'), findsWidgets);
    });
  });
}

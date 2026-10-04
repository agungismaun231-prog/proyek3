import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:proyek2/config/app_theme.dart';
import 'package:proyek2/models/booking.dart';
import 'package:proyek2/models/service_item.dart';
import 'package:proyek2/widgets/app_empty_state.dart';
import 'package:proyek2/widgets/app_section_header.dart';
import 'package:proyek2/widgets/app_stat_card.dart';
import 'package:proyek2/widgets/app_status_badge.dart';
import 'package:proyek2/widgets/booking_card.dart';

void main() {
  group('AppStatusBadge Tests', () {
    testWidgets('renders pending booking badge correctly', (tester) async {
      await tester.pumpWidget(
        const MaterialApp(
          home: Scaffold(
            body: AppStatusBadge(status: 'pending'),
          ),
        ),
      );

      expect(find.text('Menunggu'), findsOneWidget);
    });

    testWidgets('renders completed booking badge correctly', (tester) async {
      await tester.pumpWidget(
        const MaterialApp(
          home: Scaffold(
            body: AppStatusBadge(status: 'completed'),
          ),
        ),
      );

      expect(find.text('Selesai'), findsOneWidget);
    });

    testWidgets('renders invoice status badge correctly', (tester) async {
      await tester.pumpWidget(
        const MaterialApp(
          home: Scaffold(
            body: AppStatusBadge(status: 'paid', isInvoice: true),
          ),
        ),
      );

      expect(find.text('Lunas'), findsOneWidget);
    });
  });

  group('AppStatCard Tests', () {
    testWidgets('renders value, label, and responds to tap', (tester) async {
      var tapped = false;

      await tester.pumpWidget(
        MaterialApp(
          theme: AppTheme.lightTheme,
          home: Scaffold(
            body: AppStatCard(
              label: 'Total Booking',
              value: '12',
              icon: Icons.receipt_long,
              onTap: () => tapped = true,
            ),
          ),
        ),
      );

      expect(find.text('12'), findsOneWidget);
      expect(find.text('Total Booking'), findsOneWidget);

      await tester.tap(find.byType(AppStatCard));
      await tester.pump();

      expect(tapped, isTrue);
    });
  });

  group('AppEmptyState Tests', () {
    testWidgets('renders title, description, and triggers CTA action',
        (tester) async {
      var actionTriggered = false;

      await tester.pumpWidget(
        MaterialApp(
          theme: AppTheme.lightTheme,
          home: Scaffold(
            body: AppEmptyState(
              icon: Icons.inbox_outlined,
              title: 'Data Kosong',
              description: 'Tidak ada data untuk ditampilkan.',
              actionLabel: 'Tambah Data',
              onAction: () => actionTriggered = true,
            ),
          ),
        ),
      );

      expect(find.text('Data Kosong'), findsOneWidget);
      expect(find.text('Tidak ada data untuk ditampilkan.'), findsOneWidget);
      expect(find.text('Tambah Data'), findsOneWidget);

      await tester.tap(find.text('Tambah Data'));
      await tester.pump();

      expect(actionTriggered, isTrue);
    });
  });

  group('AppSectionHeader Tests', () {
    testWidgets('renders section title, count pill, and action label',
        (tester) async {
      var actionTriggered = false;

      await tester.pumpWidget(
        MaterialApp(
          theme: AppTheme.lightTheme,
          home: Scaffold(
            body: AppSectionHeader(
              title: 'Booking Terbaru',
              count: 5,
              actionLabel: 'Lihat Semua',
              onAction: () => actionTriggered = true,
            ),
          ),
        ),
      );

      expect(find.text('Booking Terbaru'), findsOneWidget);
      expect(find.text('5'), findsOneWidget);
      expect(find.text('Lihat Semua'), findsOneWidget);

      await tester.tap(find.text('Lihat Semua'));
      await tester.pump();

      expect(actionTriggered, isTrue);
    });
  });

  group('BookingCard Tests', () {
    testWidgets('renders booking information and status badge', (tester) async {
      final booking = Booking(
        id: 42,
        status: 'confirmed',
        scheduledDate: DateTime(2026, 9, 15, 10, 0),
        serviceLocation: 'Jl. Sudirman No. 45',
        notes: 'AC Sharp 1 PK',
        completionNotes: '',
        cancellationReason: '',
        totalPrice: 150000,
        service: ServiceItem(
          id: 1,
          name: 'Cuci AC Standard',
          description: 'Pembersihan indoor dan outdoor',
          price: 150000,
          durationMinutes: 60,
        ),
        technician: null,
        customer: {'name': 'Budi Santoso'},
        invoice: null,
        rating: null,
      );

      await tester.pumpWidget(
        MaterialApp(
          theme: AppTheme.lightTheme,
          home: Scaffold(
            body: BookingCard(booking: booking),
          ),
        ),
      );

      expect(find.text('Cuci AC Standard'), findsOneWidget);
      expect(find.text('Dikonfirmasi'), findsOneWidget);
      expect(find.text('Menunggu teknisi'), findsOneWidget);
      expect(find.text('Jl. Sudirman No. 45'), findsOneWidget);
      expect(find.text('Rp 150.000'), findsOneWidget);
    });
  });
}

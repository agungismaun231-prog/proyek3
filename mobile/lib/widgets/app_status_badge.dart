import 'package:flutter/material.dart';
import '../config/app_theme.dart';
import '../utils/formatters.dart';

class AppStatusBadge extends StatelessWidget {
  final String status;
  final bool isInvoice;

  const AppStatusBadge({
    super.key,
    required this.status,
    this.isInvoice = false,
  });

  @override
  Widget build(BuildContext context) {
    final (label, bgColor, textColor) = _badgeStyle(status.toLowerCase());

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(
        color: bgColor,
        borderRadius: BorderRadius.circular(AppRadius.pill),
      ),
      child: Text(
        label,
        style: TextStyle(
          color: textColor,
          fontSize: 12,
          fontWeight: FontWeight.w600,
          letterSpacing: 0.1,
        ),
      ),
    );
  }

  (String, Color, Color) _badgeStyle(String s) {
    if (isInvoice) {
      if (s == 'paid') {
        return ('Lunas', AppColors.successLight, const Color(0xFF047857));
      }
      return ('Belum Bayar', AppColors.warningLight, const Color(0xFFB45309));
    }

    final displayLabel = bookingStatusLabel(s);
    switch (s) {
      case 'pending':
        return (displayLabel, AppColors.warningLight, const Color(0xFFB45309));
      case 'confirmed':
        return (displayLabel, AppColors.infoLight, const Color(0xFF0369A1));
      case 'en_route':
        return (displayLabel, const Color(0xFFEEF2FF), const Color(0xFF4338CA));
      case 'in_progress':
        return (displayLabel, const Color(0xFFF5F3FF), const Color(0xFF6D28D9));
      case 'completed':
        return (displayLabel, AppColors.successLight, const Color(0xFF047857));
      case 'cancelled':
        return (displayLabel, AppColors.dangerLight, const Color(0xFFB91C1C));
      default:
        return (
          displayLabel,
          AppColors.surfaceElevated,
          AppColors.textSecondary
        );
    }
  }
}

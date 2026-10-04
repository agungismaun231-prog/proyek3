import 'package:flutter/material.dart';
import '../config/app_theme.dart';
import '../models/booking.dart';
import '../utils/formatters.dart';
import 'app_status_badge.dart';

class BookingCard extends StatelessWidget {
  final Booking booking;
  final VoidCallback? onTap;
  final Widget? trailingAction;
  final List<Widget>? actionButtons;

  const BookingCard({
    super.key,
    required this.booking,
    this.onTap,
    this.trailingAction,
    this.actionButtons,
  });

  @override
  Widget build(BuildContext context) {
    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(AppRadius.md),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Header: Service Name & Status Badge
              Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Expanded(
                    child: Text(
                      booking.service?.name ?? 'Layanan Servis AC',
                      style: const TextStyle(
                        fontSize: 15,
                        fontWeight: FontWeight.w700,
                        color: AppColors.textPrimary,
                        letterSpacing: -0.2,
                      ),
                    ),
                  ),
                  const SizedBox(width: 8),
                  AppStatusBadge(status: booking.status),
                ],
              ),
              const SizedBox(height: 12),

              // Metadata: Schedule & Technician & Location
              _metadataRow(
                Icons.calendar_today_outlined,
                formatDate(booking.scheduledDate),
              ),
              const SizedBox(height: 6),
              _metadataRow(
                Icons.person_outline,
                booking.technician?.name ?? 'Menunggu teknisi',
                color: booking.technician == null
                    ? AppColors.textMuted
                    : AppColors.textSecondary,
              ),
              if (booking.serviceLocation.isNotEmpty) ...[
                const SizedBox(height: 6),
                _metadataRow(
                  Icons.location_on_outlined,
                  booking.serviceLocation,
                  maxLines: 2,
                ),
              ],

              const SizedBox(height: 14),
              const Divider(height: 1),
              const SizedBox(height: 12),

              // Footer: Price & Context Actions
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                crossAxisAlignment: CrossAxisAlignment.center,
                children: [
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'Total Biaya',
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.w500,
                          color: AppColors.textMuted,
                        ),
                      ),
                      const SizedBox(height: 1),
                      Text(
                        formatRupiah(booking.totalPrice),
                        style: const TextStyle(
                          fontSize: 15,
                          fontWeight: FontWeight.w700,
                          color: AppColors.primary,
                        ),
                      ),
                    ],
                  ),
                  if (trailingAction != null) trailingAction!,
                ],
              ),

              if (actionButtons != null && actionButtons!.isNotEmpty) ...[
                const SizedBox(height: 12),
                Row(
                  mainAxisAlignment: MainAxisAlignment.end,
                  children: actionButtons!,
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }

  Widget _metadataRow(
    IconData icon,
    String text, {
    Color color = AppColors.textSecondary,
    int maxLines = 1,
  }) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Icon(icon, size: 15, color: AppColors.textMuted),
        const SizedBox(width: 8),
        Expanded(
          child: Text(
            text,
            maxLines: maxLines,
            overflow: TextOverflow.ellipsis,
            style: TextStyle(
              fontSize: 13,
              color: color,
              height: 1.25,
            ),
          ),
        ),
      ],
    );
  }
}

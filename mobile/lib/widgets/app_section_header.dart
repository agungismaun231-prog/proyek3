import 'package:flutter/material.dart';
import '../config/app_theme.dart';

class AppSectionHeader extends StatelessWidget {
  final String title;
  final int? count;
  final String? actionLabel;
  final VoidCallback? onAction;

  const AppSectionHeader({
    super.key,
    required this.title,
    this.count,
    this.actionLabel,
    this.onAction,
  });

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Expanded(
            child: Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                Flexible(
                  child: Text(
                    title,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.w700,
                      color: AppColors.textPrimary,
                      letterSpacing: -0.3,
                    ),
                  ),
                ),
                if (count != null) ...[
                  const SizedBox(width: 8),
                  Container(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                    decoration: BoxDecoration(
                      color: AppColors.surfaceElevated,
                      borderRadius: BorderRadius.circular(AppRadius.pill),
                      border: Border.all(color: AppColors.border, width: 1),
                    ),
                    child: Text(
                      count.toString(),
                      style: const TextStyle(
                        fontSize: 11,
                        fontWeight: FontWeight.w600,
                        color: AppColors.textSecondary,
                      ),
                    ),
                  ),
                ],
              ],
            ),
          ),
          if (actionLabel != null && onAction != null) ...[
            const SizedBox(width: 8),
            InkWell(
              onTap: onAction,
              borderRadius: BorderRadius.circular(AppRadius.sm),
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 4),
                child: Text(
                  actionLabel!,
                  style: const TextStyle(
                    fontSize: 13,
                    fontWeight: FontWeight.w600,
                    color: AppColors.primary,
                  ),
                ),
              ),
            ),
          ],
        ],
      ),
    );

  }
}

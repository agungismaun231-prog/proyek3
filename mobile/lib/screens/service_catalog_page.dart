import 'package:flutter/material.dart';

import '../config/app_theme.dart';
import '../models/service_item.dart';
import '../services/api_service.dart';
import '../utils/formatters.dart';
import '../widgets/app_empty_state.dart';
import 'order_page.dart';

/// Katalog layanan bergaya marketplace: pencarian, filter kategori
/// (Semua / AC / Gadget / ...), dan grid kartu layanan.
/// Menekan kartu akan membuka form pemesanan dengan layanan terpilih.
class ServiceCatalogPage extends StatefulWidget {
  final String initialCategory;
  final VoidCallback? onCreated;
  final Future<List<ServiceItem>> Function()? servicesLoader;

  const ServiceCatalogPage({
    super.key,
    this.initialCategory = 'Semua',
    this.onCreated,
    this.servicesLoader,
  });

  @override
  State<ServiceCatalogPage> createState() => _ServiceCatalogPageState();
}

class _ServiceCatalogPageState extends State<ServiceCatalogPage> {
  final _searchController = TextEditingController();
  bool _isLoading = true;
  String? _error;
  List<ServiceItem> _services = [];
  late String _category;
  String _query = '';

  @override
  void initState() {
    super.initState();
    _category = widget.initialCategory;
    _load();
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _load() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });
    try {
      final result = widget.servicesLoader != null
          ? await widget.servicesLoader!()
          : await ApiService.getServices();
      if (!mounted) return;
      setState(() {
        _services = result;
        _isLoading = false;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _error = e.toString();
        _isLoading = false;
      });
    }
  }

  List<String> get _categories {
    final names = <String>[];
    for (final s in _services) {
      if (s.category.isNotEmpty && !names.contains(s.category)) {
        names.add(s.category);
      }
    }
    return ['Semua', ...names];
  }

  List<ServiceItem> get _filtered {
    final q = _query.trim().toLowerCase();
    return _services.where((s) {
      final matchCategory = _category == 'Semua' || s.category == _category;
      final matchQuery = q.isEmpty ||
          s.name.toLowerCase().contains(q) ||
          s.description.toLowerCase().contains(q);
      return matchCategory && matchQuery;
    }).toList();
  }

  IconData _iconFor(ServiceItem s) {
    final name = s.name.toLowerCase();
    if (s.category == 'Gadget') {
      if (name.contains('laptop')) return Icons.laptop_mac_rounded;
      if (name.contains('baterai')) return Icons.battery_charging_full_rounded;
      return Icons.smartphone_rounded;
    }
    if (s.category == 'AC') return Icons.ac_unit_rounded;
    return Icons.build_rounded;
  }

  IconData _categoryIcon(String category) {
    return switch (category) {
      'Semua' => Icons.grid_view_rounded,
      'AC' => Icons.ac_unit_rounded,
      'Gadget' => Icons.smartphone_rounded,
      _ => Icons.build_rounded,
    };
  }

  Color _accent(ServiceItem s) =>
      s.category == 'Gadget' ? AppColors.success : AppColors.primary;

  Color _accentLight(ServiceItem s) =>
      s.category == 'Gadget' ? AppColors.successLight : AppColors.primaryLight;

  Future<void> _order(ServiceItem service) async {
    final created = await Navigator.of(context).push<bool>(
      MaterialPageRoute(
        builder: (routeContext) => Scaffold(
          backgroundColor: AppColors.background,
          appBar: AppBar(title: const Text('Pesan Layanan')),
          body: OrderPage(
            initialServiceId: service.id,
            onCreated: () => Navigator.of(routeContext).pop(true),
          ),
        ),
      ),
    );
    if (created == true && mounted) {
      Navigator.of(context).pop();
      widget.onCreated?.call();
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(title: const Text('Katalog Layanan')),
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 8),
            child: TextField(
              controller: _searchController,
              onChanged: (v) => setState(() => _query = v),
              decoration: InputDecoration(
                hintText: 'Cari layanan AC atau gadget',
                prefixIcon: const Icon(Icons.search_rounded),
                suffixIcon: _query.isEmpty
                    ? null
                    : IconButton(
                        icon: const Icon(Icons.close_rounded),
                        onPressed: () {
                          _searchController.clear();
                          setState(() => _query = '');
                        },
                      ),
                filled: true,
                fillColor: AppColors.surface,
                contentPadding: const EdgeInsets.symmetric(vertical: 0),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(AppRadius.pill),
                  borderSide: const BorderSide(color: AppColors.border),
                ),
                enabledBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(AppRadius.pill),
                  borderSide: const BorderSide(color: AppColors.border),
                ),
              ),
            ),
          ),
          if (!_isLoading && _error == null) _buildCategoryTiles(),
          Expanded(child: _buildBody()),
        ],
      ),
    );
  }

  Widget _buildCategoryTiles() {
    return SizedBox(
      height: 76,
      child: ListView.separated(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
        itemCount: _categories.length,
        separatorBuilder: (_, __) => const SizedBox(width: 8),
        itemBuilder: (context, index) {
          final name = _categories[index];
          final selected = name == _category;
          return InkWell(
            borderRadius: BorderRadius.circular(AppRadius.md),
            onTap: () => setState(() => _category = name),
            child: Container(
              width: 84,
              decoration: BoxDecoration(
                color: selected ? AppColors.primaryLight : AppColors.surface,
                borderRadius: BorderRadius.circular(AppRadius.md),
                border: Border.all(
                  color: selected ? AppColors.primary : AppColors.border,
                  width: selected ? 1.5 : 1,
                ),
              ),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(
                    _categoryIcon(name),
                    size: 22,
                    color: selected
                        ? AppColors.primary
                        : AppColors.textSecondary,
                  ),
                  const SizedBox(height: 2),
                  Text(
                    name,
                    style: TextStyle(
                      fontSize: 12,
                      fontWeight: FontWeight.w600,
                      color: selected
                          ? AppColors.primary
                          : AppColors.textSecondary,
                    ),
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildBody() {
    if (_isLoading) {
      return const Center(child: CircularProgressIndicator());
    }
    if (_error != null) {
      return AppEmptyState(
        icon: Icons.wifi_off_rounded,
        title: 'Gagal Memuat Layanan',
        description: 'Periksa koneksi internet Anda lalu coba lagi.',
        actionLabel: 'Coba Lagi',
        onAction: _load,
      );
    }
    final items = _filtered;
    if (items.isEmpty) {
      return const AppEmptyState(
        icon: Icons.search_off_rounded,
        title: 'Layanan Tidak Ditemukan',
        description: 'Coba kata kunci lain atau pilih kategori yang berbeda.',
      );
    }
    return RefreshIndicator(
      onRefresh: _load,
      color: AppColors.primary,
      child: GridView.builder(
        padding: const EdgeInsets.fromLTRB(16, 8, 16, 20),
        gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
          crossAxisCount: 2,
          mainAxisSpacing: 10,
          crossAxisSpacing: 10,
          mainAxisExtent: 200,
        ),
        itemCount: items.length,
        itemBuilder: (context, index) => _serviceCard(items[index]),
      ),
    );
  }

  Widget _serviceCard(ServiceItem service) {
    return InkWell(
      borderRadius: BorderRadius.circular(AppRadius.md),
      onTap: () => _order(service),
      child: Container(
        clipBehavior: Clip.antiAlias,
        decoration: BoxDecoration(
          color: AppColors.surface,
          borderRadius: BorderRadius.circular(AppRadius.md),
          border: Border.all(color: AppColors.border),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              height: 84,
              width: double.infinity,
              color: _accentLight(service),
              child: Icon(_iconFor(service), size: 36, color: _accent(service)),
            ),
            Padding(
              padding: const EdgeInsets.all(10),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    service.name,
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(
                      fontSize: 13,
                      fontWeight: FontWeight.w600,
                      color: AppColors.textPrimary,
                      height: 1.3,
                    ),
                  ),
                  const SizedBox(height: 6),
                  Text(
                    formatRupiah(service.price),
                    style: const TextStyle(
                      fontSize: 14,
                      fontWeight: FontWeight.w700,
                      color: AppColors.primary,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Row(
                    children: [
                      const Icon(Icons.schedule_rounded,
                          size: 12, color: AppColors.textMuted),
                      const SizedBox(width: 3),
                      Expanded(
                        child: Text(
                          '${service.durationMinutes} menit',
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                          style: const TextStyle(
                            fontSize: 11,
                            color: AppColors.textMuted,
                          ),
                        ),
                      ),
                      if (service.category.isNotEmpty)
                        Text(
                          service.category,
                          style: TextStyle(
                            fontSize: 11,
                            fontWeight: FontWeight.w600,
                            color: _accent(service),
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
    );
  }
}

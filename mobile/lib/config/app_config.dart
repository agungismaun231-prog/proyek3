class AppConfig {
  static const String baseUrl = String.fromEnvironment(
    'API_BASE_URL',
    defaultValue: 'http://10.0.2.2:8000/api',
  );

  /// Gambar kode QRIS yang disajikan server (public/images/qris.png).
  static String get qrisImageUrl =>
      '${baseUrl.replaceFirst(RegExp(r'/api/?$'), '')}/images/qris.png';
}

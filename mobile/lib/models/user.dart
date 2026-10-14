class User {
  final int id;
  final String name;
  final String email;
  final String role;
  final String phone;
  final String address;
  final String city;
  final String postalCode;
  final String specialization;
  final String status;
  final String description;       // <-- Tambahan baru
  final int yearsExperience;
  final String photoUrl;// <-- Tambahan baru

  User({
    required this.id,
    required this.name,
    required this.email,
    required this.role,
    this.phone = '',
    this.address = '',
    this.city = '',
    this.postalCode = '',
    this.specialization = '',
    this.status = '',
    this.description = '',        // <-- Tambahan baru
    this.yearsExperience = 0,     // <-- Tambahan baru
    this.photoUrl = '',
  });

  factory User.fromJson(Map<String, dynamic> json) {
    // Menangkap data dari relasi 'technician' jika dikirim oleh backend Laravel
    final technician = json['technician'] is Map<String, dynamic> ? json['technician'] : {};

    return User(
      id: json['id'] as int,
      name: json['name']?.toString() ?? '',
      email: json['email']?.toString() ?? '',
      role: json['role']?.toString() ?? 'customer',
      phone: technician['phone']?.toString() ?? json['phone']?.toString() ?? '',
      address: technician['address']?.toString() ?? json['address']?.toString() ?? '',
      city: json['city']?.toString() ?? '',
      postalCode: json['postal_code']?.toString() ?? '',
      specialization: technician['specialization']?.toString() ?? json['specialization']?.toString() ?? '',
      status: technician['status']?.toString() ?? json['status']?.toString() ?? '',

      // Membaca deskripsi dan pengalaman dari data teknisi atau root json
      description: technician['description']?.toString() ?? json['description']?.toString() ?? '',
      yearsExperience: int.tryParse((technician['years_experience'] ?? json['years_experience'] ?? 0).toString()) ?? 0,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'role': role,
      'phone': phone,
      'address': address,
      'city': city,
      'postal_code': postalCode,
      'specialization': specialization,
      'status': status,
      'description': description,
      'years_experience': yearsExperience,
    };
  }

  bool get isCustomer => role == 'customer';
  bool get isTechnician => role == 'technician';
  bool get isAdmin => role == 'admin';
}

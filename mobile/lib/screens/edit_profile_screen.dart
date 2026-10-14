import 'dart:io';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import '../config/app_theme.dart';
import '../models/user.dart';
import '../services/api_service.dart';

class EditProfileScreen extends StatefulWidget {
  final User user;

  const EditProfileScreen({super.key, required this.user});

  @override
  State<EditProfileScreen> createState() => _EditProfileScreenState();
}

class _EditProfileScreenState extends State<EditProfileScreen> {
  late TextEditingController _nameController;
  late TextEditingController _emailController;
  late TextEditingController _phoneController;
  late TextEditingController _addressController;
  late TextEditingController _specializationController;
  late TextEditingController _descriptionController;
  late TextEditingController _experienceController;

  File? _selectedImage;
  bool _isLoading = false;

  @override
  void initState() {
    super.initState();
    _nameController = TextEditingController(text: widget.user.name);
    _emailController = TextEditingController(text: widget.user.email);
    _phoneController = TextEditingController(text: widget.user.phone);
    _addressController = TextEditingController(text: widget.user.address);
    _specializationController = TextEditingController(text: widget.user.specialization);
    _descriptionController = TextEditingController(text: widget.user.description);
    _experienceController = TextEditingController(text: widget.user.yearsExperience.toString());
  }

  @override
  void dispose() {
    _nameController.dispose();
    _emailController.dispose();
    _phoneController.dispose();
    _addressController.dispose();
    _specializationController.dispose();
    _descriptionController.dispose();
    _experienceController.dispose();
    super.dispose();
  }

  Future<void> _pickImage() async {
    final pickedFile = await ImagePicker().pickImage(source: ImageSource.gallery);
    if (pickedFile != null) {
      setState(() {
        _selectedImage = File(pickedFile.path);
      });
    }
  }

  Future<void> _saveProfile() async {
    setState(() => _isLoading = true);
    try {
      final updatedUser = await ApiService.updateProfile(
        name: _nameController.text,
        email: _emailController.text,
        phone: _phoneController.text,
        address: _addressController.text,
        specialization: _specializationController.text,
        description: _descriptionController.text,
        yearsExperience: int.tryParse(_experienceController.text) ?? 0,
      );

      if (!mounted) return;
      Navigator.pop(context, updatedUser);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Profil berhasil diperbarui'), backgroundColor: AppColors.success),
      );
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(e.toString()), backgroundColor: AppColors.danger),
      );
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('Edit Profil Teknisi'),
      ),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          // Bagian Foto Profil dengan tombol kamera
          Center(
            child: Stack(
              children: [
                CircleAvatar(
                  radius: 50,
                  backgroundColor: AppColors.primaryLight,
                  backgroundImage: _selectedImage != null
                      ? FileImage(_selectedImage!) as ImageProvider
                      : (widget.user.photoUrl.isNotEmpty
                      ? NetworkImage(widget.user.photoUrl.startsWith('http')
                      ? widget.user.photoUrl
                      : 'http://10.0.2.2:8000/storage/${widget.user.photoUrl}') as ImageProvider
                      : null),
                  child: (_selectedImage == null && widget.user.photoUrl.isEmpty)
                      ? Text(
                    widget.user.name.isNotEmpty ? widget.user.name[0].toUpperCase() : 'T',
                    style: const TextStyle(fontSize: 32, fontWeight: FontWeight.bold, color: AppColors.primary),
                  )
                      : null,
                ),
                Positioned(
                  bottom: 0,
                  right: 0,
                  child: CircleAvatar(
                    backgroundColor: AppColors.primary,
                    radius: 18,
                    child: IconButton(
                      icon: const Icon(Icons.camera_alt, size: 16, color: Colors.white),
                      onPressed: _pickImage,
                    ),
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 24),

          // Form Input Data
          TextField(
            controller: _nameController,
            decoration: const InputDecoration(labelText: 'Nama Lengkap', border: OutlineInputBorder()),
          ),
          const SizedBox(height: 16),
          TextField(
            controller: _emailController,
            decoration: const InputDecoration(labelText: 'Email', border: OutlineInputBorder()),
          ),
          const SizedBox(height: 16),
          TextField(
            controller: _phoneController,
            decoration: const InputDecoration(labelText: 'Nomor Telepon', border: OutlineInputBorder()),
          ),
          const SizedBox(height: 16),
          TextField(
            controller: _addressController,
            decoration: const InputDecoration(labelText: 'Alamat Domisili', border: OutlineInputBorder()),
          ),
          const SizedBox(height: 16),
          TextField(
            controller: _specializationController,
            decoration: const InputDecoration(labelText: 'Spesialisasi', border: OutlineInputBorder()),
          ),
          const SizedBox(height: 16),
          TextField(
            controller: _descriptionController,
            maxLines: 3,
            decoration: const InputDecoration(labelText: 'Deskripsi', border: OutlineInputBorder()),
          ),
          const SizedBox(height: 16),
          TextField(
            controller: _experienceController,
            keyboardType: TextInputType.number,
            decoration: const InputDecoration(labelText: 'Pengalaman (Tahun)', border: OutlineInputBorder()),
          ),
          const SizedBox(height: 24),

          // Tombol Batal & Simpan
          Row(
            children: [
              Expanded(
                child: OutlinedButton(
                  onPressed: () => Navigator.pop(context),
                  style: OutlinedButton.styleFrom(minimumSize: const Size(0, 48)),
                  child: const Text('Batal'),
                ),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: FilledButton(
                  onPressed: _isLoading ? null : _saveProfile,
                  style: FilledButton.styleFrom(minimumSize: const Size(0, 48)),
                  child: _isLoading
                      ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                      : const Text('Simpan Perubahan'),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}

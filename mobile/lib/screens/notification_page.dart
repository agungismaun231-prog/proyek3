import 'package:flutter/material.dart';
import '../models/app_notification.dart';
import '../services/api_service.dart';

class NotificationPage extends StatefulWidget {
  const NotificationPage({super.key});

  @override
  State<NotificationPage> createState() =>
      _NotificationPageState();
}

class _NotificationPageState
    extends State<NotificationPage> {

  List<AppNotification> notifications = [];

  @override
  void initState() {
    super.initState();
    loadNotifications();
  }

  Future<void> loadNotifications() async {
    notifications =
    await ApiService.getNotifications();

    setState(() {});
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Notifikasi'),
      ),
      body: ListView.builder(
        itemCount: notifications.length,
        itemBuilder: (context, index) {

          final notif =
          notifications[index];

          return ListTile(
            leading:
            const Icon(Icons.notifications),
            title: Text(notif.title),
            subtitle: Text(notif.message),
          );
        },
      ),
    );
  }
}

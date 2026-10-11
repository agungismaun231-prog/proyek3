<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\Slider;
use App\Models\Technician;

/**
 * DashboardController
 * 
 * Controller untuk menampilkan admin dashboard dengan overview data.
 * Dashboard menampilkan:
 * - Statistics cards (total customers, bookings, revenue, etc)
 * - Recent bookings
 * - Services list
 * - Technician statistics
 * - Slider carousel dengan images
 * 
 * Route: GET /dashboard
 */
class DashboardController extends Controller
{
    /**
     * Display admin dashboard
     * 
     * Mengumpulkan semua data yang diperlukan untuk dashboard dan
     * menampilkannya ke view dengan formatting data.
     * 
     * Data yang dikumpulkan:
     * - totalCustomers: Jumlah total customer
     * - totalBookings: Jumlah total booking
     * - completedBookings: Jumlah booking dengan status completed
     * - pendingBookings: Jumlah booking dengan status pending
     * - totalRevenue: Total revenue dari invoice yang sudah dibayar
     * - availableTechnicians: Jumlah technician dengan status available
     * - recentBookings: 5 booking terbaru dengan relationship data
     * - servicesData: Semua service yang tersedia
     * - technicianStats: Semua technician dengan count bookings
     * - sliders: Semua slider yang aktif untuk carousel
     * 
     * @return \Illuminate\View\View Dashboard view dengan data
     */
    public function index()
    {
        // ===== STATISTICS =====
        // Query data untuk statistics cards

        $totalCustomers = Customer::count();
        $totalBookings = Booking::count();
        $completedBookings = Booking::where('status', 'completed')->count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $cancelledBookings = Booking::where('status', 'cancelled')->count();
        $inProgressBookings = Booking::where('status', 'in_progress')->count();
        $totalRevenue = Invoice::where('status', 'paid')->sum('total');
        $monthlyRevenue = Invoice::where('status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');
        $availableTechnicians = Technician::where('status', 'available')->count();
        $totalTechnicians = Technician::count();
        $totalServices = Service::count();
        $todayBookings = Booking::whereDate('scheduled_date', today())->count();

        // ===== RECENT DATA =====
        $recentBookings = Booking::latest()
            ->take(8)
            ->with('customer', 'service', 'technician')
            ->get();

        // ===== CATALOG DATA =====
        $servicesData = Service::all();
        $technicianStats = Technician::withCount('bookings')->get();
        
        // ===== SLIDER DATA =====
        $sliders = Slider::getActive();

        // ===== RETURN VIEW =====
        return view('dashboard', [
            'totalCustomers' => $totalCustomers,
            'totalBookings' => $totalBookings,
            'completedBookings' => $completedBookings,
            'pendingBookings' => $pendingBookings,
            'cancelledBookings' => $cancelledBookings,
            'inProgressBookings' => $inProgressBookings,
            'totalRevenue' => $totalRevenue,
            'monthlyRevenue' => $monthlyRevenue,
            'availableTechnicians' => $availableTechnicians,
            'totalTechnicians' => $totalTechnicians,
            'totalServices' => $totalServices,
            'todayBookings' => $todayBookings,
            'recentBookings' => $recentBookings,
            'servicesData' => $servicesData,
            'technicianStats' => $technicianStats,
            'sliders' => $sliders,
        ]);
    }
}

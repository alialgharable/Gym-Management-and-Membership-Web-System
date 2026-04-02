<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use App\Models\Member;
use App\Models\Booking;
use App\Models\GymClass;
use App\Models\Trainer;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function dashboard()
    {
        $stats = [
            'total_members' => Member::count(),
            'total_bookings' => Booking::count(),
            'total_classes' => GymClass::count(),
            'total_trainers' => Trainer::count(),
            'active_subscriptions' => \App\Models\Subscription::where('status', 'active')->count(),
        ];

        $recentMembers = Member::with('user')->latest()->take(5)->get();
        $recentBookings = Booking::with(['member.user', 'gymClass'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentMembers', 'recentBookings'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admins = Admin::with('user')->latest()->get();

        return view('admins.index', compact('admins'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::whereDoesntHave('admin')->get();

        return view('admins.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string|max:255',
        ]);

        Admin::create($validated);

        return redirect()->route('admins.index')
            ->with('success', 'Admin created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Admin $admin)
    {
        $admin->load('user');

        return view('admins.show', compact('admin'));
    }

    /**
     * Show the form for editing the resource.
     */
    public function edit(Admin $admin)
    {
        return view('admins.edit', compact('admin'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Admin $admin)
    {
        $validated = $request->validate([
            'role' => 'sometimes|string|max:255',
        ]);

        $admin->update($validated);

        return redirect()->route('admins.show', $admin)
            ->with('success', 'Admin updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        $admin->delete();

        return redirect()->route('admins.index')
            ->with('success', 'Admin deleted successfully!');
    }
}

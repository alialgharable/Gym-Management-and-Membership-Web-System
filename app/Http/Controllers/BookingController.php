<?php

namespace App\Http\Controllers;

use App\Models\GymClass;
use App\Models\Member;
use Illuminate\Http\Request;
use App\Models\Booking;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookings = Booking::with('gymclass')->get();

        return view('bookings.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $members = Member::all();
        $classes = GymClass::all();

        return view('bookings.create', compact('members', 'classes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'class_id' => 'required|exists:classes,id',
        ]);

        Booking::create([
            'member_id' => $request->member_id,
            'class_id' => $request->class_id,
            'status' => 'confirmed',
        ]);

        return redirect()->route('bookings.index')
            ->with('success', 'Booking created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        return view('bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking)
    {
        $members = Member::all();
        $classes = GymClass::all();
        return view('bookings.edit', compact('booking', 'members', 'classes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'class_id' => 'required|exists:classes,id',
            'status' => 'sometimes|string',
        ]);

        $booking->update($request->only(['member_id', 'class_id', 'status']));

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Booking updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        $booking->delete();
        return redirect()->route('bookings.index')
            ->with('success', 'Booking deleted successfully!');
    }
}

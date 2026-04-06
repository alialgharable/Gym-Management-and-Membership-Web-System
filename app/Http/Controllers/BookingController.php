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
        $user = auth()->user();

        if ($user && $user->isMember()) {
            $member = Member::where('user_id', $user->id)->first();
            $bookings = Booking::with(['gymClass.trainer.user', 'member.user'])
                ->where('member_id', $member->id)
                ->get();
        } elseif ($user && $user->isTrainer()) {
            $trainer = $user->trainer;
            $bookings = Booking::with(['gymClass.trainer.user', 'member.user'])
                ->whereHas('gymClass', function ($q) use ($trainer) {
                    $q->where('trainer_id', $trainer->id);
                })
                ->get();
        } else {
            $bookings = Booking::with(['gymClass.trainer.user', 'member.user'])->get();
        }

        return view('bookings.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();

        $isMember = $user && $user->isMember();
        $isTrainer = $user && $user->isTrainer();
        $isAdmin = $user && $user->isAdmin();
        $isGuest = !auth()->check();

        if ( $isGuest) {
            return redirect ('login');
        }
        else if (auth()->check() && !$isMember && !$isTrainer &&!$isAdmin ){
            return redirect ('subscriptions/create');
        }

        $classes = GymClass::with('trainer.user')->get();
        $members = $isMember ? Member::where('user_id', $user->id)->get() : Member::all();

        return view('bookings.create', compact('members', 'classes', 'isMember'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user && $user->isMember()) {
            $member = Member::where('user_id', $user->id)->firstOrFail();

            $request->validate([
                'class_id' => 'required|exists:classes,id',
            ]);

            Booking::create([
                'member_id' => $member->id,
                'class_id' => $request->class_id,
                'status' => 'confirmed',
            ]);

            return redirect()->route('member.dashboard')
                ->with('success', 'Booking created successfully!');
        }

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
     * Ensure current user can manipulate the booking.
     */
    private function authorizeBooking(Booking $booking)
    {
        $user = auth()->user();

        if (!$user) {
            abort(403);
        }

        if ($user->isAdmin()) {
            return;
        }

        if ($user->isMember()) {
            if ($booking->member_id !== $user->member->id) {
                abort(403);
            }
            return;
        }

        if ($user->isTrainer()) {
            if ($booking->gymClass->trainer_id !== $user->trainer->id) {
                abort(403);
            }
            return;
        }

        abort(403);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking)
    {
        $this->authorizeBooking($booking);

        $members = Member::all();
        $classes = GymClass::all();
        return view('bookings.edit', compact('booking', 'members', 'classes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        $this->authorizeBooking($booking);

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
        $this->authorizeBooking($booking);

        $booking->delete();
        return redirect()->route('bookings.index')
            ->with('success', 'Booking deleted successfully!');
    }
}

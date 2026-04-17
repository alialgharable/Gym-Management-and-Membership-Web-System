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
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Booking::with(['gymClass.trainer.user', 'member.user']);

        if ($user && $user->isMember()) {
            $member = Member::where('user_id', $user->id)->first();
            $query->where('member_id', $member->id);
        } elseif ($user && $user->isTrainer()) {
            $trainer = $user->trainer;
            $query->whereHas('gymClass', function ($q) use ($trainer) {
                $q->where('trainer_id', $trainer->id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->input('class_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('member.user', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('gymClass', function ($q3) use ($search) {
                    $q3->where('name', 'like', "%{$search}%");
                });
            });
        }

        $bookings = $query->latest()->get();

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

        if ($isGuest) {
            return redirect('login');
        } else if (auth()->check() && !$isMember && !$isTrainer && !$isAdmin) {
            return redirect('subscriptions/create');
        }

        $classes = GymClass::with(['trainer.user', 'room'])->get();
        $members = $isMember ? Member::where('user_id', $user->id)->get() : Member::all();

        return view('bookings.create', compact('members', 'classes', 'isMember'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $selectedClassId = (int) $request->input('class_id');
        $selectedClass = GymClass::findOrFail($selectedClassId);

        $isClassFull = Booking::where('class_id', $selectedClassId)
            ->where('status', 'confirmed')
            ->count() >= (int) $selectedClass->capacity;

        if ($isClassFull) {
            return back()->withInput()->with('error', 'This class is already full.');
        }

        if ($user && $user->isMember()) {
            $member = Member::where('user_id', $user->id)->firstOrFail();

            $request->validate([
                'class_id' => 'required|exists:classes,id',
            ]);

            $alreadyBooked = Booking::where('member_id', $member->id)
                ->where('class_id', $request->class_id)
                ->exists();

            if ($alreadyBooked) {
                return back()->with('error', 'You already booked this class.');
            }

            $hasTimeConflict = Booking::where('member_id', $member->id)
                ->where('status', '!=', 'cancelled')
                ->whereHas('gymClass', function ($query) use ($selectedClass) {
                    $query->where('schedule', $selectedClass->schedule);
                })
                ->exists();

            if ($hasTimeConflict) {
                return back()->withInput()->with('error', 'Time conflict: you already have another class booked at this time.');
            }

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

        $alreadyBooked = Booking::where('member_id', $request->member_id)
            ->where('class_id', $request->class_id)
            ->exists();

        if ($alreadyBooked) {
            return back()->with('error', 'This member already booked this class.');
        }

        $hasTimeConflict = Booking::where('member_id', $request->member_id)
            ->where('status', '!=', 'cancelled')
            ->whereHas('gymClass', function ($query) use ($selectedClass) {
                $query->where('schedule', $selectedClass->schedule);
            })
            ->exists();

        if ($hasTimeConflict) {
            return back()->withInput()->with('error', 'Time conflict: this member already has another class booked at this time.');
        }

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
        $user = auth()->user();

        if (!$user->isAdmin() && $booking->member->user_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        $booking->update([
            'status' => $request->status,
        ]);

        return redirect('bookings')->with('success', 'Booking Updated successfully!');
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

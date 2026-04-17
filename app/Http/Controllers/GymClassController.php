<?php

namespace App\Http\Controllers;

use App\Models\GymClass;
use App\Models\Room;
use App\Models\Trainer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GymClassController extends Controller
{
    private const CATEGORY_ROOM_MAP = [
        'combat' => 'Combat Sports Room',
        'yoga_pilates' => 'Yoga & Pilates Studio',
        'group_training' => 'Group Training Room',
        'fitness_machines' => 'Fitness Machines Hall',
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = GymClass::with(['trainer.user', 'room', 'bookings']);

        if ($user && $user->isTrainer()) {
            $query->where('trainer_id', $user->trainer->id);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('trainer.user', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('room_id')) {
            $query->where('room_id', $request->input('room_id'));
        }

        $classes = $query->latest()->get();

        return view('classes.index', compact('classes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();

        if (!$user || (!$user->isTrainer() && !$user->isAdmin())) {
            abort(403);
        }

        $trainers = Trainer::with('user')->get();
        $isTrainer = $user->isTrainer();
        $categories = $this->categories();

        return view('classes.create', compact('trainers', 'categories', 'isTrainer'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $createFullMonth = $request->boolean('create_full_month');
        $scheduleRule = $createFullMonth
            ? 'required|date_format:Y-m-d\\TH:i'
            : 'nullable|date_format:Y-m-d\\TH:i';
        
        // If trainer is creating, automatically set their trainer_id
        if ($user && $user->isTrainer()) {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'category' => 'required|in:combat,yoga_pilates,group_training,fitness_machines',
                'description' => 'nullable|string',
                'schedule' => $scheduleRule,
                'capacity' => 'nullable|integer|min:1|max:30',
                'create_full_month' => 'nullable|boolean',
            ]);
            $validated['trainer_id'] = $user->trainer->id;
        } else {
            $validated = $request->validate([
                'trainer_id' => 'required|exists:trainers,id',
                'name' => 'required|string|max:255',
                'category' => 'required|in:combat,yoga_pilates,group_training,fitness_machines',
                'description' => 'nullable|string',
                'schedule' => $scheduleRule,
                'capacity' => 'nullable|integer|min:1|max:30',
                'create_full_month' => 'nullable|boolean',
            ]);
        }

        $validated['room_id'] = $this->resolveRoomIdByCategory($validated['category']);

        if (!$this->trainerCanHandleCategory($validated['trainer_id'], $validated['category'])) {
            return back()->withInput()->with('error', 'Specialty mismatch: this trainer cannot teach the selected class category.');
        }

        $schedule = !empty($validated['schedule'])
            ? Carbon::createFromFormat('Y-m-d\\TH:i', $validated['schedule'])
            : null;

        unset($validated['create_full_month']);

        if ($createFullMonth && $schedule) {
            $conflictAt = null;
            $slot = $schedule->copy();
            $endOfMonth = $schedule->copy()->endOfMonth();

            while ($slot->lessThanOrEqualTo($endOfMonth)) {
                if ($this->hasScheduleConflict($validated['trainer_id'], $validated['room_id'], $slot)) {
                    $conflictAt = $slot->copy();
                    break;
                }

                $slot->addWeek();
            }

            if ($conflictAt) {
                return back()->withInput()->with('error', 'Conflict detected at ' . $conflictAt->format('Y-m-d H:i') . '. A trainer or room is already booked at that time.');
            }

            $createdCount = 0;
            $slot = $schedule->copy();

            while ($slot->lessThanOrEqualTo($endOfMonth)) {
                GymClass::create([
                    ...$validated,
                    'schedule' => $slot->format('Y-m-d H:i:s'),
                ]);
                $createdCount++;
                $slot->addWeek();
            }

            $redirectRoute = $user && $user->isAdmin() ? 'classes.index' : 'trainer.dashboard';

            return redirect()->route($redirectRoute)
                ->with('success', "{$createdCount} classes created for this month successfully!");
        }

        if ($schedule) {
            if ($this->hasScheduleConflict($validated['trainer_id'], $validated['room_id'], $schedule)) {
                return back()->withInput()->with('error', 'Conflict detected. This trainer or room already has a class at the selected time.');
            }

            $validated['schedule'] = $schedule->format('Y-m-d H:i:s');
        }

        GymClass::create($validated);

        $redirectRoute = $user && $user->isAdmin() ? 'classes.index' : 'trainer.dashboard';

        return redirect()->route($redirectRoute)
            ->with('success', 'Class created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(GymClass $gymClass)
    {
        $gymClass->load(['trainer.user', 'bookings.member.user']);

        return view('classes.show', compact('gymClass'));
    }

    /**
     * Show the form for editing the resource.
     */
    public function edit(GymClass $gymClass)
    {
        $user = auth()->user();

        if (!$user || (!$user->isAdmin() && (!$user->isTrainer() || $gymClass->trainer_id !== $user->trainer->id))) {
            abort(403);
        }

        $trainers = Trainer::with('user')->get();
        $categories = $this->categories();

        return view('classes.edit', compact('gymClass', 'trainers', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GymClass $gymClass)
    {
        $user = auth()->user();

        if (!$user || (!$user->isAdmin() && (!$user->isTrainer() || $gymClass->trainer_id !== $user->trainer->id))) {
            abort(403);
        }

        $validated = $request->validate([
            'trainer_id' => 'sometimes|exists:trainers,id',
            'category' => 'required|in:combat,yoga_pilates,group_training,fitness_machines',
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'schedule' => 'nullable|date_format:Y-m-d\\TH:i',
            'capacity' => 'nullable|integer|min:1|max:30',
        ]);

        // Trainers should not reassign class to another trainer
        if ($user->isTrainer()) {
            unset($validated['trainer_id']);
        }

        $validated['room_id'] = $this->resolveRoomIdByCategory($validated['category']);

        $targetTrainerId = $validated['trainer_id'] ?? $gymClass->trainer_id;

        if (!$this->trainerCanHandleCategory($targetTrainerId, $validated['category'])) {
            return back()->withInput()->with('error', 'Specialty mismatch: this trainer cannot teach the selected class category.');
        }

        $targetRoomId = $validated['room_id'] ?? $gymClass->room_id;
        $targetSchedule = !empty($validated['schedule'])
            ? Carbon::createFromFormat('Y-m-d\\TH:i', $validated['schedule'])
            : Carbon::parse($gymClass->schedule);

        if ($this->hasScheduleConflict($targetTrainerId, $targetRoomId, $targetSchedule, $gymClass->id)) {
            return back()->withInput()->with('error', 'Conflict detected. This trainer or room already has a class at the selected time.');
        }

        if (!empty($validated['schedule'])) {
            $validated['schedule'] = $targetSchedule->format('Y-m-d H:i:s');
        }

        $gymClass->update($validated);

        return redirect()->route('classes.show', $gymClass)
            ->with('success', 'Class updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GymClass $gymClass)
    {
        $user = auth()->user();

        if (!$user || (!$user->isAdmin() && (!$user->isTrainer() || $gymClass->trainer_id !== $user->trainer->id))) {
            abort(403);
        }

        $gymClass->delete();

        return redirect()->route('classes.index')
            ->with('success', 'Class deleted successfully!');
    }

    private function hasScheduleConflict(int $trainerId, int $roomId, Carbon $schedule, ?int $ignoreClassId = null): bool
    {
        $scheduleAt = $schedule->format('Y-m-d H:i:s');

        $roomConflict = GymClass::where('room_id', $roomId)
            ->where('schedule', $scheduleAt)
            ->when($ignoreClassId, fn ($q) => $q->where('id', '!=', $ignoreClassId))
            ->exists();

        if ($roomConflict) {
            return true;
        }

        return GymClass::where('trainer_id', $trainerId)
            ->where('schedule', $scheduleAt)
            ->when($ignoreClassId, fn ($q) => $q->where('id', '!=', $ignoreClassId))
            ->exists();
    }

    private function categories(): array
    {
        return [
            'combat' => 'Combat Sports',
            'yoga_pilates' => 'Yoga & Pilates',
            'group_training' => 'Group Training',
            'fitness_machines' => 'Fitness Machines',
        ];
    }

    private function resolveRoomIdByCategory(string $category): int
    {
        $roomName = self::CATEGORY_ROOM_MAP[$category] ?? self::CATEGORY_ROOM_MAP['group_training'];
        $room = Room::where('name', $roomName)->first();

        if (!$room) {
            abort(500, 'Room mapping is not configured correctly.');
        }

        return $room->id;
    }

    private function trainerCanHandleCategory(int $trainerId, string $category): bool
    {
        $trainer = Trainer::find($trainerId);

        if (!$trainer) {
            return false;
        }

        return $trainer->specialty === $category;
    }
}

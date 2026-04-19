<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

    public function index(Request $request)
    {
        $query = GymClass::with([
            'trainer.user',
            'room',
            'bookings.member.user',
        ]);

        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('trainer.user', function ($trainerQuery) use ($search) {
                        $trainerQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('room', function ($roomQuery) use ($search) {
                        $roomQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('room_id')) {
            $query->where('room_id', $request->input('room_id'));
        }

        $classes = $query->latest()->get()->map(function ($gymClass) {
            return [
                'id' => $gymClass->id,
                'trainer_id' => $gymClass->trainer_id,
                'room_id' => $gymClass->room_id,
                'name' => $gymClass->name,
                'description' => $gymClass->description,
                'schedule' => $gymClass->schedule,
                'capacity' => $gymClass->capacity,
                'category' => $gymClass->category,
                'created_at' => $gymClass->created_at,
                'updated_at' => $gymClass->updated_at,
                'trainer' => $gymClass->trainer ? [
                    'id' => $gymClass->trainer->id,
                    'specialty' => $gymClass->trainer->specialty,
                    'user' => $gymClass->trainer->user ? [
                        'id' => $gymClass->trainer->user->id,
                        'name' => $gymClass->trainer->user->name,
                        'email' => $gymClass->trainer->user->email,
                        'profile_picture' => $gymClass->trainer->user->profile_picture,
                    ] : null,
                ] : null,
                'room' => $gymClass->room ? [
                    'id' => $gymClass->room->id,
                    'name' => $gymClass->room->name,
                ] : null,
                'bookings' => $gymClass->bookings->map(function ($booking) {
                    return [
                        'id' => $booking->id,
                        'status' => $booking->status,
                        'created_at' => $booking->created_at,
                        'member' => $booking->member ? [
                            'id' => $booking->member->id,
                            'user' => $booking->member->user ? [
                                'id' => $booking->member->user->id,
                                'name' => $booking->member->user->name,
                                'email' => $booking->member->user->email,
                            ] : null,
                        ] : null,
                    ];
                })->values(),
            ];
        })->values();

        return response()->json([
            'message' => 'Classes retrieved successfully',
            'data' => $classes,
        ], 200);
    }

    public function show(GymClass $gymClass)
    {
        $gymClass->load([
            'trainer.user',
            'room',
            'bookings.member.user',
        ]);

        return response()->json([
            'message' => 'Class retrieved successfully',
            'data' => [
                'id' => $gymClass->id,
                'trainer_id' => $gymClass->trainer_id,
                'room_id' => $gymClass->room_id,
                'name' => $gymClass->name,
                'description' => $gymClass->description,
                'schedule' => $gymClass->schedule,
                'capacity' => $gymClass->capacity,
                'category' => $gymClass->category,
                'created_at' => $gymClass->created_at,
                'updated_at' => $gymClass->updated_at,
                'trainer' => $gymClass->trainer ? [
                    'id' => $gymClass->trainer->id,
                    'specialty' => $gymClass->trainer->specialty,
                    'user' => $gymClass->trainer->user ? [
                        'id' => $gymClass->trainer->user->id,
                        'name' => $gymClass->trainer->user->name,
                        'email' => $gymClass->trainer->user->email,
                        'profile_picture' => $gymClass->trainer->user->profile_picture,
                    ] : null,
                ] : null,
                'room' => $gymClass->room ? [
                    'id' => $gymClass->room->id,
                    'name' => $gymClass->room->name,
                ] : null,
                'bookings' => $gymClass->bookings->map(function ($booking) {
                    return [
                        'id' => $booking->id,
                        'status' => $booking->status,
                        'created_at' => $booking->created_at,
                        'member' => $booking->member ? [
                            'id' => $booking->member->id,
                            'user' => $booking->member->user ? [
                                'id' => $booking->member->user->id,
                                'name' => $booking->member->user->name,
                                'email' => $booking->member->user->email,
                            ] : null,
                        ] : null,
                    ];
                })->values(),
            ],
        ], 200);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user || (!$user->isTrainer() && !$user->isAdmin())) {
            return response()->json([
                'message' => 'Unauthorized.',
            ], 403);
        }

        $createFullMonth = $request->boolean('create_full_month');
        $scheduleRule = $createFullMonth
            ? 'required|date_format:Y-m-d\TH:i'
            : 'nullable|date_format:Y-m-d\TH:i';

        if ($user->isTrainer()) {
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
            return response()->json([
                'message' => 'Specialty mismatch: this trainer cannot teach the selected class category.',
            ], 422);
        }

        $schedule = !empty($validated['schedule'])
            ? Carbon::createFromFormat('Y-m-d\TH:i', $validated['schedule'])
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
                return response()->json([
                    'message' => 'Conflict detected at ' . $conflictAt->format('Y-m-d H:i') . '. A trainer or room is already booked at that time.',
                ], 422);
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

            return response()->json([
                'message' => "{$createdCount} classes created for this month successfully!",
            ], 201);
        }

        if ($schedule) {
            if ($this->hasScheduleConflict($validated['trainer_id'], $validated['room_id'], $schedule)) {
                return response()->json([
                    'message' => 'Conflict detected. This trainer or room already has a class at the selected time.',
                ], 422);
            }

            $validated['schedule'] = $schedule->format('Y-m-d H:i:s');
        }

        $gymClass = GymClass::create($validated);

        return response()->json([
            'message' => 'Class created successfully!',
            'data' => [
                'id' => $gymClass->id,
            ],
        ], 201);
    }

    public function update(Request $request, GymClass $gymClass)
    {
        $user = auth()->user();

        if (!$user || (!$user->isAdmin() && (!$user->isTrainer() || $gymClass->trainer_id !== $user->trainer->id))) {
            return response()->json([
                'message' => 'Unauthorized.',
            ], 403);
        }

        $validated = $request->validate([
            'trainer_id' => 'sometimes|exists:trainers,id',
            'category' => 'required|in:combat,yoga_pilates,group_training,fitness_machines',
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'schedule' => 'nullable|date_format:Y-m-d\TH:i',
            'capacity' => 'nullable|integer|min:1|max:30',
        ]);

        if ($user->isTrainer()) {
            unset($validated['trainer_id']);
        }

        $validated['room_id'] = $this->resolveRoomIdByCategory($validated['category']);

        $targetTrainerId = $validated['trainer_id'] ?? $gymClass->trainer_id;

        if (!$this->trainerCanHandleCategory($targetTrainerId, $validated['category'])) {
            return response()->json([
                'message' => 'Specialty mismatch: this trainer cannot teach the selected class category.',
            ], 422);
        }

        $targetRoomId = $validated['room_id'] ?? $gymClass->room_id;
        $targetSchedule = !empty($validated['schedule'])
            ? Carbon::createFromFormat('Y-m-d\TH:i', $validated['schedule'])
            : Carbon::parse($gymClass->schedule);

        if ($this->hasScheduleConflict($targetTrainerId, $targetRoomId, $targetSchedule, $gymClass->id)) {
            return response()->json([
                'message' => 'Conflict detected. This trainer or room already has a class at the selected time.',
            ], 422);
        }

        if (!empty($validated['schedule'])) {
            $validated['schedule'] = $targetSchedule->format('Y-m-d H:i:s');
        }

        $gymClass->update($validated);

        return response()->json([
            'message' => 'Class updated successfully!',
            'data' => [
                'id' => $gymClass->id,
            ],
        ], 200);
    }

    public function destroy(GymClass $gymClass)
    {
        $user = auth()->user();

        if (!$user || (!$user->isAdmin() && (!$user->isTrainer() || $gymClass->trainer_id !== $user->trainer->id))) {
            return response()->json([
                'message' => 'Unauthorized.',
            ], 403);
        }

        $gymClass->delete();

        return response()->json([
            'message' => 'Class deleted successfully!',
        ], 200);
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
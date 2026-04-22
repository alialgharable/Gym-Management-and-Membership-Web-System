<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Program;
use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProgramController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (!$user) {
            abort(403);
        }

        if (!$user->isAdmin() && !$user->isTrainer() && !$user->isMember()) {
            abort(403);
        }

        $programsQuery = Program::with(['trainer.user', 'member.user']);

        if ($user->isTrainer()) {
            $trainer = $user->trainer;
            if (!$trainer) {
                abort(403);
            }

            $programsQuery->where('trainer_id', $trainer->id);
        } elseif ($user->isMember()) {
            $member = $user->member;
            if (!$member) {
                abort(403);
            }

            $programsQuery->where('member_id', $member->id);
        }

        $programs = $programsQuery->latest()->get();

        return view('programs.index', compact('programs'));
    }

    public function create()
    {
        $user = auth()->user();

        if (!$user || (!$user->isAdmin() && !$user->isTrainer())) {
            abort(403);
        }

        $trainers = Trainer::with('user')->orderBy('id')->get();

        $members = Member::with(['user', 'trainer.user'])
            ->whereNotNull('trainer_id')
            ->orderBy('id')
            ->get();

        $selectedTrainerId = $user->isTrainer() ? optional($user->trainer)->id : old('trainer_id');

        return view('programs.create', compact('trainers', 'members', 'selectedTrainerId'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user || (!$user->isAdmin() && !$user->isTrainer())) {
            abort(403);
        }

        $trainerRule = Rule::exists('trainers', 'id');

        if ($user->isTrainer()) {
            $trainerId = optional($user->trainer)->id;
            if (!$trainerId) {
                abort(403);
            }

            $request->merge(['trainer_id' => $trainerId]);
            $trainerRule = Rule::in([$trainerId]);
        }

        $validated = $request->validate([
            'trainer_id' => ['required', $trainerRule],
            'member_id' => [
                'required',
                Rule::exists('members', 'id')->where(function ($query) use ($request) {
                    $query->where('trainer_id', $request->input('trainer_id'));
                }),
            ],
            'title' => 'required|string|max:255',
            'duration_weeks' => 'required|integer|min:1|max:52',
            'goal' => 'nullable|string|max:1500',
            'notes' => 'nullable|string|max:5000',
        ]);

        $trainer = Trainer::findOrFail($validated['trainer_id']);

        Program::create([
            'trainer_id' => $trainer->id,
            'member_id' => $validated['member_id'],
            'specialty' => $trainer->specialty,
            'title' => $validated['title'],
            'duration_weeks' => $validated['duration_weeks'],
            'goal' => $validated['goal'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('programs.index')->with('success', 'Program created successfully.');
    }

    public function show(Program $program)
    {
        $this->authorizeProgramAccess($program);

        $program->load(['trainer.user', 'member.user']);

        return view('programs.show', compact('program'));
    }

    public function edit(Program $program)
    {
        $this->authorizeProgramManagement($program);

        $user = auth()->user();

        $trainers = Trainer::with('user')->orderBy('id')->get();

        $members = Member::with(['user', 'trainer.user'])
            ->whereNotNull('trainer_id')
            ->orderBy('id')
            ->get();

        $selectedTrainerId = $user->isTrainer() ? optional($user->trainer)->id : old('trainer_id', $program->trainer_id);

        return view('programs.edit', compact('program', 'trainers', 'members', 'selectedTrainerId'));
    }

    public function update(Request $request, Program $program)
    {
        $this->authorizeProgramManagement($program);

        $user = auth()->user();

        $trainerRule = Rule::exists('trainers', 'id');

        if ($user->isTrainer()) {
            $trainerId = optional($user->trainer)->id;
            if (!$trainerId) {
                abort(403);
            }

            $request->merge(['trainer_id' => $trainerId]);
            $trainerRule = Rule::in([$trainerId]);
        }

        $validated = $request->validate([
            'trainer_id' => ['required', $trainerRule],
            'member_id' => [
                'required',
                Rule::exists('members', 'id')->where(function ($query) use ($request) {
                    $query->where('trainer_id', $request->input('trainer_id'));
                }),
            ],
            'title' => 'required|string|max:255',
            'duration_weeks' => 'required|integer|min:1|max:52',
            'goal' => 'nullable|string|max:1500',
            'notes' => 'nullable|string|max:5000',
        ]);

        $trainer = Trainer::findOrFail($validated['trainer_id']);

        $program->update([
            'trainer_id' => $trainer->id,
            'member_id' => $validated['member_id'],
            'specialty' => $trainer->specialty,
            'title' => $validated['title'],
            'duration_weeks' => $validated['duration_weeks'],
            'goal' => $validated['goal'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('programs.show', $program)->with('success', 'Program updated successfully.');
    }

    public function destroy(Program $program)
    {
        $this->authorizeProgramManagement($program);

        $program->delete();

        return redirect()->route('programs.index')->with('success', 'Program deleted successfully.');
    }

    private function authorizeProgramAccess(Program $program): void
    {
        $user = auth()->user();

        if (!$user) {
            abort(403);
        }

        if ($user->isAdmin()) {
            return;
        }

        if ($user->isTrainer() && $program->trainer_id === optional($user->trainer)->id) {
            return;
        }

        if ($user->isMember() && $program->member_id === optional($user->member)->id) {
            return;
        }

        abort(403);
    }

    private function authorizeProgramManagement(Program $program): void
    {
        $user = auth()->user();

        if (!$user) {
            abort(403);
        }

        if ($user->isAdmin()) {
            return;
        }

        if ($user->isTrainer() && $program->trainer_id === optional($user->trainer)->id) {
            return;
        }

        abort(403);
    }
}

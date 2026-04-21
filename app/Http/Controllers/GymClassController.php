<?php

namespace App\Http\Controllers;

use App\Models\GymClass;
use App\Models\Trainer;

class GymClassController extends Controller
{
    public function index()
    {
        return view('classes.index');
    }

    public function show(GymClass $gymClass)
    {
        return view('classes.show', ['classId' => $gymClass->id]);
    }

    public function create()
    {
        $user = auth()->user();
        $isTrainer = $user && $user->isTrainer();

        $trainers = Trainer::with('user')->get();
        $categories = Trainer::SPECIALTIES;

        return view('classes.create', compact('isTrainer', 'trainers', 'categories'));
    }

    public function edit(GymClass $gymClass)
    {
        $user = auth()->user();
        $isTrainer = $user && $user->isTrainer();

        if ($isTrainer) {
            $trainerId = optional($user->trainer)->id;

            if (!$trainerId || $gymClass->trainer_id !== $trainerId) {
                abort(403);
            }
        }

        $trainers = Trainer::with('user')->get();
        $categories = Trainer::SPECIALTIES;

        return view('classes.edit', compact('gymClass', 'isTrainer', 'trainers', 'categories'));
    }
}
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
        return view('classes.create');
    }

    public function edit(GymClass $gymClass)
    {
        return view('classes.edit', ['classId' => $gymClass->id]);
    }
}
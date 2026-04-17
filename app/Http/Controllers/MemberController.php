<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Member::with(['user', 'subscription']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->whereHas('subscription', function ($q) {
                    $q->where('status', 'active');
                });
            } elseif ($status === 'inactive') {
                $query->whereDoesntHave('subscription', function ($q) {
                    $q->where('status', 'active');
                });
            }
        }

        $members = $query->latest()->get();

        return view('admin.members.index', compact('members'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member)
    {
        return view('admin.members.show', compact('member'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Member $member)
    {
        if (!auth()->check()) {
            abort(403);
        }

        $user = auth()->user();

        if (!$user->isAdmin() && $user->id !== $member->user_id) {
            abort(403);
        }

        return view('admin.members.edit', compact('member'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Member $member)
    {
        $user = auth()->user();

        if (!$user || (!$user->isAdmin() && $member->user_id !== $user->id)) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $member->user->id,
            'user_id' => 'sometimes|exists:users,id',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $member->user->name = $request->name;
        $member->user->email = $request->email;

        if ($request->filled('user_id') && $user->isAdmin()) {
            $member->user_id = $request->user_id;
        }

        if ($request->hasFile('profile_picture')) {
            if ($member->user->profile_picture && Storage::disk('public')->exists($member->user->profile_picture)) {
                Storage::disk('public')->delete($member->user->profile_picture);
            }

            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            $member->user->profile_picture = $path;
        }

        $member->user->save();
        $member->save();

        return redirect()->route('members.show', $member)
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        $user = $member->user;
        $deletedUserId = $member->user_id;
        $currentUserId = auth()->id();

        $member->delete();

        if ($user && $user->role === 'member') {
            $user->role = 'user'; // or whatever your default role is
            $user->save();
        }

        if ($currentUserId === $deletedUserId) {
            return redirect()->route('home')
                ->with('success', 'Member profile deleted successfully.');
        }

        return redirect()->route('members.index')
            ->with('success', 'Member removed successfully.');
    }
}

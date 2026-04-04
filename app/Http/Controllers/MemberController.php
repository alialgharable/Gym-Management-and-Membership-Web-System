<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $members = Member::with(['user', 'subscription'])->get();

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
        $user = auth()->user();

        if (!$user || (!$user->isAdmin() && $member->user_id !== $user->id)) {
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
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,'.$member->user->id,
            'user_id' => 'sometimes|exists:users,id',
        ]);

        if ($request->filled('name')) {
            $member->user->name = $request->name;
        }

        if ($request->filled('email')) {
            $member->user->email = $request->email;
        }

        if ($request->filled('user_id') && $user->isAdmin()) {
            $member->user_id = $request->user_id;
        }

        $member->push();

        if ($member->isDirty() || $member->user->isDirty()) {
            $member->save();
            $member->user->save();
        }

        return redirect()->route('members.show', $member)
            ->with('success', 'Member updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        $member->delete();
        return redirect()->route('members.index')->with('success', 'Member removed successfully');
    }
}

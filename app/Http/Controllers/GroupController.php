<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $groups = Group::withCount('members')->latest()->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['groups' => $groups]);
        }

        return view('groups.index', ['groups' => $groups]);
    }

    public function create()
    {
        return view('groups.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $group = $request->user()->ownedGroups()->create($validated);
        
        // Owner automatically joins the group
        $group->members()->attach($request->user()->id);

        \App\Models\UserActivity::log(
            $request->user(),
            'group_created',
            "Created community group: \"{$group->name}\"",
            ['group_id' => $group->id, 'group_name' => $group->name],
            $request
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => route('groups.show', $group),
                'message' => 'Group created successfully.',
                'group' => $group
            ]);
        }

        return redirect()->route('groups.show', $group)->with('success', 'Group created successfully.');
    }

    public function show(Group $group, Request $request)
    {
        $group->load(['owner', 'members', 'posts.user']);
        $isMember = $group->members->contains(auth()->id());
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'group' => $group,
                'isMember' => $isMember
            ]);
        }

        return view('groups.show', [
            'group' => $group,
            'isMember' => $isMember
        ]);
    }

    public function storePost(Request $request, Group $group)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'required|string',
        ]);

        if (!$group->members->contains($request->user()->id)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'You must be a member of this group to post.'], 403);
            }
            return back()->withErrors(['message' => 'You must be a member of this group to post.']);
        }

        $post = $group->posts()->create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'] ?? null,
            'content' => $validated['content'],
        ]);

        \App\Models\UserActivity::log(
            $request->user(),
            'post_created',
            "Posted in group \"{$group->name}\"",
            ['group_id' => $group->id, 'group_name' => $group->name, 'post_id' => $post->id],
            $request
        );

        if ($request->ajax() || $request->wantsJson()) {
            $post->load('user');
            return response()->json([
                'success' => true,
                'message' => 'Post published successfully.',
                'post' => [
                    'id' => $post->id,
                    'title' => $post->title,
                    'content' => $post->content,
                    'created_at' => $post->created_at->toIso8601String(),
                    'user' => [
                        'id' => $post->user->id,
                        'name' => $post->user->name,
                        'is_online' => $post->user->is_online,
                    ]
                ]
            ]);
        }

        return back()->with('success', 'Post published successfully.');
    }

    public function join(Group $group, Request $request)
    {
        if (!$group->members->contains(auth()->id())) {
            $group->members()->attach(auth()->id());
            \App\Models\UserActivity::log(
                auth()->user(),
                'group_joined',
                "Joined community group: \"{$group->name}\"",
                ['group_id' => $group->id, 'group_name' => $group->name]
            );
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'isMember' => true,
                'members_count' => $group->members()->count(),
                'message' => 'You have joined the group.'
            ]);
        }

        return back()->with('success', 'You have joined the group.');
    }

    public function leave(Group $group, Request $request)
    {
        $group->members()->detach(auth()->id());
        \App\Models\UserActivity::log(
            auth()->user(),
            'group_left',
            "Left community group: \"{$group->name}\"",
            ['group_id' => $group->id, 'group_name' => $group->name]
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'isMember' => false,
                'members_count' => $group->members()->count(),
                'message' => 'You have left the group.'
            ]);
        }

        return back()->with('success', 'You have left the group.');
    }
}


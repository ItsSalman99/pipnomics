<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use Inertia\Inertia;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::withCount('members')->latest()->get();
        return Inertia::render('Groups/Index', ['groups' => $groups]);
    }

    public function create()
    {
        return Inertia::render('Groups/Create');
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

        return redirect()->route('groups.show', $group)->with('success', 'Group created successfully.');
    }

    public function show(Group $group)
    {
        $group->load(['owner', 'members', 'posts.user']);
        $isMember = $group->members->contains(auth()->id());
        
        return Inertia::render('Groups/Show', [
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

        return back()->with('success', 'Post published successfully.');
    }

    public function join(Group $group)
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
        return back()->with('success', 'You have joined the group.');
    }

    public function leave(Group $group)
    {
        $group->members()->detach(auth()->id());
        \App\Models\UserActivity::log(
            auth()->user(),
            'group_left',
            "Left community group: \"{$group->name}\"",
            ['group_id' => $group->id, 'group_name' => $group->name]
        );
        return back()->with('success', 'You have left the group.');
    }
}

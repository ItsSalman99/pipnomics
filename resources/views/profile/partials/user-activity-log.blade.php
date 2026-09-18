<section>
    <header class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Activity & Audit Trail</span>
            </h2>
            <p class="mt-1 text-xs text-slate-400">
                Full chronological log of security, authentication, and community events on your account.
            </p>
        </div>

        <!-- Filter Buttons -->
        <div class="flex flex-wrap items-center gap-1.5 bg-slate-950/80 p-1 rounded-lg border border-slate-800 self-start">
            <button type="button" onclick="window.filterActivityLogs('all')" id="activity-tab-all" class="px-2.5 py-1 text-xs font-semibold rounded-md transition bg-emerald-500 text-slate-950 font-bold">All</button>
            <button type="button" onclick="window.filterActivityLogs('auth')" id="activity-tab-auth" class="px-2.5 py-1 text-xs font-semibold rounded-md transition text-slate-400 hover:text-white">Auth</button>
            <button type="button" onclick="window.filterActivityLogs('community')" id="activity-tab-community" class="px-2.5 py-1 text-xs font-semibold rounded-md transition text-slate-400 hover:text-white">Community</button>
        </div>
    </header>

    <div class="mt-6 overflow-hidden rounded-xl border border-slate-800/80 bg-slate-950/40">
        @if(isset($activities) && count($activities) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-950/80 text-slate-400 uppercase font-mono text-[10px] tracking-wider border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-4 w-40">Event Type</th>
                            <th class="py-3 px-4">Description</th>
                            <th class="py-3 px-4 w-32 hidden md:table-cell">IP Address</th>
                            <th class="py-3 px-4 w-44 text-right">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-850/60" id="activity-logs-tbody">
                        @foreach($activities as $activity)
                            @php
                                $type = $activity->activity_type;
                                $isAuth = in_array($type, ['login', 'logout', 'register', 'profile_updated', 'premium_toggled']);
                                $isCommunity = in_array($type, ['comment_created', 'post_created', 'group_created', 'group_joined', 'group_left']);
                                
                                $badgeClass = match($type) {
                                    'login' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
                                    'logout' => 'bg-slate-800 text-slate-400 border-slate-700',
                                    'register' => 'bg-teal-500/10 text-teal-400 border-teal-500/30',
                                    'comment_created' => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                                    'post_created' => 'bg-blue-500/10 text-blue-400 border-blue-500/30',
                                    'group_created' => 'bg-purple-500/10 text-purple-400 border-purple-500/30',
                                    'group_joined' => 'bg-indigo-500/10 text-indigo-400 border-indigo-500/30',
                                    'group_left' => 'bg-rose-500/10 text-rose-400 border-rose-500/30',
                                    'profile_updated' => 'bg-cyan-500/10 text-cyan-400 border-cyan-500/30',
                                    'premium_toggled' => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/30',
                                    default => 'bg-slate-800 text-slate-300 border-slate-700'
                                };
                            @endphp
                            <tr class="activity-row hover:bg-slate-900/50 transition" data-category="{{ $isAuth ? 'auth' : ($isCommunity ? 'community' : 'other') }}">
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono font-bold border {{ $badgeClass }}">
                                        {{ str_replace('_', ' ', strtoupper($type)) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-slate-200 font-medium">
                                    {{ $activity->description }}
                                </td>
                                <td class="py-3 px-4 text-slate-400 font-mono text-[11px] hidden md:table-cell">
                                    {{ $activity->ip_address ?? '127.0.0.1' }}
                                </td>
                                <td class="py-3 px-4 text-right text-slate-400 font-mono text-[11px]">
                                    {{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-8 text-center text-slate-500 text-xs">
                No activity logs recorded yet.
            </div>
        @endif
    </div>
</section>

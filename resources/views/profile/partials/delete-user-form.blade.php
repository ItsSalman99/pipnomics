<section class="space-y-6">
    <header>
        <h2 class="text-lg font-bold text-rose-400">
            Delete Account
        </h2>
        <p class="mt-1 text-xs text-slate-400 leading-relaxed">
            Once your account is deleted, all of its resources, activity history, and forum posts will be permanently removed.
        </p>
    </header>

    <button 
        type="button" 
        onclick="document.getElementById('confirm-user-deletion-modal').classList.remove('hidden')" 
        class="bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs px-5 py-2.5 rounded-xl transition border border-rose-500/30 shadow-md shadow-rose-600/10"
    >
        Delete Account
    </button>

    <!-- Confirm Deletion Modal -->
    <div id="confirm-user-deletion-modal" class="fixed inset-0 z-50 hidden bg-slate-950/85 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 w-full max-w-lg rounded-2xl overflow-hidden shadow-2xl p-6 space-y-4">
            <h3 class="text-lg font-bold text-white">
                Are you sure you want to delete your account?
            </h3>
            <p class="text-xs text-slate-400 leading-relaxed">
                Please enter your account password to confirm you would like to permanently delete your Pipnomics account and all associated telemetry.
            </p>

            <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4 pt-2">
                @csrf
                @method('delete')

                <div>
                    <input 
                        id="delete_password" 
                        name="password" 
                        type="password" 
                        placeholder="Password" 
                        required 
                        class="w-full bg-slate-950 text-sm text-slate-100 border border-slate-800 rounded-xl px-4 py-2.5 focus:outline-none focus:border-rose-500/50"
                    />
                    @if ($errors->userDeletion->has('password'))
                        <p class="text-rose-400 text-xs mt-1.5">{{ $errors->userDeletion->first('password') }}</p>
                    @endif
                </div>

                <div class="flex justify-end space-x-3 pt-2">
                    <button 
                        type="button" 
                        onclick="document.getElementById('confirm-user-deletion-modal').classList.add('hidden')" 
                        class="px-4 py-2 text-xs font-semibold text-slate-400 hover:text-white transition"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        class="bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs px-5 py-2.5 rounded-xl transition border border-rose-500/30"
                    >
                        Confirm Account Deletion
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

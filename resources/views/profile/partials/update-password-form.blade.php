<section>
    <header>
        <h2 class="text-lg font-bold text-white">
            Update Password
        </h2>
        <p class="mt-1 text-xs text-slate-400">
            Ensure your account is using a long, random password to stay secure.
        </p>
    </header>

    <form id="profile-password-form" onsubmit="window.submitUpdatePassword(event)" class="mt-6 space-y-5">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Current Password</label>
            <input 
                id="update_password_current_password" 
                name="current_password" 
                type="password" 
                autocomplete="current-password" 
                required 
                class="w-full bg-slate-950 text-sm text-slate-100 border border-slate-800 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-500/50"
            />
            <p id="password-current-error" class="text-rose-400 text-xs mt-1.5 hidden"></p>
        </div>

        <div>
            <label for="update_password_password" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">New Password</label>
            <input 
                id="update_password_password" 
                name="password" 
                type="password" 
                autocomplete="new-password" 
                required 
                class="w-full bg-slate-950 text-sm text-slate-100 border border-slate-800 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-500/50"
            />
            <p id="password-new-error" class="text-rose-400 text-xs mt-1.5 hidden"></p>
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Confirm Password</label>
            <input 
                id="update_password_password_confirmation" 
                name="password_confirmation" 
                type="password" 
                autocomplete="new-password" 
                required 
                class="w-full bg-slate-950 text-sm text-slate-100 border border-slate-800 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-500/50"
            />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button 
                id="profile-password-submit-btn"
                type="submit" 
                class="bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs px-5 py-2.5 rounded-xl transition shadow-md shadow-emerald-500/10"
            >
                Update Password
            </button>
            <span id="profile-password-status" class="text-xs text-emerald-400 font-semibold hidden">Password updated successfully.</span>
        </div>
    </form>
</section>

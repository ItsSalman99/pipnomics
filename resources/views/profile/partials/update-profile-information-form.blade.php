<section>
    <header>
        <h2 class="text-lg font-bold text-white">
            Profile Information
        </h2>
        <p class="mt-1 text-xs text-slate-400">
            Update your account's profile name and email address.
        </p>
    </header>

    <form id="profile-info-form" onsubmit="window.submitProfileInfo(event)" class="mt-6 space-y-5">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Name</label>
            <input 
                id="profile-name" 
                name="name" 
                type="text" 
                value="{{ old('name', Auth::user()->name) }}" 
                required 
                autofocus 
                autocomplete="name" 
                class="w-full bg-slate-950 text-sm text-slate-100 border border-slate-800 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-500/50"
            />
            <p id="profile-name-error" class="text-rose-400 text-xs mt-1.5 hidden"></p>
        </div>

        <div>
            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Email</label>
            <input 
                id="profile-email" 
                name="email" 
                type="email" 
                value="{{ old('email', Auth::user()->email) }}" 
                required 
                autocomplete="username" 
                class="w-full bg-slate-950 text-sm text-slate-100 border border-slate-800 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-500/50"
            />
            <p id="profile-email-error" class="text-rose-400 text-xs mt-1.5 hidden"></p>
        </div>

        @if ($mustVerifyEmail && ! Auth::user()->hasVerifiedEmail())
            <div class="p-3 bg-amber-500/10 border border-amber-500/20 rounded-xl">
                <p class="text-xs text-amber-300">
                    Your email address is unverified.
                    <button form="send-verification" class="underline text-amber-400 font-bold hover:text-amber-300 ml-1">
                        Click here to re-send the verification email.
                    </button>
                </p>
            </div>
        @endif

        <div class="flex items-center gap-4 pt-2">
            <button 
                id="profile-info-submit-btn"
                type="submit" 
                class="bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs px-5 py-2.5 rounded-xl transition shadow-md shadow-emerald-500/10"
            >
                Save Changes
            </button>
            <span id="profile-info-status" class="text-xs text-emerald-400 font-semibold hidden">Saved successfully.</span>
        </div>
    </form>
</section>

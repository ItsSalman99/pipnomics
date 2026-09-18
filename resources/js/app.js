// ==========================================================================
// Pipnomics Core Application & AJAX Client Engine
// ==========================================================================

import '../css/app.css';

// --------------------------------------------------------------------------
// 1. Toast Notification Utility
// --------------------------------------------------------------------------
window.showToast = function(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `pointer-events-auto flex items-center space-x-2.5 px-4 py-3 rounded-xl border shadow-2xl transition-all duration-300 transform translate-y-4 opacity-0 text-xs font-semibold ${
        type === 'success' 
            ? 'bg-emerald-950/90 text-emerald-300 border-emerald-500/30' 
            : (type === 'error' 
                ? 'bg-rose-950/90 text-rose-300 border-rose-500/30' 
                : 'bg-slate-900/90 text-slate-200 border-slate-750')
    }`;

    const icon = type === 'success'
        ? '<svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'
        : (type === 'error'
            ? '<svg class="w-4 h-4 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'
            : '<svg class="w-4 h-4 text-cyan-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>');

    toast.innerHTML = `${icon}<span>${message}</span>`;
    container.appendChild(toast);

    // Animate in
    requestAnimationFrame(() => {
        toast.classList.remove('translate-y-4', 'opacity-0');
    });

    // Auto remove
    setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-y-2');
        setTimeout(() => toast.remove(), 300);
    }, 4000);
};

window.showProRequiredToast = function(message) {
    window.showToast(message || 'This feature requires an active PRO membership.', 'error');
};

// --------------------------------------------------------------------------
// 2. Global AJAX Fetch Wrapper with CSRF & Error Handling
// --------------------------------------------------------------------------
window.pipnomicsFetch = async function(url, options = {}) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    const headers = {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken || '',
        ...(options.headers || {})
    };

    if (options.body && typeof options.body === 'object' && !(options.body instanceof FormData)) {
        headers['Content-Type'] = 'application/json';
        options.body = JSON.stringify(options.body);
    }

    try {
        const response = await fetch(url, {
            ...options,
            headers
        });

        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            throw { status: response.status, data };
        }

        return data;
    } catch (err) {
        if (err.status === 401) {
            window.openGuestAuthModal('access this feature');
        } else if (err.status === 403) {
            window.showToast(err.data?.error || err.data?.message || 'Action not authorized.', 'error');
        } else if (err.status === 422 && err.data?.errors) {
            // Validation errors
            const firstError = Object.values(err.data.errors)[0]?.[0];
            if (firstError) window.showToast(firstError, 'error');
        }
        throw err;
    }
};
window.pipfolioFetch = window.pipnomicsFetch;

// --------------------------------------------------------------------------
// 3. Theme Engine (Light Mode Only)
// --------------------------------------------------------------------------
function initThemeEngine() {
    document.documentElement.classList.remove('dark');
    document.documentElement.classList.add('theme-light');
    localStorage.setItem('theme', 'light');
}

// --------------------------------------------------------------------------
// 4. Navigation & Dropdowns
// --------------------------------------------------------------------------
function initNavigation() {
    const userDropdownBtn = document.getElementById('user-dropdown-btn');
    const userDropdownMenu = document.getElementById('user-dropdown-menu');

    if (userDropdownBtn && userDropdownMenu) {
        userDropdownBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdownMenu.classList.toggle('hidden');
        });

        document.addEventListener('click', (e) => {
            if (!userDropdownMenu.contains(e.target) && !userDropdownBtn.contains(e.target)) {
                userDropdownMenu.classList.add('hidden');
            }
        });
    }

    // Mobile menu toggle
    const mobileBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileBars = document.getElementById('mobile-icon-bars');
    const mobileClose = document.getElementById('mobile-icon-close');

    if (mobileBtn && mobileMenu) {
        mobileBtn.addEventListener('click', () => {
            const isClosed = mobileMenu.classList.contains('hidden');
            mobileMenu.classList.toggle('hidden');
            if (mobileBars && mobileClose) {
                if (isClosed) {
                    mobileBars.classList.add('hidden');
                    mobileClose.classList.remove('hidden');
                } else {
                    mobileBars.classList.remove('hidden');
                    mobileClose.classList.add('hidden');
                }
            }
        });
    }
}

// --------------------------------------------------------------------------
// 5. Global Market Session Clocks (UTC Live Indicators)
// --------------------------------------------------------------------------
function updateSessionClocks() {
    const now = new Date();
    const utcHour = now.getUTCHours();
    const utcMin = String(now.getUTCMinutes()).padStart(2, '0');
    const utcSec = String(now.getUTCSeconds()).padStart(2, '0');
    const utcHourStr = String(utcHour).padStart(2, '0');

    const clockEl = document.getElementById('live-utc-clock');
    if (clockEl) {
        clockEl.textContent = `${utcHourStr}:${utcMin}:${utcSec} UTC`;
    }

    const sessions = [
        { id: 'session-sydney', open: 22, close: 7 },
        { id: 'session-tokyo', open: 23, close: 8 },
        { id: 'session-london', open: 8, close: 17 },
        { id: 'session-newyork', open: 13, close: 22 },
    ];

    sessions.forEach(s => {
        const el = document.getElementById(s.id);
        if (!el) return;
        const dot = el.querySelector('.session-dot');
        const badge = el.querySelector('.session-badge');
        const label = el.querySelector('.session-label');
        const title = el.querySelector('.session-title');

        let isOpen = false;
        if (s.open < s.close) {
            isOpen = utcHour >= s.open && utcHour < s.close;
        } else {
            isOpen = utcHour >= s.open || utcHour < s.close;
        }

        if (isOpen) {
            if (dot) dot.className = 'w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse shadow-md shadow-emerald-500/50 session-dot shrink-0';
            if (badge) {
                badge.className = 'session-badge text-[9px] font-mono font-bold px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 shadow-sm shadow-emerald-500/10';
                badge.textContent = 'OPEN';
            }
            if (label) label.className = 'text-xs font-mono text-emerald-400 font-semibold session-label';
            if (title) title.className = 'text-xs font-bold text-white block session-title';
            el.classList.add('border-emerald-500/40', 'bg-emerald-950/15', 'shadow-sm', 'shadow-emerald-500/5');
            el.classList.remove('border-slate-800/70', 'bg-slate-950');
        } else {
            if (dot) dot.className = 'w-2.5 h-2.5 rounded-full bg-slate-700 session-dot shrink-0';
            if (badge) {
                badge.className = 'session-badge text-[9px] font-mono font-bold px-2 py-0.5 rounded-full bg-slate-850 text-slate-500 border border-slate-800';
                badge.textContent = 'CLOSED';
            }
            if (label) label.className = 'text-xs font-mono text-slate-500 session-label';
            if (title) title.className = 'text-xs font-bold text-slate-300 block session-title';
            el.classList.remove('border-emerald-500/40', 'bg-emerald-950/15', 'shadow-sm', 'shadow-emerald-500/5');
            el.classList.add('border-slate-800/70', 'bg-slate-950');
        }
    });
}

// --------------------------------------------------------------------------
// 6. Live Online Telemetry Polling (Every 15 Seconds via AJAX)
// --------------------------------------------------------------------------
function initTelemetryPolling() {
    const counterEl = document.getElementById('online-traders-count');
    if (!counterEl) return;

    setInterval(async () => {
        try {
            const data = await window.pipnomicsFetch('/dashboard?telemetry=1');
            if (data && typeof data.onlineTradersCount === 'number') {
                counterEl.textContent = data.onlineTradersCount;
            }
        } catch (e) {
            // silent fail on network hiccups
        }
    }, 15000);
}

// --------------------------------------------------------------------------
// 7. Interactive PRO Membership AJAX Toggle
// --------------------------------------------------------------------------
window.toggleUserPremium = async function() {
    const btn = document.getElementById('toggle-premium-btn');
    const btnText = document.getElementById('toggle-premium-btn-text');
    const badge = document.getElementById('user-premium-badge');
    
    if (btn) btn.disabled = true;

    try {
        const data = await window.pipnomicsFetch('/profile/toggle-premium', {
            method: 'POST'
        });

        if (data.success) {
            const isPro = data.is_premium;
            if (badge) {
                badge.textContent = isPro ? 'PRO MEMBER' : 'FREE ACCOUNT';
                badge.className = `text-[10px] font-bold px-2 py-0.5 rounded-full border ${
                    isPro ? 'bg-amber-500/10 text-amber-400 border-amber-500/30 shadow-md shadow-amber-500/5' : 'bg-slate-800 text-slate-400 border-slate-700'
                }`;
            }
            if (btn && btnText) {
                btnText.textContent = isPro ? 'Cancel Premium Status' : 'Activate Premium Membership';
                btn.className = `w-full py-2.5 px-4 rounded-lg font-bold text-xs transition duration-300 flex items-center justify-center space-x-2 border shadow-lg shadow-black/20 ${
                    isPro ? 'bg-slate-800 hover:bg-slate-750 text-slate-300 border-slate-700 hover:text-white' : 'bg-gradient-to-r from-emerald-500 to-cyan-500 hover:from-emerald-400 hover:to-cyan-400 text-slate-950 border-emerald-400 hover:scale-[1.02]'
                }`;
            }
            window.showToast(data.message || (isPro ? 'Upgraded to PRO membership!' : 'Downgraded to Free account.'), 'success');
        }
    } catch (err) {
        window.showToast('Failed to toggle premium status.', 'error');
    } finally {
        if (btn) btn.disabled = false;
    }
};

// --------------------------------------------------------------------------
// 8. Interactive News Show Page Engine (Dedicated Article Page, AJAX Likes, Threaded Replies)
// --------------------------------------------------------------------------
window.openNewsModal = function(item) {
    if (item && item.id) {
        window.location.href = `/news/${item.id}`;
        return;
    }
};

window.closeNewsModal = function() {
    const modal = document.getElementById('news-modal');
    if (modal) modal.classList.add('hidden');
};

window.newsShowApp = {
    async toggleLike(newsId) {
        const likeBtn = document.getElementById('article-like-btn');
        const likeIcon = document.getElementById('article-like-icon');
        const likeText = document.getElementById('article-like-btn-text');
        const likeBadge = document.getElementById('article-like-badge');

        if (!newsId) return;

        try {
            const data = await window.pipnomicsFetch(`/news/${newsId}/like`, {
                method: 'POST'
            });

            if (data.success) {
                const isLiked = data.liked;
                if (likeBadge) likeBadge.textContent = data.likes_count;

                if (isLiked) {
                    likeBtn?.classList.remove('bg-slate-800', 'hover:bg-slate-750', 'text-slate-300', 'hover:text-white', 'border-slate-700');
                    likeBtn?.classList.add('bg-rose-500/15', 'text-rose-400', 'border-rose-500/30', 'shadow-md', 'shadow-rose-500/10');
                    if (likeIcon) {
                        likeIcon.setAttribute('fill', 'currentColor');
                        likeIcon.className = 'w-4 h-4 fill-rose-500 text-rose-500 animate-pulse';
                    }
                    if (likeText) likeText.textContent = 'Liked';
                } else {
                    likeBtn?.classList.remove('bg-rose-500/15', 'text-rose-400', 'border-rose-500/30', 'shadow-md', 'shadow-rose-500/10');
                    likeBtn?.classList.add('bg-slate-800', 'hover:bg-slate-750', 'text-slate-300', 'hover:text-white', 'border-slate-700');
                    if (likeIcon) {
                        likeIcon.setAttribute('fill', 'none');
                        likeIcon.className = 'w-4 h-4 text-slate-400';
                    }
                    if (likeText) likeText.textContent = 'Like Article';
                }

                window.showToast(data.message, 'success');
            }
        } catch (err) {
            window.showToast(err.data?.message || 'Failed to update like status. Please login.', 'error');
        }
    },

    toggleReplyBox(commentId) {
        const box = document.getElementById(`reply-box-${commentId}`);
        if (!box) return;
        const isHidden = box.classList.contains('hidden');
        box.classList.toggle('hidden');
        if (isHidden) {
            const input = document.getElementById(`reply-input-${commentId}`);
            if (input) input.focus();
        }
    },

    async submitComment(e, parentId = null) {
        e.preventDefault();
        const newsId = window.CURRENT_ARTICLE_ID;
        if (!newsId) return;

        let input, submitBtn;
        if (parentId) {
            input = document.getElementById(`reply-input-${parentId}`);
            submitBtn = document.getElementById(`reply-submit-btn-${parentId}`);
        } else {
            input = document.getElementById('top-comment-input');
            submitBtn = document.getElementById('top-comment-submit-btn');
        }

        const content = input?.value.trim();
        if (!content) return;

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.dataset.origText = submitBtn.innerHTML;
            submitBtn.innerHTML = 'Posting...';
        }

        try {
            const payload = { content };
            if (parentId) {
                payload.parent_id = parentId;
            }

            const data = await window.pipnomicsFetch(`/news/${newsId}/comments`, {
                method: 'POST',
                body: payload
            });

            if (data.success && data.comment) {
                const comment = data.comment;
                
                if (parentId) {
                    // Append nested reply
                    const repliesList = document.getElementById(`replies-list-${parentId}`);
                    if (repliesList) {
                        repliesList.classList.remove('hidden');
                        const replyNode = document.createElement('div');
                        replyNode.className = 'bg-slate-900/90 border-l-2 border-emerald-500/50 pl-3 py-2 pr-3 rounded-r-lg border-y border-r border-slate-850 animate-fadeIn';
                        replyNode.innerHTML = `
                            <div class="flex justify-between items-center mb-1">
                                <div class="flex items-center space-x-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full inline-block ${comment.is_online ? 'bg-emerald-400 animate-pulse' : 'bg-slate-600'}"></span>
                                    <span class="text-xs font-bold text-emerald-400">${escapeHtml(comment.username || 'Trader')}</span>
                                </div>
                                <span class="text-[9px] font-mono text-slate-500">${formatTimeAgo(comment.created_at)}</span>
                            </div>
                            <p class="text-xs text-slate-300">${escapeHtml(comment.content)}</p>
                        `;
                        repliesList.appendChild(replyNode);
                    }
                    this.toggleReplyBox(parentId);
                } else {
                    // Prepend top-level comment
                    const placeholder = document.getElementById('no-comments-placeholder');
                    if (placeholder) placeholder.remove();

                    const container = document.getElementById('comments-container');
                    if (container) {
                        const firstLetter = (comment.username || 'T').charAt(0).toUpperCase();
                        const commentNode = document.createElement('div');
                        commentNode.id = `comment-node-${comment.id}`;
                        commentNode.className = 'bg-slate-950/60 border border-slate-800/80 rounded-xl p-4.5 space-y-3 transition duration-150 animate-fadeIn';
                        commentNode.innerHTML = `
                            <div class="flex justify-between items-center">
                                <div class="flex items-center space-x-2">
                                    <div class="w-7 h-7 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-xs text-emerald-400">
                                        ${firstLetter}
                                    </div>
                                    <div>
                                        <div class="flex items-center space-x-1.5">
                                            <span class="text-xs font-bold text-slate-200">${escapeHtml(comment.username || 'Trader')}</span>
                                            <span class="w-1.5 h-1.5 rounded-full inline-block ${comment.is_online ? 'bg-emerald-400 shadow-sm shadow-emerald-400 animate-pulse' : 'bg-slate-600'}"></span>
                                        </div>
                                        <span class="text-[10px] font-mono text-slate-500">${formatTimeAgo(comment.created_at)}</span>
                                    </div>
                                </div>
                                <button 
                                    type="button" 
                                    onclick="window.newsShowApp.toggleReplyBox(${comment.id})" 
                                    class="text-xs font-semibold text-slate-400 hover:text-emerald-400 transition flex items-center space-x-1 bg-slate-900 hover:bg-slate-850 px-2.5 py-1 rounded-md border border-slate-800"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                    <span>Reply</span>
                                </button>
                            </div>
                            <p class="text-xs text-slate-300 leading-relaxed pl-9">
                                ${escapeHtml(comment.content)}
                            </p>
                            <div id="reply-box-${comment.id}" class="hidden pl-9 pt-2">
                                <form onsubmit="window.newsShowApp.submitComment(event, ${comment.id})" class="space-y-2 bg-slate-900 p-3 rounded-lg border border-slate-800">
                                    <div class="text-[10px] text-emerald-400 font-mono font-semibold">Replying to ${escapeHtml(comment.username || 'Trader')}:</div>
                                    <textarea 
                                        id="reply-input-${comment.id}"
                                        rows="2" 
                                        placeholder="Write your reply..." 
                                        class="w-full bg-slate-950 text-xs text-slate-200 border border-slate-800 rounded p-2 focus:outline-none focus:border-emerald-500/50 resize-none"
                                        required
                                    ></textarea>
                                    <div class="flex justify-end space-x-2">
                                        <button 
                                            type="button" 
                                            onclick="window.newsShowApp.toggleReplyBox(${comment.id})" 
                                            class="px-3 py-1 text-xs text-slate-400 hover:text-white"
                                        >
                                            Cancel
                                        </button>
                                        <button 
                                            id="reply-submit-btn-${comment.id}"
                                            type="submit" 
                                            class="bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs px-3 py-1 rounded transition"
                                        >
                                            Post Reply
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div id="replies-list-${comment.id}" class="space-y-2.5 pl-9 pt-1 hidden"></div>
                        `;
                        container.prepend(commentNode);
                    }
                }

                if (input) input.value = '';

                // Update counter badges
                const commentsHeaderBadge = document.getElementById('comments-count-header');
                const articleCommentBadge = document.getElementById('article-comment-counter-badge');
                if (data.comments_count !== undefined) {
                    if (commentsHeaderBadge) commentsHeaderBadge.textContent = data.comments_count;
                    if (articleCommentBadge) articleCommentBadge.textContent = data.comments_count;
                }

                window.showToast(data.message || 'Comment published successfully!', 'success');
            }
        } catch (err) {
            window.showToast(err.data?.message || 'Failed to post comment.', 'error');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = submitBtn.dataset.origText || 'Post Comment';
            }
        }
    }
};

// --------------------------------------------------------------------------
// 9. Market News Hub Page Engine (`resources/views/news/index.blade.php`)
// --------------------------------------------------------------------------
window.newsApp = {
    items: [],
    filterType: 'all',
    searchQuery: '',

    init(initialNews) {
        this.items = initialNews || [];
        this.render();
    },

    setFilter(type) {
        this.filterType = type;
        ['all', 'news', 'analysis'].forEach(t => {
            const btn = document.getElementById(`news-tab-${t}`);
            if (btn) {
                if (t === type) {
                    btn.className = 'px-3.5 py-1.5 text-xs font-semibold rounded-md transition bg-emerald-500 text-slate-950 font-bold';
                } else {
                    btn.className = 'px-3.5 py-1.5 text-xs font-semibold rounded-md transition text-slate-400 hover:text-white';
                }
            }
        });
        this.render();
    },

    handleSearch(query) {
        this.searchQuery = query.toLowerCase().trim();
        this.render();
    },

    render() {
        const grid = document.getElementById('news-cards-grid');
        const empty = document.getElementById('news-empty-state');
        if (!grid) return;

        const filtered = this.items.filter(item => {
            if (this.filterType === 'news' && item.is_analysis) return false;
            if (this.filterType === 'analysis' && !item.is_analysis) return false;

            if (this.searchQuery) {
                const title = (item.title || '').toLowerCase();
                const desc = (item.description || item.summary || '').toLowerCase();
                if (!title.includes(this.searchQuery) && !desc.includes(this.searchQuery)) {
                    return false;
                }
            }
            return true;
        });

        if (filtered.length === 0) {
            grid.innerHTML = '';
            if (empty) empty.classList.remove('hidden');
            return;
        }

        if (empty) empty.classList.add('hidden');

        grid.innerHTML = filtered.map((item, idx) => `
            <a 
                href="/news/${item.id}"
                class="bg-slate-900 border border-slate-800/80 hover:border-emerald-500/40 rounded-xl p-5 shadow-lg flex flex-col justify-between group hover:bg-slate-850/60 transition duration-200 block text-inherit no-underline"
            >
                <div>
                    <div class="flex justify-between items-center text-[9px] font-mono text-slate-500 mb-2">
                        <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-2 py-0.5 rounded font-bold uppercase">
                            ${item.source || 'FXStreet'}
                        </span>
                        <span>${formatTimeAgo(item.published_at)}</span>
                    </div>

                    ${item.image ? `
                        <div class="w-full h-36 rounded-lg overflow-hidden bg-slate-950 mb-3 border border-slate-800">
                            <img src="${item.image}" alt="" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                        </div>
                    ` : ''}

                    <h3 class="text-xs font-bold text-white group-hover:text-emerald-400 transition-colors line-clamp-2 leading-snug mb-2">
                        ${escapeHtml(item.title)}
                    </h3>

                    <p class="text-[11px] text-slate-400 line-clamp-3 leading-relaxed mb-4">
                        ${escapeHtml(item.summary || item.description || '')}
                    </p>
                </div>

                <div class="pt-3 border-t border-slate-800 flex items-center justify-between text-[10px] font-mono">
                    <span class="text-slate-500 flex items-center space-x-1">
                        <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        <span>${item.comments_count || (item.comments ? item.comments.length : 0)} replies</span>
                    </span>
                    <span class="text-emerald-400 font-bold group-hover:translate-x-0.5 transition-transform flex items-center space-x-1">
                        <span>Read Full Story</span>
                        <span>&rarr;</span>
                    </span>
                </div>
            </a>
        `).join('');
    }
};

// --------------------------------------------------------------------------
// 10. Economic Calendar Engine (`resources/views/calendar/index.blade.php`)
// --------------------------------------------------------------------------
window.calendarApp = {
    events: [],
    viewMode: 'week', // day | week | month
    selectedDate: new Date(),
    selectedTimezone: 'LOCAL',
    timeFormat24: false,
    availableCurrencies: ['USD', 'EUR', 'GBP', 'AUD', 'CAD', 'CHF', 'JPY', 'NZD', 'CNY'],
    selectedCurrencies: ['USD', 'EUR', 'GBP', 'AUD', 'CAD', 'CHF', 'JPY', 'NZD', 'CNY'],
    selectedImpacts: ['High', 'Medium', 'Low', 'Holiday'],
    searchQuery: '',

    currencyMeta: {
        USD: { name: 'US Dollar', flag: '🇺🇸' },
        EUR: { name: 'Euro', flag: '🇪🇺' },
        GBP: { name: 'British Pound', flag: '🇬🇧' },
        JPY: { name: 'Japanese Yen', flag: '🇯🇵' },
        AUD: { name: 'Australian Dollar', flag: '🇦🇺' },
        CAD: { name: 'Canadian Dollar', flag: '🇨🇦' },
        CHF: { name: 'Swiss Franc', flag: '🇨🇭' },
        NZD: { name: 'New Zealand Dollar', flag: '🇳🇿' },
        CNY: { name: 'Chinese Yuan', flag: '🇨🇳' },
    },

    init(initialEvents) {
        this.events = initialEvents || [];
        this.renderCurrencyPills();
        this.render();
    },

    renderCurrencyPills() {
        const container = document.getElementById('cal-currency-pills');
        if (!container) return;

        container.innerHTML = this.availableCurrencies.map(curr => {
            const isSelected = this.selectedCurrencies.includes(curr);
            const meta = this.currencyMeta[curr] || { flag: '🌐' };
            return `
                <button 
                    type="button" 
                    onclick="window.calendarApp.toggleCurrency('${curr}')"
                    class="px-2 py-1 rounded-lg text-xs font-mono font-bold transition flex items-center space-x-1 ${
                        isSelected 
                            ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' 
                            : 'bg-slate-950 text-slate-500 border border-slate-800 hover:text-slate-300'
                    }"
                >
                    <span>${meta.flag}</span>
                    <span>${curr}</span>
                </button>
            `;
        }).join('');
    },

    toggleCurrency(curr) {
        if (this.selectedCurrencies.includes(curr)) {
            this.selectedCurrencies = this.selectedCurrencies.filter(c => c !== curr);
        } else {
            this.selectedCurrencies.push(curr);
        }
        this.renderCurrencyPills();
        this.render();
    },

    selectAllCurrencies() {
        this.selectedCurrencies = [...this.availableCurrencies];
        this.renderCurrencyPills();
        this.render();
    },

    clearAllCurrencies() {
        this.selectedCurrencies = [];
        this.renderCurrencyPills();
        this.render();
    },

    toggleImpact(level, checked) {
        if (checked) {
            if (!this.selectedImpacts.includes(level)) this.selectedImpacts.push(level);
        } else {
            this.selectedImpacts = this.selectedImpacts.filter(i => i !== level);
        }
        this.render();
    },

    setViewMode(mode) {
        this.viewMode = mode;
        ['day', 'week', 'month'].forEach(m => {
            const btn = document.getElementById(`cal-mode-${m}`);
            if (btn) {
                if (m === mode) {
                    btn.className = 'px-3 py-1 text-xs font-semibold rounded-md transition bg-emerald-500 text-slate-950 font-bold';
                } else {
                    btn.className = 'px-3 py-1 text-xs font-semibold rounded-md transition text-slate-400 hover:text-white';
                }
            }
        });
        this.render();
    },

    prevPeriod() {
        const d = new Date(this.selectedDate);
        if (this.viewMode === 'day') d.setDate(d.getDate() - 1);
        else if (this.viewMode === 'week') d.setDate(d.getDate() - 7);
        else d.setMonth(d.getMonth() - 1);
        this.selectedDate = d;
        this.render();
    },

    nextPeriod() {
        const d = new Date(this.selectedDate);
        if (this.viewMode === 'day') d.setDate(d.getDate() + 1);
        else if (this.viewMode === 'week') d.setDate(d.getDate() + 7);
        else d.setMonth(d.getMonth() + 1);
        this.selectedDate = d;
        this.render();
    },

    goToToday() {
        this.selectedDate = new Date();
        this.render();
    },

    setTimezone(tz) {
        this.selectedTimezone = tz;
        this.render();
    },

    toggleTimeFormat() {
        this.timeFormat24 = !this.timeFormat24;
        const label = document.getElementById('cal-timeformat-label');
        if (label) label.textContent = this.timeFormat24 ? '24H' : '12H';
        this.render();
    },

    handleSearch(q) {
        this.searchQuery = q.toLowerCase().trim();
        this.render();
    },

    getActiveRange() {
        const anchor = new Date(this.selectedDate);
        if (this.viewMode === 'day') {
            const start = new Date(anchor.setHours(0, 0, 0, 0));
            const end = new Date(anchor.setHours(23, 59, 59, 999));
            return { start, end };
        } else if (this.viewMode === 'week') {
            const day = anchor.getDay();
            const diff = anchor.getDate() - day;
            const start = new Date(anchor.setDate(diff));
            start.setHours(0, 0, 0, 0);
            const end = new Date(start);
            end.setDate(start.getDate() + 6);
            end.setHours(23, 59, 59, 999);
            return { start, end };
        } else {
            const start = new Date(anchor.getFullYear(), anchor.getMonth(), 1, 0, 0, 0, 0);
            const end = new Date(anchor.getFullYear(), anchor.getMonth() + 1, 0, 23, 59, 59, 999);
            return { start, end };
        }
    },

    formatEventTime(dateStr) {
        if (!dateStr) return '--:--';
        const date = new Date(dateStr);
        const options = {
            hour: 'numeric',
            minute: '2-digit',
            hour12: !this.timeFormat24
        };
        if (this.selectedTimezone !== 'LOCAL') {
            options.timeZone = this.selectedTimezone;
        }
        return new Intl.DateTimeFormat('en-US', options).format(date);
    },

    render() {
        const tbody = document.getElementById('cal-events-tbody');
        const empty = document.getElementById('cal-empty-state');
        const label = document.getElementById('cal-active-range-label');
        if (!tbody) return;

        const range = this.getActiveRange();

        // Update range label
        if (label) {
            if (this.viewMode === 'day') {
                label.textContent = range.start.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' });
            } else if (this.viewMode === 'week') {
                const s = range.start.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                const e = range.end.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                label.textContent = `${s} - ${e}`;
            } else {
                label.textContent = range.start.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
            }
        }

        // Filter events
        const filtered = this.events.filter(e => {
            const evDate = new Date(e.date);
            // Time range check
            if (evDate < range.start || evDate > range.end) return false;

            // Currency check
            if (this.selectedCurrencies.length > 0 && !this.selectedCurrencies.includes(e.country)) {
                return false;
            }

            // Impact check
            if (!this.selectedImpacts.includes(e.impact)) return false;

            // Search query check
            if (this.searchQuery) {
                const title = (e.title || '').toLowerCase();
                const curr = (e.country || '').toLowerCase();
                if (!title.includes(this.searchQuery) && !curr.includes(this.searchQuery)) {
                    return false;
                }
            }

            return true;
        });

        if (filtered.length === 0) {
            tbody.innerHTML = '';
            if (empty) empty.classList.remove('hidden');
            return;
        }

        if (empty) empty.classList.add('hidden');

        // Group by Date String
        const grouped = {};
        filtered.forEach(e => {
            const dateKey = new Date(e.date).toLocaleDateString('en-US', { weekday: 'long', month: 'short', day: 'numeric' });
            if (!grouped[dateKey]) grouped[dateKey] = [];
            grouped[dateKey].push(e);
        });

        let html = '';
        for (const [dateTitle, groupEvents] of Object.entries(grouped)) {
            html += `
                <tr class="bg-slate-950/90 text-slate-300 font-bold border-y border-slate-800">
                    <td colspan="7" class="py-2.5 px-4 text-xs font-mono tracking-wide text-emerald-400">
                        ${dateTitle}
                    </td>
                </tr>
            `;

            groupEvents.forEach((ev, i) => {
                const flag = this.currencyMeta[ev.country]?.flag || '🌐';
                const impactClass = ev.impact === 'High' 
                    ? 'bg-rose-500/15 text-rose-400 border-rose-500/30' 
                    : (ev.impact === 'Medium' 
                        ? 'bg-amber-500/15 text-amber-400 border-amber-500/30' 
                        : (ev.impact === 'Low' 
                            ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30' 
                            : 'bg-slate-800 text-slate-400 border-slate-700'));

                const formattedTime = this.formatEventTime(ev.date);

                html += `
                    <tr class="hover:bg-slate-850/40 transition">
                        <td class="py-3 px-4 font-mono text-slate-300">${formattedTime}</td>
                        <td class="py-3 px-4 font-mono font-bold text-white flex items-center space-x-1.5">
                            <span>${flag}</span>
                            <span>${ev.country}</span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-mono font-bold border ${impactClass}">
                                ${ev.impact}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-slate-200 font-medium">${escapeHtml(ev.title)}</td>
                        <td class="py-3 px-4 text-right font-mono font-bold text-white">${ev.actual || '--'}</td>
                        <td class="py-3 px-4 text-right font-mono text-slate-400">${ev.forecast || '--'}</td>
                        <td class="py-3 px-4 text-right font-mono text-slate-400">${ev.previous || '--'}</td>
                    </tr>
                `;
            });
        }

        tbody.innerHTML = html;
    }
};

// --------------------------------------------------------------------------
// 11. Community Forums AJAX Handlers (`resources/views/groups/*`)
// --------------------------------------------------------------------------
window.filterGroups = function(query) {
    const q = (query || '').toLowerCase().trim();
    const cards = document.querySelectorAll('.group-card');
    let visibleCount = 0;

    cards.forEach(card => {
        const name = card.getAttribute('data-name') || '';
        const desc = card.getAttribute('data-desc') || '';
        if (name.includes(q) || desc.includes(q)) {
            card.classList.remove('hidden');
            visibleCount++;
        } else {
            card.classList.add('hidden');
        }
    });

    const countEl = document.getElementById('groups-count');
    if (countEl) countEl.textContent = visibleCount;
};

window.toggleGroupMembership = async function(groupId, currentIsMember) {
    const btn = document.getElementById('group-membership-btn');
    if (btn) btn.disabled = true;

    const endpoint = currentIsMember ? `/groups/${groupId}/leave` : `/groups/${groupId}/join`;

    try {
        const data = await window.pipnomicsFetch(endpoint, { method: 'POST' });
        if (data.success) {
            const isNowMember = data.isMember;
            
            // Update button
            if (btn) {
                btn.textContent = isNowMember ? 'Leave Group' : 'Join Group';
                btn.className = `${isNowMember ? 'bg-rose-600 hover:bg-rose-500 text-white border-rose-500/30' : 'bg-gradient-to-r from-emerald-500 to-cyan-500 hover:from-emerald-400 hover:to-cyan-400 text-slate-950 border-emerald-400 shadow-md shadow-emerald-500/5'} px-4 py-2 rounded-lg font-bold text-xs transition duration-200 border hover:scale-[1.02]`;
                btn.setAttribute('onclick', `window.toggleGroupMembership(${groupId}, ${isNowMember})`);
            }

            // Update create post box visibility
            const postBox = document.getElementById('create-post-box');
            if (postBox) {
                if (isNowMember) postBox.classList.remove('hidden');
                else postBox.classList.add('hidden');
            }

            // Update sidebar members count
            const countEl = document.getElementById('group-sidebar-members-count');
            if (countEl && typeof data.members_count === 'number') {
                countEl.textContent = `${data.members_count} Active`;
            }

            window.showToast(data.message || (isNowMember ? 'Joined group!' : 'Left group.'), 'success');
        }
    } catch (err) {
        window.showToast('Could not update group membership.', 'error');
    } finally {
        if (btn) btn.disabled = false;
    }
};

window.submitGroupPost = async function(e, groupId) {
    e.preventDefault();
    const titleInput = document.getElementById('post-title-input');
    const contentInput = document.getElementById('post-content-input');
    const submitBtn = document.getElementById('post-submit-btn');

    const title = titleInput?.value.trim() || '';
    const content = contentInput?.value.trim() || '';

    if (!content) return;

    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Publishing...';
    }

    try {
        const data = await window.pipnomicsFetch(`/groups/${groupId}/posts`, {
            method: 'POST',
            body: { title, content }
        });

        if (data.success && data.post) {
            const list = document.getElementById('group-posts-list');
            const empty = document.getElementById('posts-empty-state');
            if (empty) empty.remove();

            const postCard = document.createElement('div');
            postCard.className = 'bg-slate-900 border border-slate-800/80 rounded-xl p-5 shadow-lg space-y-2.5 hover:border-slate-700 transition animate-fade-in';
            postCard.innerHTML = `
                <div class="flex justify-between items-start">
                    <div class="flex items-center space-x-2">
                        <div class="w-7 h-7 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-xs font-bold text-emerald-400 font-mono">
                            ${(data.post.user?.name || 'T')[0]}
                        </div>
                        <div>
                            <div class="flex items-center space-x-1.5">
                                <span class="text-xs font-bold text-white">${escapeHtml(data.post.user?.name || 'Trader')}</span>
                                <span class="w-1.5 h-1.5 rounded-full ${data.post.user?.is_online ? 'bg-emerald-400 shadow-sm shadow-emerald-400 animate-pulse' : 'bg-slate-600'}"></span>
                            </div>
                        </div>
                    </div>
                    <span class="text-[10px] text-slate-500 font-mono">Just now</span>
                </div>
                ${data.post.title ? `<h4 class="text-xs font-bold text-emerald-400 mt-1">${escapeHtml(data.post.title)}</h4>` : ''}
                <p class="text-xs text-slate-300 leading-relaxed whitespace-pre-line">${escapeHtml(data.post.content)}</p>
            `;

            if (list) list.prepend(postCard);

            // Update badge count
            const badge = document.getElementById('group-posts-badge');
            if (badge) {
                const current = parseInt(badge.textContent) || 0;
                badge.textContent = `${current + 1} posts`;
            }

            if (titleInput) titleInput.value = '';
            if (contentInput) contentInput.value = '';

            window.showToast('Post published successfully!', 'success');
        }
    } catch (err) {
        window.showToast(err.data?.error || err.data?.message || 'Failed to publish post.', 'error');
    } finally {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Publish Post';
        }
    }
};

window.submitCreateGroup = async function(e) {
    e.preventDefault();
    const form = document.getElementById('create-group-form');
    const submitBtn = document.getElementById('create-group-submit-btn');
    const nameInput = document.getElementById('group-name');
    const descInput = document.getElementById('group-description');
    const nameError = document.getElementById('group-name-error');

    if (nameError) nameError.classList.add('hidden');
    if (submitBtn) submitBtn.disabled = true;

    try {
        const data = await window.pipnomicsFetch('/groups', {
            method: 'POST',
            body: {
                name: nameInput?.value.trim(),
                description: descInput?.value.trim()
            }
        });

        if (data.success && data.redirect) {
            window.showToast('Forum launched successfully! Redirecting...', 'success');
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 600);
        }
    } catch (err) {
        if (err.data?.errors?.name && nameError) {
            nameError.textContent = err.data.errors.name[0];
            nameError.classList.remove('hidden');
        } else {
            window.showToast(err.data?.message || 'Failed to create forum.', 'error');
        }
        if (submitBtn) submitBtn.disabled = false;
    }
};

// --------------------------------------------------------------------------
// 12. Profile & Auth Forms AJAX Submissions
// --------------------------------------------------------------------------
window.submitProfileInfo = async function(e) {
    e.preventDefault();
    const nameInput = document.getElementById('profile-name');
    const emailInput = document.getElementById('profile-email');
    const nameError = document.getElementById('profile-name-error');
    const emailError = document.getElementById('profile-email-error');
    const statusMsg = document.getElementById('profile-info-status');
    const submitBtn = document.getElementById('profile-info-submit-btn');

    if (nameError) nameError.classList.add('hidden');
    if (emailError) emailError.classList.add('hidden');
    if (submitBtn) submitBtn.disabled = true;

    try {
        const data = await window.pipnomicsFetch('/profile', {
            method: 'PATCH',
            body: {
                name: nameInput?.value.trim(),
                email: emailInput?.value.trim()
            }
        });

        if (data.success) {
            if (statusMsg) {
                statusMsg.classList.remove('hidden');
                setTimeout(() => statusMsg.classList.add('hidden'), 3000);
            }
            window.showToast(data.message || 'Profile updated successfully.', 'success');
        }
    } catch (err) {
        if (err.data?.errors) {
            if (err.data.errors.name && nameError) {
                nameError.textContent = err.data.errors.name[0];
                nameError.classList.remove('hidden');
            }
            if (err.data.errors.email && emailError) {
                emailError.textContent = err.data.errors.email[0];
                emailError.classList.remove('hidden');
            }
        }
    } finally {
        if (submitBtn) submitBtn.disabled = false;
    }
};

window.submitUpdatePassword = async function(e) {
    e.preventDefault();
    const currPass = document.getElementById('update_password_current_password');
    const newPass = document.getElementById('update_password_password');
    const confirmPass = document.getElementById('update_password_password_confirmation');
    const currError = document.getElementById('password-current-error');
    const newError = document.getElementById('password-new-error');
    const statusMsg = document.getElementById('profile-password-status');
    const submitBtn = document.getElementById('profile-password-submit-btn');

    if (currError) currError.classList.add('hidden');
    if (newError) newError.classList.add('hidden');
    if (submitBtn) submitBtn.disabled = true;

    try {
        const data = await window.pipnomicsFetch('/password', {
            method: 'PUT',
            body: {
                current_password: currPass?.value,
                password: newPass?.value,
                password_confirmation: confirmPass?.value
            }
        });

        if (data.success) {
            if (currPass) currPass.value = '';
            if (newPass) newPass.value = '';
            if (confirmPass) confirmPass.value = '';

            if (statusMsg) {
                statusMsg.classList.remove('hidden');
                setTimeout(() => statusMsg.classList.add('hidden'), 3000);
            }
            window.showToast(data.message || 'Password updated successfully.', 'success');
        }
    } catch (err) {
        if (err.data?.errors) {
            if (err.data.errors.current_password && currError) {
                currError.textContent = err.data.errors.current_password[0];
                currError.classList.remove('hidden');
            }
            if (err.data.errors.password && newError) {
                newError.textContent = err.data.errors.password[0];
                newError.classList.remove('hidden');
            }
        }
    } finally {
        if (submitBtn) submitBtn.disabled = false;
    }
};

window.filterActivityLogs = function(category) {
    const rows = document.querySelectorAll('.activity-row');
    ['all', 'auth', 'community'].forEach(cat => {
        const btn = document.getElementById(`activity-tab-${cat}`);
        if (btn) {
            if (cat === category) {
                btn.className = 'px-2.5 py-1 text-xs font-semibold rounded-md transition bg-emerald-500 text-slate-950 font-bold';
            } else {
                btn.className = 'px-2.5 py-1 text-xs font-semibold rounded-md transition text-slate-400 hover:text-white';
            }
        }
    });

    rows.forEach(row => {
        const rowCat = row.getAttribute('data-category');
        if (category === 'all' || rowCat === category) {
            row.classList.remove('hidden');
        } else {
            row.classList.add('hidden');
        }
    });
};

// Auth forms (Login, Register, Password Reset)
window.submitAuthLogin = async function(e) {
    e.preventDefault();
    const email = document.getElementById('login-email')?.value.trim();
    const password = document.getElementById('login-password')?.value;
    const remember = document.getElementById('remember_me')?.checked;
    const emailError = document.getElementById('login-email-error');
    const submitBtn = document.getElementById('login-submit-btn');

    if (emailError) emailError.classList.add('hidden');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span>Signing In...</span>';
    }

    try {
        const data = await window.pipnomicsFetch('/login', {
            method: 'POST',
            body: { email, password, remember }
        });

        if (data.success && data.redirect) {
            window.location.href = data.redirect;
        }
    } catch (err) {
        if (err.data?.errors?.email && emailError) {
            emailError.textContent = err.data.errors.email[0];
            emailError.classList.remove('hidden');
        } else {
            window.showToast('Invalid login credentials.', 'error');
        }
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span>Sign In</span>';
        }
    }
};

window.submitAuthRegister = async function(e) {
    e.preventDefault();
    const name = document.getElementById('register-name')?.value.trim();
    const email = document.getElementById('register-email')?.value.trim();
    const password = document.getElementById('register-password')?.value;
    const password_confirmation = document.getElementById('register-password-confirmation')?.value;
    const nameError = document.getElementById('register-name-error');
    const emailError = document.getElementById('register-email-error');
    const passError = document.getElementById('register-password-error');
    const submitBtn = document.getElementById('register-submit-btn');

    if (nameError) nameError.classList.add('hidden');
    if (emailError) emailError.classList.add('hidden');
    if (passError) passError.classList.add('hidden');
    if (submitBtn) submitBtn.disabled = true;

    try {
        const data = await window.pipnomicsFetch('/register', {
            method: 'POST',
            body: { name, email, password, password_confirmation }
        });

        if (data.success && data.redirect) {
            window.location.href = data.redirect;
        }
    } catch (err) {
        if (err.data?.errors) {
            if (err.data.errors.name && nameError) {
                nameError.textContent = err.data.errors.name[0];
                nameError.classList.remove('hidden');
            }
            if (err.data.errors.email && emailError) {
                emailError.textContent = err.data.errors.email[0];
                emailError.classList.remove('hidden');
            }
            if (err.data.errors.password && passError) {
                passError.textContent = err.data.errors.password[0];
                passError.classList.remove('hidden');
            }
        }
        if (submitBtn) submitBtn.disabled = false;
    }
};

window.submitForgotPassword = async function(e) {
    e.preventDefault();
    const email = document.getElementById('forgot-email')?.value.trim();
    const emailError = document.getElementById('forgot-email-error');
    const submitBtn = document.getElementById('forgot-submit-btn');

    if (emailError) emailError.classList.add('hidden');
    if (submitBtn) submitBtn.disabled = true;

    try {
        const data = await window.pipnomicsFetch('/forgot-password', {
            method: 'POST',
            body: { email }
        });
        if (data.success) {
            window.showToast(data.status || 'Password reset link sent to your email.', 'success');
        }
    } catch (err) {
        if (err.data?.errors?.email && emailError) {
            emailError.textContent = err.data.errors.email[0];
            emailError.classList.remove('hidden');
        }
    } finally {
        if (submitBtn) submitBtn.disabled = false;
    }
};

window.submitResetPassword = async function(e) {
    e.preventDefault();
    const token = document.getElementById('reset-token')?.value;
    const email = document.getElementById('reset-email')?.value.trim();
    const password = document.getElementById('reset-password')?.value;
    const password_confirmation = document.getElementById('reset-password-confirmation')?.value;
    const emailError = document.getElementById('reset-email-error');
    const passError = document.getElementById('reset-password-error');
    const submitBtn = document.getElementById('reset-submit-btn');

    if (emailError) emailError.classList.add('hidden');
    if (passError) passError.classList.add('hidden');
    if (submitBtn) submitBtn.disabled = true;

    try {
        const data = await window.pipnomicsFetch('/reset-password', {
            method: 'POST',
            body: { token, email, password, password_confirmation }
        });
        if (data.success && data.redirect) {
            window.location.href = data.redirect;
        }
    } catch (err) {
        if (err.data?.errors) {
            if (err.data.errors.email && emailError) {
                emailError.textContent = err.data.errors.email[0];
                emailError.classList.remove('hidden');
            }
            if (err.data.errors.password && passError) {
                passError.textContent = err.data.errors.password[0];
                passError.classList.remove('hidden');
            }
        }
        if (submitBtn) submitBtn.disabled = false;
    }
};

// Guest Modal
window.openGuestAuthModal = function(action) {
    const modal = document.getElementById('guest-auth-modal');
    const actionText = document.getElementById('guest-auth-modal-action-text');
    if (actionText && action) {
        actionText.textContent = `You need to log in to ${action} and participate in the community features.`;
    }
    if (modal) modal.classList.remove('hidden');
};

window.closeGuestAuthModal = function() {
    const modal = document.getElementById('guest-auth-modal');
    if (modal) modal.classList.add('hidden');
};

// --------------------------------------------------------------------------
// 13. Trading School Application Form Handling
// --------------------------------------------------------------------------
window.submitSchoolForm = async function(e) {
    e.preventDefault();
    const form = document.getElementById('school-registration-form');
    const submitBtn = document.getElementById('school-submit-btn');
    const submitText = document.getElementById('school-submit-text');
    const successBox = document.getElementById('school-success-message');
    const successDetail = document.getElementById('school-success-detail');

    if (!form) return;

    const formData = new FormData(form);
    const payload = {
        name: formData.get('name'),
        email: formData.get('email'),
        phone: formData.get('phone'),
        experience_level: formData.get('experience_level'),
        preferred_track: formData.get('preferred_track'),
        schedule: formData.get('schedule'),
        goals: formData.get('goals')
    };

    if (submitBtn) {
        submitBtn.disabled = true;
        if (submitText) submitText.textContent = 'Submitting Application...';
    }

    try {
        const data = await window.pipnomicsFetch('/school', {
            method: 'POST',
            body: payload
        });

        if (data.success) {
            form.classList.add('hidden');
            if (successBox) successBox.classList.remove('hidden');
            if (successDetail && data.message) successDetail.textContent = data.message;
            window.showToast('Trading school application submitted successfully!', 'success');
        }
    } catch (err) {
        window.showToast(err.data?.message || 'Failed to submit application. Please check your entries.', 'error');
    } finally {
        if (submitBtn) {
            submitBtn.disabled = false;
            if (submitText) submitText.textContent = 'Enroll & Join Classes';
        }
    }
};

window.resetSchoolForm = function() {
    const form = document.getElementById('school-registration-form');
    const successBox = document.getElementById('school-success-message');
    if (form) {
        form.reset();
        form.classList.remove('hidden');
    }
    if (successBox) {
        successBox.classList.add('hidden');
    }
};

// --------------------------------------------------------------------------
// 13. Helper Functions
// --------------------------------------------------------------------------
function formatTimeAgo(dateString) {
    if (!dateString) return 'Just now';
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMins / 60);

    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return `${diffMins}m ago`;
    if (diffHours < 24) return `${diffHours}h ago`;
    return date.toLocaleDateString([], { month: 'short', day: 'numeric' });
}

function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// --------------------------------------------------------------------------
// 14. Lifecycle Bootstrapping
// --------------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', () => {
    initThemeEngine();
    initNavigation();
    updateSessionClocks();
    setInterval(updateSessionClocks, 1000);
    initTelemetryPolling();

    // Initialize News App if on News Page
    if (window.INITIAL_NEWS_ITEMS) {
        window.newsApp.init(window.INITIAL_NEWS_ITEMS);
    }

    // Initialize Calendar App if on Calendar Page
    if (window.INITIAL_CALENDAR_EVENTS) {
        window.calendarApp.init(window.INITIAL_CALENDAR_EVENTS);
    }
});

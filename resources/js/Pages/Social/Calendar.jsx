import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { statusBadgeClasses, statusFilterChipClasses } from '@/Utils/pillStyles';
import { Head, router } from '@inertiajs/react';
import { useState, useMemo } from 'react';

const MONTH_NAMES = ['January','February','March','April','May','June','July','August','September','October','November','December'];
const DAY_NAMES = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];

const platformStyle = {
    facebook: { bg: 'bg-blue-600', text: 'text-white', label: 'FB', ring: 'ring-blue-400/30' },
    instagram: { bg: 'bg-gradient-to-br from-purple-500 via-pink-500 to-orange-400', text: 'text-white', label: 'IG', ring: 'ring-pink-400/30' },
};

const statusColor = {
    draft: { dot: 'bg-amber-400', bg: 'bg-amber-500/10', text: 'text-amber-600', border: 'border-amber-300/40' },
    scheduled: { dot: 'bg-blue-400', bg: 'bg-blue-500/10', text: 'text-blue-600', border: 'border-blue-300/40' },
    published: { dot: 'bg-emerald-400', bg: 'bg-emerald-500/10', text: 'text-emerald-600', border: 'border-emerald-300/40' },
    failed: { dot: 'bg-rose-400', bg: 'bg-rose-500/10', text: 'text-rose-600', border: 'border-rose-300/40' },
    cancelled: { dot: 'bg-gray-400', bg: 'bg-gray-500/10', text: 'text-gray-500', border: 'border-gray-300/40' },
};

const PlatformBadge = ({ platform }) => {
    const s = platformStyle[platform] || platformStyle.facebook;
    return <span className={`inline-flex h-5 w-7 items-center justify-center rounded text-[10px] font-extrabold ${s.bg} ${s.text}`}>{s.label}</span>;
};

const StatusDot = ({ status }) => {
    const c = statusColor[status] || statusColor.draft;
    return <span className={`inline-block h-2 w-2 rounded-full ${c.dot}`} />;
};

const StatusPill = ({ status }) => {
    const c = statusColor[status] || statusColor.draft;
    return (
        <span className={`inline-flex items-center gap-1.5 rounded-full border px-2.5 py-0.5 text-xs font-semibold capitalize ${statusBadgeClasses(status)}`}>
            <StatusDot status={status} /> {status}
        </span>
    );
};

const MetricCard = ({ icon, label, value }) => (
    <div className="flex items-center gap-2.5 rounded-xl border border-gray-100 bg-gray-50/80 px-3 py-2">
        <span className="text-gray-400">{icon}</span>
        <div>
            <p className="text-xs text-gray-400">{label}</p>
            <p className="text-sm font-bold text-gray-800">{value.toLocaleString()}</p>
        </div>
    </div>
);

function buildCalendarDays(year, month) {
    const firstDay = new Date(year, month - 1, 1);
    const lastDay = new Date(year, month, 0);
    const startDow = firstDay.getDay();
    const totalDays = lastDay.getDate();
    const cells = [];
    for (let i = 0; i < startDow; i++) cells.push(null);
    for (let d = 1; d <= totalDays; d++) cells.push(d);
    while (cells.length % 7 !== 0) cells.push(null);
    return cells;
}

function todayStr() {
    const d = new Date();
    return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
}

export default function SocialCalendar({ auth, posts, month, year, status, statuses }) {
    const [selectedPost, setSelectedPost] = useState(null);
    const today = todayStr();
    const cells = useMemo(() => buildCalendarDays(year, month), [year, month]);

    const postsByDate = useMemo(() => {
        const map = {};
        posts.forEach((p) => {
            if (!p.scheduled_date) return;
            if (!map[p.scheduled_date]) map[p.scheduled_date] = [];
            map[p.scheduled_date].push(p);
        });
        return map;
    }, [posts]);

    const navigate = (dir) => {
        let m = month + dir;
        let y = year;
        if (m < 1) { m = 12; y--; }
        if (m > 12) { m = 1; y++; }
        const params = { month: m, year: y };
        if (status) params.status = status;
        router.get('/social/calendar', params, { preserveState: true, preserveScroll: true, replace: true });
    };

    const goToday = () => {
        const now = new Date();
        const params = { month: now.getMonth() + 1, year: now.getFullYear() };
        if (status) params.status = status;
        router.get('/social/calendar', params, { preserveState: true, preserveScroll: true, replace: true });
    };

    const filterStatus = (s) => {
        const params = { month, year };
        if (s) params.status = s;
        router.get('/social/calendar', params, { preserveState: true, preserveScroll: true, replace: true });
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div className="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h2 className="text-xl font-bold text-gray-950">Social Calendar</h2>
                        <p className="mt-1 text-sm text-gray-500">Scheduled posts and engagement metrics across platforms.</p>
                    </div>
                    <div className="flex items-center gap-2">
                        <span className="mr-2 text-xs font-semibold text-gray-400">{posts.length} posts</span>
                        <button onClick={goToday} className="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-600 shadow-sm transition hover:bg-gray-50">Today</button>
                    </div>
                </div>
            }
        >
            <Head title="Social Calendar" />

            <div className="py-8">
                <div className="space-y-5">

                    {/* Controls bar */}
                    <div className="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-gray-200/80 bg-white p-4 shadow-sm">
                        {/* Month navigation */}
                        <div className="flex items-center gap-3">
                            <button onClick={() => navigate(-1)} className="flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-500 shadow-sm transition hover:bg-gray-50 hover:text-gray-800">
                                <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2.5}><path strokeLinecap="round" strokeLinejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
                            </button>
                            <h3 className="min-w-[180px] text-center text-lg font-bold text-gray-900">
                                {MONTH_NAMES[month - 1]} {year}
                            </h3>
                            <button onClick={() => navigate(1)} className="flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-500 shadow-sm transition hover:bg-gray-50 hover:text-gray-800">
                                <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2.5}><path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                            </button>
                        </div>

                        {/* Status filter pills */}
                        <div className="flex flex-wrap items-center gap-2">
                            <button onClick={() => filterStatus('')}
                                className={`rounded-full border px-3 py-1 text-xs font-semibold transition ${statusFilterChipClasses('', !status)}`}>
                                All
                            </button>
                            {statuses.map((s) => {
                                const active = status === s;
                                return (
                                    <button key={s} onClick={() => filterStatus(s)}
                                        className={`inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold capitalize transition ${statusFilterChipClasses(s, active)}`}>
                                        <StatusDot status={s} /> {s}
                                    </button>
                                );
                            })}
                        </div>
                    </div>

                    {/* Calendar grid */}
                    <div className="overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-sm">
                        {/* Day headers */}
                        <div className="grid grid-cols-7 border-b border-gray-100 bg-gray-50/80">
                            {DAY_NAMES.map((d) => (
                                <div key={d} className="px-2 py-3 text-center text-xs font-bold uppercase tracking-wider text-gray-400">{d}</div>
                            ))}
                        </div>

                        {/* Date cells */}
                        <div className="grid grid-cols-7">
                            {cells.map((day, idx) => {
                                if (day === null) {
                                    return <div key={`e-${idx}`} className="min-h-[120px] border-b border-r border-gray-50 bg-gray-50/30" />;
                                }
                                const dateStr = `${year}-${String(month).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
                                const isToday = dateStr === today;
                                const dayPosts = postsByDate[dateStr] || [];

                                return (
                                    <div key={dateStr}
                                        className={`relative min-h-[120px] border-b border-r border-gray-100 p-1.5 transition-colors ${isToday ? 'bg-blue-50/40' : 'hover:bg-gray-50/60'}`}>
                                        <div className="mb-1 flex items-center justify-between px-1">
                                            <span className={`inline-flex h-6 w-6 items-center justify-center rounded-full text-xs font-bold ${isToday ? 'bg-blue-600 text-white' : 'text-gray-500'}`}>
                                                {day}
                                            </span>
                                            {dayPosts.length > 0 && (
                                                <span className="text-[10px] font-semibold text-gray-400">{dayPosts.length}</span>
                                            )}
                                        </div>
                                        <div className="space-y-1">
                                            {dayPosts.slice(0, 3).map((post) => {
                                                const sc = statusColor[post.status] || statusColor.draft;
                                                return (
                                                    <button key={post.id} onClick={() => setSelectedPost(post)}
                                                        className={`group flex w-full items-center gap-1.5 rounded-lg border px-1.5 py-1 text-left transition hover:shadow-sm ${sc.border} ${sc.bg}`}>
                                                        <PlatformBadge platform={post.platform} />
                                                        <span className={`truncate text-[11px] font-medium ${sc.text}`}>
                                                            {post.content_short}
                                                        </span>
                                                    </button>
                                                );
                                            })}
                                            {dayPosts.length > 3 && (
                                                <p className="px-1 text-[10px] font-semibold text-gray-400">+{dayPosts.length - 3} more</p>
                                            )}
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    </div>

                    {/* Mobile list fallback */}
                    <div className="block sm:hidden space-y-2">
                        {posts.length === 0 && (
                            <div className="rounded-2xl border border-gray-200/80 bg-white px-5 py-12 text-center text-sm text-gray-400">
                                No posts scheduled for this month.
                            </div>
                        )}
                        {posts.map((post) => (
                            <button key={post.id} onClick={() => setSelectedPost(post)}
                                className="flex w-full items-center gap-3 rounded-xl border border-gray-200/80 bg-white p-3 text-left shadow-sm transition hover:bg-gray-50">
                                <PlatformBadge platform={post.platform} />
                                <div className="min-w-0 flex-1">
                                    <p className="truncate text-sm font-medium text-gray-800">{post.content_short}</p>
                                    <p className="text-xs text-gray-400">{post.scheduled_date} · {post.scheduled_time}</p>
                                </div>
                                <StatusPill status={post.status} />
                            </button>
                        ))}
                    </div>
                </div>
            </div>

            {/* Post detail slide-over */}
            {selectedPost && (
                <div className="fixed inset-0 z-50 flex justify-end" onClick={() => setSelectedPost(null)}>
                    <div className="absolute inset-0 bg-gray-950/40 backdrop-blur-sm" />
                    <div className="relative ml-auto flex h-full w-full max-w-md flex-col bg-white shadow-2xl"
                         onClick={(e) => e.stopPropagation()}
                         style={{ animation: 'slideIn 0.25s ease-out' }}>

                        {/* Header */}
                        <div className="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                            <div className="flex items-center gap-3">
                                <PlatformBadge platform={selectedPost.platform} />
                                <StatusPill status={selectedPost.status} />
                            </div>
                            <button onClick={() => setSelectedPost(null)}
                                className="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-gray-100 hover:text-gray-600">
                                <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}><path strokeLinecap="round" strokeLinejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        {/* Content */}
                        <div className="flex-1 overflow-y-auto px-6 py-5 space-y-5">
                            {/* Schedule info */}
                            <div className="rounded-xl border border-gray-100 bg-gray-50/80 p-4">
                                <p className="text-xs font-bold uppercase tracking-wider text-gray-400">Scheduled</p>
                                <p className="mt-1 text-sm font-semibold text-gray-800">
                                    {selectedPost.scheduled_date} at {selectedPost.scheduled_time}
                                </p>
                                {selectedPost.published_at && (
                                    <>
                                        <p className="mt-3 text-xs font-bold uppercase tracking-wider text-gray-400">Published</p>
                                        <p className="mt-1 text-sm font-semibold text-emerald-700">{selectedPost.published_at}</p>
                                    </>
                                )}
                            </div>

                            {/* Post content */}
                            <div>
                                <p className="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Content</p>
                                <div className="rounded-xl border border-gray-100 bg-gray-50/50 p-4">
                                    <p className="whitespace-pre-wrap text-sm leading-relaxed text-gray-700">
                                        {selectedPost.content}
                                    </p>
                                </div>
                            </div>

                            {/* Failure reason */}
                            {selectedPost.failure_reason && (
                                <div className="rounded-xl border border-rose-200 bg-rose-50 p-4">
                                    <p className="text-xs font-bold uppercase tracking-wider text-rose-400 mb-1">Failure Reason</p>
                                    <p className="text-sm text-rose-700">{selectedPost.failure_reason}</p>
                                </div>
                            )}

                            {/* Engagement metrics */}
                            <div>
                                <p className="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Engagement</p>
                                <div className="grid grid-cols-2 gap-2">
                                    <MetricCard label="Reach" value={selectedPost.reach}
                                        icon={<svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}><path strokeLinecap="round" strokeLinejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path strokeLinecap="round" strokeLinejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>} />
                                    <MetricCard label="Clicks" value={selectedPost.clicks}
                                        icon={<svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}><path strokeLinecap="round" strokeLinejoin="round" d="M15.042 21.672 13.684 16.6m0 0-2.51 2.225.569-9.47 5.227 7.917-3.286-.672ZM12 2.25V4.5m5.834.166-1.591 1.591M20.25 10.5H18M7.757 14.743l-1.59 1.591M6 10.5H3.75m4.007-4.243-1.59-1.591" /></svg>} />
                                    <MetricCard label="Likes" value={selectedPost.likes}
                                        icon={<svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}><path strokeLinecap="round" strokeLinejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" /></svg>} />
                                    <MetricCard label="Comments" value={selectedPost.comments}
                                        icon={<svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}><path strokeLinecap="round" strokeLinejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" /></svg>} />
                                    <MetricCard label="Shares" value={selectedPost.shares}
                                        icon={<svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}><path strokeLinecap="round" strokeLinejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" /></svg>} />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            <style>{`
                @keyframes slideIn {
                    from { transform: translateX(100%); }
                    to { transform: translateX(0); }
                }
            `}</style>
        </AuthenticatedLayout>
    );
}

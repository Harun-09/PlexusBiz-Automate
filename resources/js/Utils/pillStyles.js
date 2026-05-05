const STATUS_BADGE_CLASSES = {
    active: 'border-emerald-200 bg-emerald-50 text-emerald-700',
    approved: 'border-emerald-200 bg-emerald-50 text-emerald-700',
    confirmed: 'border-emerald-200 bg-emerald-50 text-emerald-700',
    completed: 'border-emerald-200 bg-emerald-50 text-emerald-700',
    success: 'border-emerald-200 bg-emerald-50 text-emerald-700',
    published: 'border-emerald-200 bg-emerald-50 text-emerald-700',
    paid: 'border-emerald-200 bg-emerald-50 text-emerald-700',
    resolved: 'border-emerald-200 bg-emerald-50 text-emerald-700',
    closed: 'border-emerald-200 bg-emerald-50 text-emerald-700',
    blocked: 'border-rose-200 bg-rose-50 text-rose-700',
    failed: 'border-rose-200 bg-rose-50 text-rose-700',
    cancelled: 'border-rose-200 bg-rose-50 text-rose-700',
    rejected: 'border-rose-200 bg-rose-50 text-rose-700',
    suspended: 'border-rose-200 bg-rose-50 text-rose-700',
    inactive: 'border-slate-200 bg-slate-50 text-slate-600',
    skipped: 'border-slate-200 bg-slate-50 text-slate-600',
    pending: 'border-amber-200 bg-amber-50 text-amber-700',
    processing: 'border-amber-200 bg-amber-50 text-amber-700',
    scheduled: 'border-amber-200 bg-amber-50 text-amber-700',
    running: 'border-amber-200 bg-amber-50 text-amber-700',
    draft: 'border-amber-200 bg-amber-50 text-amber-700',
    waiting_supplier: 'border-amber-200 bg-amber-50 text-amber-700',
    open: 'border-amber-200 bg-amber-50 text-amber-700',
};

const STATUS_FILTER_CLASSES = {
    active: {
        active: 'border-emerald-600 bg-emerald-600 text-white shadow-sm',
        inactive: 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100',
    },
    blocked: {
        active: 'border-rose-600 bg-rose-600 text-white shadow-sm',
        inactive: 'border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100',
    },
    pending: {
        active: 'border-amber-500 bg-amber-500 text-slate-950 shadow-sm',
        inactive: 'border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100',
    },
    inactive: {
        active: 'border-slate-600 bg-slate-600 text-white shadow-sm',
        inactive: 'border-slate-200 bg-slate-50 text-slate-600 hover:bg-slate-100',
    },
    neutral: {
        active: 'border-slate-950 bg-slate-950 text-white shadow-sm',
        inactive: 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50',
    },
};

const normalize = (value) => String(value || '').toLowerCase().replace(/[\s-]+/g, '_');

export function statusBadgeClasses(status) {
    const normalized = normalize(status);
    return STATUS_BADGE_CLASSES[normalized] || 'border-slate-200 bg-slate-50 text-slate-700';
}

export function statusFilterChipClasses(status, active) {
    const normalized = normalize(status);

    if (!normalized || normalized === 'all') {
        return active
            ? STATUS_FILTER_CLASSES.neutral.active
            : STATUS_FILTER_CLASSES.neutral.inactive;
    }

    if (['active', 'approved', 'confirmed', 'completed', 'success', 'published', 'paid', 'resolved', 'closed'].includes(normalized)) {
        return active
            ? STATUS_FILTER_CLASSES.active.active
            : STATUS_FILTER_CLASSES.active.inactive;
    }

    if (['blocked', 'failed', 'cancelled', 'rejected', 'suspended'].includes(normalized)) {
        return active
            ? STATUS_FILTER_CLASSES.blocked.active
            : STATUS_FILTER_CLASSES.blocked.inactive;
    }

    if (['pending', 'processing', 'scheduled', 'running', 'draft', 'waiting_supplier', 'open'].includes(normalized)) {
        return active
            ? STATUS_FILTER_CLASSES.pending.active
            : STATUS_FILTER_CLASSES.pending.inactive;
    }

    if (['inactive', 'skipped', 'disabled'].includes(normalized)) {
        return active
            ? STATUS_FILTER_CLASSES.inactive.active
            : STATUS_FILTER_CLASSES.inactive.inactive;
    }

    return active
        ? STATUS_FILTER_CLASSES.neutral.active
        : STATUS_FILTER_CLASSES.neutral.inactive;
}

const SELECTION_PILL_PALETTES = {
    neutral: {
        active: 'border-slate-950 bg-slate-950 text-white shadow-sm',
        inactive: 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50',
    },
    brand: {
        active: 'border-[#0b2e71] bg-[#0b2e71] text-white shadow-sm',
        inactive: 'border-[#d7e3f4] bg-white text-[#0b2e71] hover:border-[#4f7fe0] hover:text-[#2953b1]',
    },
    brandInverse: {
        active: 'border-white bg-white text-[#0b2e71] shadow-sm',
        inactive: 'border-white/20 bg-white/10 text-white hover:bg-white/15',
    },
};

export function selectionPillClasses(active, palette = 'neutral') {
    const resolved = typeof palette === 'string' ? (SELECTION_PILL_PALETTES[palette] || SELECTION_PILL_PALETTES.neutral) : palette;
    return active ? resolved.active : resolved.inactive;
}

const ACTION_BUTTON_VARIANTS = {
    primary: 'border-[#0b2e71] bg-[#0b2e71] text-white shadow-sm hover:bg-[#092752] focus:ring-[#0b2e71]',
    secondary: 'border-[#d7e3f4] bg-white text-[#0b2e71] shadow-sm hover:border-[#4f7fe0] hover:bg-[#eef5ff] focus:ring-[#4f7fe0]',
    neutral: 'border-slate-200 bg-white text-slate-700 shadow-sm hover:border-slate-300 hover:bg-slate-50 focus:ring-slate-500',
    success: 'border-emerald-200 bg-emerald-50 text-emerald-700 shadow-sm hover:border-emerald-300 hover:bg-emerald-100 focus:ring-emerald-500',
    danger: 'border-rose-200 bg-rose-50 text-rose-700 shadow-sm hover:border-rose-300 hover:bg-rose-100 focus:ring-rose-500',
};

export function actionButtonClasses(variant = 'neutral') {
    return ACTION_BUTTON_VARIANTS[variant] || ACTION_BUTTON_VARIANTS.neutral;
}

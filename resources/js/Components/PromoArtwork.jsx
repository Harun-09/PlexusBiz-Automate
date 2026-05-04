const variantStyles = {
    hero: {
        shell: 'bg-[radial-gradient(circle_at_22%_18%,rgba(255,255,255,0.16),transparent_30%),radial-gradient(circle_at_78%_18%,rgba(255,138,0,0.24),transparent_26%),linear-gradient(135deg,#060e2a_0%,#0b2e71_52%,#1f68d9_100%)]',
        aura: 'bg-cyan-300/20',
        accent: 'bg-[#ff8a00]',
        inner: 'bg-[linear-gradient(145deg,#07102a_0%,#0b3d91_48%,#1f68d9_100%)]',
    },
    banner: {
        shell: 'bg-[radial-gradient(circle_at_18%_20%,rgba(255,255,255,0.22),transparent_26%),radial-gradient(circle_at_84%_20%,rgba(255,184,73,0.24),transparent_24%),linear-gradient(135deg,#0c2568_0%,#1144a8_55%,#3e7de6_100%)]',
        aura: 'bg-amber-300/20',
        accent: 'bg-[#ffd059]',
        inner: 'bg-[linear-gradient(160deg,#0c2a73_0%,#1555b7_45%,#1f68d9_100%)]',
    },
    gamingBanner: {
        shell: 'bg-[radial-gradient(circle_at_16%_18%,rgba(255,255,255,0.16),transparent_26%),radial-gradient(circle_at_82%_20%,rgba(255,91,242,0.22),transparent_22%),radial-gradient(circle_at_70%_80%,rgba(31,104,217,0.2),transparent_26%),linear-gradient(135deg,#050816_0%,#100f33_42%,#3412a3_75%,#6f2bda_100%)]',
        aura: 'bg-fuchsia-300/20',
        accent: 'bg-[#ff8a00]',
        inner: 'bg-[linear-gradient(160deg,#060b1d_0%,#111c4f_45%,#3516ad_100%)]',
    },
    categoryTile: {
        shell: 'bg-[radial-gradient(circle_at_18%_18%,rgba(255,255,255,0.74),transparent_24%),radial-gradient(circle_at_82%_18%,rgba(31,104,217,0.12),transparent_22%),linear-gradient(180deg,#fbfdff_0%,#ecf3ff_60%,#d8e4f7_100%)]',
        aura: 'bg-[#1f68d9]/8',
        accent: 'bg-[#ff8a00]',
        inner: 'bg-white',
    },
    smartHome: {
        shell: 'bg-[radial-gradient(circle_at_16%_18%,rgba(255,255,255,0.25),transparent_24%),radial-gradient(circle_at_78%_22%,rgba(255,178,89,0.3),transparent_24%),radial-gradient(circle_at_72%_82%,rgba(74,44,23,0.26),transparent_26%),linear-gradient(135deg,#f3dcc9_0%,#e6c3a3_34%,#bc835d_72%,#7b4a2b_100%)]',
        aura: 'bg-white/20',
        accent: 'bg-[#ff8a00]',
        inner: 'bg-[linear-gradient(160deg,#f5e1cf_0%,#e2b893_42%,#ad6b42_100%)]',
    },
    memory: {
        shell: 'bg-[radial-gradient(circle_at_18%_18%,rgba(255,255,255,0.12),transparent_28%),radial-gradient(circle_at_82%_22%,rgba(255,64,64,0.16),transparent_24%),radial-gradient(circle_at_70%_84%,rgba(31,104,217,0.24),transparent_24%),linear-gradient(135deg,#05070f_0%,#081433_48%,#0f4bb0_100%)]',
        aura: 'bg-cyan-300/16',
        accent: 'bg-[#ff1f1f]',
        inner: 'bg-[linear-gradient(160deg,#060b1d_0%,#0b2e71_45%,#1f68d9_100%)]',
    },
    strip: {
        shell: 'bg-[radial-gradient(circle_at_16%_18%,rgba(11,61,145,0.14),transparent_24%),radial-gradient(circle_at_82%_18%,rgba(255,138,0,0.18),transparent_24%),linear-gradient(135deg,#f8fbff_0%,#eef4ff_52%,#d9e6fb_100%)]',
        aura: 'bg-[#0b3d91]/10',
        accent: 'bg-[#0b3d91]',
        inner: 'bg-[linear-gradient(160deg,#ffffff_0%,#eef4ff_45%,#dbe7fb_100%)]',
    },
    tile: {
        shell: 'bg-[radial-gradient(circle_at_22%_20%,rgba(27,61,165,0.14),transparent_24%),radial-gradient(circle_at_82%_18%,rgba(255,138,0,0.14),transparent_22%),linear-gradient(135deg,#f3f7ff_0%,#dfeaff_54%,#bfd3f6_100%)]',
        aura: 'bg-[#1f68d9]/10',
        accent: 'bg-[#1f68d9]',
        inner: 'bg-[linear-gradient(160deg,#ffffff_0%,#eef4ff_46%,#d8e5fb_100%)]',
    },
};

function renderGamingBannerScene() {
    return (
        <>
            <div className="absolute inset-0 bg-[radial-gradient(circle_at_16%_18%,rgba(255,255,255,0.14),transparent_24%),radial-gradient(circle_at_82%_20%,rgba(255,91,242,0.16),transparent_22%),radial-gradient(circle_at_72%_80%,rgba(31,104,217,0.18),transparent_26%),linear-gradient(135deg,rgba(10,11,29,0.2),rgba(18,18,56,0.05))]" />
            <div className="absolute left-6 top-7 h-[66%] w-[50%] rounded-[28px] border border-white/16 bg-white/6 p-3 backdrop-blur">
                <div className="relative h-full overflow-hidden rounded-[20px] bg-[linear-gradient(180deg,#060b1d_0%,#0b2e71_52%,#1f68d9_100%)] shadow-[0_20px_46px_-28px_rgba(0,0,0,0.7)]">
                    <div className="absolute inset-0 bg-[radial-gradient(circle_at_20%_22%,rgba(255,255,255,0.3),transparent_18%),radial-gradient(circle_at_70%_18%,rgba(255,138,0,0.26),transparent_16%),radial-gradient(circle_at_72%_78%,rgba(255,255,255,0.12),transparent_18%)] opacity-90" />
                    <div className="absolute left-4 top-4 h-4 w-20 rounded-full bg-white/14" />
                    <div className="absolute left-4 top-11 h-3 w-28 rounded-full bg-white/18" />
                    <div className="absolute right-4 bottom-4 h-10 w-24 rounded-[12px] border border-white/20 bg-white/10" />
                </div>
            </div>

            <div className="absolute left-[40%] bottom-8 h-20 w-[34%] rounded-[28px] border border-white/16 bg-white/8 p-3 backdrop-blur">
                <div className="relative h-full overflow-hidden rounded-[18px] bg-[linear-gradient(135deg,#0b122e_0%,#21125c_55%,#ff8a00_120%)] shadow-[0_18px_36px_-26px_rgba(0,0,0,0.72)]">
                    <div className="absolute left-4 top-4 h-3 w-20 rounded-full bg-white/20" />
                    <div className="absolute left-4 top-9 h-2 w-32 rounded-full bg-white/12" />
                    <div className="absolute right-4 bottom-4 h-6 w-10 rounded-[10px] bg-white/16" />
                </div>
            </div>

            <div className="absolute right-6 top-7 h-[70%] w-[28%] rounded-[26px] border border-white/18 bg-white/8 p-3 backdrop-blur">
                <div className="relative h-full rounded-[18px] bg-[linear-gradient(180deg,#090d1f_0%,#13184a_45%,#4f27d7_100%)] shadow-[0_20px_48px_-28px_rgba(0,0,0,0.72)]">
                    <div className="absolute inset-0 bg-[linear-gradient(135deg,rgba(255,255,255,0.08),rgba(255,255,255,0))]" />
                    <div className="absolute left-1/2 top-1/2 h-20 w-12 -translate-x-1/2 -translate-y-1/2 rounded-[18px] bg-[linear-gradient(180deg,#1a1d3d_0%,#040712_100%)] shadow-[0_16px_30px_-22px_rgba(0,0,0,0.78)]">
                        <div className="absolute inset-3 rounded-[12px] bg-[radial-gradient(circle_at_50%_45%,rgba(255,138,0,0.28),transparent_20%),linear-gradient(180deg,#0b2e71_0%,#1f68d9_100%)]" />
                    </div>
                    <div className="absolute left-4 bottom-4 h-2 w-14 rounded-full bg-white/15" />
                </div>
            </div>

            <div className="absolute bottom-6 left-6 right-6 flex items-center justify-between gap-4">
                <div className="inline-flex items-center gap-2 rounded-full border border-white/18 bg-white/10 px-3 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-[#ffddb6] backdrop-blur">
                    PC GAMING WEEK EXTENDED
                </div>
                <div className="inline-flex items-center gap-2 rounded-full border border-white/18 bg-white/10 px-3 py-1 text-[10px] font-black uppercase tracking-[0.18em] text-[#ffddb6] backdrop-blur">
                    Shop now
                </div>
            </div>

            <div className="absolute right-6 top-10 max-w-[330px] text-right text-white [text-shadow:0_3px_12px_rgba(8,10,28,0.55)]">
                <h3 className="text-3xl font-black leading-[0.92] tracking-[-0.08em] sm:text-4xl lg:text-[3.25rem]">
                    LEVEL UP YOUR GAME
                </h3>
                <p className="mt-3 text-sm font-semibold text-white/90 sm:text-base">
                    Power. Precision. Performance.
                </p>
            </div>
        </>
    );
}

function renderCategoryTileScene(scene = '') {
    const key = scene.toLowerCase();
    const base = (
        <>
            <div className="absolute inset-0 bg-[radial-gradient(circle_at_18%_18%,rgba(255,255,255,0.78),transparent_24%),radial-gradient(circle_at_82%_18%,rgba(31,104,217,0.12),transparent_22%),linear-gradient(180deg,#fbfdff_0%,#eef4ff_62%,#dbe6f7_100%)]" />
            <div className="absolute inset-x-4 top-4 h-5 rounded-full bg-white/70 shadow-[0_10px_24px_-16px_rgba(15,23,42,0.38)]" />
            <div className="absolute inset-x-4 bottom-4 h-1.5 rounded-full bg-[#dbe7fb]" />
        </>
    );

    if (key.includes('cpu')) {
        return (
            <>
                {base}
                <div className="absolute left-5 top-10 h-24 w-24 rounded-[18px] bg-[linear-gradient(180deg,#163b95_0%,#0b2e71_100%)] shadow-[0_18px_34px_-22px_rgba(15,23,42,0.62)]">
                    <div className="absolute inset-3 rounded-[14px] border border-white/10 bg-[linear-gradient(180deg,#2c57bf_0%,#123a8a_100%)]" />
                    <div className="absolute right-4 bottom-4 h-6 w-10 rounded-[10px] bg-[#ff8a00]" />
                </div>
                <div className="absolute right-5 top-12 h-28 w-24 rounded-[18px] border border-slate-200 bg-white shadow-[0_16px_30px_-22px_rgba(15,23,42,0.45)]">
                    <div className="absolute inset-3 rounded-[12px] bg-[linear-gradient(180deg,#f9fbff_0%,#dfe9ff_100%)]" />
                    <div className="absolute left-4 top-4 h-3 w-12 rounded-full bg-[#0b3d91]/15" />
                    <div className="absolute left-4 top-9 h-12 w-12 rounded-[12px] border border-[#0b3d91]/10 bg-[linear-gradient(180deg,#ffcf30_0%,#ff8a00_100%)]" />
                </div>
            </>
        );
    }

    if (key.includes('graphics') || key.includes('video card') || key.includes('gpu') || key.includes('card')) {
        return (
            <>
                {base}
                <div className="absolute left-5 top-11 h-24 w-36 rounded-[22px] bg-[linear-gradient(180deg,#09111f_0%,#141c38_100%)] shadow-[0_20px_36px_-24px_rgba(15,23,42,0.72)]">
                    <div className="absolute left-4 top-4 h-16 w-16 rounded-full border border-white/8 bg-[radial-gradient(circle_at_45%_45%,rgba(255,138,0,0.4),transparent_18%),radial-gradient(circle_at_54%_54%,rgba(255,255,255,0.18),transparent_24%),#1b2243]" />
                    <div className="absolute right-3 top-5 h-14 w-14 rounded-full border border-white/8 bg-[radial-gradient(circle_at_46%_46%,rgba(255,91,242,0.34),transparent_18%),#1b2243]" />
                    <div className="absolute bottom-3 left-4 h-2 w-12 rounded-full bg-[#ff8a00]/70" />
                </div>
                <div className="absolute right-5 top-10 h-28 w-20 rounded-[18px] border border-slate-200 bg-white shadow-[0_16px_30px_-22px_rgba(15,23,42,0.45)]">
                    <div className="absolute inset-3 rounded-[12px] bg-[linear-gradient(180deg,#eef4ff_0%,#cfe1ff_100%)]" />
                    <div className="absolute left-4 top-4 h-4 w-6 rounded-full bg-[#0b3d91]/20" />
                </div>
            </>
        );
    }

    if (key.includes('ssd') || key.includes('flash')) {
        return (
            <>
                {base}
                <div className="absolute left-6 top-12 h-14 w-28 rounded-[18px] bg-[linear-gradient(180deg,#0f172a_0%,#1f2937_100%)] shadow-[0_16px_32px_-24px_rgba(15,23,42,0.7)]">
                    <div className="absolute inset-3 rounded-[12px] bg-[linear-gradient(180deg,#0b3d91_0%,#1f68d9_100%)]" />
                    <div className="absolute right-3 top-3 h-3 w-3 rounded-full bg-[#ff8a00]" />
                </div>
                <div className="absolute right-6 top-10 h-28 w-20 rounded-[18px] border border-slate-200 bg-white shadow-[0_16px_30px_-22px_rgba(15,23,42,0.45)]">
                    <div className="absolute inset-3 rounded-[12px] bg-[linear-gradient(180deg,#eaf2ff_0%,#d6e2fb_100%)]" />
                    <div className="absolute bottom-3 left-3 h-2 w-10 rounded-full bg-[#0b3d91]/18" />
                </div>
            </>
        );
    }

    if (key.includes('hard drive') || key.includes('storage')) {
        return (
            <>
                {base}
                <div className="absolute left-5 top-10 h-28 w-[5.5rem] rounded-[18px] bg-[linear-gradient(180deg,#be123c_0%,#e11d48_45%,#9f1239_100%)] shadow-[0_16px_32px_-22px_rgba(190,18,60,0.55)]">
                    <div className="absolute inset-3 rounded-[12px] border border-white/10 bg-white/12" />
                </div>
                <div className="absolute right-6 top-12 h-24 w-24 rounded-[18px] bg-[linear-gradient(180deg,#14532d_0%,#22c55e_100%)] shadow-[0_16px_32px_-22px_rgba(20,83,45,0.5)]">
                    <div className="absolute inset-3 rounded-[12px] border border-white/10 bg-white/12" />
                </div>
            </>
        );
    }

    if (key.includes('tv') || key.includes('video')) {
        return (
            <>
                {base}
                <div className="absolute left-5 top-10 h-24 w-36 rounded-[18px] bg-[linear-gradient(180deg,#0f172a_0%,#1f68d9_100%)] shadow-[0_16px_30px_-22px_rgba(15,23,42,0.48)]">
                    <div className="absolute inset-3 rounded-[12px] bg-[linear-gradient(135deg,#f8fbff_0%,#cfe1ff_100%)]" />
                    <div className="absolute left-5 bottom-4 h-2 w-14 rounded-full bg-[#0b3d91]/20" />
                </div>
                <div className="absolute right-6 top-12 h-20 w-12 rounded-[12px] bg-[linear-gradient(180deg,#0b3d91_0%,#1f68d9_100%)] shadow-[0_16px_30px_-22px_rgba(15,23,42,0.45)]" />
                <div className="absolute right-12 top-8 h-4 w-4 rounded-full bg-[#ff8a00]" />
            </>
        );
    }

    if (key.includes('gift')) {
        return (
            <>
                {base}
                <div className="absolute left-6 top-11 h-20 w-28 rounded-[18px] bg-[linear-gradient(135deg,#0b3d91_0%,#1f68d9_100%)] shadow-[0_16px_30px_-22px_rgba(15,23,42,0.45)]" />
                <div className="absolute left-12 top-8 h-20 w-28 rounded-[18px] bg-[linear-gradient(135deg,#ffcf30_0%,#ff8a00_100%)] shadow-[0_16px_30px_-22px_rgba(255,138,0,0.45)]" />
                <div className="absolute right-6 top-12 h-18 w-24 rounded-[16px] bg-[linear-gradient(135deg,#f4f7ff_0%,#dbe7fb_100%)] shadow-[0_16px_30px_-22px_rgba(15,23,42,0.2)]" />
            </>
        );
    }

    if (key.includes('audio')) {
        return (
            <>
                {base}
                <div className="absolute left-6 top-10 h-24 w-20 rounded-[18px] bg-[linear-gradient(180deg,#fafafa_0%,#d9e4f3_100%)] shadow-[0_16px_30px_-22px_rgba(15,23,42,0.22)]">
                    <div className="absolute inset-3 rounded-full border border-slate-200 bg-[radial-gradient(circle_at_50%_50%,#cfd8e8_0%,#eef4ff_58%,#dfe8f7_100%)]" />
                </div>
                <div className="absolute right-6 top-16 h-12 w-28 rounded-[16px] bg-[linear-gradient(180deg,#0f172a_0%,#1f2937_100%)] shadow-[0_16px_30px_-22px_rgba(15,23,42,0.55)]" />
            </>
        );
    }

    if (key.includes('kitchen') || key.includes('appliance')) {
        return (
            <>
                {base}
                <div className="absolute left-8 top-9 h-28 w-24 rounded-[22px] bg-[linear-gradient(180deg,#f8fafc_0%,#dfe7f4_100%)] shadow-[0_16px_32px_-22px_rgba(15,23,42,0.22)]">
                    <div className="absolute inset-3 rounded-[18px] bg-[linear-gradient(180deg,#1f2937_0%,#111827_100%)]" />
                    <div className="absolute right-3 top-3 h-4 w-4 rounded-full bg-[#ff8a00]" />
                    <div className="absolute inset-x-5 bottom-4 h-4 rounded-[10px] bg-[#ffcf30]" />
                </div>
            </>
        );
    }

    if (key.includes('camera')) {
        return (
            <>
                {base}
                <div className="absolute left-6 top-10 h-[6.5rem] w-[5.5rem] rounded-[18px] bg-[linear-gradient(180deg,#0f172a_0%,#111827_100%)] shadow-[0_16px_32px_-22px_rgba(15,23,42,0.55)]">
                    <div className="absolute left-1/2 top-1/2 h-14 w-14 -translate-x-1/2 -translate-y-1/2 rounded-full bg-[radial-gradient(circle_at_50%_50%,#1f68d9_0%,#0b3d91_46%,#030712_100%)]" />
                    <div className="absolute bottom-3 left-1/2 h-2 w-8 -translate-x-1/2 rounded-full bg-[#ff8a00]" />
                </div>
                <div className="absolute right-6 top-12 h-20 w-20 rounded-full bg-[linear-gradient(180deg,#eaf2ff_0%,#cfe1ff_100%)] shadow-[0_16px_32px_-22px_rgba(15,23,42,0.2)]">
                    <div className="absolute inset-3 rounded-full border border-[#0b3d91]/10 bg-white" />
                </div>
            </>
        );
    }

    if (key.includes('wireless') || key.includes('network')) {
        return (
            <>
                {base}
                <div className="absolute left-6 top-12 h-20 w-[7.5rem] rounded-[18px] bg-[linear-gradient(180deg,#f8fafc_0%,#dbe7fb_100%)] shadow-[0_16px_32px_-22px_rgba(15,23,42,0.2)]">
                    <div className="absolute inset-x-5 top-4 h-8 rounded-[12px] bg-[linear-gradient(180deg,#0b3d91_0%,#1f68d9_100%)]" />
                    <div className="absolute left-6 top-[-10px] h-8 w-1.5 rounded-full bg-[#0b3d91]" />
                    <div className="absolute right-6 top-[-10px] h-8 w-1.5 rounded-full bg-[#0b3d91]" />
                    <div className="absolute right-3 bottom-3 h-2 w-6 rounded-full bg-[#ff8a00]" />
                </div>
                <div className="absolute right-6 top-12 h-20 w-[4.5rem] rounded-[18px] bg-[linear-gradient(180deg,#0f172a_0%,#1f2937_100%)] shadow-[0_16px_32px_-22px_rgba(15,23,42,0.55)]" />
            </>
        );
    }

    if (key.includes('printer')) {
        return (
            <>
                {base}
                <div className="absolute left-6 top-12 h-[5.5rem] w-32 rounded-[18px] bg-[linear-gradient(180deg,#f8fafc_0%,#dbe7fb_100%)] shadow-[0_16px_32px_-22px_rgba(15,23,42,0.2)]">
                    <div className="absolute inset-x-5 top-4 h-8 rounded-[10px] bg-white shadow-inner" />
                    <div className="absolute inset-x-4 bottom-4 h-5 rounded-[10px] bg-[linear-gradient(180deg,#0b3d91_0%,#1f68d9_100%)]" />
                </div>
            </>
        );
    }

    return (
        <>
            {base}
            <div className="absolute left-6 top-10 h-24 w-28 rounded-[18px] bg-[linear-gradient(180deg,#0b3d91_0%,#1f68d9_100%)] shadow-[0_16px_32px_-22px_rgba(15,23,42,0.45)]">
                <div className="absolute inset-3 rounded-[12px] border border-white/10 bg-white/10" />
            </div>
            <div className="absolute right-6 top-12 h-20 w-[4.5rem] rounded-[16px] bg-[linear-gradient(180deg,#ffcf30_0%,#ff8a00_100%)] shadow-[0_16px_32px_-22px_rgba(255,138,0,0.45)]" />
        </>
    );
}

function PromoArtwork({
    variant = 'hero',
    label,
    title,
    copy,
    sceneLabel,
    imageUrl,
    imageAlt = '',
    imageFit = 'contain',
    imageClassName = '',
    className = '',
    framed = false,
}) {
    const style = variantStyles[variant] ?? variantStyles.hero;

    return (
        <div
            aria-hidden="true"
            className={`relative w-full overflow-hidden rounded-[24px] ${framed ? 'border border-white/15 shadow-[0_18px_45px_-28px_rgba(15,23,42,0.65)]' : ''} ${style.shell} ${className}`}
        >
            <div className={`absolute -left-14 top-8 h-32 w-32 rounded-full blur-3xl ${style.aura}`} />
            <div className={`absolute right-0 top-0 h-40 w-40 rounded-full blur-3xl ${style.aura}`} />
            <div className="absolute inset-0 opacity-35 [background-image:linear-gradient(90deg,rgba(255,255,255,0.18)_1px,transparent_1px),linear-gradient(180deg,rgba(255,255,255,0.18)_1px,transparent_1px)] [background-size:28px_28px]" />
            <div className="absolute inset-0 bg-gradient-to-tr from-white/5 via-transparent to-white/15" />

            {variant === 'hero' ? (
                <div className="absolute inset-0">
                    <div className="absolute left-5 top-7 h-[56%] w-[60%] rounded-[26px] border border-white/20 bg-[#050a1f]/55 p-3 shadow-[0_26px_60px_-34px_rgba(0,0,0,0.7)] backdrop-blur">
                        <div className={`relative h-full overflow-hidden rounded-[18px] ${style.inner}`}>
                            <div className="absolute inset-0 bg-[radial-gradient(circle_at_18%_18%,rgba(255,255,255,0.45),transparent_18%),radial-gradient(circle_at_72%_16%,rgba(255,138,0,0.28),transparent_16%),radial-gradient(circle_at_70%_78%,rgba(255,255,255,0.18),transparent_18%)] opacity-70" />
                            <div className="absolute left-4 top-4 h-4 w-20 rounded-full bg-white/15" />
                            <div className="absolute left-4 top-11 h-3 w-28 rounded-full bg-white/20" />
                            <div className="absolute left-4 top-16 h-3 w-[4.5rem] rounded-full bg-white/14" />
                            <div className="absolute right-4 bottom-4 h-10 w-24 rounded-[12px] border border-white/20 bg-white/10" />
                        </div>
                    </div>

                    <div className="absolute right-4 top-6 h-[66%] w-[30%] rounded-[26px] border border-white/20 bg-white/10 p-3 backdrop-blur">
                        <div className="flex h-full flex-col overflow-hidden rounded-[18px] bg-white shadow-[0_18px_42px_-24px_rgba(15,23,42,0.55)]">
                            <div className="h-16 bg-[linear-gradient(135deg,#ffcf30_0%,#ff8a00_60%,#0b3d91_100%)]" />
                            <div className="flex-1 space-y-2 p-3">
                                <div className="h-3 rounded-full bg-slate-200" />
                                <div className="h-3 w-2/3 rounded-full bg-slate-200" />
                                <div className="mt-2 h-10 rounded-[14px] bg-[linear-gradient(135deg,#0b3d91_0%,#1f68d9_100%)]" />
                            </div>
                        </div>
                    </div>

                    <div className="absolute bottom-4 left-4 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-[10px] font-black uppercase tracking-[0.18em] text-[#ffd59a] backdrop-blur">
                        Live offer
                        <span className={`h-2 w-2 rounded-full ${style.accent}`} />
                    </div>
                </div>
            ) : null}

            {variant === 'gamingBanner' ? (
                <div className="absolute inset-0">
                    {imageUrl ? (
                        <img
                            src={imageUrl}
                            alt={imageAlt}
                            loading="lazy"
                            className={`absolute inset-0 h-full w-full ${imageFit === 'contain' ? 'object-contain' : 'object-cover'} ${imageClassName}`}
                        />
                    ) : (
                        renderGamingBannerScene()
                    )}
                </div>
            ) : null}

            {variant === 'banner' ? (
                <div className="absolute inset-0">
                    <div className="absolute left-6 top-8 h-[68%] w-[52%] rounded-[28px] border border-white/20 bg-white/10 p-3 backdrop-blur">
                        <div className="relative h-full rounded-[20px] border border-white/20 bg-[#081533]/70 shadow-[0_20px_50px_-28px_rgba(0,0,0,0.65)]">
                            <div className="absolute inset-0 bg-[radial-gradient(circle_at_22%_18%,rgba(255,255,255,0.35),transparent_18%),radial-gradient(circle_at_78%_18%,rgba(255,138,0,0.28),transparent_16%),linear-gradient(135deg,rgba(255,255,255,0.04),rgba(255,255,255,0))]" />
                            <div className="absolute left-4 top-4 h-4 w-24 rounded-full bg-white/18" />
                            <div className="absolute left-4 top-11 h-3 w-32 rounded-full bg-white/16" />
                            <div className="absolute right-4 bottom-4 h-12 w-20 rounded-[14px] border border-white/20 bg-white/12" />
                        </div>
                    </div>

                    <div className="absolute right-7 top-10 h-[58%] w-[38%] rounded-[30px] border border-white/25 bg-white/10 p-3 backdrop-blur">
                        <div className="relative h-full overflow-hidden rounded-[20px] bg-white shadow-[0_18px_44px_-28px_rgba(15,23,42,0.55)]">
                            <div className="absolute inset-0 bg-[linear-gradient(160deg,rgba(255,255,255,0.82),rgba(255,255,255,0.2))]" />
                            <div className="absolute inset-x-0 top-0 h-16 bg-[linear-gradient(135deg,#0b3d91_0%,#1f68d9_100%)]" />
                            <div className="absolute left-4 top-4 h-8 w-20 rounded-[14px] bg-[#ffcf30]" />
                            <div className="absolute right-4 top-4 h-8 w-8 rounded-full bg-[#ff8a00]" />
                            <div className="absolute left-4 bottom-4 h-12 w-[70%] rounded-[18px] bg-[#eaf2ff]" />
                        </div>
                    </div>

                    <div className="absolute bottom-5 left-6 right-6 h-2 rounded-full bg-white/20" />
                </div>
            ) : null}

            {variant === 'categoryTile' ? (
                <div className="absolute inset-0">
                    <div className="absolute inset-0 bg-[linear-gradient(180deg,rgba(255,255,255,0.08),rgba(255,255,255,0))]" />
                    <div className="absolute inset-0 rounded-[24px] p-3">
                        <div className="relative h-full overflow-hidden rounded-[18px] bg-white shadow-[0_16px_36px_-24px_rgba(15,23,42,0.38)]">
                            {imageUrl ? (
                                <>
                                    <div className="absolute inset-0 bg-[radial-gradient(circle_at_18%_18%,rgba(255,255,255,0.72),transparent_24%),radial-gradient(circle_at_82%_18%,rgba(31,104,217,0.08),transparent_22%),linear-gradient(180deg,#fbfdff_0%,#eef4ff_62%,#dbe6f7_100%)]" />
                                    <img
                                        src={imageUrl}
                                        alt={imageAlt}
                                        loading="lazy"
                                        className={`relative z-10 h-full w-full ${imageFit === 'contain' ? 'object-contain p-3' : 'object-cover'} ${imageClassName}`}
                                    />
                                </>
                            ) : (
                                renderCategoryTileScene(sceneLabel)
                            )}
                        </div>
                    </div>
                </div>
            ) : null}

            {variant === 'memory' ? (
                <div className="absolute inset-0">
                    <div className="absolute left-5 top-7 h-[62%] w-[44%] rounded-[28px] border border-white/15 bg-white/8 p-3 backdrop-blur">
                        <div className="relative h-full overflow-hidden rounded-[20px] bg-[linear-gradient(145deg,#050816_0%,#0b2e71_48%,#1f68d9_100%)] shadow-[0_20px_48px_-26px_rgba(0,0,0,0.65)]">
                            <div className="absolute inset-0 bg-[radial-gradient(circle_at_18%_18%,rgba(255,255,255,0.25),transparent_18%),radial-gradient(circle_at_68%_20%,rgba(255,138,0,0.22),transparent_16%),radial-gradient(circle_at_72%_78%,rgba(255,255,255,0.12),transparent_18%)] opacity-85" />
                            <div className="absolute left-4 top-4 h-4 w-24 rounded-full bg-white/15" />
                            <div className="absolute left-4 top-11 h-3 w-28 rounded-full bg-white/20" />
                            <div className="absolute left-4 top-16 h-3 w-[4.5rem] rounded-full bg-white/16" />
                            <div className="absolute right-4 bottom-4 h-10 w-24 rounded-[14px] border border-white/15 bg-white/10" />
                        </div>
                    </div>

                    <div className="absolute right-4 top-6 h-[68%] w-[44%] rounded-[30px] border border-white/20 bg-white/8 p-3 backdrop-blur">
                        <div className="relative h-full overflow-hidden rounded-[20px] bg-[#060b1d] shadow-[0_20px_52px_-28px_rgba(0,0,0,0.7)]">
                            <div className="absolute inset-0 bg-[linear-gradient(135deg,rgba(255,255,255,0.05),rgba(255,255,255,0))]" />
                            <div className="absolute left-4 top-5 right-4 flex h-20 items-center gap-3 rounded-[18px] border border-white/10 bg-[linear-gradient(135deg,#ff1f1f_0%,#ff7b22_45%,#ffd059_100%)] px-4 shadow-[0_18px_36px_-24px_rgba(255,99,71,0.7)]">
                                <div className="h-10 w-10 rounded-xl bg-black/35" />
                                <div className="h-4 w-24 rounded-full bg-white/18" />
                            </div>
                            <div className="absolute inset-x-4 bottom-4 rounded-[18px] border border-white/10 bg-[linear-gradient(145deg,#0b2e71_0%,#1f68d9_100%)] p-4">
                                <div className="h-3 w-20 rounded-full bg-white/20" />
                                <div className="mt-3 grid grid-cols-6 gap-1">
                                    {Array.from({ length: 12 }).map((_, index) => (
                                        <span
                                            key={index}
                                            className={`h-6 rounded-[3px] ${index % 2 === 0 ? 'bg-[#0f1d4a]' : 'bg-[#dbe7fb]'}`}
                                        />
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="absolute bottom-4 left-5 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-[10px] font-black uppercase tracking-[0.18em] text-[#ffd59a] backdrop-blur">
                        Memory Finder
                        <span className={`h-2 w-2 rounded-full ${style.accent}`} />
                    </div>
                </div>
            ) : null}

            {variant === 'smartHome' ? (
                <div className="absolute inset-0 isolate overflow-hidden">
                    <div className="absolute inset-0 bg-[radial-gradient(circle_at_14%_20%,rgba(255,255,255,0.34),transparent_18%),radial-gradient(circle_at_62%_16%,rgba(255,236,210,0.28),transparent_18%),radial-gradient(circle_at_88%_78%,rgba(35,18,10,0.28),transparent_16%),linear-gradient(135deg,#f7e4d5_0%,#e4b98f_24%,#c98559_50%,#8a5331_76%,#4f2b18_100%)]" />
                    <div className="absolute inset-0 bg-[linear-gradient(90deg,rgba(255,247,239,0.36)_0%,rgba(255,247,239,0.18)_26%,rgba(255,247,239,0)_42%,rgba(0,0,0,0.16)_76%,rgba(0,0,0,0.28)_100%)]" />
                    <div className="absolute inset-x-0 top-0 h-24 bg-[linear-gradient(180deg,rgba(255,255,255,0.34),transparent)]" />
                    <div className="absolute inset-x-0 bottom-0 h-[42%] bg-[linear-gradient(180deg,rgba(255,255,255,0),rgba(54,25,12,0.1)_26%,rgba(34,16,10,0.28)_100%)]" />

                    <div className="absolute left-[-4%] top-10 h-[70%] w-[54%] rounded-[36px] bg-[linear-gradient(180deg,#f6e7d9_0%,#e8c5a5_36%,#cd8a5a_72%,#8a5230_100%)] shadow-[0_26px_60px_-34px_rgba(83,42,18,0.7)]">
                        <div className="absolute inset-x-6 top-6 h-16 rounded-[26px] bg-[linear-gradient(135deg,rgba(255,255,255,0.52),rgba(255,238,214,0.18))]" />
                        <div className="absolute left-6 top-20 h-[48%] w-[58%] rounded-[30px] bg-[radial-gradient(circle_at_28%_24%,rgba(255,255,255,0.45),transparent_18%),linear-gradient(180deg,#f9e7d9_0%,#edccb0_52%,#cb8656_100%)] shadow-[0_22px_48px_-30px_rgba(55,27,11,0.62)]">
                            <div className="absolute left-4 top-4 h-14 w-[4.5rem] rounded-[18px] bg-white/55" />
                            <div className="absolute left-24 top-3 h-16 w-24 rounded-[18px] bg-[#f7efe7]/90" />
                            <div className="absolute left-[42%] top-5 h-12 w-[4.5rem] rounded-[18px] bg-[#f2e8de]" />
                        </div>
                        <div className="absolute left-10 bottom-16 h-14 w-[48%] rounded-[24px] bg-[#f0d7c3]/80 shadow-[0_12px_32px_-20px_rgba(60,31,14,0.8)]" />
                        <div className="absolute left-12 bottom-11 h-28 w-4 rounded-full bg-[#c88958]" />
                        <div className="absolute left-8 bottom-9 h-6 w-10 rounded-full bg-[#f6e9dc]" />
                        <div className="absolute left-[52%] bottom-14 h-24 w-28 rounded-[28px] bg-[linear-gradient(180deg,#8f5832_0%,#b37243_100%)] shadow-[0_18px_42px_-26px_rgba(75,39,18,0.84)]" />
                        <div className="absolute left-[56%] bottom-[4.5rem] h-3 w-12 rounded-full bg-[#f6e9dc]/70" />
                        <div className="absolute left-[32%] bottom-8 h-12 w-[44%] rounded-full bg-[#f8efe7]/55 blur-[1px]" />
                    </div>

                    <div className="absolute right-7 top-8 h-[72%] w-[31%] rounded-[30px] bg-[linear-gradient(180deg,#93633f_0%,#6d4026_48%,#4e2919_100%)] shadow-[0_22px_52px_-30px_rgba(49,25,13,0.82)]">
                        <div className="absolute inset-0 rounded-[30px] bg-[radial-gradient(circle_at_62%_24%,rgba(255,209,154,0.16),transparent_18%),radial-gradient(circle_at_46%_74%,rgba(255,255,255,0.08),transparent_18%)]" />
                        <div className="absolute left-1/2 top-1/2 h-[4.5rem] w-7 -translate-x-1/2 -translate-y-1/2 rounded-full border border-[#e1b180]/55 bg-[#2b1a12] shadow-[0_0_0_5px_rgba(255,255,255,0.04)]" />
                        <div className="absolute left-4 top-4 h-2 w-14 rounded-full bg-white/15" />
                        <div className="absolute right-4 top-4 h-16 w-2 rounded-full bg-white/10" />
                    </div>

                    <div className="absolute right-12 bottom-10 h-16 w-16 rounded-full bg-[#f9f3ed] shadow-[0_12px_26px_-20px_rgba(45,26,15,0.58)]">
                        <div className="absolute inset-3 rounded-full border border-[#dfb590] bg-white/80" />
                        <div className="absolute inset-[11px] rounded-full bg-[#e8f0ff]" />
                    </div>

                    <div className="absolute left-6 top-6 z-10 max-w-[760px] text-white [text-shadow:0_4px_16px_rgba(53,29,13,0.58)]">
                        <p className="inline-flex rounded-full border border-white/25 bg-white/15 px-4 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-[#ffe2b8] backdrop-blur">
                            Smart comfort home
                        </p>
                        <div className="mt-4 max-w-[700px] rounded-[28px] border border-white/18 bg-[linear-gradient(135deg,rgba(44,22,12,0.82),rgba(103,53,25,0.66))] px-5 py-4 shadow-[0_18px_44px_-28px_rgba(0,0,0,0.58)] backdrop-blur sm:px-6 sm:py-5">
                            <h3 className="max-w-[620px] text-2xl font-black leading-[0.95] tracking-[-0.07em] sm:text-4xl lg:text-5xl">
                                Warm up your home with smart tech
                            </h3>
                            <div className="mt-4 flex flex-wrap items-center gap-2">
                                <span className="rounded-full bg-[#5d301b]/90 px-4 py-2 text-xs font-bold text-white shadow-[0_12px_22px_-18px_rgba(0,0,0,0.7)] sm:text-sm">
                                    Cozy upgrades for every room
                                </span>
                                <span className="rounded-full bg-[#ff8a00] px-4 py-2 text-xs font-bold text-white shadow-[0_12px_22px_-18px_rgba(0,0,0,0.7)] sm:text-sm">
                                    Empower your space
                                </span>
                            </div>
                        </div>
                        <div className="mt-4 hidden flex-wrap items-center gap-2 sm:flex">
                            <span className="rounded-full border border-white/20 bg-white/10 px-3 py-1 text-[10px] font-black uppercase tracking-[0.18em] text-[#ffe5c0] backdrop-blur">
                                Connected living
                            </span>
                            <span className="rounded-full border border-white/20 bg-white/10 px-3 py-1 text-[10px] font-black uppercase tracking-[0.18em] text-[#ffe5c0] backdrop-blur">
                                Automation ready
                            </span>
                        </div>
                    </div>

                    <div className="absolute bottom-5 left-6 right-6 z-10 hidden items-center justify-between gap-3 sm:flex">
                        <div className="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-[10px] font-black uppercase tracking-[0.18em] text-[#ffe2b8] backdrop-blur">
                            Cozy home tech
                            <span className={`h-2 w-2 rounded-full ${style.accent}`} />
                        </div>
                        <div className="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-[10px] font-black uppercase tracking-[0.18em] text-[#ffe2b8] backdrop-blur">
                            See more smart picks
                        </div>
                    </div>
                </div>
            ) : null}

            {variant === 'strip' ? (
                <div className="absolute inset-0">
                    <div className="absolute left-5 top-6 h-[66%] w-[34%] rounded-[28px] border border-[#d7e3f4] bg-white p-3 shadow-[0_18px_42px_-30px_rgba(15,23,42,0.4)]">
                        <div className="h-full rounded-[18px] bg-[linear-gradient(160deg,#0b3d91_0%,#1f68d9_100%)] p-3">
                            <div className="h-8 w-20 rounded-full bg-white/12" />
                            <div className="mt-4 space-y-2">
                                <div className="h-3 rounded-full bg-white/18" />
                                <div className="h-3 w-2/3 rounded-full bg-white/14" />
                            </div>
                            <div className="mt-5 grid grid-cols-3 gap-2">
                                {Array.from({ length: 9 }).map((_, index) => (
                                    <span key={index} className="h-3 rounded-[3px] bg-white/12" />
                                ))}
                            </div>
                        </div>
                    </div>

                    <div className="absolute right-4 top-6 h-[72%] w-[28%] rounded-[26px] border border-white/30 bg-white/70 p-3 shadow-[0_18px_42px_-30px_rgba(15,23,42,0.35)] backdrop-blur">
                        <div className="relative h-full rounded-[18px] bg-white p-3 shadow-inner">
                            <div className="grid h-full grid-cols-3 gap-1">
                                {Array.from({ length: 36 }).map((_, index) => (
                                    <span
                                        key={index}
                                        className={`rounded-[2px] ${index % 4 === 0 || index % 5 === 0 ? 'bg-[#0b3d91]' : 'bg-[#dbe7fb]'}`}
                                    />
                                ))}
                            </div>
                        </div>
                    </div>

                    <div className="absolute left-[42%] top-[20%] h-8 w-20 rounded-full bg-[#ff8a00]/20 blur-xl" />
                    <div className="absolute bottom-4 left-6 inline-flex items-center gap-2 rounded-full border border-[#d7e3f4] bg-white/90 px-3 py-1 text-[10px] font-black uppercase tracking-[0.18em] text-[#0b3d91] shadow-sm">
                        App ready
                    </div>
                </div>
            ) : null}

            {variant === 'tile' ? (
                <div className="absolute inset-0">
                    <div className="absolute left-5 top-6 h-[58%] w-[48%] rounded-[24px] border border-white/20 bg-white/15 p-3 backdrop-blur">
                        <div className="h-full rounded-[18px] bg-white/90 p-3 shadow-[0_16px_40px_-24px_rgba(15,23,42,0.45)]">
                            <div className="h-3 w-20 rounded-full bg-[#0b3d91]/20" />
                            <div className="mt-3 h-14 rounded-[14px] bg-[linear-gradient(135deg,#0b3d91_0%,#1f68d9_100%)]" />
                            <div className="mt-3 h-3 rounded-full bg-slate-200" />
                            <div className="mt-2 h-3 w-2/3 rounded-full bg-slate-200" />
                        </div>
                    </div>

                    <div className="absolute right-6 top-8 h-[44%] w-[28%] rounded-[22px] border border-white/25 bg-white/15 p-3 backdrop-blur">
                        <div className="h-full rounded-[16px] bg-[linear-gradient(160deg,#ffcf30_0%,#ff8a00_100%)] shadow-[0_14px_36px_-24px_rgba(255,138,0,0.55)]" />
                    </div>

                    <div className="absolute right-10 bottom-8 h-14 w-14 rounded-full bg-[#0b3d91]/20 blur-2xl" />
                </div>
            ) : null}

            {label || title || copy ? (
                <div className="absolute inset-x-4 bottom-4 rounded-[18px] border border-white/30 bg-white/86 p-3 shadow-[0_12px_30px_-22px_rgba(15,23,42,0.45)] backdrop-blur">
                    {label ? (
                        <p className="text-[10px] font-black uppercase tracking-[0.18em] text-[#0b3d91]">{label}</p>
                    ) : null}
                    {title ? <p className="mt-1 text-sm font-black tracking-[-0.03em] text-slate-900">{title}</p> : null}
                    {copy ? <p className="mt-1 text-xs leading-5 text-slate-600">{copy}</p> : null}
                </div>
            ) : null}
        </div>
    );
}

export default PromoArtwork;

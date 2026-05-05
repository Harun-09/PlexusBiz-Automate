import { useEffect, useState } from 'react';
import PromoArtwork from '@/Components/PromoArtwork';

function CarouselButton({ direction, onClick, label }) {
    const isPrev = direction === 'prev';

    return (
        <button
            type="button"
            onClick={onClick}
            aria-label={label}
            className={`absolute top-1/2 z-20 -translate-y-1/2 rounded-[18px] border border-white/10 bg-black/40 px-3 py-5 text-4xl leading-none text-white/90 shadow-[0_18px_35px_-24px_rgba(0,0,0,0.65)] backdrop-blur transition hover:bg-black/55 ${
                isPrev ? 'left-4' : 'right-4'
            }`}
        >
            {isPrev ? '<' : '>'}
        </button>
    );
}

export default function PromoCarousel({ slides = [], className = '' }) {
    const [activeIndex, setActiveIndex] = useState(0);
    const [paused, setPaused] = useState(false);

    const totalSlides = slides.length;
    const currentSlide = slides[activeIndex] ?? slides[0] ?? null;

    useEffect(() => {
        if (paused || totalSlides <= 1) {
            return undefined;
        }

        const intervalId = window.setInterval(() => {
            setActiveIndex((current) => (current + 1) % totalSlides);
        }, 5500);

        return () => window.clearInterval(intervalId);
    }, [paused, totalSlides]);

    if (!currentSlide) {
        return null;
    }

    const goToSlide = (nextIndex) => {
        setActiveIndex((nextIndex + totalSlides) % totalSlides);
    };

    return (
        <article className={`overflow-hidden rounded-[28px] border border-[#d7e3f4] bg-white shadow-sm ${className}`}>
            <div className="relative">
                <div className="grid min-h-[300px] gap-0 lg:grid-cols-[1.08fr_.92fr]">
                    <div className="relative min-h-[300px] overflow-hidden bg-gradient-to-r from-[#4f7fe0] via-[#3f70d4] to-[#2953b1]">
                        {currentSlide.imageUrl ? (
                            <img
                                src={currentSlide.imageUrl}
                                alt=""
                                className="h-full min-h-[300px] w-full object-cover"
                                loading="lazy"
                            />
                        ) : (
                            <PromoArtwork
                                variant={currentSlide.artVariant || 'hero'}
                                className="h-full min-h-[300px]"
                                framed={false}
                            />
                        )}
                        <div className="absolute inset-0 bg-gradient-to-r from-[#040c1f]/82 via-[#040c1f]/18 to-transparent" />

                        {totalSlides > 1 ? (
                            <>
                                <CarouselButton
                                    direction="prev"
                                    label="Previous slide"
                                    onClick={() => goToSlide(activeIndex - 1)}
                                />
                                <CarouselButton
                                    direction="next"
                                    label="Next slide"
                                    onClick={() => goToSlide(activeIndex + 1)}
                                />
                            </>
                        ) : null}

                        {totalSlides > 1 ? (
                            <button
                                type="button"
                                onClick={() => setPaused((current) => !current)}
                                aria-label={paused ? 'Resume slideshow' : 'Pause slideshow'}
                                className="absolute bottom-4 right-4 z-20 inline-flex h-11 w-11 items-center justify-center rounded-full bg-black/55 text-sm font-black text-white shadow-[0_12px_28px_-20px_rgba(0,0,0,0.7)] backdrop-blur transition hover:bg-black/70"
                            >
                                {paused ? '>' : '||'}
                            </button>
                        ) : null}
                    </div>

                    <div className="flex flex-col justify-center gap-4 bg-gradient-to-r from-[#4f7fe0] via-[#3f70d4] to-[#2953b1] px-6 py-6 text-white">
                        <p className="text-[11px] font-black uppercase tracking-[0.2em] text-[#ffd59a]">
                            {currentSlide.eyebrow || 'Featured'}
                        </p>
                        <h3 className="max-w-md text-2xl font-black tracking-[-0.05em] sm:text-3xl">
                            {currentSlide.title}
                        </h3>
                        <p className="max-w-md text-sm leading-6 text-blue-100">
                            {currentSlide.copy}
                        </p>
                        <div className="flex flex-wrap items-center gap-3">
                            <a
                                href={currentSlide.href || '#deals'}
                                className="inline-flex rounded-full bg-[#ffcf30] px-5 py-2.5 text-xs font-black uppercase tracking-[0.16em] text-[#0b2e71]"
                            >
                                {currentSlide.cta || 'Shop now'}
                            </a>
                            {totalSlides > 1 ? (
                                <span className="text-xs font-bold uppercase tracking-[0.18em] text-white/60">
                                    {activeIndex + 1}/{totalSlides}
                                </span>
                            ) : null}
                        </div>

                        {totalSlides > 1 ? (
                            <div className="flex flex-wrap gap-2 pt-1">
                                {slides.map((slide, index) => (
                                    <button
                                        key={`${slide.title}-${index}`}
                                        type="button"
                                        onClick={() => goToSlide(index)}
                                        aria-label={`Go to slide ${index + 1}`}
                                        className={`h-2.5 rounded-full transition ${
                                            index === activeIndex ? 'w-9 bg-[#ffcf30]' : 'w-2.5 bg-white/30 hover:bg-white/50'
                                        }`}
                                    />
                                ))}
                            </div>
                        ) : null}
                    </div>
                </div>
            </div>
        </article>
    );
}

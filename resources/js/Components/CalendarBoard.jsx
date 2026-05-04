export default function CalendarBoard({ days = [], renderDay }) {
    return (
        <div className="grid grid-cols-1 gap-px overflow-hidden rounded-lg border border-slate-200 bg-slate-200 sm:grid-cols-7">
            {days.map((day, index) => (
                <div key={day.key || index} className="min-h-32 bg-white p-3">
                    {renderDay ? renderDay(day) : (
                        <>
                            <p className="text-xs font-black uppercase tracking-wide text-slate-400">{day.label}</p>
                            <div className="mt-2 space-y-2">
                                {(day.items || []).map((item) => (
                                    <div key={item.id || item.label} className="rounded-md bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-700">
                                        {item.label}
                                    </div>
                                ))}
                            </div>
                        </>
                    )}
                </div>
            ))}
        </div>
    );
}

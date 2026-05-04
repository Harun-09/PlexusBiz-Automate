import Modal from '@/Components/Modal';

export default function ConfirmModal({ show, title, message, confirmLabel = 'Confirm', onClose, onConfirm, processing = false }) {
    return (
        <Modal show={show} onClose={onClose} maxWidth="md">
            <div className="p-6">
                <h2 className="text-lg font-black text-slate-950">{title}</h2>
                <p className="mt-2 text-sm leading-6 text-slate-600">{message}</p>
                <div className="mt-6 flex justify-end gap-2">
                    <button type="button" onClick={onClose} className="rounded-lg border border-slate-200 px-4 py-2 text-sm font-bold text-slate-700">
                        Cancel
                    </button>
                    <button
                        type="button"
                        onClick={onConfirm}
                        disabled={processing}
                        className="rounded-lg bg-slate-950 px-4 py-2 text-sm font-bold text-white disabled:opacity-60"
                    >
                        {processing ? 'Working' : confirmLabel}
                    </button>
                </div>
            </div>
        </Modal>
    );
}

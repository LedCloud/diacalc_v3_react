import Modal from "@/Components/Modal.jsx";
import {useTrans} from "@/Hooks/useTrans.jsx";

export default function CalorieCounterPopup({show, onClose, eaten, menu, limit})
{
    const { __ } = useTrans();
    const total = Number(eaten ?? 0) + Number(menu ?? 0);

    return (
        <Modal show={show} onClose={onClose} header={__('counter')} maxWidth="md">
            <div className="py-3 px-4">
                <p className="mb-4">{__('daily_calorie_counter')}</p>
                <div className="flex items-end justify-center gap-2 text-center">
                    <div>
                        <div className="text-sm text-slate-600 mb-1">{__('eaten')}</div>
                        <div className="bg-slate-200 rounded px-3 py-1 font-semibold">{eaten}</div>
                    </div>
                    <div className="pb-1">+</div>
                    <div>
                        <div className="text-sm text-slate-600 mb-1">{__('in_menu')}</div>
                        <div className="bg-slate-200 rounded px-3 py-1 font-semibold">{menu}</div>
                    </div>
                    <div className="pb-1">=</div>
                    <div>
                        <div className="text-sm text-slate-600 mb-1">{__('total')}</div>
                        <div className="bg-slate-200 rounded px-3 py-1 font-semibold">{total}</div>
                    </div>
                    <div>
                        <div className="text-sm text-slate-600 mb-1">{__('limit')}</div>
                        <div className="px-3 py-1 font-semibold">{limit}</div>
                    </div>
                </div>
            </div>
            <div className="flex justify-end gap-3 p-2 border-t border-slate-200">
                <button
                    type="button"
                    className="px-2 py-1 w-24 rounded ring-2 ring-offset-1 ring-blue-400 bg-sky-300"
                    onClick={onClose}
                >{__('close')}</button>
            </div>
        </Modal>
    );
}

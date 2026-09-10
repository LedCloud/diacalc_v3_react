import Modal from "@/Components/Modal.jsx";
import {useTrans} from "@/Hooks/useTrans.jsx";

const formatDec = (n, fractions) =>
    Number(n).toFixed(fractions).replace('.', ',');

export default function DoseDetailsPopup({show, onClose, dose, product, be})
{
    const { __ } = useTrans();

    const carb = product?.getCarb?.() ?? 0;
    const xe = Number(be) > 0 ? carb / Number(be) : 0;

    return (
        <Modal show={show} onClose={onClose} header={__('dose_breakdown')} maxWidth="md">
            <div className="dose-details-popup py-3 px-4">
                <table className="dose-details-popup__table">
                    <thead>
                        <tr>
                            <th>{__('dose_quick')}</th>
                            <th>{__('dose_slow')}</th>
                            <th>{__('dose_sum')}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr className="dose-details-popup__totals">
                            <td>{formatDec(dose.getQuick(), 1)}</td>
                            <td>{formatDec(dose.getSlow(), 1)}</td>
                            <td>{formatDec(dose.getWholeD(), 1)}</td>
                        </tr>
                        <tr>
                            <td>
                                ({__('dose_dps')} {formatDec(dose.getDPS(), 1)}
                                {' + '}{__('dose_q_carb')} {formatDec(dose.getQCarbD(), 1)})
                            </td>
                            <td>
                                ({__('dose_sl_carb')} {formatDec(dose.getSlCarbD(), 1)}
                                {' + '}{__('dose_sl_prot')} {formatDec(dose.getProtFatD(), 1)})
                            </td>
                            <td>{formatDec(dose.getWholeD(), 1)}</td>
                        </tr>
                        <tr>
                            <td>
                                ({__('dose_dps')} {formatDec(dose.getDPS(), 1)}
                                {' + '}{__('dose_carb')} {formatDec(dose.getCarbD(), 1)})
                            </td>
                            <td>
                                ({__('dose_prot_fat')} {formatDec(dose.getProtFatD(), 1)})
                            </td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>

                <table className="dose-details-popup__table dose-details-popup__nutrition">
                    <thead>
                        <tr>
                            <th>{__('p')}</th>
                            <th>{__('f')}</th>
                            <th>{__('c')}</th>
                            <th>{__('be')}</th>
                            <th>{__('gi')}</th>
                            <th>{__('gl')}</th>
                            <th>{__('kcal')}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{formatDec(product.getProt(), 1)}</td>
                            <td>{formatDec(product.getFat(), 1)}</td>
                            <td>{formatDec(product.getCarb(), 1)}</td>
                            <td>{formatDec(xe, 1)}</td>
                            <td>{formatDec(product.gi, 0)}</td>
                            <td>{formatDec(product.getGLIndx(), 0)}</td>
                            <td>{formatDec(product.getCalor(), 0)}</td>
                        </tr>
                    </tbody>
                </table>
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

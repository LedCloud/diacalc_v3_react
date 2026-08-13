import Pane from "@/Components/Pane.jsx";
import React, {useState} from "react";
import InputTwoLines from "@/Components/InputTwoLines.jsx";
import Modal from '@/Components/Modal';
import {useTrans} from "@/Hooks/useTrans.jsx";
import {router} from "@inertiajs/react";

export default function ProductsTabContect({
    allSettings, setAllSettings, activeTab, menuMasks, errors, className = ''})
{
    const [fillDefault, setFillDefault] = useState(false);
    const [showModal, setShowModal] = useState(false);

    const tabId = 'products';

    const changeFreqQty = (val) => {
        setAllSettings({...allSettings, freq_qty: val});
    };

    const changeFilterOff = (val) => {
        setAllSettings({...allSettings, filter_off: val});
    };

    const closeModal = () => {
        setShowModal(false);
    };

    const handleFillProducts = () => {
        router.post(route('settings.fill_products'), {}, {
            preserveScroll: true,
        });
        setShowModal(false);
    };

    const { __ } = useTrans();

    return (<>
        <div className={`tab-pane ${className} ${activeTab === tabId ? 'active' : ''}`}>
            <Pane header={__('products')}
                  className={`products_pane tab-pane panes__pane ${activeTab === tabId ? 'active' : ''}`}>
                <fieldset>
                    <div className="field">
                        <label className="checkbox-group" htmlFor="fillDefault">
                            <input id="fillDefault"
                                   checked={fillDefault}
                                   name="fillDefault"
                                   value="fill"
                                   onChange={(e) => {
                                       setFillDefault(e.target.checked);
                                       if (e.target.checked) {
                                           setShowModal(true);
                                       }
                                   }}
                                   type="checkbox"/>{__('fill_default')}</label>
                    </div>

                    <div className="field">
                        <label className="checkbox-group" htmlFor="useFreq">
                            <input id="useFreq"
                                   checked={Boolean(allSettings.use_freq)}
                                   name="use_freq"
                                   value="use"
                                   onChange={(e) => setAllSettings({...allSettings, use_freq: e.target.checked})}
                                   type="checkbox"/>{__('use_freq')}</label>
                    </div>
                    {Boolean(allSettings.use_freq) && (<div className="field">
                        <InputTwoLines value={allSettings.freq_qty}
                                       name="freq_qty"
                                       id="freq_qty"
                                       label={__('freq_qty')}
                                       onChange={changeFreqQty}
                        />
                        {errors.freq_qty && <div className="validation-error">{errors.freq_qty}</div>}
                    </div>)}

                    <div className="field">
                        <InputTwoLines value={allSettings.filter_off}
                                       name="filter_off"
                                       id="filter_off"
                                       label={__('filter_off')}
                                       onChange={changeFilterOff}
                        />
                        {errors.filter_off && <div className="validation-error">{errors.filter_off}</div>}
                    </div>

                </fieldset>
            </Pane>
            <Modal show={showModal} onClose={closeModal} header={__('attention_notice')}>
                <div className="py-3 px-4">
                        <p dangerouslySetInnerHTML={{ __html: __('fill_msg_desc')}} />
                </div>
                <div className="flex gap-3 p-2">
                    <button
                        type="button"
                        className="px-2 py-1 w-24 rounded ring-2 ring-offset-1 ring-blue-400 bg-sky-300"
                        onClick={handleFillProducts}>{__('ok')}
                    </button>
                    <button
                        type="button"
                        className="px-2 py-1 w-24 rounded ring-2 ring-offset-1 ring-slate-400 bg-white"
                        onClick={() => {
                            setFillDefault(false);
                            setShowModal(false);
                        }}>{__('cancel')}
                    </button>
                </div>
            </Modal>
        </div>
    </>);
}

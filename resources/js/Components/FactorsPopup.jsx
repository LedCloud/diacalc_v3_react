import InputOneLine from "@/Components/InputOneLine.jsx";
import Modal from "@/Components/Modal.jsx";
import React, {useEffect, useState} from "react";
import Glucose from "@/Classes/Glucose.js";
import {useTrans} from "@/Hooks/useTrans.jsx";

export default function FactorsPopup({onCloseHandler, factor, show, glConfig})
{
    const emptyFactor = {
        k1: factor?.k1 ?? 1,
        k2: factor?.k2 ?? 0,
        k3: factor?.k3 ?? 2,
        gl1: factor?.gl1 ?? 5.6,
        gl2: factor?.gl2 ?? 5.6,
        be: factor?.be ?? 10,
    };

    const [localFactor, setLocalFactor] = useState(emptyFactor);
    const [activeField, setActiveField] = useState({ id: null, val: '' });
    const [glucose1, setGlucose1] = useState(new Glucose(emptyFactor.gl1));
    const [glucose2, setGlucose2] = useState(new Glucose(emptyFactor.gl2));
    const [ouv, setOUV] = useState(new Glucose(emptyFactor.k3));

    const { __ } = useTrans();

    useEffect(() => {
        if (!show) {
            return;
        }
        const next = {
            k1: factor.k1,
            k2: factor.k2,
            k3: factor.k3,
            gl1: factor.gl1,
            gl2: factor.gl2,
            be: factor.be,
        };
        setLocalFactor(next);
        setGlucose1(new Glucose(next.gl1));
        setGlucose2(new Glucose(next.gl2));
        setOUV(new Glucose(next.k3));
        setActiveField({ id: null, val: '' });
    }, [show]);

    const parseDec = (s) => parseFloat(String(s).replace(',', '.'));
    const formatDec = (n, fractions) =>
        Number(n).toFixed(fractions).replace('.', ',');

    const valGlucose1 = activeField.id === 'glucose1' ? activeField.val : glucose1.getView(glConfig);
    const valGlucose2 = activeField.id === 'glucose2' ? activeField.val : glucose2.getView(glConfig);
    const valOUV = activeField.id === 'ouv' ? activeField.val : ouv.getView({...glConfig, precision: 2});

    const valK1 = activeField.id === 'k1' ? activeField.val : formatDec(localFactor.k1, 2);
    const valK2 = activeField.id === 'k2' ? activeField.val : formatDec(localFactor.k2, 2);
    const valBE = activeField.id === 'be' ? activeField.val : formatDec(localFactor.be, 0);

    const updateValue = (val, name) => {
        setActiveField({ id: name, val: val });
    };

    const formatValue = (val, name) => {
        const nextFactor = {...localFactor};
        const parsed = parseDec(val);
        if (!isNaN(parsed) && !/[.,]$/.test(val)) {
            const flats = ['k1', 'k2', 'be'];
            if (!flats.includes(name)) {
                const newGl = new Glucose();
                newGl.setVal(parsed, glConfig);
                switch (name) {
                    case 'glucose1':
                        setGlucose1(newGl);
                        break;
                    case 'glucose2':
                        setGlucose2(newGl);
                        break;
                    case 'ouv':
                        setOUV(newGl);
                        break;
                }
                const eatingKey = name === 'glucose1' ? 'gl1'
                    : name === 'glucose2' ? 'gl2'
                        : 'k3';
                nextFactor[eatingKey] = newGl.val;
            } else {
                const fractions = name === 'be' ? 0 : 2;
                nextFactor[name] = Number(parsed.toFixed(fractions));
            }
            setLocalFactor(nextFactor);
        }
        setActiveField({ id: null, val: '' });
    };

    const closeWithResult = () => {
        onCloseHandler?.(localFactor);
    };

    return (
        <Modal show={show} onClose={closeWithResult} header={__('set_factors')} maxWidth="md">
            <div className="py-3 px-4 flex flex-col gap-2">
                <InputOneLine value={valK1} name="k1" label={__('k1')} onChange={updateValue} onBlur={formatValue} />
                <InputOneLine value={valK2} name="k2" label={__('k2')} onChange={updateValue} onBlur={formatValue} />
                <InputOneLine value={valOUV} name="ouv" label={__('ouv')} onChange={updateValue} onBlur={formatValue} />
                <InputOneLine value={valGlucose1} name="glucose1" label={__('gl1')} onChange={updateValue} onBlur={formatValue} />
                <InputOneLine value={valGlucose2} name="glucose2" label={__('gl2')} onChange={updateValue} onBlur={formatValue} />
                <InputOneLine value={valBE} name="be" label={__('be')} onChange={updateValue} onBlur={formatValue} />
            </div>
            <div className="flex gap-3 p-2">
                <button
                    type="button"
                    className="px-2 py-1 w-24 rounded ring-2 ring-offset-1 ring-blue-400 bg-sky-300"
                    onClick={closeWithResult}
                >{__('ok')}</button>
            </div>
        </Modal>
    );
}

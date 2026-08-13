import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import Pane from "@/Components/Pane.jsx";
import Glucose from "@/Classes/Glucose.js";
import React, {useEffect, useRef, useState} from 'react';
import {usePage, Form} from '@inertiajs/react'
import InputTwoLines from "@/Components/InputTwoLines.jsx";
import InputOneLine from "@/Components/InputOneLine.jsx";
import Button from "@/Components/Button.jsx";
import Modal from "@/Components/Modal.jsx";
import { useTrans } from '@/Hooks/useTrans';
import PageContainer from "@/Components/PageContainer.jsx";

export default function Factors({ auth }) {

    const [activeField, setActiveField] = useState({ id: null, val: '' });
    const {settings, factors, errors} = usePage().props;
    const [allSettings, setAllSettings] = useState(settings ?? null);
    const [pageFactors, setPageFactors] = useState(factors ?? null);

    const [showDialog, setShowDialog] = useState(false);
    const defaultFactors = {k1:1,k2:0,k3:2, time:"08:00"};
    const [dialogData, setDialogData] = useState(defaultFactors);
    // 1. Create a reference for the input you want to auto-focus
    const dialogInputRef = useRef(null);
    const { __ } = useTrans();

    const config = {
        mmol: Boolean(settings.is_mmol),
        plasma: Boolean(settings.is_plasma),
        precision: 2
    };

    const [dialogK3, setK3Dialog] = useState((new Glucose(2)).getView(config));

    useEffect(() => {
        setAllSettings(settings);
        setPageFactors(factors);
    }, [settings, factors]);

    const setTime = (id, value) => {
        setPageFactors(latest =>
            latest.map(el =>
                el.id === id
                    ? {...el, time: value}
                    : el));
    };

    const setFactor = (id, name, value) => {
        setPageFactors(latest =>
            latest.map(el =>
                el.id === id
                    ? {...el, [name]: value}
                    : el));
    };

    const formatFactor = (id, name, value) => {
        const parsed = parseFloat(value);
        if (!isNaN(parsed) && !value.endsWith('.')) {
            setPageFactors(latest =>
                latest.map(el =>
                    el.id === id
                        ? {...el, [name]: String(parsed.toFixed(2))}
                        : el));
        }
    };

    const updateOUV = (id, value) => {
        setActiveField({ id: id, val: value });

        const parsed = parseFloat(value);
        if (!isNaN(parsed) && !value.endsWith('.')) {
            const newGl = new Glucose();
            newGl.setVal(value, config);
            setPageFactors(latest =>
                latest.map(el =>
                    el.id === id
                        ? {...el, k3: newGl.val}
                        : el));
        }
    };

    const formatOUV = (id, value) => {
        const parsed = parseFloat(value);
        if (!isNaN(parsed) && !value.endsWith('.')) {
            const newGl = new Glucose();
            newGl.setVal(value, config);
            setPageFactors(latest =>
                latest.map(el =>
                    el.id === id
                        ? {...el, k3: newGl.val}
                        : el));
        }
        setActiveField({ id: null, val: '' });
    };

    const setWeight = (val) => {
        setAllSettings({...allSettings, weight: val});
    };
    const formatWeight = (val) => {
        const parsed = parseFloat(val);
        if (!isNaN(parsed) && !val.endsWith('.')) {
            setAllSettings({...allSettings, weight: parsed.toFixed(0)});
        }
    };

    const setK3Factor = (val) => {
        setAllSettings({...allSettings, k3_factor: val});
    };
    const formatK3Factor = (val) => {
        const parsed = parseFloat(val);
        if (!isNaN(parsed) && !val.endsWith('.')) {
            setAllSettings({...allSettings, k3_factor: parsed.toFixed(0)});
        }
    };

    const setBE = (val) => {
        setAllSettings({...allSettings, be: val});
    };
    const formatBE = (val) => {
        const parsed = parseFloat(val);
        if (!isNaN(parsed) && !val.endsWith('.')) {
            setAllSettings({...allSettings, be: parsed.toFixed(0)});
        }
    };

    const openAddFactorsDialog = () => {
        const min = pageFactors.length > 0 ? Math.min(...pageFactors.map(e => e.id)) : 0;
        const nextId = min >= 0 ? -1 : (min - 1);
        setDialogData({...defaultFactors, id:nextId});
        setShowDialog(true);
    };

    const updateDialog = (val, field) => {
        setDialogData({...dialogData, [field]: val});
    }
    const formatDialog = (val, field) => {
        if ('time' === field) {
            return;
        }
        const parsed = parseFloat(val);
        if (!isNaN(parsed) && !val.endsWith('.')) {
            setDialogData({...dialogData, [field]: parsed.toFixed(2)});
        }
    }

    const useDialogResult = () => {
        setPageFactors(latest => {
            const copy = { ...dialogData};
            const newGl = new Glucose(2);
            newGl.setVal(dialogK3, config);
            copy.k3 = newGl.val;

            let updated;
            if (copy.id < 0 && latest.find(e => e.id === copy.id) === undefined) {
                updated = [...latest, copy];
            } else {
                updated = latest.map(e => e.id === copy.id ? copy : e);
            }

            updated.sort((a, b) => a.time.localeCompare(b.time));

            return updated;
        });
    };

    const delRow = (id) => {
        setPageFactors(latest => latest.filter(e => e.id !== id));
    };

    const updateK3Dialog = (val) => {
        setK3Dialog(val);
    };

    const formatK3Dialog = (val) => {
        const parsed = parseFloat(val);
        if (!isNaN(parsed) && !val.endsWith('.')) {
            const newGl = new Glucose(5.6);
            newGl.setVal(val, config);
            setK3Dialog(newGl.getView(config));
            setDialogData({...dialogData, k3: val});
        }
    }

    const calculateOUV = () => {
        //if (!$base->query("UPDATE `coefs` SET `k3`=".
        //             $k3factor."/(".$weight."*`k1`*10/".$be.") WHERE `iduser`='".$user_id."';")){
        setPageFactors(latest => {
            return latest.map(row => {
                return {
                    id: row.id,
                    time: row.time,
                    k1: row.k1,
                    k2: row.k2,
                    k3: +allSettings.k3_factor / (allSettings.weight * row.k1 * 10 / allSettings.be),
                };
            });
        });
    };

    const gl = new Glucose(5.6);

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">{__('factors')}</h2>}
        >
            <Head title={__('factors')} />

            <PageContainer>
                <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 factors-layout">
                    <Form action="/factors" method="patch"
                          options={{
                            preserveScroll: true
                        }}>
                        <Pane header={__('factors')} className="factors-layout__pane factors">
                            <fieldset>
                                <div className="field">
                                    <label className="checkbox-group" htmlFor="timedFactors">
                                <input id="timedFactors"
                                       checked={Boolean(allSettings.factors_by_time)}
                                       name="factors_by_time"
                                       value="timed"
                                       onChange={(e) => {
                                           setAllSettings({...allSettings, factors_by_time: (e.target.checked ? 1 : 0)});
                                       }}
                                       type="checkbox"/>{__('factors_by_time')}</label>
                                </div>
                                <InputTwoLines value={allSettings.weight}
                                               name="weight"
                                               id="weight"
                                               label={__('your_weight')}
                                               onChange={setWeight}
                                               onBlur={formatWeight}
                                />
                                {errors.weight && <div className="validation-error">{errors.weight}</div>}

                                <InputTwoLines value={allSettings.k3_factor}
                                               name="k3_factor"
                                               id="k3_factor"
                                               label={__("k3_factor")}
                                               onChange={setK3Factor}
                                               onBlur={formatK3Factor}
                                />
                                {errors.k3_factor && <div className="validation-error">{errors.k3_factor}</div>}

                                <InputTwoLines value={allSettings.be}
                                               name="be"
                                               id="be"
                                               label={__("be")}
                                               onChange={setBE}
                                               onBlur={formatBE}
                                />
                                {errors.be && <div className="validation-error">{errors.be}</div>}

                                <button type="button" className="btn" onClick={calculateOUV}>{__('calc_ouv')}</button>
                                <div className="alert alert-well">{__('calc_notice')}!</div>
                            </fieldset>
                            <table>
                                <thead>
                                    <tr>
                                        <th className="w-2/12">{__('time')}</th>
                                        <th className="w-3/12">{__('k1')}</th>
                                        <th className="w-3/12">{__('k2')}</th>
                                        <th className="w-3/12">{__('ouv')}</th>
                                        <th className="w-1/12"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                {pageFactors.map(row => {
                                    gl.val = row.k3;
                                    return (
                                        <tr key={row.id}>
                                            <td data-header={__('time')}>
                                                <input type="hidden" name={`factors[${row.id}][id]`}
                                                       value={row.id}/>
                                                <input type="time"
                                                       className="w-full"
                                                       name={`factors[${row.id}][time]`} value={row.time}
                                                       onChange={(e) => setTime(row.id, e.target.value)}
                                                />
                                                {errors[`factors.${row.id}.time`] && (
                                                    <div className="validation-error">
                                                        {errors[`factors.${row.id}.time`]}
                                                    </div>
                                                )}
                                            </td>
                                            <td data-header={__('k1')}>
                                                <input type="number"
                                                       name={`factors[${row.id}][k1]`} value={row.k1}
                                                       className="w-full"
                                                       onFocus={(e) => e.target.select()}
                                                       onChange={(e) => setFactor(row.id, 'k1', e.target.value)}
                                                       onBlur={(e) => formatFactor(row.id, 'k1', e.target.value)}
                                                />
                                                {errors[`factors.${row.id}.k1`] && (
                                                    <div className="validation-error">
                                                        {errors[`factors.${row.id}.k1`]}
                                                    </div>
                                                )}
                                            </td>
                                            <td data-header={__('k2')}>
                                                <input type="number"
                                                       name={`factors[${row.id}][k2]`} value={row.k2}
                                                       className="w-full"
                                                       onFocus={(e) => e.target.select()}
                                                       onChange={(e) => setFactor(row.id, 'k2', e.target.value)}
                                                       onBlur={(e) => formatFactor(row.id, 'k2', e.target.value)}
                                                />
                                                {errors[`factors.${row.id}.k2`] && (
                                                    <div className="validation-error">
                                                        {errors[`factors.${row.id}.k2`]}
                                                    </div>
                                                )}
                                            </td>
                                            <td data-header={__('ouv')}>
                                                <input type="hidden"
                                                       name={`factors[${row.id}][k3]`}
                                                       value={row.k3}
                                                       />
                                                <input
                                                    name={`factors[${row.id}][ouv]`}
                                                    value={activeField.id === row.id ? activeField.val : gl.getView(config)}
                                                    className="w-full"
                                                    onFocus={(e) => e.target.select()}
                                                    onChange={(e) => updateOUV(row.id, e.target.value)}
                                                    onBlur={(e) => formatOUV(row.id, e.target.value)}
                                                    />
                                                {errors[`factors.${row.id}.ouv`] && (
                                                    <div className="validation-error">
                                                        {errors[`factors.${row.id}.ouv`]}
                                                    </div>
                                                )}
                                            </td>
                                            <td className="text-center">
                                                <button className="btn"
                                                    type="button"
                                                        onClick={() => delRow(row.id)}
                                                        ><strong>X</strong></button>
                                            </td>
                                        </tr>
                                    )
                                })}
                                </tbody>
                            </table>
                            <div className="flex gap-3 p-2">
                                <Button className="ring-slate-400"
                                        onClick={openAddFactorsDialog}
                                >{__('add')}</Button>
                            </div>

                            <div className="flex gap-3 p-2">
                                <Button className="ring-blue-400 bg-sky-300"
                                        type="submit"
                                >{__('save')}</Button>
                            </div>
                        </Pane>
                    </Form>
                    <Modal show={showDialog} onClose={() => setShowDialog(false)}
                           header={__('add_factors')} >
                        <div className="py-3 px-4 flex flex-col gap-2">
                            <InputOneLine value={dialogData.time}
                                          name="time"
                                          label={__('time')}
                                          type="time"
                                          onChange={updateDialog}
                                          onBlur={formatDialog}
                            />
                            <InputOneLine value={dialogData.k1}
                                          name="k1" label={__('k1')}
                                          onChange={updateDialog}
                                          onBlur={formatDialog}
                            />
                            <InputOneLine value={dialogData.k2}
                                          name="k2" label={__('k2')}
                                          onChange={updateDialog}
                                          onBlur={formatDialog}
                            />
                            <InputOneLine value={dialogK3}
                                          name="k3" label={__('ouv')}
                                          onChange={updateK3Dialog}
                                          onBlur={formatK3Dialog}
                            />
                        </div>
                        <div className="flex gap-3 p-2">
                            <button
                                type="button"
                                className="px-2 py-1 w-24 rounded ring-2 ring-offset-1 ring-blue-400 bg-sky-300"
                                onClick={() => {
                                    useDialogResult();
                                    setShowDialog(false);
                                }}>{__('add')}
                            </button>
                            <button
                                type="button"
                                className="px-2 py-1 w-24 rounded ring-2 ring-offset-1 ring-slate-400 bg-white"
                                onClick={() => setShowDialog(false)}>{__('cancel')}
                            </button>
                        </div>
                    </Modal>
                </div>
            </PageContainer>
        </AuthenticatedLayout>
    );
}

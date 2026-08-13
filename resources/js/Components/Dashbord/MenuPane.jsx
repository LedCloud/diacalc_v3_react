import {useTrans} from "@/Hooks/useTrans.jsx";
import React, {useEffect, useState} from "react";
import InfoPiece from "@/Components/Dashbord/InfoPiece.jsx";
import { CiTrash, CiCirclePlus } from "react-icons/ci";
import { GoPencil } from "react-icons/go";
import Factor from "@/Classes/Factor.js";
import MenuProduct from "@/Classes/MenuProduct.js";
import valueCalculator from "@/Classes/MenuCalulator.js";
import {useForm} from "@inertiajs/react";
import CalculableInput from "@/Components/CalculableInput.jsx";
import GlucoseInput from "@/Components/GlucoseInput.jsx";
import Scale from "@/Components/Scale.jsx";
import Glucose from "@/Classes/Glucose.js";
import {usePage, router} from '@inertiajs/react'
import Dose from "@/Classes/Dose.js";
import Tooltip from "@/Components/Tooltip.jsx";

export default function MenuPane()
{
    //This is to understand what field is being edited right now, to allow enter part of the number
    const [activeField, setActiveField] = useState({ id: null, val: '' });

    // 1. Props from Inertia (read-only snapshot)
    const {settings, menu_masks, factors, menu_items, eating} = usePage().props;

    const factorsByTime = Boolean(+settings.factors_by_time);
    // 24h object (by-time) or user Factors.jsx list (0–N); empty is normal → hide select
    const factorOptions = Object.values(factors ?? {});

    // 2. Form: local editable copy. Applied k1/k2/k3 always live in data.eating.
    // Schedule rows are copied into eating only via select or clear (now-row when by-time).
    const { data, setData } = useForm({
        menu_items: menu_items || [],
        eating: {
            k1: eating?.k1 ?? 1,
            k2: eating?.k2 ?? 0,
            k3: eating?.k3 ?? 2,
            gl1: eating?.gl1 ?? 5.6,
            gl2: eating?.gl2 ?? 5.6,
            be: eating?.be ?? 10,
            eaten: eating?.eaten ?? 0,
            eaten_date: eating?.eaten_date ?? null,
        }
    });

    useEffect(() => { //this one is needed to update eating after post request
        if (eating) {
            setData('eating', {
                k1: eating.k1,
                k2: eating.k2,
                k3: eating.k3,
                gl1: eating.gl1,
                gl2: eating.gl2,
                be: eating.be,
                eaten: eating.eaten,
                eaten_date: eating.eaten_date,
            });
        }
    }, [eating]);

    // Select highlight only — do not overwrite eating on load
    const [currentFactor, setCurrentFactor] = useState(() => {
        if (!factorOptions.length) {
            return null;
        }
        if (factorsByTime) {
            return factorOptions.find(f => f.now === true) ?? factorOptions[0];
        }
        return factorOptions[0];
    });

    const factor = new Factor(
        data.eating.k1,
        data.eating.k2,
        data.eating.k3,
        data.eating.gl1,
        data.eating.gl2,
        data.eating.be
    );

    // 3. СИНХРОНИЗАЦИЯ: Если Laravel обновит menu_items (например, прилетит новый список),
    // мы должны обновить и состояние формы.
    useEffect(() => {
        if (menu_items) {
            setData("menu_items", menu_items);
        }
    }, [menu_items]);

    const { __ } = useTrans();

    const isMmol = () => {
        return Boolean(+settings.is_mmol);
    }

    const isPlasma = () => {
        return Boolean(+settings.is_plasma);
    }

    const formGlConfig = () => {
        return {
            mmol: isMmol(),
            plasma: isPlasma()
        }
    };

    const [glucose1, setGlucose1] = useState(new Glucose(factor.gl1));
    const [glucose2, setGlucose2] = useState(new Glucose(factor.gl2));
    const [ouv, setOUV] = useState(new Glucose(factor.k3));

    const [k1, setK1] = useState(factor.k1);

    /** Copy a schedule / Factors.jsx row into eating (local + DB). Does not run on page load. */
    const applyFactorToEating = (selected, { onSuccess } = {}) => {
        if (!selected) {
            return;
        }
        const nextEating = {
            ...data.eating,
            k1: +selected.k1,
            k2: +selected.k2,
            k3: +selected.k3,
        };
        setData('eating', nextEating);
        setCurrentFactor(selected);
        setOUV(new Glucose(+selected.k3));
        router.post(route('dashboard.updatefactors'), {
            factor: {
                k1: nextEating.k1,
                k2: nextEating.k2,
                k3: nextEating.k3,
                gl1: nextEating.gl1,
                gl2: nextEating.gl2,
                be: nextEating.be,
            },
        }, {
            preserveScroll: true,
            onSuccess,
        });
    };

    const clearMenu = () => {
        const nowRow = factorsByTime
            ? factorOptions.find(f => f.now === true)
            : null;

        const postClear = () => {
            setData('menu_items', []);
            router.post(route('dashboard.updatemenu'), {
                menu_items: [],
            }, {
                preserveScroll: true,
            });
        };

        if (nowRow) {
            applyFactorToEating(nowRow, { onSuccess: postClear });
        } else {
            postClear();
        }
    };

    const deleteItem = (id) => {
        const updated = data.menu_items.filter(el => el.id !== id);
        setData('menu_items', updated);  // optimistic UI update
        router.delete(route('dashboard.deletemenu', id), {
            preserveScroll: true,
            onError: () => {
                // rollback if server fails
                setData('menu_items', menu_items);
            },
        });
    };

    const mask_keys = Object.keys(menu_masks);

    const mask_arr = Object.fromEntries(
        mask_keys.map(mask_name => {
        return {
            show: Boolean(settings.menu_info & menu_masks[mask_name]),
            name: mask_name,
            mask: menu_masks[mask_name]
        };
    }).filter(e => e.show)
            .map(item => [item.name, item.mask]));

    const [showDetails , setShowDetails] = useState(false);

    const setMenuItemWeightAndSave = (id, value) => {
        const updated = data.menu_items.map(el =>
            el.id === id ? { ...el, weight: value } : el
        );
        setData('menu_items', updated);

        router.post(route('dashboard.updatemenu'), {
            menu_items: updated,
        }, {
            preserveScroll: true,
        });
    };

    const updateGlucose = (val, field) => {
        setActiveField({ id: field, val: val });

        const parsed = parseFloat(val);
        if (!isNaN(parsed) && !val.endsWith('.')) {
            const gls = ['glucose1', 'glucose2', 'ouv'];
            if (gls.includes(field)) {
                const newGl = new Glucose();
                const config = formGlConfig();
                newGl.setVal(val, config);
                switch (field) {
                    case 'glucose1':setGlucose1(newGl);
                        break;
                    case 'glucose2': setGlucose2(newGl);
                        break;
                    case 'ouv': setOUV(newGl);
                        break;
                }
            }
        }
    };

    const formatGlucose = (val, field) => {
        const nextEating = {...data.eating};
        const parsed = parseFloat(val);
        if (!isNaN(parsed) && !val.endsWith('.')) {
            const gls = ['glucose1', 'glucose2', 'ouv'];
            if (gls.includes(field)) {
                const newGl = new Glucose();
                newGl.setVal(val, formGlConfig());
                switch (field) {
                    case 'glucose1':setGlucose1(newGl);
                        break;
                    case 'glucose2': setGlucose2(newGl);
                        break;
                    case 'ouv': setOUV(newGl);
                        break;
                }
                const eatingKey = field === 'glucose1' ? 'gl1'
                    : field === 'glucose2' ? 'gl2'
                    : 'k3';
                nextEating[eatingKey] = newGl.val;
                setData('eating', nextEating);
            }
        }
        setActiveField({ id: null, val: '' });

        //here we can send the changes to back
        router.post(route('dashboard.updatefactors'), {
            factor: {
                k1: nextEating.k1,
                k2: nextEating.k2,
                k3: nextEating.k3,
                gl1: nextEating.gl1,
                gl2: nextEating.gl2,
                be: nextEating.be,
            },
        }, {
            preserveScroll: true,
        });
    };

    const parseDec = (s) => parseFloat(String(s).replace(',', '.'));
    const formatDec = (n, fractions) =>
        Number(n).toFixed(fractions).replace('.', ',');

    const updateFactor = (val, field) => {
        setActiveField({ id: field, val: val });
    };

    const formatFactor = (val, field) => {
        const nextEating = {...data.eating};
        const parsed = parseDec(val);
        if (!isNaN(parsed) && !/[.,]$/.test(val)) {
            let fractions = 0;
            if (field === 'k1' || field === 'k2') {
                fractions = 2;
            }
            nextEating[field] = Number(parsed.toFixed(fractions));
            setData('eating', nextEating);
        }
        //release latch
        setActiveField({ id: null, val: '' });

        //here we can send the changes to back
        router.post(route('dashboard.updatefactors'), {
            factor: {
                k1: nextEating.k1,
                k2: nextEating.k2,
                k3: nextEating.k3,
                gl1: nextEating.gl1,
                gl2: nextEating.gl2,
                be: nextEating.be,
            },
        }, {
            preserveScroll: true,
        });
    };

    const valGlucose1 = activeField.id === 'glucose1' ? activeField.val : glucose1.getView(formGlConfig());
    const valGlucose2 = activeField.id === 'glucose2' ? activeField.val : glucose2.getView(formGlConfig());
    const valOUV = activeField.id === 'ouv' ? activeField.val : ouv.getView({...formGlConfig(), precision: 2});

    const valK1 = activeField.id === 'k1' ? activeField.val : formatDec(data.eating.k1, 2);
    const valK2 = activeField.id === 'k2' ? activeField.val : formatDec(data.eating.k2, 2);
    const valBE = activeField.id === 'be' ? activeField.val : formatDec(data.eating.be, 0);

    const calculateMenu = () => {
        //first find out the summarized product
        const product = new MenuProduct('',0,0,0,0,0,50,0);
        //Now add all products to it
        data.menu_items.forEach(i => {
            const pr = new MenuProduct(i.name, i.id, i.weight, i.prot, i.fat, i.carb, i.gi, 0);
            product.addProduct(pr);
        });

        //Okay now we can calculate Dose
        const dose = new Dose(product, data.eating);

        return {
            dose: dose,
            product: product
        };
    };

    const calculation = calculateMenu();

    return (
        <div className="menu-pane">
            <div className="menu-pane__actions">
                <Tooltip text={__('create_product')}>
                <div className="menu-pane__actions__plus btn"><CiCirclePlus /></div>
                </Tooltip>
                <Tooltip text={__('record_diary')}>
                <div className="menu-pane__actions__diary btn"><GoPencil /></div>
                </Tooltip>
                <div className="menu-pane__actions__counter">0+839 / 1800</div>
                <Tooltip text={__('trash_menu')}>
                    <div className="menu-pane__actions__trash btn" onClick={clearMenu}><CiTrash /></div>
                </Tooltip>
            </div>
            <div className="menu-pane__menu">
                {(data.menu_items ?? []).map(item => {
                    const product = new MenuProduct(item.name, item.id, item.weight,
                        item.prot, item.fat, item.carb, item.gi, 0);

                    const info_pieces = Object.entries(mask_arr).map(([mask]) => {
                        const {val, precision} = valueCalculator(item, mask, settings, factor);
                        return (
                            <InfoPiece
                                key={`piece_${item.id}_${mask}`}
                                title={mask}
                                value={val}
                                precision={precision}
                            />
                        );
                    });

                    return (
                        <div className="menu-item" key={item.id}>
                            <div className="menu-item__name">{product.name}</div>
                            <div className="menu-item__info">
                                {info_pieces}
                            </div>
                            <div className="menu-item__weight">
                                <CalculableInput
                                    valueIn={product.weight}
                                    setHandler={(val) => setMenuItemWeightAndSave(item.id, val)}
                                />
                            </div>
                            <div className="menu-item__close btn"
                                 onClick={() => {deleteItem(item.id)}}
                            >X</div>
                        </div>
                    );
                })}

                <div className="menu-pane__factors">
                    {factorOptions.length > 0 && (
                    <div className="menu-pane__factors__select">
                        <select className="factors-selector"
                            onChange={(e) => {
                                const selected = factorOptions.find(f => f.id == e.target.value);
                                if (!selected) return;
                                applyFactorToEating(selected);
                            }}
                            value={currentFactor?.id ?? ''}
                        >
                            {factorOptions.map((f) => {
                                 const gl = new Glucose(f.k3);
                                 return (
                                    <option key={f.id} value={f.id}>{f.time} k1={f.k1} k2={f.k2} OUV={gl.getView(formGlConfig())}</option>
                                 );
                            })}
                        </select>
                    </div>
                    )}
                    <div className="menu-pane__factors__k1 factor">
                        <label htmlFor="factors-k1">{__('k1')}</label>
                        <input
                            id="factors-k1"
                            name="k1"
                            value={valK1}
                            onFocus={(e) => e.target.select()}
                            onChange={(e) => updateFactor(e.target.value, e.target.name)}
                            onBlur={(e) => formatFactor(e.target.value, e.target.name)}
                        />
                    </div>
                    <div className="menu-pane__factors__k2 factor">
                        <label htmlFor="factors-k2">{__('k2')}</label>
                        <input id="factors-k2"
                               name="k2"
                               value={valK2}
                               onFocus={(e) => e.target.select()}
                               onChange={(e) => updateFactor(e.target.value, e.target.name)}
                               onBlur={(e) => formatFactor(e.target.value, e.target.name)}
                           />
                    </div>
                    <GlucoseInput
                        className="menu-pane__factors__k3 factor"
                        id="factors-k3"
                        field="ouv"
                        label={__('ouv')}
                        value={valOUV}
                        onChange={(v) => updateGlucose(v, 'ouv')}
                        onBlur={(v) => formatGlucose(v, 'ouv')}
                    />
                    <GlucoseInput
                        className="menu-pane__factors__gl1 factor"
                        id="factors-gl1"
                        field="glucose1"
                        label={__('gl1')}
                        value={valGlucose1}
                        onChange={(v) => updateGlucose(v, 'glucose1')}
                        onBlur={(v) => formatGlucose(v, 'glucose1')}
                    />
                    <GlucoseInput
                        className="menu-pane__factors__gl2 factor"
                        id="factors-gl2"
                        field="glucose2"
                        label={__('gl2')}
                        value={valGlucose2}
                        onChange={(v) => updateGlucose(v, 'glucose2')}
                        onBlur={(v) => formatGlucose(v, 'glucose2')}
                    />
                    <div className="menu-pane__factors__be factor">
                        <label htmlFor="factors-be">{__('be')}</label>
                        <input id="factors-be"
                               name="be"
                               value={valBE}
                               onFocus={(e) => e.target.select()}
                               onChange={(e) => updateFactor(e.target.value, e.target.name)}
                               onBlur={(e) => formatFactor(e.target.value, e.target.name)}
                        />
                    </div>
                    <div className="menu-pane__factors__results">
                        <div className="factors__results__prot results-piece">
                            <div className="factors__results__prot_lbl results-piece__lbl">{__('p')}</div>
                            <div className="factors__results__prot_vl results-piece__vl">{formatDec(calculation.product.getProt(),0)}</div>
                        </div>
                        <div className="factors__results__fat results-piece">
                            <div className="factors__results__fat_lbl results-piece__lbl">{__('f')}</div>
                            <div className="factors__results__fat_vl results-piece__vl">{formatDec(calculation.product.getFat(),0)}</div>
                        </div>
                        <div className="factors__results__carb results-piece">
                            <div className="factors__results__carb_lbl results-piece__lbl">{__('c')}</div>
                            <div className="factors__results__carb_vl results-piece__vl">{formatDec(calculation.product.getCarb(),0)}</div>
                        </div>
                        <div className="factors__results__gi results-piece">
                            <div className="factors__results__gi_lbl results-piece__lbl">{__('gi')}</div>
                            <div className="factors__results__gi_vl results-piece__vl">{calculation.product.gi}</div>
                        </div>
                        <div className="factors__results__calorie results-piece">
                            <div className="factors__results__calorie_lbl results-piece__lbl">{__('kcal')}</div>
                            <div className="factors__results__calorie_vl results-piece__vl">{formatDec(calculation.product.getCalor(),0)}</div>
                        </div>
                        <div className="factors__results__gl results-piece">
                            <div className="factors__results__gl_lbl results-piece__lbl">{__('gl')}</div>
                            <div className="factors__results__gl_vl results-piece__vl">{formatDec(calculation.product.getGLIndx(),0)}</div>
                        </div>
                    </div>
                    <div
                        className={`menu-pane__factors__grand-total${showDetails ? ' is-open' : ''}`}
                        onClick={() => setShowDetails(!showDetails)}
                    >
                        <div className="doses-sign">+</div>
                        <div className="doses-quick"><span className="dose-label">{__('dose_quick')}</span>{formatDec(calculation.dose.getQuick(),1)}</div>
                        <div className="doses-slow"><span className="dose-label">{__('dose_slow')}</span>{formatDec(calculation.dose.getSlow(),1)}</div>
                        <div className="doses-sum"><span className="dose-label">=&nbsp;{__('dose_sum')}</span>{formatDec(calculation.dose.getWholeD(),1)}</div>

                        <div className="doses-detail-quick1"><span className="dose-label">({__('dose_dps')}</span>{formatDec(calculation.dose.getDPS(),1)}
                            <span className="dose-label"> + {__('dose_q_carb')}</span>{formatDec(calculation.dose.getQCarbD(),1)}
                            <span className="dose-label">)</span></div>
                        <div className="doses-detail-slow1"><span className="dose-label">({__('dose_slow')}</span>{formatDec(calculation.dose.getSlCarbD(),1)}
                            <span className="dose-label"> + {__('dose_sl_prot')}</span>{formatDec(calculation.dose.getSlow(),1)}
                            <span className="dose-label">)</span></div>

                        <div className="doses-detail-quick2"><span className="dose-label">(dps</span>{formatDec(calculation.dose.getDPS(),1)}
                            <span className="dose-label"> + {__('dose_carb')}</span>{formatDec(calculation.dose.getCarbD(),1)}
                            <span className="dose-label">)</span>
                        </div>
                        <div className="doses-detail-slow2"><span className="dose-label">({__('dose_prot_fat')}</span>{formatDec(calculation.dose.getProtFatD(),1)})
                        </div>
                    </div>
                    <div className="menu-pane__factors__scale">
                        <Scale prot={calculation.product.getProt()} fat={calculation.product.getFat()} carb={calculation.product.getCarb()}/>
                    </div>
                </div>
            </div>
        </div>
    );
}

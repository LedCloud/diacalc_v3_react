import InputTwoLines from "@/Components/InputTwoLines.jsx";
import Pane from "@/Components/Pane.jsx";
import React, {useState} from "react";
import Glucose from "@/Classes/Glucose.js";
import {useTrans} from "@/Hooks/useTrans.jsx";

export default function GlucoseTabContent(
    {allSettings, setAllSettings, activeTab, menuMasks, errors, className = ''}
) {

    const [activeField, setActiveField] = useState({ id: null, val: '' });
    const [targetGl, setTargetGl] = useState(new Glucose(allSettings.target));
    const [lowGl, setLowGl] = useState(new Glucose(allSettings.low_level));
    const [highGl, setHighGl] = useState(new Glucose(allSettings.high_level));

    const tabId = 'glucose';

    const { __ } = useTrans();

    const setMmol = (val) => {
        setAllSettings({...allSettings, is_mmol: val === '1'});
    };

    const setPlasma = (val) => {
        setAllSettings({...allSettings, is_plasma: val === '1'});
    };

    const isMmol = () => {
        return Boolean(+allSettings.is_mmol);
    }

    const isPlasma = () => {
        return Boolean(+allSettings.is_plasma);
    }

    const formGlConfig = () => {
        return {
            mmol: isMmol(),
            plasma: isPlasma()
        }
    };

    const updateGlucoseLevel = (val, field) => {
        setActiveField({ id: field, val: val });

        const parsed = parseFloat(val);
        if (!isNaN(parsed) && !val.endsWith('.')) {
            const newGl = new Glucose();
            const config = formGlConfig();
            newGl.setVal(val, config);
            if (field) {
                switch (field) {
                    case 'target_gl':setTargetGl(newGl);
                        break;
                    case 'low_level_gl': setLowGl(newGl);
                        break;
                    case 'high_level_gl': setHighGl(newGl);
                        break;
                }
            }
        }
    };

    const formatGlucoseLevel = (val, field) => {
        const parsed = parseFloat(val);
        if (!isNaN(parsed) && !val.endsWith('.')) {
            const newGl = new Glucose();
            newGl.setVal(val, formGlConfig());
            if (field) {
                switch (field) {
                    case 'target_gl':setTargetGl(newGl);
                        break;
                    case 'low_level_gl': setLowGl(newGl);
                        break;
                    case 'high_level_gl': setHighGl(newGl);
                        break;
                }
            }
        }
        setActiveField({ id: null, val: '' });
    };

    const valTarget = activeField.id === 'target_gl' ? activeField.val : targetGl.getView(formGlConfig());
    const valLow = activeField.id === 'low_level_gl' ? activeField.val : lowGl.getView(formGlConfig());
    const valHigh = activeField.id === 'high_level_gl' ? activeField.val : highGl.getView(formGlConfig());

    return (
        <div className={`tab-pane ${className} ${activeTab === tabId ? 'active' : ''}`}>
            <Pane header={__('glucose')}
                  className={`glucose_pane tab-pane panes__pane ${activeTab === tabId ? 'active' : ''}`}>
                <fieldset>
                    <div className="horizontal-group checkbox-group">
                        <label htmlFor="whole">
                            <input id="whole"
                                   name="is_plasma"
                                   value="0"
                                   type="radio"
                                   checked={Boolean(!(+allSettings.is_plasma))}
                                   onChange={(e) => setPlasma(e.target.value)}
                            />{__('whole')}</label>
                    </div>
                    <div className="horizontal-group checkbox-group">
                        <label htmlFor="plasma">
                            <input id="plasma"
                                   name="is_plasma"
                                   value="1"
                                   type="radio"
                                   checked={Boolean(+allSettings.is_plasma)}
                                   onChange={(e) => setPlasma(e.target.value)}
                            />{__('plasma')}</label>
                    </div>
                </fieldset>

                <fieldset>
                    <div className="horizontal-group checkbox-group">
                        <label htmlFor="mmol">
                            <input id="mmol"
                                   name="is_mmol"
                                   value="1"
                                   type="radio"
                                   checked={Boolean(+allSettings.is_mmol)}
                                   onChange={(e) => setMmol(e.target.value)}
                            />{__('mmol')}</label>
                    </div>
                    <div className="horizontal-group checkbox-group">
                        <label htmlFor="mgdl">
                            <input id="mgdl"
                                   name="is_mmol"
                                   value="0"
                                   type="radio"
                                   checked={Boolean(!(+allSettings.is_mmol))}
                                   onChange={(e) => setMmol(e.target.value)}
                            />{__('mgdl')}</label>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>{__('glucose_levels')}</legend>
                    {/* here we will have raw values but the names should correspond the settings names */}
                    <input type="hidden" name="target" value={targetGl.val}/>
                    <input type="hidden" name="low_level" value={lowGl.val}/>
                    <input type="hidden" name="high_level" value={highGl.val}/>
                    <div className="field">
                        <InputTwoLines value={valTarget}
                                       name="target_gl"
                                       id="target"
                                       label={__('target')}
                                       onChange={updateGlucoseLevel}
                                       onBlur={formatGlucoseLevel}
                        />
                        {errors.target && <div className="validation-error">{errors.target}</div>}
                    </div>
                    <div className="field">
                        <InputTwoLines value={valLow}
                                       name="low_level_gl"
                                       id="low_level"
                                       label={__('low_level')}
                                       onChange={updateGlucoseLevel}
                                       onBlur={formatGlucoseLevel}
                        />
                        {errors.low_level && <div className="validation-error">{errors.low_level}</div>}
                    </div>
                    <div className="field">
                        <InputTwoLines value={valHigh}
                                       name="high_level_gl"
                                       id="high_level"
                                       label={__('high_level')}
                                       onChange={updateGlucoseLevel}
                                       onBlur={formatGlucoseLevel}
                        />
                        {errors.high_level && <div className="validation-error">{errors.high_level}</div>}
                    </div>
                </fieldset>
            </Pane>
        </div>
    );
};

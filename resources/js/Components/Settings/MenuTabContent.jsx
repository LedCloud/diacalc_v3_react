import MaskInput from "@/Components/Settings/MaskInput.jsx";
import InputTwoLines from "@/Components/InputTwoLines.jsx";
import Pane from "@/Components/Pane.jsx";
import React from "react";
import {useTrans} from "@/Hooks/useTrans.jsx";

export default function MenuTabContect({allSettings, setAllSettings, activeTab, menuMasks, errors, className = ''})
{
    const tabId = 'menu';
    const setMenuInfo = (val) => {
        setAllSettings({...allSettings, menu_info: val});
    };

    const changeCaloryLimit = (val) => {
        setAllSettings({...allSettings, calorie_limit: val});
    };

    const setRoundTo = (val) => {
        setAllSettings({...allSettings, round_to: val});
    };

    const { __ } = useTrans();

    return (
        <div className={`tab-pane ${className} ${activeTab === tabId ? 'active' : ''}`}>
            <Pane header={__('menu')}
                  className={`menu_pane tab-pane panes__pane ${activeTab === tabId ? 'active' : ''}`}>
                <input type="hidden" name="menu_info" value={allSettings.menu_info}/>
                <fieldset>
                    <legend>{__('menu_info')}</legend>
                    <MaskInput name="prot" strName={__('proteins')} masks={menuMasks}
                               menuInfo={allSettings.menu_info}
                               handlerInfo={setMenuInfo}/>

                    <MaskInput name="fat"
                               strName={__('fats')}
                               masks={menuMasks} menuInfo={allSettings.menu_info} handlerInfo={setMenuInfo}/>

                    <MaskInput name="carb"
                               strName={__('carbs')}
                               masks={menuMasks} menuInfo={allSettings.menu_info} handlerInfo={setMenuInfo}/>

                    <MaskInput name="be"
                               strName={__('be')}
                               masks={menuMasks} menuInfo={allSettings.menu_info} handlerInfo={setMenuInfo}/>

                    <MaskInput name="dose"
                               strName={__('dose')}
                               masks={menuMasks} menuInfo={allSettings.menu_info} handlerInfo={setMenuInfo}/>

                    <MaskInput name="gi"
                               strName={__('gi')}
                               masks={menuMasks} menuInfo={allSettings.menu_info} handlerInfo={setMenuInfo}/>

                    <MaskInput name="gl"
                               strName={__('gl')}
                               masks={menuMasks} menuInfo={allSettings.menu_info} handlerInfo={setMenuInfo}/>

                    <MaskInput name="calorie"
                               strName={__('calories')}
                               masks={menuMasks} menuInfo={allSettings.menu_info} handlerInfo={setMenuInfo}/>
                </fieldset>
                <fieldset>
                    <div className="field">
                        <label htmlFor="round-to">{__('round')}</label>
                        <select id="round-to"
                                name="round_to"
                                value={allSettings.round_to}
                                onChange={(e) => {
                                    setRoundTo(+e.target.value)
                                }}
                        >
                            <option value="0">{__('round_int')}</option>
                            <option value="1">{__('round_half')}</option>
                            <option value="2">{__('round_quarter')}</option>
                        </select>
                    </div>
                    <div className="field">
                        <InputTwoLines value={allSettings.calorie_limit}
                                       label={__('calorie_limit')}
                                       name="calorie_limit"
                                       onChange={changeCaloryLimit}
                        />
                        {errors.calorie_limit && <div className="validation-error">{errors.calorie_limit}</div>}
                    </div>
                </fieldset>
            </Pane>
        </div>
    );
}

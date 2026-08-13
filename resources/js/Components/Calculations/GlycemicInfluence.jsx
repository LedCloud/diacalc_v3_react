import React, { useState } from "react";
import Glucose from "@/Classes/Glucose.js";
import InputOneLine from "@/Components/InputOneLine.jsx";
import {useTrans} from "@/Hooks/useTrans.jsx";

const GlycemicInfluence = ({user}) => {
    const { __ } = useTrans();
    const [activeField, setActiveField] = useState({ id: null, val: '' });
    const [ouvGl, setOuvGl] = useState(new Glucose(2.0));
    const [k1, setK1] = useState('1.00');

    const [mmol, setMmol] = useState('mmol');
    const [plasma, setPlasma] = useState('whole');

    const updateOuv = (val) => {
        setActiveField({ id: 'ouv', val: val });

        const parsed = parseFloat(val);
        if (!isNaN(parsed) && !val.endsWith('.')) {
            const newGl = new Glucose();
            const config = {
                mmol: mmol === 'mmol',
                plasma: plasma === 'plasma',
            };
            newGl.setVal(val, config);
            setOuvGl(newGl);
        }
    };

    const formatOuv = (val) => {
        const parsed = parseFloat(val);
        if (!isNaN(parsed)) {
            const newGl = new Glucose();
            newGl.setVal(val, buildConfig());
            setOuvGl(newGl);
        }
        setActiveField({ id: null, val: '' });
    };

    const formatK1 = (val) => {
        const parsed = parseFloat(val);

        if (!isNaN(parsed)) {
            const formatted = parsed.toFixed(2);
            setK1(formatted);
        }
    };

    const buildConfig =  () => {
        return {
            mmol: mmol === 'mmol',
            plasma: plasma === 'plasma',
            precision: mmol === 'mmol' ? 2 : 0,
        };
    };

    const calcInsulinInfluence = (val) =>
    {
        const config = buildConfig()
        const gl = new Glucose(5.6);
        gl.setVal(+ouv * +val, config)
        return gl.getView(config);
    };

    const calcCarboInfluence = (val) =>
    {
        const gl = new Glucose(5.6);
        gl.setVal(ouv, buildConfig());
        const vl = +val * gl.val * +k1 / +user.be;
        return vl.toFixed(mmol === 'mmol' ? 2 : 0);
    };

    const ouv = activeField.id === 'ouv' ? activeField.val : ouvGl.getView(buildConfig());

    return (
        <div className="influence__panel">
            <div className="influence__panel__inputs">
                <div className="influence__panel__inputs_top">
                    <InputOneLine id='ouv'
                                  label={__('ouv')}
                                  value={ouv}
                                  onChange={updateOuv}
                                  onBlur={formatOuv}/>

                    <InputOneLine id='k1'
                                  label={__('k1')}
                                  value={k1}
                                  onChange={setK1}
                                  onBlur={formatK1}/>
                </div>
                <div className="influence__panel__inputs_bottom">
                    <div>
                        <div className="horizontal-group checkbox-group">
                            <label htmlFor="mmol">
                                <input id="mmol"
                                       name="mmolmgdl"
                                       value="mmol"
                                       type="radio"
                                       checked={mmol === 'mmol'}
                                       onChange={(e) => setMmol(e.target.value)}
                                />{__('mmol')}</label>
                        </div>
                        <div className="horizontal-group checkbox-group">
                            <label htmlFor="mgdl">
                                <input id="mgdl"
                                       name="mmolmgdl"
                                       value="mgdl"
                                       type="radio"
                                       checked={mmol === 'mgdl'}
                                       onChange={(e) => setMmol(e.target.value)}
                                />{__('mgdl')}</label>
                        </div>
                    </div>
                    <div>
                        <div className="horizontal-group checkbox-group">
                            <label htmlFor="whole">
                                <input id="whole"
                                       name="wholeplasma"
                                       value="whole"
                                       type="radio"
                                       checked={plasma === 'whole'}
                                       onChange={(e) => setPlasma(e.target.value)}
                                />{__('whole')}</label>
                        </div>
                        <div className="horizontal-group checkbox-group">
                            <label htmlFor="plasma">
                                <input id="plasma"
                                       name="wholeplasma"
                                       value="plasma"
                                       type="radio"
                                       checked={plasma === 'plasma'}
                                       onChange={(e) => setPlasma(e.target.value)}
                                />{__('plasma')}</label>
                        </div>
                    </div>
                </div>
            </div>
            <div className="influence__panel__results">
                <div>
                    <table className="table-auto md:table-fixed">
                        <thead>
                        <tr>
                            <th className="text-center pr-2 pl-2">{__("insulin_units")}</th>
                            <th className="text-center pr-2 pl-2">
                                {plasma === 'plasma' ? __('plasma') : __('whole') }, {mmol === 'mmol' ? __('mmol') : __('mgdl') }
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        {[0.1, 0.2, 0.25, 0.5].map((value) => (
                            <tr key={value} className="even:bg-slate-50">
                                <td className="text-center">{value}</td>
                                <td className="text-center">{calcInsulinInfluence(value)}</td>
                            </tr>
                        ))}
                        </tbody>
                    </table>
                </div>
                <div>
                    <table className="table-auto md:table-fixed">
                        <thead>
                        <tr>
                            <th className="text-center pr-2 pl-2">{__('carb_gr')}</th>
                            <th className="text-center pr-2 pl-2">
                                {plasma === 'plasma' ? __('plasma') : __('whole') }, {mmol === 'mmol' ? __('mmol') : __('mgdl') }
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        {[1, 2, 5, 10].map((value) => (
                            <tr key={value} className="even:bg-slate-50">
                                <td className="text-center">{value}</td>
                                <td className="text-center">{calcCarboInfluence(value)}</td>
                            </tr>
                        ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );
};

export default GlycemicInfluence;

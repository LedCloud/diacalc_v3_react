import React, {useState} from "react";
import InputOneLine from "@/Components/InputOneLine.jsx";
import {useTrans} from "@/Hooks/useTrans.jsx";

export default function BMICorrection() {
    const { __ } = useTrans();
    const [weight, setWeight] = useState('60');
    const [height, setHeight] = useState('170');
    const [age, setAge] = useState('40');
    const [targetWeight, setTargetWeight] = useState('60');
    const [period, setPeriod] = useState('12');
    const [sex, setSex] = useState('male');
    const [steps, setSteps] = useState('7000');

    const calcBmi = () => {
        const parsedW = parseFloat(weight);
        const parsedH = parseFloat(height);
        if (isNaN(parsedW) || isNaN(parsedH)) {
            return '---';
        }
        return (10000 * parsedW /( parsedH * parsedH)).toFixed(1);
    };
    const bmi = calcBmi();

    const stepsToActivity = () => {
        const parsed = parseFloat(steps);
        if (isNaN(parsed))
            return 0;
        return  (+steps - 5000)/400 + 110;
    }
    const calculateActivityType = () => {
        const intv = +steps;
        if (isNaN(intv))
            return;
        if (intv > 25000) {
            return __('extremal');
        } else if (intv > 15000) {
            return __('high');
        } else if (intv > 7000) {
            return __('moderate');
        } else if (intv > 5000) {
            return __('light');
        }
        return __('sedentary');
    };

    const activity_type = calculateActivityType();

    const calcCalorsNeeded = ({weight, height, age, male, activity}) => {
        if (male){
            var s = 5;
        }else{
            s = -161;
        }
        const basal = 9.99 * weight + 6.25 * height - 4.92 * age + s;
        return (basal * activity / 100).toFixed(0);
    }

    const getCalor2Loose = (weight, targetWeight) => {
        const weightChange = +weight - +targetWeight;

        if (weightChange >= 0){
            return weightChange * 7716;
        }

        return weightChange * 11023;
    }

    function * genMonths(min){
        let index = min;
        while (index < 24) {
            index++;
            yield index;
        }
    }
    const calculateCalories = () => {
        //var steps = 5000+2000*(val-110)/5;
        //(steps - 5000)/400 = val - 110;
        //(steps - 5000)/400 + 110 = val <= for 7000 we get 115
        const config = {weight: weight, height: height, age: age, male: sex === 'male', activity: stepsToActivity()};

        const calorieCurrent = calcCalorsNeeded(config);
        config.weight = targetWeight;
        let calorieTarget = calcCalorsNeeded(config);
        //do no allow to starve
        if (calorieTarget <= 1200) {
            calorieTarget = 1201;
        }
        const calorieToLoose = getCalor2Loose(weight, targetWeight);
        let minMonths;
        if (calorieToLoose > 0){//loose weight
            minMonths = Math.ceil(calorieToLoose/( (calorieTarget - 1200) * 30 ));
        } else {
            //gain weight
            let addon = 0;
            if (calorieTarget < 5500){
                addon = 5500 - calorieTarget;
                minMonths = Math.ceil(-calorieToLoose/(addon*30));
            }
            else{
                minMonths = 0;
            }
        }
        let months_range = [];

        let currentSelection = +period;

        if (minMonths>24 && minMonths!==0){
            //create an only one option
            months_range.push(minMonths);
            currentSelection = minMonths;
        } else {
            //generate months and select the nearest that was previously selected
            months_range = [...genMonths(minMonths)];
            if (!months_range.includes(currentSelection)) {
                if (currentSelection < Math.min(...months_range)) {
                    currentSelection = Math.min(...months_range);
                } else if (currentSelection > Math.max(...months_range)) {
                    currentSelection = Math.max(...months_range);
                }
            }
        }

        //Теперь можно произвести расчет калорийности для снижения.
        const calorieToGetTarget = (calorieTarget - calorieToLoose/(currentSelection * 30)).toFixed(0);

        return [calorieCurrent, calorieTarget, calorieToGetTarget, months_range, currentSelection];
    };

    const [calorieCurrent, calorieTarget, calorieToGetTarget, months_range, periodSelection] = calculateCalories();

    if (periodSelection != period) {
        setPeriod('' + periodSelection);
    }

    const periods = months_range;

    return (
        <div className="bmi-correction__panel">
            <div className="bmi_panel">
                <div className="bmi_panel__bmi-label">{__('bmi')}</div>
                <div className="bmi_panel__bmi-result">{bmi}</div>
                <div className="bmi_panel__weight-label">{__('weight_kg')}:</div>
                <input
                    className="bmi_panel__weight-input"
                    value={weight}
                    onFocus={(e) => e.target.select()}
                    onChange={(e) => setWeight(e.target.value)}/>
                <div className="bmi_panel__height-label">{__('height_cm')}:</div>
                <input
                    className="bmi_panel__height-input"
                    value={height}
                    onFocus={(e) => e.target.select()}
                    onChange={(e) => setHeight(e.target.value)}/>

                <div className="bmi_panel__sex-label bmi_panel__height-label">{__('your_sex')}</div>
                <div className="bmi_panel__sex-input bmi_panel__height-input">
                    <div className="sex-group__radio checkbox-group">
                        <label htmlFor="sexMale">
                            <input id="sexMale"
                                   name="sex"
                                   value="male"
                                   type="radio"
                                   checked={sex === 'male'}
                                   onChange={(e) => setSex(e.target.value)}
                            />{__('male')}</label>
                    </div>
                    <div className="sex-group__radio checkbox-group">
                        <label htmlFor="sexFemale">
                            <input id="sexFemale"
                                   name="sex"
                                   value="female"
                                   type="radio"
                                   checked={sex === 'female'}
                                   onChange={(e) => setSex(e.target.value)}
                            />{__('female')}</label>
                    </div>
                </div>

                <div className="bmi_panel__note"
                     dangerouslySetInnerHTML={{__html: __('bmi_children_notice')}} />
            </div>
            <div className="target_panel">
                <InputOneLine value={age}
                              onChange={setAge}
                              onBlur={setAge}
                              label={__('age')}
                              className="target_panel__age-input"
                />
                <InputOneLine
                    value={targetWeight}
                    onChange={setTargetWeight}
                    onBlur={setTargetWeight}
                    label={__('target_weight')}
                    className="target_panel__weight-input"
                />
                <div className="target_panel__period">
                    <label>{__('correction_period')}</label>
                    {/* add calculation based on age, targetWeight, sex and activity */}
                    <select value={period} onChange={(e) => setPeriod(e.target.value)}>
                        {periods.map((value) => (
                            <option key={value}>{value}</option>
                        ))}
                    </select>
                </div>
            </div>
            <div className="results_panel">

                <fieldset className="results_panel__activity">
                    <div>
                        <label htmlFor="activity-selector">
                            {__('activity')}: <span className="activity__type">{activity_type}</span> &bull; {__('steps_count')}:
                            ≈<span className="activity__steps">{steps}</span>
                        </label>
                        <input id="activity-selector"
                               className="activity-selector"
                               type="range" min="5000" max="33000" step="2000" value={steps}
                               onChange={(e) => setSteps(e.target.value)}/>
                    </div>
                    <div className="results_panel__results">
                        <div className="results_panel__results__header">{__('calories_a_day')}</div>
                        <div className="results_panel__results__result">
                            <div>{__('for_current_weight')}</div>
                            <div>{calorieCurrent}</div>
                        </div>
                        <div className="results_panel__results__result">
                            <div>{__('for_target_weight')}</div>
                            <div>{calorieTarget}</div>
                        </div>
                        <div className="results_panel__results__result">
                            <div>{__('to_gain_target_weight')}</div>
                            <div>{calorieToGetTarget}</div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    );
}

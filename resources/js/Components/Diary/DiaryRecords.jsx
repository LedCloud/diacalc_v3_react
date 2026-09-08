import {usePage} from "@inertiajs/react";
import {useTrans} from "@/Hooks/useTrans.jsx";
import Glucose from "@/Classes/Glucose.js";
import MenuProduct from "@/Classes/MenuProduct.js";
import Factor from "@/Classes/Factor.js";
import Dose from "@/Classes/Dose.js";

const TYPE_COMMENT = 1;
const TYPE_GLUCOSE = 2;
const TYPE_MEAL = 3;

function formatDec(n, fractions) {
    const parsed = parseFloat(n);
    if (Number.isNaN(parsed)) {
        return n;
    }
    return parsed.toFixed(fractions);
}

function formatTime(timestamp, locale) {
    const date = new Date(String(timestamp).replace(' ', 'T'));
    return date.toLocaleTimeString(locale, { hour: '2-digit', minute: '2-digit' });
}

function formatDay(dateValue, locale) {
    const [year, month, day] = String(dateValue).split('-').map(Number);
    return new Date(year, month - 1, day).toLocaleDateString(locale);
}

const isMmol = (settings) => {
    return Boolean(settings.is_mmol);
}

const isPlasma = (settings) => {
    return Boolean(settings.is_plasma);
}

const formGlConfig = (settings) => {
    return {
        mmol: isMmol(settings),
        plasma: isPlasma(settings)
    }
};

function glucoseView(value, settings) {
    const glucose = new Glucose(value);
    return glucose.getView(formGlConfig(settings));
}

function glucoseTone(value, settings) {
    const amount = new Glucose(value).val;
    const low = Number(settings.low_level ?? 4);
    const high = Number(settings.high_level ?? 8);
    if (amount < low) {
        return 'low';
    }
    if (amount > high) {
        return 'high';
    }
    return 'normal';
}

function mealProduct(meal) {
    return new MenuProduct(
        '',
        -1,
        meal.weight,
        meal.prot,
        meal.fat,
        meal.carb,
        meal.gi,
        -1,
    );
}

export default function DiaryRecords({ days = [], loading = false }) {
    const { __ } = useTrans();
    const { settings = {}, menu_masks = {} } = usePage().props;
    const locale = typeof document !== 'undefined'
        ? document.documentElement.lang || undefined
        : undefined;
    const menuInfo = Number(settings.menu_info ?? 0);
    const calorieLimit = Number(settings.calory_limit ?? 0);
    const breadUnit = Number(settings.be ?? 10);

    if (!days.length) {
        return (
            <div className="diary-records__empty">
                {loading ? '' : __('no_records')}
            </div>
        );
    }

    return (
        <div className="diary-records">
            {days.map((day) => {
                const showCalories = calorieLimit > 0 && day.calories > 0;
                const calorieTone = day.calories >= calorieLimit ? 'warning' : 'normal';

                return (
                    <section key={day.date} className="diary-records__day">
                        <h4 className="diary-records__date">{formatDay(day.date, locale)}</h4>
                        <ul className="diary-records__list">
                            {day.records.map((record) => (
                                <li key={record.id}>
                                    <DiaryRecordRow
                                        record={record}
                                        settings={settings}
                                        menuInfo={menuInfo}
                                        menuMasks={menu_masks}
                                        breadUnit={breadUnit}
                                        locale={locale}
                                        __={__}
                                    />
                                </li>
                            ))}
                            {showCalories && (
                                <li>
                                    <div className={`diary-event diary-event--${calorieTone} diary-event--calories`}>
                                        {__('eaten')}: <strong>{Math.round(day.calories)}</strong>
                                        {' '}
                                        {__('limit')}: <strong>{calorieLimit}</strong>
                                    </div>
                                </li>
                            )}
                        </ul>
                    </section>
                );
            })}
        </div>
    );
}

function DiaryRecordRow({ record, settings, menuInfo, menuMasks, breadUnit, locale, __ }) {
    const time = formatTime(record.timestamp, locale);
    const comment = record.comment ?? '';

    if (record.type === TYPE_COMMENT) {
        return (
            <div className="diary-event">
                <strong>{time}</strong> {comment}
            </div>
        );
    }

    if (record.type === TYPE_GLUCOSE) {
        const tone = glucoseTone(record.glucose, settings);
        return (
            <div className={`diary-event diary-event--${tone}`}>
                <strong>
                    {time} {__('bg')} {glucoseView(record.glucose, settings)}
                </strong>
                {comment ? ` ${comment}` : ''}
            </div>
        );
    }

    if (record.type !== TYPE_MEAL || !record.meal) {
        return (
            <div className="diary-event">
                <strong>{time}</strong> {comment}
            </div>
        );
    }

    const meal = record.meal;
    const product = mealProduct(meal);
    const factor = new Factor(meal.k1, meal.k2, meal.k3, meal.gl1, meal.gl2, breadUnit);
    const dose = new Dose(product, factor);
    const dps = dose.getDPS();
    const tone = glucoseTone(meal.gl1, settings);
    const bits = [];

    if (menuInfo & (menuMasks.prot ?? 1)) {
        bits.push(`${__('p')} ${formatDec(product.getProt(), 1)}`);
    }
    if (menuInfo & (menuMasks.fat ?? 2)) {
        bits.push(`${__('f')} ${formatDec(product.getFat(), 1)}`);
    }
    if (menuInfo & (menuMasks.carb ?? 4)) {
        bits.push(`${__('c')} ${formatDec(product.getCarb(), 1)}`);
    }
    if (menuInfo & (menuMasks.be ?? 8) && breadUnit > 0) {
        bits.push(`${__('be')} ${formatDec(product.getCarb() / breadUnit, 1)}`);
    }
    if (menuInfo & (menuMasks.gi ?? 32)) {
        bits.push(`${__('gi')} ${formatDec(product.gi, 0)}`);
    }
    if (menuInfo & (menuMasks.gl ?? 64)) {
        bits.push(`${__('gl')} ${formatDec(product.getGLIndx(), 0)}`);
    }
    if (menuInfo & (menuMasks.calorie ?? 128)) {
        bits.push(`${__('kcal')} ${formatDec(product.getCalor(), 0)}`);
    }

    const productNames = (meal.products ?? [])
        .map((item) => item.name)
        .filter(Boolean)
        .join(', ');

    return (
        <div className={`diary-event diary-event--${tone}`}>
            <div>
                <strong>
                    {time} {__('bg')} {glucoseView(meal.gl1, settings)}
                </strong>
                {bits.length ? ` ${bits.join(' ')}` : ''}
            </div>
            <div>
                {__('dose')} {formatDec(dose.getWholeD(), 1)}
                {dps !== 0 ? ` ${__('dps')} ${formatDec(dps, 1)}` : ''}
                {comment ? ` • ${comment}` : ''}
            </div>
            {productNames ? (
                <div className="diary-event__products">{productNames}</div>
            ) : null}
        </div>
    );
}

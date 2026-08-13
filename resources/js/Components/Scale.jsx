import {useTrans} from "@/Hooks/useTrans.jsx";

export default function Scale({prot, fat, carb})
{
    const { __ } = useTrans();

    const normalize = (val, total) => {
        const f = val / total;
        return (f * 100).toFixed(0);
    };

    const total = prot + fat + carb;

    const p = normalize(prot, total);
    const f = normalize(fat, total);
    const c = normalize(carb, total);

    return (<div className="scale-bars">
        <div className="scale-bars__prot"
             role="progressbar"
             id="ruler-prot"
             style={{width: `${p}%`}}
        >{__('prot')}:{p}%
        </div>
        <div className="scale-bars__fat progress-bar progress-bar-warning"
             role="progressbar"
             id="ruler-fat"
             style={{width: `${f}%`}}
        >{__('fat')}:{f}%
        </div>
        <div className="progress-bar scale-bars__carb"
             role="progressbar"
             id="ruler-carb"
             style={{width: `${c}%`}}
        >{__('carb')}:{c}%
        </div>
    </div>);
}

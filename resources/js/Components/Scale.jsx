import {useTrans} from "@/Hooks/useTrans.jsx";

export default function Scale({prot, fat, carb})
{
    const { __ } = useTrans();

    const normalize = (val, total) => {
        const f = val / total;
        return (f * 100).toFixed(0);
    };

    const total = prot + fat + carb;

    if (!Number.isFinite(total) || total === 0) {
        return null;
    }

    const p = normalize(prot, total);
    const f = normalize(fat, total);
    const c = normalize(carb, total);
    const caption = (pct, key) => `${Number(pct) > 10 ? `${__(key)}:` : ''}${pct}%`;

    return (<div className="scale-bars">
        <div className="scale-bars__prot"
             role="progressbar"
             id="ruler-prot"
             style={{width: `${p}%`}}
        >{caption(p, 'prot_sh')}
        </div>
        <div className="scale-bars__fat progress-bar progress-bar-warning"
             role="progressbar"
             id="ruler-fat"
             style={{width: `${f}%`}}
        >{caption(f, 'fat_sh')}
        </div>
        <div className="progress-bar scale-bars__carb"
             role="progressbar"
             id="ruler-carb"
             style={{width: `${c}%`}}
        >{caption(c, 'carb_sh')}
        </div>
    </div>);
}

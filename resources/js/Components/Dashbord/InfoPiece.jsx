import {useTrans} from "@/Hooks/useTrans.jsx";
import Tooltip from "@/Components/Tooltip.jsx";

export default function     InfoPiece({title, value, precision=1})
{
    const { __ } = useTrans();
console.log('Title in info piece', title);
    return (<>
        <span className="info-piece__title">{__(`short_${title}`)}</span>
        <Tooltip text={__(title)}>
        <span tabIndex={-1} className="info-piece__value">{parseFloat(value).toFixed(precision)}</span>
        </Tooltip>
    </>);
}

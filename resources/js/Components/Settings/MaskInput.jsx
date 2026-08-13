export default function MaskInput({name, strName, masks, menuInfo, handlerInfo}) {

    /**
     * Set bit in the bitmask
     * @param value 1 or 0
     * @param bit   bit to be set, for example 0b100
     * @param mask  current bit mask
     */
    const setBit = (value, bit, mask) => {
        //bit = 0b100 or 0b000;
        //not having a bit position in case of zero it's not possible to set the bit
        //in mask to zero
        //1 EXPECT bit is not zero find its position
        if (Boolean(bit) === false)
            throw new Error('Bit must be not zero');

        let p = 0;
        while (((bit >> p) & 1) === 0) {
            p++;
        }
        if (Boolean(value) === false) {
            //set zero
            mask  &= ~(1 << p);
        } else {
            mask |= (1 << p);
        }

        return mask;
    }

    const bit = masks[name];

    return (
        <div className="field">
            <label className="checkbox-group"
                   htmlFor={`menu-${name}`}>
                <input id={`menu-${name}`}
                       checked={Boolean(bit & menuInfo)}
                       onChange={(e) => {
                           const mask = setBit(e.target.checked, bit, menuInfo);
                           handlerInfo(mask);
                       }}
                       type="checkbox"/>{strName}</label>
        </div>
    );
}
;

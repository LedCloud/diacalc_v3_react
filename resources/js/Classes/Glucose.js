export default class Glucose {
    //Изначально храним СК в ммоль и цельной крови
    constructor(val) { this.val = parseFloat(val); }
    setVal(val, {mmol = true, plasma = false}) {
        let parsed = parseFloat(val);
        if (isNaN(parsed)) {
            parsed = 0;
        }
        this.val = parsed / this.getFactor({mmol:mmol, plasma:plasma});
    }
    getView({mmol = true, plasma = false, precision = null}) {
        if (this.val === 0) return '';
        return ''+(this.val * this.getFactor({mmol:mmol, plasma:plasma}))
            .toFixed(precision ? precision : (mmol?1:0))
            ;
    }
    getFactor({mmol = true, plasma = false}){
        return (plasma?1.12:1) * (mmol ? 1 : 18);
    };
    getHbA1c(){
        return ((this.val * 1.12 + 2.59) / 1.59).toFixed(1);
    }
    setHbA1c(val){
        let parsed = parseFloat(val);
        if (isNaN(parsed)) {
            parsed = 0;
        }
        this.val = (parsed * 1.59 - 2.59)/1.12;
    }
}

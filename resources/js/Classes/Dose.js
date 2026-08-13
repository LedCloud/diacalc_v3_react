export default class Dose {
    constructor(prod, factor) {
        this.prod = prod;
        this.factor = factor;
    }

    // ДПС (Доза на подколку / снижение)
    getDPS() {
        if (!this.factor.k3 || +this.factor.k3 === 0) {
            return 0;
        }
        console.log(this.factor, this.prod);
        return (this.factor.gl1 - this.factor.gl2) / this.factor.k3;
    }

    // Доза на быстрые углеводы
    getQCarbD() {
        return (this.prod.getQCarb() * this.factor.k1) / 10;
    }

    // Доза на медленные углеводы
    getSlCarbD() {
        return (this.prod.getSlCarb() * this.factor.k1) / 10;
    }

    // Общая доза на углеводы
    getCarbD() {
        console.log((this.prod.getCarb() * this.factor.k1) / 10);
        return (this.prod.getCarb() * this.factor.k1) / 10;
    }

    // Доза на белки и жиры (К2)
    getProtFatD() {
        console.log(
            (this.factor.k2 * this.prod.getProt() * 4) / 100 + (this.factor.k2 * this.prod.getFat() * 9) / 100
        );
        return (
            (this.factor.k2 * this.prod.getProt() * 4) / 100 +
            (this.factor.k2 * this.prod.getFat() * 9) / 100
        );
    }

    // Вся доза целиком
    getWholeD() {
        console.log(this.getDPS(),this.getCarbD(),this.getProtFatD());
        return this.getDPS() + this.getCarbD() + this.getProtFatD();
    }

    getQuick() {
        return this.getDPS() + this.getQCarbD();
    }

    getSlow() {
        return this.getSlCarbD() + this.getProtFatD();
    }
}

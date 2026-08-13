export default class Factor {
    constructor(k1, k2, k3, gl1, gl2, be) {
        this.k1 = +k1;
        this.k2 = +k2;
        this.k3 = +k3;
        this.gl1 = +gl1;
        this.gl2 = +gl2;
        this.be = +be;
    }

    getK1() {
        return (this.k1 * this.be) / 10;
    }

    setK1(newk1) {
        if (this.be === 0) {
            this.be = 10;
        }
        this.k1 = (10 * newk1) / this.be;
    }

    clone(factor) {
        this.k1 = factor.k1;
        this.k2 = factor.k2;
        this.k3 = factor.k3;
        this.gl1 = factor.gl1;
        this.gl2 = factor.gl2;
        this.be = factor.be;
    }
}

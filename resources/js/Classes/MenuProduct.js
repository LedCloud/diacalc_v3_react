export default class MenuProduct {
    constructor(name, id, weight, prot, fat, carb, gi, idorig){
        this.name = name;
        this.id = +id;
        this.weight = +weight;
        this.prot = +prot;
        this.fat = +fat;
        this.carb = +carb;
        this.gi = +gi;
        this.idorig = +idorig;
    }

    getProt() {
        return (this.prot * this.weight) / 100;
    }

    getFat() {
        return (this.fat * this.weight) / 100;
    }

    getCarb() {
        return (this.carb * this.weight) / 100;
    }

    getQCarb() {
        return (this.carb * this.weight * this.gi) / 10000;
    }

    getSlCarb() {
        return this.getCarb() - this.getQCarb();
    }

    getCalor() {
        //console.log('Calorie', this.getCalorByFat(), this.getCalorByProt(), this.getCalorByCarb());
        return this.getCalorByFat() + this.getCalorByProt() + this.getCalorByCarb();
    }

    getCalorByFat() {
        return this.getFat() * 9.3;
    }

    getCalorByProt() {
        return this.getProt() * 4.1;
    }

    getCalorByCarb() {
        return this.getCarb() * 4.1;
    }

    getGLIndx() {
        // На самом деле гликемическая нагрузка
        return (this.carb * this.weight * this.gi) / 10000;
    }

    addProduct(pr) {
        let allProt = this.getProt() + pr.getProt();
        let allFat = this.getFat() + pr.getFat();
        let allCarb = this.getCarb() + pr.getCarb();
        let allQCarb = this.getQCarb() + pr.getQCarb();
        let newGi;

        if (allCarb > 0) {
            newGi = Math.round((100 * allQCarb) / allCarb);
        } else {
            newGi = 50;
        }

        let newWeight = this.weight + pr.weight;

        if (newWeight !== 0) {
            if (this.name === "") {
                this.name = pr.name;
            } else {
                this.name = this.name + " " + pr.name;
            }
            this.prot = (100 * allProt) / newWeight;
            this.fat = (100 * allFat) / newWeight;
            this.carb = (100 * allCarb) / newWeight;
            this.gi = newGi;
            this.weight = newWeight;
            this.id = -1;
            this.idorig = 0;
        } else {
            this.prot = this.fat = this.carb = 0;
            this.gi = 50;
        }
    }
}

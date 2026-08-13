import MenuProduct from "@/Classes/MenuProduct.js";
import Dose from "@/Classes/Dose.js";

export default function valueCalculator (item, name, settings, factor) {
    const nutients = ['carb', 'fat', 'prot'];
    if (nutients.includes(name)) {
        return {
            val: (item.weight * item[name] / 100),
            precision: 1
        }
    }
    if (name === 'gi') {
        return {
            val: item.gi,
            precision: 0
        };
    }
    if (name === 'be') {
        return {
            val: (item.weight * item.carb * settings.be / 100).toFixed(1),
            precision: 0
        };
    }
    const prod = new MenuProduct(
        item.name, item.id,
        item.weight,
        item.prot,
        item.fat,
        item.carb,
        item.gi,
        0
    );

    if (name === 'dose') {
        const dose = new Dose(prod, factor);
        return {
            val:dose.getWholeD(),
            precision: 1
        };
    }
    if (name === 'gn') {
        return {
            val: prod.getGLIndx(),
            precision: 0
        };
    }
    if (name === 'calorie') {
        return {
            val:prod.getCalor(),
            precision: 0
        };
    }
    if (name === 'gl') {
        return {
            val: prod.gi,
            precision: 0
        };
    }
};

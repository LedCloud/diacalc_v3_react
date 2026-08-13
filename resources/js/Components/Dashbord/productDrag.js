const PRODUCT_DRAG_TYPE = 'application/x-diacalc-product';
const PRODUCT_DRAG_PREFIX = 'diacalc-product:';

let draggingProductId = null;
let draggingProductGroupId = null;

export function beginProductDrag(dataTransfer, productId, productGroupId = null) {
    draggingProductId = String(productId);
    draggingProductGroupId = productGroupId != null && Number(productGroupId) > 0
        ? String(productGroupId)
        : null;
    dataTransfer.setData(PRODUCT_DRAG_TYPE, draggingProductId);
    dataTransfer.setData('text/plain', PRODUCT_DRAG_PREFIX + draggingProductId);
    dataTransfer.effectAllowed = 'copyMove';
}

export function endProductDrag() {
    draggingProductId = null;
    draggingProductGroupId = null;
}

export function isProductDrag(dataTransfer) {
    if (draggingProductId) {
        return true;
    }
    return Array.from(dataTransfer?.types || []).includes(PRODUCT_DRAG_TYPE);
}

export function getProductDragPayload(dataTransfer) {
    const custom = dataTransfer.getData(PRODUCT_DRAG_TYPE);
    const plain = dataTransfer.getData('text/plain') || '';
    const fromPlain = plain.startsWith(PRODUCT_DRAG_PREFIX)
        ? plain.slice(PRODUCT_DRAG_PREFIX.length)
        : '';
    const raw = custom || fromPlain || draggingProductId;
    const id = Number(raw);
    const groupId = draggingProductGroupId != null ? Number(draggingProductGroupId) : null;
    draggingProductId = null;
    draggingProductGroupId = null;
    return {
        productId: Number.isInteger(id) && id > 0 ? id : null,
        productGroupId: Number.isInteger(groupId) && groupId > 0 ? groupId : null,
    };
}

export function getProductIdFromDrop(dataTransfer) {
    return getProductDragPayload(dataTransfer).productId;
}

export function canDropProductOnGroup(group) {
    return Boolean(group) && !group.virtual && Number(group.id) > 0;
}

export function menuHasProduct(menuItems, productId) {
    const id = Number(productId);
    return (menuItems ?? []).some(item => Number(item.product_id) === id);
}

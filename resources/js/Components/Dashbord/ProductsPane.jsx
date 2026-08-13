import {useTrans} from "@/Hooks/useTrans.jsx";
import {router, usePage} from "@inertiajs/react";
import React, {useEffect, useRef, useState} from "react";
import {CiCircleChevDown, CiCircleChevUp, CiCircleRemove} from "react-icons/ci";
import Tooltip from "@/Components/Tooltip.jsx";
import ContextMenu from "@/Components/ContextMenu.jsx";
import Modal from "@/Components/Modal.jsx";
import InputOneLine from "@/Components/InputOneLine.jsx";
import {Dialog, DialogPanel, Transition, TransitionChild} from "@headlessui/react";

export default function ProductsPane()
{
    const { __ } = useTrans();
    const {groups = []} = usePage().props;
    const [selectedGrId, setSelectedGrId] = useState(groups[0]?.id ?? null);
    const [showGroupPopup, setShowGroupPopup] = useState(false);
    const [products, setProducts] = useState([]);
    const [cache, setCache] = useState({});
    const [loading, setLoading] = useState(false);
    const [contextProductId, setContextProductId] = useState(null);
    const [showEditDialog, setShowEditDialog] = useState(false);
    const [editData, setEditData] = useState({
        id: null,
        name: '',
        prot: '',
        fat: '',
        carb: '',
        gi: '',
    });
    const [menuSettings, setMenuSettings] = useState({
        visible: false,
        x: 0,
        y: 0,
    });
    const [searchQuery, setSearchQuery] = useState('');
    const [searchResults, setSearchResults] = useState([]);
    const [scrollToProductId, setScrollToProductId] = useState(null);
    const [highlightProductId, setHighlightProductId] = useState(null);
    const groupsListRef = useRef(null);
    const menuRef = useRef(null);
    const productsListRef = useRef(null);
    const searchBoxRef = useRef(null);

    const formatter = (val, fractions = 1) => {
        const parsed = parseFloat(val);
        if (isNaN(parsed)) {
            return val;
        }
        return parsed.toFixed(fractions);
    };

    useEffect(() => {
        if (!groups.length) {
            setSelectedGrId(null);
            return;
        }
        const stillExists = groups.some(g => g.id === selectedGrId);
        if (!stillExists) {
            setSelectedGrId(groups[0].id);
        }
    }, [groups, selectedGrId]);

    useEffect(() => {
        const listElement = groupsListRef.current;
        if (!listElement || selectedGrId === null || selectedGrId === undefined) {
            return;
        }

        const selectedElement = listElement.querySelector(`[data-group-id="${selectedGrId}"]`);
        selectedElement?.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    }, [selectedGrId, groups]);

    useEffect(() => {
        if (selectedGrId === null || selectedGrId === undefined) {
            setProducts([]);
            return;
        }

        const currentId = String(selectedGrId);

        if (cache[currentId]) {
            setProducts(cache[currentId]);
            return;
        }

        setLoading(true);
        axios.get(route('dashboard.groups.products', selectedGrId))
            .then(response => {
                const data = response.data;
                setProducts(data);
                setCache(prevCache => ({
                    ...prevCache,
                    [currentId]: data,
                }));
            })
            .catch(error => console.error('Error fetching products', error))
            .finally(() => setLoading(false));
    }, [selectedGrId]);

    useEffect(() => {
        const query = searchQuery.trim();
        if (query.length < 3) {
            setSearchResults([]);
            return;
        }

        const timer = setTimeout(() => {
            axios.get(route('dashboard.products.search'), { params: { q: query } })
                .then(response => setSearchResults(response.data ?? []))
                .catch(error => {
                    console.error('Error searching products', error);
                    setSearchResults([]);
                });
        }, 250);

        return () => clearTimeout(timer);
    }, [searchQuery]);

    useEffect(() => {
        if (scrollToProductId === null || loading) {
            return;
        }

        const listElement = productsListRef.current;
        if (!listElement) {
            return;
        }

        const productElement = listElement.querySelector(`[data-product-id="${scrollToProductId}"]`);
        if (!productElement) {
            return;
        }

        productElement.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        setHighlightProductId(scrollToProductId);
        setScrollToProductId(null);

        const clearHighlight = setTimeout(() => setHighlightProductId(null), 1500);
        return () => clearTimeout(clearHighlight);
    }, [scrollToProductId, products, loading]);

    useEffect(() => {
        const handleClickOutsideSearch = (e) => {
            if (searchBoxRef.current && !searchBoxRef.current.contains(e.target)) {
                setSearchResults([]);
            }
        };

        document.addEventListener('click', handleClickOutsideSearch);
        return () => document.removeEventListener('click', handleClickOutsideSearch);
    }, []);

    const selectedGroup = groups.find(g => g.id === selectedGrId);
    const canMove = Boolean(selectedGroup) && !selectedGroup.virtual && groups.filter(g => !g.virtual).length > 1;

    const changeGroup = (direction) => {
        const current = groups.findIndex(g => g.id === selectedGrId);
        if (current < 0 || !groups.length) {
            return;
        }
        if (direction === 'left') {
            setSelectedGrId(current === 0 ? groups[groups.length - 1].id : groups[current - 1].id);
        } else {
            setSelectedGrId(current === groups.length - 1 ? groups[0].id : groups[current + 1].id);
        }
    };

    const moveGroup = (direction) => {
        if (!canMove || !selectedGroup) {
            return;
        }
        router.post(route('dashboard.groups.move', selectedGroup.id), {
            direction,
        }, {
            preserveScroll: true,
        });
    };

    const closeGroupPopup = () => setShowGroupPopup(false);

    const clearSearch = () => {
        setSearchQuery('');
        setSearchResults([]);
    };

    const selectSearchResult = (result) => {
        setSearchQuery('');
        setSearchResults([]);
        setScrollToProductId(result.id);
        setSelectedGrId(result.product_group_id);
    };

    const handleContextMenu = (e, productId) => {
        e.preventDefault();
        e.stopPropagation();
        setContextProductId(productId);
        setMenuSettings({
            visible: true,
            x: e.clientX,
            y: e.clientY,
        });
    };

    useEffect(() => {
        const handleClickOutside = (e) => {
            if (menuRef.current && !menuRef.current.contains(e.target)) {
                setMenuSettings((prev) => ({ ...prev, visible: false }));
            }
        };

        if (menuSettings.visible) {
            document.addEventListener('click', handleClickOutside);
        }

        return () => document.removeEventListener('click', handleClickOutside);
    }, [menuSettings.visible]);

    const closeMenu = () => setMenuSettings(prev => ({ ...prev, visible: false }));

    const removeProductFromLocalState = (productId) => {
        setProducts(prev => prev.filter(p => p.id !== productId));
        setCache(prevCache => {
            const next = {};
            Object.keys(prevCache).forEach((key) => {
                next[key] = prevCache[key].filter(p => p.id !== productId);
            });
            return next;
        });
    };

    const updateProductInLocalState = (updated) => {
        setProducts(prev => prev.map(p => (p.id === updated.id ? { ...p, ...updated } : p)));
        setCache(prevCache => {
            const next = {};
            Object.keys(prevCache).forEach((key) => {
                next[key] = prevCache[key].map(p => (p.id === updated.id ? { ...p, ...updated } : p));
            });
            return next;
        });
    };

    const addProductToMenu = (productId) => {
        if (!productId) {
            return;
        }
        router.post(route('dashboard.products.add_to_menu', productId), {}, {
            preserveScroll: true,
        });
    };

    const openEditDialog = (productId) => {
        const product = products.find(p => p.id === productId);
        if (!product) {
            return;
        }
        setEditData({
            id: product.id,
            name: product.name ?? '',
            prot: String(product.prot ?? ''),
            fat: String(product.fat ?? ''),
            carb: String(product.carb ?? ''),
            gi: String(product.gi ?? ''),
        });
        setShowEditDialog(true);
    };

    const updateEditField = (value, name) => {
        setEditData(prev => ({ ...prev, [name]: value }));
    };

    const saveProduct = () => {
        if (!editData.id) {
            return;
        }
        const payload = {
            name: editData.name,
            prot: editData.prot,
            fat: editData.fat,
            carb: editData.carb,
            gi: editData.gi,
        };
        router.patch(route('dashboard.products.update', editData.id), payload, {
            preserveScroll: true,
            onSuccess: () => {
                updateProductInLocalState({
                    id: editData.id,
                    name: editData.name,
                    prot: editData.prot,
                    fat: editData.fat,
                    carb: editData.carb,
                    gi: editData.gi,
                });
                setShowEditDialog(false);
            },
        });
    };

    const deleteProduct = (productId) => {
        if (!productId) {
            return;
        }
        if (!window.confirm(__('confirm_delete_product'))) {
            return;
        }
        router.delete(route('dashboard.products.delete', productId), {
            preserveScroll: true,
            onSuccess: () => removeProductFromLocalState(productId),
        });
    };

    const menuItems = [
        {
            name: __('add_to_menu'),
            handler: () => addProductToMenu(contextProductId),
        },
        {
            name: __('edit'),
            handler: () => openEditDialog(contextProductId),
        },
        {
            name: __('delete'),
            handler: () => deleteProduct(contextProductId),
        },
    ];

    if (!groups.length) {
        return <div className="products-pane" />;
    }

    return (
        <div className="products-pane">
            <div className="products-pane__groups-compact">
                <div
                    className="products-pane__groups-compact__left btn p-3"
                    onClick={() => changeGroup('left')}
                >&lt;&lt;</div>
                <div
                    className="products-pane__groups-compact__name btn p-3"
                    onClick={() => setShowGroupPopup(true)}
                >{selectedGroup?.name}</div>
                <div
                    className="products-pane__groups-compact__right btn p-3"
                    onClick={() => changeGroup('right')}
                >&gt;&gt;</div>
            </div>

            <div className="products-pane__groups-full">
                <div className="products-pane__toolbar">
                    <Tooltip text={__('move_up')}>
                        <button
                            type="button"
                            className="products-pane__toolbar__btn"
                            disabled={!canMove}
                            onClick={() => moveGroup('up')}
                            aria-label={__('move_up')}
                        >
                            <CiCircleChevUp size="1.8em" />
                        </button>
                    </Tooltip>
                    <Tooltip text={__('move_down')}>
                        <button
                            type="button"
                            className="products-pane__toolbar__btn"
                            disabled={!canMove}
                            onClick={() => moveGroup('down')}
                            aria-label={__('move_down')}
                        >
                            <CiCircleChevDown size="1.8em" />
                        </button>
                    </Tooltip>
                </div>
                <div className="products-pane__list" ref={groupsListRef}>
                    {groups.map(group => (
                        <div
                            key={group.virtual ? 'virtual-freq' : group.id}
                            className={`group-item ${selectedGrId === group.id ? 'bg-sky-300' : 'bg-slate-50'}`}
                            data-group-id={group.id}
                            onClick={() => setSelectedGrId(group.id)}
                        >
                            {group.name}
                        </div>
                    ))}
                </div>
            </div>

            <div className="products-pane__main">
                <div className="products-pane__search" ref={searchBoxRef}>
                    <div className="products-pane__search__row">
                        <input
                            type="text"
                            className="products-pane__search__input"
                            value={searchQuery}
                            placeholder={__('search_product')}
                            onChange={(e) => setSearchQuery(e.target.value)}
                        />
                        <button
                            type="button"
                            className="products-pane__search__clear"
                            onClick={clearSearch}
                            aria-label={__('close')}
                        >×</button>
                    </div>
                    {searchResults.length > 0 && (
                        <div className="products-pane__search__results">
                            {searchResults.map(result => (
                                <div
                                    key={result.id}
                                    className="products-pane__search__result"
                                    onClick={() => selectSearchResult(result)}
                                >{result.name}</div>
                            ))}
                        </div>
                    )}
                </div>

                <div className="products-pane__products context-menu-box" ref={productsListRef}>
                    {loading ? <p>Loading...</p> : (
                        products.map(product => (
                            <div
                                className={`product-item bg-slate-50 border-2 border-slate-600 rounded-lg${highlightProductId === product.id ? ' is-highlighted' : ''}`}
                                key={product.id}
                                data-product-id={product.id}
                                onContextMenu={(e) => handleContextMenu(e, product.id)}
                            >
                                <div className="product-item__name">{product.name}</div>
                                <div className="product-item__description">
                                    <Tooltip text={__('prot')}>{formatter(product.prot)}</Tooltip>-
                                    <Tooltip text={__('fat')}>{formatter(product.fat)}</Tooltip>-
                                    <Tooltip text={__('carb')}>{formatter(product.carb)}</Tooltip>-
                                    <Tooltip text={__('gi')}>{formatter(product.gi, 0)}</Tooltip>
                                </div>
                            </div>
                        ))
                    )}
                    <ContextMenu
                        menuSettings={menuSettings}
                        menuRef={menuRef}
                        menuItems={menuItems}
                        onClose={closeMenu}
                    />
                </div>
            </div>

            <Transition show={showGroupPopup} leave="duration-200">
                <Dialog
                    as="div"
                    id="products-group-modal"
                    className="fixed inset-0 z-50 flex transform items-center overflow-y-auto px-4 py-6 transition-all sm:px-0 p-3"
                    onClose={closeGroupPopup}
                >
                    <TransitionChild
                        enter="ease-out duration-300"
                        enterFrom="opacity-0"
                        enterTo="opacity-100"
                        leave="ease-in duration-200"
                        leaveFrom="opacity-100"
                        leaveTo="opacity-0"
                    >
                        <div className="absolute inset-0 bg-gray-500/75" />
                    </TransitionChild>
                    <TransitionChild
                        enter="ease-out duration-300"
                        enterFrom="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        enterTo="opacity-100 translate-y-0 sm:scale-100"
                        leave="ease-in duration-200"
                        leaveFrom="opacity-100 translate-y-0 sm:scale-100"
                        leaveTo="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    >
                        <DialogPanel
                            className="mb-6 transform overflow-hidden rounded-lg bg-white shadow-xl transition-all sm:mx-auto sm:w-full md:w-1/2 lg:w-1/3"
                        >
                            <div className="products-pane__toolbar products-pane__toolbar--popup px-2 pt-2">
                                <div className="products-pane__toolbar__moves">
                                    <Tooltip text={__('move_up')}>
                                        <button
                                            type="button"
                                            className="products-pane__toolbar__btn"
                                            disabled={!canMove}
                                            onClick={() => moveGroup('up')}
                                            aria-label={__('move_up')}
                                        >
                                            <CiCircleChevUp size="1.8em" />
                                        </button>
                                    </Tooltip>
                                    <Tooltip text={__('move_down')}>
                                        <button
                                            type="button"
                                            className="products-pane__toolbar__btn"
                                            disabled={!canMove}
                                            onClick={() => moveGroup('down')}
                                            aria-label={__('move_down')}
                                        >
                                            <CiCircleChevDown size="1.8em" />
                                        </button>
                                    </Tooltip>
                                </div>
                                <div className="products-pane__toolbar__close">
                                    <Tooltip text={__('close')}>
                                        <button
                                            type="button"
                                            className="products-pane__toolbar__btn"
                                            onClick={closeGroupPopup}
                                            aria-label={__('close')}
                                        >
                                            <CiCircleRemove size="1.8em" />
                                        </button>
                                    </Tooltip>
                                </div>
                            </div>
                            <div className="products-pane__popup-list">
                                {groups.map((gr) => (
                                    <div
                                        className={`group m-1 px-2 py-1 rounded cursor-pointer ${selectedGrId === gr.id ? 'border-sky-600 bg-sky-300' : 'hover:ring'}`}
                                        key={gr.virtual ? 'virtual-freq' : gr.id}
                                        onClick={() => setSelectedGrId(gr.id)}
                                    >{gr.name}</div>
                                ))}
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </Dialog>
            </Transition>

            <Modal
                show={showEditDialog}
                onClose={() => setShowEditDialog(false)}
                header={__('edit_product')}
                maxWidth="md"
            >
                <div className="py-3 px-4 flex flex-col gap-2">
                    <InputOneLine
                        value={editData.name}
                        name="name"
                        label={__('name')}
                        onChange={updateEditField}
                    />
                    <InputOneLine
                        value={editData.prot}
                        name="prot"
                        label={__('prot')}
                        onChange={updateEditField}
                    />
                    <InputOneLine
                        value={editData.fat}
                        name="fat"
                        label={__('fat')}
                        onChange={updateEditField}
                    />
                    <InputOneLine
                        value={editData.carb}
                        name="carb"
                        label={__('carb')}
                        onChange={updateEditField}
                    />
                    <InputOneLine
                        value={editData.gi}
                        name="gi"
                        label={__('gi')}
                        onChange={updateEditField}
                    />
                </div>
                <div className="flex gap-3 p-2">
                    <button
                        type="button"
                        className="px-2 py-1 w-24 rounded ring-2 ring-offset-1 ring-blue-400 bg-sky-300"
                        onClick={saveProduct}
                    >{__('save')}</button>
                    <button
                        type="button"
                        className="px-2 py-1 w-24 rounded ring-2 ring-offset-1 ring-slate-400 bg-white"
                        onClick={() => setShowEditDialog(false)}
                    >{__('cancel')}</button>
                </div>
            </Modal>
        </div>
    );
}

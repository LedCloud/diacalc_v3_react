import {useTrans} from "@/Hooks/useTrans.jsx";
import {router, usePage} from "@inertiajs/react";
import {useEffect, useRef, useState} from "react";
import {Dialog, DialogPanel, Transition, TransitionChild} from "@headlessui/react";
import ContextMenu from "@/Components/ContextMenu.jsx";
import Tooltip from "@/Components/Tooltip.jsx";
import Modal from "@/Components/Modal.jsx";
import InputOneLine from "@/Components/InputOneLine.jsx";

export default function ArchivePage(){
    // 1. Создаем ссылку на DOM-элемент списка
    const listRef = useRef(null);
    const groupsListRef = useRef(null);
    const { __ } = useTrans();
    const {groups, productGroups = []} = usePage().props;
    const [selectedGrId, setSelectedGrId] = useState(groups[0]?.id || null);
    //const [selectedPr, setSelectedPr] = useState(products.length ? products[0].id : 0);
    const [showGroupPopup, setShowGroupPopup] = useState(false);
    const [products, setProducts] = useState([]);

    // This object acts as our local cache
    const [cache, setCache] = useState({});

    const [loading, setLoading] = useState(false);
    const [contextProductId, setContextProductId] = useState(null);
    const [showAddDialog, setShowAddDialog] = useState(false);
    const [addData, setAddData] = useState({
        name: '',
        prot: '',
        fat: '',
        carb: '',
        gi: '',
        product_group_id: productGroups[0]?.id ?? null,
    });

    const formatter = (val, fractions = 1) => {
        const parsed = parseFloat(val);
        if (isNaN(parsed)) {
            return val;
        }
        return parsed.toFixed(fractions);
    }

    useEffect(() => {
        const listElement = listRef.current;
        if (!listElement) return;

        // Таймер для скролла вниз (запустится через 400мс после открытия)
        const scrollDownTimeout = setTimeout(() => {
            listElement.scrollTo({
                top: 60, // Дистанция в пикселях, на которую опустится список
                behavior: 'smooth',
            });

            // Таймер для возврата наверх (запустится через 1 секунду после открытия)
            const scrollUpTimeout = setTimeout(() => {
                listElement.scrollTo({
                    top: 0,
                    behavior: 'smooth',
                });
            }, 600); // 600мс задержки, чтобы пользователь успел заметить движение

            return () => clearTimeout(scrollUpTimeout);
        }, 400);

        return () => clearTimeout(scrollDownTimeout);
    }, []); // Пустой массив зависимостей означает, что эффект сработает один раз при монтировании

    useEffect(() => {
        if (!selectedGrId) return;

        const currentId = String(selectedGrId);

        console.log('GrID', selectedGrId, cache);

        if (cache[currentId]) {
            console.log('Got from cache');
            setProducts(cache[currentId]);
            return;
        }
        setLoading(true);
        axios.get(`/archive/groups/${currentId}/products`)
            .then(response => {
                const data = response.data;
                console.log('Got products', data);
                // Update products state
                setProducts(data);

                // Save to cache for next time
                setCache(prevCache => ({
                    ...prevCache,
                    [currentId]: data
                }));
            })
            .catch(error => console.error("Error fetching products", error))
            .finally(() => setLoading(false));

    }, [selectedGrId]);

    useEffect(() => {
        const listElement = groupsListRef.current;
        if (!listElement || !selectedGrId) return;

        const selectedElement = listElement.querySelector(`[data-group-id="${selectedGrId}"]`);
        selectedElement?.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    }, [selectedGrId]);

    const closeGroupPopup = () => {
        setShowGroupPopup(false);
    };

    const changeGroup = (direction) => {
        const current = groups.findIndex(g => g.id === selectedGrId);
        let toBeSelected;
        if (current < 0)
            return;
        if ('left' === direction) {
            if (current === 0)
                toBeSelected = groups[groups.length - 1].id;
            else
                toBeSelected = groups[current - 1].id;
        } else {
            if (current === groups.length - 1)
                toBeSelected = groups[0].id;
            else
                toBeSelected = groups[current + 1].id;
        }
        setSelectedGrId(toBeSelected);
    }

    const selectedGroup = groups.find(gr => gr.id === selectedGrId);

    // Store menu visibility and screen coordinates
    const [menuSettings, setMenuSettings] = useState({
        visible: false,
        x: 0,
        y: 0,
    });

    const menuRef = useRef(null);

    // Handle right-click on a product
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

    // Close menu when clicking anywhere else
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

    const openAddDialog = (productId) => {
        const product = products.find(p => p.id === productId);
        if (!product) {
            return;
        }
        if (!productGroups.length) {
            window.alert(__('no_product_groups'));
            return;
        }
        setAddData({
            name: product.name ?? '',
            prot: String(product.prot ?? ''),
            fat: String(product.fat ?? ''),
            carb: String(product.carb ?? ''),
            gi: String(product.gi ?? ''),
            product_group_id: productGroups[0].id,
        });
        setShowAddDialog(true);
    };

    const updateAddField = (value, name) => {
        setAddData(prev => ({ ...prev, [name]: value }));
    };

    const saveToProducts = () => {
        if (!addData.product_group_id) {
            return;
        }
        router.post(route('archive.add_to_products'), {
            name: addData.name,
            prot: addData.prot,
            fat: addData.fat,
            carb: addData.carb,
            gi: addData.gi,
            product_group_id: addData.product_group_id,
        }, {
            preserveScroll: true,
            onSuccess: () => setShowAddDialog(false),
        });
    };

    const menuItems = [
        {
            name: __("add_to_products"),
            handler: () => openAddDialog(contextProductId),
        },
    ];

    const closeMenu = () => setMenuSettings(prev => ({ ...prev, visible: false }));

    return (<div className="archive-layout">
        <div className="groups-compact">
            <div className="groups-compact__left btn p-3"
                 onClick={() => changeGroup('left')}
            >&lt;&lt;</div>
            <div className="group btn p-3"
                 onClick={() => setShowGroupPopup(true)}
            >{selectedGroup.name}</div>
            <div className="groups-compact__right btn p-3"
                 onClick={() => changeGroup('right')}
            >&gt;&gt;</div>
        </div>
        <div className="groups-full">
            <div className="groups-full__header">
                <div className="btn groups-full__left" onClick={() => changeGroup('left')}>&lt;&lt;</div>
                <div className="groups-full__selected_group">{selectedGroup.name}</div>
                <div className="btn groups-full__right" onClick={() => changeGroup('right')}>&gt;&gt;</div>
            </div>
            <div className="groups-full__list" ref={groupsListRef}>
                {groups.map(group => (
                    <div
                        className={`group-item bg-slate-50 ${selectedGroup.id === group.id ? "bg-slate-300" : ""}`}
                        key={group.id}
                        data-group-id={group.id}
                        onClick={() => setSelectedGrId(group.id)}
                    >{group.name}</div>
                ))}
            </div>
        </div>
        <div ref={listRef}
            className="products context-menu-box">
            {loading ? <p>Loading...</p> : (
                <>
                {products.map(product => (
                    <div
                        className="product-item bg-slate-50 border-2  border-slate-600 rounded-lg"
                        key={product.id}
                        onContextMenu={(e) => handleContextMenu(e, product.id)}
                    >
                        <div className="product-item__name">{product.name}</div>
                        <div className="product-item__description">
                            <Tooltip text={__("prot")}>{formatter(product.prot)}</Tooltip>-
                            <Tooltip text={__("fat")}>{formatter(product.fat)}</Tooltip>-
                            <Tooltip text={__("carb")}>{formatter(product.carb)}</Tooltip>-
                            <Tooltip text={__('gi')}>{formatter(product.gi,0)}</Tooltip>
                        </div>
                    </div>
                ))}
                </>
            )}
            <ContextMenu menuSettings={menuSettings}
                         menuRef={menuRef}
                         menuItems={menuItems}
                         onClose={closeMenu}
            />
        </div>
            <Transition show={showGroupPopup} leave="duration-200">
                <Dialog
                    as="div"
                    id="modal"
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
                            className={`mb-6 transform overflow-hidden rounded-lg bg-white shadow-xl transition-all sm:mx-auto sm:w-full md:w-1/2 lg:w-1/3`}
                        >
                            {groups.map((gr) => {
                                return <div className={`group m-1 px-2 py-1 rounded cursor-pointer ${selectedGrId === gr.id ? 'border-sky-600 bg-sky-300' : 'hover:ring hover:text-bold'}`}
                                            key={gr.id}
                                            onClick={() => {
                                                setSelectedGrId(gr.id);
                                                setShowGroupPopup(false);
                                            }}
                                >{gr.name}</div>
                            })}
                        </DialogPanel>
                    </TransitionChild>
                </Dialog>
            </Transition>

            <Modal
                show={showAddDialog}
                onClose={() => setShowAddDialog(false)}
                header={__('add_to_products')}
                maxWidth="md"
            >
                <div className="py-3 px-4 flex flex-col gap-2">
                    <InputOneLine
                        value={addData.name}
                        name="name"
                        label={__('name')}
                        onChange={updateAddField}
                    />
                    <InputOneLine
                        value={addData.prot}
                        name="prot"
                        label={__('prot')}
                        onChange={updateAddField}
                    />
                    <InputOneLine
                        value={addData.fat}
                        name="fat"
                        label={__('fat')}
                        onChange={updateAddField}
                    />
                    <InputOneLine
                        value={addData.carb}
                        name="carb"
                        label={__('carb')}
                        onChange={updateAddField}
                    />
                    <InputOneLine
                        value={addData.gi}
                        name="gi"
                        label={__('gi')}
                        onChange={updateAddField}
                    />
                    <div className="horizontal-group">
                        <label htmlFor="product_group_id">{__('group')}</label>
                        <select
                            id="product_group_id"
                            value={addData.product_group_id ?? ''}
                            onChange={(e) => updateAddField(Number(e.target.value), 'product_group_id')}
                        >
                            {productGroups.map(group => (
                                <option key={group.id} value={group.id}>{group.name}</option>
                            ))}
                        </select>
                    </div>
                </div>
                <div className="flex gap-3 p-2">
                    <button
                        type="button"
                        className="px-2 py-1 w-24 rounded ring-2 ring-offset-1 ring-blue-400 bg-sky-300"
                        onClick={saveToProducts}
                    >{__('save')}</button>
                    <button
                        type="button"
                        className="px-2 py-1 w-24 rounded ring-2 ring-offset-1 ring-slate-400 bg-white"
                        onClick={() => setShowAddDialog(false)}
                    >{__('cancel')}</button>
                </div>
            </Modal>
        </div>);
}

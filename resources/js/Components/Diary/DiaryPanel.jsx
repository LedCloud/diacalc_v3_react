import {useTrans} from "@/Hooks/useTrans.jsx";
import Dropdown from "@/Components/Dropdown.jsx";
import Tooltip from "@/Components/Tooltip.jsx";
import DiaryRecords from "@/Components/Diary/DiaryRecords.jsx";
import {CiCirclePlus, CiCircleRemove} from "react-icons/ci";
import {BsPinAngleFill, BsChat} from "react-icons/bs";
import {CgArrowsV} from "react-icons/cg";
import {useEffect, useRef, useState} from "react";

export default function DiaryPanel({
    index,
    isPrimary,
    canAdd,
    start,
    end,
    onStartChange,
    onEndChange,
    onRangeChange,
    onAdd,
    onClose,
    onGoToFirst,
}) {
    const { __ } = useTrans();
    const pageNumber = index + 1;
    const [days, setDays] = useState([]);
    const [loading, setLoading] = useState(true);
    const onRangeChangeRef = useRef(onRangeChange);
    onRangeChangeRef.current = onRangeChange;

    useEffect(() => {
        if (!start || !end) {
            setDays([]);
            setLoading(false);
            return;
        }

        const controller = new AbortController();
        setLoading(true);

        axios.get(route('diary.records'), {
            params: { start, end },
            signal: controller.signal,
        })
            .then((response) => {
                const data = response.data ?? {};
                setDays(data.diary ?? []);
                if (
                    (data.start && data.start !== start)
                    || (data.end && data.end !== end)
                ) {
                    onRangeChangeRef.current?.(data.start, data.end);
                }
            })
            .catch((error) => {
                if (error.code === 'ERR_CANCELED') {
                    return;
                }
                console.error('Error fetching diary', error);
                setDays([]);
            })
            .finally(() => {
                if (!controller.signal.aborted) {
                    setLoading(false);
                }
            });

        return () => controller.abort();
    }, [start, end]);

    return (
        <section className={`diary-panel${isPrimary ? ' is-primary' : ''}`}>
            <div className="diary-panel__toolbar">
                {isPrimary ? (
                    <>
                        <div className="diary-panel__toolbar-start">
                            <h3 className="diary-panel__title diary-panel__mobile-only">
                                {__('events')}
                            </h3>
                            <div className="diary-panel__desktop-only diary-panel__desktop-actions">
                                <div className="diary-panel__btn-group">
                                    <Tooltip text={__('glucose')}>
                                        <button
                                            type="button"
                                            className="btn diary-panel__icon-btn diary-panel__icon-btn--primary"
                                            aria-label={__('glucose')}
                                        >
                                            <BsPinAngleFill />
                                        </button>
                                    </Tooltip>
                                    <Tooltip text={__('comment')}>
                                        <button
                                            type="button"
                                            className="btn diary-panel__icon-btn diary-panel__icon-btn--primary"
                                            aria-label={__('comment')}
                                        >
                                            <BsChat />
                                        </button>
                                    </Tooltip>
                                </div>
                                <button
                                    type="button"
                                    className="btn diary-panel__icon-btn"
                                    aria-hidden="true"
                                    tabIndex={-1}
                                >
                                    <CgArrowsV />
                                </button>
                            </div>
                        </div>
                        <div className="diary-panel__toolbar-end">
                            {canAdd && (
                                <div className="diary-panel__desktop-only">
                                    <Tooltip text={__('add_page')}>
                                        <button
                                            type="button"
                                            className="btn diary-panel__icon-btn"
                                            onClick={onAdd}
                                            aria-label={__('add_page')}
                                        >
                                            <CiCirclePlus />
                                        </button>
                                    </Tooltip>
                                </div>
                            )}
                            <div className="diary-panel__mobile-only">
                                <Dropdown>
                                    <Dropdown.Trigger>
                                        <button type="button" className="btn diary-panel__dob">
                                            {__('add')}
                                        </button>
                                    </Dropdown.Trigger>
                                    <Dropdown.Content align="right" width="48">
                                        <button type="button" className="diary-panel__menu-item">
                                            {__('glucose')}
                                        </button>
                                        <button type="button" className="diary-panel__menu-item">
                                            {__('comment')}
                                        </button>
                                        {canAdd && (
                                            <>
                                                <div className="diary-panel__menu-divider" />
                                                <button
                                                    type="button"
                                                    className="diary-panel__menu-item"
                                                    onClick={onAdd}
                                                >
                                                    {__('page')}
                                                </button>
                                            </>
                                        )}
                                    </Dropdown.Content>
                                </Dropdown>
                            </div>
                        </div>
                    </>
                ) : (
                    <>
                        <div className="diary-panel__toolbar-start">
                            <button
                                type="button"
                                className="btn diary-panel__home diary-panel__mobile-only"
                                onClick={onGoToFirst}
                            >
                                {__('page_n', { n: 1 })}
                            </button>
                            <button
                                type="button"
                                className="btn diary-panel__icon-btn diary-panel__desktop-only"
                                aria-hidden="true"
                                tabIndex={-1}
                            >
                                <CgArrowsV />
                            </button>
                        </div>
                        <div className="diary-panel__toolbar-center">
                            <h3 className="diary-panel__title diary-panel__mobile-only">
                                {__('page_n', { n: pageNumber })}
                            </h3>
                        </div>
                        <div className="diary-panel__toolbar-end">
                            <Tooltip text={__('close_panel')}>
                                <button
                                    type="button"
                                    className="btn diary-panel__icon-btn diary-panel__close"
                                    onClick={onClose}
                                    aria-label={__('close_panel')}
                                >
                                    <CiCircleRemove />
                                </button>
                            </Tooltip>
                        </div>
                    </>
                )}
            </div>

            <div className="diary-panel__dates">
                <input
                    type="date"
                    className="diary-panel__date"
                    value={start}
                    max={end || undefined}
                    onChange={(event) => onStartChange(event.target.value)}
                    aria-label={__('start_date')}
                />
                <input
                    type="date"
                    className="diary-panel__date"
                    value={end}
                    onChange={(event) => onEndChange(event.target.value)}
                    aria-label={__('end_date')}
                />
            </div>

            <div className="diary-panel__body" aria-busy={loading}>
                <DiaryRecords days={days} loading={loading} />
            </div>
        </section>
    );
}

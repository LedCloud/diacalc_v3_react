import React, {useEffect, useRef, useState} from 'react';
import {useTrans} from "@/Hooks/useTrans.jsx";
import {Head} from "@inertiajs/react";
import PageContainer from "@/Components/PageContainer.jsx";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.jsx";
import DiaryPanel from "@/Components/Diary/DiaryPanel.jsx";
import {clampDateRange, defaultDateRange} from "@/Components/Diary/dateRange.js";

const MAX_PANELS = 3;

function createPanel(range) {
    return {
        id: crypto.randomUUID?.() ?? `panel-${Date.now()}-${Math.random().toString(16).slice(2)}`,
        ...(range ?? defaultDateRange()),
    };
}

export default function Diary() {
    const { __ } = useTrans();
    const layoutRef = useRef(null);
    const previousCount = useRef(1);

    const [panels, setPanels] = useState(() => [createPanel()]);

    useEffect(() => {
        if (panels.length > previousCount.current) {
            layoutRef.current?.lastElementChild?.scrollIntoView({
                behavior: 'smooth',
                inline: 'start',
                block: 'nearest',
            });
        }
        previousCount.current = panels.length;
    }, [panels.length]);

    const addPanel = () => {
        setPanels((current) => {
            if (current.length >= MAX_PANELS) {
                return current;
            }
            return [...current, createPanel()];
        });
    };

    const removePanel = (id) => {
        setPanels((current) => {
            if (current.length <= 1) {
                return current;
            }
            return current.filter((panel) => panel.id !== id);
        });
        requestAnimationFrame(() => {
            layoutRef.current?.scrollTo({ left: 0, behavior: 'smooth' });
        });
    };

    const updatePanelDate = (id, field, value) => {
        setPanels((current) => current.map((panel) => {
            if (panel.id !== id) {
                return panel;
            }
            const start = field === 'start' ? value : panel.start;
            const end = field === 'end' ? value : panel.end;
            return { ...panel, ...clampDateRange(start, end) };
        }));
    };

    const applyPanelRange = (id, start, end) => {
        setPanels((current) => current.map((panel) => (
            panel.id === id ? { ...panel, ...clampDateRange(start, end) } : panel
        )));
    };

    const goToFirst = () => {
        layoutRef.current?.firstElementChild?.scrollIntoView({
            behavior: 'smooth',
            inline: 'start',
            block: 'nearest',
        });
    };

    const canAdd = panels.length < MAX_PANELS;

    return (
        <AuthenticatedLayout
            className="single-page"
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    {__('diary')}
                </h2>
            }
        >
            <Head title={__('diary')} />

            <PageContainer classNameExternal="dashboard-page">
                <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg h-full">
                    <div className="p-2 text-gray-900 h-full min-h-0">
                        <div
                            ref={layoutRef}
                            className="diary-layout"
                            style={{ '--panel-count': panels.length }}
                        >
                            {panels.map((panel, index) => (
                                <DiaryPanel
                                    key={panel.id}
                                    index={index}
                                    isPrimary={index === 0}
                                    canAdd={canAdd}
                                    start={panel.start}
                                    end={panel.end}
                                    onStartChange={(value) => updatePanelDate(panel.id, 'start', value)}
                                    onEndChange={(value) => updatePanelDate(panel.id, 'end', value)}
                                    onRangeChange={(start, end) => applyPanelRange(panel.id, start, end)}
                                    onAdd={addPanel}
                                    onClose={() => removePanel(panel.id)}
                                    onGoToFirst={goToFirst}
                                />
                            ))}
                        </div>
                    </div>
                </div>
            </PageContainer>
        </AuthenticatedLayout>
    );
}

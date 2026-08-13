import React, {useEffect, useState} from "react";
import {usePage, Form} from '@inertiajs/react'
import MenuTabContect from "@/Components/Settings/MenuTabContent.jsx";
import GlucoseTabContent from "@/Components/Settings/GlucoseTabContent.jsx";
import Tag from "@/Components/Tabs/Tag.jsx";
import ProductsTabContect from "@/Components/Settings/ProductsTabContent.jsx";
import { useTrans } from '@/Hooks/useTrans';

export default function SettingsTabbedPane() {

    const [activeTab, setActiveTab] = useState('menu');
    const {settings, menuMasks, errors} = usePage().props;
    const { __ } = useTrans();

    const [allSettings, setAllSettings] = useState(settings ?? null);

    useEffect(() => {
        setAllSettings(settings);
    }, [settings]);

    return (<div className="tabs">
        <div className="tabs__tags">

            <Tag id="menu" active={activeTab} name={__('menu')} clickHandler={setActiveTab} />
            <Tag id="glucose" active={activeTab} name={__('glucose')} clickHandler={setActiveTab} />
            <Tag id="products" active={activeTab} name={__('products')} clickHandler={setActiveTab} />

        </div>
        <div className="tabs__content">
            <Form action="/settings_react" className="settings-layout" method="patch">

                <MenuTabContect allSettings={allSettings}
                                setAllSettings={setAllSettings}
                                activeTab={activeTab}
                                menuMasks={menuMasks}
                                errors={errors}
                                className="settings-layout__menu"
                />

                <GlucoseTabContent allSettings={allSettings}
                                   setAllSettings={setAllSettings}
                                   activeTab={activeTab}
                                   errors={errors}
                                   className="settings-layout__glucose"
                />

                <ProductsTabContect allSettings={allSettings}
                                    setAllSettings={setAllSettings}
                                    activeTab={activeTab}
                                    errors={errors}
                                    className="settings-layout__products"
                                    />

                <div className="button-horizontal">
                    <button className="btn settings__btn-save primary w-full md:w-36 mt-3"
                            type="submit">{__('save')}
                    </button>
                </div>
            </Form>
        </div>
    </div>);
};

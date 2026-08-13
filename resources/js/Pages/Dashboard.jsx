import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import React, {useMemo} from 'react';
import PageContainer from "@/Components/PageContainer.jsx";
import Accordion from "@/Components/Accordion.jsx";
import MenuPane from "@/Components/Dashbord/MenuPane.jsx";
import ProductsPane from "@/Components/Dashbord/ProductsPane.jsx";
import {useTrans} from "@/Hooks/useTrans.jsx";

export default function Dashboard() {
    const { __ } = useTrans();

    // Stable element identity so Accordion does not receive a fresh <MenuPane />
    // on every Dashboard render (avoids unnecessary reconcile churn).
    const menuPane = useMemo(() => <MenuPane />, []);
    const productsPane = useMemo(() => <ProductsPane />, []);

    const items = useMemo(() => [
        {
            title: __('menu'),
            content: menuPane,
        },
        {
            title: __('products'),
            content: productsPane,
        },
    ], [__, menuPane, productsPane]);

    return (
        <AuthenticatedLayout
            className="single-page"
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Dashboard
                </h2>
            }
        >
            <Head title="Dashboard" />

            <PageContainer>
                <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg h-full">
                    <div className="p-6 text-gray-900 h-full min-h-0">
                        <Accordion items={items}/>
                    </div>
                </div>
            </PageContainer>
        </AuthenticatedLayout>
    );
}

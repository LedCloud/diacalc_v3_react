import SettingsTabbedPage from '@/Components/SettingsTabbedPage';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { useTrans } from '@/Hooks/useTrans';

export default function Settings({ auth, settings }) {
    const { __ } = useTrans();

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">{__('settings')}</h2>}
        >
            <Head title={__('settings')} />

            <div className="py-6">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <SettingsTabbedPage />
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

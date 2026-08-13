import {useTrans} from "@/Hooks/useTrans.jsx";
import {Head} from "@inertiajs/react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.jsx";
import ArchivePage from "@/Components/ArchivePage.jsx";
import PageContainer from "@/Components/PageContainer.jsx";

export default function Archive({ auth })
{
    const { __ } = useTrans();

    return (<AuthenticatedLayout
        user={auth.user}
        className="single-page"
        header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">{__('archive')}</h2>}
    >
        <Head title={__('archive')} />

        <PageContainer>
            <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 h-full">
                <ArchivePage />
            </div>
        </PageContainer>
    </AuthenticatedLayout>);
}

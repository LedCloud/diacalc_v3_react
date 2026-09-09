import { usePage } from '@inertiajs/react';
import { useState } from 'react';
import MainMenu from '@/Components/MainMenu';
import ToastContainer from "@/Components/ToastContainer.jsx";
import MobileMenu from "@/Components/MobileMenu.jsx";

export default function AuthenticatedLayout({ header, children, className = '' }) {
    const user = usePage().props.auth.user;
    const { menu } = usePage().props;

    const [showingNavigationDropdown, setShowingNavigationDropdown] =
        useState(false);

    return (
        <div className={`min-h-screen bg-gray-100 ${className}`}>
            <nav className="relative z-50 border-b border-gray-100 bg-white">
                <div className="mx-auto flex max-w-7xl items-center gap-2 px-4 sm:px-6 lg:px-8">
                    <div className="flex min-w-0 flex-1 items-center gap-1 sm:gap-0">
                        <MainMenu items={menu} user={user}/>

                        <div className="flex shrink-0 items-center sm:hidden">
                            <button
                                type="button"
                                onClick={() =>
                                    setShowingNavigationDropdown(
                                        (previousState) => !previousState,
                                    )
                                }
                                className="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none"
                                aria-label="Menu"
                            >
                                <svg
                                    className="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        className={
                                            !showingNavigationDropdown
                                                ? 'inline-flex'
                                                : 'hidden'
                                        }
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        className={
                                            showingNavigationDropdown
                                                ? 'inline-flex'
                                                : 'hidden'
                                        }
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {header && (
                        <div className="ms-auto min-w-0 shrink truncate text-end text-base font-semibold leading-tight text-gray-800 sm:hidden [&_h2]:m-0 [&_h2]:text-base [&_h2]:font-semibold [&_h2]:leading-tight">
                            {header}
                        </div>
                    )}
                </div>

                <div
                    className={
                        (showingNavigationDropdown ? 'block' : 'hidden') +
                        ' sm:hidden'
                    }
                >
                    <MobileMenu user={user} items={menu} />
                </div>
            </nav>

            {header && (
                <header className="hidden bg-white shadow sm:block">
                    <div className="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                        {header}
                    </div>
                </header>
            )}

            <main>{children}</main>

            <ToastContainer />
        </div>
    );
}

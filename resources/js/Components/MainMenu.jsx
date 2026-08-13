import React, { useState } from 'react';
import NavLink from '@/Components/NavLink';
import {Link} from "@inertiajs/react";
import ApplicationLogo from "@/Components/ApplicationLogo.jsx";
import Dropdown from "@/Components/Dropdown.jsx";
import {useAuth} from "@/Hooks/useAuth.jsx";

export default function MainMenu({ items, user }) {
    const { hasAccess } = useAuth();

    return (
        <div className="flex h-16 justify-between">
            <div className="flex">
                <div className="flex shrink-0 items-center">
                    <Link href="/">
                        <ApplicationLogo className="block h-9 w-auto fill-current text-gray-800"/>
                    </Link>
                </div>

                <div className="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <nav className="space-x-8 sm:-my-px sm:ml-10 sm:flex">
                        {items.map((item, index) => {

                            if (item.submenu) {
                                return <SubMenu key={index} item={item}/>;
                            }

                            return (
                                <MenuLink key={index} item={item}/>
                            );
                        })}
                    </nav>
                </div>
            </div>

            <div className="hidden sm:ms-6 sm:flex sm:items-center">
                <div className="relative ms-3">
                    <Dropdown>
                        <Dropdown.Trigger>
                            <span className="inline-flex rounded-md">
                                <button
                                    type="button"
                                    className="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                                >
                                    {user.name}

                                    <svg
                                        className="-me-0.5 ms-2 h-4 w-4"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            fillRule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clipRule="evenodd"
                                        />
                                    </svg>
                                </button>
                            </span>
                        </Dropdown.Trigger>

                        <Dropdown.Content>
                            <Dropdown.Link
                                href={route('profile.edit')}
                            >
                                Profile
                            </Dropdown.Link>
                            {hasAccess('platform.index') && (
                                <Dropdown.Link
                                    href={route('platform.index')}
                                >
                                    Admin Panel**
                                </Dropdown.Link>
                            )}
                            <Dropdown.Link
                                href={route('logout')}
                                method="post"
                                as="button"
                            >
                                Log Out
                            </Dropdown.Link>
                        </Dropdown.Content>
                    </Dropdown>
                </div>
            </div>
        </div>
    )
        ;
}

// Вспомогательный компонент для обычных ссылок
function MenuLink({item}) {
    if (item.livewire) {
        return (
            <a href={route(item.route)}
               className="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 hover:text-gray-700">
                {item.name}
            </a>
        );
    }
    return (
        <NavLink href={route(item.route)} active={route().current(item.route)}>
            {item.name}
        </NavLink>
    );
}

// Компонент для выпадающего списка
function SubMenu({item}) {
    const [isOpen, setIsOpen] = useState(false);

    return (
        <div
            className="relative inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 cursor-pointer"
            onMouseEnter={() => setIsOpen(true)}
            onMouseLeave={() => setIsOpen(false)}
        >
            <div className="flex items-center hover:text-gray-700">
                {item.name}
                <svg className="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7"/>
                </svg>
            </div>

            {/* Твое кастомное подменю (аналог того, что было в Blade) */}
            {isOpen && (
                <div
                    className="absolute top-full left-0 w-48 bg-white border border-gray-200 shadow-lg z-50 py-2 rounded-md">
                    {item.submenu.map((sub, idx) => (
                        <div key={idx}>
                            {sub.livewire ? (
                                <a
                                    href={route(sub.route)}
                                    className="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                >
                                    {sub.name}
                                </a>
                            ) : (
                                <NavLink
                                    method={sub.method ?? 'get' }
                                    href={route(sub.route, sub.params || {})}
                                    active={route().current(sub.route)}
                                    subitem={true}
                                    className="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 border-none"
                                >
                                    {sub.name}
                                </NavLink>
                            )}
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}

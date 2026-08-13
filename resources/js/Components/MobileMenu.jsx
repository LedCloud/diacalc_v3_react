import React, { useState } from 'react';
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.jsx";
import { Link } from "@inertiajs/react";
import { useAuth } from "@/Hooks/useAuth.jsx";

export default function MobileMenu({ items = [], user }) {
    const { hasAccess } = useAuth();
    const [openSubmenus, setOpenSubmenus] = useState({});

    const toggleSubmenu = (index) => {
        setOpenSubmenus(prev => ({
            ...prev,
            [index]: !prev[index],
        }));
    };

    return (
        <>
            <div className="pt-2 pb-3 space-y-1">
                {items.map((menuItem, index) => {
                    const hasSubmenu = menuItem.submenu && menuItem.submenu.length > 0;
                    const isSubmenuOpen = !!openSubmenus[index];

                    if (hasSubmenu) {
                        return (
                            <div key={index} className="border-l-4 border-transparent">
                                <button
                                    type="button"
                                    onClick={() => toggleSubmenu(index)}
                                    className="flex w-full items-center justify-between ps-3 pe-4 py-2 text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 transition duration-150 ease-in-out"
                                >
                                    <div>{menuItem.name}</div>
                                    <div className="ms-1 shadow-sm">
                                        <svg
                                            className={`h-4 w-4 transform transition-transform duration-200 ${isSubmenuOpen ? 'rotate-180' : ''}`}
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
                                    </div>
                                </button>

                                {isSubmenuOpen && (
                                    <div className="ps-4 space-y-1 bg-gray-50/50 transition ease-out duration-200">
                                        {menuItem.submenu.map((subItem, subIndex) => (
                                            subItem.livewire ? (
                                                <a
                                                    key={subIndex}
                                                    href={route(subItem.route, subItem.params || {})}
                                                    className="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 transition duration-150 ease-in-out"
                                                >
                                                    {subItem.name}
                                                </a>
                                            ) : (
                                                <Link
                                                    key={subIndex}
                                                    method={subItem.method ?? 'get'}
                                                    href={route(subItem.route, subItem.params || {})}
                                                    as={subItem.method === 'post' ? 'button' : 'a'}
                                                    className={`block w-full text-start pl-3 pr-4 py-2 border-l-4 text-base font-medium transition duration-150 ease-in-out ${
                                                        route().current(subItem.route)
                                                            ? 'border-sky-400 text-sky-700 bg-sky-50'
                                                            : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300'
                                                    }`}
                                                    onClick={() => {
                                                        if (subItem.method === 'post') {
                                                            toggleSubmenu(index);
                                                        }
                                                    }}
                                                >
                                                    {subItem.name}
                                                </Link>
                                            )
                                        ))}
                                    </div>
                                )}
                            </div>
                        );
                    }

                    return (
                        <MenuLink key={index} item={menuItem} />
                    );
                })}
            </div>

            <div className="border-t border-gray-200 pb-1 pt-4">
                <div className="px-4">
                    <div className="text-base font-medium text-gray-800">
                        {user.name}
                    </div>
                    <div className="text-sm font-medium text-gray-500">
                        {user.email}
                    </div>
                </div>

                <div className="mt-3 space-y-1">
                    <ResponsiveNavLink href={route('profile.edit')}>
                        Profile
                    </ResponsiveNavLink>
                    {hasAccess('platform.index') && (
                        <a
                            href={route('platform.index')}
                            className="flex w-full items-start border-l-4 py-2 pe-4 ps-3 border-transparent text-gray-600 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-800 focus:border-gray-300 focus:bg-gray-50 focus:text-gray-800 text-base font-medium transition duration-150 ease-in-out focus:outline-none"
                        >
                            Admin Panel
                        </a>
                    )}
                    <ResponsiveNavLink
                        method="post"
                        href={route('logout')}
                        as="button"
                    >
                        Log out
                    </ResponsiveNavLink>
                </div>
            </div>
        </>
    );
}

function MenuLink({ item }) {
    if (item.livewire) {
        return (
            <a
                href={route(item.route)}
                className="flex w-full items-start border-l-4 py-2 pe-4 ps-3 border-transparent text-gray-600 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-800 text-base font-medium transition duration-150 ease-in-out"
            >
                {item.name}
            </a>
        );
    }

    return (
        <ResponsiveNavLink
            href={route(item.route)}
            active={route().current(item.route)}
        >
            {item.name}
        </ResponsiveNavLink>
    );
}

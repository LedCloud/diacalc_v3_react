import {usePage} from "@inertiajs/react";
import {useEffect, useState} from "react";

export default function ToastContainer()
{
    const { flash } = usePage().props;
    const [stack, setStack] = useState([]);

    useEffect(() => {
        if (!flash || Object.keys(flash).length === 0) return;

        // 2. Map flash keys to new toast objects
        const newToasts = Object.entries(flash)
            .filter(([key, value]) => value !== null) // Only handle non-null flashes
            .map(([key, value]) => {
                const id = Date.now() + Math.random(); // Added random to prevent ID collisions

                // 3. Set up the auto-remove timer for THIS specific ID
                setTimeout(() => {
                    setStack((prev) => prev.filter((t) => t.id !== id));
                }, 3000);
                return {
                    id,
                    text: value,
                    className: key === 'notification' ? 'border-green-500' :
                        (key === 'warning' ? 'border-amber-500' : 'border-red-500')
                };
            });

        // 4. Update state once with all new toasts
        if (newToasts.length > 0) {
            setStack((prev) => [...prev, ...newToasts]);
        }
    }, [flash]);

    return (
        <div className="fixed top-5 right-5 space-y-2 z-50">
            {stack.map((value) => (
                <div key={value.id}
                     className={`bg-white shadow-lg border-l-4 p-4 rounded ${value.className}`}
                >
                    <span>{value.text}</span>
                </div>
            ))}
        </div>
    );
}

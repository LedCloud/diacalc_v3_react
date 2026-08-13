import { useState } from 'react';

// Принимаем пропсы (аналог param1 в blade)
export default function LinkedInputs({ initialValue = 0, label = "Число" }) {
    const [val, setVal] = useState(initialValue);

    return (
        <div className="p-4 border rounded bg-gray-50 space-y-3">
            <div>
                <label className="block text-sm font-medium text-gray-700">{label}:</label>
                <input
                    type="number"
                    className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    value={val}
                    onChange={(e) => setVal(+e.target.value)}
                />
            </div>

            <div>
                <label className="block text-sm font-medium text-gray-700">{label} (x2):</label>
                <input
                    type="number"
                    className="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100"
                    value={val * 2}
                    onChange={(e) => setVal((+e.target.value / 2) | 0)}
                />
            </div>
        </div>
    );
}

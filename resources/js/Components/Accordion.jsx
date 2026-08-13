import {useState} from "react";

export default function Accordion({
    items = [],
    defaultOpenIndex = 0,
    className = '',
})
{
    const initialIndex = items.length === 0
        ? -1
        : Math.min(Math.max(defaultOpenIndex, 0), items.length - 1);

    const [openIndex, setOpenIndex] = useState(initialIndex);

    return (
        <div className={`accordion ${className}`}>
            {items.map((item, index) => {
                const isOpen = openIndex === index;

                return (
                    <div
                        key={item.id ?? index}
                        className={`accordion__item ${isOpen ? 'is-open' : ''}`}
                    >
                        <button
                            type="button"
                            className="accordion__header"
                            aria-expanded={isOpen}
                            onClick={() => setOpenIndex(index)}
                        >
                            <span className="accordion__icon" aria-hidden="true" />
                            <span className="accordion__title">{item.title}</span>
                        </button>
                        {isOpen && (
                            <div className="accordion__panel">{item.content}</div>
                        )}
                    </div>
                );
            })}
        </div>
    );
}

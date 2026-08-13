import React, { useState, useRef, Children, isValidElement } from 'react';
import { createPortal } from 'react-dom';

function childHasBtnClass(children) {
    return Children.toArray(children).some(
        (child) =>
            isValidElement(child) &&
            typeof child.props.className === 'string' &&
            child.props.className.split(/\s+/).includes('btn')
    );
}

export default function Tooltip({ text, children }) {
    const [visible, setVisible] = useState(false);
    const [coords, setCoords] = useState({ top: 0, left: 0 });
    const triggerRef = useRef(null);
    const cursor = childHasBtnClass(children) ? 'pointer' : 'help';

    const handleMouseEnter = () => {
        if (!triggerRef.current) return;

        // Получаем точные экранные координаты элемента-триггера
        const rect = triggerRef.current.getBoundingClientRect();

        setCoords({
            // Позиционируем НАД элементом (вычитаем высоту тултипа, например ~40px)
            top: rect.top - 38 + window.scrollY,
            // Центрируем по горизонтали
            left: rect.left + rect.width / 2 + window.scrollX,
        });
        setVisible(true);
    };

    const handleMouseLeave = () => setVisible(false);

    return (
        <>
            {/* Элемент-триггер (на который наводим) */}
            <span
                ref={triggerRef}
                onMouseEnter={handleMouseEnter}
                onMouseLeave={handleMouseLeave}
                onFocus={handleMouseEnter}
                onBlur={handleMouseLeave}
                style={{ display: 'inline-block', cursor }}
                tabIndex="-1"
                className="tooltip"
            >
        {children}
      </span>

            {/* Рендерим тултип в body, если он активен */}
            {visible && createPortal(
                <div
                    style={{
                        position: 'fixed', // Важно: фиксированное позиционирование относительно экрана
                        top: coords.top,
                        left: coords.left,
                        transform: 'translateX(-50%)', // Центрируем сам тултип
                        backgroundColor: '#333',
                        color: '#fff',
                        padding: '6px 12px',
                        borderRadius: '4px',
                        fontSize: '14px',
                        zIndex: 9999, // Теперь z-index сработает, так как мы в корне body!
                        pointerEvents: 'none',
                        whiteSpace: 'nowrap',
                    }}
                >
                    {text}
                </div>,
                document.body // Портируем в конец документа
            )}
        </>
    );
}

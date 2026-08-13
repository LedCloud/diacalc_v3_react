import {forwardRef} from "react";

const InputOneLine = forwardRef(
    ({ label, value, onChange, onBlur, id, type, name = '', className = '', focused = false}, ref) => {
    return (
        <div className={`horizontal-group ${className}`}>
            <label htmlFor={id}>{label}</label>
            <input
                id={id}
                type={type ?? 'text'}
                onFocus={(e) => e.target.select()}
                ref={ref}
                value={value}
                onChange={(e) => (onChange?.(e.target.value, name ?? null))}
                onBlur={(e) => (onBlur?.(e.target.value, name ?? null))}
            />
        </div>
    );
    });

InputOneLine.displayName = 'InputOneLine';

export default InputOneLine;

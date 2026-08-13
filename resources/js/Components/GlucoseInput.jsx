import { forwardRef } from "react";

/**
 * Compact labelled glucose field for factor strips / dashboard.
 * Emits (value, field) on change/blur — same contract as InputTwoLines —
 * so the parent can parse Glucose, recalculate the menu, and save.
 *
 * Prefer `field` for the logical key passed to callbacks. Keep HTML `name`
 * separate (or omit it) so it cannot collide with form/Inertia data keys.
 */
const GlucoseInput = forwardRef(
    (
        {
            label,
            value,
            onChange,
            onBlur,
            id,
            name = "",
            field,
            className = "",
            inputClassName = "",
            type = "text",
        },
        ref
    ) => {
        const fieldKey = field ?? (name || undefined);

        return (
            <div className={className}>
                {label != null && label !== "" && (
                    <label htmlFor={id}>{label}</label>
                )}
                <input
                    ref={ref}
                    id={id}
                    name={name || undefined}
                    type={type}
                    className={inputClassName}
                    value={value}
                    onFocus={(e) => e.target.select()}
                    onChange={(e) => onChange?.(e.target.value, fieldKey)}
                    onBlur={(e) => onBlur?.(e.target.value, fieldKey)}
                />
            </div>
        );
    }
);

GlucoseInput.displayName = "GlucoseInput";

export default GlucoseInput;

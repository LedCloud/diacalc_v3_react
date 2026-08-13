const InputTwoLines = ({ label, value, onChange, onBlur, id, name='', field, className = '' }) => (
    <div className={`vertical-group ${className}`}>
        <label htmlFor={id}>{label}</label>
        <input
            name={name}
            id={id}
            onFocus={(e) => e.target.select()}
            value={value}
            onChange={(e) => (onChange ? onChange(e.target.value, field ?? null) : () => {})}
            onBlur={(e) => ( onBlur? onBlur(e.target.value, field ?? null) : () => {})}
        />
    </div>
);

export default InputTwoLines;

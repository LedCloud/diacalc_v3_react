import Tooltip from "@/Components/Tooltip.jsx";

function Button({
    icon,
    onClick,
    disabled = false,
    tooltip = false,
    plain = false,
    className = '',
}) {
    const button = (
        <button
            type="button"
            className={`menu-bar__btn btn${plain ? ' menu-bar__btn--plain' : ''}${className ? ` ${className}` : ''}`}
            disabled={disabled}
            onClick={onClick}
            aria-label={tooltip}
        >
            {icon}
        </button>
    );

    if (!tooltip) {
        return button;
    }

    return <Tooltip text={tooltip}>{button}</Tooltip>;
}

function Separator() {
    return <span className="menu-bar__separator" role="separator" aria-hidden="true" />;
}

function Spacer() {
    return <span className="menu-bar__spacer" aria-hidden="true" />;
}

function Fill({ children }) {
    return <div className="menu-bar__fill">{children}</div>;
}

export default function MenuBar({ children, className = '' }) {
    return (
        <div className={`menu-bar${className ? ` ${className}` : ''}`}>
            {children}
        </div>
    );
}

MenuBar.Button = Button;
MenuBar.Separator = Separator;
MenuBar.Spacer = Spacer;
MenuBar.Fill = Fill;

export default function Pane({children, header = null, className = ''}) {
    return (
        <div className={`panes__pane ${className}`}>
            {header ? <div className="panes__pane_header">{header}</div> : null}
            <div className="panes__pane_content">
                {children}
            </div>
        </div>
    );
};

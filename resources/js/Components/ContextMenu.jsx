export default function ContextMenu({ menuSettings, menuRef, menuItems, onClose}) {
    if (!menuSettings?.visible) return null;

    return (
    <div
        ref={menuRef}
        className="context-menu"
        style={{top: menuSettings.y, left: menuSettings.x}}
    >
        {menuItems?.map((item, index) => (
            <div className="context-menu-item" key={index} onClick={() => {
                onClose?.();
                item.handler?.();
            }}>{item.name}</div>
        ))}
    </div>
    );
}

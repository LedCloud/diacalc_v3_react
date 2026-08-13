import React from "react";

export default function Tag({id, active, name, clickHandler})
{
    return (
        <button type="button"
                onClick={() => clickHandler(id)}
                className={`tab-btn ${active === id ? 'active' : ''}`}
        >{name}
        </button>
    );
};

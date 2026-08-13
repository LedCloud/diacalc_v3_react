export default function Button({className = '',
                                   type = 'button',
                                   disabled,
                                   children,
                                   ...props})
{
    return (
        <button
            {...props}
            type={type}
            className={
                `px-2 py-1 w-32 rounded ring-2 ring-offset-1 uppercase ${
                    disabled && 'opacity-25'
                } ` + className
            }
            disabled={disabled}
        >
            {children}
        </button>
    );
}

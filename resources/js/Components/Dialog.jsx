import React from 'react';

export default function Dialog({children,
                                   showDlg = false,
                                   header = '',
                                   okText,
                                   okHandler,
                                   cancelText = '',
                                   cancelHandler = null,
                                   closeHandler = null,
                                    className=''
                                })
{
    if (!showDlg) return null;

    const Wrapper = className ? 'div' : React.Fragment;
    const wrapperProps = className ? { className: `alert alert-${className}` } : {};

    return (
            <div className="dialog">
                <div className={`dialog__header ${className ? className : ''}`}>
                    <div className="dialog__header_header">{header}</div>
                    <div className="dialog__header_close"
                        onClick={closeHandler}>X</div>
                </div>
                <div className="dialog__content">
                    <Wrapper {...wrapperProps}>
                    {children}
                    </Wrapper>
                </div>
                <div className="dialog__footer">
                    <button className="dialog__footer__button btn primary dialog__footer__button__ok"
                            type="button"
                            onClick={okHandler}>{okText}
                    </button>
                    {cancelText !== '' && (
                        <button className="dialog__footer__button btn default dialog__footer__button__cancel"
                                type="button"
                            onClick={cancelHandler}>{cancelText}</button>
                    )}
                </div>
            </div>
        );
}

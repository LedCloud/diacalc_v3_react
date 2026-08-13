import React, {useEffect, useState} from "react";

export default function CalculableInput({label, id, valueIn, fractions=0, setHandler})
{
    const [value, setValue] = useState(valueIn);
    const [errors, setError] = useState([]);

    // Синхронизируем локальный стейт, если valueIn изменился в родителе
    useEffect(() => {
        setValue(String(valueIn ?? ""));
    }, [valueIn]);

    // Регулярное выражение разрешает ТОЛЬКО: цифры, точки, +, -, *, /, (, ) и пробелы
    const allowed = /^[0-9+\-*/().\s]+$/;

    const errorSetter = (error) => {
        // Создаем НОВЫЙ массив с помощью spread-оператора
        setError(last => [...last, error]);
    };

    // const errorSetter = (error) => {
    //     setError(last => {
    //         last.push(error);
    //         setError(last);
    //     });
    // };

    const handleBlur = () => {
        if (!value.trim()) return;

        if (!allowed.test(value)) {
            errorSetter('Разрешены только числа, скобки и знаки +, -, *, /');
            return;
        }

        try {
            // Безопасное вычисление очищенной строки без использования eval()
            const calculate = new Function(`return (${value})`);
            const result = calculate();

            if (typeof result === 'number' && !isNaN(result)) {
                const res = String(result.toFixed(fractions));
                setValue(res);
                setHandler?.(res);
                setError([]);
            } else {
                errorSetter('Некорректное математическое выражение (например, незакрытая скобка).');
            }
        } catch (error) {
            errorSetter('Некорректное математическое выражение.');
        }
    };

    return (<>
        {label && <label htmlFor={id}>{label}</label>}
        <input
            id={id}
            type="text"
            value={value}
            onFocus={(e) => e.target.select()}
            onChange={(e) => {
                setValue(e.target.value);
                //setHandler(e.target.value);
            }}
            onBlur={handleBlur}
            placeholder="(15+30)*3 - 10"
        />
        {errors && errors.map(
            error => {
                return <div className="validation-error">{error}</div>;
            }
        )}
    </>);
}

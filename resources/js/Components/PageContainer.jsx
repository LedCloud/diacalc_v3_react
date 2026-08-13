export default function PageContainer({children, header = null, classNameExternal = '', classNameInternal = ''}){
    return (
        <div className={`py-6 h-full ${classNameExternal}`}>
            <div className={`max-w-7xl mx-auto sm:px-6 lg:px-8 h-full ${classNameInternal}`}>
                {children}
            </div>
        </div>
    );
}

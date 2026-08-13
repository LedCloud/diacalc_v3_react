@if (session()->has('notification'))
    <div class="alert alert-success"
         x-data="{ show: false }"
         x-init="show = true; setTimeout(() => show = false, 3000)"
         x-show="show"
    >
        {{ session('notification') }}
    </div>
@elseif(session()->has('warning'))
    <div class="alert alert-warning"
         x-data="{ show: false }"
         x-init="show = true; setTimeout(() => show = false, 3000)"
         x-show="show"
    >
        {{ session('warning') }}
    </div>
@elseif(session()->has('error'))
    <div class="alert alert-error"
         x-data="{ show: false }"
         x-init="show = true; setTimeout(() => show = false, 3000)"
         x-show="show"
    >
        {{ session('error') }}
    </div>
@endif

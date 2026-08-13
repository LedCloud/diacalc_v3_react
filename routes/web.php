<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Http\Request;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

//Route::get('/dashboard', function () {
//    return Inertia::render('Dashboard');
//})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', \App\Http\Middleware\InjectRouteTranslations::class])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/settings_react', [\App\Http\Controllers\SettingsController::class, 'index'])
        ->name('settings');
    Route::patch('/settings_react', [\App\Http\Controllers\SettingsController::class, 'update'])
        ->name('settings');
    Route::post('/settings_react/fill-products', [\App\Http\Controllers\SettingsController::class, 'fillProducts'])
        ->name('settings.fill_products');

    Route::get('/factors', [App\Http\Controllers\FactorsController::class, 'index'])
        ->name('factors');
    Route::patch('/factors', [App\Http\Controllers\FactorsController::class, 'update'])
        ->name('factors');

    Route::get('/dashboard', [App\Http\Controllers\DashbordController::class, 'index'])
        ->name('dashboard');
    Route::post('/dashboard/updatemenu', [App\Http\Controllers\DashbordController::class, 'update'])
        ->name('dashboard.updatemenu');
    Route::post('/dashboard/updateating', [App\Http\Controllers\DashbordController::class, 'updateFactors'])
        ->name('dashboard.updatefactors');
    Route::delete('/dashboard/updatemenu/{menu}', [App\Http\Controllers\DashbordController::class, 'delete'])
        ->name('dashboard.deleteitem');
    Route::post('/dashboard/groups/{group}/move', [App\Http\Controllers\DashbordController::class, 'moveGroup'])
        ->name('dashboard.groups.move');
    Route::get('/dashboard/groups/{group}/products', [App\Http\Controllers\DashbordController::class, 'getProducts'])
        ->name('dashboard.groups.products')
        ->whereNumber('group');
    Route::get('/dashboard/products/search', [App\Http\Controllers\DashbordController::class, 'searchProducts'])
        ->name('dashboard.products.search');
    Route::post('/dashboard/products/{product}/add-to-menu', [App\Http\Controllers\DashbordController::class, 'addProductToMenu'])
        ->name('dashboard.products.add_to_menu');
    Route::patch('/dashboard/products/{product}', [App\Http\Controllers\DashbordController::class, 'updateProduct'])
        ->name('dashboard.products.update');
    Route::delete('/dashboard/products/{product}', [App\Http\Controllers\DashbordController::class, 'deleteProduct'])
        ->name('dashboard.products.delete');

    Route::get('/calculations', function () {
        if (auth()) {
            $be = Auth::user()->getSetting('User')['be'] ?? 10;
        } else {
            $be = 10;
        }
        return Inertia::render('Calculations', [
            'user' => [
                'be' => $be,
            ],
        ]);
    })->name('calculations');

    Route::get('/archive', [\App\Http\Controllers\ArchiveController::class, 'index'])
        ->name('archive');

    Route::get('/archive/groups/{group}/products', [\App\Http\Controllers\ArchiveController::class, 'getProducts'])
        ->name('archive.get_products');
    Route::post('/archive/add-to-products', [\App\Http\Controllers\ArchiveController::class, 'addToProducts'])
        ->name('archive.add_to_products');

    Route::post('/language/{lang}', function(Request $request, string $lang){
        $supportedLocales = config('app.supported_locales', ['en' => 'English']);

        // Abort if someone passes a locale we don't support
        if (!in_array($lang, $supportedLocales)) {
            abort(400);
        }

        // Save the choice in the user's session
        session()->put('locale', $lang);

        return back();
    })->name('language.switch');

    //Route::livewire('/settings', 'pages::settings')->name('settings');
    //Route::livewire('/factors', 'pages::more.factors')->name('factors');
    //Route::livewire('/factors/create', 'pages::more.factors.create')->name('factors.create');
    //Route::livewire('/factors/{id}/edit', 'pages::more.factors.create')->name('factors.edit');
});



require __DIR__.'/auth.php';

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

//Route::get('/meal', function () {
//    return Inertia::render('Meal');
//})->middleware(['auth', 'verified'])->name('meal');

Route::middleware(['auth', \App\Http\Middleware\InjectRouteTranslations::class])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])
        ->name('settings.index');
    Route::patch('/settings', [\App\Http\Controllers\SettingsController::class, 'update'])
        ->name('settings.update');
    Route::post('/settings/fill-products', [\App\Http\Controllers\SettingsController::class, 'fillProducts'])
        ->name('settings.fill_products');

    Route::get('/factors', [App\Http\Controllers\FactorsController::class, 'index'])
        ->name('factors.index');
    Route::patch('/factors', [App\Http\Controllers\FactorsController::class, 'update'])
        ->name('factors.update');

    Route::get('/meal', [App\Http\Controllers\MealController::class, 'index'])
        ->name('meal');
    Route::post('/meal/updatemenu', [App\Http\Controllers\MealController::class, 'update'])
        ->name('meal.updatemenu');
    Route::post('/meal/updateating', [App\Http\Controllers\MealController::class, 'updateFactors'])
        ->name('meal.updatefactors');
    Route::delete('/meal/updatemenu/{menu}', [App\Http\Controllers\MealController::class, 'deleteitem'])
        ->name('meal.deleteitem');
    Route::post('/meal/groups/{group}/move', [App\Http\Controllers\MealController::class, 'moveGroup'])
        ->name('meal.groups.move');
    Route::get('/meal/groups/{group}/products', [App\Http\Controllers\MealController::class, 'getProducts'])
        ->name('meal.groups.products')
        ->whereNumber('group');
    Route::get('/meal/products/search', [App\Http\Controllers\MealController::class, 'searchProducts'])
        ->name('meal.products.search');
    Route::post('/meal/products/{product}/add-to-menu', [App\Http\Controllers\MealController::class, 'addProductToMenu'])
        ->name('meal.products.add_to_menu');
    Route::patch('/meal/products/{product}', [App\Http\Controllers\MealController::class, 'updateProduct'])
        ->name('meal.products.update');
    Route::patch('/meal/products/{product}/move', [App\Http\Controllers\MealController::class, 'moveProductToGroup'])
        ->name('meal.products.move');
    Route::delete('/meal/products/{product}', [App\Http\Controllers\MealController::class, 'deleteProduct'])
        ->name('meal.products.delete');

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
    })->name('calculations.index');

    Route::get('/diary', [\App\Http\Controllers\DiaryController::class, 'index'])
        ->name('diary.index');
    Route::get('/diary/records', [\App\Http\Controllers\DiaryController::class, 'records'])
        ->name('diary.records');

    Route::get('/archive', [\App\Http\Controllers\ArchiveController::class, 'index'])
        ->name('archive.index');

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
});



require __DIR__.'/auth.php';

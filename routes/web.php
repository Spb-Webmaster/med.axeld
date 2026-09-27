<?php

use App\Http\Controllers\Ajax\CityController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Axios\AxiosController;
use App\Http\Controllers\Cabinet\BloodPressureController;
use App\Http\Controllers\CabinetController;
use App\Http\Controllers\FancyBox\FancyBoxController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/** Главная **/
Route::get('/', [HomeController::class, 'index'])->name('home');
/** ///Главная **/


/** Город (сессия) **/
Route::post('/set-city', [CityController::class, 'setCity'])->name('city.set');
/** ///Город (сессия) **/

/** Вход в личный кабинет. Регистрации нет — пользователей заводит администратор в MoonShine **/
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'create')->middleware('guest')->name('login');
    Route::post('/login', 'store')->middleware(['guest', 'throttle:10,1'])->name('login.store');
    Route::post('/logout', 'destroy')->middleware('auth')->name('logout');
});
/** ///Вход **/

/**
 | Личный кабинет.
 |
 | auth.session нужен ради CabinetController::updatePassword: без него
 | logoutOtherDevices() не закрывает чужие сессии после смены пароля.
 **/
Route::controller(CabinetController::class)
    ->middleware(['auth', 'auth.session'])
    ->group(function () {
        Route::get('/cabinet', 'index')->name('cabinet');
        Route::get('/cabinet/setting', 'edit')->name('cabinet.edit');
        Route::put('/cabinet/setting', 'update')->name('cabinet.update');
        Route::put('/cabinet/setting/password', 'updatePassword')->name('cabinet.password');
    });

/** Дневник давления: календарь и запись замеров **/
Route::controller(BloodPressureController::class)
    ->middleware(['auth', 'auth.session'])
    ->prefix('cabinet/pressure')
    ->name('cabinet.pressure')
    ->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store')->name('.store');

        // Замер на день один, поэтому ключ записи — дата, а не id
        Route::delete('/{date}', 'destroy')
            ->where('date', '\d{4}-\d{2}-\d{2}')
            ->name('.destroy');
    });
/** ///Дневник давления **/
/** ///Личный кабинет **/

/** FancyBox AJAX **/
Route::controller(FancyBoxController::class)->group(function () {
    Route::post('/fancybox-ajax', 'fancybox');
});
/** ///FancyBox AJAX **/

/** Axios async forms **/
Route::controller(AxiosController::class)->group(function () {
    Route::post('/upload-form-async', 'async');
    Route::post('/call-me-blue', 'callMeBlue');
});
/** ///Axios async forms **/

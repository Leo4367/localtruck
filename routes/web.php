<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BrokerController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/appointments', [AppointmentController::class, 'show'])->name('appointment.show');
});

Route::get('/pickup', function () {
    return Inertia::render('Appoint/Pickup');
})->name('pickup');

Route::get('/delivery', function () {
    return Inertia::render('Appoint/Delivery');
})->name('delivery');

Route::get('/broker', function () {
    return Inertia::render('Appoint/Broker');
})->name('broker');

Route::get('/price',function (){
    return Inertia::render('Broker/Price');
});


Route::post('/appointment', [AppointmentController::class, 'store'])->name('appointment.store');
Route::get('/appointment/forbidden-dates', [AppointmentController::class, 'forbiddenDates'])->name('appointment.forbidden-dates');
Route::get('/appointment/warehouse', [AppointmentController::class, 'getWarehouses'])->name('appointment.warehouse');
Route::get('/booked-slots', [AppointmentController::class, 'getBookedSlots'])->name('appointment.booked-slots');
Route::get('/booked-warehouse', [AppointmentController::class, 'getBookedWarehouse'])->name('appointment.booked-warehouse');
Route::post('/broker-sendemail', [InquiryController::class, 'store'])->name('inquiry.store');
Route::get('/broker-sendemail', [InquiryController::class, 'index'])->name('inquiry.index');

require __DIR__.'/auth.php';

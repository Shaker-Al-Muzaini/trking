<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\PropertyController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(callback: function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    Route::inertia('about', 'About')->name('about');
    Route::get('contact', [ContactController::class, 'index'])
        ->name('contact');
    Route::post('contact', [ContactController::class, 'store'])
        ->name('contact.store');
    Route::get('/contact/messages', [ContactController::class, 'messages'])->name('contact.messages');
    Route::get('property', [PropertyController::class, 'index'])->name('property');
    Route::post('property', [PropertyController::class, 'store'])->name('property.store');
    Route::put('/property/{property}', [PropertyController::class, 'update'])->name('property.update');

});

require __DIR__.'/settings.php';

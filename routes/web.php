<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
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
    Route::get('/books', [BookController::class, 'index'])->name('books');
    Route::post('/books/{book}', [BookController::class, 'update'])->name('books.update.patch');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');


    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');


});

require __DIR__.'/settings.php';

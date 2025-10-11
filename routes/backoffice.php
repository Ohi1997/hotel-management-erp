<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('/dashboard', 'backoffice.dashboard')->name('backoffice.dashboard');

    Route::get('/customers', \App\Livewire\Backoffice\Customers\Index::class)->name('backoffice.customers.index');
});

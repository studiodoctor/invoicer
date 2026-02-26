<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Quote signing (public)
Route::get('/quotes/sign/{token}', [QuoteController::class, 'sign'])->name('quotes.sign');
Route::post('/quotes/sign/{token}', [QuoteController::class, 'processSign'])->name('quotes.process-sign');

// Protected routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Clients
    Route::resource('clients', ClientController::class);

    // Quotes
    Route::resource('quotes', QuoteController::class);
    Route::post('/quotes/{quote}/send', [QuoteController::class, 'send'])->name('quotes.send');
    Route::get('/quotes/{quote}/pdf', [QuoteController::class, 'pdf'])->name('quotes.pdf');
    Route::get('/quotes/{quote}/preview', [QuoteController::class, 'preview'])->name('quotes.preview');
    Route::post('/quotes/{quote}/duplicate', [QuoteController::class, 'duplicate'])->name('quotes.duplicate');
    Route::post('/quotes/{quote}/convert', [QuoteController::class, 'convert'])->name('quotes.convert');

    // Invoices
    Route::resource('invoices', InvoiceController::class);
    Route::post('/invoices/{invoice}/send', [InvoiceController::class, 'send'])->name('invoices.send');
    Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
    Route::get('/invoices/{invoice}/preview', [InvoiceController::class, 'preview'])->name('invoices.preview');
    Route::get('/invoices/{invoice}/receipt', [InvoiceController::class, 'receipt'])->name('invoices.receipt');
    Route::post('/invoices/{invoice}/duplicate', [InvoiceController::class, 'duplicate'])->name('invoices.duplicate');
    Route::post('/invoices/{invoice}/payment', [InvoiceController::class, 'recordPayment'])->name('invoices.payment');
    Route::post('/invoices/{invoice}/mark-paid', [InvoiceController::class, 'markAsPaid'])->name('invoices.mark-paid');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::delete('/settings/logo', [SettingsController::class, 'removeLogo'])->name('settings.remove-logo');
});
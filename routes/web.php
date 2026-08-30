<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ContentAdminController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SubmissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/assistant/poser-une-question', [ChatbotController::class, 'answer'])->middleware('throttle:30,1')->name('chatbot.answer');
Route::get('/index.html', [SiteController::class, 'home']);

$pages = [
    'about', 'admissions', 'bibliotheque', 'contact',
    'incubateur', 'inscription-master', 'recherche',
];

foreach ($pages as $page) {
    Route::get("pages/{$page}.html", [SiteController::class, 'staticPage'])
        ->defaults('page', $page)
        ->name("pages.{$page}");
}

Route::get('/pages/formations.html', [SiteController::class, 'programs'])->name('pages.formations');
Route::get('/pages/actualites.html', [SiteController::class, 'articles'])->name('pages.actualites');
Route::get('/pages/annonces-concours.html', [SiteController::class, 'announcements'])->name('pages.annonces-concours');
Route::get('/pages/vie-etudiante.html', [SiteController::class, 'studentLife'])->name('pages.vie-etudiante');
Route::get('/pages/entreprises.html', [SiteController::class, 'partners'])->name('pages.entreprises');
Route::get('/administration', [AuthController::class, 'administration'])->name('admin.login');
Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AuthController::class, 'create'])->name('login');
    Route::post('/connexion', [AuthController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/deconnexion', [AuthController::class, 'destroy'])->name('logout');
    Route::prefix('administration/site')->name('admin.content.')->group(function () {
        Route::get('/', [ContentAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/{resource}', [ContentAdminController::class, 'index'])->name('index');
        Route::get('/{resource}/nouveau', [ContentAdminController::class, 'create'])->name('create');
        Route::post('/{resource}', [ContentAdminController::class, 'store'])->name('store');
        Route::get('/{resource}/{item}/modifier', [ContentAdminController::class, 'edit'])->name('edit');
        Route::put('/{resource}/{item}', [ContentAdminController::class, 'update'])->name('update');
        Route::delete('/{resource}/{item}', [ContentAdminController::class, 'destroy'])->name('destroy');
    });
});

Route::post('/contact', [SubmissionController::class, 'contact'])->name('contact.store');
Route::post('/admissions', [SubmissionController::class, 'admission'])->name('admissions.store');
Route::post('/inscription-master', [SubmissionController::class, 'master'])->name('master.store');

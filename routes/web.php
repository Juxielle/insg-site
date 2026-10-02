<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ContentAdminController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\SubmissionAdminController;
use App\Http\Controllers\ContestAdminController;
use App\Http\Controllers\ContestEntryController;
use App\Http\Controllers\ContestPublicController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/assistant/poser-une-question', [ChatbotController::class, 'answer'])->middleware('throttle:30,1')->name('chatbot.answer');
Route::get('/index.html', [SiteController::class, 'home']);
Route::get('/concours', [ContestPublicController::class, 'index'])->name('contests.index');
Route::get('/concours/resultats', [ContestPublicController::class, 'results'])->name('contests.results');
Route::post('/concours/resultats/recherche', [ContestPublicController::class, 'search'])->middleware('throttle:20,1')->name('contests.results.search');

$pages = [
    'about', 'admissions', 'bibliotheque', 'contact',
    'incubateur', 'entrepreneuriat', 'inscription-master', 'recherche',
    'international',
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
    Route::prefix('administration/concours')->name('admin.contests.')->group(function () {
        Route::get('/', [ContestAdminController::class, 'index'])->name('index');
        Route::get('/nouveau', [ContestAdminController::class, 'create'])->name('create');
        Route::post('/', [ContestAdminController::class, 'store'])->name('store');
        Route::get('/{contest}', [ContestAdminController::class, 'show'])->name('show');
        Route::get('/{contest}/modifier', [ContestAdminController::class, 'edit'])->name('edit');
        Route::put('/{contest}', [ContestAdminController::class, 'update'])->name('update');
        Route::get('/{contest}/resultats', [ContestAdminController::class, 'results'])->name('results');
        Route::put('/{contest}/resultats', [ContestAdminController::class, 'saveResults'])->name('results.save');
        Route::post('/{contest}/publier', [ContestAdminController::class, 'publish'])->name('publish');
        Route::post('/{contest}/depublier', [ContestAdminController::class, 'unpublish'])->name('unpublish');
        Route::get('/{contest}/export.csv', [ContestAdminController::class, 'export'])->name('export');
        Route::prefix('/{contest}/tours/{round}')->where(['round' => '[12]'])->group(function () {
            Route::get('/etudiants/nouveau', [ContestEntryController::class, 'create'])->name('entries.create');
            Route::post('/etudiants', [ContestEntryController::class, 'store'])->name('entries.store');
            Route::get('/etudiants/{entry}/modifier', [ContestEntryController::class, 'edit'])->name('entries.edit');
            Route::put('/etudiants/{entry}', [ContestEntryController::class, 'update'])->name('entries.update');
            Route::delete('/etudiants/{entry}', [ContestEntryController::class, 'destroy'])->name('entries.destroy');
            Route::delete('/etudiants', [ContestEntryController::class, 'clear'])->name('entries.clear');
            Route::post('/import-excel', [ContestEntryController::class, 'import'])->name('entries.import');
            Route::get('/resultats.pdf', [ContestEntryController::class, 'pdf'])->name('pdf');
        });
    });
    Route::prefix('administration/site')->name('admin.content.')->group(function () {
        Route::get('/', [ContentAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/demandes', [SubmissionAdminController::class, 'index'])->name('submissions.index');
        Route::get('/demandes/{submission}', [SubmissionAdminController::class, 'show'])->name('submissions.show');
        Route::put('/demandes/{submission}/statut', [SubmissionAdminController::class, 'updateStatus'])->name('submissions.status');
        Route::get('/demandes/{submission}/documents/{document}', [SubmissionAdminController::class, 'download'])->name('submissions.download');
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
Route::get('/inscription-master/suivi', [SubmissionController::class, 'masterTracking'])->name('master.tracking');
Route::post('/inscription-master/suivi', [SubmissionController::class, 'trackMaster'])->middleware('throttle:20,1')->name('master.track');

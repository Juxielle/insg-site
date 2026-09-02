<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ContentAdminController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\SubmissionAdminController;
use App\Http\Controllers\ContestAdminController;
use App\Http\Controllers\ContestPublicController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/assistant/poser-une-question', [ChatbotController::class, 'answer'])->middleware('throttle:30,1')->name('chatbot.answer');
Route::get('/index.html', [SiteController::class, 'home']);
Route::get('/concours', [ContestPublicController::class, 'index'])->name('contests.index');
Route::get('/concours/{contest}/inscription', [ContestPublicController::class, 'create'])->name('contests.apply');
Route::post('/concours/{contest}/inscription', [ContestPublicController::class, 'store'])->middleware('throttle:10,1')->name('contests.store');
Route::get('/concours/confirmation/{code}', [ContestPublicController::class, 'confirmation'])->name('contests.confirmation');
Route::get('/concours/resultats', [ContestPublicController::class, 'results'])->name('contests.results');
Route::post('/concours/resultats/recherche', [ContestPublicController::class, 'search'])->middleware('throttle:20,1')->name('contests.results.search');

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
    Route::prefix('administration/concours')->name('admin.contests.')->group(function () {
        Route::get('/', [ContestAdminController::class, 'index'])->name('index');
        Route::get('/nouveau', [ContestAdminController::class, 'create'])->name('create');
        Route::post('/', [ContestAdminController::class, 'store'])->name('store');
        Route::get('/{contest}', [ContestAdminController::class, 'show'])->name('show');
        Route::get('/{contest}/modifier', [ContestAdminController::class, 'edit'])->name('edit');
        Route::put('/{contest}', [ContestAdminController::class, 'update'])->name('update');
        Route::put('/{contest}/statut', [ContestAdminController::class, 'transition'])->name('transition');
        Route::get('/{contest}/candidatures', [ContestAdminController::class, 'applications'])->name('applications');
        Route::get('/{contest}/candidatures/nouvelle', [ContestAdminController::class, 'createApplication'])->name('applications.create');
        Route::post('/{contest}/candidatures', [ContestAdminController::class, 'storeApplication'])->name('applications.store');
        Route::get('/candidatures/{application}', [ContestAdminController::class, 'application'])->name('application');
        Route::put('/candidatures/{application}', [ContestAdminController::class, 'review'])->name('review');
        Route::get('/candidatures/{application}/documents/{document}', [ContestAdminController::class, 'downloadDocument'])->name('documents.download');
        Route::get('/candidatures/{application}/convocation', [ContestAdminController::class, 'convocation'])->name('convocation');
        Route::get('/{contest}/resultats', [ContestAdminController::class, 'results'])->name('results');
        Route::put('/{contest}/resultats', [ContestAdminController::class, 'saveResults'])->name('results.save');
        Route::post('/{contest}/resultats/valider', [ContestAdminController::class, 'validateResults'])->name('results.validate');
        Route::post('/{contest}/publier', [ContestAdminController::class, 'publish'])->name('publish');
        Route::post('/{contest}/depublier', [ContestAdminController::class, 'unpublish'])->name('unpublish');
        Route::get('/{contest}/export.csv', [ContestAdminController::class, 'export'])->name('export');
        Route::get('/{contest}/liste-officielle', [ContestAdminController::class, 'officialList'])->name('official-list');
        Route::post('/{contest}/import/apercu', [ContestAdminController::class, 'importPreview'])->name('import.preview');
        Route::post('/{contest}/import/confirmer', [ContestAdminController::class, 'importConfirm'])->name('import.confirm');
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

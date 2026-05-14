<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\ConceptController;
use App\Http\Controllers\GeneratedQuestionController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('/domains')->group(function(){

        Route::controller(DomainController::class)->group(function(){
            Route::get('/', 'index')->name('domains.index');
            Route::get('/create', 'create')->name('domains.create');
            Route::post('/', 'store')->name('domains.store');
            Route::get('/{domain}', 'show')->name('domains.show');
            Route::get('/{domain}/edit', 'edit')->name('domains.edit');
            Route::put('/{domain}', 'update')->name('domains.update');
            Route::delete('/{domain}', 'destroy')->name('domains.destroy');
        });

        Route::controller(ConceptController::class)->group(function(){
            Route::get('/{domain}/concept/create', 'create')->name('concepts.create');
            Route::post('/{domain}/concept', 'store')->name('concepts.store');
            Route::get('/concept/{concept}', 'show')->name('concepts.show');
            Route::get('/concept/{concept}/edit', 'edit')->name('concepts.edit');
            Route::put('/concept/{concept}', 'update')->name('concepts.update');
            Route::patch('/concept/{concept}/status', 'updateStatus')->name('concepts.status');
            Route::delete('/concept/{concept}', 'archive')->name('concepts.archive');
            Route::patch('/concept/{concept}/restore', 'restore')->name('concepts.restore');
            Route::delete('/concept/{concept}/force', 'forceDelete')->name('concepts.forceDelete');
            Route::post('/concept/{concept}/generate-questions', 'generateQuestions')->name('concepts.generate');
        });

        Route::controller(GeneratedQuestionController::class)->group(function(){
            Route::delete('/generated-questions/{generatedQuestion}', 'destroy')->name('generatedQuestions.destroy');
            Route::get('/generated-questions/{generatedQuestion}/archive', 'regenerate')->name('generatedQuestions.regenerate');
            Route::get('/concept/{concept}/questions','archivedQuestions')->name('generatedQuestions.index');
        });
    });
});

require __DIR__.'/auth.php';

<?php

use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\SourceController as AdminSourceController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ArticleReportController;
use App\Http\Controllers\ArticleVoteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ArticleController::class, 'index'])->name('home');

Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');

Route::middleware(['auth'])->group(function () {
    Route::get('/articles/create', [ArticleController::class, 'create'])->name('articles.create');
    Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');
    Route::post('/articles/{article}/votes', [ArticleVoteController::class, 'store'])->name('articles.votes.store');
    Route::post('/articles/{article}/reports', [ArticleReportController::class, 'store'])->name('articles.reports.store');
});

Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');

Route::get('/dashboard', DashboardController::class)->middleware(['auth'])->name('dashboard');

Route::middleware(['auth', 'role:admin|moderator'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboardController::class)->name('dashboard');
    Route::get('/articles', [AdminArticleController::class, 'index'])->name('articles.index');
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [AdminReportController::class, 'export'])->name('reports.export');
    Route::patch('/reports/{report}', [AdminReportController::class, 'update'])->name('reports.update');
    Route::get('/sources', [AdminSourceController::class, 'index'])->name('sources.index');
    Route::patch('/sources/{source}', [AdminSourceController::class, 'update'])->name('sources.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

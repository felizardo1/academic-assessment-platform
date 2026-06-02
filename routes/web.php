<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\SectionController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'role:teacher'])->name('dashboard');

// Rotas para docentes autenticados
Route::middleware(['auth', 'role:teacher'])->group(function () {
    Route::resource('exams', ExamController::class);
    Route::post('exams/{exam}/sections', [SectionController::class, 'store'])->name('sections.store');
    Route::delete('sections/{section}', [SectionController::class, 'destroy'])->name('sections.destroy');
    Route::get('exams/{exam}/generate', [PdfController::class, 'generate'])->name('exams.generate');
    Route::get('exams/{exam}/download/exam', [PdfController::class, 'downloadExam'])->name('exams.download.exam');
    Route::get('exams/{exam}/download/correction', [PdfController::class, 'downloadCorrection'])->name('exams.download.correction');
});

// Rotas para administrador
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('admin/users', AdminController::class)->names('admin.users');
});


require __DIR__.'/settings.php';

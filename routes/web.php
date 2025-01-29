<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StreamDataController;

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/register', function() {
    return view('register');
})->name('register');

Route::post('/login', [AuthController::class, 'login'])->name('login.api');
Route::post('/register', [AuthController::class, 'register'])->name('register.api');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/stream_data/branches_label', [StreamDataController::class, 'getBranchesAndLabel'])->name('stream_data.branches_label');
Route::post('/stream_data/by_labels', [StreamDataController::class, 'getSnapshotByLabels'])->name('stream_data.by_labels');
Route::post('/stream_data/history_by_labels', [StreamDataController::class, 'getHistoryByLabels'])->name('stream_data.history_by_labels');
Route::post('/stream_data/history_by_labels_update', [StreamDataController::class, 'getSnapshotByLabelsAndLastId'])->name('stream_data.history_by_labels_update');

Route::get('/', [StreamDataController::class, 'dashboard'])->name('dashboard')->middleware('auth');


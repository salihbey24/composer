<?php 
Route::controller(\App\Http\Controllers\Users1Controller::class)->group(function () {

    Route::get('/Users1', 'index');
    Route::get('/Users1/create', 'create');
    Route::post('/Users1/create', 'store');
    Route::get('/Users1/{id}', 'show');
    Route::get('/Users1/edit/{id}', 'edit');
    Route::post('/Users1/edit/{id}', 'update');
    Route::get('/Users1/destroy/{id}', 'destroy');
});
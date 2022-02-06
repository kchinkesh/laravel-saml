<?php

Route::group(['prefix' => 'saml', 'middleware' => 'saml'], function () {
    Route::get('/logout', [Kchinkesh\LaravelSaml\Http\Controllers\Saml2Controller::class, 'logout'])->name('saml_logout');
    Route::get('/login', [Kchinkesh\LaravelSaml\Http\Controllers\Saml2Controller::class, 'login'])->name('saml_login');
    Route::get('/metadata', [Kchinkesh\LaravelSaml\Http\Controllers\Saml2Controller::class, 'metadata'])->name('saml_metadata');
    Route::post('/acs', [Kchinkesh\LaravelSaml\Http\Controllers\Saml2Controller::class, 'acs'])->name('saml_acs');
    Route::get('/sls', [Kchinkesh\LaravelSaml\Http\Controllers\Saml2Controller::class, 'sls'])->name('saml_sls');
});

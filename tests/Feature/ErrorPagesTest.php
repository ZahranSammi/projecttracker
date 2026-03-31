<?php

use Illuminate\Support\Facades\Route;

beforeEach(function (): void {
    Route::get('/_test/errors/{status}', function (string $status) {
        abort((int) $status);
    });
});

it('renders the 404 page with the matching status code', function (): void {
    $response = $this->get('/missing-glacier-route');

    $response->assertNotFound();
    $response->assertSee('404');
    $response->assertSee('That page drifted out of view.');
});

it('renders server error pages with the matching status code', function (): void {
    $response = $this->get('/_test/errors/503');

    $response->assertStatus(503);
    $response->assertSee('503');
    $response->assertSee('Glacier is temporarily unavailable.');
});

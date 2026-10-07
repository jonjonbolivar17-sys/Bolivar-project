<?php

test('the Arakan public emergency response portal is displayed with current weather', function () {
    $response = $this->get('/');

    $response
        ->assertSeeText('Arakan')
        ->assertSeeText('Current Weather')
        ->assertSeeText('Arakan, Cotabato')
        ->assertSeeText('Real-time disaster response')
        ->assertSeeText('Fast desk report')
        ->assertSeeText('Barangay status matrix')
        ->assertSeeText('MDRRMO Desk')
        ->assertSeeText('064-521-8899');
});

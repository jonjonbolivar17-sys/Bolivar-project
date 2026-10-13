<?php

test('the public accident reporting landing page explains the reporting process', function () {
    $response = $this->get('/');

    $response
        ->assertSeeText('Accident Reporting System')
        ->assertSeeText('Report Accidents.')
        ->assertSeeText('Record Locations.')
        ->assertSeeText('Track Progress.')
        ->assertSeeText('Everything You Need to Report and Monitor Incidents')
        ->assertSeeText('How the Reporting Process Works')
        ->assertSeeText('A More Organized Way to Report Accidents')
        ->assertSeeText('Ready to Get Started?')
        ->assertSee('href="'.route('login').'"', false)
        ->assertSee('href="'.route('register').'"', false)
        ->assertDontSeeText('064-521-8899')
        ->assertDontSeeText('Real-time disaster response');
});

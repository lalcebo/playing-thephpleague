<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

it('should can be render as json', function () {
    expect(get('/'))
        ->assertOk()
        ->assertExactJson([
            'name' => 'League Framework',
            'title' => 'My New Simple API',
            'version' => 1,
        ]);
});

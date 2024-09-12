<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| You may extend TestCase to apply specific setup for all tests in specific
| directories or globally. Pest uses `beforeEach` to make configurations available.
|
*/

// Apply TestCase to both Feature and Unit tests
pest()->extend(TestCase::class)->in('Feature', 'Unit');

// Apply RefreshDatabase only to Feature tests (or other specific test directories that need database)
uses(RefreshDatabase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Scoped Setup for Feature Tests
|--------------------------------------------------------------------------
|
| To make the route names available only for Feature tests, we use the `beforeEach`
| hook inside the `uses()` function to limit it to the `Feature` test directory.
|
*/

uses()->beforeEach(function () {
    // Make route names available globally for feature tests only
    $this->routeNames = config('routes');
})->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions.
| The "expect()" function gives you access to a set of "expectations" methods that you
| can use to assert different things.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| Here you can expose helpers as global functions to help you reduce the number
| of lines of code in your test files.
|
*/

function something()
{
    // ..
}

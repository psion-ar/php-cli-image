<?php

declare(strict_types=1);

use Pest\Expectation;
use Spatie\TemporaryDirectory\TemporaryDirectory;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

pest()->extend(Tests\TestCase::class)->in('Feature');
pest()->beforeAll(function () {
    (new TemporaryDirectory(getTempPath()))->delete();
})->beforeEach(function () {
    $this->tempDir = (new TemporaryDirectory(getTestSupportPath()))->name('temp');
})->in('.');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toHaveMime', function (string $expectedMime): Expectation {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $actualMime = finfo_file($finfo, $this->value);
    finfo_close($finfo);

    expect($actualMime)->toBe($expectedMime);

    return $this;
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function getTestSupportPath(string $suffix = ''): string
{
    return __DIR__."/TestSupport/{$suffix}";
}

function getTempPath(string $suffix = ''): string
{
    return getTestSupportPath("temp/{$suffix}");
}

function getTestFiles(string $filename = ''): string
{
    return getTestSupportPath("TestFiles/{$filename}");
}

function getAllTestFiles(): array
{
    return glob(getTestSupportPath('TestFiles/*.*'), GLOB_NOSORT);
}

function getTestJpg(): string
{
    return getTestFiles('test.jpg');
}

function callMethod(object $object, string $method, array $args = []): mixed
{
    $class = new \ReflectionClass($object);
    $method = $class->getMethod($method);

    return $args === []
        ? $method->invoke($object)
        : $method->invokeArgs($object, $args);
}

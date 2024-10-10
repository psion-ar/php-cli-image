<?php

declare(strict_types=1);

describe('is_image', function (): void {
    it('returns false if file does not exist')
        ->expect(fn () => is_image('foo/bar.txt'))
        ->toBeFalse();

    it('returns false if path is directory')
        ->expect(fn () => is_image(getTestSupportPath()))
        ->toBeFalse();

    it('returns false if file is less than 12 bytes', function () {
        $path = $this->tempDir->path('test.txt');

        file_put_contents($path, '');

        expect(filesize($path))->toBeLessThan(12);
        expect(is_image($path))->toBeFalse();

        unlink($path);
    });

    it('returns false if file is not an image', function () {
        $path = $this->tempDir->path('test.txt');
        file_put_contents($path, 'foo bar baz qux');

        expect(filesize($path))->toBeGreaterThan(12);
        expect(is_image($path))->toBeFalse();

        unlink($path);
    });

    it('returns true if file is an image')
        ->with(getAllTestFiles())
        ->expect(fn (string $path) => is_image($path))
        ->toBeTrue();
});

describe('getReflectionPropertyValue', function (): void {
    beforeEach(function (): void {
        $this->object = new class
        {
            public string $foo = 'foo';

            protected string $bar = 'bar';

            private string $baz = 'baz';
        };
    });

    it('gets the value of a public property')
        ->expect(fn () => getReflectionPropertyValue($this->object, 'foo'))->toEqual('foo');

    it('gets the value of a protected property')
        ->expect(fn () => getReflectionPropertyValue($this->object, 'bar'))->toEqual('bar');

    it('gets the value of a private property')
        ->expect(fn () => getReflectionPropertyValue($this->object, 'baz'))->toEqual('baz');

    it('fails if the property does not exist')
        ->expect(fn () => getReflectionPropertyValue($this->object, 'qux'))->throws(\ReflectionException::class);
});

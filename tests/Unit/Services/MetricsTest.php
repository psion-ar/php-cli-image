<?php

declare(strict_types=1);

use App\Services\Metrics;

describe('MetricsTest', function () {
    it('benchmarks a certain action', function () {
        // arrange
        $regex = '/\[Duration:\] \d\.\d{2} s \/ \d+ (image|images)\n\[Average: \] \d+\.\d{2} s \/ image/';

        // act
        $metrics = new Metrics(fn () => 'foo', 2);

        // assert
        expect($metrics->get())->toMatch($regex);
    });

    it('fails when number of items is less than 1')
        ->expect(fn () => new Metrics(fn () => 'foo', 0))
        ->throws(InvalidArgumentException::class);
});

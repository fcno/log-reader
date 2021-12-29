<?php

use Fcno\LogReader\Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

uses(TestCase::class)
    ->beforeEach(function() {
        $this->fs_name = 'log-aplicacao';

        $this->file_system = Storage::fake($this->fs_name, [
            'driver' => 'local',
        ]);

        $this->file_name = Str::of('laravel-')
                                ->append(now()->format('Y-m-d'))
                                ->finish('.log')
                                ->__toString();
    })
    ->in(__DIR__);

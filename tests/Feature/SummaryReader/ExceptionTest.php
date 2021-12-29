<?php

/**
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 *
 * @see https://pestphp.com/docs/
 */

use Fcno\LogReader\Exceptions\FileNotFoundException;
use Fcno\LogReader\Exceptions\FileSystemNotDefinedException;
use Fcno\LogReader\Exceptions\NotDailyLogException;
use Fcno\LogReader\Facades\SummaryReader;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->fs_name = 'log-aplicacao';

    $this->file_system = Storage::fake($this->fs_name, [
        'driver' => 'local',
    ]);

    $this->file_name = Str::of('laravel-')
                            ->append(now()->format('Y-m-d'))
                            ->finish('.log')
                            ->__toString();
});

test('lança exceção ao acionar métodos get e infoAbout sem previamente definir o File System', function () {
    expect(
        fn () => SummaryReader::get($this->file_name)
    )->toThrow(FileSystemNotDefinedException::class);

    expect(
        fn () => SummaryReader::infoAbout($this->file_name)
    )->toThrow(FileSystemNotDefinedException::class);
});

test('lança exceção ao acionar método infoAbout informando arquivo de log com nome fora do padrão Laravel para logs diários', function () {
    // padrão correto é laravel-yyyy-mm-dd.log
    $file_name = 'laravel.log';

    expect(
        fn () => SummaryReader::from($this->fs_name)
                                ->infoAbout($file_name)
    )->toThrow(NotDailyLogException::class);
});

test('lança exceção ao acionar método infoAbout informando arquivo de log inexistente', function () {
    $file_name = 'laravel-1500-01-30.log';

    expect(
        fn () => SummaryReader::from($this->fs_name)
                            ->infoAbout($file_name)
    )->toThrow(FileNotFoundException::class);
});

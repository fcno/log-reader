<?php

/**
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 *
 * @link https://pestphp.com/docs/
 */

use Fcno\LogReader\Exceptions\FileNotFoundException;
use Fcno\LogReader\Facades\LogReader;
use Fcno\LogReader\LogReader as Reader;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->fs_name = 'log-aplicacao';

    $this->file_system = Storage::fake($this->fs_name, [
        'driver' => 'local'
    ]);
});

test('o facade retorna o objeto da classe corretamente', function () {
    expect(LogReader::from($this->fs_name))->toBeInstanceOf(Reader::class);
});

test('lança exceção ao tentar ler sumário de arquivo inexistente', function () {
    expect(
        fn() => LogReader::from($this->fs_name)
                            ->getDailySummary('laravel-2500-12-30.log')
    )->toThrow(FileNotFoundException::class);
});

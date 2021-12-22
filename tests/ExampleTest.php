<?php

/**
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 *
 * @link https://pestphp.com/docs/
 */

use Fcno\LogReader\Facades\LogReader as LogReaderFacade;
use Fcno\LogReader\LogReader;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->fs_name = 'log-aplicacao';

    $this->file_system = Storage::fake($this->fs_name, [
        'driver' => 'local'
    ]);
});

test('o facade retorna o objeto da classe corretamente', function () {
    expect(LogReaderFacade::from($this->fs_name))->toBeInstanceOf(LogReader::class);
});

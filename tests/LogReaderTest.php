<?php

/**
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 *
 * @link https://pestphp.com/docs/
 */

use Fcno\LogReader\Exceptions\FileNotFoundException;
use Fcno\LogReader\Facades\LogReader;
use Fcno\LogReader\LogReader as Reader;
use Fcno\LogReader\Tests\Stubs\LogGenerator;
use illuminate\support\Str;
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

test('sumariza corretamente a quantidade de logs de um determinado tipo e a sua data', function () {
    $level           = 'ALERT';
    $amount          = 5;
    $appended_level  = 'DEBUG';
    $appended_amount = 10;

    $today = now()
                ->subDay()
                ->format('Y-m-d');

    $file_name = Str::of('laravel-')
                    ->append($today)
                    ->finish('.log');

    LogGenerator::on($this->fs_name)
                ->create(['level' => $level])
                ->count(files: 1, records: $amount)
                ->appendLevel(
                    log_file: $file_name,
                    records: $appended_amount,
                    level: $appended_level
                );

    $summary = LogReader::from($this->fs_name)->getDailySummary($file_name);

    expect($summary)
    ->get('date')->toBe($today)
    ->get($level)->toBe($amount)
    ->get($appended_level)->toBe($appended_amount)
    ->get('EMERGENCY')->toBeNull();
});

test('obtém todas as informações sobre os registros de um determinado arquivo de log', function () {
    $level           = 'alert';
    $amount          = 5;

    $today = now()
                ->subDay()
                ->format('Y-m-d');

    $file_name = Str::of('laravel-')
                    ->append($today)
                    ->finish('.log');

    LogGenerator::on($this->fs_name)
                ->create(['level' => $level])
                ->count(files: 1, records: $amount);

    $response = LogReader::from($this->fs_name)->fullInfoAbout($file_name);

    expect($response->first())
    ->toHaveKeys(['date', 'time', 'env', 'level', 'message', 'context', 'extra'])
    ->get('date')->toBe($today);
});

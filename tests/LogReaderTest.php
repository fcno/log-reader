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
use Illuminate\Support\Facades\Storage;
use illuminate\support\Str;

beforeEach(function () {
    $this->fs_name = 'log-aplicacao';

    $this->file_system = Storage::fake($this->fs_name, [
        'driver' => 'local',
    ]);

    $this->file_name = Str::of('laravel-')
                            ->append(now()->format('Y-m-d'))
                            ->finish('.log');
});

test('o facade retorna o objeto da classe corretamente', function () {
    expect(LogReader::from($this->fs_name))->toBeInstanceOf(Reader::class);
});

test('lança exceção ao tentar ler sumário de arquivo inexistente', function () {
    expect(
        fn () => LogReader::from($this->fs_name)
                            ->getDailySummary('laravel-2500-12-30.log')
    )->toThrow(FileNotFoundException::class);
});

test('sumariza corretamente a quantidade de logs de um determinado tipo e a sua data', function () {
    $level           = 'alert';
    $amount          = 5;
    $appended_level  = 'debug';
    $appended_amount = 10;

    LogGenerator::on($this->fs_name)
                ->create(['level' => $level])
                ->count(files: 1, records: $amount)
                ->appendLevel(
                    log_file: $this->file_name,
                    records: $appended_amount,
                    level: $appended_level
                );

    $response = LogReader::from($this->fs_name)
                            ->getDailySummary($this->file_name);

    expect($response)
    ->get('date')->toBe(now()->format('Y-m-d'))
    ->get($level)->toBe($amount)
    ->get($appended_level)->toBe($appended_amount)
    ->get('emergency')->toBeNull();
});

test('obtém todas as informações sobre os registros de um determinado arquivo de log', function () {
    LogGenerator::on($this->fs_name)
                ->create(null)
                ->count(files: 1, records: 1);

    $response = LogReader::from($this->fs_name)
                            ->fullInfoAbout($this->file_name)
                            ->get();

    expect($response->first())
    ->toHaveKeys(['date', 'time', 'env', 'level', 'message', 'context', 'extra'])
    ->get('date')->toBe(now()->format('Y-m-d'));
});

test('lança exceção ao tentar paginar com página ou por página menor que 1', function () {

    LogGenerator::on($this->fs_name)
                ->create(null)
                ->count(files: 1, records: 1);

    expect(
        fn () => LogReader::from($this->fs_name)
                            ->fullInfoAbout($this->file_name)
                            ->paginate(page:-1, per_page: 1)
    )->toThrow(RuntimeException::class);

    expect(
        fn () => LogReader::from($this->fs_name)
                            ->fullInfoAbout($this->file_name)
                            ->paginate(page: 1, per_page: -1)
    )->toThrow(RuntimeException::class);
});

test('retorna o conteúdo do arquivo de log de acordo com a paginação informada', function () {
    $amount          = 10;
    $per_page        = 3;
    $page            = 3;
    $expected_amount = 3;

    LogGenerator::on($this->fs_name)
                ->create(null)
                ->count(files: 1, records: $amount);

    $response = LogReader::from($this->fs_name)
                            ->fullInfoAbout($this->file_name)
                            ->paginate(page: $page, per_page: $per_page);

    expect($response)->toHaveCount($expected_amount);
});

test('lança exceção ao tentar paginar os arquivos com página ou por página menor que 1', function () {
    expect(
        fn () => LogReader::from($this->fs_name)
                            ->fullSummary(page:-1, per_page: 1)
    )->toThrow(\RuntimeException::class);

    expect(
        fn () => LogReader::from($this->fs_name)
                            ->fullSummary(page: 1, per_page: -1)
    )->toThrow(\RuntimeException::class);
});

test('retorna a quantidade esperada de acordo com a paginação solicitada', function ($page, $expect) {
    LogGenerator::on($this->fs_name)
                ->create(null)
                ->count(files: 17, records: 1);

    $response = LogReader::from($this->fs_name)
                            ->fullSummary(page: $page, per_page: 5);

    expect($response)->toHaveCount($expect);
})->with([
    [3, 5], // página 3 retorna 5 arquivos. Página completa
    [4, 2], // página 4 retorna 2 arquivos. Página incompleta, chegou-se ao fim
    [5, 0]  // página 5 retorna 0 arquivos. Paginação já chegou ao fim
]);

<?php

/**
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 *
 * @link https://pestphp.com/docs/
 */

use Fcno\LogReader\Exceptions\FileNotFoundException;
use Fcno\LogReader\Exceptions\NotDailyLogException;
use Fcno\LogReader\Facades\RecordReader;
use Fcno\LogReader\RecordReader as Reader;
use Fcno\LogReader\Tests\Stubs\LogGenerator;
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
    ;
});

test('o facade retorna o objeto da classe corretamente', function () {
    expect(RecordReader::from($this->fs_name))->toBeInstanceOf(Reader::class);
});

test('lança exceção ao tentar ler registro de arquivo de log inexistente', function () {
    expect(
        fn () => RecordReader::from($this->fs_name)
                                ->infoAbout('laravel-2500-12-30.log')
    )->toThrow(FileNotFoundException::class);
});

test('lança exceção ao tentar ler registro de arquivo de log com nome fora do padrão laravel diário', function () {
    $new_name = 'laravel.log';

    LogGenerator::on($this->fs_name)
                ->create(null)
                ->count(files: 1, records: 1);

    $this->file_system->move(
        from: $this->file_name,
        to: $new_name
    );

    expect(
        fn () => RecordReader::from($this->fs_name)
                                ->infoAbout($new_name)
    )->toThrow(NotDailyLogException::class);
});

test('obtém informações completas acerca dos registros de um determinado arquivo de log', function () {
    LogGenerator::on($this->fs_name)
                ->create(null)
                ->count(files: 1, records: 1);

    $response = RecordReader::from($this->fs_name)
                            ->infoAbout($this->file_name)
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
        fn () => RecordReader::from($this->fs_name)
                                ->infoAbout($this->file_name)
                                ->paginate(page:-1, per_page: 1)
    )->toThrow(RuntimeException::class);

    expect(
        fn () => RecordReader::from($this->fs_name)
                                ->infoAbout($this->file_name)
                                ->paginate(page: 1, per_page: -1)
    )->toThrow(RuntimeException::class);
});

test('obtém a quantidade de registros do arquivo de log de acordo com a paginação solicitada', function ($page, $expect) {
    LogGenerator::on($this->fs_name)
                ->create(null)
                ->count(files: 1, records: 14);

    $response = RecordReader::from($this->fs_name)
                            ->infoAbout($this->file_name)
                            ->paginate(page: $page, per_page: 5);

    expect($response)->toHaveCount($expect);
})->with([
    [2, 5], // página 2 retorna 5 registros. Página completa
    [3, 4], // página 3 retorna 4 registros. Página incompleta, chegou-se ao fim
    [4, 0],  // página 3 retorna 0 registros. Paginação já chegou ao fim
]);

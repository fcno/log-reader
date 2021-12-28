<?php

/**
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 *
 * @link https://pestphp.com/docs/
 */

use Fcno\LogReader\Exceptions\InvalidPaginationException;
use Fcno\LogReader\Facades\LogReader;
use Fcno\LogReader\LogReader as Reader;
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
});

test('o facade retorna o objeto da classe corretamente', function () {
    expect(LogReader::from($this->fs_name))->toBeInstanceOf(Reader::class);
});

test('obtém todos os arquivos de log do file system ordenados do mais recente para o mais antigo', function () {
    $amount = 10;
    $last_log_file = Str::of('laravel-')
                        ->append(
                            now()
                            ->subDays($amount - 1)
                            ->format('Y-m-d')
                        )
                        ->finish('.log')
                        ->__toString();

    LogGenerator::on($this->fs_name)
                ->create(null)
                ->count(files: $amount, records: 1);

    $response = LogReader::from($this->fs_name)->get();

    expect($response)
    ->toHaveCount($amount)
    ->first()->toBe($this->file_name)
    ->last()->toBe($last_log_file);
});

test('lança exceção ao tentar paginar com página ou por página menor que 1', function () {
    expect(
        fn () => LogReader::from($this->fs_name)
                                ->paginate(page:-1, per_page: 1)
    )->toThrow(InvalidPaginationException::class);

    expect(
        fn () => LogReader::from($this->fs_name)
                                ->paginate(page: 1, per_page: -1)
    )->toThrow(InvalidPaginationException::class);
});

test('obtém a quantidade de arquivos esperada de acordo com a paginação solicitada ordenados do mais recente para o mais antigo', function ($page, $expect) {
    LogGenerator::on($this->fs_name)
                ->create(null)
                ->count(files: 14, records: 1);

    $response = LogReader::from($this->fs_name)
                            ->paginate(page: $page, per_page: 5);

    expect($response)->toHaveCount($expect);
})->with([
    [2, 5], // página 2 retorna 5 arquivos. Página completa
    [3, 4], // página 3 retorna 4 arquivos. Página incompleta, chegou-se ao fim
    [4, 0],  // página 4 retorna 0 arquivos. Paginação já chegou ao fim
]);

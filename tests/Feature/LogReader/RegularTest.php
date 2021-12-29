<?php

/**
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 *
 * @see https://pestphp.com/docs/
 */

use Fcno\LogReader\Facades\LogReader;
use Fcno\LogReader\LogReader as Reader;
use Fcno\LogReader\Tests\Stubs\LogGenerator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

test('o Facade retorna o objeto da classe', function () {
    expect(LogReader::from($this->fs_name))->toBeInstanceOf(Reader::class);
});

test('obtém todos os arquivos de log do File System ordenados do mais recente para o mais antigo', function () {
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

test('obtém a quantidade de arquivos de log de acordo com a paginação solicitada ordenados do mais recente para o mais antigo', function ($page, $expect) {
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

test('deleta um arquivo de log', function () {
    LogGenerator::on($this->fs_name)
                ->create(null)
                ->count(files: 1, records: 1);

    $this->file_system->assertExists($this->file_name);
    expect(
        LogReader::from($this->fs_name)
                    ->delete($this->file_name)
    )->toBeTrue();
    $this->file_system->assertMissing($this->file_name);
});

test('faz o download de um arquivo de log', function () {
    LogGenerator::on($this->fs_name)
                ->create(null)
                ->count(files: 1, records: 1);

    $response = LogReader::from($this->fs_name)
                            ->download($this->file_name);

    expect($response->headers->get('content-disposition'))
    ->toBe("attachment; filename={$this->file_name}");
});

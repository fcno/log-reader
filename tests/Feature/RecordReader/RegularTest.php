<?php

/**
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 *
 * @see https://pestphp.com/docs/
 */

use Fcno\LogReader\Facades\RecordReader;
use Fcno\LogReader\RecordReader as Reader;
use Fcno\LogReader\Tests\Stubs\LogGenerator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

test('o Facade retorna o objeto da classe corretamente', function () {
    expect(RecordReader::from($this->fs_name))->toBeInstanceOf(Reader::class);
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

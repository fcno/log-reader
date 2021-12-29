<?php

/**
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 *
 * @see https://pestphp.com/docs/
 */

use Fcno\LogReader\Facades\SummaryReader;
use Fcno\LogReader\SummaryReader as Reader;
use Fcno\LogReader\Tests\Stubs\LogGenerator;

test('o Facade retorna o objeto da classe', function () {
    expect(SummaryReader::from($this->fs_name))->toBeInstanceOf(Reader::class);
});

test('sumariza a quantidade de registros do arquivo de log por nível e informa a sua data', function () {
    $level = 'alert';
    $amount = 5;
    $appended_level = 'debug';
    $appended_amount = 10;

    LogGenerator::on($this->fs_name)
                ->create(['level' => $level])
                ->count(files: 1, records: $amount)
                ->appendLevel(
                    log_file: $this->file_name,
                    records: $appended_amount,
                    level: $appended_level
                );

    $response = SummaryReader::from($this->fs_name)
                                ->infoAbout($this->file_name)
                                ->get();

    expect($response)
    ->get('date')->toBe(now()->format('Y-m-d'))
    ->get($level)->toBe($amount)
    ->get($appended_level)->toBe($appended_amount)
    ->get('emergency')->toBeNull();
});

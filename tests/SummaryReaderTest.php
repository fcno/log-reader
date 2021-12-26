<?php

/**
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 *
 * @link https://pestphp.com/docs/
 */

use Fcno\LogReader\Exceptions\{FileNotFoundException, NotDailyLogException};
use Fcno\LogReader\Facades\SummaryReader;
use Fcno\LogReader\SummaryReader as Reader;
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
                            ->finish('.log');
});

test('o facade retorna o objeto da classe corretamente', function () {
    expect(SummaryReader::from($this->fs_name))->toBeInstanceOf(Reader::class);
});

test('lança exceção ao tentar ler sumário de arquivo de log inexistente', function () {
    expect(
        fn () => SummaryReader::from($this->fs_name)
                                ->infoAbout('laravel-2500-12-30.log')
    )->toThrow(FileNotFoundException::class);
});


test('lança exceção ao tentar ler sumário de arquivo de log com nome fora do padrão laravel diário', function () {
    $new_name = 'laravel.log';

    LogGenerator::on($this->fs_name)
                ->create(null)
                ->count(files: 1, records: 1);

    $this->file_system->move(
        from: $this->file_name,
        to: $new_name
    );

    expect(
        fn () => SummaryReader::from($this->fs_name)
                                ->infoAbout($new_name)
    )->toThrow(NotDailyLogException::class);
});

test('sumariza corretamente a quantidade de registros de um determinado tipo e a sua data', function () {
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

    $response = SummaryReader::from($this->fs_name)
                                ->infoAbout($this->file_name)
                                ->get();

    expect($response)
    ->get('date')->toBe(now()->format('Y-m-d'))
    ->get($level)->toBe($amount)
    ->get($appended_level)->toBe($appended_amount)
    ->get('emergency')->toBeNull();
});

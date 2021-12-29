<?php

/**
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 *
 * @see https://pestphp.com/docs/
 */

use Fcno\LogReader\Exceptions\FileNotFoundException;
use Fcno\LogReader\Exceptions\InvalidPaginationException;
use Fcno\LogReader\Exceptions\NotDailyLogException;
use Fcno\LogReader\Facades\RecordReader;
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

test('lança exceção ao tentar ler registro de arquivo de log inexistente', function () {
    expect(
        fn () => RecordReader::from($this->fs_name)
                                ->infoAbout('laravel-2500-12-30.log')
    )->toThrow(FileNotFoundException::class);
});

test('lança exceção ao tentar ler registro de arquivo de log com nome fora do padrão Laravel para logs diários', function () {
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

test('lança exceção ao tentar paginar com número da página ou com a quantidade de itens por página menor que 1', function () {
    LogGenerator::on($this->fs_name)
                ->create(null)
                ->count(files: 1, records: 1);

    expect(
        fn () => RecordReader::from($this->fs_name)
                                ->infoAbout($this->file_name)
                                ->paginate(page: -1, per_page: 1)
    )->toThrow(InvalidPaginationException::class);

    expect(
        fn () => RecordReader::from($this->fs_name)
                                ->infoAbout($this->file_name)
                                ->paginate(page: 1, per_page: -1)
    )->toThrow(InvalidPaginationException::class);
});

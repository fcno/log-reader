<?php

/**
 * @see https://pestphp.com/docs/
 */

use Fcno\LogReader\Exceptions\FileNotFoundException;
use Fcno\LogReader\Exceptions\FileSystemNotDefinedException;
use Fcno\LogReader\Exceptions\InvalidPaginationException;
use Fcno\LogReader\Exceptions\NotDailyLogException;
use Fcno\LogReader\Facades\LogReader;

test('lança exceção ao acionar métodos delete, download, get e paginate sem previamente definir o File System', function () {
    expect(
        fn () => LogReader::delete($this->file_name)
    )->toThrow(FileSystemNotDefinedException::class);

    expect(
        fn () => LogReader::download($this->file_name)
    )->toThrow(FileSystemNotDefinedException::class);

    expect(
        fn () => LogReader::get()
    )->toThrow(FileSystemNotDefinedException::class);

    expect(
        fn () => LogReader::paginate(page: 2, per_page: 5)
    )->toThrow(FileSystemNotDefinedException::class);
});

test('lança exceção ao acionar método paginate com número da página ou com a quantidade de itens por página menor que 1', function () {
    expect(
        fn () => LogReader::from($this->fs_name)
                            ->paginate(page: -1, per_page: 1)
    )->toThrow(InvalidPaginationException::class);

    expect(
        fn () => LogReader::from($this->fs_name)
                            ->paginate(page: 1, per_page: -1)
    )->toThrow(InvalidPaginationException::class);
});

test('lança exceção ao acionar métodos delete ou download informando arquivo de log com nome fora do padrão Laravel para logs diários', function () {
    // padrão correto é laravel-yyyy-mm-dd.log
    $file_name = 'laravel.log';

    expect(
        fn () => LogReader::from($this->fs_name)
                            ->delete($file_name)
    )->toThrow(NotDailyLogException::class);

    expect(
        fn () => LogReader::from($this->fs_name)
                            ->download($file_name)
    )->toThrow(NotDailyLogException::class);
});

test('lança exceção ao acionar métodos delete ou download informando arquivo de log inexistente', function () {
    $file_name = 'laravel-1500-01-30.log';

    expect(
        fn () => LogReader::from($this->fs_name)
                            ->delete($file_name)
    )->toThrow(FileNotFoundException::class);

    expect(
        fn () => LogReader::from($this->fs_name)
                            ->download($file_name)
    )->toThrow(FileNotFoundException::class);
});

<?php

namespace Fcno\LogReader;

use Fcno\LogReader\Contracts\BaseReader;
use Fcno\LogReader\Contracts\IDelete;
use Fcno\LogReader\Contracts\IDownload;
use Fcno\LogReader\Contracts\IPaginate;
use Fcno\LogReader\Exceptions\FileNotFoundException;
use Fcno\LogReader\Exceptions\FileSystemNotDefinedException;
use Fcno\LogReader\Exceptions\InvalidPaginationException;
use Fcno\LogReader\Exceptions\NotDailyLogException;
use Illuminate\Support\Collection;

/**
 * Manipular os arquivos de log do ***File System*** gerados no padrão
 * **Laravel** diário, isto é, no padrão `laravel-2020-09-20.log`.
 *
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 */
final class LogReader extends BaseReader implements IPaginate, IDelete, IDownload
{
    /**
     * {@inheritdoc}
     *
     * Nesse caso, a lista dos arquivos de log diários.
     */
    public function get(): Collection
    {
        throw_if(! $this->file_system, FileSystemNotDefinedException::class);

        $collection = collect($this->file_system->files());

        $filtered = $collection->filter(function ($value, $key) {
            return preg_match(Regex::LOG_FILE, $value);
        });

        return $filtered
                ->sortDesc()
                ->values();
    }

    /**
     * {@inheritdoc}
     *
     * Nesse caso, a lista de arquivos de log diários.
     */
    public function paginate(int $page, int $per_page): Collection
    {
        throw_if(! $this->file_system,       FileSystemNotDefinedException::class);
        throw_if($page < 1 || $per_page < 1, InvalidPaginationException::class);

        return $this->get()
                    ->slice(
                        offset: ($page - 1) * $per_page,
                        length: $per_page
                    );
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $log_file): bool
    {
        throw_if(! $this->file_system,                     FileSystemNotDefinedException::class);
        throw_if(! preg_match(Regex::LOG_FILE, $log_file), NotDailyLogException::class);
        throw_if($this->file_system->missing($log_file),   FileNotFoundException::class);

        return $this->file_system->delete($log_file);
    }

    /**
     * {@inheritdoc}
     */
    public function download(string $log_file): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        throw_if(! $this->file_system,                     FileSystemNotDefinedException::class);
        throw_if(! preg_match(Regex::LOG_FILE, $log_file), NotDailyLogException::class);
        throw_if($this->file_system->missing($log_file),   FileNotFoundException::class);

        return $this->file_system->download(
            $log_file,
            $log_file,
            $this->setDownloadHeaders($log_file)
        );
    }

    /**
     * Define o cabeçalho para o ***download*** do arquivo.
     *
     * @param string $log_file Ex.: laravel-2000-12-30.log
     *
     * @return array ***headers*** do download
     */
    private function setDownloadHeaders(string $log_file): array
    {
        return [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'attachment; filename=' . $log_file,
        ];
    }
}

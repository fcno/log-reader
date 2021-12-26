<?php

namespace Fcno\LogReader;

use Fcno\LogReader\Contracts\BaseReader;
use Fcno\LogReader\Contracts\IPaginate;
use Fcno\LogReader\Contracts\IReader;
use Illuminate\Support\Collection;

/**
 * Manipular os arquivos de log do file system gerados no padrão laravel diário
 * isto é, no padrão `laravel-2020-09-20.log`.
 *
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 */
final class LogReader extends BaseReader implements IReader, IPaginate
{
    /**
     * @inheritdoc
     *
     * Nesse caso, a lista dos arquivos de log diários.
     */
    public function get(): Collection
    {
        $collection = collect($this->file_system->files());

        $filtered = $collection->filter(function ($value, $key) {
            return preg_match(Regex::LOG_FILE, $value);
        });

        return $filtered
                ->sortDesc()
                ->values();
    }

    /**
     * @inheritdoc
     *
     * Nesse caso, a lista de arquivos de log diários.
     */
    public function paginate(int $page, int $per_page): Collection
    {
        throw_if($page < 1 || $per_page < 1);

        return $this->get()
                    ->slice(
                        offset: ($page - 1) * $per_page,
                        length: $per_page
                    );
    }
}

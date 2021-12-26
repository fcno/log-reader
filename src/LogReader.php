<?php

namespace Fcno\LogReader;

use Fcno\LogReader\Contracts\{BaseReader, IPaginate, IReader};
use Illuminate\Support\Collection;

/**
 * Manipular os arquivos de log do file system.
 *
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 */
final class LogReader extends BaseReader implements IReader, IPaginate
{
    /**
     * @inheritdoc
     *
     * Nesse caso, os arquivos de log
     */
    public function get(): Collection
    {
        return collect($this->file_system->files())
                ->sortDesc()
                ->values();
    }

    /**
     * @inheritdoc
     *
     * Nesse caso, a lista de arquivos de log.
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

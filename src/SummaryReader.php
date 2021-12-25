<?php

namespace Fcno\LogReader;

use Bcremer\LineReader\LineReader;
use Fcno\LogReader\Contracts\BaseContentReader;
use Illuminate\Support\Collection;

/**
 * Manipular um arquivo de log sumarizando seu conteúdo.
 *
 * O sumário é feito por meio da contabilização da quantidade de níveis de logs
 * no arquivo, ou seja, a quantidade de registros do tipo debug, info, etc, bem
 * como a data desses resgistros.
 *
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 */
final class SummaryReader extends BaseContentReader
{
    /**
     * @inheritdoc
     *
     * Sumariza:
     * - Data do log (Y-m-d)
     * - Quantidade de registros por level
     */
    public function get(): Collection
    {
        return $this->readyToGoSummary(
            $this->readLog()
        );
    }

    /**
     * @inheritdoc
     *
     * Nesse caso, um Generator devido a desnecessidade de paginação.
     *
     * @return \Generator
     */
    protected function getLineGenerator(): \Generator
    {
        return LineReader::readLines($this->getFullPath());
    }

    /**
     * @inheritdoc
     *
     * Interesse em:
     * - date
     * - level
     */
    protected function filteredData(array $data): Collection
    {
        return collect($data)
                ->only(['date', 'level']);
    }

    /**
     * Prepara o sumário para ser retornado ao chamador.
     *
     * Contabiliza a quantidade de logs por nível e adiciona o data dos
     * registros.
     *
     * @param \Illuminate\Support\Collection  $summary_in_process
     *
     * @return \Illuminate\Support\Collection
     */
    private function readyToGoSummary(Collection $summary_in_process): Collection
    {
        $summary = $summary_in_process->countBy('level');

        $summary->put(
            'date',
            $summary_in_process->first()->get('date')
        );

        return $summary;
    }
}

<?php

namespace Fcno\LogReader\Contracts;

use Illuminate\Support\Collection;

/**
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 */
interface IReader
{
    /**
     * Define o file system de armazenamento dos logs diários da aplicação de
     * acordo com o nome informado.
     *
     * Trata-se do file system em que esta library buscará pelos arquivos.
     *
     * @param string $disk nome do file system
     */
    public function from(string $disk): static;

    /**
     * Obtém as informações solicitadas.
     */
    public function get(): Collection;
}

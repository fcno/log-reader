<?php

namespace Fcno\LogReader\Contracts;

use Illuminate\Support\Collection;

/**
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 */
interface IReader
{
    /**
     * Define o ***File System*** de armazenamento dos logs diários da
     * aplicação de acordo com o nome informado.
     *
     * Trata-se do ***file system*** em que este ***package*** buscará pelos
     * arquivos de log.
     *
     * @param string $disk nome do ***File System***
     */
    public function from(string $disk): static;

    /**
     * Obtém as informações solicitadas.
     *
     * @throws \Fcno\LogReader\Exceptions\FileSystemNotDefinedException
     */
    public function get(): Collection;
}

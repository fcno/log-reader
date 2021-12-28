<?php

namespace Fcno\LogReader;

/**
 * Regex usadas na library.
 *
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 *
 * @see https://www.youtube.com/watch?v=a178TJOZPIo&list=PLpzy7FIRqpGBQ_aqz_hXDBch1aAA-lmgu&index=6
 * @see https://www.phpliveregex.com/#tab-preg-match
 * @see https://stackoverflow.com/questions/34578333/correct-way-to-add-comments-to-a-regular-expression-in-php?answertab=votes#tab-top
 * @see https://chromatichq.com/insights/selfdocumenting-regular-expressions
 */
class Regex
{
    /**
     * @var string
     *
     * Padrão da regex para extração dos dados de um registro de log diário
     */
    public const RECORD = '/
    ^\#@\#                                  # Caracteres que definem o inicio do padrão
    (?<date>[0-9]{4}-[0-9]{2}-[0-9]{2})     # Captura a data e insere no índice date
    \s                                      # Espaço em branco
    (?<time>[0-9]{2}:[0-9]{2}:[0-9]{2})     # Captura a hora e insere no índice time
    \|{3}                                   # Sequência de delimitadores
    (?<env>.*?)                             # Captura o tipo de ambiente e insere no índice env
    \|{3}                                   # Sequência de delimitadores
    (?<level>.*?)                           # Captura o nível do log e insere no índice level
    \|{3}                                   # Sequência de delimitadores
    (?<message>.*?)                         # Captura a mensagem e insere no índice message
    \|{3}                                   # Sequência de delimitadores
    (?<context>.*?)                         # Captura o contexto e insere no índice context
    \|{3}                                   # Sequência de delimitadores
    (?<extra>.*?)                           # Captura os dados extras e insere no índice extra
    @\#@                                    # Caracteres que definem o fim do padrão
    /x';

    /**
     * @var string
     *
     * Padrão para o nome do arquivo de log diário.
     *
     * Ex.: laravel-2020-04-30.log
     */
    public const LOG_FILE = '/
    ^laravel-                           # Caracteres que definem o inicio do padrão
    (?<date>[0-9]{4}-[0-9]{2}-[0-9]{2}) # Captura a data e insere no índice date
    .log                                # Caracteres que definem o fim do padrão
    /x';
}

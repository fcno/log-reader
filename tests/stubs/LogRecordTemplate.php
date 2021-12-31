<?php

namespace Fcno\LogReader\Tests\Stubs;

use Faker\Factory;
use Illuminate\Support\Carbon;

/**
 * Template para geração de registros de log.
 *
 * Registros (records) são os dados de interesse de um determinado evento
 * gerador.
 * Cada registro possui as seguintes informações:
 * - date    - data do evento
 * - time    - hora do evento
 * - env     - ambiente em que o evento ocorreu
 * - level   - nível do evento nos termos da PSR-3
 * - message - mensagem
 * - context - mensagem de contexto
 * - extra   - dados extras sobre o evento
 *
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 */
final class LogRecordTemplate
{
    /**
     * @var string
     *
     * Template de criação de um registro de log
     */
    private $log_record =
        '#@#'.
        ':date :time|||'.
        ':env|||'.
        ':level|||'.
        ':message|||'.
        ':context|||'.
        ':extra'.
        '@#@';

    /**
     * @var \Faker\Generator
     *
     * Faker utilizado para gerar dados aleatórios
     */
    private $faker;

    /**
     * Cria uma instância do objeto.
     */
    public static function create(): static
    {
        return new static();
    }

    /**
     * Cria uma instância do objeto.
     */
    public function __construct()
    {
        $this->faker = Factory::create();
    }

    /**
     * String com um registro pronto para armazenamento.
     *
     * A sua geração é de acordo com as regras informadas para os campos.
     *
     * Ex.: Se em `$fields` for informado `['env' => 'local']` o registro gerado
     * conterá esse valor. Já os campos não informados serão gerados com
     * valores randômicos.
     */
    public function getRecord(?array $fields): string
    {
        return $this->generate($fields);
    }

    /**
     * Gera um registro observando as definições para os campos informados.
     */
    private function generate(?array $fields): string
    {
        $new_def = collect($fields);

        $this
            ->setDate($new_def->get('date'))
            ->setTime($new_def->get('time'))
            ->setEnv($new_def->get('env'))
            ->setLevel($new_def->get('level'))
            ->setMessage($new_def->get('message'))
            ->setContext($new_def->get('context'))
            ->setExtra($new_def->get('extra'));

        return $this->log_record;
    }

    /**
     * Define a data do registro.
     *
     * @param string|null $date yyyy-mm-dd
     */
    private function setDate(?string $date): static
    {
        $this->replace(
            ':date',
            $date ?: now()->format('Y-m-d')
        );

        return $this;
    }

    /**
     * Define o horário do registro.
     *
     * @param string|null $time hh-mm-ss
     */
    private function setTime(?string $time): static
    {
        $this->replace(
            ':time',
            $time ?: Carbon::createFromTime(
                random_int(0, 23),
                random_int(0, 59),
                random_int(0, 59)
            )->format('H:i:s')
        );

        return $this;
    }

    /**
     * Define o ambiente.
     */
    private function setEnv(?string $env): static
    {
        $this->replace(
            ':env',
            $env ?: $this->faker->randomElement(['local', 'staging', 'production'])
        );

        return $this;
    }

    /**
     * Define o log nos termos da PSR-3.
     *
     * @see https://www.php-fig.org/psr/psr-3/
     */
    private function setLevel(?string $level): static
    {
        $this->replace(
            ':level',
            $level ?: $this->faker->randomElement([
                'emergency',
                'alert',
                'critical',
                'error',
                'warning',
                'notice',
                'info',
                'debug',
            ])
        );

        return $this;
    }

    /**
     * Define a mensagem.
     */
    private function setMessage(?string $message): static
    {
        $this->replace(
            ':message',
            $message ?: $this->faker->sentence()
        );

        return $this;
    }

    /**
     * Define a mensagem de contexto.
     */
    private function setContext(?string $context): static
    {
        $this->replace(
            ':context',
            $context ?: $this->faker->paragraph()
        );

        return $this;
    }

    /**
     * Define as informações extras.
     */
    private function setExtra(?string $extra): static
    {
        $this->replace(
            ':extra',
            $extra ?: (rand(0, 1)
                        ? $this->faker->word()
                        : null)
        );

        return $this;
    }

    /**
     * Subtitui um determinado trecho da string de acordo com o padrão.
     *
     * Note que a string que será alvo da subtisuição é uma propriedade desta
     * classe, portanto, ela não é enviada por parâmetro.
     *
     * @param string $replacement
     */
    private function replace(string $pattern, ?string $replacement): void
    {
        $this->log_record = mb_eregi_replace($pattern, $replacement, $this->log_record);
    }
}

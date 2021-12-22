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
     * @var string  $log_record
     *
     * Template de criação de um registro de log.
     */
    private $log_record =
        "#@#"            .
        ":date :time|||" .
        ":env|||"        .
        ":level|||"      .
        ":message|||"    .
        ":context|||"    .
        ":extra"         .
        "@#@";

    /**
     * @var \Faker\Generator  $faker
     *
     * Faker utilizado para gerar dados aleatórios
     */
    private $faker;

    /**
     * Cria uma instância do objeto.
     *
     * @return static
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
     * String com os registros prontos para armazenamento
     *
     * A sua geração é de acordo com a quantidade, bem como com os campos
     * informados.
     *
     * Ex.: Se em `$fields` for informado ['env' => 'local'] todos os registros
     * conterão esse valor. Já os campos não informados serão gerados com
     * valores randômicos.
     *
     * @param array|null  $amount  quantidade de registros
     * @param array|null  $fields
     *
     * @return string
     */
    public function getRecords(int $amount = 1, array $fields = null): string
    {
        $records = collect();

        for ($i = 0; $i < $amount; $i++) {
            $records->push($this->generate($fields));
        }
        return $records->join(PHP_EOL);
    }

    /**
     * Gera um registro observando as definições para os campos informados.
     *
     * @param array|null  $fields
     *
     * @return string
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
     * @param string|null  $date yyyy-mm-dd
     *
     * @return static
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
     * @param string|null  $time hh-mm-ss
     *
     * @return static
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
     *
     * @param string|null  $env
     *
     * @return static
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
     * @param string|null  $level
     *
     * @return static
     *
     * @link https://www.php-fig.org/psr/psr-3/
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
                'debug'
            ])
        );

        return $this;
    }

    /**
     * Define a mensagem.
     *
     * @param string|null  $message
     *
     * @return static
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
     *
     * @param string|null  $context
     *
     * @return static
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
     *
     * @param string|null  $extra
     *
     * @return static
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
     * @param string  $patten
     * @param string  $replacement
     */
    private function replace(string $pattern, ?string $replacement): void
    {
        $this->log_record = mb_eregi_replace($pattern, $replacement, $this->log_record);
    }

}

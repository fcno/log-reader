# Log Reader para aplicações Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/fcno/log-reader.svg?style=flat-square)](https://packagist.org/packages/fcno/log-reader)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/fcno/log-reader/run-tests?label=tests)](https://github.com/fcno/log-reader/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/fcno/log-reader/Check%20&%20fix%20styling?label=code%20style)](https://github.com/fcno/log-reader/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/fcno/log-reader.svg?style=flat-square)](https://packagist.org/packages/fcno/log-reader)

---

[English](README-en.md) 🔹 [Português](README.md)

---

Leitor de arquivos de log diários para aplicações **[Laravel](https://laravel.com/docs)**.

Além da função primária, este package oferece paginação do conteúdo e dos arquivos de log, bem como leitura linha a linha de maneira transparente, possibilitando trabalhos com arquivos grandes sem carregá-los inteiramente em memória.

```php
use Fcno\LogReader\Facades\RecordReader;

RecordReader::from(disk: 'file_system_name')
            ->infoAbout(log_file: 'filename.log')
            ->get();
```

&nbsp;

---

## Conteúdo

1. [Notas](#notas)

2. [Pré-requisitos](#pré-requisitos)

3. [Instalação](#instalação)

4. [Como funciona](#como-funciona)

    1. [LogReader](#fcnologReaderfacadeslogreader)

    2. [RecordReader](#fcnologReaderfacadesrecordreader)

    3. [SummaryReader](#fcnologReaderfacadessummaryreader)

5. [Testes e Integração Contínua](#testes-e-integração-contínua)

6. [Changelog](#changelog)

7. [Contribuição](#contribuição)

8. [Vulnerabilidades de Segurança](#vulnerabilidades-de-segurança)

9. [Suporte e Atualizações](#suporte-e-atualizações)

10. [Créditos](#créditos)

11. [Agradecimentos](#agradecimentos)

12. [Licença](#licença)

---

## Notas

⭐ Este package é destinado a leitura de arquivos de **[log diários](https://laravel.com/docs/8.x/logging#configuring-the-single-and-daily-channels)** gerados por aplicações **[Laravel](https://laravel.com/docs)**. Utilizá-lo para leitura de outros tipos pode (e irá) trazer resultados equivocados.

⭐ Este package não provê **[views](https://laravel.com/docs/8.x/views)**, visto que se trata de funcionalidade que seria, na prática, pouco aproveitada, dada as preferências pessoais de cada um. Portanto, a implementação das views fica a cargo do desenvolvedor da aplicação.

⬆️ [Voltar](#conteúdo)

&nbsp;

## Pré-requisitos

PHP ^8.0

Para uma checagem completa dos pré-requisitos:

1. Via [Composer](https://getcomposer.org/doc/03-cli.md#check-platform-reqs)

    ```bash
    composer require fcno/log-reader
    composer check-platform-reqs
    ```

2. Via [GitHub Dependencies](../../network/dependencies)

⬆️ [Voltar](#conteúdo)

&nbsp;

## Instalação

1. Configurar um **custom channel** para definir os campos e os delimitadores dos registros de um arquivo de log diário

    ```php
    // config/logging.php

    'channels' => [
        ...
        'custom' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'), // de acordo com sua necessidade
            'days' => 30,                         // de acordo com sua necessidade
            'formatter' => Monolog\Formatter\LineFormatter::class,
            'formatter_with' => [
                'format' => "#@#%datetime%|||%channel%|||%level_name%|||%message%|||%context%|||%extra%@#@\n",
                'dateFormat' => 'd-m-Y H:i:s',
            ],
        ],
    ],
    ```

    &nbsp;

2. Definir a variável env **LOG_CHANNEL** para usar o channel criado

    ```php
    // .env
    LOG_CHANNEL=custom
    ```

    &nbsp;

3. Definir e configurar o disco em que os arquivos de log estão armazenados

    ```php
    // config/filesystems.php

    'disks' => [
        // ...
        'disk_name' => [
            'driver' => 'local',
            'root' => storage_path('logs'), // de acordo com sua necessidade
        ],
    ],
    ```

    &nbsp;

4. Instalar o package via **[composer](https://getcomposer.org/)**:

    ```bash
    composer require fcno/log-reader
    ```

⬆️ [Voltar](#conteúdo)

&nbsp;

## Como funciona

Este package expôe três maneiras de interagir com os arquivos de log, cada uma por meio de uma **[Facade](https://laravel.com/docs/8.x/facades)** com objetivos específicos:

&nbsp;

1. ### **Fcno\LogReader\Facades\LogReader**

    Responsável por manipular os arquivos (no padrão **laravel-yyyy-mm-dd.log**), sem contudo ler o seu conteúdo.

    ✏️ **from**

    Assinatura e uso: informa ao package em que disco a aplicação armazena os arquivos de log

    ```php
    use Fcno\LogReader\Facades\LogReader;

    /**
     * @param string $disk nome do disco de log do File System
     * 
     * @return static
     */
    LogReader::from(disk: 'disk_name');
    ```

    &nbsp;

    Retorno: Instância da classe **LogReader**

    &nbsp;

    ✏️ **get**

    Assinatura e uso: Todos os arquivos de log do disco

    ```php
    use Fcno\LogReader\Facades\LogReader;

    /**
     * @throws \Fcno\LogReader\Exceptions\FileSystemNotDefinedException
     * 
     * @return \Illuminate\Support\Collection
     */
    LogReader::from(disk: 'disk_name')
                ->get();
    ```

    &nbsp;

    Retorno: **[Collection](https://laravel.com/docs/8.x/collections)** com todos os arquivos de log do disco informado ordenados do mais recente para o mais antigo

    ```php
    // \Illuminate\Support\Collection;
    [
        0 => "laravel-2021-12-27.log",
        1 => "laravel-2021-12-26.log",
        2 => "laravel-2021-12-25.log",
        3 => "laravel-2021-12-24.log",
        4 => "laravel-2021-12-23.log",
        5 => "laravel-2021-12-22.log",
        6 => "laravel-2021-12-21.log",
        7 => "laravel-2021-12-20.log",
        8 => "laravel-2021-12-19.log",
        9 => "laravel-2021-12-18.log",
        // ...
    ]
    ```

    &nbsp;

    ✏️ **paginate**

    Assinatura e uso: 5 arquivos de log da página 2, ou seja, do 6º ao 10º arquivos

    ```php
    use Fcno\LogReader\Facades\LogReader;

    /**
     * @param int  $page número da página
     * @param int  $per_page itens por página
     * 
     * @throws \Fcno\LogReader\Exceptions\InvalidPaginationException
     * @throws \Fcno\LogReader\Exceptions\FileSystemNotDefinedException
     * 
     * @return \Illuminate\Support\Collection
     */
    LogReader::from(disk: 'disk_name')
                ->paginate(page: 2, per_page: 5);
    ```

    &nbsp;

    Retorno: **[Collection](https://laravel.com/docs/8.x/collections)** paginada com dados no mesmo formato do método **get**

    > Retornará uma **[Collection](https://laravel.com/docs/8.x/collections)** vazia ou com quantidade de itens menor que a esperada, caso a listagem dos arquivos já tenha chegado ao seu fim.

    &nbsp;

    🚨 **Exceptions**:

    - O método **get** lança:

        - **\Fcno\LogReader\Exceptions\FileSystemNotDefinedException** caso o método seja acionado sem previamente se definir o **disco** do **File System**

    - O método **paginate** lança:

        - **\Fcno\LogReader\Exceptions\InvalidPaginationException** caso **$page** ou **$per_page** sejam menores que 1

        - **\Fcno\LogReader\Exceptions\FileSystemNotDefinedException** caso o método seja acionado sem previamente se definir o **disco** do **File System**

    ⬆️ [Voltar](#conteúdo)

    &nbsp;

    ---

    &nbsp;

2. ### **Fcno\LogReader\Facades\RecordReader**

    Responsável por ler o conteúdo (registros / records) do arquivo de log.

    O registro (record) é o nome dado ao conjunto de informações que foram adicionadas ao log para registrar dados sobre um evento de interesse.

    Um arquivo de log pode conter um ou mais registros e, dada a sua infinidade, podem ser paginados a critério do desenvolvedor da aplicação.

    ✏️ **from**

    Assinatura e uso: informa ao package em que disco a aplicação armazena os arquivos de log

    ```php
    use Fcno\LogReader\Facades\RecordReader;

    /**
     * @param string $disk nome do disco de log do File System
     * 
     * @return static
     */
    RecordReader::from(disk: 'disk_name');
    ```

    &nbsp;

    Retorno: Instância da classe **RecordReader**

    &nbsp;

    ✏️ **infoAbout**

    Assinatura e uso: informa ao package qual arquivo de log deve ser trabalhado

    ```php
    use Fcno\LogReader\Facades\RecordReader;

    /**
     * @param string  $log_file nome do arquivo de log que que será trabalhado
     * 
     * @throws \Fcno\LogReader\Exceptions\FileNotFoundException
     * @throws \Fcno\LogReader\Exceptions\NotDailyLogException
     * @throws \Fcno\LogReader\Exceptions\FileSystemNotDefinedException
     * 
     * @return static
     */
    RecordReader::from(disk: 'disk_name')
                ->infoAbout(log_file: 'filename.log');
    ```

    &nbsp;

    Retorno: Instância da classe **RecordReader**

    &nbsp;

    ✏️ **get**

    Assinatura e uso: Todos os registros do arquivo de log

    ```php
    use Fcno\LogReader\Facades\RecordReader;

   /**
     * @throws \Fcno\LogReader\Exceptions\FileSystemNotDefinedException
     * 
     * @return \Illuminate\Support\Collection
     */
    RecordReader::from(disk: 'disk_name')
                ->infoAbout(log_file: 'filename.log')
                ->get();
    ```

    &nbsp;

    Retorno: **[Collection](https://laravel.com/docs/8.x/collections)** com todos os registros do arquivo de log

    ```php
    // \Illuminate\Support\Collection;
    [
        "date" => "2021-12-27",
        "time" => "03:05:08",
        "env" => "production",
        "level" => "emergency",
        "message" => "Lorem ipsum dolor sit amet.",
        "context" => "Donec ultrices ex libero, ut euismod dui ,vulputate et. Quisque et vestibulum eros, quis dapibus ipsum.",
        "extra" => ""
    ],
    [
        "date" => "2021-12-27",
        "time" => "04:05:08",
        "env" => "local",
        "level" => "info",
        "message" => "Donec imperdiet dapibus facilisis.",
        "context" => "Integer sollicitudin, mauris sit amet luctus finibus, arcu lorem fringilla velit, eget scelerisque ex metus in ante.",
        "extra" => "velit"
    ]
    ```

    &nbsp;

    ✏️ **paginate**

    Assinatura e uso: 5 registros da página 2 do arquivo de log, ou seja, do 6º ao 10º

    ```php
    use Fcno\LogReader\Facades\RecordReader;

    /**
     * @param int  $page número da página
     * @param int  $per_page itens por página
     * 
     * @throws \Fcno\LogReader\Exceptions\InvalidPaginationException
     * @throws \Fcno\LogReader\Exceptions\FileSystemNotDefinedException
     * 
     * @return \Illuminate\Support\Collection
     */
    RecordReader::from(disk: 'disk_name')
                ->infoAbout(log_file: 'filename.log')
                ->paginate(page: 2, per_page: 5);
    ```

    &nbsp;

    Retorno: **[Collection](https://laravel.com/docs/8.x/collections)**  paginada com dados no mesmo formato do método **get**

    > Retornará uma **[Collection](https://laravel.com/docs/8.x/collections)** vazia ou com quantidade de itens menor que a esperada, caso os registros já tenham chegado ao seu fim.
    >
    > Os registros são exibidos na ordem em que estão gravados no arquivo. Não existe ordenação alguma feita por este package.

    &nbsp;

    🚨 **Exceptions**:

    - O método **infoAbout** lança:

        - **Fcno\LogReader\Exceptions\FileNotFoundException** caso o arquivo não seja encontrado;

        - **Fcno\LogReader\Exceptions\NotDailyLogException** caso o aquivo não seja no padrão **laravel-yyy-mm-dd.log**.

        - **\Fcno\LogReader\Exceptions\FileSystemNotDefinedException** caso o método seja acionado sem previamente se definir o **disco** do **File System**

    - O método **get** lança:

        - **\Fcno\LogReader\Exceptions\FileSystemNotDefinedException** caso o método seja acionado sem previamente se definir o **disco** do **File System**

    - O método **paginate** lança:

        - **\Fcno\LogReader\Exceptions\InvalidPaginationException** caso **$page** ou **$per_page** sejam menores que 1

        - **\Fcno\LogReader\Exceptions\FileSystemNotDefinedException** caso o método seja acionado sem previamente se definir o **disco** do **File System**

    ⬆️ [Voltar](#conteúdo)

    &nbsp;

    ---

    &nbsp;

3. ### **Fcno\LogReader\Facades\SummaryReader**

    Responsável por ler o conteúdo (registros / records) do arquivo de log e gerar um sumário.

    O sumário (summary) é o nome dado a contabilização dos registros (records) por nível, isto é, a quantidade de registros do tipo **debug**, **info**, **notice** etc.

    &nbsp;

    ✏️ **from**

    Assinatura: informa ao package em que disco a aplicação armazena os arquivos de log

    ```php
    use Fcno\LogReader\Facades\SummaryReader;

    /**
     * @param string $disk nome do disco de log do File System
     * 
     * @return static
     */
    SummaryReader::from(disk: 'disk_name');
    ```

    &nbsp;

    Retorno: Instância da classe **SummaryReader**

    &nbsp;

    ✏️ **infoAbout**

    Assinatura e uso: informa ao package qual arquivo de log deve ser trabalhado

    ```php
    use Fcno\LogReader\Facades\SummaryReader;

    /**
     * @param string  $log_file nome do arquivo de log que que será trabalhado
     * 
     * @throws \Fcno\LogReader\Exceptions\FileNotFoundException
     * @throws \Fcno\LogReader\Exceptions\NotDailyLogException
     * @throws \Fcno\LogReader\Exceptions\FileSystemNotDefinedException
     * 
     * @return static
     */
    SummaryReader::from(disk: 'disk_name')
                    ->infoAbout(log_file: 'filename.log');
    ```

    &nbsp;

    ✏️ **get**

    Assinatura e uso: Sumário de todos os registros do arquivo de log, bem como a sua data

    ```php
    use Fcno\LogReader\Facades\SummaryReader;

   /**
     * @throws \Fcno\LogReader\Exceptions\FileSystemNotDefinedException
     * 
     * @return \Illuminate\Support\Collection
     */
    SummaryReader::from(disk: 'disk_name')
                    ->infoAbout(log_file: 'filename.log')
                    ->get();
    ```

    &nbsp;

    Retorno: **[Collection](https://laravel.com/docs/8.x/collections)** com o sumário de todos os registros do arquivo de log informado bem como a sua data, isto é, a quantidade de ocorrências dos diversos níveis de log presentes no arquivo, bem como a data de suas ocorrências

    ```php
    // \Illuminate\Support\Collection;
    [
        "alert" => 5,
        "debug" => 10,
        "date" => "2021-12-27"
    ],
    [
        "emergency" => 1,
        "info" => 5,
        "warning" => 10,
        "date" => "2021-12-26"
    ]
    ```

    &nbsp;

    > Este package não possui cravado em seu código a necessidade de os níveis de log da aplicação serem aderentes à **[PSR-3](https://www.php-fig.org/psr/psr-3/)**. Contudo, é considerado boa prática implementar esse tipo de padrão na aplicação.
    >
    > Níveis que não possuírem registros, não serão retornados (contabilizados) na Coleção.
    >
    > A data, no padrão **yyyy-mm-dd**, retornada é a presente no primeiro registro. Parte-se do princípio que todos os registros do arquivo foram gerados no mesmo dia, visto que este package destina-se aos logs diários.

    &nbsp;

    🚨 **Exceptions**:

    - O método **infoAbout** lança:

        - **Fcno\LogReader\Exceptions\FileNotFoundException** caso o arquivo não seja encontrado;

        - **Fcno\LogReader\Exceptions\NotDailyLogException** caso o aquivo não seja no padrão **laravel-yyy-mm-dd.log**.

        - **\Fcno\LogReader\Exceptions\FileSystemNotDefinedException** caso o método seja acionado sem previamente se definir o **disco** do **File System**

    - O método **get** lança:

        - **\Fcno\LogReader\Exceptions\FileSystemNotDefinedException** caso o método seja acionado sem previamente se definir o **disco** do **File System**

    ⬆️ [Voltar](#conteúdo)

    &nbsp;

## Testes e Integração Contínua

```bash
composer analyse
composer test
composer test-coverage
```

⬆️ [Voltar](#conteúdo)

&nbsp;

## Changelog

Por favor, veja o [CHANGELOG](../CHANGELOG.md) para maiores informações sobre o que mudou em cada versão.

⬆️ [Voltar](#conteúdo)

&nbsp;

## Contribuição

Por favor, veja [CONTRIBUTING](../.github/CONTRIBUTING.md) para maiores detalhes.

⬆️ [Voltar](#conteúdo)

&nbsp;

## Vulnerabilidades de Segurança

Por favor, veja na [política de segurança](../../security/policy) como reportar vulnerabilidades ou falha de segurança.

⬆️ [Voltar](#conteúdo)

&nbsp;

## Suporte e Atualizações

A versão mais recente receberá suporte e atualizações sempre que houver necessidade. As demais receberão apenas atualizações para corrigir [vulnerabilidades de segurança](#vulnerabilidades-de-segurança) por até 06 meses após ela ter sido substituída por uma nova versão.

🐛 Encontrou um bug?!?! Abra um **[issue](../../issues/new)**.

✨ Alguma ideia nova?!?! Inicie [uma discussão](../../discussions/new?category=ideas).

⬆️ [Voltar](#conteúdo)

&nbsp;

## Créditos

- [Fábio Cassiano](https://github.com/fcno)

- [All Contributors](../../contributors)

⬆️ [Voltar](#conteúdo)

&nbsp;

## Agradecimentos

👋 Agradeço às pessoas e organizações abaixo por terem doado seu tempo na construção de projetos open-source que foram usados neste package.

- ❤️ [Laravel](https://github.com/laravel) pelos packages:

  - [illuminate/collections](https://github.com/illuminate/collections)

  - [illuminate/contracts](https://github.com/illuminate/contracts)

  - [illuminate/filesystem](https://github.com/illuminate/filesystem)

  - [illuminate/support](https://github.com/illuminate/support)

- ❤️ [Spatie](https://github.com/spatie) pelos packages:

  - [spatie/package-skeleton-laravel](https://github.com/spatie/package-skeleton-laravel)

  - [spatie/laravel-package-tools](https://github.com/spatie/laravel-package-tools)

  - [spatie/laravel-ray](https://github.com/spatie/laravel-ray)

- ❤️ [Orchestra Platform](https://github.com/orchestral) pelo package [orchestral/testbench](https://github.com/orchestral/testbench)

- ❤️ [Nuno Maduro](https://github.com/FakerPHP) pelos packages:

  - [nunomaduro/collision](https://github.com/nunomaduro/collision)

  - [nunomaduro/larastan](https://github.com/nunomaduro/larastan)

- ❤️ [PEST](https://github.com/pestphp) pelos packages:

  - [pestphp/pest](https://github.com/pestphp/pest)

  - [pestphp/pest-plugin-laravel](https://github.com/pestphp/pest-plugin-laravel)

- ❤️ [Benjamin Cremer](https://github.com/bcremer) pelo package [bcremer/LineReader](https://github.com/bcremer/LineReader)

- ❤️ [Jordi Boggiano](https://github.com/Seldaek) pelo package [Seldaek/monolog](https://github.com/Seldaek/monolog)

- ❤️ [Sebastian Bergmann](https://github.com/sebastianbergmann) pelo package [sebastianbergmann/phpunit](https://github.com/sebastianbergmann/phpunit)

- ❤️ [FakerPHP](https://github.com/FakerPHP) pelo package [FakerPHP/Faker](https://github.com/FakerPHP/Faker)

- ❤️ [PHPStan](https://github.com/phpstan) pelos packages:

  - [phpstan/phpstan](https://github.com/phpstan/phpstan)

  - [phpstan/phpstan-deprecation-rules](https://github.com/phpstan/phpstan-deprecation-rules)

  - [phpstan/phpstan-phpunit](https://github.com/phpstan/phpstan-phpunit)

💸 Algumas dessas pessoas ou organizações possuem alguns produtos/serviços que podem ser comprados. Se você puder ajudá-los comprando algum deles ou se tornando um patrocinador, mesmo que por curto período, ajudará toda a comunidade **open-source** a continuar desenvolvendo soluções para todos.

⬆️ [Voltar](#conteúdo)

&nbsp;

## Licença

The MIT License (MIT). Por favor, veja o **[License File](../LICENSE.md)** para maiores informações.

⬆️ [Voltar](#conteúdo)

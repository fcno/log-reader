# Log Reader para aplicações Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/fcno/log-reader.svg?style=flat-square)](https://packagist.org/packages/fcno/log-reader)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/fcno/log-reader/run-tests?label=tests)](https://github.com/fcno/log-reader/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/fcno/log-reader/Check%20&%20fix%20styling?label=code%20style)](https://github.com/fcno/log-reader/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/fcno/log-reader.svg?style=flat-square)](https://packagist.org/packages/fcno/log-reader)

Leitor de arquivos de log diários para aplicações **[Laravel](https://laravel.com/)**.

Além da função primária, este *package* oferece paginação do conteúdo e dos arquivos de log, bem como leitura linha a linha possibilitando trabalhos com arquivos grandes, sem carregá-los inteiramente em memória.

```php
use Fcno\LogReader\Facades\RecordReader;

RecordReader::from('file_system_name')
            ->infoAbout('filename.log')
            ->get();
```

&nbsp;

## Notas

- Este *package* é destinado a leitura de arquivos de **[log diários](https://laravel.com/docs/8.x/logging#configuring-the-single-and-daily-channels)** gerados por aplicações **[Laravel](https://laravel.com/)**. Utilizá-lo para leitura de outros tipos pode (e irá) trazer resultados equivocados.
- O termo 'disk_name' é usado ao longo desta documentação para representar a *string* com o nome do disco de armazenamento dos arquivos de log configurado no *[File System](https://laravel.com/docs/8.x/filesystem)*. Não se trata de uma instãncia da classe, mas apenas de seu nome.
- O termo 'file_name.log' é usado ao longo desta documentação para representar o nome do arquivo de log diário, gerado no padrão **laravel-yyyy-mm-dd.log**. Ex.: laravel-2020-01-30.log

&nbsp;

## Instalação

1. Configurar o *custom channel* para definir os campos e os delimitadores dos registros do arquivo de log

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

2. Definir a variável **LOG_CHANNEL** para usar o *channel* criado

    ```php
    // .env
    LOG_CHANNEL=custom
    ```

    &nbsp;

3. Definir e configurar o disco em que os arquivos de log são armazenados

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

4. Instalar o *package* via **[composer](https://getcomposer.org/)**:

    ```bash
    composer require fcno/log-reader
    ```

&nbsp;

## Uso

Este *package* expôe três maneiras de interagir com os arquivos de log, cada uma por meio de uma **[Facade](https://laravel.com/docs/8.x/facades)** com objetivos específicos:

&nbsp;

1. **Fcno\LogReader\Facades\LogReader**

    Responsável por manipular os arquivos (no padrão **laravel-yyyy-mm-dd.log**), sem contudo ler o seu conteúdo.

    - O métido ***get*** retorna uma **[Collection](https://laravel.com/docs/8.x/collections)** com todos os arquivos de log do disco informado ordenados do mais recente para o mais antigo.

    ```php
    use Fcno\LogReader\Facades\LogReader;

    LogReader::from('disk_name')
                ->get();
    ```

    &nbsp;

    Retorno: A coleção possuirá todos os arquivos de log do disco.

    ```php
    // \Illuminate\Support\Collection;
    [
        0 => "laravel-2021-12-27.log"
        1 => "laravel-2021-12-26.log"
        2 => "laravel-2021-12-25.log"
        3 => "laravel-2021-12-24.log"
        4 => "laravel-2021-12-23.log"
        5 => "laravel-2021-12-22.log"
        6 => "laravel-2021-12-21.log"
        7 => "laravel-2021-12-20.log"
        8 => "laravel-2021-12-19.log"
        9 => "laravel-2021-12-18.log"
    ]
    ```

    &nbsp;

    - O método ***paginate*** retorna uma **[Collection](https://laravel.com/docs/8.x/collections)** paginada dos arquivos de log do disco informado ordenados do mais recente para o mais antigo. No exemplo, retorna 5 arquivos da página 2, ou seja, do 6º ao 10º arquivo.

    ```php
    use Fcno\LogReader\Facades\LogReader;

    LogReader::from('disk_name')
                ->paginate(page: 2, per_page: 5);
    ```

    &nbsp;

    Retorno: Coleção paginada com dados no mesmo formato do método ***get***.

    > Retornará uma Coleção vazia ou com quantidade de itens menor que a esperada, caso a listagem dos arquivos já tenha chegado ao seu fim.

    &nbsp;

    ***Exceptions***:

    - O método ***paginate*** da classe **LogReader** lança:
        - ***\RuntimeException*** caso ***$page*** ou ***$per_page*** sejam menores que 1.

    &nbsp;

    ---

    &nbsp;

2. **Fcno\LogReader\Facades\RecordReader**

    Responsável por ler o conteúdo (registros / *records*) do arquivo de log.

    O registro (*record*) é o nome dado ao conjunto de informações que foram adicionadas ao log para registrar dados sobre um evento de interesse.

    Um arquivo de log pode conter um ou mais registros e, dada a sua infinidade, podem ser paginados a critério do desenvolvedor.

    &nbsp;

    - O método ***get*** retorna uma **[Collection](https://laravel.com/docs/8.x/collections)** com todos os registros do arquivo de log informado.

    ```php
    use Fcno\LogReader\Facades\RecordReader;

    RecordReader::from('disk_name')
                ->infoAbout('filename.log')
                ->get();
    ```

    &nbsp;

    Retorno: A coleção possuirá todos os registros do arquivo de log.

    ```php
    // \Illuminate\Support\Collection;
    [
        "date" => "2021-12-27"
        "time" => "03:05:08"
        "env" => "production"
        "level" => "emergency"
        "message" => "Lorem ipsum dolor sit amet."
        "context" => "Donec ultrices ex libero, ut euismod dui vulputate et. Quisque et vestibulum eros, quis dapibus ipsum."
        "extra" => ""
    ],
    [
        "date" => "2021-12-27"
        "time" => "04:05:08"
        "env" => "local"
        "level" => "info"
        "message" => "Donec imperdiet dapibus facilisis."
        "context" => "Integer sollicitudin, mauris sit amet luctus finibus, arcu lorem fringilla velit, eget scelerisque ex metus in ante."
        "extra" => "velit"
    ]
    ```

    &nbsp;

    - O método ***paginate*** retorna uma **[Collection](https://laravel.com/docs/8.x/collections)** paginada dos registros do arquivo de log informado. No exemplo, retorna 20 registros da página 3, ou seja, do 41º ao 60º registro do arquivo.

    ```php
    use Fcno\LogReader\Facades\RecordReader;

    RecordReader::from('disk_name')
                ->infoAbout('filename.log')
                ->paginate(page: 3, per_page: 20);
    ```

    &nbsp;

    Retorno: Coleção paginada com dados no mesmo formato do método ***get***.

    >Retornará uma **[Collection](https://laravel.com/docs/8.x/collections)** vazia ou com quantidade de itens menor que a esperada, caso os registros já tenham chegado ao seu fim.
    >
    > Os registros são exibidos na ordem em que estão gravados no arquivo. Não existe ordenação alguma feita por este *package*.

    &nbsp;

    ***Exceptions***:

    - O método **infoAbout** da classe **RecordReader** lança:

        - ***Fcno\LogReader\Exceptions\FileNotFoundException*** caso o arquivo não seja encontrado;

        - ***Fcno\LogReader\Exceptions\NotDailyLogException*** caso o aquivo não seja no padrão **laravel-yyy-mm-dd.log**;

    - O método ***paginate*** lança:
        - ***\RuntimeException*** caso ***$page*** ou ***$per_page*** sejam menores que 1.

    &nbsp;

    ---

    &nbsp;

3. **Fcno\LogReader\Facades\SummaryReader**

    Responsável por ler o conteúdo (registros / *records*) do arquivo de log e gerar um sumário.

    O sumário (*summary*) é o nome dado a contabilização dos registros (*records*) por nível, isto é, a quantidade de registros do tipo ***debug***, ***info***, ***notice*** etc.

    &nbsp;

    - O método ***get*** retorna uma **[Collection](https://laravel.com/docs/8.x/collections)** com o sumário de todos os registros do arquivo de log informado bem como a sua data.

    ```php
    use Fcno\LogReader\Facades\SummaryReader;

    SummaryReader::from('disk_name')
                    ->infoAbout('filename.log')
                    ->get();
    ```

    &nbsp;

    Retorno: A coleção possuirá a quantidade de ocorrências dos diversos níveis de log presentes no arquivo, bem como a data de suas ocorrências.

    ```php
    // \Illuminate\Support\Collection;
    [
        "alert" => 5
        "debug" => 10
        "date" => "2021-12-27"
    ]
    ```

    &nbsp;

    > Este *package* não possui, cravado em seu código, a necessidade de os níveis de log da aplicação serem aderentes à **[PSR-3](https://www.php-fig.org/psr/psr-3/)**. Contudo, é considerado boa prática implementar esse tipo de padrão na aplicação.
    >
    > Níveis que não possuírem registros, não serão retornados (contabilizados) na Coleção.
    >
    > A data, no padrão **yyyy-mm-dd**, retornada é a presente no primeiro registro. Parte-se do princípio que todos os registros do arquivo foram gerados no mesmo dia, visto que este *package* destina-se aos logs diários.

    &nbsp;

    ***Exceptions***:

    - O método **infoAbout** da classe **SummaryReader** lança:

        - ***Fcno\LogReader\Exceptions\FileNotFoundException*** caso o arquivo não seja encontrado;

        - ***Fcno\LogReader\Exceptions\NotDailyLogException*** caso o aquivo não seja no padrão **laravel-yyy-mm-dd.log**;

    &nbsp;

## Testes e Integração Contínua

```bash
composer analyse
composer test
composer test-coverage
```

&nbsp;

## Changelog

Por favor, veja o [CHANGELOG](CHANGELOG.md) para maiores informações sobre o que mudou recentemente.

&nbsp;

## Contribuição

Por favor, veja [CONTRIBUTING](.github/CONTRIBUTING.md) para maiores detalhes.

&nbsp;

## Vulnerabilidades e Segurança

Por favor, veja na [política de segurança](../../security/policy) como reportar uma vulnerabilidade.

&nbsp;

## Crédidos

- [Fabio Cassiano](https://github.com/fcno)
- [All Contributors](../../contributors)

&nbsp;

## Licença

The MIT License (MIT). Por favor, veja o ***[License File](LICENSE.md)*** para maiores informações.

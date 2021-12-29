<?php

/**
 * @link https://github.com/FriendsOfPHP/PHP-CS-Fixer
 */

$finder = Symfony\Component\Finder\Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'not_operator_with_successor_space' => true,
        'blank_line_before_statement' => [
            'statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try'],
        ],
        'phpdoc_line_span' => true,
        'phpdoc_order' => true,
        'phpdoc_order_by_value' => [
            'annotations' => ['author', 'covers', 'coversNothing', 'dataProvider', 'depends', 'group', 'internal', 'method', 'property', 'property-read', 'property-write', 'requires', 'throws', 'uses']
        ],
        'phpdoc_types_order' => [
            'null_adjustment' => 'always_last', 'sort_algorithm' => 'alpha'
        ],
        'phpdoc_var_annotation_correct_order' => true,
        'phpdoc_var_without_name' => false,
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
            'keep_multiple_spaces_after_comma' => true,
        ],
    ])
    ->setFinder($finder);

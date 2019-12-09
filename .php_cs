<?php
// Inspired by  https://github.com/jolicode/codingstyle
$header = <<<OEF
This file is part of the EasyblueDoctrineExtensionsBundle project.
(c) Easyblue <support@easyblue.io>
For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
OEF;
return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'header_comment' => ['header' => $header],
        'array_syntax' => ['syntax' => 'short'],
        'ordered_class_elements' => true,
        'ordered_imports' => true,
        'heredoc_to_nowdoc' => true,
        'php_unit_strict' => true,
        'php_unit_construct' => true,
        'phpdoc_add_missing_param_annotation' => true,
        'phpdoc_no_package' => true,
        'phpdoc_no_useless_inheritdoc' => true,
        'phpdoc_order' => true,
        'phpdoc_separation' => true,
        'phpdoc_indent' => true,
        'phpdoc_inline_tag' => true,
        'no_empty_phpdoc' => true,
        'strict_comparison' => true,
        'strict_param' => true,
        'single_quote' => true,
        'yoda_style' => true,
        'no_extra_consecutive_blank_lines' => [
            'break',
            'continue',
            'extra',
            'return',
            'throw',
            'use',
            'parenthesis_brace_block',
            'square_brace_block',
            'curly_brace_block',
        ],
        'no_short_echo_tag' => true,
        'no_unreachable_default_argument_value' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'semicolon_after_instruction' => true,
        'combine_consecutive_unsets' => true,
        'ternary_to_null_coalescing' => true,
        'declare_strict_types' => true,
        'declare_equal_normalize' => ['space' =>'single'],
        'no_superfluous_phpdoc_tags' => false,
        'single_line_throw' => false,
        'binary_operator_spaces' => [
            'operators' => [
                '=' => 'align',
                '=>' => 'align',
            ],
        ],
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in('Resources')
            ->in('Traits')
            ->in('DependencyInjection')
            ->in('EventSubscriber')
    )
;

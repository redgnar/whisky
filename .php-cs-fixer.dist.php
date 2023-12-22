<?php

$currentDir = getcwd();
$finder = PhpCsFixer\Finder::create();

$finder
    ->in($currentDir.'/src');

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'multiline_whitespace_before_semicolons' => false,
        'no_unused_imports' => true,
        'no_useless_else' => true,
        'protected_to_private' => false,
        'array_indentation' => true,
        'blank_line_between_import_groups' => false,
        'align_multiline_comment' => true,
    ])
    ->setFinder($finder)
    ->setCacheFile('.php-cs-fixer.cache') // forward compatibility with 3.x line
    ;
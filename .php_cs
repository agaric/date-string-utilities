<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
;

return PhpCsFixer\Config::create()
    ->setRules([
        '@PhpCsFixer' => true,
        'no_short_echo_tag' => false,
        'yoda_style' => [
            'always_move_variable' => false,
            'equal' => false,
            'identical' => false,
            'less_and_greater' => false,
        ],
        'nullable_type_declaration_for_default_null_value' => ['use_nullable_type_declaration' => true],
        'phpdoc_types_order' => [
            'null_adjustment' => 'always_last',
        ],
        'ordered_imports' => [
            'importsOrder' => ['class', 'function', 'const'],
        ],
    ])
    ->setFinder($finder)
    ;

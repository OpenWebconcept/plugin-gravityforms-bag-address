<?php

$finder = Symfony\Component\Finder\Finder::create()
    ->notPath('vendor')
    ->notPath('node_modules')
    ->in(__DIR__)
    ->name('*.php')
    ->notName('*.blade.php');

$config = new PhpCsFixer\Config();
$config
    ->setRules([
        '@PSR2'                  => true,
        'array_syntax'           => ['syntax' => 'short'],
        'ordered_imports'        => [ 'sort_algorithm' => 'alpha' ],
        'no_unused_imports'      => true,
        'binary_operator_spaces' => [
            'default' => 'single_space',
            'operators' => [
                '===' => 'align_single_space_minimal',
                '=>' => 'align_single_space_minimal',
                '=' => 'align_single_space_minimal',
            ]
        ],
        'full_opening_tag'       => true,
        'yoda_style'             => [
            'always_move_variable' => true,
            'equal'                => true,
            'identical'            => true,
            'less_and_greater'     => true,
        ],
    ])
    ->setFinder($finder);

return $config;

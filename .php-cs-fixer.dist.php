<?php

$finder = PhpCsFixer\Finder::create()
    // ->exclude('somedir')
    // ->notPath('src/Symfony/Component/Translation/Tests/fixtures/resources.php')
    ->in(__DIR__)
;

$config = new PhpCsFixer\Config();

return $config->setRules([
        '@Symfony' => true,
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
        ],
    ])
    ->setFinder($finder)
;

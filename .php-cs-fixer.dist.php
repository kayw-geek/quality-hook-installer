<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->ignoreDotFiles(false)
    ->ignoreVCSIgnored(true)
    ->exclude(['dev-tools/phpstan', 'tests/Fixtures'])
    ->in(__DIR__);

$config = new PhpCsFixer\Config();
$config
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12'                                           => true,
        '@Symfony'                                         => true,
        '@Symfony:risky'                                   => true,
        '@PHP80Migration'                                  => true,
        '@PHP80Migration:risky'                            => true,
        'align_multiline_comment'                          => true,
        'array_indentation'                                => true,
        'binary_operator_spaces'                           => ['operators' => ['=' => 'align_single_space_minimal', '=>' => 'align_single_space_minimal', '??=' => 'align_single_space_minimal', '|' => 'no_space']],
        'blank_line_before_statement'                      => ['statements' => ['do', 'for', 'foreach', 'if', 'return', 'switch', 'while']],
        'class_attributes_separation'                      => ['elements' => ['method' => 'one', 'trait_import' => 'none']],
        'combine_consecutive_issets'                       => true,
        'combine_consecutive_unsets'                       => true,
        'concat_space'                                     => ['spacing' => 'one'],
        'echo_tag_syntax'                                  => ['format' => 'long'],
        'escape_implicit_backslashes'                      => true,
        'fopen_flags'                                      => false,
        'fully_qualified_strict_types'                     => false,
        'function_to_constant'                             => ['functions' => ['get_class', 'get_called_class', 'php_sapi_name', 'phpversion', 'pi']],
        'heredoc_to_nowdoc'                                => true,
        'increment_style'                                  => ['style' => 'post'],
        'method_argument_space'                            => ['on_multiline' => 'ensure_fully_multiline', 'keep_multiple_spaces_after_comma' => false, 'after_heredoc' => true],
        'method_chaining_indentation'                      => true,
        'modernize_types_casting'                          => false,
        'multiline_comment_opening_closing'                => true,
        'multiline_whitespace_before_semicolons'           => ['strategy' => 'no_multi_line'],
        'native_constant_invocation'                       => false,
        'native_function_invocation'                       => false,
        'no_alternative_syntax'                            => true,
        'no_null_property_initialization'                  => true,
        'no_superfluous_phpdoc_tags'                       => false,
        'no_unneeded_curly_braces'                         => true,
        'no_useless_return'                                => true,
        'not_operator_with_space'                          => false,
        'nullable_type_declaration_for_default_null_value' => true,
        'operator_linebreak'                               => true,
        'ordered_class_elements'                           => true,
        'ordered_imports'                                  => ['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'alpha'],
        'php_unit_strict'                                  => true,
        'php_unit_test_case_static_method_calls'           => true,
        'phpdoc_add_missing_param_annotation'              => true,
        'phpdoc_annotation_without_dot'                    => false,
        'phpdoc_no_alias_tag'                              => false,
        'phpdoc_no_empty_return'                           => false,
        'phpdoc_summary'                                   => false, // no need to add dot at the end of short description
        'phpdoc_to_comment'                                => false, // allow use of docblock comment in function body
        'phpdoc_var_annotation_correct_order'              => true,
        'pow_to_exponentiation'                            => false,
        'self_static_accessor'                             => true,
        'simplified_null_return'                           => false,
        'strict_comparison'                                => true,
        'strict_param'                                     => true,
        'trailing_comma_in_multiline'                      => ['elements' => ['arrays', 'arguments', 'parameters', 'match']],
        'use_arrow_functions'                              => false,
        'whitespace_after_comma_in_array'                  => true,
        'yoda_style'                                       => ['equal' => false, 'identical' => false, 'less_and_greater' => false, 'always_move_variable' => false],
    ])
    ->setFinder($finder);

return $config;

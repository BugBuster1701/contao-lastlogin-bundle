<?php

$header = <<<EOF
This file is part of a BugBuster Contao Bundle (Resources\contao)

@copyright  Glen Langer 2024 <http://contao.ninja>
@author     Glen Langer (BugBuster)
@package    Lastlogin
@license    LGPL-3.0-or-later
@see        https://github.com/BugBuster1701/contao-lastlogin-bundle
EOF;

$finder = PhpCsFixer\Finder::create()
    ->exclude('languages')
    ->exclude('templates')
    ->exclude('themes')
    ->in([
        __DIR__.'/src/Resources/contao',
    ])
;

$config = new PhpCsFixer\Config();
return $config
    ->setRules([
        'align_multiline_comment' => true,
        'array_syntax' => ['syntax' => 'long'],
        'blank_line_after_opening_tag' => true,
        'blank_line_before_statement' => [
            'statements' => ['return'],
        ],
        'cast_spaces' => true,
        'elseif' => true,
        'escape_implicit_backslashes' => true,
        'function_declaration' => true,
        'function_typehint_space' => true,
        'header_comment' => ['header' => $header],
        'linebreak_after_opening_tag' => true,
        'lowercase_cast' => true,
        'lowercase_keywords' => true,
        'method_argument_space' => true,
        'modernize_types_casting' => true,
        'multiline_comment_opening_closing' => true,
        'native_function_casing' => true,
        'native_function_invocation' => [
            'include' => ['@compiler_optimized'],
        ],
        'new_with_braces' => true,
        'no_alternative_syntax' => true,
        'no_binary_string' => true,
        'no_empty_comment' => true,
        'no_empty_phpdoc' => true,
        'no_empty_statement' => true,
        'no_extra_blank_lines' => true,
        'no_leading_import_slash' => true,
        'no_leading_namespace_whitespace' => true,
        'no_mixed_echo_print' => true,
        'no_multiline_whitespace_around_double_arrow' => true,
        'no_null_property_initialization' => true,
        'no_short_bool_cast' => true,
        'no_singleline_whitespace_before_semicolons' => true,
        'no_spaces_around_offset' => true,
        'no_spaces_inside_parenthesis' => true,
        'no_trailing_comma_in_singleline' => true,
        'no_unneeded_control_parentheses' => true,
        'no_unneeded_curly_braces' => true,
        'no_unreachable_default_argument_value' => true,
        'no_unused_imports' => false,
        'no_whitespace_before_comma_in_array' => true,
        'no_whitespace_in_blank_line' => true,
        'normalize_index_brace' => true,
        'object_operator_without_whitespace' => true,
        'ordered_imports' => true,
        'phpdoc_align' => true,
        'phpdoc_annotation_without_dot' => true,
        'phpdoc_indent' => true,
        'phpdoc_inline_tag_normalizer' => true,
        'phpdoc_no_access' => true,
        'phpdoc_no_alias_tag' => true,
        'phpdoc_no_package' => true,
        'phpdoc_no_useless_inheritdoc' => true,
        'phpdoc_return_self_reference' => true,
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_trim' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'phpdoc_types' => true,
        'phpdoc_var_without_name' => true,
        'return_type_declaration' => true,
        'self_accessor' => true,
        'semicolon_after_instruction' => true,
        'short_scalar_cast' => true,
        'single_blank_line_before_namespace' => true,
        'single_class_element_per_statement' => true,
        'single_import_per_statement' => true,
        'single_line_comment_style' => true,
        'standardize_not_equals' => true,
        'string_line_ending' => true,
        'switch_case_semicolon_to_colon' => true,
        'switch_case_space' => true,
        'ternary_operator_spaces' => true,
        'trim_array_spaces' => true,
        'unary_operator_spaces' => true,
        'whitespace_after_comma_in_array' => true,
        'yoda_style' => false,
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setUsingCache(false)
;

<?php

/**
 * Kunena Component
 *
 * @package         Kunena
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

/**
 * This is the configuration file for php-cs-fixer, after the one of Joomla (joomla-cms, .php-cs-fixer.dist.php).
 *
 * @link https://github.com/FriendsOfPHP/PHP-CS-Fixer
 * @link https://mlocati.github.io/php-cs-fixer-configurator/#version:3.95
 *
 *
 * If you would like to run the automated clean up, then open a command line and type one of the commands below
 *
 * To run a quick dry run to see the files that would be modified:
 *
 *        ./vendor/bin/php-cs-fixer fix --dry-run
 *
 * To run a full check, with automated fixing of each problem :
 *
 *        ./vendor/bin/php-cs-fixer fix
 *
 * You can run the clean up on a single file if you need to, this is faster
 *
 *        ./vendor/bin/php-cs-fixer fix --dry-run src/site/src/Controllers/TopicController.php
 *        ./vendor/bin/php-cs-fixer fix src/site/src/Controllers/TopicController.php
 */

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Config\RuleCustomisationPolicyInterface;
use PhpCsFixer\Finder;

// The code of the package: the component, the libraries and the plugins (not the libraries of other projects which Kunena
// ships, they keep their own code style).
$finder = (new Finder())
    ->in(__DIR__ . '/src')
    ->exclude([
        'libraries/kunena/External',
        'media',
    ])
;

final class KunenaPolicy implements RuleCustomisationPolicyInterface
{
    public function getPolicyVersionForCache(): string
    {
        return hash_file('xxh128', __FILE__);
    }

    public function getRuleCustomisers(): array
    {
        // The rules which change the layout of a file which mixes HTML and PHP: they are not used for the templates (a change
        // there is also a change of every template override of the sites).
        $disableLayoutFile = [
            'binary_operator_spaces',
            'native_function_invocation',
            'no_trailing_whitespace_in_comment',
            'single_space_around_construct',
            'statement_indentation',
            'braces_position',
            'combine_consecutive_issets',
            'no_spaces_after_function_name',
            'no_unneeded_control_parentheses',
            'single_line_after_imports',
            'spaces_inside_parentheses',
            'ternary_operator_spaces',
            'trailing_comma_in_multiline',
            'blank_line_before_statement',
            'no_extra_blank_lines',
        ];

        $ruleCustomisers = [];

        foreach ($disableLayoutFile as $rule) {
            $ruleCustomisers[$rule] = static function (\SplFileInfo $file): bool {
                return !self::isLayoutFile($file);
            };
        }

        return $ruleCustomisers;
    }

    private static function isLayoutFile(\SplFileInfo $file): bool
    {
        $path = str_replace('\\', '/', $file->getPathname());

        // The templates of the backend and of the plugins (tmpl), the templates of the site (layouts and pages of a template of
        // Kunena, like aurelia) and the layouts of Joomla style.
        return str_contains($path, '/tmpl/')
            || str_contains($path, '/layouts/')
            || str_contains($path, '/site/template/');
    }
}

$config = (new Config())
    ->setRiskyAllowed(true)
    ->setHideProgress(false)
    ->setUsingCache(false)
    ->setRules(
        [
            // Basic ruleset is PSR 12
            '@PSR12'                                           => true,
            // Short array syntax
            'array_syntax'                                     => ['syntax' => 'short'],
            // List of values separated by a comma is contained on a single line should not have a trailing comma like [$foo, $bar,] = ...
            'no_trailing_comma_in_singleline'                  => true,
            // Arrays on multiline should have a trailing comma
            'trailing_comma_in_multiline'                      => ['elements' => ['arrays']],
            // Align elements in multiline array and variable declarations on new lines below each other
            'binary_operator_spaces'                           => ['operators' => ['=>' => 'align_single_space_minimal', '=' => 'align', '??=' => 'align']],
            // The "No break" comment in switch statements
            'no_break_comment'                                 => ['comment_text' => 'No break'],
            // Remove unused imports
            'no_unused_imports'                                => true,
            // Classes from the global namespace are imported, like the code of Kunena does (use Exception;), not \Exception in the code
            'global_namespace_import'                          => ['import_classes' => true, 'import_constants' => false, 'import_functions' => false],
            // A fully qualified class name in the code or in a docblock is imported (use) and written short
            'fully_qualified_strict_types'                     => ['import_symbols' => true, 'leading_backslash_in_global_namespace' => false],
            // Alpha order imports
            'ordered_imports'                                => ['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'alpha'],
            // The @property lines of a class docblock: the types, the names and the descriptions in columns
            'phpdoc_align'                                     => ['align' => 'vertical', 'tags' => ['property', 'property-read', 'property-write']],
            // There should not be useless else cases
            'no_useless_else'                                  => true,
            // Native function invocation
            'native_function_invocation'                       => ['include' => ['@compiler_optimized']],
            // Adds null to type declarations when parameter have a default null value
            'nullable_type_declaration_for_default_null_value' => true,
            // Removes unneeded parentheses around control statements
            'no_unneeded_control_parentheses'                  => true,
            // Using isset($var) && multiple times should be done in one call.
            'combine_consecutive_issets'                       => true,
            // Calling unset on multiple items should be done in one call
            'combine_consecutive_unsets'                       => true,
            // There must be no sprintf calls with only the first argument
            'no_useless_sprintf'                               => true,

            // The rules of Kunena which its code follows already (from the earlier configuration of Kunena)
            // One space around the concatenation operator: 'a' . 'b'
            'concat_space'                                     => ['spacing' => 'one'],
            // A blank line before a return
            'blank_line_before_statement'                      => ['statements' => ['return']],
            // No more than one blank line, no blank lines with spaces
            'no_extra_blank_lines'                             => true,
            'no_whitespace_in_blank_line'                      => true,
            // One space after a cast: (int) $value
            'cast_spaces'                                      => true,
            // No empty statements (;;)
            'no_empty_statement'                               => true,
        ]
    )
    ->setRuleCustomisationPolicy(new KunenaPolicy())
    ->setFinder($finder)
;

return $config;

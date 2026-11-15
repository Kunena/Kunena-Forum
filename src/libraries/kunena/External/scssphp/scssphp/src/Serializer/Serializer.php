<?php

/**
 * SCSSPHP
 *
 * @copyright 2018-2020 Anthon Pang
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @link http://scssphp.github.io/scssphp
 */

namespace KunenaScssPhp\ScssPhp\Serializer;

use KunenaScssPhp\ScssPhp\Ast\Css\CssNode;
use KunenaScssPhp\ScssPhp\Ast\Css\CssParentNode;
use KunenaScssPhp\ScssPhp\Ast\Selector\Selector;
use KunenaScssPhp\ScssPhp\Exception\SassScriptException;
use KunenaScssPhp\ScssPhp\Logger\LoggerInterface;
use KunenaScssPhp\ScssPhp\OutputStyle;
use KunenaScssPhp\ScssPhp\Value\Value;
use KunenaScssPhp\ScssPhp\Visitor\CssVisitor;
use KunenaScssPhp\ScssPhp\Visitor\ModifiableCssVisitor;

/**
 * @internal
 */
final class Serializer
{
    public static function serialize(CssNode $node, bool $inspect = false, OutputStyle $style = OutputStyle::EXPANDED, bool $sourceMap = false, bool $charset = true, ?LoggerInterface $logger = null): SerializeResult
    {
        $visitor = new SerializeVisitor($inspect, true, $style, $sourceMap, $logger);
        $node->accept($visitor);
        $css = (string) $visitor->getBuffer();

        $prefix = '';

        if ($charset && strlen($css) !== mb_strlen($css, 'UTF-8')) {
            if ($style === OutputStyle::COMPRESSED) {
                $prefix = "\u{FEFF}";
            } else {
                $prefix = '@charset "UTF-8";' . "\n";
            }
        }

        return new SerializeResult(
            $prefix . $css,
            $sourceMap ? $visitor->getBuffer()->buildSourceMap($prefix) : null,
        );
    }

    /**
     * Converts $value to a CSS string.
     *
     * If $inspect is `true`, this will emit an unambiguous representation of the
     * source structure. Note however that, although this will be valid SCSS, it
     * may not be valid CSS. If $inspect is `false` and $value can't be
     * represented in plain CSS, throws a {@see SassScriptException}.
     *
     * If $quote is `false`, quoted strings are emitted without quotes.
     */
    public static function serializeValue(Value $value, bool $inspect = false, bool $quote = true): string
    {
        // Force loading the CssParentNode and CssVisitor before using the visitor because of a weird PHP behavior.
        class_exists(CssParentNode::class);
        class_exists(CssVisitor::class);

        $visitor = new SerializeVisitor($inspect, $quote);
        $value->accept($visitor);

        return (string) $visitor->getBuffer();
    }

    /**
     * Converts $selector to a CSS string.
     *
     * If $inspect is `true`, this will emit an unambiguous representation of the
     * source structure. Note however that, although this will be valid SCSS, it
     * may not be valid CSS. If $inspect is `false` and $selector can't be
     * represented in plain CSS, throws a {@see SassScriptException}.
     */
    public static function serializeSelector(Selector $selector, bool $inspect = false): string
    {
        // Force loading the CssParentNode and CssVisitor before using the visitor because of a weird PHP behavior.
        class_exists(CssParentNode::class);
        class_exists(CssVisitor::class);

        $visitor = new SerializeVisitor($inspect);
        $selector->accept($visitor);

        return (string) $visitor->getBuffer();
    }
}

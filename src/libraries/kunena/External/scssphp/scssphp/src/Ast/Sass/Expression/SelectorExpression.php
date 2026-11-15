<?php

/**
 * SCSSPHP
 *
 * @copyright 2012-2020 Leaf Corcoran
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @link http://scssphp.github.io/scssphp
 */

namespace KunenaScssPhp\ScssPhp\Ast\Sass\Expression;

use KunenaScssPhp\ScssPhp\Ast\Sass\Expression;
use KunenaScssPhp\ScssPhp\Visitor\ExpressionVisitor;
use KunenaScssPhp\SourceSpan\FileSpan;

/**
 * A parent selector reference, `&`.
 *
 * @internal
 */
final class SelectorExpression implements Expression
{
    private readonly FileSpan $span;

    public function __construct(FileSpan $span)
    {
        $this->span = $span;
    }

    public function getSpan(): FileSpan
    {
        return $this->span;
    }

    public function accept(ExpressionVisitor $visitor)
    {
        return $visitor->visitSelectorExpression($this);
    }

    public function __toString(): string
    {
        return '&';
    }
}

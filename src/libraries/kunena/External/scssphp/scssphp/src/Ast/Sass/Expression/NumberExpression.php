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
use KunenaScssPhp\ScssPhp\Value\SassNumber;
use KunenaScssPhp\ScssPhp\Visitor\ExpressionVisitor;
use KunenaScssPhp\SourceSpan\FileSpan;

/**
 * A number literal.
 *
 * @internal
 */
final class NumberExpression implements Expression
{
    private readonly float $value;

    private readonly FileSpan $span;

    private readonly ?string $unit;

    public function __construct(float $value, FileSpan $span, ?string $unit = null)
    {
        $this->value = $value;
        $this->span = $span;
        $this->unit = $unit;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getSpan(): FileSpan
    {
        return $this->span;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function accept(ExpressionVisitor $visitor)
    {
        return $visitor->visitNumberExpression($this);
    }

    public function __toString(): string
    {
        return (string) SassNumber::create($this->value, $this->unit);
    }
}

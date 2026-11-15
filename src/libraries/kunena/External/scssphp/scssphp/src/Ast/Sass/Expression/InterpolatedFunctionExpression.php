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

use KunenaScssPhp\ScssPhp\Ast\Sass\ArgumentInvocation;
use KunenaScssPhp\ScssPhp\Ast\Sass\CallableInvocation;
use KunenaScssPhp\ScssPhp\Ast\Sass\Expression;
use KunenaScssPhp\ScssPhp\Ast\Sass\Interpolation;
use KunenaScssPhp\ScssPhp\Visitor\ExpressionVisitor;
use KunenaScssPhp\SourceSpan\FileSpan;

/**
 * An interpolated function invocation.
 *
 * This is always a plain CSS function.
 *
 * @internal
 */
final class InterpolatedFunctionExpression implements Expression, CallableInvocation
{
    /**
     * The name of the function being invoked.
     */
    private readonly Interpolation $name;

    /**
     * The arguments to pass to the function.
     */
    private readonly ArgumentInvocation $arguments;

    private readonly FileSpan $span;

    public function __construct(Interpolation $name, ArgumentInvocation $arguments, FileSpan $span)
    {
        $this->span = $span;
        $this->name = $name;
        $this->arguments = $arguments;
    }

    public function getName(): Interpolation
    {
        return $this->name;
    }

    public function getArguments(): ArgumentInvocation
    {
        return $this->arguments;
    }

    public function getSpan(): FileSpan
    {
        return $this->span;
    }

    public function accept(ExpressionVisitor $visitor)
    {
        return $visitor->visitInterpolatedFunctionExpression($this);
    }

    public function __toString(): string
    {
        return $this->name . $this->arguments;
    }
}

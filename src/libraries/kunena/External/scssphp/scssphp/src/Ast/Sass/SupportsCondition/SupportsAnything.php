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

namespace KunenaScssPhp\ScssPhp\Ast\Sass\SupportsCondition;

use KunenaScssPhp\ScssPhp\Ast\Sass\Interpolation;
use KunenaScssPhp\ScssPhp\Ast\Sass\SupportsCondition;
use KunenaScssPhp\SourceSpan\FileSpan;

/**
 * A supports condition that represents the forwards-compatible
 * `<general-enclosed>` production.
 *
 * @internal
 */
final class SupportsAnything implements SupportsCondition
{
    /**
     * The contents of the condition.
     */
    private readonly Interpolation $contents;

    private readonly FileSpan $span;

    public function __construct(Interpolation $contents, FileSpan $span)
    {
        $this->contents = $contents;
        $this->span = $span;
    }

    public function getContents(): Interpolation
    {
        return $this->contents;
    }

    public function getSpan(): FileSpan
    {
        return $this->span;
    }

    public function __toString(): string
    {
        return "($this->contents)";
    }
}

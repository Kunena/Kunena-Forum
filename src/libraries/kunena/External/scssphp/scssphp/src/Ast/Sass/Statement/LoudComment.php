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

namespace KunenaScssPhp\ScssPhp\Ast\Sass\Statement;

use KunenaScssPhp\ScssPhp\Ast\Sass\Interpolation;
use KunenaScssPhp\ScssPhp\Ast\Sass\Statement;
use KunenaScssPhp\ScssPhp\Visitor\StatementVisitor;
use KunenaScssPhp\SourceSpan\FileSpan;

/**
 * A loud CSS-style comment.
 *
 * @internal
 */
final class LoudComment implements Statement
{
    private readonly Interpolation $text;

    public function __construct(Interpolation $text)
    {
        $this->text = $text;
    }

    public function getText(): Interpolation
    {
        return $this->text;
    }

    public function getSpan(): FileSpan
    {
        return $this->text->getSpan();
    }

    public function accept(StatementVisitor $visitor)
    {
        return $visitor->visitLoudComment($this);
    }

    public function __toString(): string
    {
        return (string) $this->text;
    }
}

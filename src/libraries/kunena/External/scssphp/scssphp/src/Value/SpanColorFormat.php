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

namespace KunenaScssPhp\ScssPhp\Value;

use KunenaScssPhp\SourceSpan\FileSpan;

/**
 * @internal
 */
final class SpanColorFormat implements ColorFormat
{
    private readonly FileSpan $span;

    public function __construct(FileSpan $span)
    {
        $this->span = $span;
    }

    public function getOriginal(): string
    {
        return $this->span->getText();
    }
}

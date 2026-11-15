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

namespace KunenaScssPhp\ScssPhp\Serializer;

use KunenaScssPhp\ScssPhp\SourceMap\SingleMapping;

/**
 * The result of converting a CSS AST to CSS text.
 *
 * @internal
 */
final class SerializeResult
{
    public function __construct(
        public readonly string $css,
        public readonly ?SingleMapping $mapping,
    ) {
    }
}

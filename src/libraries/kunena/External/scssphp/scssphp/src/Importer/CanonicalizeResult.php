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

namespace KunenaScssPhp\ScssPhp\Importer;

use KunenaScssPhp\League\Uri\Contracts\UriInterface;

/**
 * @internal
 */
final class CanonicalizeResult
{
    public function __construct(
        public readonly Importer $importer,
        public readonly UriInterface $canonicalUrl,
        public readonly UriInterface $originalUrl,
    ) {
    }
}

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

namespace KunenaScssPhp\ScssPhp\Logger;

use KunenaScssPhp\ScssPhp\Deprecation;
use KunenaScssPhp\ScssPhp\StackTrace\Trace;
use KunenaScssPhp\SourceSpan\FileSpan;
use KunenaScssPhp\SourceSpan\SourceSpan;

/**
 * A logger that silently ignores all messages.
 */
final class QuietLogger implements LoggerInterface
{
    public function warn(string $message, ?Deprecation $deprecation = null, ?FileSpan $span = null, ?Trace $trace = null): void
    {
    }

    public function debug(string $message, SourceSpan $span): void
    {
    }
}

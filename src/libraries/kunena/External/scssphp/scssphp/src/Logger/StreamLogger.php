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
use KunenaScssPhp\ScssPhp\Util;
use KunenaScssPhp\ScssPhp\Util\Path;
use KunenaScssPhp\SourceSpan\FileSpan;
use KunenaScssPhp\SourceSpan\SourceSpan;

/**
 * A logger that prints to a PHP stream (for instance stderr)
 */
final class StreamLogger implements LoggerInterface
{
    /**
     * @var resource
     */
    private $stream;
    private bool $closeOnDestruct;

    /**
     * @param resource $stream          A stream resource
     * @param bool     $closeOnDestruct If true, takes ownership of the stream and close it on destruct to avoid leaks.
     */
    public function __construct($stream, bool $closeOnDestruct = false)
    {
        $this->stream = $stream;
        $this->closeOnDestruct = $closeOnDestruct;
    }

    /**
     * @internal
     */
    public function __destruct()
    {
        if ($this->closeOnDestruct) {
            fclose($this->stream);
        }
    }

    public function warn(string $message, ?Deprecation $deprecation = null, ?FileSpan $span = null, ?Trace $trace = null): void
    {
        $prefix = ($deprecation !== null ? 'DEPRECATION ' : '') . 'WARNING';

        if ($span === null) {
            $formattedMessage = ': ' . $message;
        } elseif ($trace !== null) {
            // If there's a span and a trace, the span's location information is
            // probably duplicated in the trace, so we just use it for highlighting.
            $formattedMessage = ': ' . $message . "\n\n" . $span->highlight();
        } else {
            $formattedMessage = ' on ' . $span->message("\n" . $message);
        }

        if ($trace !== null) {
            $formattedMessage .= "\n" . Util::indent(rtrim($trace->getFormattedTrace()), 4);
        }

        fwrite($this->stream, $prefix . $formattedMessage . "\n\n");
    }

    public function debug(string $message, SourceSpan $span): void
    {
        $url = $span->getStart()->getSourceUrl() === null ? '-' : Path::prettyUri($span->getStart()->getSourceUrl());
        $line = $span->getStart()->getLine() + 1;
        $location = "$url:$line ";

        fwrite($this->stream, \sprintf("%sDEBUG: %s", $location, $message) . "\n");
    }
}

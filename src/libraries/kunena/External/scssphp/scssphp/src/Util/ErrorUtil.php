<?php

/**
 * SCSSPHP
 *
 * @copyright 2018-2020 Anthon Pang
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @link http://scssphp.github.io/scssphp
 */

namespace KunenaScssPhp\ScssPhp\Util;

use KunenaScssPhp\ScssPhp\StackTrace\Trace;
use KunenaScssPhp\SourceSpan\FileSpan;

/**
 * @internal
 */
final class ErrorUtil
{
    /**
     * @throws \OutOfRangeException
     */
    public static function checkIntInInterval(int $value, int $minValue, int $maxValue, ?string $name = null): void
    {
        if ($value < $minValue || $value > $maxValue) {
            $nameDisplay = $name ? " $name" : '';

            throw new \OutOfRangeException("Invalid value:$nameDisplay must be between $minValue and $maxValue: $value.");
        }
    }

    public static function formatErrorMessage(string $message, FileSpan $span, Trace $sassTrace): string
    {
        $formattedMessage = $message . "\n" . $span->highlight();

        foreach (explode("\n", $sassTrace->getFormattedTrace()) as $frame) {
            if ($frame === '') {
                continue;
            }
            $formattedMessage .= "\n";
            $formattedMessage .= '  ' . $frame;
        }

        return $formattedMessage;
    }

    /**
     * @param array<string, FileSpan> $secondarySpans
     */
    public static function formatErrorMessageMultiple(string $message, FileSpan $span, string $primaryLabel, array $secondarySpans, Trace $sassTrace): string
    {
        $formattedMessage = $message . "\n" . $span->highlightMultiple($primaryLabel, $secondarySpans);

        foreach (explode("\n", $sassTrace->getFormattedTrace()) as $frame) {
            if ($frame === '') {
                continue;
            }
            $formattedMessage .= "\n";
            $formattedMessage .= '  ' . $frame;
        }

        return $formattedMessage;
    }
}

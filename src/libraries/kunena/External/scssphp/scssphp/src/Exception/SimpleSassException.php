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

namespace KunenaScssPhp\ScssPhp\Exception;

use KunenaScssPhp\ScssPhp\StackTrace\Trace;
use KunenaScssPhp\ScssPhp\Util;
use KunenaScssPhp\ScssPhp\Util\ErrorUtil;
use KunenaScssPhp\SourceSpan\FileSpan;

/**
 * @internal
 */
final class SimpleSassException extends \Exception implements SassException
{
    private readonly string $originalMessage;

    private readonly FileSpan $span;

    public function __construct(string $message, FileSpan $span, ?\Throwable $previous = null)
    {
        $this->originalMessage = $message;
        $this->span = $span;

        parent::__construct(ErrorUtil::formatErrorMessage($message, $span, $this->getSassTrace()), 0, $previous);
    }

    public function getOriginalMessage(): string
    {
        return $this->originalMessage;
    }

    public function getSpan(): FileSpan
    {
        return $this->span;
    }

    public function getSassTrace(): Trace
    {
        return new Trace([Util::frameForSpan($this->span, 'root stylesheet')]);
    }

    public function withAdditionalSpan(FileSpan $span, string $label, ?\Throwable $previous = null): MultiSpanSassException
    {
        return new MultiSpanSassException($this->originalMessage, $this->span, '', [$label => $span], $previous);
    }

    public function withTrace(Trace $trace, ?\Throwable $previous = null): SassRuntimeException
    {
        return new SimpleSassRuntimeException($this->originalMessage, $this->span, $trace, $previous);
    }
}

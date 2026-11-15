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

namespace KunenaScssPhp\ScssPhp\Ast\Css;

use KunenaScssPhp\ScssPhp\Ast\Selector\SelectorList;
use KunenaScssPhp\ScssPhp\Util\Box;
use KunenaScssPhp\ScssPhp\Util\EquatableUtil;
use KunenaScssPhp\ScssPhp\Visitor\ModifiableCssVisitor;
use KunenaScssPhp\SourceSpan\FileSpan;

/**
 * A modifiable version of {@see CssStyleRule} for use in the evaluation step.
 *
 * @internal
 */
final class ModifiableCssStyleRule extends ModifiableCssParentNode implements CssStyleRule
{
    /**
     * A reference to the modifiable selector list provided by the extension
     * store, which may update it over time as new extensions are applied.
     *
     * @var Box<SelectorList>
     */
    private readonly Box $selector;

    private readonly SelectorList $originalSelector;

    private readonly FileSpan $span;

    private readonly bool $fromPlainCss;

    /**
     * @param Box<SelectorList> $selector
     */
    public function __construct(Box $selector, FileSpan $span, ?SelectorList $originalSelector = null, bool $fromPlainCss = false)
    {
        parent::__construct();
        $this->selector = $selector;
        $this->originalSelector = $originalSelector ?? $selector->getValue();
        $this->span = $span;
        $this->fromPlainCss = $fromPlainCss;
    }

    public function getSelector(): SelectorList
    {
        return $this->selector->getValue();
    }

    public function getOriginalSelector(): SelectorList
    {
        return $this->originalSelector;
    }

    public function isFromPlainCss(): bool
    {
        return $this->fromPlainCss;
    }

    public function getSpan(): FileSpan
    {
        return $this->span;
    }

    public function accept(ModifiableCssVisitor $visitor)
    {
        return $visitor->visitCssStyleRule($this);
    }

    public function equalsIgnoringChildren(ModifiableCssNode $other): bool
    {
        return $other instanceof ModifiableCssStyleRule && EquatableUtil::equals($this->selector, $other->selector);
    }

    public function copyWithoutChildren(): ModifiableCssStyleRule
    {
        return new ModifiableCssStyleRule($this->selector, $this->span, $this->originalSelector);
    }
}

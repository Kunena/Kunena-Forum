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

namespace KunenaScssPhp\ScssPhp\Visitor;

use KunenaScssPhp\ScssPhp\Ast\Selector\AttributeSelector;
use KunenaScssPhp\ScssPhp\Ast\Selector\ClassSelector;
use KunenaScssPhp\ScssPhp\Ast\Selector\ComplexSelector;
use KunenaScssPhp\ScssPhp\Ast\Selector\ComplexSelectorComponent;
use KunenaScssPhp\ScssPhp\Ast\Selector\CompoundSelector;
use KunenaScssPhp\ScssPhp\Ast\Selector\IDSelector;
use KunenaScssPhp\ScssPhp\Ast\Selector\ParentSelector;
use KunenaScssPhp\ScssPhp\Ast\Selector\PlaceholderSelector;
use KunenaScssPhp\ScssPhp\Ast\Selector\PseudoSelector;
use KunenaScssPhp\ScssPhp\Ast\Selector\SelectorList;
use KunenaScssPhp\ScssPhp\Ast\Selector\SimpleSelector;
use KunenaScssPhp\ScssPhp\Ast\Selector\TypeSelector;
use KunenaScssPhp\ScssPhp\Ast\Selector\UniversalSelector;
use KunenaScssPhp\ScssPhp\Util\IterableUtil;

/**
 * A visitor that visits each selector in a Sass selector AST and returns
 * `true` if any of the individual methods return `true`.
 *
 * Each method returns `false` by default.
 *
 * @template-implements SelectorVisitor<bool>
 * @internal
 */
abstract class AnySelectorVisitor implements SelectorVisitor
{
    public function visitComplexSelector(ComplexSelector $complex): bool
    {
        return IterableUtil::any($complex->getComponents(), fn (ComplexSelectorComponent $component) => $this->visitCompoundSelector($component->getSelector()));
    }

    public function visitCompoundSelector(CompoundSelector $compound): bool
    {
        return IterableUtil::any($compound->getComponents(), fn (SimpleSelector $simple) => $simple->accept($this));
    }

    public function visitPseudoSelector(PseudoSelector $pseudo): bool
    {
        $selector = $pseudo->getSelector();

        return $selector === null ? false : $selector->accept($this);
    }

    public function visitSelectorList(SelectorList $list): bool
    {
        return IterableUtil::any($list->getComponents(), $this->visitComplexSelector(...));
    }

    public function visitAttributeSelector(AttributeSelector $attribute): bool
    {
        return false;
    }

    public function visitClassSelector(ClassSelector $klass): bool
    {
        return false;
    }

    public function visitIDSelector(IDSelector $id): bool
    {
        return false;
    }

    public function visitParentSelector(ParentSelector $parent): bool
    {
        return false;
    }

    public function visitPlaceholderSelector(PlaceholderSelector $placeholder): bool
    {
        return false;
    }

    public function visitTypeSelector(TypeSelector $type): bool
    {
        return false;
    }

    public function visitUniversalSelector(UniversalSelector $universal): bool
    {
        return false;
    }
}

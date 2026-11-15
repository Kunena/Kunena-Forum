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
use KunenaScssPhp\ScssPhp\Ast\Selector\CompoundSelector;
use KunenaScssPhp\ScssPhp\Ast\Selector\IDSelector;
use KunenaScssPhp\ScssPhp\Ast\Selector\ParentSelector;
use KunenaScssPhp\ScssPhp\Ast\Selector\PlaceholderSelector;
use KunenaScssPhp\ScssPhp\Ast\Selector\PseudoSelector;
use KunenaScssPhp\ScssPhp\Ast\Selector\SelectorList;
use KunenaScssPhp\ScssPhp\Ast\Selector\TypeSelector;
use KunenaScssPhp\ScssPhp\Ast\Selector\UniversalSelector;

/**
 * An interface for visitors that traverse selectors.
 *
 * @internal
 *
 * @template T
 */
interface SelectorVisitor
{
    /**
     * @return T
     */
    public function visitAttributeSelector(AttributeSelector $attribute);

    /**
     * @return T
     */
    public function visitClassSelector(ClassSelector $klass);

    /**
     * @return T
     */
    public function visitComplexSelector(ComplexSelector $complex);

    /**
     * @return T
     */
    public function visitCompoundSelector(CompoundSelector $compound);

    /**
     * @return T
     */
    public function visitIDSelector(IDSelector $id);

    /**
     * @return T
     */
    public function visitParentSelector(ParentSelector $parent);

    /**
     * @return T
     */
    public function visitPlaceholderSelector(PlaceholderSelector $placeholder);

    /**
     * @return T
     */
    public function visitPseudoSelector(PseudoSelector $pseudo);

    /**
     * @return T
     */
    public function visitSelectorList(SelectorList $list);

    /**
     * @return T
     */
    public function visitTypeSelector(TypeSelector $type);

    /**
     * @return T
     */
    public function visitUniversalSelector(UniversalSelector $universal);
}

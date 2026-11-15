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

use KunenaScssPhp\ScssPhp\Ast\Css\CssAtRule;
use KunenaScssPhp\ScssPhp\Ast\Css\CssComment;
use KunenaScssPhp\ScssPhp\Ast\Css\CssDeclaration;
use KunenaScssPhp\ScssPhp\Ast\Css\CssImport;
use KunenaScssPhp\ScssPhp\Ast\Css\CssKeyframeBlock;
use KunenaScssPhp\ScssPhp\Ast\Css\CssMediaRule;
use KunenaScssPhp\ScssPhp\Ast\Css\CssStyleRule;
use KunenaScssPhp\ScssPhp\Ast\Css\CssStylesheet;
use KunenaScssPhp\ScssPhp\Ast\Css\CssSupportsRule;

/**
 * An interface for visitors that traverse CSS statements.
 *
 * @internal
 *
 * @template T
 * @template-extends ModifiableCssVisitor<T>
 */
interface CssVisitor extends ModifiableCssVisitor
{
    /**
     * @return T
     */
    public function visitCssAtRule(CssAtRule $node);

    /**
     * @return T
     */
    public function visitCssComment(CssComment $node);

    /**
     * @return T
     */
    public function visitCssDeclaration(CssDeclaration $node);

    /**
     * @return T
     */
    public function visitCssImport(CssImport $node);

    /**
     * @return T
     */
    public function visitCssKeyframeBlock(CssKeyframeBlock $node);

    /**
     * @return T
     */
    public function visitCssMediaRule(CssMediaRule $node);

    /**
     * @return T
     */
    public function visitCssStyleRule(CssStyleRule $node);

    /**
     * @return T
     */
    public function visitCssStylesheet(CssStylesheet $node);

    /**
     * @return T
     */
    public function visitCssSupportsRule(CssSupportsRule $node);
}

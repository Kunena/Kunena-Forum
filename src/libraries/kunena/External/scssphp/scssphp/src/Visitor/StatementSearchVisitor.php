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

use KunenaScssPhp\ScssPhp\Ast\Sass\Statement;
use KunenaScssPhp\ScssPhp\Ast\Sass\Statement\AtRootRule;
use KunenaScssPhp\ScssPhp\Ast\Sass\Statement\AtRule;
use KunenaScssPhp\ScssPhp\Ast\Sass\Statement\CallableDeclaration;
use KunenaScssPhp\ScssPhp\Ast\Sass\Statement\ContentBlock;
use KunenaScssPhp\ScssPhp\Ast\Sass\Statement\ContentRule;
use KunenaScssPhp\ScssPhp\Ast\Sass\Statement\DebugRule;
use KunenaScssPhp\ScssPhp\Ast\Sass\Statement\Declaration;
use KunenaScssPhp\ScssPhp\Ast\Sass\Statement\EachRule;
use KunenaScssPhp\ScssPhp\Ast\Sass\Statement\ErrorRule;
use KunenaScssPhp\ScssPhp\Ast\Sass\Statement\ExtendRule;
use KunenaScssPhp\ScssPhp\Ast\Sass\Statement\ForRule;
use KunenaScssPhp\ScssPhp\Ast\Sass\Statement\FunctionRule;
use KunenaScssPhp\ScssPhp\Ast\Sass\Statement\IfClause;
use KunenaScssPhp\ScssPhp\Ast\Sass\Statement\IfRule;
use KunenaScssPhp\ScssPhp\Ast\Sass\Statement\ImportRule;
use KunenaScssPhp\ScssPhp\Ast\Sass\Statement\IncludeRule;
use KunenaScssPhp\ScssPhp\Ast\Sass\Statement\LoudComment;
use KunenaScssPhp\ScssPhp\Ast\Sass\Statement\MediaRule;
use KunenaScssPhp\ScssPhp\Ast\Sass\Statement\MixinRule;
use KunenaScssPhp\ScssPhp\Ast\Sass\Statement\ParentStatement;
use KunenaScssPhp\ScssPhp\Ast\Sass\Statement\ReturnRule;
use KunenaScssPhp\ScssPhp\Ast\Sass\Statement\SilentComment;
use KunenaScssPhp\ScssPhp\Ast\Sass\Statement\StyleRule;
use KunenaScssPhp\ScssPhp\Ast\Sass\Statement\Stylesheet;
use KunenaScssPhp\ScssPhp\Ast\Sass\Statement\SupportsRule;
use KunenaScssPhp\ScssPhp\Ast\Sass\Statement\VariableDeclaration;
use KunenaScssPhp\ScssPhp\Ast\Sass\Statement\WarnRule;
use KunenaScssPhp\ScssPhp\Ast\Sass\Statement\WhileRule;
use KunenaScssPhp\ScssPhp\Util\IterableUtil;

/**
 * A StatementVisitor whose `visit*` methods default to returning `null`, but
 * which returns the first non-`null` value returned by any method.
 *
 * This can be extended to find the first instance of particular nodes in the
 * AST.
 *
 * @internal
 *
 * @template T
 * @template-implements StatementVisitor<T|null>
 */
abstract class StatementSearchVisitor implements StatementVisitor
{
    public function visitAtRootRule(AtRootRule $node)
    {
        return $this->visitChildren($node->getChildren());
    }

    public function visitAtRule(AtRule $node)
    {
        if ($node->getChildren() !== null) {
            return $this->visitChildren($node->getChildren());
        }

        return null;
    }

    public function visitContentBlock(ContentBlock $node)
    {
        return $this->visitCallableDeclaration($node);
    }

    public function visitContentRule(ContentRule $node)
    {
        return null;
    }

    public function visitDebugRule(DebugRule $node)
    {
        return null;
    }

    public function visitDeclaration(Declaration $node)
    {
        if ($node->getChildren() !== null) {
            return $this->visitChildren($node->getChildren());
        }

        return null;
    }

    public function visitEachRule(EachRule $node)
    {
        return $this->visitChildren($node->getChildren());
    }

    public function visitErrorRule(ErrorRule $node)
    {
        return null;
    }

    public function visitExtendRule(ExtendRule $node)
    {
        return null;
    }

    public function visitForRule(ForRule $node)
    {
        return $this->visitChildren($node->getChildren());
    }

    public function visitFunctionRule(FunctionRule $node)
    {
        return $this->visitCallableDeclaration($node);
    }

    public function visitIfRule(IfRule $node)
    {
        $value = IterableUtil::search($node->getClauses(), fn(IfClause $clause) => IterableUtil::search($clause->getChildren(), fn(Statement $child) => $child->accept($this)));

        if ($node->getLastClause() !== null) {
            $value ??= IterableUtil::search($node->getLastClause()->getChildren(), fn(Statement $child) => $child->accept($this));
        }

        return $value;
    }

    public function visitImportRule(ImportRule $node)
    {
        return null;
    }

    public function visitIncludeRule(IncludeRule $node)
    {
        if ($node->getContent() !== null) {
            return $this->visitContentBlock($node->getContent());
        }

        return null;
    }

    public function visitLoudComment(LoudComment $node)
    {
        return null;
    }

    public function visitMediaRule(MediaRule $node)
    {
        return $this->visitChildren($node->getChildren());
    }

    public function visitMixinRule(MixinRule $node)
    {
        return $this->visitCallableDeclaration($node);
    }

    public function visitReturnRule(ReturnRule $node)
    {
        return null;
    }

    public function visitSilentComment(SilentComment $node)
    {
        return null;
    }

    public function visitStyleRule(StyleRule $node)
    {
        return $this->visitChildren($node->getChildren());
    }

    public function visitStylesheet(Stylesheet $node)
    {
        return $this->visitChildren($node->getChildren());
    }

    public function visitSupportsRule(SupportsRule $node)
    {
        return $this->visitChildren($node->getChildren());
    }

    public function visitVariableDeclaration(VariableDeclaration $node)
    {
        return null;
    }

    public function visitWarnRule(WarnRule $node)
    {
        return null;
    }

    public function visitWhileRule(WhileRule $node)
    {
        return $this->visitChildren($node->getChildren());
    }

    /**
     * Visits each of $node's expressions and children.
     *
     * The default implementations of {@see visitFunctionRule} and {@see visitMixinRule}
     * call this.
     *
     * @return T|null
     */
    protected function visitCallableDeclaration(CallableDeclaration $node)
    {
        return $this->visitChildren($node->getChildren());
    }

    /**
     * Visits each child in $children.
     *
     * The default implementation of the visit methods for all {@see ParentStatement}s
     * call this.
     *
     * @param Statement[] $children
     *
     * @return T|null
     */
    protected function visitChildren(array $children)
    {
        return IterableUtil::search($children, fn (Statement $child) => $child->accept($this));
    }
}

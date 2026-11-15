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

namespace KunenaScssPhp\ScssPhp\Ast\Sass;

use KunenaScssPhp\ScssPhp\Visitor\StatementVisitor;

/**
 * A statement in a Sass syntax tree.
 *
 * @internal
 */
interface Statement extends SassNode
{
    /**
     * @template T
     * @param StatementVisitor<T> $visitor
     * @return T
     */
    public function accept(StatementVisitor $visitor);
}

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

namespace KunenaScssPhp\ScssPhp\Block;

use KunenaScssPhp\ScssPhp\Block;
use KunenaScssPhp\ScssPhp\Type;

/**
 * @internal
 */
class AtRootBlock extends Block
{
    /**
     * @var array|null
     */
    public $selector;

    /**
     * @var array|null
     */
    public $with;

    public function __construct()
    {
        $this->type = Type::T_AT_ROOT;
    }
}

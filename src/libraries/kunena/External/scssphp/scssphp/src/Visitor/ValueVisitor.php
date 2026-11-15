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

use KunenaScssPhp\ScssPhp\Value\SassBoolean;
use KunenaScssPhp\ScssPhp\Value\SassCalculation;
use KunenaScssPhp\ScssPhp\Value\SassColor;
use KunenaScssPhp\ScssPhp\Value\SassFunction;
use KunenaScssPhp\ScssPhp\Value\SassList;
use KunenaScssPhp\ScssPhp\Value\SassMap;
use KunenaScssPhp\ScssPhp\Value\SassMixin;
use KunenaScssPhp\ScssPhp\Value\SassNumber;
use KunenaScssPhp\ScssPhp\Value\SassString;

/**
 * An interface for visitors that traverse SassScript $values.
 *
 * @internal
 *
 * @template T
 */
interface ValueVisitor
{
    /**
     * @return T
     */
    public function visitBoolean(SassBoolean $value);

    /**
     * @return T
     */
    public function visitCalculation(SassCalculation $value);

    /**
     * @return T
     */
    public function visitColor(SassColor $value);

    /**
     * @return T
     */
    public function visitFunction(SassFunction $value);

    /**
     * @return T
     */
    public function visitMixin(SassMixin $value);

    /**
     * @return T
     */
    public function visitList(SassList $value);

    /**
     * @return T
     */
    public function visitMap(SassMap $value);

    /**
     * @return T
     */
    public function visitNull();

    /**
     * @return T
     */
    public function visitNumber(SassNumber $value);

    /**
     * @return T
     */
    public function visitString(SassString $value);
}

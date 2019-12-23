<?php
/**
 * Kunena Component
 * @package Kunena.Build
 *
 * @copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

/**
 * WARNING: It is better to install the build tool PHING through
 * PEAR.  PEAR auto creates some environment variables
 * to run the build tool using the $ phing command versus
 * having to do $ php build.php command.  Composer also distributes
 * extra needed dependencies to be able to run. We have provided a
 * composer file as an alternate solution to be able to install
 * and use the build tool for those that are familiar composer,
 * however it is recommended to use the PEAR package instead.
 **/
require_once "vendor/bin/phing";

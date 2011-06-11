<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div id="Kunena">
<?php
$this->displayMenu ();
$this->displayLoginBox ();
include dirname ( __FILE__ ) . '/vote_embed.php';
$this->displayFooter ();
?>
</div>
<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.BBCode
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

// [terminal colortext="#ffffff"]root@domain.com:~/www/components/com_kunena$[/terminal]

// Display text in terminal window.

$colortext = isset($this->params['colortext']) ? $this->params['colortext'] : '#ffffff';
?>
<pre style="font-family:monospace;background-color:#444444;color:<?php echo $colortext; ?>;"><?php echo $this->content; ?></pre>

<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.BBCode
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
**/

namespace Kunena\Forum\Site;

defined('_JEXEC') or die();

use function defined;

// [terminal colortext="#ffffff"]root@domain.com:~/www/components/com_kunena$[/terminal]

// Display text in terminal window.

$colortext = isset($this->params['colortext']) ? $this->params['colortext'] : '#ffffff';
?>
<pre style="font-family:monospace;background-color:#444444;color:<?php echo $colortext; ?>;"><?php echo $this->content; ?></pre>

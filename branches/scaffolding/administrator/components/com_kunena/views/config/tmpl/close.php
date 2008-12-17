<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	(C) 2008 Kunena. All rights reserved, see COPYRIGHT.php
 * @license		GNU General Public License, see LICENSE.php
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die('Invalid Request.');

JHTML::_('behavior.mootools');
?>

<script type="text/javascript">
	window.addEvent('domready', function(){ window.parent.document.getElementById('sbox-window').close(); });
</script>
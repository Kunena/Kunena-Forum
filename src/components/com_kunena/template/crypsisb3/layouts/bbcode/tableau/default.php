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

// [tableau height=1000]http://public.tableausoftware.com/views/WorldCup/WorldCupWinningPercentages?:embed=y&:toolbar=yes[/tableau]

// Display visual analytics from http://www.tableausoftware.com/
?>
<script type="text/javascript" src="<?php echo $this->server; ?>/javascripts/api/viz_v1.js"></script>
<object class="tableauViz" width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>" style="display:none;">
	<param name="name" value="<?php echo $this->content; ?>" />
	<param name="toolbar" value="<?php echo $this->toolbar; ?>" />
</object>

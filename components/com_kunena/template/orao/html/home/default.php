<?php
/**
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="forumlist">
	<div class="inner">
		<span class="corners-top"><span></span></span>
			<ul class="topiclist">
				<li class="header">
					<dl class="icon">
						<dt><?php echo $this->escape($this->header); ?></dt>
						<dd>&nbsp;</dd>
					</dl>
				</li>
			</ul>
			<div class="kdetailsbox">
				<div class="kcontent">
				<?php
				if (!empty($this->html)) :
					echo '123';
				else :
					echo KunenaHtmlParser::parseBBCode($this->body);
				endif; ?>
				</div>
				<div class="clr"></div>
			</div>
		<span class="corners-bottom"><span></span></span>
	</div>
</div>
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
<?php foreach ($this->top as $top) : ?>
<div class="forumlist">
	<div class="catinner">
		<span class="corners-top"><span></span></span>
			<ul class="topiclist">
				<li class="header">
					<dl class="icon frontstats">
						<dt><span class="ktitle"><?php echo $top[0]->title ?></span></dt>
						<dd class="tk-toggler"><a class="ktoggler close" rel="kpopuserhitstats_tbody"></a></dd>
					</dl>
				</li>
			</ul>
			<ul id="kpopuserhitstats_tbody" class="topiclist forums tk-stats-body">
			<?php foreach ($top as $id=>$item) : ?>
				<li class="row">
					<dl class="icon">
						<dt>
							<?php echo $item->link ?>
						</dt>
						<dd class="tk-stats-bar">
							<div class="tk-progressbar tk-widget tk-widget-content tk-corner-all">
								<span><?php echo $item->count ?> <?php echo $top[0]->titleCount ?></span>
								<div style="width: <?php echo $item->percent ?>%;" class="tk-progressbar-value tk-widget-header tk-corner-left">
									<span><?php echo $item->count ?> <?php echo $top[0]->titleCount ?></span>
								</div>
							</div>
						</dd>
					</dl>
				</li>
			<?php endforeach ?>
			</ul>
		<span class="corners-bottom"><span></span></span>
	</div>
</div>
<?php endforeach ?>
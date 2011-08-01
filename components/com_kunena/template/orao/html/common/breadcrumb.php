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
$template = KunenaFactory::getTemplate();
$this->params = $template->params;
$item = array_shift($this->pathway);
?>
<?php if ($item && $this->params->get('showPathway') == '1') : ?>
<div class="forumlist tk-forum-action tk-clear">
	<div class="catinner">
		<span class="corners-top"><span></span></span>
			<ul class="topiclist forums">
				<li class="rowfull tk-fixed">
					<dl class="icon">
						<dt></dt>
						<dd class="tk-thread-action" style="width:100%">

			<ul class="kbreadcrumb-path">
				<li style="display:inline;"><a href="<?php echo $item->link ?>"><?php echo $item->name ?></a></li>
				<?php foreach ($this->pathway as $item) : ?>
				<li style="display:inline;"> &raquo; <a href="<?php echo $item->link ?>"><?php echo $item->name ?></a></li>
				<?php endforeach ?>
			</ul>

						</dd>
					</dl>
				</li>
			</ul>
		<span class="corners-bottom"><span></span></span>
	</div>
</div>
<?php endif ?>
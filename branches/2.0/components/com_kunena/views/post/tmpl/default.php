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


?>
<div id="bbcode-editor">
	<ul class="toolbar"></ul>
	<span class="help"></span>
	<br class="clear" />
	<textarea id="comment_body" name="body" class="editor inputbox required" tabindex="5" rows="8" cols="60" style="font-size:12px;"></textarea>

	<ul class="emoticon-palette">
<?php foreach ($emoticons as $emoticon) : ?>
		<li>
			<img class="emoticon-palette" src="<?php echo $emoticon->path; ?>" alt="<?php echo $emoticon->code; ?>" title="<?php echo $emoticon->name; ?>" />
		</li>
<?php endforeach; ?>
	</ul>
	<br class="clear" />
</div>

<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Topic
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/

defined('_JEXEC') or die ();
$this->addStyleSheet('assets/css/rating.css');

?>

<?php if ($this->category->allow_ratings && $this->config->ratingenabled): ?>
<input id="topic_id" type="hidden" value="<?php echo $this->topicid ?>" />
<input type="hidden" id="krating_submit_url" name="url" value="<?php echo 'index.php?option=com_kunena&view=topic&layout=rate&topic_id=' . $this->topicid . '&format=raw'; ?>" />
<div id="krating"></div>
<?php endif; ?>

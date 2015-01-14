<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Topic
 *
 * @copyright   (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/** @var KunenaForumTopic $topic */
$topic = $this->topic;
$me = KunenaUserHelper::getMyself();

$this->addScriptDeclaration('// <![CDATA[
var kunena_anonymous_name = "'.JText::_('COM_KUNENA_USERNAME_ANONYMOUS').'";
// ]]>');

$config = KunenaFactory::getConfig();

// If polls are enabled, load also poll JavaScript.
if ($config->pollenabled == 1)
{
	JText::script('COM_KUNENA_POLL_OPTION_NAME');
	JText::script('COM_KUNENA_EDITOR_HELPLINE_OPTION');
	$this->addScript('poll.js');
}

// Load FancyBox library if enabled in configuration
if ($config->lightbox == 1)
{
	$template = KunenaTemplate::getInstance();
	if ( $template->params->get('lightboxColor') == 'white') {
		$this->addStyleSheet('css/fancybox-white.css');
	}
	else  {
		$this->addStyleSheet('css/fancybox-black.css');
	}
	$this->addScript('js/fancybox.js');
	JFactory::getDocument()->addScriptDeclaration('
				jQuery(document).ready(function() {
					jQuery(".fancybox-button").fancybox({
						prevEffect		: \'none\',
						nextEffect		: \'none\',
						closeBtn		:  true,
						helpers		: {
							title	: { type : \'inside\' },
							buttons	: {}
						}
					});
				});
			');
}
// Load caret.js always before atwho.js script and use it for autocomplete, emojiis...
$this->addScript('js/caret.js');
$this->addScript('js/atwho.js');
$this->addStyleSheet('css/atwho.css');
?>
<?php if ($this->category->headerdesc) : ?>
<div class="alert alert-info">
	<a class="close" data-dismiss="alert" href="#">&times;</a>
	<?php echo $this->category->displayField('headerdesc'); ?>
</div>
<?php endif; ?>

<h3>
	<?php echo $topic->getIcon(); ?>
	<?php echo $topic->displayField('subject'); ?>
</h3>

<div class="pull-left">
	<?php echo $this->subLayout('Widget/Pagination/List')
		->set('pagination', $this->pagination)
		->set('display', true); ?>
</div>
<div class="pull-right">
	<?php echo $this->subLayout('Widget/Search')
		->set('id', $topic->id)
		->set('title', JText::_('COM_KUNENA_SEARCH_TOPIC'))
		->setLayout('topic'); ?>
</div>

<div class="clearfix"></div>

<?php
echo $this->subLayout('Widget/Module')->set('position', 'kunena_topictitle');
echo $this->subRequest('Topic/Poll')->set('id', $topic->id);
echo $this->subLayout('Widget/Module')->set('position', 'kunena_poll');
if($me->exists()) echo $this->subRequest('Topic/Item/Actions')->set('id', $topic->id);

foreach ($this->messages as $id => $message)
{
	echo $this->subRequest('Topic/Item/Message')
		->set('mesid', $message->id)
		->set('location', $id);
}
?>

<div class="pull-left">
	<?php echo $this->subLayout('Widget/Pagination/List')
		->set('pagination', $this->pagination)
		->set('display', true);; ?>
</div>
<div class="pull-right">
	<?php echo $this->subLayout('Widget/Search')
		->set('id', $topic->id)
		->set('title', JText::_('COM_KUNENA_SEARCH_TOPIC'))
		->setLayout('topic'); ?>
</div>

<?php echo $this->subRequest('Topic/Item/Actions')->set('id', $topic->id); ?>
<div class="clearfix"></div>

<?php echo $this->subLayout('Category/Moderators')->set('moderators', $this->category->getModerators(false)); ?>

<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Topic
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

// @var KunenaForumTopic $topic

$topic = $this->topic;
$me = KunenaUserHelper::getMyself();

$this->addScriptDeclaration(
	'// <![CDATA[
var kunena_anonymous_name = "' . JText::_('COM_KUNENA_USERNAME_ANONYMOUS') . '";
// ]]>');

JText::script('COM_KUNENA_RATE_LOGIN');
JText::script('COM_KUNENA_RATE_NOT_YOURSELF');
JText::script('COM_KUNENA_RATE_ALLREADY');
JText::script('COM_KUNENA_RATE_SUCCESSFULLY_SAVED');

JText::script('COM_KUNENA_SOCIAL_EMAIL_LABEL');
JText::script('COM_KUNENA_SOCIAL_TWITTER_LABEL');
JText::script('COM_KUNENA_SOCIAL_FACEBOOK_LABEL');
JText::script('COM_KUNENA_SOCIAL_GOOGLEPLUS_LABEL');
JText::script('COM_KUNENA_SOCIAL_LINKEDIN_LABEL');
JText::script('COM_KUNENA_SOCIAL_PINTEREST_LABEL');
JText::script('COM_KUNENA_SOCIAL_STUMBLEUPON_LABEL');
JText::script('COM_KUNENA_SOCIAL_WHATSAPP_LABEL');

$this->addStyleSheet('assets/css/jquery.atwho.css');

// Load caret.js always before atwho.js script and use it for autocomplete, emojiis...
$this->addScript('assets/js/jquery.caret.js');
$this->addScript('assets/js/jquery.atwho.js');
$this->addScript('assets/js/topic.js');

$this->addStyleSheet('assets/css/rating.css');
$this->addScript('assets/js/rating.js');
$this->addScript('assets/js/krating.js');

$this->ktemplate = KunenaFactory::getTemplate();
$social = $this->ktemplate->params->get('socialshare');
$quick = $this->ktemplate->params->get('quick');
?>
<div><?php echo $this->subLayout('Widget/Module')->set('position', 'kunena_topic_top'); ?></div>
<?php if ($this->category->headerdesc) : ?>
<div class="alert alert-info">
	<a class="close" data-dismiss="alert" href="#">&times;</a>
	<?php echo $this->category->displayField('headerdesc'); ?>
</div>
<?php endif; ?>

<h1>
	<?php echo $topic->getIcon($topic->getCategory()->iconset);?>
	<?php
	if ($this->ktemplate->params->get('labels') != 0)
	{
		echo $this->subLayout('Widget/Label')->set('topic', $this->topic)->setLayout('default');
	}
	?>
	<?php echo $topic->displayField('subject');?>
	<?php echo $this->subLayout('Topic/Item/Rating')->set('category', $this->category)->set('topicid', $topic->id)->set('config', $this->config);?>
</h1>

<div><?php echo $this->subRequest('Topic/Item/Actions')->set('id', $topic->id); ?></div>

<div class="pull-left">
	<?php echo $this->subLayout('Widget/Pagination/List')
	->set('pagination', $this->pagination)
	->set('display', true); ?>
</div>

<h2 class="pull-right">
	<?php echo $this->subLayout('Widget/Search')
	->set('id', $topic->id)
	->set('title', JText::_('COM_KUNENA_SEARCH_TOPIC'))
	->setLayout('topic'); ?>
</h2>

<div class="clearfix"></div>

<?php if ($social == 1) : ?>
        <div><?php echo $this->subLayout('Widget/Social'); ?></div>
<?php endif; ?>

<?php if ($social == 2) : ?>
        <div><?php echo $this->subLayout('Widget/Socialcustomtag'); ?></div>
<?php endif; ?>

<?php
if ($this->ktemplate->params->get('displayModule'))
{
	echo $this->subLayout('Widget/Module')->set('position', 'kunena_topictitle');
}

echo $this->subRequest('Topic/Poll')->set('id', $topic->id);

if ($this->ktemplate->params->get('displayModule'))
{
	echo $this->subLayout('Widget/Module')->set('position', 'kunena_poll');
}

$count = 1;
foreach ($this->messages as $id => $message)
{
	echo $this->subRequest('Topic/Item/Message')
		->set('mesid', $message->id)
		->set('location', $id);

	if ($this->ktemplate->params->get('displayModule'))
	{
		echo $this->subLayout('Widget/Module')
			->set('position', 'kunena_msg_row_' . $count++);
	}
}

if ($quick == 2)
{
	echo $this->subLayout('Message/Edit')
		->set('message', $this->message)
		->setLayout('full');
}
?>

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

<div><?php echo $this->subRequest('Topic/Item/Actions')->set('id', $topic->id); ?></div>

<?php if ($this->ktemplate->params->get('writeaccess')) : ?>
<div><?php echo $this->subLayout('Widget/Writeaccess')->set('id', $topic->id); ?></div>
<?php endif;

if ($this->config->enableforumjump)
{
	echo $this->subLayout('Widget/Forumjump')->set('categorylist', $this->categorylist);
}?>

<div class="pull-right"><?php echo $this->subLayout('Category/Moderators')->set('moderators', $this->category->getModerators(false)); ?></div>

<div class="clearfix"></div>

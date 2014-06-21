<?php
/**
 * @version     1.0.0
 * @package     com_timeline
 * @copyright   Copyright (C) 2014. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Adam Bouqdib <info@donjoomla.com> - http://donjoomla.com
 */
// no direct access
defined('_JEXEC') or die;

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_timeline', JPATH_ADMINISTRATOR);
$canEdit = JFactory::getUser()->authorise('core.edit', 'com_timeline');
if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_timeline')) {
	$canEdit = JFactory::getUser()->id == $this->item->created_by;
}
JHtml::_('jquery.framework');
$document = JFactory::getDocument();
$document->addScript('http://cdn.knightlab.com/libs/timeline/latest/js/storyjs-embed.js');
$js = '
		jQuery(document).ready(function() {
			var timeline_data = {"timeline":'.json_encode($this->item->timeline).'}
			createStoryJS({
				width:              "'.$this->item->width.'",
				height:             "'.$this->item->height.'",
				source:             timeline_data,
				embed_id:           "timeline-embed",
				start_at_end:       '.$this->item->start_at_end.',
				start_at_slide:     "'.$this->item->start_at_slide.'",
				start_zoom_adjust:  "'.$this->item->start_zoom_adjust.'",
				hash_bookmark:      '.$this->item->hash_bookmark.',
				font:               "'.$this->item->font.'",
				debug:              false,
				lang:               "'.$this->item->lang.'",
				maptype:            "'.$this->item->maptype.'"
		});
    });';
$document->addScriptDeclaration($js);
?>
<?php if($this->params->get('show_page_heading')) : ?>
<h1><?php echo $this->params->get('page_heading'); ?></h1>
<?php endif; ?>
<?php if ($this->item) : ?>
<div id="timeline-embed"></div>
    <?php if($canEdit && $this->item->checked_out == 0): ?>
		<a href="<?php echo JRoute::_('index.php?option=com_timeline&task=timeline.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_TIMELINE_EDIT_ITEM"); ?></a>
	<?php endif; ?>
								<?php if(JFactory::getUser()->authorise('core.delete','com_timeline')):
								?>
									<a href="javascript:document.getElementById('form-timeline-delete-<?php echo $this->item->id ?>').submit()"><?php echo JText::_("COM_TIMELINE_DELETE_ITEM"); ?></a>
									<form id="form-timeline-delete-<?php echo $this->item->id; ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_timeline&task=timeline.remove'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
										<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
										<input type="hidden" name="option" value="com_timeline" />
										<input type="hidden" name="task" value="timeline.remove" />
										<?php echo JHtml::_('form.token'); ?>
									</form>
								<?php
								endif;
							?>
<?php
else:
    echo JText::_('COM_TIMELINE_ITEM_NOT_LOADED');
endif;
?>

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
?>
<?php if ($this->item) : ?>
<div class="item_fields">
  <ul class="fields_list">
    <li><?php echo JText::_('COM_TIMELINE_FORM_LBL_ITEM_ID'); ?>: <?php echo $this->item->id; ?></li>
    <li><?php echo JText::_('COM_TIMELINE_FORM_LBL_ITEM_TIMELINE'); ?>: <?php echo $this->item->timeline; ?></li>
    <li><?php echo JText::_('COM_TIMELINE_FORM_LBL_ITEM_STATE'); ?>: <?php echo $this->item->state; ?></li>
    <li><?php echo JText::_('COM_TIMELINE_FORM_LBL_ITEM_CHECKED_OUT'); ?>: <?php echo $this->item->checked_out; ?></li>
    <li><?php echo JText::_('COM_TIMELINE_FORM_LBL_ITEM_CHECKED_OUT_TIME'); ?>: <?php echo $this->item->checked_out_time; ?></li>
    <li><?php echo JText::_('COM_TIMELINE_FORM_LBL_ITEM_HEADLINE'); ?>: <?php echo $this->item->headline; ?></li>
    <li><?php echo JText::_('COM_TIMELINE_FORM_LBL_ITEM_STARTDATE'); ?>: <?php echo $this->item->startdate; ?></li>
    <li><?php echo JText::_('COM_TIMELINE_FORM_LBL_ITEM_ENDDATE'); ?>: <?php echo $this->item->enddate; ?></li>
    <li><?php echo JText::_('COM_TIMELINE_FORM_LBL_ITEM_CREATED_BY'); ?>: <?php echo $this->item->created_by; ?></li>
    <li><?php echo JText::_('COM_TIMELINE_FORM_LBL_ITEM_TEXT'); ?>: <?php echo $this->item->text; ?></li>
    <li><?php echo JText::_('COM_TIMELINE_FORM_LBL_ITEM_TAG'); ?>: <?php echo $this->item->tag; ?></li>
    <li><?php echo JText::_('COM_TIMELINE_FORM_LBL_ITEM_MEDIA'); ?>: <?php echo $this->item->media; ?></li>
    <li><?php echo JText::_('COM_TIMELINE_FORM_LBL_ITEM_THUMBNAIL'); ?>: <?php echo $this->item->thumbnail; ?></li>
    <li><?php echo JText::_('COM_TIMELINE_FORM_LBL_ITEM_CREDIT'); ?>: <?php echo $this->item->credit; ?></li>
    <li><?php echo JText::_('COM_TIMELINE_FORM_LBL_ITEM_CAPTION'); ?>: <?php echo $this->item->caption; ?></li>
    <li><?php echo JText::_('COM_TIMELINE_FORM_LBL_ITEM_CLASSNAME'); ?>: <?php echo $this->item->classname; ?></li>
  </ul>
</div>
<?php if($canEdit && $this->item->checked_out == 0): ?>
<a href="<?php echo JRoute::_('index.php?option=com_timeline&task=item.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_TIMELINE_EDIT_ITEM"); ?></a>
<?php endif; ?>
<?php if(JFactory::getUser()->authorise('core.delete','com_timeline')):
								?>
<a href="javascript:document.getElementById('form-item-delete-<?php echo $this->item->id ?>').submit()"><?php echo JText::_("COM_TIMELINE_DELETE_ITEM"); ?></a>
<form id="form-item-delete-<?php echo $this->item->id; ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_timeline&task=item.remove'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
  <input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
  <input type="hidden" name="option" value="com_timeline" />
  <input type="hidden" name="task" value="item.remove" />
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

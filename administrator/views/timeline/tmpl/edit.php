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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_timeline/assets/css/timeline.css');
?>
<script type="text/javascript">
    js = jQuery.noConflict();
    js(document).ready(function() {
        
	js('input:hidden.start_at_slide').each(function(){
		var name = js(this).attr('name');
		if(name.indexOf('start_at_slidehidden')){
			js('#jform_start_at_slide option[value="'+js(this).val()+'"]').attr('selected',true);
		}
	});
	js("#jform_start_at_slide").trigger("liszt:updated");
    });

    Joomla.submitbutton = function(task)
    {
        if (task == 'timeline.cancel') {
            Joomla.submitform(task, document.getElementById('timeline-form'));
        }
        else {
            
            if (task != 'timeline.cancel' && document.formvalidator.isValid(document.id('timeline-form'))) {
                
                Joomla.submitform(task, document.getElementById('timeline-form'));
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
</script>
<form action="<?php echo JRoute::_('index.php?option=com_timeline&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="timeline-form" class="form-validate">
  <div class="form-inline form-inline-header">
	<div class="control-group">
      <div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
      <div class="controls"><?php echo $this->form->getInput('title'); ?></div>
    </div>
  </div>
    
  <div class="form-horizontal"> 
  <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?> 
  <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_TIMELINE_TITLE_TIMELINE', true)); ?>
    <div class="row-fluid">
      <div class="span9">
        <fieldset class="adminform">
          <div class="control-group">
            <?php echo $this->form->getInput('text'); ?>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('type'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('type'); ?></div>
          </div>
        </fieldset>
      </div>
      <div class="span3">
        <fieldset class="form-vertical">
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('lang'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('lang'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('state'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('id'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('created_by'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('created_by'); ?></div>
          </div>
        </fieldset>
      </div>
    </div>
    <?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'style', JText::_('COM_TIMELINE_FORM_LBL_ITEM_MEDIA', true)); ?>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('media'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('media'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('credit'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('credit'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('caption'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('caption'); ?></div>
          </div>
    <?php echo JHtml::_('bootstrap.endTab'); ?> 
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'options', JText::_('JOPTIONS', true)); ?>
    <div class="row-fluid">
      <div class="span6">
        <fieldset class="adminform">
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('width'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('width'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('height'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('height'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('start_zoom_adjust'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('start_zoom_adjust'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('hash_bookmark'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('hash_bookmark'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('start_at_slide'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('start_at_slide'); ?></div>
          </div>
          <?php
				foreach((array)$this->item->start_at_slide as $value): 
					if(!is_array($value)):
						echo '<input type="hidden" class="start_at_slide" name="jform[start_at_slidehidden]['.$value.']" value="'.$value.'" />';
					endif;
				endforeach;
			?>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('maptype'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('maptype'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('font'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('font'); ?></div>
          </div>
        </fieldset>
      </div>
    </div>
    <?php echo JHtml::_('bootstrap.endTab'); ?> 
	<?php echo JHtml::_('bootstrap.endTabSet'); ?>
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?> </div>
</form>

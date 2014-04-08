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
        
	js('input:hidden.timeline').each(function(){
		var name = js(this).attr('name');
		if(name.indexOf('timelinehidden')){
			js('#jform_timeline option[value="'+js(this).val()+'"]').attr('selected',true);
		}
	});
	js("#jform_timeline").trigger("liszt:updated");
    });

    Joomla.submitbutton = function(task)
    {
        if (task == 'item.cancel') {
            Joomla.submitform(task, document.getElementById('item-form'));
        }
        else {
            
            if (task != 'item.cancel' && document.formvalidator.isValid(document.id('item-form'))) {
                
                Joomla.submitform(task, document.getElementById('item-form'));
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
</script>
<form action="<?php echo JRoute::_('index.php?option=com_timeline&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="item-form" class="form-validate">
  <div class="form-inline form-inline-header">
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('headline'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('headline'); ?></div>
          </div>
  </div>
  <div class="form-horizontal"> 
  <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?> 
  <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_TIMELINE_TITLE_ITEM', true)); ?>
    <div class="row-fluid">
      <div class="span9">
        <fieldset class="adminform">
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('startdate'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('startdate'); ?> - <?php echo $this->form->getInput('enddate'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('text'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('text'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('classname'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('classname'); ?></div>
          </div>
        </fieldset>
      </div>
      <div class="span3">
        <fieldset class="form-vertical">
          <div class="control-group">
          <?php
				foreach((array)$this->item->timeline as $value): 
					if(!is_array($value)):
						echo '<input type="hidden" class="timeline" name="jform[timelinehidden]['.$value.']" value="'.$value.'" />';
					endif;
				endforeach;
			?>
            <div class="control-label"><?php echo $this->form->getLabel('timeline'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('timeline'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('tag'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('tag'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('state'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('created_by'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('created_by'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('id'); ?></div>
          </div>
        </fieldset>
      </div>
    </div>
    <?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'media', JText::_('COM_TIMELINE_FORM_LBL_ITEM_MEDIA', true)); ?>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('media'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('media'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('thumbnail'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('thumbnail'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('credit'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('credit'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('caption'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('caption'); ?></div>
          </div>
    <?php echo JHtml::_('bootstrap.endTab'); ?> <?php echo JHtml::_('bootstrap.endTabSet'); ?>
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?> </div>
</form>

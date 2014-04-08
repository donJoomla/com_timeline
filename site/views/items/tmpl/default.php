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
?>
<script type="text/javascript">
    function deleteItem(item_id){
        if(confirm("<?php echo JText::_('COM_TIMELINE_DELETE_MESSAGE'); ?>")){
            document.getElementById('form-item-delete-' + item_id).submit();
        }
    }
</script>

<div class="items">
    <ul class="items_list">
<?php $show = false; ?>
        <?php foreach ($this->items as $item) : ?>

            
				<?php
					if($item->state == 1 || ($item->state == 0 && JFactory::getUser()->authorise('core.edit.own',' com_timeline'))):
						$show = true;
						?>
							<li>
								<a href="<?php echo JRoute::_('index.php?option=com_timeline&view=item&id=' . (int)$item->id); ?>"><?php echo $item->headline; ?></a>
								<?php
									if(JFactory::getUser()->authorise('core.edit.state','com_timeline')):
									?>
										<a href="javascript:document.getElementById('form-item-state-<?php echo $item->id; ?>').submit()"><?php if($item->state == 1): echo JText::_("COM_TIMELINE_UNPUBLISH_ITEM"); else: echo JText::_("COM_TIMELINE_PUBLISH_ITEM"); endif; ?></a>
										<form id="form-item-state-<?php echo $item->id ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_timeline&task=item.save'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
											<input type="hidden" name="jform[id]" value="<?php echo $item->id; ?>" />
											<input type="hidden" name="jform[state]" value="<?php echo (int)!((int)$item->state); ?>" />
											<input type="hidden" name="option" value="com_timeline" />
											<input type="hidden" name="task" value="item.publish" />
											<?php echo JHtml::_('form.token'); ?>
										</form>
									<?php
									endif;
									if(JFactory::getUser()->authorise('core.delete','com_timeline')):
									?>
										<a href="javascript:deleteItem(<?php echo $item->id; ?>);"><?php echo JText::_("COM_TIMELINE_DELETE_ITEM"); ?></a>
										<form id="form-item-delete-<?php echo $item->id; ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_timeline&task=item.remove'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
											<input type="hidden" name="jform[id]" value="<?php echo $item->id; ?>" />
											<input type="hidden" name="option" value="com_timeline" />
											<input type="hidden" name="task" value="item.remove" />
											<?php echo JHtml::_('form.token'); ?>
										</form>
									<?php
									endif;
								?>
							</li>
						<?php endif; ?>

<?php endforeach; ?>
        <?php
        if (!$show):
            echo JText::_('COM_TIMELINE_NO_ITEMS');
        endif;
        ?>
    </ul>
</div>
<?php if ($show): ?>
    <div class="pagination">
        <p class="counter">
            <?php echo $this->pagination->getPagesCounter(); ?>
        </p>
        <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
<?php endif; ?>


									<?php if(JFactory::getUser()->authorise('core.create','com_timeline')): ?><a href="<?php echo JRoute::_('index.php?option=com_timeline&task=item.edit&id=0'); ?>"><?php echo JText::_("COM_TIMELINE_ADD_ITEM"); ?></a>
	<?php endif; ?>
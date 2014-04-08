<?php
/**
 * @version     1.0.0
 * @package     com_timeline
 * @copyright   Copyright (C) 2014. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Adam Bouqdib <info@donjoomla.com> - http://donjoomla.com
 */

// No direct access.
defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Timelines list controller class.
 */
class TimelineControllerTimelines extends TimelineController
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function &getModel($name = 'Timelines', $prefix = 'TimelineModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}
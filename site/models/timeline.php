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

jimport('joomla.application.component.modelitem');
jimport('joomla.event.dispatcher');

/**
 * Timeline model.
 */
class TimelineModelTimeline extends JModelItem {

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
    protected function populateState() {
        $app = JFactory::getApplication('com_timeline');

        // Load state from the request userState on edit or from the passed variable on default
        if (JFactory::getApplication()->input->get('layout') == 'edit') {
            $id = JFactory::getApplication()->getUserState('com_timeline.edit.timeline.id');
        } else {
            $id = JFactory::getApplication()->input->get('id');
            JFactory::getApplication()->setUserState('com_timeline.edit.timeline.id', $id);
        }
        $this->setState('timeline.id', $id);

        // Load the parameters.
        $params = $app->getParams();
        $params_array = $params->toArray();
        if (isset($params_array['item_id'])) {
            $this->setState('timeline.id', $params_array['item_id']);
        }
        $this->setState('params', $params);
    }

    /**
     * Method to get an ojbect.
     *
     * @param	integer	The id of the object to get.
     *
     * @return	mixed	Object on success, false on failure.
     */
    public function &getData($id = null) {
        if ($this->_item === null) {
            $this->_item = false;

            if (empty($id)) {
                $id = $this->getState('timeline.id');
            }

            // Get a level row instance.
            $table = $this->getTable();

            // Attempt to load the row.
            if ($table->load($id)) {
                // Check published state.
                if ($published = $this->getState('filter.published')) {
                    if ($table->state != $published) {
                        return $this->_item;
                    }
                }

                // Convert the JTable to a clean JObject.
                $properties = $table->getProperties(1);
                $this->_item = JArrayHelper::toObject($properties, 'JObject');
            } elseif ($error = $table->getError()) {
                $this->setError($error);
            }
        }

        
		if ( isset($this->_item->created_by) ) {
			$this->_item->created_by = JFactory::getUser($this->_item->created_by)->name;
		}
		
		$this->_item->start_at_end = ($this->_item->start_at_slide >= 0 ? 'false' : 'true');

			if (isset($this->_item->start_at_slide) && $this->_item->start_at_slide != '') {
				if(is_object($this->_item->start_at_slide)){
					$this->_item->start_at_slide = JArrayHelper::fromObject($this->_item->start_at_slide);
				}
				$values = (is_array($this->_item->start_at_slide)) ? $this->_item->start_at_slide : explode(',',$this->_item->start_at_slide);

				$textValue = array();
				foreach ($values as $value){
					$db = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query
							->select('headline')
							->from('`#__timeline_items`')
							->where('id = ' .$value);
					$db->setQuery($query);
					$results = $db->loadObject();
					if ($results) {
						$textValue[] = $results->headline;
					}
				}

				$this->_item->start_at_slide = !empty($textValue) ? implode(', ', $textValue) : $this->_item->start_at_slide;

			}
			
			$this->_item->timeline = new stdClass;
			$this->_item->timeline->headline = $this->_item->title;
			$this->_item->timeline->type = $this->_item->type;
			$this->_item->timeline->text = $this->_item->text;
			$this->_item->timeline->asset = new stdClass;
			$this->_item->timeline->asset->media = $this->_item->media;
			$this->_item->timeline->asset->thumbnail = $this->_item->thumbnail;
			$this->_item->timeline->asset->credit = $this->_item->credit;
			$this->_item->timeline->asset->caption = $this->_item->caption;
			unset($this->_item->type, $this->_item->text, $this->_item->media, $this->_item->thumbnail, $this->_item->credit, $this->_item->caption);
			
			// add the dates to the timeline
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query
				->select('headline, startdate AS startDate, enddate AS endDate, text, tag, media, thumbnail, credit, caption, classname')
				->from('`#__timeline_items`')
				->where('state = 1')
				->where('timeline = ' .$id);
			$db->setQuery($query);
			$results = $db->loadObjectList();
			foreach($results as &$result) {
				$result->startDate = JHtml::date($result->startDate, 'Y,m,d');
				$result->endDate = ($result->endDate>0 ? JHtml::date($result->endDate, 'Y,m,d') : $result->startDate);
				$result->asset = new stdClass;
				$result->asset->media = $result->media;
				$result->asset->thumbnail = $result->thumbnail;
				$result->asset->credit = $result->credit;
				$result->asset->caption = $result->caption;
				unset($result->media, $result->thumbnail, $result->credit, $result->caption);
			}
			if ($results) {
				$this->_item->timeline->date = $results;
			}
        return json_decode(json_encode($this->filter_object($this->_item)));
    }

    public function getTable($type = 'Timeline', $prefix = 'TimelineTable', $config = array()) {
        $this->addTablePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to check in an item.
     *
     * @param	integer		The id of the row to check out.
     * @return	boolean		True on success, false on failure.
     * @since	1.6
     */
    public function checkin($id = null) {
        // Get the id.
        $id = (!empty($id)) ? $id : (int) $this->getState('timeline.id');

        if ($id) {

            // Initialise the table
            $table = $this->getTable();

            // Attempt to check the row in.
            if (method_exists($table, 'checkin')) {
                if (!$table->checkin($id)) {
                    $this->setError($table->getError());
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Method to check out an item for editing.
     *
     * @param	integer		The id of the row to check out.
     * @return	boolean		True on success, false on failure.
     * @since	1.6
     */
    public function checkout($id = null) {
        // Get the user id.
        $id = (!empty($id)) ? $id : (int) $this->getState('timeline.id');

        if ($id) {

            // Initialise the table
            $table = $this->getTable();

            // Get the current user object.
            $user = JFactory::getUser();

            // Attempt to check the row out.
            if (method_exists($table, 'checkout')) {
                if (!$table->checkout($user->get('id'), $id)) {
                    $this->setError($table->getError());
                    return false;
                }
            }
        }

        return true;
    }

    public function getCategoryName($id) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query
                ->select('title')
                ->from('#__categories')
                ->where('id = ' . $id);
        $db->setQuery($query);
        return $db->loadObject();
    }

    public function publish($id, $state) {
        $table = $this->getTable();
        $table->load($id);
        $table->state = $state;
        return $table->store();
    }

    public function delete($id) {
        $table = $this->getTable();
        return $table->delete($id);
	}
	
	function filter_object($input) 
	{
		if(is_object($input)) $input = (array) $input;
		foreach ($input as &$value)
		{
			if(is_object($value)) $value = (array) $value;
			if (is_array($value)) {
				$value = $this->filter_object($value);
			}
		}
		return array_filter($input);
	} 

}

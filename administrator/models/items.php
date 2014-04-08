<?php

/**
 * @version     1.0.0
 * @package     com_timeline
 * @copyright   Copyright (C) 2014. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Adam Bouqdib <info@donjoomla.com> - http://donjoomla.com
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Timeline records.
 */
class TimelineModelItems extends JModelList {

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                                'id', 'a.id',
                'timeline', 'a.timeline',
                'state', 'a.state',
                'headline', 'a.headline',
                'startdate', 'a.startdate',
                'enddate', 'a.enddate',
                'created_by', 'a.created_by',
                'text', 'a.text',
                'tag', 'a.tag',
                'media', 'a.media',
                'thumbnail', 'a.thumbnail',
                'credit', 'a.credit',
                'caption', 'a.caption',
                'classname', 'a.classname',

            );
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     */
    protected function populateState($ordering = null, $direction = null) {
        // Initialise variables.
        $app = JFactory::getApplication('administrator');

        // Load the filter state.
        $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
        $this->setState('filter.state', $published);

        
		//Filtering startdate
		$this->setState('filter.startdate.from', $app->getUserStateFromRequest($this->context.'.filter.startdate.from', 'filter_from_startdate', '', 'string'));
		$this->setState('filter.startdate.to', $app->getUserStateFromRequest($this->context.'.filter.startdate.to', 'filter_to_startdate', '', 'string'));

		//Filtering enddate
		$this->setState('filter.enddate.from', $app->getUserStateFromRequest($this->context.'.filter.enddate.from', 'filter_from_enddate', '', 'string'));
		$this->setState('filter.enddate.to', $app->getUserStateFromRequest($this->context.'.filter.enddate.to', 'filter_to_enddate', '', 'string'));

		//Filtering created_by
		$this->setState('filter.created_by', $app->getUserStateFromRequest($this->context.'.filter.created_by', 'filter_created_by', '', 'string'));


        // Load the parameters.
        $params = JComponentHelper::getParams('com_timeline');
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.headline', 'asc');
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param	string		$id	A prefix for the store id.
     * @return	string		A store id.
     * @since	1.6
     */
    protected function getStoreId($id = '') {
        // Compile the store id.
        $id.= ':' . $this->getState('filter.search');
        $id.= ':' . $this->getState('filter.state');

        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return	JDatabaseQuery
     * @since	1.6
     */
    protected function getListQuery() {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
                $this->getState(
                        'list.select', 'a.*'
                )
        );
        $query->from('`#__timeline_items` AS a');

        
		// Join over the users for the checked out user
		$query->select("uc.name AS editor");
		$query->join("LEFT", "#__users AS uc ON uc.id=a.checked_out");
		// Join over the foreign key 'timeline'
		$query->select('#__timeline_timelines_1193573.title AS timelines_title_1193573');
		$query->join('LEFT', '#__timeline_timelines AS #__timeline_timelines_1193573 ON #__timeline_timelines_1193573.id = a.timeline');
		// Join over the user field 'created_by'
		$query->select('created_by.name AS created_by');
		$query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');

        

		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('a.state = ' . (int) $published);
		} else if ($published === '') {
			$query->where('(a.state IN (0, 1))');
		}

        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int) substr($search, 3));
            } else {
                $search = $db->Quote('%' . $db->escape($search, true) . '%');
                $query->where('( a.headline LIKE '.$search.'  OR  a.tag LIKE '.$search.' )');
            }
        }

        

		//Filtering startdate
		$filter_startdate_from = $this->state->get("filter.startdate.from");
		if ($filter_startdate_from) {
			$query->where("a.startdate >= '".$db->escape($filter_startdate_from)."'");
		}
		$filter_startdate_to = $this->state->get("filter.startdate.to");
		if ($filter_startdate_to) {
			$query->where("a.startdate <= '".$db->escape($filter_startdate_to)."'");
		}

		//Filtering enddate
		$filter_enddate_from = $this->state->get("filter.enddate.from");
		if ($filter_enddate_from) {
			$query->where("a.enddate >= '".$db->escape($filter_enddate_from)."'");
		}
		$filter_enddate_to = $this->state->get("filter.enddate.to");
		if ($filter_enddate_to) {
			$query->where("a.enddate <= '".$db->escape($filter_enddate_to)."'");
		}

		//Filtering created_by
		$filter_created_by = $this->state->get("filter.created_by");
		if ($filter_created_by) {
			$query->where("a.created_by = '".$db->escape($filter_created_by)."'");
		}


        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');
        if ($orderCol && $orderDirn) {
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }

        return $query;
    }

    public function getItems() {
        $items = parent::getItems();
        
		foreach ($items as $oneItem) {

			if (isset($oneItem->timeline)) {
				$values = explode(',', $oneItem->timeline);

				$textValue = array();
				foreach ($values as $value){
					$db = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query
							->select('title')
							->from('`#__timeline_timelines`')
							->where('id = ' . $db->quote($db->escape($value)));
					$db->setQuery($query);
					$results = $db->loadObject();
					if ($results) {
						$textValue[] = $results->title;
					}
				}

			$oneItem->timeline = !empty($textValue) ? implode(', ', $textValue) : $oneItem->timeline;

			}

			if ( isset($oneItem->tag) ) {
				// Catch the item tags (string with ',' coma glue)
				$tags = explode(",",$oneItem->tag);

				$db = JFactory::getDbo();
					$namedTags = array(); // Cleaning and initalization of named tags array

					// Get the tag names of each tag id
					foreach ($tags as $tag) {

						$query = $db->getQuery(true);
						$query->select("title");
						$query->from('`#__tags`');
						$query->where( "id=" . intval($tag) );

						$db->setQuery($query);
						$row = $db->loadObjectList();

						// Read the row and get the tag name (title)
						if (!is_null($row)) {
							foreach ($row as $value) {
								if ( $value && isset($value->title) ) {
									$namedTags[] = trim($value->title);
								}
							}
						}

					}

					// Finally replace the data object with proper information
					$oneItem->tag = !empty($namedTags) ? implode(', ',$namedTags) : $oneItem->tag;
				}
		}
        return $items;
    }

}

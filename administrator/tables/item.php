<?php

/**
 * @version     1.0.0
 * @package     com_timeline
 * @copyright   Copyright (C) 2014. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Adam Bouqdib <info@donjoomla.com> - http://donjoomla.com
 */
// No direct access
defined('_JEXEC') or die;

/**
 * item Table class
 */
class TimelineTableitem extends JTable {

    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {
        parent::__construct('#__timeline_items', 'id', $db);
    }

    /**
     * Overloaded bind function to pre-process the params.
     *
     * @param	array		Named array
     * @return	null|string	null is operation was satisfactory, otherwise returns an error
     * @see		JTable:bind
     * @since	1.5
     */
    public function bind($array, $ignore = '') {

        

		//Support for multiple or not foreign key field: timeline
			if(isset($array['timeline'])){
				if(is_array($array['timeline'])){
					$array['timeline'] = implode(',',$array['timeline']);
				}
				else if(strrpos($array['timeline'], ',') != false){
					$array['timeline'] = explode(',',$array['timeline']);
				}
				else if(empty($array['timeline'])) {
					$array['timeline'] = '';
				}
			}
		$input = JFactory::getApplication()->input;
		$task = $input->getString('task', '');
		if(($task == 'save' || $task == 'apply') && (!JFactory::getUser()->authorise('core.edit.state','com_timeline') && $array['state'] == 1)){
			$array['state'] = 0;
		}

		if ( isset($array['tag']) && !empty($array['tag']) ) {
			// Load the tags helper.
			require_once JPATH_ADMINISTRATOR . '/components/com_tags/helpers/tags.php';

			// Get the allowed actions for the user
			$canDo = TagsHelper::getActions('com_tags'); // The helper get the user and the component name itself

			// Load the tags model.
			require_once JPATH_ADMINISTRATOR . '/components/com_tags/models/tag.php';
			JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tags/tables');

			// Get an instance of the table for insertion the new tags
			$tagsModel = TagsModelTag::getInstance('Tag','TagsModel');

			$tags = array(); // Initialization of the tag container must be processed

			// If tags is an array, store-mode
			if ( is_array($array['tag']) ) {
				// "Allow user creation" mode must be activated (default) in the component creation field
				// Save the tags does not exist into the table tags and get its id for save the entire Item with the proper data
				foreach ($array['tag'] as $singleTag) {
					// If there is any new tag... create it to get the id and save into the table #__COMPONENT_NAME_TABLE_NAME
					if ( strpos($singleTag, "#new#") !== FALSE ) {
						$user           = JFactory::getUser();
						$userId         = $user->id; // For writting permissions 
						$tagName        = str_replace("#new#", "", $singleTag);
						$tagAlias       = $tagPath = preg_replace('/[\s\W\.]+/', '-', $tagName); // Tags alias filter
						$tagMetadata    = array(
								"author"=>""
								, "robots"=>""
								, "tags"=>null
						);

						// The data tag field row
						$data = array(
								"parent_id" => 0
								, "path" => $tagPath
								, "title" => $tagName
								, "alias" => $tagAlias
								, "created_by_alias" => $user
								, "created_user_id" => $userId
								, "published" => 1
								, "checked_out"=> 0
								, "metadata" => json_encode($tagMetadata)
						);

						// Finally, store the tag if the user is granted for that
						if ( $canDo->get('core.create') ) {
							$table = $tagsModel->getTable();
							$table->bind($data) ? $table->store($data) : exit;
							$tags[] = $table->id; // And store the insert_id
						}
					}

				// NOT new Tag (already exists)
				// $singleTag is the tag id
				else
					$tags[] = intval($singleTag);
				}

				// Overrride the tags array, because we should need to change the id before field saving
				// The field in database will look like "299,345,567,567"
				$array['tag'] = implode(',',$tags);
			}
		}

        if (isset($array['params']) && is_array($array['params'])) {
            $registry = new JRegistry();
            $registry->loadArray($array['params']);
            $array['params'] = (string) $registry;
        }

        if (isset($array['metadata']) && is_array($array['metadata'])) {
            $registry = new JRegistry();
            $registry->loadArray($array['metadata']);
            $array['metadata'] = (string) $registry;
        }
        if (!JFactory::getUser()->authorise('core.admin', 'com_timeline.item.' . $array['id'])) {
            $actions = JFactory::getACL()->getActions('com_timeline', 'item');
            $default_actions = JFactory::getACL()->getAssetRules('com_timeline.item.' . $array['id'])->getData();
            $array_jaccess = array();
            foreach ($actions as $action) {
                $array_jaccess[$action->name] = $default_actions[$action->name];
            }
            $array['rules'] = $this->JAccessRulestoArray($array_jaccess);
        }
        //Bind the rules for ACL where supported.
        if (isset($array['rules']) && is_array($array['rules'])) {
            $this->setRules($array['rules']);
        }

        return parent::bind($array, $ignore);
    }

    /**
     * This function convert an array of JAccessRule objects into an rules array.
     * @param type $jaccessrules an arrao of JAccessRule objects.
     */
    private function JAccessRulestoArray($jaccessrules) {
        $rules = array();
        foreach ($jaccessrules as $action => $jaccess) {
            $actions = array();
            foreach ($jaccess->getData() as $group => $allow) {
                $actions[$group] = ((bool) $allow);
            }
            $rules[$action] = $actions;
        }
        return $rules;
    }

    /**
     * Overloaded check function
     */
    public function check() {

        //If there is an ordering column and this is a new row then get the next ordering value
        if (property_exists($this, 'ordering') && $this->id == 0) {
            $this->ordering = self::getNextOrder();
        }

        return parent::check();
    }

    /**
     * Method to set the publishing state for a row or list of rows in the database
     * table.  The method respects checked out rows by other users and will attempt
     * to checkin rows that it can after adjustments are made.
     *
     * @param    mixed    An optional array of primary key values to update.  If not
     *                    set the instance property value is used.
     * @param    integer The publishing state. eg. [0 = unpublished, 1 = published]
     * @param    integer The user id of the user performing the operation.
     * @return    boolean    True on success.
     * @since    1.0.4
     */
    public function publish($pks = null, $state = 1, $userId = 0) {
        // Initialise variables.
        $k = $this->_tbl_key;

        // Sanitize input.
        JArrayHelper::toInteger($pks);
        $userId = (int) $userId;
        $state = (int) $state;

        // If there are no primary keys set check to see if the instance key is set.
        if (empty($pks)) {
            if ($this->$k) {
                $pks = array($this->$k);
            }
            // Nothing to set publishing state on, return false.
            else {
                $this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
                return false;
            }
        }

        // Build the WHERE clause for the primary keys.
        $where = $k . '=' . implode(' OR ' . $k . '=', $pks);

        // Determine if there is checkin support for the table.
        if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time')) {
            $checkin = '';
        } else {
            $checkin = ' AND (checked_out = 0 OR checked_out = ' . (int) $userId . ')';
        }

        // Update the publishing state for rows with the given primary keys.
        $this->_db->setQuery(
                'UPDATE `' . $this->_tbl . '`' .
                ' SET `state` = ' . (int) $state .
                ' WHERE (' . $where . ')' .
                $checkin
        );
        $this->_db->query();

        // Check for a database error.
        if ($this->_db->getErrorNum()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        // If checkin is supported and all rows were adjusted, check them in.
        if ($checkin && (count($pks) == $this->_db->getAffectedRows())) {
            // Checkin each row.
            foreach ($pks as $pk) {
                $this->checkin($pk);
            }
        }

        // If the JTable instance value is in the list of primary keys that were set, set the instance.
        if (in_array($this->$k, $pks)) {
            $this->state = $state;
        }

        $this->setError('');
        return true;
    }

    /**
     * Define a namespaced asset name for inclusion in the #__assets table
     * @return string The asset name 
     *
     * @see JTable::_getAssetName 
     */
    protected function _getAssetName() {
        $k = $this->_tbl_key;
        return 'com_timeline.item.' . (int) $this->$k;
    }

    /**
     * Returns the parent asset's id. If you have a tree structure, retrieve the parent's id using the external key field
     *
     * @see JTable::_getAssetParentId 
     */
    protected function _getAssetParentId(JTable $table = null, $id = null) {
        // We will retrieve the parent-asset from the Asset-table
        $assetParent = JTable::getInstance('Asset');
        // Default: if no asset-parent can be found we take the global asset
        $assetParentId = $assetParent->getRootId();
        // The item has the component as asset-parent
        $assetParent->loadByName('com_timeline');
        // Return the found asset-parent-id
        if ($assetParent->id) {
            $assetParentId = $assetParent->id;
        }
        return $assetParentId;
    }

    public function delete($pk = null) {
        $this->load($pk);
        $result = parent::delete($pk);
        if ($result) {
            
            
        }
        return $result;
    }

}

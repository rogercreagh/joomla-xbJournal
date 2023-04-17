<?php
/*******
 * @package xbJournals Component
 * @filesource admin/models/calendar.php
 * @version 0.0.0.9 12th April 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
defined('_JEXEC') or die;

use Joomla\Registry\Registry;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\MVC\Model\AdminModel;

class XbjournalsModelCalendar extends AdminModel {
    
    public $typeAlias = 'com_xbjournals.calendar';
    
    public function getItem($pk = null) {
        
        $item = parent::getItem($pk);
     
        
        if (!empty($item->id)) {
            // Convert the metadata field to an array.
            $registry = new Registry($item->metadata);
            $item->metadata = $registry->toArray();
            
//             $tagsHelper = new TagsHelper;
//             $item->tags = $tagsHelper->getTagIds($item->id, 'com_xbmaps.map');
        }
        //fix null dates for clendar controls
        if (empty($item->created)) {
            $item->created = Factory::getDate()->toSql();
        }
        if (empty($item->created_by)) {
            $item->created_by = Factory::getUser()->id;
        }
        if (empty($item->modified)) {
            $item->modified = '0000-00-00 00:00:00';
        }
        
        return $item;
    }
    
    public function getTable($type = 'Server', $prefix = 'XbjournalsTable', $config = array()) {
        
        return JTable::getInstance($type, $prefix, $config);
    }
    
    public function getForm($data = array(), $loadData = true) {
        
        $form = $this->loadForm( 'com_xbjournals.calendar', 'calendar',
            array('control' => 'jform','load_data' => $loadData)
            );
        
        if (empty($form)) {
            return false;
        }
        
        return $form;
    }
    
    protected function loadFormData() {
        $data = Factory::getApplication()->getUserState('com_xbjournals.edit.calendar.data', array() );
        
        if (empty($data)) {
            $data = $this->getItem();
        }
        
        return $data;
    }
    
    protected function prepareTable($table) {
        $date = Factory::getDate();
        $user = Factory::getUser();
        $db = Factory::getDbo();
        
        $table->title = htmlspecialchars_decode($table->title, ENT_QUOTES);
        $table->alias = ApplicationHelper::stringURLSafe($table->alias);
        
        if (empty($table->alias)) {
            $table->alias = ApplicationHelper::stringURLSafe($table->title);
        }
        // Set the values
        if (empty($table->created)) {
            $table->created = $date->toSql();
        }
        if (empty($table->created_by)) {
            $table->created_by = Factory::getUser()->id;
        }
        if (empty($table->id)) {
            
            // Set ordering to the last item if not set
            if (empty($table->ordering)) {
                $query = $db->getQuery(true)
                ->select('MAX(ordering)')
                ->from($db->quoteName('#__xbjournals_calendars'));
                
                $db->setQuery($query);
                $max = $db->loadResult();
                
                $table->ordering = $max + 1;
            }
            //set modified to null to stop joomla defaulting to zero
            $table->modified = NULL;
        } else {
            // not new so set/update the modified details
            $table->modified    = $date->toSql();
            $table->modified_by = $user->id;
        }
    }
    
    public function publish(&$pks, $value = 1) {
        if (!empty($pks)) {
            foreach ($pks as $item) {
                $db = $this->getDbo();
                $query = $db->getQuery(true)
                ->update($db->quoteName('#__xbjournals_calendars'))
                ->set('state = ' . (int) $value)
                ->where('id='.$item);
                $db->setQuery($query);
                if (!($db->execute())) {
                    $this->setError($db->getErrorMsg());
                    return false;
                }
            }
            return true;
        }
    }
    
    public function delete(&$pks, $value = 1) {
        if (!empty($pks)) {
            $cnt = 0;
            $table = $this->getTable('calendar');
            foreach ($pks as $i=>$item) {
                $table->load($item);
                if (!$table->delete($item)) {
                    $mapword = ($cnt == 1)?  Text::_('XBJOURNALS_SERVER') : Text::_('XBJOURNALS_SERVERS');
                    Factory::getApplication()->enqueueMessage($cnt.$mapword.Text::_('XBJOURNALS_DELETED'));
                    $this->setError($table->getError());
                    return false;
                }
                $table->reset();
                $cnt++;
            }
            $mapword = ($cnt == 1)? Text::_('XBJOURNALS_SERVER') : Text::_('XBJOURNALS_SERVER');
            Factory::getApplication()->enqueueMessage($cnt.$mapword.' '.Text::_('XBJOURNALS_DELETED'));
            return true;
        }
    }
    
    public function save($data) {
        $input = Factory::getApplication()->input;
        
        if ($input->get('task') == 'save2copy') {
            $origTable = clone $this->getTable();
            $origTable->load($input->getInt('id'));
            
            if ($data['title'] == $origTable->title) {
                list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['title']);
                $data['title'] = $title;
                $data['alias'] = $alias;
            } else {
                if ($data['alias'] == $origTable->alias) {
                    $data['alias'] = '';
                }
            }
            // standard Joomla practice is to set the new copy record as unpublished
            $data['state'] = 0;
        }
        //set empty dates to null to stop j3 creating zero dates in mysql
//        if ($data['rec_date']=='') { $data['rec_date'] = NULL; }
        
        if (parent::save($data)) {
            //other stuff if req - eg saving subform data
            
            //$newcnt = XbjournalsHelper::getServerCalendars($calendarid);
            
            return true;
        }
        
        return false;
    }
    
}

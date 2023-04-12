<?php
/*******
 * @package xbJournals Component
 * @filesource admin/models/calendars.php
 * @version 0.0.0.8 12th April 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
defined('_JEXEC') or die;

 use Joomla\CMS\Factory;
// use Joomla\CMS\Component\ComponentHelper;
// use Joomla\CMS\Toolbar\Toolbar;
// use Joomla\CMS\Toolbar\ToolbarHelper;
// use Joomla\CMS\Language\Text;
// use Joomla\CMS\Layout\FileLayout;

class XbjournalsModelCalendars extends JModelList {
    
    public function __construct() {
        
        parent::__construct();
    }
    
    protected function getListQuery() {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        $query->select('a.id AS id, a.title AS title, a.alias AS alias, a.server_id AS server_id,'
            .'a.cal_displayname AS displayname, a.cal_url AS url, a.cal_ctag AS ctag,'
            .'a.cal_calendar_id AS cal_calendar_id, a.cal_components AS components, a.last_checked AS last_checked,'
            .'a.description AS description, a.state AS published, a.access AS access, a.catid AS catid,'
			.'a.created AS created, a.created_by AS created_by, a.created_by_alias AS created_by_alias,'
			.'a.modified AS modified, a.modified_by AS modified_by,'
            .'a.checked_out AS checked_out, a.checked_out_time AS checked_out_time,'
            .'a.metadata AS metadata, a.ordering AS ordering, a.params AS params, a.note AS note');
        $query->select('(SELECT COUNT(*) FROM #__xbjournals_vjournal_entries AS e WHERE e.calendar_id = a.id) AS ecnt' );
            
        $query->from('#__xbjournals_calendars AS a');
            
        $query->leftJoin('#__xbjournals_servers AS s ON s.id = a.server_id');
        $query->select('s.title AS server_title');
        //filter on published state
        //filter on category
        //filter on vjournal allowed
            
        $orderCol       = $this->state->get('list.ordering', 'title');
        $orderDirn      = $this->state->get('list.direction', 'ASC');
        
        $query->order($db->escape($orderCol.' '.$orderDirn));
        
        return $query;
            
    }
    
    public function getItems() {
        $items  = parent::getItems();
        if ($items) {
            $db = Factory::getDbo();
            $query = $db->getQuery(true);
            foreach ($items as $i=>$item) {
                if ($item->ecnt > 0) {
                    $query->clear();
                    $query->select('e.id, e.title, e.entry_type, e.ent_dtstart')
                        ->from('#__xbjournals_vjournal_entries AS e')
                        ->where('e.calendar_id = '.$db->q($item->id))
                        ->order('e.ent_dtstart DESC');
                    $query->setLimit(5);    
                    $db->setQuery($query);
                    $item->entries = $db->loadAssocList();
                }
            }
        }
        return $items;
    }
    
}

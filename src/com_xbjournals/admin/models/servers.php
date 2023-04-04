<?php
/*******
 * @package xbJournals Component
 * @filesource admin/models/servers.php
 * @version 0.0.0.3 3rd April 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
defined('_JEXEC') or die;

// use Joomla\CMS\Factory;
// use Joomla\CMS\Component\ComponentHelper;
// use Joomla\CMS\Toolbar\Toolbar;
// use Joomla\CMS\Toolbar\ToolbarHelper;
// use Joomla\CMS\Language\Text;
// use Joomla\CMS\Layout\FileLayout;

class XbjournalsModelServers extends JModelList {
    
    public function __construct() {
        
        parent::__construct();
    }
    
    protected function getListQuery() {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        
        $query->select('a.id AS id, a.title AS title, a.alias AS alias,'
            .'a.url AS url, a.username AS username, a.password AS password,'
            .'a.description AS description, a.state AS published, a.access AS access,'
			.'a.created AS created, a.created_by AS created_by, a.created_by_alias AS created_by_alias,'
			.'a.modified AS modified, a.modified_by AS modified_by,'
            .'a.checked_out AS checked_out, a.checked_out_time AS checked_out_time,'
            .'a.metadata AS metadata, a.ordering AS ordering, a.params AS params, a.note AS note');
            $query->select('(SELECT COUNT(*) FROM #__xbjournals_calendars AS j WHERE j.server_id=a.id) AS jcnt' );
            $query->from('#__xbjournals_servers AS a');
            
            $orderCol       = $this->state->get('list.ordering', 'title');
            $orderDirn      = $this->state->get('list.direction', 'ASC');
            
            $query->order($db->escape($orderCol.' '.$orderDirn));
            
            return $query;
            
    }
    
    public function getItems() {
        $items  = parent::getItems();
        
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        foreach ($items as $i=>$item) {
            if ($item->jcnt > 0) {
                $query->select('j.id, j.title')->from('#__xbjournals_calendars AS j')->where('j.server_id = '.$item->id);
                $db->setQuery($query);
                $item->journals = $db->loadAssoc();
            }
        }
        return $items;
    }
    
}

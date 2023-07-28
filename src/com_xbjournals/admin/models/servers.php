<?php
/*******
 * @package xbJournals Component
 * @filesource admin/models/servers.php
 * @version 0.1.2.3 21st April 2023
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

class XbjournalsModelServers extends JModelList {
    
    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id', 'title', 'a.title',
                'a.username','username','a.url','url',
                'ordering','a.ordering', 'category_title', 'cat.title',
                'published','a.state','modified','a.modified');
        }
        parent::__construct($config);
    }
    
    protected function populateState($ordering = 'title', $direction = 'desc') {
        
        $app = Factory::getApplication();
        $filt = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $filt);
        $filt = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
        $this->setState('filter.published', $filt);
        
        parent::populateState($ordering, $direction);
    }
    
    protected function getListQuery() {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        
        $query->select('a.id AS id, a.title AS title, a.alias AS alias,'
            .'a.url AS url, a.username AS username, a.password AS password,'
            .'a.description AS description, a.state AS published, a.access AS access,'
			.'a.created AS created, a.created_by AS created_by, a.created_by_alias AS created_by_alias,'
			.'a.modified AS modified, a.modified_by AS modified_by, a.updated AS updated,'
            .'a.checked_out AS checked_out, a.checked_out_time AS checked_out_time,'
            .'a.metadata AS metadata, a.ordering AS ordering, a.params AS params, a.note AS note');
            $query->select('(SELECT COUNT(*) FROM #__xbjournals_calendars AS c WHERE c.server_id=a.id) AS ccnt' );
            $query->from('#__xbjournals_servers AS a');
            //exclude local servers
            $query->where($db->qn('url').' IS NOT NULL AND '.$db->qn('url'). ' <> '.$db->q(''));
            // Filter by published state
            $published = $this->getState('filter.published');
            if (is_numeric($published)) {
                $query->where('a.state = ' . (int) $published);
            }
            
            // Filter by search in title/id/synop
            $search = $this->getState('filter.search');
            
            if (!empty($search)) {
                if (stripos($search, 'i:') === 0) {
                    $query->where($db->quoteName('a.id') . ' = ' . (int) substr($search, 2));
                } elseif (stripos($search,'d:')===0) {
                    $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim(substr($search,2)), true) . '%'));
                    $query->where('(a.description LIKE ' . $search.')');
                } elseif (stripos($search,'a:')===0) {
                    $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim(substr($search,2)), true) . '%'));
                    $query->where('(a.alias LIKE ' . $search.')');
                } elseif (stripos($search,':')!= 1) {
                    $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
                    $query->where('(a.title LIKE ' . $search  . ')');
                }
            }
            
            $orderCol       = $this->state->get('list.ordering', 'title');
            $orderDirn      = $this->state->get('list.direction', 'ASC');
            
            $query->order($db->escape($orderCol.' '.$orderDirn));
            
            return $query;
            
    }

    public function getItems() {
        $items  = parent::getItems();
        
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        foreach ($items as $i=>$item) {
            if ($item->ccnt > 0) {
                $query->clear();
                $query->select('b.id, b.title,IF (b.cal_components LIKE '.$db->q('%VJOURNAL%').', 1, 0) AS vjok')->from('#__xbjournals_calendars AS b')->where('b.server_id = '.$db->q($item->id));
                $db->setQuery($query);
                $item->calendars = $db->loadAssocList();
            }
        }
        return $items;
    }
    
}

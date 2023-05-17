<?php
/*******
 * @package xbJournals Component
 * @filesource admin/models/attachments.php
 * @version 0.0.5.3 17th May 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
defined('_JEXEC') or die;

 use Joomla\CMS\Factory;
 use Joomla\CMS\Helper\TagsHelper;
 use Joomla\CMS\Filter\OutputFilter;
 use Joomla\CMS\Uri\Uri;
 use Joomla\Utilities\ArrayHelper;
 // use Joomla\CMS\Component\ComponentHelper;
// use Joomla\CMS\Toolbar\Toolbar;
// use Joomla\CMS\Toolbar\ToolbarHelper;
// use Joomla\CMS\Language\Text;
// use Joomla\CMS\Layout\FileLayout;

class XbjournalsModelAttachments extends JModelList {
    
    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id', 'a.entry_id','entry_id','label', 'a.label', 'a.fmttype', 'fmttype',
                'e.entry_type',' entry_type', 'cal_title','calid', 'e.title', 'itemtitle');
        }
        parent::__construct($config);
    }
    
    protected function populateState($ordering = 'itemtitle', $direction = 'desc') {
        
        $app = Factory::getApplication();
        $filt = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $filt);
        $filt = $this->getUserStateFromRequest($this->context . '.filter.item', 'filter_item', '');
        $this->setState('filter.item', $filt);
        $filt = $this->getUserStateFromRequest($this->context . '.filter.locrem', 'filter_locrem', '');
        $this->setState('filter.locrem', $filt);
        $formSubmited = $app->input->post->get('form_submited');
        
        if ($formSubmited)
        {
            $journalfilt = $app->input->post->get('item');
            $this->setState('filter.journal', $journalfilt);
        }
        
        parent::populateState($ordering, $direction);
    }
    
    protected function getListQuery() {

        $app = Factory::getApplication();
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        $query->select('a.id AS id, a.entry_id AS entry_id, a.uri AS uri, a.fmttype AS fmttype, a.uri AS uri'
            .', a.filename AS filename, a.label AS label, a.localpath AS localpath, a.info AS info');
            
        $query->from('#__xbjournals_vjournal_attachments AS a');
        
        $query->join('LEFT', '#__xbjournals_vjournal_entries AS e ON e.id = a.entry_id');
        $query->select('e.id AS itemid, e.title AS itemtitle, e.entry_type as entry_type, e.dtstamp as entrydate');
        
        $query->leftJoin('#__xbjournals_calendars AS cal ON cal.id = e.calendar_id');
        $query->select('cal.id AS calid, cal.title AS cal_title');

        $cal = $this->getState('filter.cal');
        if (is_numeric($cal)) {
            $query->where('e.calendar_id = ' . (int) $cal);
        }
        
        // Filter by search in label/id/fname
        $search = $this->getState('filter.search');
        
        if (!empty($search)) {
            if (stripos($search, 'i:') === 0) {
                $query->where($db->quoteName('a.id') . ' = ' . (int) substr($search, 2));
            } elseif (stripos($search,'f:')===0) {
                $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim(substr($search,2)), true) . '%'));
                $query->where('(a.filename LIKE ' . $search.')');
            } elseif (stripos($search,':')!= 1) {
                $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
                $query->where('(a.label LIKE ' . $search  . ')');
            }
        }
                
        $orderCol       = $this->state->get('list.ordering', 'label');
        $orderDirn      = $this->state->get('list.direction', 'ASC');
        
        $query->order($db->escape($orderCol.' '.$orderDirn));
        
        return $query;
            
    }
    
    public function getItems() {

        $items  = parent::getItems();
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        if ($items) {
//            foreach ($items as $item) {
//            }
        }
        return $items;
    }
    
     
}

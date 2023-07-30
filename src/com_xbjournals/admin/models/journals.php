<?php
/*******
 * @package xbJournals Component
 * @filesource admin/models/journals.php
 * @version 0.1.2.4 23rd July 2023
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

class XbjournalsModelJournals extends JModelList {
    
    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id', 'title', 'a.title',
                'cal.title', 'cal_title', 'a.dtstart', 'dtstart', 'a.dtstamp', 'dtstamp',
                'ordering','a.ordering', 'category_title', 'cat.title',
                'catid', 'a.catid', 'category_id', 
                'published','a.state');
        }
        parent::__construct($config);
    }
    
    protected function populateState($ordering = 'title', $direction = 'desc') {
        
        $app = Factory::getApplication();
        $filt = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $filt);
        $filt = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
        $this->setState('filter.published', $filt);
        $filt = $this->getUserStateFromRequest($this->context . '.filter.journal', 'filter_journal', '');
        $this->setState('filter.journal', $filt);
        $filt = $this->getUserStateFromRequest($this->context . '.filter.category_id', 'filter_category_id');
        $this->setState('filter.categoryId', $filt);
        $filt = $this->getUserStateFromRequest($this->context . '.filter.tagfilt', 'filter_tagfilt', '');
        $this->setState('filter.tagfilt', $filt);
        $filt = $this->getUserStateFromRequest($this->context . '.filter.taglogic', 'filter_taglogic');
        $this->setState('filter.taglogic', $filt);
        //         $filt = $this->getUserStateFromRequest($this->context . '.filter.', 'filter_');
        //         $this->setState('filter.', $filt);
        
        $formSubmited = $app->input->post->get('form_submited');
        
        if ($formSubmited)
        {
            $journalfilt = $app->input->post->get('journal');
            $this->setState('filter.journal', $journalfilt);
            
            $categoryId = $app->input->post->get('category_id');
            $this->setState('filter.category_id', $categoryId);
            
            $tagfilt = $app->input->post->get('tagfilt');
            $this->setState('filter.tagfilt', $tagfilt);
        }
        
        parent::populateState($ordering, $direction);
    }
    
    protected function getListQuery() {

        $app = Factory::getApplication();
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        $query->select('a.id AS id, a.title AS title, a.alias AS alias, a.calendar_id AS calendar_id,'
            .'a.uid AS uid, a.parentuid AS parentuid, a.dtstart AS dtstart, a.categories AS cal_cats, a.dtstamp AS dtstamp,'
            .'a.description AS description, a.state AS published, a.access AS access, a.catid AS catid,'
			.'a.created AS created, a.created_by AS created_by, a.created_by_alias AS created_by_alias,'
			.'a.state AS published, a.modified AS modified, a.modified_by AS modified_by, a.updated AS updated,'
            .'a.checked_out AS checked_out, a.checked_out_time AS checked_out_time,'
            .'a.metadata AS metadata, a.ordering AS ordering, a.params AS params, a.note AS note');
        $query->select('(SELECT COUNT(*) FROM #__xbjournals_vjournal_attachments AS at WHERE at.entry_id = a.id) AS atcnt' );
            
        $query->from('#__xbjournals_vjournal_entries AS a');
        
        $query->join('LEFT', '#__categories AS cat ON cat.id = a.catid');
        $query->select('cat.title AS category_title');
        
        $query->leftJoin('#__xbjournals_calendars AS cal ON cal.id = a.calendar_id');
        $query->select('cal.title AS cal_title');
        $query->where('a.entry_type = '.$db->q('Journal'));
        
        // Filter by published state
        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
            $query->where('a.state = ' . (int) $published);
        }
      
        // Filter by Journal
        $journal = $app->getUserStateFromRequest('vjid', 'cvjid','');
        $app->setUserState('vjid', '');
        if ($journal == '') {
            $journal = $this->getState('filter.journal');            
        }
        if (is_numeric($journal)) {
            $query->where('a.calendar_id = ' . (int) $journal);
        }
        
        // Filter by category.
        $categoryId = $app->getUserStateFromRequest('catid', 'catid','');
        $app->setUserState('catid', '');
        if ($categoryId=='') {
            $categoryId = $this->getState('filter.category_id');
        }
        if (is_numeric($categoryId)) {
            $query->where($db->quoteName('a.catid') . ' = ' . (int) $categoryId);
        } elseif (is_array($categoryId)) {
            $categoryId = implode(',', $categoryId);
            $query->where($db->quoteName('a.catid') . ' IN ('.$categoryId.')');
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
        
        //filter by tags
        $tagId = $app->getUserStateFromRequest('tagid', 'tagid','');
        $app->setUserState('tagid', '');
        if (!empty($tagId)) {
            $tagfilt = array(abs($tagId));
            $taglogic = $tagId>0 ? 0 : 2;
        } else {
            $tagfilt = $this->getState('filter.tagfilt');
            $taglogic = $this->getState('filter.taglogic');  //0=ANY 1=ALL 2= None
        }
        
        if (empty($tagfilt)) {
            $subQuery = '(SELECT content_item_id FROM #__contentitem_tag_map
 					WHERE type_alias LIKE '.$db->quote('com_xbjournals.journal').')';
            if ($taglogic === '1') {
                $query->where('a.id NOT IN '.$subQuery);
            } elseif ($taglogic === '2') {
                $query->where('a.id IN '.$subQuery);
            }
        } else {
            $tagfilt = ArrayHelper::toInteger($tagfilt);
            $subquery = '(SELECT tmap.tag_id AS tlist FROM #__contentitem_tag_map AS tmap
                WHERE tmap.type_alias = '.$db->quote('com_xbjournals.journal').'
                AND tmap.content_item_id = a.id)';
            switch ($taglogic) {
                case 1: //all
                    for ($i = 0; $i < count($tagfilt); $i++) {
                        $query->where($tagfilt[$i].' IN '.$subquery);
                    }
                    break;
                case 2: //none
                    for ($i = 0; $i < count($tagfilt); $i++) {
                        $query->where($tagfilt[$i].' NOT IN '.$subquery);
                    }
                    break;
                default: //any
                    if (count($tagfilt)==1) {
                        $query->where($tagfilt[0].' IN '.$subquery);
                    } else {
                        $tagIds = implode(',', $tagfilt);
                        if ($tagIds) {
                            $subQueryAny = '(SELECT DISTINCT content_item_id FROM #__contentitem_tag_map
                                WHERE tag_id IN ('.$tagIds.') AND type_alias = '.$db->quote('com_xbjournals.journal').')';
                            $query->innerJoin('(' . (string) $subQueryAny . ') AS tagmap ON tagmap.content_item_id = a.id');
                        }
                    }                   
                    break;
            } //end switch
        } //end if $tagfilt
        
        $orderCol       = $this->state->get('list.ordering', 'title');
        $orderDirn      = $this->state->get('list.direction', 'ASC');
        
        $query->order('parentuid ASC'); //always put subitems at the bottom of the list
        $query->order($db->escape($orderCol.' '.$orderDirn));
        
        return $query;
            
    }
    
    public function getItems() {

        $items  = parent::getItems();
        $tagsHelper = new TagsHelper;
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        if ($items) {
            foreach ($items as $item) {
                $item->atts = XbjournalsHelper::getVjournalAttachments($item->id);
                $item->parent = '';
                if (!is_null($item->parentuid)) $item->parent =  XbjournalsHelper::getVjournalParent($item->parentuid);
                $item->subs = XbjournalsHelper::getVjournalSubitems($item->uid);
                $item->tags = $tagsHelper->getItemTags('com_journals.journal' , $item->id);               
            }
        }
        return $items;
    }
    
     
}

<?php
/*******
 * @package xbJournals Component
 * @filesource admin/models/calendars.php
 * @version 0.0.1.1 21st April 2023
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
    
    /**
     * @name getCalendarJournalItems()
     * @desc gets all the items from the server for the calendar
     * if item updated save new version
     * if item new save it
     * if item deleted mark as archived (keep it in local database, but hide from front-end
     * @param int $calid - the joomla id of the calendar
     * @return array with counts of items, new, changed, same, deleted
     */
    public function getCalendarJournalItems($calid) {
        $cnts = array('local'=>0,'server'=>0,'new'=>0,'same'=>0,'updated'=>0,'deleted'=>0);
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('entry_type, etag, uid, alias')
            ->from($db->qn('#__xbjournals_vjournal_entries'))
            ->where($db->qn('calendar_id').' = '.$db->q($calid)
                .' AND '.$db->qn('entry_type').' = '.$db->q('Journal'));
        $db->setQuery($query);
        $localitems = $db->loadAssocList('id'); 
        if ($localitems) {
            $cnts['local'] = count($localitems);
        }
        $serveritems = XbjournalsHelper::getCalendarJournalEntries($calid);
        if ($serveritems) {
            $cnts['server'] = count($serveritems);
        }
        if ($cnts['server']>0){
            foreach ($serveritems as $sitem) {
                if ($cnts['local'] == 0) {
                    //new item
                    $cnts['new'] = $cnts['server'];
                } else {
                    //put this bit as a function that can also be called fro  getCalendarNoteItems()
                    $key = array_search($sitem['uid'],array_column($localitems,'uid','id'));
                    if ($key===false) {
                        //new item
                        $cnts['new']++;
                    } else {
                        if ($sitem['etag'] == $localitems[$key]['etag']) {
                            //no change
                            $cnts['same']++;
                        } else {
                            //update
                            $cnts['updated']++;
                        }  //end etag matches              
                    } //endif item found                   
               } //endif local items>0
            } //endforeach server item
            // now check for missing items (array_diff on array of uids)
            if ($cnts['local'] > 0 ) {
                $missing = array_diff(array_column($localitems,'uid','id'), array_column($serveritems,'uid'));
                //set status archived
                $cnts['deleted'] = count($missing);               
            }
        } //endif server items>0
        Factory::getApplication()->enqueueMessage('local: '.$cnts['local'].' server: '.$cnts['server']
            .' new: '.$cnts['new'].' same: '.$cnts['same'].' updated: '.$cnts['updated'].' deleted: '.$cnts['deleted']);
    }
    
    function addNewItem($item) {
        $res = false;
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $setarr = array();
        $otherarr = array();
        $otherprops = array();
        foreach ($item as $prop) {
            switch ($prop['property']) {
                case 'etag':
                case 'href':
                case 'uid':
                case 'description':
                case 'location':
                case 'url':
                case 'class':
                case 'status':
                case 'geo':
                case 'comments':
                    //these all have value only no params for now
                    $setarr[] = $db->qn($prop['property']).' = '.json_encode($prop);
                    break;
                case 'attach':
                    //this adds to a separate table and optionally also saves images to /images/xbjournals and docs to /xbjournals/files/
                    break;
                case 'summary':
                    //generate title and alias (checking unique and adding cycle no)
                    break;
                case 'dtstamp':
                case 'dtstart':
                case 'created':
                case 'modified':
                    $datestr = $prop['value'];
                    //input is YYYYMMDD or YYYYMMDDHHMMSS or YYYYMMDDHHMMSSZ
                    //output should be Y-m-d H:i:s (always datetime, with 00:00:00 if date only)
                    //we will ignore timezones for now
                    if (strlen($datestr) < 14) $datestr .= str_repeat('0', 14-strlen($$datestr));
                    $date = DateTime::createFromFormat('YmdHis',$datestr);
                    $datestr = $date->format('Y-m-d H:i:s');
                    $prop['value']=$datestr;
                    //convert string to sql date format
                    break;
                default:
                    //add to others array;
                    $otherarr[] = $prop;
                    break;
            }
            
        }
        $setarr[] = $db->qn('otherprops').' = '.json_encode($otherprops);
        $setarr[] = $db->qn('catid').' = '.$db->q('');
        $setarr[] = $db->qn('state').' = '.$db->q('1');
        $setarr[] = $db->qn('created_by').' = '.$db->q('');
        $setarr[] = $db->qn('note').' = '.$db->q('imported '.date('d M Y'));
        $setarr[] = $db->qn('').' = '.$db->q('');
        $db->getQuery(true);
        $query->insert('#__xbjournals_vjournal_entries')
            ->set($setarr);
        //add catid,state=1,created_by,note='imported date'
        //add to db
        Factory::getApplication()->enqueueMessage($query->dump());
        return $res;
    }
}

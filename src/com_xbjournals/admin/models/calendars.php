<?php
/*******
 * @package xbJournals Component
 * @filesource admin/models/calendars.php
 * @version 0.1.2.5 28th July 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
defined('_JEXEC') or die;

 use Joomla\CMS\Factory;
 use Joomla\CMS\Filter\OutputFilter;
 use Joomla\CMS\Component\ComponentHelper;
// use Joomla\CMS\Toolbar\Toolbar;
// use Joomla\CMS\Toolbar\ToolbarHelper;
// use Joomla\CMS\Language\Text;
// use Joomla\CMS\Layout\FileLayout;

class XbjournalsModelCalendars extends JModelList {
    
    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id', 
                'title', 'a.title',
                'server_title', 's.title',
                'ecnt', 'category_id',
                'category_title', 'cat.title',
                'ordering','a.ordering', 
                'catid', 'a.catid',
                'last_checked', 'a.last_checked', 
                'published','a.state',
                'modified','a.modified');
        }
        parent::__construct($config);
    }
    
    protected function populateState($ordering = 'server_title', $direction = 'asc') {
        
        $app = Factory::getApplication();
        $filt = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $filt);
        $filt = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
        $this->setState('filter.published', $filt);
        $filt = $this->getUserStateFromRequest($this->context . '.filter.category_id', 'filter_category_id');
        $this->setState('filter.categoryId', $filt);
//         $filt = $this->getUserStateFromRequest($this->context . '.filter.tagfilt', 'filter_tagfilt', '');
//         $this->setState('filter.tagfilt', $filt);
//         $filt = $this->getUserStateFromRequest($this->context . '.filter.taglogic', 'filter_taglogic');
//         $this->setState('filter.taglogic', $filt);
        //         $filt = $this->getUserStateFromRequest($this->context . '.filter.', 'filter_');
        //         $this->setState('filter.', $filt);
        
        $formSubmited = $app->input->post->get('form_submited');
        
        if ($formSubmited)
        {
            $categoryId = $app->input->post->get('category_id');
            $this->setState('filter.category_id', $categoryId);
            
//             $tagfilt = $app->input->post->get('tagfilt');
//             $this->setState('filter.tagfilt', $tagfilt);
        }
        parent::populateState($ordering, $direction);
    }
    
    protected function getListQuery() {
        $db = Factory::getDbo();
        $app = Factory::getApplication();
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
        
        $query->join('LEFT', '#__categories AS cat ON cat.id = a.catid');
        $query->select('cat.title AS category_title');
        
        //ignore local calendars
        $query->where('a.cal_url <> '.$db->q(''));
        
        // Filter by published state
        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
            $query->where('a.state = ' . (int) $published);
        }
        // Filter by server
        $server = $this->getState('filter.server');
        if (is_numeric($server)) {
            $query->where('a.server_id = ' . (int) $server);
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
        
        $orderCol       = $this->state->get('list.ordering', 'server_title');
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
                    $query->select('id, title, entry_type, dtstart')
                        ->from('#__xbjournals_vjournal_entries')
                        ->where($db->qn('calendar_id').' = '.$db->q($item->id))
                        ->order('dtstart DESC');
                    $query->setLimit(5);    
                    $db->setQuery($query);
                    $item->entries = $db->loadAssocList();
                }
            }
        }
        
        return $items;
    }
    
    public function getLocalItems(int $calid, $entrytype ='') {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id, entry_type, etag, uid, dtstamp')
            ->from($db->qn('#__xbjournals_vjournal_entries'))
            ->where($db->qn('calendar_id').' = '.$db->q($calid));
        if ($entrytype != '') $query->andWhere($db->qn('entry_type').' = '.$db->q($entry_type));
        $db->setQuery($query);
        $localitems = $db->loadAssocList('id');
        return $localitems;
    }
    

//     public function importNewItems(int $calid) {
//         $db = Factory::getDbo();
//         $query = $db->getQuery(true);
//         $query->select('last_checked')->from($db->qn('#__xbjournals_calendars'))
//             ->where($db->qn('id').' = '.$db->q($calid));
//         $db->setQuery($query);
//         $last = $db->loadResult();
//         return $this->importJournalItems($calid, $last);
//     }
     
    /**
     * @name importJournalItems()
     * @desc imports items for calendar from server
     * if item updated save new version
     * if item new save it
     * if item deleted mark as archived (keep it in local database, but hide from front-end
     * @param int $calid - the joomla id of the calendar
     * @return array with counts of items, new, changed, same, deleted
     */
    public function importJournalItems(int $calid, $startstr = null, $endstr = null, $dateprop = "DTSTAMP") {
        $cnts = array('local'=>0,'server'=>0,'new'=>0,'same'=>0,'updated'=>0,'missing'=>0,'archived'=>0);
        $res = false;
        $db = Factory::getDbo();
        $localitems = $this->getLocalItems($calid);
        if ($localitems) {
            $cnts['local'] = count($localitems);
        }
        $start = ($startstr != '') ? XbjournalsHelper::date2VcalDate($startstr) : null;
        if ($endstr != '') {
            if (strlen($endstr) < 11) $endstr .= ' 23:59:59'; //make end at end of day to include current day
            $end = XbjournalsHelper::date2VcalDate($endstr);
        } else { 
            $end = null; 
        }
        $serveritems = XbjournalsHelper::getCalendarVJournalItems($calid, $start, $end, $dateprop);
        if ($serveritems) {
            $cnts['server'] = count($serveritems);
        }
//        $uidarr = array();
        if ($cnts['server']>0){
            foreach ($serveritems as $serveritem) {
                $serveritem['calid'] = $calid;
                if ($cnts['local'] == 0) {
                    //trivial case, all items must be new
                    $res = $this->addNewItem($serveritem, $calid);
                    if ($res) $cnts['new']++;
                } else {
                    //get the uid and etag      
                    $etag = $serveritem['etag']['value'];
                    $uid = $serveritem['uid']['value'];
//                    $uidarr[] = $uid;
                    $key = array_search($uid,array_column($localitems,'uid','id'));
                    if ($key===false) {
                        //new item
                        $res = $this->addNewItem($serveritem, $calid);
                        if ($res) $cnts['new']++;
                    } else {  //uid matches
                        if ($etag == $localitems[$key]['etag']) {
                            //no change
                            $cnts['same']++;
                            //update entry updated
                            $query = $db->getQuery(true);
                            $query->update($db->qn('#__xbjournals_vjournal_entries'))->set('updated = '.$db->q(date("Y-m-d H:i:s")))
                            ->where($db->qn('id').' = '.$db->q($localitems[$key]['id']));
                            $db->setQuery($query);
                            try {
                                $res = $db->execute();
                            } catch (Exception $e) {
                                XbjournalsHelper::doError('Problem updating Entry '.$calid.' updated datetime', $e);
                            }
                            
                        } else {
                            // test dtstamp to check an old version hasn't been restored on db
                            $localstamp = strtotime($localitems[$key]['dtstamp']);
                            $serverstamp = strtotime($serveritem['dtstamp']['value']);
                            if ($serverstamp>$localstamp) {
                                //delete old item and add updated item as new
                                //TODO for safety hold copy on old item until item has been updated(added)
                                $query = $db->getQuery(true);
                                $query->delete($db->qn('#__xbjournals_vjournal_entries'))->where($db->qn('id').' = '. $db->q($key));
                                $db->setQuery($query);
                                try {
                                    $res = $db->execute();                                   
                                } catch (Exception $e) {
                                    XbjournalsHelper::doError('Error deleting item #'.$key.' from database',$e);
                                }
                                if ($res) $res = $this->addNewItem($serveritem, $calid);
                                if ($res) $cnts['updated']++;
                            }
                        }  //end etag matches              
                    } //endif item found                   
               } //endif local items>0
            } //endforeach server item
        } //endif server items>0
//        $query = $db->getQuery(true);
//        $query->update($db->qn('#__xbjournals_calendars'))->set('last_checked = '.$db->q(date("Y-m-d H:i:s")))
//            ->where($db->qn('id').' = '.$db->q($calid));
//        $db->setQuery($query);
//        try {
//            $res = $db->execute();    
//        } catch (Exception $e) {
//            XbjournalsHelper::doError('Problem updating Calendar '.$calid.' last_checked datetime', $e);
//        }
        Factory::getApplication()->enqueueMessage($cnts['local'].' local, '.$cnts['server'].' server, '
            .$cnts['new'].' new, '.$cnts['same'].' same, '.$cnts['updated'].' updated, '.$cnts['missing'].' missing, '.$cnts['archived'].' archived ');
        return $cnts;
    }
    
    function addNewItem(array $item, int $calid) {
        $params = ComponentHelper::getParams('com_xbjournals');
        $newid = false;
        $db = Factory::getDbo();
        $insertarr = array();
        $itemparams = array();
        $attachments = array();
        $comments= array();
        $attendees=array();
        $otherprops = array();
        $categories = '';
        $title = '';
        $desc = '';
        $summary = '';
        $catid = 0;
        $uid = '';
        $entrytype = (array_key_exists('dtstart', $item)) ? 'Journal' : 'Note';
        $catparent = ($entrytype == 'Journal') ? $params->get('def_entcat',0) : $params->get('def_notecat',0);
        foreach ($item as $prop) {
            switch ($prop['property']) {
                case 'uid':
                    $uid = $prop['value'];
                case 'etag':
                case 'href':
                case 'sequence':
                case 'description':
                case 'location':
                case 'geo':
                case 'url':
                    $insertarr[] = array('col'=>$db->qn($prop['property']),'val'=>$db->q($prop['value']));
                    if (!empty($prop['params'])) {
                        $itemparams[] = array('property'=>$prop['property'],'params'=>$prop['params']);
                    }
                    break;
                case 'description':
                    //check if we already have a description (multiples are allowed, we'll only use first
                    //TODO check language parameter
                    if ($desc != '') {
                        $otherprops[] = $prop;
                    } else {
                        $desc = $prop['value'];
                        $insertarr[] = array('col'=>$db->qn('description'),'val'=>$db->q($prop['value']));
                        if (!empty($prop['params'])) {
                            $itemparams[] = array('property'=>$prop['property'],'params'=>$prop['params']);
                        }
                    }
                    break;
                //TODO check incoming  X-DESC or whatever
                case 'summary':
                    $summary = $prop['value'];
                    $insertarr[] = array('col'=>$db->qn($prop['property']),'val'=>$db->q($prop['value']));
                    if (!empty($prop['params'])) {
                        $itemparams[] = array('property'=>$prop['property'],'params'=>$prop['params']);
                    }
                    break;
                case 'categories':
                    $categories .= $prop['value'].','; //ignoring params
                    break;
                case 'comment':
                    $comments[] = $prop;
                    break;
                case 'attendee':
                    $attendees[] = $prop; 
                    break;
                case 'attach':
                    $attachments[] = $prop;
                    break;
                case 'last-modified':
                    $prop['property'] = 'modified';
                case 'dtstamp':  
                case 'dtstart':
                case 'created':
                    $datestr = $this->vCalDate2SqlDate($prop['value']);
                    $insertarr[] = array('col'=>$db->qn($prop['property']),'val'=>$db->q($datestr));
                    if (!empty($prop['params'])) {
                        $itemparams[] = array('property'=>$prop['property'],'params'=>$prop['params']);
                    }
                    break;
                case 'related-to':
                    $paramstr = strtolower(implode(',',$prop['params']));
                    if ((strpos($paramstr,'reltype') === false) || (strpos($paramstr,'parent') !== false)) {
                        $insertarr[] = array('col'=>$db->qn('parentuid'),'val'=>$db->q($prop['value']));
                    }
                    $otherprops[] = $prop;
                    break;
                case 'class':
                    //test for param and map to access if required
                    if ($params->get('map_vjclass_access',0)==1) {
                        switch (strtolower($prop['value'])) {
                            case 'private':
                                $insertarr[] = array('col'=>$db->qn('access'), 'val'=>$db->q(3));
                                break;
                            case 'confidential':
                                $insertarr[] = array('col'=>$db->qn('access'), 'val'=>$db->q(4));
                                break;                               
                            default:
                                $insertarr[] = array('col'=>$db->qn('access'), 'val'=>$db->q(1));
                                break;
                        }
                    }
                    $insertarr[] = array('col'=>$db->qn('class'), 'val'=>$db->q($prop['value']));
                    if (!empty($prop['params'])) {
                        $itemparams[] = array('property'=>$prop['property'],'params'=>$prop['params']);
                    }
                    break;
                case 'status':
                    if ($params->get('map_vjstatus_cat',0)==1) {
                        //todo get journal root cat param
                        $catid = XbjournalsHelper::createCategory($prop['value'],'',$catparent,'com_xbjournals','created from vJournal status');
                    }
                    $insertarr[] = array('col'=>$db->qn('status'), 'val'=>$db->q($prop['value']));
                    if (!empty($prop['params'])) {
                        $itemparams[] = array('property'=>$prop['property'],'params'=>$prop['params']);
                    }
                    break;
                case 'x-status':
                    if ($params->get('map_vjstatus_cat',0)==2) {
                        //todo get journal root cat param
                        $catid = XbjournalsHelper::createCategory($prop['value'],'',$catparent,'com_xbjournals','created from vJournal x-status');                        
                    }
                    $insertarr[] = array('col'=>$db->qn('x-status'), 'val'=>$db->q($prop['value']));
                    if (!empty($prop['params'])) {
                        $itemparams[] = array('property'=>$prop['property'],'params'=>$prop['params']);
                    }
                    break;
                default:
                    //add any properties we don't use to a single json encoded column
                    $otherprops[] = $prop;
                    break;
            } //endswitch $item['property']           
        } // endforeach $item
        //add title and alias
        //generate title and alias (checking unique and adding cycle no)
        $title = $summary;
        if ($title == '') {
            $title = ($desc='') ? 'missing summary & desc' : XbjournalsHelper::truncateToText($desc, 100, 'firstsent', false);
        }
        
        $cycle = 0;
        $alias = OutputFilter::stringURLSafe($title);
        $test = $alias;
        //make alias unique
        while (XbjournalsHelper::checkValueExists($test,'#__xbjournals_vjournal_entries','alias')) {
            $cycle ++;
            $test = $alias.'-'.sprintf("%02d", $cycle);
        }
        $alias = $test;
        $insertarr[] = array('col'=>$db->qn('title'),'val'=>$db->q($title));
        $insertarr[] = array('col'=>$db->qn('alias'),'val'=>$db->q($alias));
        
        //add updated
        $insertarr[]=array('col'=>$db->qn('updated'),'val'=>$db->q(date("Y-m-d H:i:s")));
        
        $insertarr[]=array('col'=>$db->qn('entry_type'),'val'=>$db->q($entrytype));
        if ($catid == 0) {
            $catid = XbjournalsHelper::createCategory('Uncategorised');
        }
        $insertarr[] = array('col'=>$db->qn('catid'), 'val'=>$db->q($catid));
        
        //deal with properties that might have had multiple entries
        $categories = trim($categories,", ");
        if ($categories) 
            //todo convert to tags after saving as need item id
            $insertarr[] = array('col'=>$db->qn('categories'),'val'=>$db->q($categories));
        if (!empty($attendees)) 
            //?todo check for local users
            $insertarr[] = array('col'=>$db->qn('attendees'),'val'=>$db->q(json_encode($attendees)));
        if (!empty($comments))
            $insertarr[] = array('col'=>$db->qn('comments'),'val'=>$db->q(json_encode($comments)));
        if (!empty($otherprops)) 
            $insertarr[] = array('col'=>$db->qn('otherprops'),'val'=>$db->q(json_encode($otherprops)));
        
        // save all ofthe propertyparameters for recovery when syncing back
        if (!empty($itemparams)) 
            $insertarr[] = array('col'=>$db->qn('itemparams'),'val'=>$db->q(json_encode($itemparams)));

        $insertarr[] = array('col'=>$db->qn('state'),'val'=>$db->q('1'));
        $insertarr[] = array('col'=>$db->qn('created_by'),'val'=>$db->q(Factory::getUser()->id));
        $insertarr[] = array('col'=>$db->qn('note'),'val'=>$db->q('imported '.date('d M Y')));
        $insertarr[] = array('col'=>$db->qn('calendar_id'),'val'=>$db->q($item['calid']));
        $query = $db->getQuery(true);
        $query->insert('#__xbjournals_vjournal_entries')
            ->columns(array_column($insertarr,'col'))->values(implode(',',array_column($insertarr,'val')));
       $db->setQuery($query);
       try {
           $db->execute();
           $newid = $db->insertid();
       } catch (Exception $e) {
           $this->doError('Error inserting to database',$e);
       }
        //TODO add tags using $newid
       if (!empty( $attachments)) {
           $this->insertAttachments($newid, $uid, $attachments);
       }
       
       return $newid;
    }
    
    /**
     * @name function insertAttachments()
     * @desc
     * @param int $itemid - the id of the entry to link the attachments to
     * @param array $attachments - array of ('params'=>string from the 
     * @return mixed[]
     */
    function insertAttachments(int $itemid, string $uid, array $attachments) {
        $db = Factory::getDbo();
        $attpath = '/images/xbjournals/'; //TODO get component param for this
        $attids = array();
        $query = $db->getQuery(true);
        $query->select('atthash')->from($db->qn('#__xbjournals_vjournal_attachments'));
        $query->where($db->qn('entry_id').' = '.$db->q($itemid));
        $db->setQuery($query);
        $hashes = $db->loadColumn();
        $hashes = array_flip($hashes);
        foreach ($attachments as $attach) {
            //nedd to check if attachment already exists
            $insertarr = array();
            $insertarr[] = array('col'=>$db->qn('entry_id'),'val'=>$db->q($itemid));
            $otherparams = array();
            $hasblob = false;
            $fname = '';
            $hashstr = '';
            foreach ($attach['params'] as $param) {
                //need to handle situation if embedded file has same filename as another
                $pname = strtolower(substr($param,0,strpos($param,'=')));
                $pvalue = substr($param,strpos($param,'=')+1);
                $fname = ''; $xlabel = ''; $labelok = false;
                $fmttype = '';
                switch ($pname) {
                    case 'filename':
                        $fname = $pvalue;
                        $hashstr .= $pvalue;
                        break;
                    case 'x-label':
                        $xlabel = $pvalue;
                        $otherparams[] = array($pname=>$pvalue);
                        break;
                    case 'value':
                        $insertarr[] = array('col'=>$db->qn($pname),'val'=>$db->q($pvalue));
                        if (strtolower($pvalue) == 'binary') {
                            $hasblob = true;
                            $insertarr[] = array('col'=>$db->qn('inline_data'),'val'=>$db->q($attach['value']));
                        }
                        $hashstr .= $pvalue;
                        break;
                    case 'fmttype':
                        $fmttype = $pvalue;
 //                       $insertarr[] = array('col'=>$db->qn($pname),'val'=>$db->q($pvalue));
                        $hashstr .= $pvalue;
                        break;
                    case 'encoding':
                        $insertarr[] = array('col'=>$db->qn($pname),'val'=>$db->q($pvalue));
                        $hashstr .= $pvalue;
                        break;
                    case 'label':
                        $labelok = true;
                        $insertarr[] = array('col'=>$db->qn($pname),'val'=>$db->q($pvalue));
                        break;
                    default:
                        $otherparams[] = array($pname=>$pvalue);
                    break;
                }
            } //endforeach param
            if ($fname == '') {
                if ($xlabel != '') {
                    $fname = $xlabel;
                } elseif ($hasblob) {
                    $fname = 'file-'.date_format(getDate(),'YmdHis');
                } elseif ($attach['value'] != '') {
                    $fname = basename(parse_url($attach['value'],PHP_URL_PATH));
                }
            }
            //check if remote file that it is a file! Look for extension as simple test,also guess mime type if fmttype not present
            if ($fname != '') {
                //make filename unique
                $cnt = 0;
                $lname = $fname;
                while (file_exists(JPATH_ROOT.$attpath.$lname)) {
                    $cnt++;
                    $lname = pathinfo($fname, PATHINFO_FILENAME).'-'.sprintf("%02d", $cnt).'.'.pathinfo($fname, PATHINFO_EXTENSION);
                }
//                $fname = $tname;                
                $insertarr[] = array('col'=>$db->qn('filename'),'val'=>$db->q($fname));
            }
            if (!$labelok) {
                $label = $fname;
                $label .= ($cnt>0) ? ' ('.sprintf("%02d", $cnt).')' : '';
                $insertarr[] = array('col'=>$db->qn('label'),'val'=>$db->q($label));                
            }
            if ((!$hasblob) && ($attach['value'] != '')) {
                $insertarr[] = array('col'=>$db->qn('uri'),'val'=>$db->q($attach['value']));   
                $hashstr .= $pvalue;
            }
            if (!empty($otherparams)) {
                $insertarr[] = array('col'=>$db->qn('otherparams'),'val'=>$db->q(json_encode($otherparams)));
            }
            $atthash = hash("sha256",$hashstr);
            if (!array_key_exists($atthash,$hashes)) {
                $insertarr[] = array('col'=>$db->qn('atthash'),'val'=>$db->q($atthash));                
                $query = $db->getQuery(true);
                $query->insert('#__xbjournals_vjournal_attachments')
                ->columns(array_column($insertarr,'col'))->values(implode(',',array_column($insertarr,'val')));
                $db->setQuery($query);
                try {
                    $db->execute();
                    $attid = $db->insertid();
                    $attids[] = $attid;
                } catch (Exception $e) {
                    $this->doError('Error inserting to database',$e);
                }
                //now save file attachment if there is one. make sure its a unique name
                $localpath = '';
                if ($hasblob) {
                    try {
                        $data = base64_decode( $attach['value'] );                    
                        $bcnt = file_put_contents(JPATH_ROOT.$attpath.$lname, $data);
                    } catch (Exception $e) {
                        $this->doError('Error saving attachment '.$attpath.$lname,$e);
                    }
                    if ($bcnt) {
                        Factory::getApplication()->enqueueMessage('Embedded attachment '.$fname.' saved as '.$attpath.$lname.'&nbsp;&nbsp;Size:'.$bcnt.' bytes');
                        $localpath = $attpath.$lname;
                        $info = system("file -b '".JPATH_ROOT.$attpath.$lname."'");
                        if ($fmttype == '') $fmttype = mime_content_type(JPATH_ROOT.$attpath.$lname);
                    } else {
                        Factory::getApplication()->enqueueMessage('Error; attachment not saved '.$attpath.$fname,'Warning');
                    }
                } else {
                    //TODO provide component parameter to allow this
                    //if we have a uri and we have a filename and the destination doesn't exist and the source has a mime type
                    $res = false;
                    if (filter_var($attach['value'], FILTER_VALIDATE_URL)) { // is valid url
                        $testfile = fopen($attach['value'],"r");
                        if ($testfile) { //is a file we can open
                            fclose($testfile);
                            $res = copy($attach['value'], JPATH_ROOT.$attpath.$lname);
                            if ($fmttype == '') $fmttype = mime_content_type(JPATH_ROOT.$attpath.$lname);
                            if ($res) {
                                Factory::getApplication()->enqueueMessage('Copied '.$attach['value'].' to '.$attpath.$lname); 
                                $localpath = $attpath.$lname;
                            } else {
                                Factory::getApplication()->enqueueMessage('Problem copying remote file to local storage '.$attpath.$fname,'Warning');
                            }
                        } else {
                            Factory::getApplication()->enqueueMessage('Could not open remote file for copying '.$attach['value'],'Warning');
                        }
                    } else {
                        Factory::getApplication()->enqueueMessage('Invalid url for remote file '.$attach['value'],'Warning');
                    }
                }
                if ($localpath != '') {
                    $query = $db->getQuery(true);
                    $query->update('#__xbjournals_vjournal_attachments')
                    ->set($db->qn('localpath').' = '.$db->q($localpath))
                    ->set($db->qn('info').' = '.$db->q($info))
                    ->set($db->qn('fmttype').' = '.$db->q($fmttype))
                    ->where($db->qn('id').' = '.$db->q($attid));
                    $db->setQuery($query);
                    try {
                        $db->execute();
                    } catch (Exception $e) {
                        $this->doError('Error updating localpath in database',$e);
                    }
                }
            } //endif $atthash doesn't exist
        } //endforeach attach
        return $attids;
    }
                
    function vCalDate2SqlDate (string $datestr) {
        //input should be YYYYMMDD or YYYYMMDDTHHMMSS or YYYYMMDDTHHMMSSZ
        //output should be Y-m-d H:i:s (always datetime, with 00:00:00 if date only)
        //TODO timezones
        $datestr = str_replace('T',' ',$datestr);
        // $datestr = str_replace(array('T','Z'), ' ', $datestr);
        if (strlen($datestr) < 14) $datestr .= '000000';
        //convert string to sql date format
        $datestr = trim($datestr,' Z');
        $date = DateTime::createFromFormat('Ymd His',$datestr);
        $datestr = $date->format('Y-m-d H:i:s');
        return $datestr;
    }
    
//     function doError(string $message, Exception $e) {
//         Factory::getApplication()->enqueueMessage($message.'<br />' .$e->getMessage().' ('.$e->getCode().')','Error');
//     }
}

<?php
/*******
 * @package xbJournals Component
 * @filesource admin/models/calendars.php
 * @version 0.0.4.2 11th May 2023
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
    
    /**
     * @name importJournalItems()
     * @desc imports items for calendar from server
     * if item updated save new version
     * if item new save it
     * if item deleted mark as archived (keep it in local database, but hide from front-end
     * @param int $calid - the joomla id of the calendar
     * @return array with counts of items, new, changed, same, deleted
     */
    public function importJournalItems(int $calid) {
        $cnts = array('local'=>0,'server'=>0,'new'=>0,'same'=>0,'updated'=>0,'missing'=>0,'archived'=>0);
        $res = false;
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id, entry_type, etag, uid, dtstamp')
            ->from($db->qn('#__xbjournals_vjournal_entries'))
            ->where($db->qn('calendar_id').' = '.$db->q($calid));
//                .' AND '.$db->qn('entry_type').' = '.$db->q('Journal'));
        $db->setQuery($query);
        $localitems = $db->loadAssocList('id'); 
        if ($localitems) {
            $cnts['local'] = count($localitems);
        }
        $serveritems = XbjournalsHelper::getCalendarJournalEntries($calid);
        if ($serveritems) {
            $cnts['server'] = count($serveritems);
        }
        $uidarr = array();
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
                    $uidarr[] = $uid;
//                     foreach ($sitem as $line) {
//                         if ($line['property']=='uid') {
//                             $uid = $line['value'];
//                         }
//                         if ($line['property']=='etag') $etag = $line['value'];
//                     }                       
                    $key = array_search($uid,array_column($localitems,'uid','id'));
                    if ($key===false) {
                        //new item
                        $res = $this->addNewItem($serveritem, $calid);
                        if ($res) $cnts['new']++;
                    } else {  //uid matches
                        if ($etag == $localitems[$key]['etag']) {
                            //no change
                            $cnts['same']++;
                        } else {
                            // test dtstamp to check an old version hasn't been restored on db
                            $localstamp = strtotime($localitems[$key]['dtstamp']);
                            $serverstamp = strtotime($serveritem['dtstamp']['value']);
                            if ($serverstamp>$localstamp) {
                                $query = $db->getQuery(true);
                                $query->delete($db->qn('#__xbjournals_vjournal_entries'))->where($db->qn('id').' = '. $db->q($key));
                                $db->setQuery($query);
                                try {
                                    $res = $db->execute();                                   
                                } catch (Exception $e) {
                                    $this->doError('Error deleting item #'.$key.' from database',$e);
                                }
                                if ($res) $res = $this->addNewItem($serveritem, $calid);
                                if ($res) $cnts['updated']++;
                            }
                        }  //end etag matches              
                    } //endif item found                   
               } //endif local items>0
            } //endforeach server item
//             // now check for missing items (array_diff on array of uids)
//             if ($cnts['local'] > 0 ) {
//                 //we should only do thisifwe have allof the items from the server or are using the same filter to check local
//                 $missing = array_diff(array_column($localitems,'uid','id'), $uidarr);
//                 //set status archived
//                 $cnts['missing'] = count($missing);
//                 foreach ($missing as $key) {
//                     $query = $db->getQuery(true);
//                     $query->select('`state`')->from($db->qn('#__xbjournals_vjournal_entries'))->where($db->qn('uid').' = '. $db->q($key));
//                     $db->setQuery($query);
//                     $res = $db->loadResult();
//                     if ($res != 2) {
//                         $query->clear();
//                         $query->update($db->qn('#__xbjournals_vjournal_entries'))->set($db->qn('state').' = '.$db->q('2'))
//                             ->where($db->qn('uid').' = '. $db->q($key).' AND '.$db->qn('state').' = '.$db->q('1'));  
//                         $db->setQuery($query);
//                         try {
//                             $res = $db->execute();
//                         } catch (Exception $e) {
//                             $this->doError('Error archiving item #'.$key,$e);
//                         }
//                         //TODO move archiveditems to local calendar by setting calid ot local cal
//                         if ($res) $cnts['archived'] ++; //= count($missing);                                      
//                     }
//                 }
//             }
        } //endif server items>0
        Factory::getApplication()->enqueueMessage($cnts['local'].' local, '.$cnts['server'].' server, '
            .$cnts['new'].' new, '.$cnts['same'].' same, '.$cnts['updated'].' updated, '.$cnts['missing'].'missing, '.$cnts['archived'].' archived, ');
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
        $descdone = false;
        $statdone = false;
        $cat = '';
        $uid = '';
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
                    //check if we already have a description
                    if ($descdone) {
                        $otherprops[] = $prop;
                    } else {
                        $insertarr[] = array('col'=>$db->qn($prop['property']),'val'=>$db->q($prop['value']));
                        if (!empty($prop['params'])) {
                            $itemparams[] = array('property'=>$prop['property'],'params'=>$prop['params']);
                        }
                        $descdone = true;
                    }
                    break;
                case 'summary':
                    //generate title and alias (checking unique and adding cycle no)
                    $insertarr[] = array('col'=>$db->qn($prop['property']),'val'=>$db->q($prop['value']));
                    $cycle = 0;
                    $title = $prop['value'];
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
                    if (!empty($prop['params'])) {
                        $itemparams[] = array('property'=>$prop['property'],'params'=>$prop['params']);
                    }
                    break;
                case 'categories':
                    $categories .= $prop.','; //ignoring params
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
                    if ($params->get('jclass',0)==1) {
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
                    $otherprops[] = $prop;                   
                    break;
                case 'status':
                    if ($params->get('jstatus',0)==1) {
                        if ($statdone == false) {
                            //todo get journal root cat param
                            $catid = XbjournalsHelper::createCategory($prop['value'],'',1,'com_xbjournals','created from vJournal status');
                            $cat = array('col'=>$db->qn('catid'), 'val'=>$db->q($catid));
                        }
                    }
                    $insertarr[] = array('col'=>$db->qn('status'), 'val'=>$db->q($prop['value']));
                    if (!empty($prop['params'])) {
                        $itemparams[] = array('property'=>$prop['property'],'params'=>$prop['params']);
                    }
                    break;
                case 'x-status':
                    //test for param an map to category id if required, creating category if it doesn't exist
                    //if x-status take it, if status check for existing status vlaue first and only take if none
                    if ($params->get('jstatus',0)==1) {
                        //todo get journal root cat param
                        $catid = XbjournalsHelper::createCategory($prop['value'],'',1,'com_xbjournals','created from vJournal status');                        
                        $cat = array('col'=>$db->qn('catid'), 'val'=>$db->q($catid));
                        $statdone = true;
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
        
        $entrytype = (array_key_exists('dtstart', $item)) ? 'Journal' : 'Note';
        $insertarr[]=array('col'=>$db->qn('entry_type'),'val'=>$db->q($entrytype));
        if (!$statdone) {
            $catid = XbjournalsHelper::createCategory('Uncategorised');
            $cat = array('col'=>$db->qn('catid'), 'val'=>$db->q($catid));
        }
        if ($cat) {
            $insertarr[] = $cat;
        }
        if ($categories) 
            $insertarr[] = array('col'=>$db->qn('categories'),'val'=>$db->q($categories));
        if (!empty($attendees)) 
            $insertarr[] = array('col'=>$db->qn('attendees'),'val'=>$db->q(json_encode($attendees)));
        if (!empty($comments))
            $insertarr[] = array('col'=>$db->qn('comments'),'val'=>$db->q(json_encode($comments)));
        if (!empty($otherprops)) 
            $insertarr[] = array('col'=>$db->qn('otherprops'),'val'=>$db->q(json_encode($otherprops)));
        if (!empty($itemparams)) 
            $insertarr[] = array('col'=>$db->qn('itemparams'),'val'=>$db->q(json_encode($itemparams)));

//            $insertarr[] = array('col'=>$db->qn('catid'),'val'=>$db->q('0'));
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
                        $insertarr[] = array('col'=>$db->qn($pname),'val'=>$db->q($pvalue));
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
            if ($fname != '') {
                //make filename unique
                $cnt = 0;
                while (file_exists($attpath.$fname.($cnt>0) ? sprintf("%02d", $cnt) : '')) {
                    $cnt++;
                }
                if ($cnt>0) $fname = $fname.'-'.sprintf("%02d", $cnt);                
                $insertarr[] = array('col'=>$db->qn('filename'),'val'=>$db->q($fname));
            }
            if (!$labelok) {
                $insertarr[] = array('col'=>$db->qn('label'),'val'=>$db->q('Attachment for item #'.$itemid));                
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
                        $bcnt = file_put_contents(JPATH_ROOT.$attpath.$fname, $data);
                    } catch (Exception $e) {
                        $this->doError('Error saving attachment '.$attpath.$fname,$e);
                    }
                    if ($bcnt) {
                        Factory::getApplication()->enqueueMessage('Attachment saved '.$attpath.$fname.'&nbsp;&nbsp;Size:'.$bcnt.' bytes');
                        $localpath = $attpath.$fname;
                    } else {
                        Factory::getApplication()->enqueueMessage('Error; attachment not saved '.$attpath.$fname,'Warning');
                    }
                } else {
                    //TODO provide component parameter to allow this
                    //if we have a uri and we have a filename and the destination doesn't exist
                    if ((filter_var($attach['value'], FILTER_VALIDATE_URL)) && ($fname) && (!file_exists($fname))) {
                        $res = copy($attach['value'], JPATH_ROOT.$attpath.$fname);
                    }
                    if ($res) {
                        Factory::getApplication()->enqueueMessage('Copied '.$attach['value'].' to '.$attpath.$fname); 
                        $localpath = $attpath.$fname;
                    } else {
                        Factory::getApplication()->enqueueMessage('Problem copying remote file to local storage '.$attpath.$fname,'Warning');
                    }
                }
                if ($localpath != '') {
                    $query = $db->getQuery(true);
                    $query->update('#__xbjournals_vjournal_attachments')
                    ->set($db->qn('localpath').' = '.$db->q($localpath))
                        ->where($db->qn('id').' = '.$db->q($attid));
                    $db->setQuery($query);
                    try {
                        $db->execute();
                    } catch (Exception $e) {
                        $this->doError('Error updating localpath in database',$e);
                    }
                }
            }
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
    
    function doError(string $message, Exception $e) {
        Factory::getApplication()->enqueueMessage($message.'<br />' .$e->getMessage().' ('.$e->getCode().')','Error');
    }
}

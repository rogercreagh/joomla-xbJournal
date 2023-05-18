<?php
/*******
 * @package xbJournals Component
 * @filesource admin/models/dashboard.php
 * @version 0.0.5.4 18th May 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;

class XbjournalsModelDashboard extends JModelList {
    
    public function __construct() {       
        parent::__construct();
    }
          
    public function getServers() {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        $query->select('a.id AS id, a.title AS title, a.alias AS alias,'
            .'a.url AS url, a.username AS username, a.password AS password,'
            .'a.description AS description, a.state AS published, a.access AS access,'
			.'a.created AS created, a.created_by AS created_by, a.created_by_alias AS created_by_alias,'
			.'a.modified AS modified, a.modified_by AS modified_by,'
            .'a.checked_out AS checked_out, a.checked_out_time AS checked_out_time,'
            .'a.metadata AS metadata, a.ordering AS ordering, a.params AS params, a.note AS note');
        $query->select('(SELECT COUNT(DISTINCT(c.id)) FROM #__xbjournals_calendars AS c WHERE c.server_id = a.id) AS calcnt');
        $query->from('#__xbjournals_servers AS a');
 //       $query->leftJoin('#__xbjournals_calendars AS c ON c.server_id = a.id');
        $query->order('title ASC');
        $db->setQuery($query);
        $servers = $db->loadObjectList();
        return $servers;    
    }
    
    public function getCalendars() {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        $query->select('a.id AS id, a.title AS title, a.alias AS alias, a.state AS published,'
            .'a.last_checked AS last_checked, a.catid AS catid, a.note AS note,'
            .'a.created AS created, a.created_by AS created_by, a.created_by_alias AS created_by_alias,'
            .'a.modified AS modified, a.modified_by AS modified_by,'
            .'a.cal_components AS components, s.title AS server');
        $query->select('(SELECT COUNT(DISTINCT(e.id)) FROM #__xbjournals_vjournal_entries AS e WHERE e.calendar_id = a.id AND e.entry_type = '.$db->q('Journal').') AS jentcnt');
        $query->select('(SELECT COUNT(DISTINCT(f.id)) FROM #__xbjournals_vjournal_entries AS f WHERE f.calendar_id = a.id AND f.entry_type = '.$db->q('Note').') AS nentcnt');
        $query->from('#__xbjournals_calendars AS a');
        $query->leftJoin('#__xbjournals_servers AS s ON s.id = a.server_id ');
        
        
        $query->order('s.title ASC, a.title ASC');
        $db->setQuery($query);
        $servers = $db->loadObjectList();
        return $servers;
    }
    
    public function getClient() {
        $result = array();
        $client = Factory::getApplication()->client;
        $class = new ReflectionClass('Joomla\Application\Web\WebClient');
        $constants = array_flip($class->getConstants());
        
        $result['browser'] = $constants[$client->browser].' '.$client->browserVersion;
        $result['platform'] = $constants[$client->platform].($client->mobile ? ' (mobile)' : '');
        $result['mobile'] = $client->mobile;
        return $result;
    }
    
    public function getJournalStates() {
        $cnts = array('total'=>0,'published'=>0,'unpublished'=>0,'archived'=>0,'trashed'=>0);
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        return $cnts;
    }

    public function getNotebookStates() {
        $cnts = array('total'=>0,'published'=>0,'unpublished'=>0,'archived'=>0,'trashed'=>0);
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        return $cnts;
    }
    
    public function getAttachmentCounts() {
        $cnts = array('total'=>0, 'journals'=>0, 'notes'=>0, 'calendars'=>0, 'servers'=>0, 'embed'=>0, 'remote'=>0, 'rem2local'=>0);
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('a.id, a.uri AS uri, a.value AS value, a.localpath AS localpath, e.entry_type AS type, e.calendar_id AS calid, c.server_id AS serverid');
        $query->from($db->qn('#__xbjournals_vjournal_attachments').' AS a');
        $query->leftJoin($db->qn('#__xbjournals_vjournal_entries').' AS e ON e.id = a.entry_id');
        $query->leftJoin($db->qn('#__xbjournals_calendars').' AS c ON c.id = e.calendar_id');
        $db->setQuery($query);
        $res = $db->loadObjectList();
        if ($res) {
            foreach ($res as $att) {
                if ($att->uri) $cnts['remote'] ++;
                if ($att->type == 'Journal') $cnts['journals'] ++;
                if ($att->type == 'Note') $cnts['notes'] ++;
                if ($att->value == 'BINARY') {
                    $cnts['embed'] ++;
                } elseif ($att->localpath) {
                    $cnts['rem2local'] ++;
                }
            }
            $cnts['total'] = count($res);
            $cnts['calendars'] = count(array_column($res, null, 'calid'));
            $cnts['servers'] = count(array_column($res, null, 'serverid'));
        }
        return $cnts;
    }
    
    public function getCatCnts() {
        $cnts = array('cats'=>0, 'journals' => 0, 'notes' =>0 );
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        return $cnts;
        
    }
    
    public function getTagCnts() {
        $result = array('journals' => 0, 'notes' =>0 );
        
        return $result;
        
    }
    
}
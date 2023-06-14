<?php
/*******
 * @package xbJournals
 * @filesource admin/models/jcategory.php
 * @version 0.0.6.1 13th June 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Component\ComponentHelper;

class XbjournalsModelJcategory extends JModelItem {

	protected function populateState() {
		$app = Factory::getApplication();
		
		// Load state from the request.
		$id = $app->input->getInt('id');
		$this->setState('cat.id', $id);
		
	}
	
	public function getItem($id = null) {
		if (!isset($this->item) || !is_null($id)) {
//			$params = ComponentHelper::getParams('com_xbjournals');
			
			$id    = is_null($id) ? $this->getState('cat.id') : $id;
			$db = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select('c.id AS id, c.path AS path, c.title AS title, c.description AS description, c.alias AS alias, c.note As note, c.metadata AS metadata' );
			$query->select('(SELECT COUNT(*) FROM #__xbjournals_calendars AS cals WHERE cals.catid = c.id) AS ccnt');
			$query->select('(SELECT COUNT(*) FROM #__xbjournals_vjournal_entries AS j WHERE j.catid = c.id AND j.entry_type = '.$db->q('Journal').' ) AS jcnt');
			$query->select('(SELECT COUNT(*) FROM #__xbjournals_vjournal_entries AS j WHERE j.catid = c.id AND j.entry_type = '.$db->q('Note').' ) AS ncnt');
			$query->from('#__categories AS c');
			$query->where('c.id = '.$id);
			
			try {
				$db->setQuery($query);
				$this->item = $db->loadObject();
			} catch (Exception $e) {
				$dberr = $e->getMessage();
				Factory::getApplication()->enqueueMessage($dberr.'<br />Query: '.$query, 'error');				
			}			
			if ($this->item) {				
				$item = &$this->item;
				//get titles and ids of calendars jounrals and notes in this jcategory
				if ($item->ccnt > 0) {
					$query = $db->getQuery(true);
					$query->select('cal.id AS calid, cal.title AS title')
					->from('#__categories AS cal');
					$query->join('LEFT','#__xbjournals_calendars AS cal ON cal.catid = c.id');
					$query->where('c.id='.$item->id);
					$query->order('cal.title');
					$db->setQuery($query);
					$item->cals = $db->loadObjectList();
				} else {
					$item->cals = '';
				}
				if ($item->jcnt > 0) {
					$query = $db->getQuery(true);
					$query->select('j.id AS jid, j.title AS title');
					$query->from('#__categories AS c');
					$query->join('LEFT','#__xbjournals_vjournal_entries AS j ON j.catid = c.id');
					$query->where('c.id='.$item->id)->where('j.entry_type = '.$db->q('Journal'));
					$query->order('j.title');
					$db->setQuery($query);
					$item->journals = $db->loadObjectList();
				} else {
					$item->journals='';
				}
				if ($item->ncnt > 0) {
					$query = $db->getQuery(true);
					$query->select('j.id AS jid, j.title AS title');
					$query->from('#__categories AS c');
					$query->join('LEFT','#__xbjournals_vjournal_entries AS j ON j.catid = c.id');
					$query->where('c.id='.$item->id)->where('j.entry_type = '.$db->q('Note'));
					$query->order('j.title');
					$db->setQuery($query);
					$item->notes = $db->loadObjectList();
				} else {
				    $item->notes='';
				}
			}
			
			return $this->item;
		} //endif item set			
	} //end getItem()
}

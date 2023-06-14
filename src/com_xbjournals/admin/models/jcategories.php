<?php
/*******
 * @package xbJournals
 * @filesource admin/models/jcategories.php
 * @version 0.0.6.1 12th June 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\TagsHelper;

class XbjournalsModelJcategories extends JModelList {
	
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
					'id', 'title', 'path','jcnt','ncnt','ccnt',
					'published', 'parent'
			);
		}
		parent::__construct($config);
	}
		
	protected function getListQuery() {
		// Initialize variables.
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('DISTINCT c.id AS id, c.path AS path, c.level AS level, c.title AS title, c.description AS description,
		 c.note AS note, c.published AS published,  c.checked_out AS checked_out, c.extension,
         c.checked_out_time AS checked_out_time, c.lft');
		$query->from('#__categories AS c');
		
		$query->select('(SELECT COUNT(*) FROM #__xbjournals_calendars AS cals WHERE cals.catid = c.id ) AS ccnt');
		$query->select('(SELECT COUNT(*) FROM #__xbjournals_vjournal_entries AS j WHERE j.catid = c.id AND j.entry_type = '.$db->q('Journal').' ) AS jcnt');
		$query->select('(SELECT COUNT(*) FROM #__xbjournals_vjournal_entries AS j WHERE j.catid = c.id AND j.entry_type = '.$db->q('Note').' ) AS ncnt');
		
		$query->where('c.extension = '.$db->quote('com_xbjournals'));
		
		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('published = ' . (int) $published);
		} else if ($published === '') {
			$query->where('(published IN (0, 1))');
		}
		
		//filter by tag branch
		$branch = $this->getState('filter.branch');
		if ($branch != '') {
			$query->where('c.alias LIKE '.$db->quote('%'.$branch.'%'));
		}
				
		// Filter by search in title/id/desc
		$search = $this->getState('filter.search');		
		if (!empty($search)) {
			if (stripos($search, 'i:') === 0) {
				$query->where($db->quoteName('c.id') . ' = ' . (int) substr($search, 2));
			} elseif ((stripos($search,'s:')===0) || (stripos($search,'d:')===0)) {
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim(substr($search,2)), true) . '%'));
				$query->where('(c.description LIKE ' . $search.')');
			} elseif (stripos($search,':')!= 1) {
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				$query->where('(c.title LIKE ' . $search . ')');
			}
		}
		
		// Add the list ordering clause.
		$orderCol       = $this->state->get('list.ordering', 'title');
		$orderDirn      = $this->state->get('list.direction', 'ASC');		
		$query->order($db->escape('extension, '.$orderCol.' '.$orderDirn));
		
		return $query;
	}

	public function getItems() {
		$items  = parent::getItems();
		return $items;
	}
}
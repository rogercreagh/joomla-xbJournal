<?php
/*******
 * @package xbJournals
 * @filesource admin/helpers/xbjournals.php
 * @version 0.0.0.1 2nd April 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Filter\OutputFilter;

class XbjournalsHelper extends ContentHelper
{
	public static function getActions($component = 'com_xbjournals', $section = 'component', $categoryid = 0) {
	    
	    $user 	=Factory::getUser();
	    $result = new JObject;
	    if (empty($categoryid)) {
	        $assetName = $component;
	        $level = $section;
	    } else {
	        $assetName = $component.'.category.'.(int) $categoryid;
	        $level = 'category';
	    }
	    $actions = Access::getActions('com_xbjournals', $level);
	    foreach ($actions as $action) {
	        $result->set($action->name, $user->authorise($action->name, $assetName));
	    }
	    return $result;
	}
	
	public static function addSubmenu($vName = 'dashboard') {
			JHtmlSidebar::addEntry(
	            Text::_('XBCULTURE_ICONMENU_DASHBOARD'),
	            'index.php?option=com_xbfilms&view=dashboard',
	            $vName == 'dashboard'
		        );
	}
    

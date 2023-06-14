<?php 
/*******
 * @package xbJournals
 * @filesource admin/views/fcategory/view.html.php
 * @version 0.0.6.1 12th June 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

class XbjournalsViewJcategory extends JViewLegacy {

	public function display($tpl = null) {
		
		$this->item = $this->get('Item');
		
		$this->addToolBar();
		XbjournalsHelper::addSubmenu('jcategories');
		$this->sidebar = JHtmlSidebar::render();
		
		parent::display($tpl);
		// Set the document
		$this->setDocument();
	}
	
	protected function addToolBar() {
		$canDo = XbjournalsHelper::getActions();
		
		ToolBarHelper::title(Text::_( 'XBJOURNALS_ADMIN_CATEGORY_TITLE' ), 'folder' );
		
		ToolbarHelper::custom('jcategory.jcategories', 'folder', '', 'XBJOURNALS_CAT_LIST', false) ;
		
		if ($canDo->get('core.admin')) {
			ToolBarHelper::preferences('com_xbjournals');
		}
	}
	
	protected function setDocument() {
		$document = Factory::getDocument();
		$document->setTitle(Text::_('XBJOURNALS_ADMIN_CATEGORY_TITLE'));
	}
	
}

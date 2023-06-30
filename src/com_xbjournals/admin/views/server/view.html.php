<?php
/*******
 * @package xbJournals Component
 * @filesource admin/views/server/view.html.php
 * @version 0.0.7.0 30th June 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

class XbjournalsViewServer extends JViewLegacy {
    
    protected $form = null;
    
    public function display($tpl = null) {
        
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        $this->canDo = XbjournalsHelper::getActions('com_xbjournals', 'server', $this->item->id);
        
        $params      = $this->get('State')->get('params');
        
        if (count($errors = $this->get('Errors'))) {
            Factory::getApplication()->enqueueMessage(implode('<br />', $errors),'error');
            return false;
        }
        
        $this->addToolBar();
        
        parent::display($tpl);
        // Set the document
        $this->setDocument();
        
    }
    
    protected function addToolBar()
    {
        $input = Factory::getApplication()->input;
        $input->set('hidemainmenu', true);
        $user = Factory::getUser();
        $userId = $user->get('id');
        $checkedOut     = !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
        
        $canDo = $this->canDo;
        
        $isNew = ($this->item->id == 0);
        
        if ($isNew) {
            $title = Text::_('XBJOURNALS_ADMIN_NEWSERVER_TITLE');
        } elseif ($checkedOut) {
            $title = Text::_('XBJOURNALS_ADMIN_VIEWSERVER_TITLE');
        } else {
            $title = Text::_('XBJOURNALS_ADMIN_EDITSERVER_TITLE');
        }
        ToolBarHelper::title($title, 'database');
        
        ToolbarHelper::apply('server.apply');
        ToolbarHelper::save('server.save');
        ToolbarHelper::custom('servers.getall', 'file-plus', '', 'XBJOURNALS_GETALL', true) ;
        ToolbarHelper::custom('servers.getdates', 'file-check', '', 'XBJOURNALS_GETDATES', true) ;
        ToolbarHelper::save2new('server.save2new');
        ToolbarHelper::save2copy('server.save2copy');
        if ($isNew) {
            ToolbarHelper::cancel('server.cancel','JTOOLBAR_CANCEL');
        } else {
            ToolbarHelper::cancel('server.cancel','JTOOLBAR_CLOSE');
        }
        ToolbarHelper::custom(); //spacer
        ToolbarHelper::help( '', false,'https://crosborne.uk/xbjournals/doc?tmpl=component#serveredit' );
    }
    
    protected function setDocument()
    {
        $document = Factory::getDocument();
        $document->setTitle(strip_tags(($this->item->id == 0) ? Text::_('XBJOURNALS_ADMIN_NEWSERVER_TITLE') : Text::_('XBJOURNALS_ADMIN_EDITSERVER_TITLE')));
    }
    
}
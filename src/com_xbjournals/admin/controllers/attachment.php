<?php
/*******
 * @package xbJournals Component
 * @filesource admin/controllers/attachment.php
 * @version 0.1.4.0 12th October 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;

class XbjournalsControllerAttachment extends  FormController {
    
    public function __construct($config = array(), MVCFactoryInterface $factory = null)
    {
        parent::__construct($config, $factory);
        //$this->registerTask('savepreview', 'save');
    }
    
}

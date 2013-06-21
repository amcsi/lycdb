<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class SearchController extends AbstractActionController
{
    public function indexAction() {
        $sm = $this->getServiceLocator();
        $model = $sm->get('Lycee\Model');
        $options = array ();
        $options['template'] = true;
        $result = $model->get($options);
        $view = array ();
        $view['cards'] = $result;
        return new ViewModel($view);
    }
}

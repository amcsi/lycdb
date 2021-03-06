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

class ImportController extends AbstractActionController
{
    public function indexAction() {
        return $this->lyceeImport();
    }

    public function omoshiroiAction() {
        return $this->omoshiroiImport();
    }

    public function lyceeAction() {
        return $this->lyceeImport();
    }

    public function omoshiroiImport() {
        $importer = new \Lycee\OmoshiroiImporter;
        $importer->importByHtmlFile('data/omoshiroi-lycee-search.html');
        return false;
    }

    public function lyceeImport() {
        $sm = $this->getServiceLocator();
        $importer = $sm->get('Lycee\LyceeImporter');
        $result = $importer->import();
        return false;
    }
}

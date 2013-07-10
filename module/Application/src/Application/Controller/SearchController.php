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
        $options['pref_lang'] = 'en'; // prefer english

        $request = $this->getRequest();
        //$page = $request->fromRoute('page', 1);
        $page = $this->getEvent()->getRouteMatch()->getParam('page', 1);
        if ($request->getQuery('search')) {
            $options['cid'] = $request->getQuery('cid');
            $options['name'] = $request->getQuery('name');
            $options['type'] = $request->getQuery('card_type');
            $options['cost_type'] = $request->getQuery('cost_type');
            $options['element_type'] = $request->getQuery('element_type');
            $elements = array ('snow', 'moon', 'flower', 'lightning', 'sun', 'star');
            $options['cost'] = array ();
            $options['element'] = array ();
            foreach ($elements as $key => $element) {
                $options['cost'][$key] = $request->getQuery("cost_$element");
                if (\Lycee\Lycee::STAR != $key) {
                    $options['element'][$key] = (bool) $request->getQuery("element_$element");
                }
            }
            $options['ex'] = $request->getQuery('ex');
           $options['ex_equality'] = $request->getQuery('ex_operator');
            $options['text'] = $request->getQuery('text');
        }
        $options['page'] = $page;
        $result = $model->get($options);
        $total = $model->foundRows;
        $pageCount = $model->pageCount;

        $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\Null($total));
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(50);

        $view = array ();
        $view['cards'] = $result;
        $view['paginator'] = $paginator;
        $view['route']  = 'search';
        return new ViewModel($view);
    }
}

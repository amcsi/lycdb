<?php
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class LycdbMarkupToHtml extends AbstractHelper {

    protected $_sm;

    public function getLyceeModel() {
        return $this->_sm->get('Lycee\Model');
    }

    public function __construct($sm) {
        $this->_sm = $sm;
    }

    public function __invoke($string) {
        $options = array ();
        $options['basePath'] = $this->getView()->basePath();
        return $this->getLyceeModel()->lycdbMarkupToHtml($string, $options);
    }
}

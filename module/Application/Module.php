<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public $isAttila = false; // Server is Attila's local server
    public $isLocalDev = false; // Server is a local dev server
    public $levelStaging = false; // APPLICATION_ENV is at least staging level
    public $levelDevelopment = false; // APPLICATION_ENV is at least development level
    public $isDevIp = false;
    public $isDebugOutputSafe = false; // Is it safe to output debug information
    public $isDebugInvisibleSafe = false; // Is it safe to invisibly output debug information
    public $showAnalytics = false;

    public $sm;

    public $config;

    protected function _initEnv($e) {
        if (!defined('APPLICATION_ENV')) {
            $appEnv = getenv('APPLICATION_ENV');
            if (!$appEnv) {
                $appEnv = 'production';
            }
            define('APPLICATION_ENV', $appEnv);
        }
        $appEnv = APPLICATION_ENV;

        switch (APPLICATION_ENV) {
        case 'testing':
        case 'development':
            $this->levelDevelopment = true;
        case 'staging':
            $this->levelStaging = true;
        case 'production':
            break;
        }
        if ('development' == APPLICATION_ENV) {
            $this->levelStaging = true;
        }
        $this->isDebugOutputSafe = $this->isLocalDev || $this->isDevIp;
        $this->isDebugInvisibleSafe = $this->isDebugOutputSafe || $this->isLocalDev || $this->levelStaging;

        $application = $e->getParam('application');
        $viewModel = $application->getMvcEvent()->getViewModel();
        $viewModel->appEnv = APPLICATION_ENV;

        $sm = $application->getServiceManager();
        $this->sm = $sm;

        $config = $sm->get('Config');
        $this->config = $config;
        $viewModel->google_analytics = $config['google_analytics'];
    }

    public function onBootstrap(MvcEvent $e)
    {
        $this->_initEnv($e);
        $e->getApplication()->getServiceManager()->get('translator');
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
		$response = $e->getResponse();
		$response->getHeaders()->addHeaders(array (
			'Content-Type' => 'text/html; charset=utf-8'
		));
        $eventManager->attach(MvcEvent::EVENT_RENDER, array ($this, 'onRender'));
    }

    public function onRender(MvcEvent $e) {

        if (!empty($this->config['amysql']['profile'])) {
            $sm = $this->sm;
            $amysql = $sm->get('AMysql');
            $viewModel = $e->getViewModel();
            $viewModel->amysqlQueriesData = $amysql->getQueriesData();
            $viewModel->amysqlTotalTime = $amysql->totalTime;
        }
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getViewHelperConfig() {
        return array (
            'invokables' => array (
                'lycdbMarkupToHtml' => 'Application\View\Helper\LycdbMarkupToHtml'
            )
        );
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}

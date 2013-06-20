<?php
namespace Lycee;

class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function onBootstrap($e) {
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig() {
        return array(
            'abstract_factories' => array(
                // this solves the depencency for the Zend Cache factory service, allowing for configurations
                // under application's config's 'caches' property.
                'Zend\Cache\Service\StorageCacheAbstractServiceFactory'
            ),
            'aliases' => array(),
            'factories' => array(
                'Lycee\LyceeImporter' => function ($sm) {
                    $cache = $sm->get('Lycee\Cache');
                    $lyceeImporter = new LyceeImporter;
                    $lyceeImporter->setServiceManager($sm);
                    return $lyceeImporter;
                },
                'AMysql' => 'Lycee\Zend\AMysqlFactory',
            ),
            'invokables' => array(),
            'services' => array(),
            'shared' => array(),
        );
    }
}


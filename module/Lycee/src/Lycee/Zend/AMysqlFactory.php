<?php
namespace Lycee\Zend;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AMysqlFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        // Configure the cache
        $config = $serviceLocator->get('Config');
        $amysql = new \AMysql(
            $config['amysql']['host'],
            $config['amysql']['user'],
            $config['amysql']['password']
        );
        $amysql->selectDb($config['amysql']['db']);

        return $amysql;
    }
}


<?php
namespace Lycee\Zend;

class AMysqlAbstractServiceFactory implements bstractFactoryInterface {

    /**
     * @var array
     */
    protected $config;

    /**
     * Configuration key for cache objects
     *
     * @var string
     */
    protected $configKey = 'amysqls';

    /**
     * @param  ServiceLocatorInterface $services
     * @param  string                  $name
     * @param  string                  $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $services, $name, $requestedName)
    {
        $config = $this->getConfig($services);
        if (empty($config)) {
            return false;
        }

        return (isset($config[$requestedName]) && is_array($config[$requestedName]));
    }

    /**
     * @param  ServiceLocatorInterface              $services
     * @param  string                               $name
     * @param  string                               $requestedName
     * @return \Zend\Cache\Storage\StorageInterface
     */
    public function createServiceWithName(ServiceLocatorInterface $services, $name, $requestedName)
    {
        $config = $this->getConfig($services);
        $config = $config[$requestedName];
        return self::factory($config);
    }

    /**
     * Retrieve cache configuration, if any
     *
     * @param  ServiceLocatorInterface $services
     * @return array
     */
    protected function getConfig(ServiceLocatorInterface $services)
    {
        if ($this->config !== null) {
            return $this->config;
        }

        if (!$services->has('Config')) {
            $this->config = array();
            return $this->config;
        }

        $config = $services->get('Config');
        if (!isset($config[$this->configKey])) {
            $this->config = array();
            return $this->config;
        }

        $this->config = $config[$this->configKey];
        return $this->config;
    }
}

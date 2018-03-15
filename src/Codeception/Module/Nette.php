<?php

namespace Codeception\Module;

use Codeception\Configuration;
use Codeception\Exception\ModuleRequireException;
use Codeception\Lib\Framework;
use Codeception\Lib\Interfaces\PartedModule;
use Nette\DI\Container;

class Nette extends Framework implements PartedModule
{
    /**
     * @var \Nette\DI\Container
     */
    public $container;

    /**
     * @var array
     */
    public $config = [
        'app_path' => '../app',
        'bootstrap_path' => null,
        'environment' => 'test',
        'em_service' => 'doctrine.orm.entity_manager',
    ];

    public function _parts()
    {
        return ['services'];
    }

    public function _initialize()
    {
        if (!($bootstrap = $this->config['bootstrap_path'])) {
            $bootstrap = Configuration::projectDir().$this->config['app_path'].DIRECTORY_SEPARATOR.'bootstrap.php';
        }

        if (!file_exists($bootstrap)) {
            // todo: improve the message
            throw new  ModuleRequireException(__CLASS__, 'Bootstrap file not found');
        }

        $this->container = require $bootstrap;
        $this->container->getService('application')->run();
    }

    /**
     * @return \Nette\DI\Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param \Nette\DI\Container $container
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Grabs a service from Nette DI Container.
     *
     * @param $service
     *
     * @return $mixed
     */
    public function grabService($service)
    {
        if (!$this->container->hasService($service)) {
            $this->fail("Service $service is not available in container");
        }

        return $this->container->getService($service);
    }

    public function amOnRoute($route, $params = [])
    {
        $router = $this->grabService('router');
        // todo: check if route exists and generate url
        $url = '/';
        $this->amOnPage($url);
    }

    public function amOnPage($page)
    {
        $this->_loadPage('GET', $page);
    }
}

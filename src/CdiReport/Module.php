<?php

namespace CdiReport;

class Module {

    public function getConfig() {
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/../../src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function onBootstrap(\Zend\Mvc\MvcEvent $mvcEvent) {
        
    }

    public function getServiceConfig() {
        return array(
            'invokables' => array(
                'cditool_googleapi_service' => 'CdiTools\Service\GoogleApi',
            ),
            'factories' => array(
                'cdireport_module_options' => function ($sm) {
                    $config = $sm->get('Config');
                    return new Options\GoogleApiOptions(isset($config['CdiReport']) ? $config['CdiReport'] : array());
                },
                'cdireport' => 'CdiReport\Service\Factory\AlgoFactory',
            ),
        );
    }

}
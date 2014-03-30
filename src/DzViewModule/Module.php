<?php

/**
 * Fichier de module de DzViewModule.
 *
 * PHP version 5.3.0
 *
 * Copyright 2014 Adrien Desfourneaux
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @category Source
 * @package  DzViewModule
 * @author   Adrien Desfourneaux (aka Dieze) <dieze51@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License Version 2.0
 * @link     https://github.com/dieze/DzViewModule
 */

namespace DzViewModule;

use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ControllerPluginProviderInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\ModuleManagerInterface;

/**
 * Classe module de DzViewModule.
 *
 * @category Source
 * @package  DzViewModule
 * @author   Adrien Desfourneaux (aka Dieze) <dieze51@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License Version 2.0
 * @link     https://github.com/dieze/DzViewModule
 */
class Module implements
    InitProviderInterface,
    AutoloaderProviderInterface,
    BootstrapListenerInterface,
    ControllerPluginProviderInterface,
    ServiceProviderInterface
{
    /**
     * Initialise le ModuleManager.
     *
     * @param ModuleManagerInterface $manager Gestionnaire de module.
     *
     * @return void
     */
    public function init(ModuleManagerInterface $manager)
    {
        $eventManager = $manager->getEventManager();

        // Ajoute le nouveau ServiceManager ViewModelManager au ServiceListener.
        $eventManager->attach(new Listener\AddViewModelManagerListener(), 100);
    }

    /**
     * Ecoute l'événement bootstrap.
     *
     * @param EventInterface $event Evénement MVC, instance de MvcEvent.
     *
     * @return void
     */
    public function onBootstrap(EventInterface $event)
    {
        $application  = $event->getTarget();
        $locator      = $application->getServiceManager();
        $eventManager = $application->getEventManager();

        // Enregistre un événement "render", à une priorité haute
        // afin qu'il s'éxécute avant que la vue commence à faire le rendu.
        $eventManager->attach(new Listener\RegisterViewStrategyListener(), 100);
    }

    /**
     * Retourne un tableau à parser par Zend\Loader\AutoloaderFactory.
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/../../autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }

    /**
     * Doit retourner un objet de type \Zend\ServiceManager\Config
     * ou un tableau pour créer un tel objet.
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getControllerPluginConfig()
    {
        return array(
            'factories' => array(
                'DzViewModule\ViewModel' => 'DzViewModule\Controller\Plugin\Factory\ViewModelFactory',
            ),
            'aliases' => array(
                'viewModel' => 'DzViewModule\ViewModel',
            ),
        );
    }

    /**
     * Doit retourner un objet de type \Zend\ServiceManager\Config
     * ou un tableau pour créer un tel objet.
     *
     * @return array|\Zend\ServiceManager\Config
     *
     * @see ServiceProviderInterface
     */
    public function getServiceConfig()
    {
        return array(
            'invokables' => array(
                'DzViewModule\PhpRenderer'              => 'DzViewModule\View\Renderer\PhpRenderer',
            ),
            'factories' => array(
                'DzViewModule\ViewModelManager'    => 'DzViewModule\Factory\ViewModelManagerFactory',
                'DzViewModule\PhpRendererStrategy' => 'DzViewModule\Factory\PhpRendererStrategyFactory',
                'dzlogger' => function ($sm) {
                    $writer = new \Zend\Log\Writer\FirePhp();
                    $logger = new \Zend\Log\Logger();
                    $logger->addWriter($writer);

                    return $logger;
                }
            ),
            'aliases' => array(
                'ViewModelManager' => 'DzViewModule\ViewModelManager',
            ),
        );
    }
}

<?php

/**
 * Fichier de source pour le RegisterViewStrategyListener.
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
 * @package  DzViewModule\Listener
 * @author   Adrien Desfourneaux (aka Dieze) <dieze51@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License Version 2.0
 * @link     https://github.com/dieze/DzViewModule
 */

namespace DzViewModule\Listener;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;

/**
 * Classe RegisterViewStrategyListener.
 *
 * Ajoute la stratégie de vue personnalisée à l'événement
 * render de la vue.
 *
 * @category Source
 * @package  DzViewModule\Listener
 * @author   Adrien Desfourneaux (aka Dieze) <dieze51@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License Version 2.0
 * @link     https://github.com/dieze/DzViewModule
 */
class RegisterViewStrategyListener extends AbstractListenerAggregate
{
    /**
     * Attache un ou plusieurs écouteurs
     *
     * @param EventManagerInterface $events Instance de EventManager
     *
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        // Enregistre un événement "render", à une priorité haute
        // afin qu'il s'éxécute avant que la vue commence à faire le rendu.
        $this->listeners[] = $events->attach(
            'render',
            array(
                $this,
                'registerViewStrategy'
            ), 100
        );
    }

    /**
     * Enregistre la stratégie de vue personnalisée auprès de la vue.
     *
     * @param EventInterface $e Evénement.
     *
     * @return void
     */
    public function registerViewStrategy(EventInterface $event)
    {
        $application = $event->getTarget();
        $locator     = $application->getServiceManager();
        $view        = $locator->get('Zend\View\View');
        $strategy    = $locator->get('DzViewModule\PhpRendererStrategy');

        // Attache la stratégie, qui est un Listener Aggregate, avec une priorité haute.
        $view->getEventManager()->attach($strategy, 100);
    }
}

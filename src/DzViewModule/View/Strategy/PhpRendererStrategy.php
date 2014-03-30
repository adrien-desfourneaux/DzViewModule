<?php

/**
 * Fichier source pour le PhpRendererStrategy.
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
 * @package  DzViewModule\View\Strategy
 * @author   Adrien Desfourneaux (aka Dieze) <dieze51@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License Version 2.0
 * @link     https://github.com/dieze/DzViewModule
 */

namespace DzViewModule\View\Strategy;

use DzViewModule\View\Renderer\PhpRenderer;

use Zend\EventManager\EventManagerInterface;
use Zend\View\Strategy\PhpRendererStrategy as ZendPhpRendererStrategy;

/**
 * Classe PhpRendererStrategy.
 *
 * Etend Zend\View\Strategy\PhpRendererStrategy.
 * Injecte le \DzViewModule\View\Renderer\PhpRenderer avant
 * le renderer par défaut de Zend \Zend\View\Renderer\PhpRenderer.
 *
 * @category Source
 * @package  DzViewModule\View\Strategy
 * @author   Adrien Desfourneaux (aka Dieze) <dieze51@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License Version 2.0
 * @link     https://github.com/dieze/DzViewModule
 */
class PhpRendererStrategy extends ZendPhpRendererStrategy
{
    /**
     * {@inheritdoc}
     */
    public function __construct(PhpRenderer $renderer)
    {
        parent::__construct($renderer);
    }

    /**
     * Attache le PhpRendererStrategy à l'EventManager,
     * avec une priorité plus élevée que le PhpRenderer de Zend.
     *
     * @param EventManagerInterface $events   Gestionnaire d'événement.
     * @param integer               $priority Priorité du listener.
     *
     * @return void
     */
    public function attach(EventManagerInterface $events, $priority = 2)
    {
        parent::attach($events, $priority);
    }
}

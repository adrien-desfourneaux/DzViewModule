<?php

/**
 * Fichier source pour le PhpRendererStrategyFactory.
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
 * @package  DzViewModule\Factory
 * @author   Adrien Desfourneaux (aka Dieze) <dieze51@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License Version 2.0
 * @link     https://github.com/dieze/DzViewModule
 */

namespace DzViewModule\Factory;

use DzViewModule\View\Strategy\PhpRendererStrategy;

use DzViewModule\View\Renderer\PhpRenderer;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Classe PhpRendererStrategyFactory.
 *
 * Classe usine pour la statégie de rendu PHP.
 *
 * @category Source
 * @package  DzViewModule\Factory
 * @author   Adrien Desfourneaux (aka Dieze) <dieze51@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License Version 2.0
 * @link     https://github.com/dieze/DzViewModule
 */
class PhpRendererStrategyFactory implements FactoryInterface
{
    /**
     * Cré et retourne une stratégie de rendu PHP.
     *
     * Doit être appelé JUSTE AVANT le rendu, car on a besoin
     * que le ViewManager, le ViewRenderer et le ViewModel soient
     * initialisés.
     *
     * @param ServiceLocatorInterface $serviceLocator Localisateur de service.
     *
     * @return PhpRendererStrategy
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $newRenderer = $serviceLocator->get('DzViewModule\PhpRenderer');

        $viewManager    = $serviceLocator->get('ViewManager');
        $oldRenderer    = $viewManager->getRenderer();
        $viewModel      = $viewManager->getViewModel();

        $newRenderer->setResolver($viewManager->getResolver());
        $newRenderer->setVars($viewModel->getVariables());

        $newRenderer->setHelperPluginManager($oldRenderer->getHelperPluginManager());
        $newRenderer->setFilterChain($oldRenderer->getFilterChain());
        $newRenderer->setCanRenderTrees($oldRenderer->canRenderTrees());

        return new PhpRendererStrategy($newRenderer);
    }
}

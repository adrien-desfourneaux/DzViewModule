<?php

/**
 * Fichier source pour le ViewModelManagerFactory.
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

use DzViewModule\View\ModelPluginManager;

use Zend\Mvc\Service\AbstractPluginManagerFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Classe usine pour le manager de ViewModel.
 *
 * @category Source
 * @package  DzViewModule\Factory
 * @author   Adrien Desfourneaux (aka Dieze) <dieze51@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License Version 2.0
 * @link     https://github.com/dieze/DzViewModule
 */
class ViewModelManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = 'DzViewModule\View\ModelPluginManager';

    /**
     * Cré et retourne le Model Manager
     *
     * @param ServiceLocatorInterface $serviceLocator Localisateur de service
     *
     * @return ModelPluginManager
     *
     * @throws Exception\RuntimeException
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $plugins = parent::createService($serviceLocator);

        return $plugins;
    }
}

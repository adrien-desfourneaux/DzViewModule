<?php

/**
 * Fichier source pour le ViewModelFactory.
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
 * @package  DzViewModule\Controller\Plugin\Factory
 * @author   Adrien Desfourneaux (aka Dieze) <dieze51@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License Version 2.0
 * @link     https://github.com/dieze/DzViewModule
 */

namespace DzViewModule\Controller\Plugin\Factory;

use DzViewModule\Controller\Plugin\ViewModel;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Classe ViewModelFactory.
 *
 * Classe usine pour le plugin de contrôleur ViewModel.
 *
 * @category Source
 * @package  DzViewModule\Controller\Plugin\Factory
 * @author   Adrien Desfourneaux (aka Dieze) <dieze51@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License Version 2.0
 * @link     https://github.com/dieze/DzViewModule
 */
class ViewModelFactory implements FactoryInterface
{
    /**
     * Cré et retourne un plugin de contrôleur ViewModel.
     *
     * @param ServiceLocatorInterface $serviceLocator Localisateur de service.
     *
     * @return ViewModel
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $plugin = new ViewModel;

        $locator  = $serviceLocator->getServiceLocator();
        $models = $locator->get('DzViewModule\ViewModelManager');

        $plugin->setModelPluginManager($models);

        return $plugin;
    }
}

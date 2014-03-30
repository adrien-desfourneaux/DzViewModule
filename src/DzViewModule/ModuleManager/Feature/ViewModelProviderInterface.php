<?php

/**
 * Fichier source pour l'interface ViewModelProviderInterface.
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
 * @package  DzViewModule\ModuleManager\Feature
 * @author   Adrien Desfourneaux (aka Dieze) <dieze51@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License Version 2.0
 * @link     https://github.com/dieze/DzViewModule
 */

namespace DzViewModule\ModuleManager\Feature;

/**
 * Interface ViewModelProviderInterface.
 *
 * Permet à une classe Module qui implémente ViewModelProviderInterface
 * de déclarer ses ViewModel au ModelPluginManager.
 *
 * @category Source
 * @package  DzViewModule\ModuleManager\Feature
 * @author   Adrien Desfourneaux (aka Dieze) <dieze51@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License Version 2.0
 * @link     https://github.com/dieze/DzViewModule
 */
interface ViewModelProviderInterface
{
    /**
     * Doit retourner un objet \Zend\ServiceManager\Config
     * ou un tableau pour remplir un tel objet.
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getViewModelConfig();
}

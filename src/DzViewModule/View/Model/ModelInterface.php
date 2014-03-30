<?php

/**
 * Fichier source pour l'interface ModelInterface.
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
 * @package  DzViewModule\View\Model
 * @author   Adrien Desfourneaux (aka Dieze) <dieze51@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License Version 2.0
 * @link     https://github.com/dieze/DzViewModule
 */

namespace DzViewModule\View\Model;

use Zend\Mvc\Controller\PluginManager as ControllerPluginManager;
use Zend\Mvc\Controller\Plugin\PluginInterface;
use Zend\Stdlib\InitializableInterface;
use Zend\View\Helper\HelperInterface;
use Zend\View\HelperPluginManager;
use Zend\View\Model\ModelInterface as ZendModelInterface;

/**
 * Interface pour les models du module DzViewModule.
 *
 * Etend Zend\View\Model\ModelInterface.
 *
 * @category Source
 * @package  DzViewModule\View\Model
 * @author   Adrien Desfourneaux (aka Dieze) <dieze51@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License Version 2.0
 * @link     https://github.com/dieze/DzViewModule
 */
interface ModelInterface extends ZendModelInterface, InitializableInterface
{
    /**
     * Inclut les assets dans la page.
     *
     * @return void
     */
    public function includeAssets();

    /**
     * Définit l'état de l'initialisation du ViewModel.
     *
     * @param boolean $value Nouvel état.
     *
     * @return ModelInterface
     */
    public function setIsInitialized($value);

    /**
     * Obtient l'état de l'initialisation du ViewModel.
     *
     * @return boolean
     */
    public function getIsInitialized();

    /**
     * Définit les variables par défaut du ViewModel.
     *
     * @param array $defaults Nouvelles valeurs.
     *
     * @return ModelInterface
     */
    public function setDefaults($defaults);

    /**
     * Obtient les variables par défaut du ViewModel.
     *
     * @return array
     */
    public function getDefaults();

    /**
     * Définit les assets du ViewModel.
     *
     * @param array $assets Nouveaux assets.
     *
     * @return ModelInterface
     */
    public function setAssets($assets);

    /**
     * Obtient les assets du ViewModel.
     *
     * @return array
     */
    public function getAssets();

    /**
     * Définit le HelperPluginManager.
     *
     * @param HelperPluginManager $helpers Nouveau manager.
     *
     * @return ModelInterface
     */
    public function setHelperPluginManager(HelperPluginManager $helpers);

    /**
     * Obtient le HelperPluginManager.
     *
     * @return HelperPluginManager
     */
    public function getHelperPluginManager();

    /**
     * Obtient l'instance d'un helper
     *
     * @param string     $name    Nom du helper à retourner.
     * @param null|array $options Options à passer au constructeur (si non déjà instancié).
     *
     * @return HelperInterface
     */
    public function helper($name, array $options = null);

    /**
     * Définit le ControllerPluginManager.
     *
     * @param ControllerPluginManager $plugins Nouveau manager.
     *
     * @return ModelInterface
     */
    public function setControllerPluginManager(ControllerPluginManager $plugins);

    /**
     * Obtient le ControllerPluginManager.
     *
     * @return ControllerPluginManager
     */
    public function getControllerPluginManager();

    /**
     * Obtient l'instance d'un plugin.
     *
     * @param string     $name    Nom du plugin à retourner.
     * @param null|array $options Options à passer au constructeur (si non déjà instancié).
     *
     * @return PluginInterface
     */
    public function plugin($name, $options = null);
}

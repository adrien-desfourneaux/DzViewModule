<?php

/**
 * Fichier source pour le plugin de contrôleur ViewModel.
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
 * @package  DzViewModule\Controller\Plugin
 * @author   Adrien Desfourneaux (aka Dieze) <dieze51@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License Version 2.0
 * @link     https://github.com/dieze/DzViewModule
 */

namespace DzViewModule\Controller\Plugin;

use DzViewModule\View\ModelPluginManager;
use DzViewModule\View\ModelPluginManagerAwareInterface;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Model\ModelInterface;

/**
 * Plugin de Contrôleur ViewModel.
 *
 * Obtient un ViewModel auprès du gestionnaire de ViewModel.
 *
 * @category Source
 * @package  DzViewModule\Controller\Plugin
 * @author   Adrien Desfourneaux (aka Dieze) <dieze51@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License Version 2.0
 * @link     https://github.com/dieze/DzViewModule
 */
class ViewModel extends AbstractPlugin implements ModelPluginManagerAwareInterface
{
    /**
     * Gestionnaire de ViewModel.
     *
     * @var ModelPluginManager
     */
    protected $models;

    /**
     * Méthode appelée lorsqu'un script tente
     * d'appeler cet objet comme une fonction.
     *
     * @param string $name Nom du viewmodel à récupérer.
     *
     * @return ModelInterface|ViewModel
     */
    public function __invoke($name = null)
    {
        if ($name === null) {
            return $this;
        }

        $models = $this->getModelPluginManager();
        $model = $models->get($name);

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    public function setModelPluginManager($models)
    {
        $this->models = $models;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getModelPluginManager()
    {
        return $this->models;
    }
}

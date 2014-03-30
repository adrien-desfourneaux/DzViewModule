<?php

/**
 * Fichier source pour le ModelPluginManager.
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
 * @package  DzViewModule\View
 * @author   Adrien Desfourneaux (aka Dieze) <dieze51@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License Version 2.0
 * @link     https://github.com/dieze/DzViewModule
 */

namespace DzViewModule\View;

use DzViewModule\View\Model\ModelInterface;
use DzViewModule\Exception\InvalidModelException;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Model\ModelInterface as ZendModelInterface;

/**
 * Classe ModelPluginManager.
 *
 * Gestionnaire de models. Cette classe étend AbstractPluginManager
 * pour avoir le même comportement qu'un ServiceManager.
 *
 * @category Source
 * @package  DzViewModule\View\Model
 * @author   Adrien Desfourneaux (aka Dieze) <dieze51@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License Version 2.0
 * @link     https://github.com/dieze/DzViewModule
 */
class ModelPluginManager extends AbstractPluginManager
{
    /**
     * Usines par défaut pour les models.
     *
     * @var array
     */
    protected $factories = array(
        'dzviewmoduleviewmodel' => 'DzViewModule\View\Model\ViewModel',
    );

    /**
     * Invokables par défaut pour les models.
     *
     * @var array
     */
    protected $invokableClasses = array();

    /**
     * Alias par défaut pour les models.
     *
     * @var array
     */
    protected $aliases = array(
        'viewmodel'     => 'dzviewmoduleviewmodel',
    );

    /**
     * Constructeur de ModelPluginManager.
     *
     * Après l'invocation du constructeur parent, ajoute un initializer pour injecter
     * les dépendances au ViewModel actuellement demandé.
     *
     * @param null|ConfigInterface $configuration
     */
    public function __construct(ConfigInterface $configuration = null)
    {
        parent::__construct($configuration);

        $this->addInitializer(
            array(
                $this,
                'injectViewModelDependencies'
            )
        );
    }

    /**
     * Valide un plugin.
     *
     * Vérifie que le model chargé est une instance de ModelInterface.
     *
     * @param mixed $plugin Plugin à valider.
     *
     * @return void
     *
     * @throws InvalidModelException Si le plugin est invalide.
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof ZendModelInterface) {
            return;
        }

        throw new InvalidModelException(sprintf(
            'Le plugin de type %s est invalide; il doit implémenter Zend\View\Model\ModelInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }

    /**
     * Injecte les dépendances du
     * DzViewModule\View\Model\ViewModel.
     *
     * Les dépendances injectées sont le HelperManager
     * et le PluginManager.
     *
     * @param ModelInterface          $viewModel      ViewModel demandé au gestionnaire de service.
     * @param ServiceLocatorInterface $serviceLocator Gestionnaire de service.
     *
     * @return void
     */
    public function injectViewModelDependencies($viewModel, ServiceLocatorInterface $serviceLocator)
    {
        if (!$viewModel instanceof ModelInterface) {
            return;
        }

        $parentLocator = $serviceLocator->getServiceLocator();
        $helpers       = $parentLocator->get('ViewHelperManager');
        $plugins       = $parentLocator->get('ControllerPluginManager');

        $viewModel->setHelperPluginManager($helpers);
        $viewModel->setControllerPluginManager($plugins);
    }
}

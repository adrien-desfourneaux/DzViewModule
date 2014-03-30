<?php

/**
 * Fichier source pour le PhpRenderer.
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
 * @package  DzViewModule\View\Renderer
 * @author   Adrien Desfourneaux (aka Dieze) <dieze51@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License Version 2.0
 * @link     https://github.com/dieze/DzViewModule
 */

namespace DzViewModule\View\Renderer;

use SplStack;

use DzViewModule\View\Model\ModelInterface;

use Zend\View\Model\ModelInterface as ZendModelInterface;
use Zend\View\Renderer\PhpRenderer as ZendPhpRenderer;

/**
 * Classe PhpRenderer.
 *
 * Etend Zend\View\Renderer\PhpRenderer.
 * Proxy __call vers le Model avant de faire le proxy vers les helpers.
 *
 * @category Source
 * @package  DzViewModule\View\Renderer
 * @author   Adrien Desfourneaux (aka Dieze) <dieze51@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License Version 2.0
 * @link     https://github.com/dieze/DzViewModule
 */
class PhpRenderer extends ZendPhpRenderer
{
    /**
     * Pile des ViewModel utilisés par le renderer.
     *
     * Lorsque le renderer commence le rendu d'un nouveau ViewModel,
     * ce ViewModel est ajouté à la pile. Lorsque le renderer a terminé
     * le rendu du ViewModel, celui-ci est retiré de la pile.
     * Une pile est nécéssaire pour gérer les ViewModel enfants sans perdre
     * les ViewModels parents.
     *
     * @var SplStack
     */
    public $__models;

    /**
     * Initialisation du PhpRenderer.
     *
     * Cette méthode est automatiquement appelée
     * par le constructeur.
     *
     * @return void
     */
    public function init()
    {
        $this->__models = new SplStack();
    }

    /**
     * {@inheritdoc}
     */
    public function render($nameOrModel, $values = null)
    {
        if ($nameOrModel instanceof ZendModelInterface) {
            $this->__models->push($nameOrModel);
        }

        $return = parent::render($nameOrModel, $values);

        if ($nameOrModel instanceof ZendModelInterface) {
            $this->__models->pop();
        }

        return $return;
    }

    /**
     * Overloading: proxy vers Model, puis Helpers.
     *
     * Proxy vers le Modèle. Si le modèle ne possède pas
     * la méthode appellée, proxy vers le manager d'aide de vues attaché
     * pour récupérer, retourner, et potentiellement exécuter des helpers.
     *
     * * Si le helper ne définit pas __invoke, il sera retourné.
     * * Si le helper définit __invoke, il sera appellé en tant que functor.
     *
     * @param string $method Méthode appelée.
     * @param array  $argv   Arguments de la méthode.
     *
     * @return mixed
     */
    public function __call($method, $argv)
    {
        $this->initModel();

        $model = $this->__models->top();

        if ($model !== null && is_callable(array($model, $method))) {
            return call_user_func_array(array($model, $method), $argv);
        }

        return parent::__call($method, $argv);
    }

    /**
     * Overloading: proxy to Variables container
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        $this->initModel();

        $this->syncVariables($name);

        return parent::__get($name);
    }

    /**
     * Overloading: proxy to Variables container
     *
     * @param  string $name
     * @param  mixed  $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->initModel();

        parent::__set($name, $value);
        $this->syncVariables($name);
    }

    /**
     * Overloading: proxy to Variables container
     *
     * @param  string $name
     * @return bool
     */
    public function __isset($name)
    {
        $this->initModel();

        $this->syncVariables($name);

        return parent::__isset($name);
    }

    /**
     * Appelle la méthode init du ViewModel,
     * s'il n'a pas été initialisé.
     *
     * @return void
     */
    protected function initModel()
    {
        $model = $this->__models->top();

        if ($model instanceof ModelInterface && !$model->getIsInitialized()) {
            $model->init();
        }
    }

    /**
     * Synchronize les variables du Renderer
     * et les variables du ViewModel.
     *
     * @param string $name Nom de la variable.
     *
     * @return void
     */
    protected function syncVariables($name)
    {
        $model = $this->__models->top();

        if (!$model) {
            // Rien à faire.
            return;
        }

        $vars      = $this->vars();
        $variables = $model->getVariables();

        // Dans le Renderer, mais pas dans le ViewModel
        if (isset($vars[$name]) && !isset($variables[$name])) {
            $variables[$name] = $vars[$name];
        }
        // Dans le ViewModel mais pas dans le Renderer
        elseif (!isset($vars[$name]) && isset($variables[$name])) {
            $vars[$name] = $variables[$name];
        }
    }
}

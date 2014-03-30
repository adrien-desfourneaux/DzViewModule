<?php

/**
 * Fichier de source pour le Widget
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
 * @package  DzViewModule\View\Helper
 * @author   Adrien Desfourneaux (aka Dieze) <dieze51@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License Version 2.0
 * @link     https://github.com/dieze/DzViewModule
 */

namespace DzViewModule\View\Helper;

use DzViewModule\View\Model\ModelInterface;

use Zend\Stdlib\InitializableInterface;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ModelInterface as ZendModelInterface;

/**
 * Classe d'aide de vue Widget.
 *
 * @category Source
 * @package  DzViewModule\View\Helper
 * @author   Adrien Desfourneaux (aka Dieze) <dieze51@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License Version 2.0
 * @link     https://github.com/dieze/DzViewModule
 */
class Widget extends AbstractHelper implements InitializableInterface
{
    /**
     * Options passées lors de la méthode __invoke
     * du widget.
     *
     * @var array
     */
    protected $options;

    /**
     * Tableau des options possibles attendues
     * pour le widget.
     *
     * @var array
     */
    protected $validOptions = array(
        'render', // true|false, see render()
    );

    /**
     * ViewModel retourné par le widget.
     *
     * @var ZendModelInterface
     */
    protected $viewModel;

    /**
     * Effectue le rendu du Widget
     * avec les options spécifiées.
     *
     * @param array $options Options du widget.
     *
     * @return string
     */
    public function __invoke($options = array())
    {
        $this->setOptions($options);
        $this->init();

        return $this->render();
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $options      = $this->getOptions();
        $validOptions = $this->getValidOptions();
        $viewModel    = $this->getViewModel();

        // Zend\View\View ne doit pas déclencher
        // l'événement Response lors du rendu.
        $viewModel->setOption('has_parent', true);

        // Passage des options valides en variable du ViewModel.
        foreach ($validOptions as $validOption) {
            // Ne pas passer l'option "render"
            // au ViewModel il n'en a pas besoin.
            if ($validOption == "render") {
                continue;
            }

            // Suppression des variables déjà existantes dans le ViewModel
            // qui correspondent à des options valides.
            // Sinon si on fait appel plusieurs fois au même widget dans la
            // même page, les appels suivants gardent les options des appels
            // précédents.
            unset($viewModel->$validOption);

            if (array_key_exists($validOption, $options)) {
                $viewModel->setVariable($validOption, $options[$validOption]);
            }
        }

        // Déclaration en tant que widget.
        $viewModel->setVariable('isWidget', true);
    }

    /**
     * Effectue le rendu du widget.
     *
     * @return string|Widget
     */
    public function render()
    {
        $options   = $this->getOptions();
        $viewModel = $this->getViewModel();

        $render  = true;
        if (array_key_exists('render', $options)) {
            $render = $options['render'];
        }

        if ($viewModel instanceof ModelInterface) {
            $viewModel->setIsInitialized(false);
        }

        if ($render) {
            try {
                $result = $this->getView()->render($viewModel);

                return $result;
            } catch (\Exception $ex) {
                return $ex->__toString();
            }
        } else {
            return $viewModel;
        }
    }

    /**
     * Définit les options valides.
     *
     * @param array $options Nouvelles options
     *
     * @return Widget
     */
    public function setValidOptions($options)
    {
        $this->validOptions = $options;

        return $this;
    }

    /**
     * Obtient les options valides.
     *
     * @return array
     */
    public function getValidOptions()
    {
        return $this->validOptions;
    }

    /**
     * Définit les options d'invocation.
     *
     * @param array $options Nouvelles options.
     *
     * @return Widget
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Obtient les options d'invocation.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Définit le ViewModel.
     *
     * @param ZendModelInterface $viewModel Nouveau ViewModel.
     *
     * @return Widget
     */
    public function setViewModel($viewModel)
    {
        $this->viewModel = $viewModel;

        return $this;
    }

    /**
     * Obtient le ViewModel.
     *
     * @return ZendModelInterface
     */
    public function getViewModel()
    {
        return $this->viewModel;
    }
}

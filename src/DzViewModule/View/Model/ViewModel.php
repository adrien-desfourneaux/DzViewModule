<?php

/**
 * Fichier source pour le ViewModel.
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

use ArrayAccess;
use Traversable;

use Zend\Mvc\Controller\PluginManager as ControllerPluginManager;
use Zend\Stdlib\Exception\InvalidArgumentException;
use Zend\View\HelperPluginManager;
use Zend\View\Model\ViewModel as ZendViewModel;

/**
 * Classe du ViewModel.
 *
 * Etend Zend\View\Model\ViewModel.
 *
 * Rend le ViewModel "aware" du HelperPluginManager et du ControllerPluginManager.
 * Proxy les méthodes non trouvées au sein du ViewModel vers le HelperPluginManager et le ControllerPluginManager.
 *
 * @category Source
 * @package  DzViewModule\View\Model
 * @author   Adrien Desfourneaux (aka Dieze) <dieze51@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License Version 2.0
 * @link     https://github.com/dieze/DzViewModule
 */
class ViewModel extends ZendViewModel implements ModelInterface
{
    /**
     * Etat de l'initialisation du ViewModel.
     *
     * @var boolean
     */
    protected $isInitialized = false;

    /**
     * Valeurs par défaut des variables.
     *
     * @var array
     */
    protected $defaults = array();

    /**
     * Stratégie pour les assets.
     *
     * Définit le helper et sa méthode
     * pour chaque position/extension/type d'inclusion
     * de chaque asset.
     *
     * @var array
     */
    protected $assetsStrategy = array(
        'head' => array(
            'css' => array(
                'link' => array(
                    'helper' => 'headLink',
                    'method' => 'appendStylesheet',
                ),
                'inline' => array(
                    'helper' => 'headStyle',
                    'method' => 'appendStyle',
                ),
            ),
            'js' => array(
                'link' => array(
                    'helper' => 'headScript',
                    'method' => 'appendFile',
                ),
                'inline' => array(
                    'helper' => 'headScript',
                    'method' => 'appendScript',
                ),
            ),
        ),
        'foot' => array(
            'js' => array(
                'link' => array(
                    'helper' => 'inlineScript',
                    'method' => 'appendFile',
                ),
                'inline' => array(
                    'helper' => 'inlineScript',
                    'method' => 'appendScript',
                ),
            ),
        ),
    );

    /**
     * Assets pour le ViewModel.
     *
     * Le type doit être l'exacte extension du
     * fichier (ex: css pour fichier.css, js pour fichier.js).
     *
     * @var array
     */
    protected $assets = array(
        'head' => array(
            'css' => array(),
            'js'  => array(),
        ),
        'foot' => array(
            'js' => array(),
        ),
    );

    /**
     * Helper plugin manager
     *
     * @var HelperPluginManager
     */
    protected $helpers;

    /**
     * Controller plugin manager
     *
     * @var ControllerPluginManager
     */
    protected $plugins;

    /**
     * Initialise le ViewModel.
     *
     * Cette méthode permet d'initialiser le ViewModel.
     *
     * Dans le constructeur, le Controller n'a pas finit d'assigner
     * les variables. De plus on ne peut pas accèder aux helpers/plugins
     * dans le constructeur.
     *
     * C'est pour cela que les opérations sur les variables,
     * l'accès aux helpers/plugins doit se faire dans la méthode
     * init et non dans le constructeur.
     *
     * La méthode init() est appelée automatiquement par
     * le DzViewModule\View\Renderer\PhpRenderer lors de
     * la méthode render().
     *
     * @return void
     */
    public function init()
    {
        if ($this->getIsInitialized()) {
            return;
        }

        // ViewModel::setVariables($variables, $overwrite, $replace)
        // On ne remplace pas les valeurs fournies par le controller.
        $this->setVariables($this->defaults, false, false);

        // Inclusion des assets
        $this->includeAssets();

        $this->setIsInitialized(true);
    }

    /**
     * Définit des variables de vue en masse.
     *
     * Peut être un array ou une instance de Traversable + ArrayAccess.
     *
     * @param array|ArrayAccess|Traversable $variables
     * @param bool                          $overwrite S'il faut ou non écraser le container interne avec les nouvelles variables.
     * @param bool                          $replace   S'il faut ou non remplacer une variable si elle existe.
     *
     * @throws Exception\InvalidArgumentException
     *
     * @return ViewModel
     */
    public function setVariables($variables, $overwrite = false, $replace = true)
    {
        // Vérification de $variables.
        if (!is_array($variables) && !$variables instanceof Traversable) {
            throw new InvalidArgumentException(sprintf(
                '%s: expects an array, or Traversable argument; received "%s"',
                __METHOD__,
                (is_object($variables) ? get_class($variables) : gettype($variables))
            ));
        }

        // Si il ne faut pas remplacer,
        // Si la variable n'existe pas,
        // Alors on peut la définir.
        if (!$overwrite && !$replace) {
            foreach ($variables as $key => $value) {
                if (!$this->__isset($key)) {
                    $this->setVariable($key, $value);
                }
            }

            return $this;
        } else {
            return parent::setVariables($variables, $overwrite);
        }
    }

    /**
     * Inclus les assets de la page.
     *
     * @return void
     */
    public function includeAssets()
    {
        $assets   = $this->getAssets();
        $strategy = $this->assetsStrategy;

        foreach ($assets as $position => $extensions) {
            foreach ($extensions as $extension => $entries) {
                foreach ($entries as $entry) {

                    if (is_string($entry)) {
                        $entry = (array) $entry;
                    }

                    if (substr($entry[0], -1 * (strlen($extension)+1), strlen($extension)+1) == '.' . $extension) {
                        $includeType = 'link';
                    } else {
                        $includeType = 'inline';
                    }

                    $helper = $this->helper($strategy[$position][$extension][$includeType]['helper']);
                    $method = $strategy[$position][$extension][$includeType]['method'];

                    call_user_func_array(array($helper, $method), $entry);
                }
            }
        }
    }

    /**
     * Définit le titre de la page.
     *
     * @param string $title Titre de page.
     *
     * @return void
     */
    public function setHeadTitle($title)
    {
        $headTitleHelper = $this->helper('headTitle');

        // La variable isWidget peut être définie
        // par un AbstractWidget
        // \DzViewModule\View\Helper\AbstractWidget
        if (isset($this->isWidget)) {
            if (!$this->isWidget) {
                $headTitleHelper($title);
            }
        } else {
            $headTitleHelper($title);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setIsInitialized($value)
    {
        $this->isInitialized = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsInitialized()
    {
        return $this->isInitialized;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaults($defaults)
    {
        $this->defaults = $defaults;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * {@inheritdoc}
     */
    public function setAssets($assets)
    {
        $this->assets = $assets;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAssets()
    {
        return $this->assets;
    }

    /**
     * {@inheritdoc}
     */
    public function setHelperPluginManager(HelperPluginManager $helpers)
    {
        $this->helpers = $helpers;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getHelperPluginManager()
    {
        return $this->helpers;
    }

    /**
     * {@inheritdoc}
     */
    public function helper($name, array $options = null)
    {
        $helpers = $this->getHelperPluginManager();

        return $helpers->get($name, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function setControllerPluginManager(ControllerPluginManager $plugins)
    {
        $this->plugins = $plugins;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getControllerPluginManager()
    {
        return $this->plugins;
    }

    /**
     * {@inheritdoc}
     */
    public function plugin($name, $options = null)
    {
        $plugins = $this->getControllerPluginManager();

        return $plugins->get($name, $options);
    }
}

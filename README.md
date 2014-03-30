DzViewModule
=========

Améliorations de la couche Vue du Zend Framework 2.

DzViewModule améliore la couche Vue du Zend Framework 2. Il est écrit et maintenu par Adrien Desfourneaux (aka Dieze) &lt;dieze51@gmail.com&gt;. Le projet est hébergé par GitHub à l'adresse [https://github.com/dieze/DzViewModule.git](https://github.com/dieze/DzViewModule.git).

Fonctionnalités
-------------

Le module DzViewModule permet une architecture *MVVM + C* (Modèle-Vue-VueModèle + Controller) au sein du Zend Framework 2. L'idée est d'avoir une couche Vue-VueModèle séparée du Controller. Pour cela on limite la responsabilité du controller au seul traitement de la requête et aux opérations d'ajout/modifications/suppression. C'est alors au ViewModel de récupèrer les dépendances dont il a besoin (une entité, un formulaire, etc...). Cela permet un découplage bien plus fort entre la Vue et le Controller.
	
### La classe ViewModel

#### Helpers et Plugins

La classe *DzViewModule\View\Model\ViewModel* améliore le ViewModel du Zend Framework 2 en lui permettant d'accéder aux aides de vue via la méthode *helper()* et aux plugins de controller via la méthode *plugin()*.

Dans un ViewModel :
	
	$urlHelper = $this->helper('url');
	$urlPlugin = $this->plugin('url');
	
	$url = $urlHelper('my/route');
	$url = $urlPlugin->fromRoute('my/route');
	
#### Variables par défaut
	
L'attribut *$defaults* du ViewModel permet d'attribuer des valeurs par défaut aux variables du ViewModel lors du rendu. Les variables fournies par le controller ne sont jamais écrasées par ces valeurs.

	<?php
	
	namespace MyModule\View\Model;
	
	use DzViewModule\View\Model\ViewModel;
	
	class MyViewModel extends ViewModel
	{
		protected $defaults = array(
			'id' => 3,
			'name' => 'foo',
			'description' => 'bar'
		);
	}

Ici si le controller ne définit pas la variable *'name'* du *MyViewModel*, elle vaudra *'foo'* par défaut. Par contre si le controller définit la variable *'id'* à *1*, elle ne sera pas modifiée.

### Assets

L'attribut *$assets* du ViewModel permet de définir les assets (css, js, autres...) utilisés dans la page. Par défaut seuls les assets de type *css* et *js* sont supportés.

*"head"* ajoute le fichier ou le code en *tête de page* dans la balise \<head\>. *"foot"* ajoute le fichier ou le code en *pied de page* juste avant la fermeture du \<body\>. Un fichier est détecté si la valeur fournit se termine par l'extension indiquée en clé de tableau. Les fichiers/codes sont ajoutés dans l'*ordre d'apparition*.

Il est possible de fournir des paramètres optionnels en plus du fichier/code.

Par défaut, l'aide de vue *headLink* et ses méthodes *appendStylesheet* et *appendStyle* sont utilisés pour l'ajout respectivement de fichiers css et de code css dans le \<head\>. Leurs paramètres sont :

	appendStylesheet($href, $media = "screen", $conditionalStylesheet = false, $extras = null)
	
	appendStyle($content, $attributes = array())
	
Par défaut, l'aide de vue *headScript* et ses méthodes *appendFile* et *appendScript* sont utilisés pour l'ajout respectivement de fichiers js et de code js dans le \<head\>. Leurs paramètres sont :

	appendFile($src, $type = 'text/javascript', $attrs = array())
	
	appendScript($script, $type = 'text/javascript', $attrs = array())	
	
Par défaut, l'aide de vue *inlineScript* et ses méthodes *appendFile* et *appendScript* sont utilisés pour l'ajout respectivement de fichiers et de code js en pied de page. Leurs paramètres sont les mêmes que pour *headScript*.

Les paramètres optionnels de ces méthodes peuvent être utilisés en plus du nom de fichier/code js.

	<?php
	
	namespace MyModule\View\Model;
	
	use DzViewModule\View\Model\ViewModel;
	
	class MyViewModel extends ViewModel
	{
		protected $assets = array(
			'head' => array(
				'css' => array(
					'/vendor/bootstrap/css/bootstrap.css',
					'/css/style.css',
					'body { background-color: red; }',
				),
				'js' => array(
					'/vendor/bootstrap/js/bootstrap.js',
					'/js/functions.js',
					'alert("Tête de page");',
				),
			),
			'foot' => array(
				'js' => array(
					'/js/script.js',
					'alert("Pied de page")',
				),
			),
		);
	}
	
**important** : Pour que les l'ajout des assets fonctionne, le layout doit faire appel à *$this->headLink()*, *$this->headScript()*, *$this->headStyle()* et *$this->inlineScript()* aux bons endroits :

	<html>
		<head>
			<?php echo $this->headLink(); ?>
			<?php echo $this->headStyle(); ?>
			<?php echo $this->headScript(); ?>
		</head>
		<body>
		
			<!-- contenu -->
		
			(...)
		
			<?php echo $this->inlineScript(); ?>
		</body>
	</html>

#### Méthode init()

Le ViewModel possède une méthode *init()* qui permet d'initialiser le ViewModel avant le rendu de la Vue. Cette méthode permet de définir les variables par défaut du ViewModel et de résoudre ses différentes dépendances (entités, formulaire, etc...).

Ici on récupère un récupère un formulaire d'ajout via une méthode getAddForm() et on le stocke dans la variable 'addForm' du ViewModel.

	<?php
	
	namespace MyModule\View\Model;
	
	use DzViewModule\View\Model\ViewModel;
	
	class MyViewModel extends ViewModel
	{
		public function init()
		{
			// Définition des variables par défaut.
			parent::init();
			
			// Résolution de la dépendance
			// formulaire d'ajout
			$form = $this->getAddForm();
			$this->setVariable('addForm', $form);
		}
		
		public function getAddForm() {
			// Obtention du formulaire d'ajout de projet
			// ...
			return $form;
		}
	}
	
### PhpRenderer

La classe *DzViewModule\View\Renderer\PhpRenderer* améliore le PhpRenderer du Zend Framework 2 en facilitant la communication entre la Vue (le template) et le ViewModel. Il *initialise* automatiquement le ViewModel, permet à la vue d'appeler des *méthodes* du ViewModel et met en place une *synchronisation des variables* de Vue (stockées dans le renderer) et des variables du ViewModel (*$viewModel->getVariable()*).

Cela permet de déclarer des méthodes dans le ViewModel et de les appeller depuis le template via *$this*.

Dans le ViewModel :

	<?php
	
	namespace MyModule\View\Model;
	
	use DzViewModule\View\Model\ViewModel;
	
	class MyViewModel extends ViewModel
	{
		public function renderUser($user)
		{
			$firstname = $user['firstname'];
			$lastname  = $user['lastname'];
			$phone     = $user['phone'];
			$address   = $user['address'];
			
			return
				$firstname . ' ' . 
				$lastname . ' ' .
				$phone . ' ' .
				$address;
		}
		
		public function renderUsers()
		{
			$users = $this->getVariable('users');
			$return = '';
			
			foreach ($users as $user) {
				$line = $this->renderUser($user);
				$return .= $line . '<br>';
			}
			
			return $return;
		}
	}

Depuis le template :

	<?php
	$users = $this->users;
	?>
	
	<h1>Utilisateurs</h1>
	
	<p>Un utilisateur:</p>
	
	<?php
	// En présumant qu'il y a au moins un utilisateur.
	echo $this->renderUser($users[0]);
	?>
	
	<p>Tous les utilisateurs</p>
	
	<?php
	echo $this->renderUsers();
	?>
	
Dans cet exemple d'affichage d'informations utilisateurs, on fait appel aux méthodes du ViewModel ce qui rend le template plus lisible.

### ViewModelManager

Le module *DzViewModule* ajoute un nouveau service manager appelé *ViewModelManager* au service manager de Zend Framework 2. Le *ViewModelManager* permet d'obtenir un ViewModel à partir d'une clé (chaîne de caractères).

Par exemple, à l'intérieur d'un controller ZF2, on peut récupérer un ViewModel depuis le *ViewModelManager* de cette façon :

	$locator    = $this->getServiceLocator();
	$viewModels = $locator->get('ViewModelManager');
	$viewModel  = $viewModels->get('MyModule\MyViewModel');
	
#### Enregistrer ses ViewModel auprès du ViewModelManager

Pour pouvoir récupérer ses ViewModels auprès du *ViewModelManager* il faut les déclarer. Il y a deux façons de déclarer des nouveaux ViewModel :

##### via l'interface ViewModelProviderInterface

Dans le fichier *Module.php*

	<?php
	
	namespace MyModule;
	
	use DzViewModule\ModuleManager\Feature\ViewModelProviderInterface;
	
	class Module implements
		ViewModelProviderInterface
	{
		public function getViewModelConfig()
		{
			return array(
				'invokables' => array(
					'MyModule\MyViewModel' => 'MyModule\View\Model\MyViewModel',
				),
			);
		}
	}
	
##### via le module.config.php

Dans le fichier *Module.php*

	<?php
	
	namespace MyModule;
	
	use Zend\ModuleManager\Feature\ConfigProviderInterface;
	
	class Module implements
		ConfigProviderInterface
	{
		public function getConfig()
		{
			return include __DIR__ . '/config/module.config.php';
		}
	}

Dans le fichier *config/module.config.php*

	<?php
	
	return array(
		'view_models' => array(
			'invokables' => array(
				'MyModule\MyViewModel' => 'MyModule\View\Model\MyViewModel',
			),
		),
	);
	
### Plugins de controller

#### ViewModel

Le plugin de controller *ViewModel* permet d'obtenir un ViewModel à partir de sa clé dans le *ViewModelManager*.

Dans le controller :

	$viewModel = $this->viewmodel('MyModule\MyViewModel');
	
permet d'obtenir le ViewModel enregistré sous le nom "MyModule\MyViewModel".

### Widget

Un *widget* est une aide de vue (view helper) qui permet de mettre le contenu d'une page dans une autre. On peut imaginer un widget *usersListWidget* qui affiche la liste des utilisateurs. On peut alors afficher cette liste dans n'importe quelle page juste en écrivant dans un template :
	
	<?php
	
	echo $this->usersListWidget();
	
	?>
	
Cela est rendu possible grâce aux améliorations apportées à la classe *ViewModel*. Ces améliorations permettent aux ViewModel de fonctionner de façon autonome (ils résolvent eux même leurs dépendances sans passer par le controller). La classe *DzViewModule\View\Helper\Widget* met en pratique ce principe.

La classe *Widget* accepte en dépendances un *ViewModel* et en effectue le rendu. L'invocation de l'aide de vue (méthode *__invoke()*) prend en premier paramètre un tableau d'options. Ces options sont passées en variable du ViewModel lors de l'initilisation du Widget (méthode *init()*) pour en modifier le comportement final. L'attribut *$validOptions* précise les options valides du widget.

	<?php
	
	namespace MyModule\View\Helper;
	
	use DzViewModule\View\Helper\Widget;
	
	class UsersListWidget extends Widget
	{
		protected $validOptions = array(
			'hasTitle',
			'max',
		);
		
		/*
		 * Méthode héritée de la classe Widget.
		 */
		/*public function __invoke($options = array())
		{
			// Initialisation du widget
			// et passage des options valides
			// en variables du ViewModel.
			$this->setOptions($options);
			$this->init();
			
			// Rendu du widget.
			return $this->render();
		}*/
	}

La méthode *__invoke()* est directement héritée de la classe *DzViewModule\View\Helper\Widget*, il n'est pas utile de la redéfinir.

Ici on peut imaginer que l'option *hasTitle* détermine s'il faut ou non afficher le titre du widget, et que l'option *max* détermine le nombre maximum d'utilisateurs à afficher.

#### Créer ses propres widget

En plus de l'écriture de la classe du Widget, il est nécéssaire d'écrire une *Factory* qui va y injecter le ViewModel.

	<?php
	
	namespace MyModule\View\Helper\Factory;
	
	use MyModule\View\Helper\UsersListWidget;
	
	use Zend\ServiceManager\FactoryInterface;
	use Zend\ServiceManager\ServiceLocatorInterface;
	
	class UsersListWidgetFactory implements FactoryInterface
	{
		public function createService(ServiceLocatorInterface $serviceLocator)
		{
			// récupération du service locator root
			$locator = $serviceLocator->getServiceLocator();
			$viewModels = $locator->get('ViewModelManager');
			
			$viewModel    = $viewModels->get('MyModule\UsersListViewModel');

			// Le widget attend un ViewModel dont on a déjà spécifié le template.
			$viewModel->setTemplate('my-module/user/users-list.phtml');
			
			$widget = new UsersListWidget;
			$widget->setViewModel($viewModel),
			
			return $widget;
		}
	}

Et il faut également enregister le widget en tant qu'*aide de vue*.

Dans *Module.php*

	<?php
	
	namespace MyModule;
	
	use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
	
	class Module implements
		ViewHelperProviderInterface
	{
		public function getViewHelperConfig()
		{
			return array(
				'factories' => array(
					'MyModule\UsersListWidget' => 'MyModule\View\Helper\UsersListWidget',
				),
				'aliases' => array(
					'usersListWidget' => 'MyModule\UsersListWidget',
				),
			);
		}
	}
	
On peut alors insérer le widget dans n'importe quelle page en écrivant dans le template de la page :

	<?php
	
	echo $this->usersListWidget(
		array(
			'hasTitle' => false, // Ne pas afficher le titre
			'max' => 30,         // Limiter à 30 utilisateurs
		)
	);
	
	?>

Scripts
-----------

###qa.sh

Le script DzBaseModule/bin/qa.sh est un script d'assurance qualité du code. Il permet de gérer la qualité du code et la documentation.

Vérifier la conformité du code avec les standards :

    bin/qa.sh code check

Modifier le code source pour qu'il soit conforme aux standards :

    bin/qa.sh code fix

Générer la documentation (la documentation se situera dans /doc)

    bin/qa.sh doc gen

Pour un aperçu complet des fonctionnalités de qa.sh :

    bin/qa.sh help

Licence
--------------

Copyright 2014 Adrien Desfourneaux

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
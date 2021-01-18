<?php  

namespace App;

use MF\Init\Bootstrap;

class Route extends Bootstrap {

	protected function initRoutes() {

		$routes['home'] = Array(
			'route' => '/',
			'controller' => 'IndexController',
			'action' => 'index'
		);

		$routes['cadastro'] = Array(
			'route' => '/cadastro',
			'controller' => 'IndexController',
			'action' => 'cadastro'
		);

		$routes['registrar'] = Array(
			'route' => '/registrar',
			'controller' => 'IndexController',
			'action' => 'registrar'
		);

		$routes['logar'] = Array(
			'route' => '/logar',
			'controller' => 'AuthController',
			'action' => 'logar'
		);

		$routes['timeline'] = Array(
			'route' => '/timeline',
			'controller' => 'AppController',
			'action' => 'timeline'
		);

		$routes['sair'] = Array(
			'route' => '/sair',
			'controller' => 'AuthController',
			'action' => 'sair'
		);

		$routes['sobre'] = Array(
			'route' => '/sobre',
			'controller' => 'AppController',
			'action' => 'sobre'
		);

		$routes['nova_divida'] = Array(
			'route' => '/nova_divida',
			'controller' => 'AppController',
			'action' => 'novaDivida'
		);

		$routes['deletar_divida'] = Array(
			'route' => '/deletar_divida',
			'controller' => 'AppController',
			'action' => 'deletarDivida'
		);

		$routes['alterar_divida'] = Array(
			'route' => '/alterar_divida',
			'controller' => 'AppController',
			'action' => 'alterarDivida'
		);

		$routes['altCadDivida'] = Array(
			'route' => '/altCadDivida',
			'controller' => 'AppController',
			'action' => 'altCadDivida'
		);

		$routes['pesquisar'] = Array(
			'route' => '/pesquisar',
			'controller' => 'AppController',
			'action' => 'pesquisar'
		);


		$this->setRoutes($routes);

	}


}

?>
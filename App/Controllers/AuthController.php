<?php

namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;
use App\Models\Usuario;


class AuthController extends Action { 

	public function logar() {
		$usuario = Container::getModel('usuario');

		$usuario->__set('email', $_POST['email']);
		$usuario->__set('senha', $_POST['senha']);

		$usuario->autenticar();
		
		if ($usuario->__get('id') != '' && $usuario->__get('nome')) {

			session_start();
			$_SESSION['id'] = $usuario->__get('id');
			$_SESSION['nome'] = $usuario->__get('nome');
			header('location:/timeline');

		} else {
			
			header('location:/?login=erro');
		}
	}

	public function sair() {
		session_start();
		session_destroy();

		header('location:/');
	}

}

?>
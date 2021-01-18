<?php

namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;
use App\Models\Usuario;


class IndexController extends Action {

	public function index() {
		$this->view->login = isset($_GET['login']) ? $_GET['login'] : '';
		$this->render('index');
	}

	public function cadastro() {
		
		$this->view->erroCadastro = 0;
		$this->view->sucesso = false;
		$this->render('cadastro');
	}

	public function registrar() {

		$usuario = Container::getModel('usuario');

		$usuario->__set('nome', $_POST['nome']);
		$usuario->__set('email', $_POST['email']);
		$usuario->__set('senha', $_POST['senha']);

		if($usuario->validarUsuario()) {

			if (count($usuario->listaUsuarioPorEmail()) == 0) {	

				$usuario->__set('senha', md5($_POST['senha']));
				$usuario->salvar();
				$this->view->sucesso = true;
				$this->view->erroCadastro = 0;
				$this->render('cadastro');

			} else {
				$this->view->usuario = array('nome' => $_POST['nome'], 
					'senha' => $_POST['senha'], 'email' => '');
				$this->view->erroCadastro = 1;
				$this->view->sucesso = false;
				$this->render('cadastro');
			}
			
			
		} else {
			$this->view->usuario = array('nome' => $_POST['nome'], 
				'email' => $_POST['email'],
				'senha' => $_POST['senha']);
			$this->view->erroCadastro = 2;
			$this->view->sucesso = false;
			$this->render('cadastro');
		}

	}

}


?>
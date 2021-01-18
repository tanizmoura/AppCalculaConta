<?php

namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;



class AppController extends Action { 

	public function timeline(){
		$this->autentificarLogin();

		$data = getDate();

		$this->view->mesAtual = isset($_GET['mes']) ? $_GET['mes'] : $data['mon'];
		$this->view->anoAtual = isset($_GET['ano']) ? $_GET['ano'] : $data['year'];	


		$divida = Container::getModel('Divida');
		$divida->__set('id_user',$_SESSION['id']);


		/** PAGINAÇÃO **/
		$limite = 6;	

		$pagina = isset($_GET['pagina']) && $_GET['pagina']!= '' ? $_GET['pagina'] : 1;

		$this->view->pagina_ativa = $pagina;

		$deslocamento = ($pagina - 1) * $limite;

		$this->view->totalDividas = $divida->getTotalDividas($this->view->mesAtual,$this->view->anoAtual);

		$this->view->total_paginas = ceil($this->view->totalDividas['total_dividas'] / $limite);

		$this->view->dividas = $divida->getAllByMesAno($this->view->mesAtual,$this->view->anoAtual, $limite, $deslocamento);

		/** SOMA DAS DIVIDAS **/
		$this->view->soma_dividas = $divida->somarDividas($this->view->mesAtual,$this->view->anoAtual);

		/**LISTA CATEGORIAS **/
		$categoria = Container::getModel('Categoria');
		$categoria->__set('id_user',$_SESSION['id']);
		$this->view->categorias = $categoria->getAll();


		$this->view->categoriaPesquisa = array();

		$this->render('timeline', 'layout_timeline');
		
	}

	public function sobre(){
		$this->autentificarLogin();

		$this->render('sobre', 'layout_timeline');
	
	}

	public function novaDivida(){

		$this->autentificarLogin();

		$categoria = Container::getModel('Categoria');
		$categoria->__set('id_user',$_SESSION['id']);
		$this->view->categorias = $categoria->getAll();
		
		$this->view->erro = isset($_GET['erro']) ? $_GET['erro'] : '';
		$this->view->sucesso = false;

		$this->render('novaDivida', 'layout_timeline');
	
	}


	public function repetir($divida, $acao) {

		$rota = '';

		if ($acao == 'salvar') {
			$rota = 'nova_divida?sucesso=true';

			$acao = 'salvar';

		} else if($acao == 'alterar') {

			$acao = 'alterar';
		}

		$n_repetir = $divida->__get('repetir');

		$valor = $divida->__get('valor');

		$data = $divida->__get('data');

		$data_pieces = explode('-', $data);

		for ($i=1; $i <= $n_repetir; $i++) { 

			$diaAtual = $data_pieces[2];
			$mesAtual = $data_pieces[1];

			if ($mesAtual == 13) {
				$mesAtual = 1;
				$data_pieces[1] = 1;
				$data_pieces[0]++;
			}

			if ($data_pieces[1] == 2 && $diaAtual > 28) {

				$diaAtual = 28;

			} 

			if ($diaAtual == 31 && $data_pieces[1] == 4 || $data_pieces[1] == 6
				|| $data_pieces[1] == 7 || $data_pieces[1] == 11) {
							
				$diaAtual = 30;
			}


			$data = $data_pieces[0] . '-' . $mesAtual  . '-' . $diaAtual;

			$divida->__set('valor',$valor);
			$divida->__set('data', $data);

			$data_pieces[1]++;	

			$divida->$acao();			

			$this->view->sucesso = true;
			header("location:/$rota");
		}
	
	}

	public function alterarDivida() {
		$this->autentificarLogin();
		$this->view->sucesso = false;

		$categoria = Container::getModel('Categoria');
		$this->view->categorias = $categoria->getAll();

		$divida = Container::getModel('Divida');
		$divida->__set('id', $_GET['id']);
		$this->view->divida_alterar = $divida->getDividaPorId();

		$this->render('alteraDivida', 'layout_timeline');
	}

	public function altCadDivida() {
		$this->autentificarLogin();

		$rota = '';
		$acao = '';		

		$categoria = Container::getModel('Categoria');
		$this->view->categorias = $categoria->getAll();

		$divida = Container::getModel('Divida');
		$divida->__set('id_user',$_SESSION['id']);

		if ($this->verificaCampoExiste('descricao')) {
			$divida->__set('descricao',$_POST['descricao']);
		} 

		if ($this->verificaCampoExiste('valor')) {
			$divida->__set('valor',$_POST['valor']);
		} 

		if ($this->verificaCampoExiste('repetir')) {
			$divida->__set('repetir',$_POST['repetir']);
		}

		if ($this->verificaCampoExiste('data')) {
			$divida->__set('data',$_POST['data']);
		}

		if ($this->verificaCampoExiste('categoria')) {
			$divida->__set('categoria',$_POST['categoria']);

		}

		if($_GET['acao'] == 'salvar') {

			$rota = 'nova_divida?sucesso=true';
			$acao = 'salvar';

		} else if ($_GET['acao'] == 'alterar') {
			print_r($_POST);
			$acao = 'alterar';
			$divida->__set('id',$_POST['id']);

			if (isset($_POST['por']) && $_POST['por'] == 'categoria') {

				$rota = 'pesquisar?categoria='.$_POST['categoria'].
				'&mesAno='.$_POST['mesAno'].'&por=categoria&pagina='.$_POST['pagina'];

			} else if (isset($_POST['por']) && $_POST['por'] == 'ano') {

				$rota = 'pesquisar?ano='.$_POST['ano'].'&por=ano&pagina='.$_POST['pagina'];

			} else {

				$rota = 'timeline?mes='.$_POST['mes'].'&ano='.$_POST['ano'].'&pagina='.$_POST['pagina'];
			}
		}

		if ($divida->validarDivida()) {

			if($divida->__get('repetir') > 1) {

				$this->repetir($divida,$acao);

			} else {
				$divida->$acao();		

				header("location:/$rota");
			}		

		}
	}

	public function deletarDivida() {
		$this->autentificarLogin();

		$divida = Container::getModel('Divida');
		$divida->__set('id_user',$_SESSION['id']);
		$divida->__set('id', $_GET['id']);

		$rota = '';

		if (isset($_GET['por']) && $_GET['por'] == 'ano') {

			$rota = "location:/pesquisar?por=ano&pagina=".$_GET['pagina']."&ano=".$_GET['ano'];

		} else if(isset($_GET['por']) && $_GET['por'] == 'categoria') {

			$rota = "location:/pesquisar?por=categoria&pagina=".$_GET['pagina']."&mesAno=".$_GET['mesAno']."&categoria=".$_GET['categoria'];
		}

		$mes = '';

		if (isset($_GET['mes'])) {
			$mes = $_GET['mes'];
		}
		
		print_r($_GET);

		$divida->deletar();

		if (isset($_GET['por'])) {
			header($rota);
		} else {
			header("location:/timeline?pagina=".$_GET['pagina']."&mes=".$_GET['mes']."&ano=".$_GET['ano']);
		}	
		

	}

	public function pesquisar() {
		$this->autentificarLogin();

		$tipo = isset($_GET['por']) ? $_GET['por'] : '';
		$ano = '';

		$divida = Container::getModel('Divida');
		$divida->__set('id_user',$_SESSION['id']);

		if ($tipo == 'ano') {

			$ano = $_GET['ano'];
			/** PAGINAÇÃO **/
			$limite = 6;	
			
			$this->view->mesAtual = '';
			$this->view->anoAtual = $ano;	

			$pagina = isset($_GET['pagina']) && $_GET['pagina']!= '' ? $_GET['pagina'] : 1;

			$this->view->pagina_ativa = $pagina;

			$deslocamento = ($pagina - 1) * $limite;

			$this->view->totalDividas = $divida->getTotalDividasAno($ano);

			$this->view->total_paginas = ceil($this->view->totalDividas['total_dividas'] / $limite);

			$this->view->dividas = $divida->getAllByAno($ano, $limite, $deslocamento);

			/** SOMA DAS DIVIDAS **/
			$this->view->soma_dividas = $divida->somarDividasAno($ano);

			/**LISTA CATEGORIAS **/
			$categoria = Container::getModel('Categoria');
			$categoria->__set('id_user',$_SESSION['id']);
			$this->view->categorias = $categoria->getAll();

			$this->render('timeline', 'layout_timeline');
		} elseif ($tipo == 'categoria') {
			$data = explode('-', $_GET['mesAno']);

			$ano = $data['0'];
			$mes = $data['1'];

			$limite = 6;	
			
			$this->view->mesAtual = $mes;
			$this->view->anoAtual = $ano;	

			$pagina = isset($_GET['pagina']) && $_GET['pagina']!= '' ? $_GET['pagina'] : 1;

			$this->view->pagina_ativa = $pagina;

			$deslocamento = ($pagina - 1) * $limite;

			$totalDividas = $divida->getTotalDividasCategoria($_GET['categoria'], $_GET['mesAno'])['total_dividas'];

			$this->view->total_paginas = ceil($totalDividas / $limite);

			$this->view->dividas = $divida->getAllByCategoria($_GET['categoria'], $mes, $ano, $limite, $deslocamento);

			/** SOMA DAS DIVIDAS **/
			$this->view->soma_dividas = $divida->somarDividasCategoria($_GET['categoria'], $mes, $ano);

			/**LISTA CATEGORIAS **/
			$categoria = Container::getModel('Categoria');
			$categoria->__set('id',$_GET['categoria']);
			$categoria->__set('id_user',$_SESSION['id']);

			$this->view->categorias = $categoria->getAll();

			$this->view->categoriaPesquisa = $categoria->getCategoriaById();

			$this->render('timeline', 'layout_timeline');
			
		}
	}

	public function verificaCampoExiste($campo) {
		if (isset($_POST[$campo])) {
			return true;
		} 
		
	}

	public function autentificarLogin() {
		session_start();

		if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) 
			|| $_SESSION['nome']  == '') {
			header('location:/?login=erro');
		} 

	}
}

?>

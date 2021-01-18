<?php  

namespace App\Models;

use MF\Model\Model;

class Divida extends Model { 
	private $id;
	private $id_user;
	private $descricao;
	private $valor;
	private $repetir;
	private $categoria;
	private $data;

	public function __get($atr) {
		return $this->$atr;
	}

	public function __set($atr, $valor) {
		$this->$atr = $valor;
	}

	public function salvar() {

		$query = 'insert into dividas(id_user, descricao, valor, data, categoria, repetir) values (:id_user, :descricao, :valor, :data, :categoria, :repetir)';

		$stmt = $this->db->prepare($query);

		$stmt->bindValue(':id_user', $this->__get('id_user'));
		$stmt->bindValue(':descricao', $this->__get('descricao'));
		$stmt->bindValue(':valor', $this->__get('valor'));
		$stmt->bindValue(':data', $this->__get('data'));
		$stmt->bindValue(':categoria', $this->__get('categoria'));
		$stmt->bindValue(':repetir', $this->__get('repetir'));

		$stmt->execute();

		return $this;

	}

	public function validarDivida() {
		$valido = true;

		if (strlen($this->__get('valor')) < 0) {
			$valido = false;
		}

		if (strlen($this->__get('data')) < 3) {
			$valido = false;
		}

		return $valido;
	}

	public function getAll() {
		$query = 'select d.id, d.id_user,c.id as id_categoria, d.descricao, d.valor, c.descricao as categoria, d.data 
		from dividas as d 
		left join categoria as c on (d.categoria = c.id) where d.id_user = :id_user' ;

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_user', $this->__get('id_user'));

		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function getAllByMesAno($mes, $ano, $limit, $offset) {

		$query = "select 
		d.id, c.id as id_categoria, d.descricao, d.valor, c.descricao as categoria, d.data 
		from dividas as d 
		left join categoria as c on (d.categoria = c.id) 
		where d.id_user = :id_user and MONTH(d.data) = $mes and YEAR(d.data) = $ano
		LIMIT $limit OFFSET $offset
		";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_user', $this->__get('id_user'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function getAllByCategoria($categoria, $mes, $ano, $limit, $offset) {

		$query = "select 
		d.id, d.descricao, d.valor, d.categoria,c.descricao as categoria, d.data 
		from dividas as d 
		left join categoria as c on (d.categoria = c.id) 
		where d.id_user = :id_user and MONTH(d.data) = $mes and YEAR(d.data) = $ano and d.categoria = $categoria;
		LIMIT $limit OFFSET $offset
		";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_user', $this->__get('id_user'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function getAllByAno($ano, $limit, $offset) {

		$query = "select 
		d.id, c.id as id_categoria, d.descricao, d.valor, c.descricao as categoria, d.data 
		from dividas as d 
		left join categoria as c on (d.categoria = c.id) 
		where d.id_user = :id_user and YEAR(d.data) = $ano
		LIMIT $limit OFFSET $offset
		";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_user', $this->__get('id_user'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function getTotalDividas($mes, $ano) {
		$query = "select 
		count(*) as total_dividas 
		from dividas  
		where id_user = :id_user and MONTH(data) = $mes and YEAR(data) = $ano
		";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_user', $this->__get('id_user'));

		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}

	public function getTotalDividasAno($ano) {
		$query = "select 
		count(*) as total_dividas 
		from dividas  
		where id_user = :id_user and YEAR(data) = $ano
		";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_user', $this->__get('id_user'));

		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}

	public function getTotalDividasCategoria($categoria,$data) {
		$query = "select count(*) as total_dividas from dividas where 
		id_user = :id_user and categoria = $categoria and data LIKE '2021-03%'
		";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_user', $this->__get('id_user'));

		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}

	public function somarDividas($mes, $ano) {
		$query = "select 
		SUM(valor) as total 
		from dividas  
		where id_user = :id_user and MONTH(data) = $mes and YEAR(data) = $ano
		";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_user', $this->__get('id_user'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}

	public function somarDividasAno($ano) {
		$query = "select 
		SUM(valor) as total 
		from dividas  
		where id_user = :id_user and YEAR(data) = $ano
		";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_user', $this->__get('id_user'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}

	public function somarDividasCategoria($categoria, $mes, $ano) {
		$query = "select 
		SUM(valor) as total
		from dividas  
		where id_user = :id_user and categoria = $categoria and YEAR(data) = $ano and MONTH(data) = $mes
		";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_user', $this->__get('id_user'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}

	public function getDividaPorId() {
		$query = 'select id, descricao, valor, data, categoria, repetir from dividas where id = :id';
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id', $this->__get('id'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}

	public function alterar() {
		$query = "update dividas set descricao = :descricao, valor = :valor, data = :data, categoria = :categoria, repetir = :repetir where id = :id";

		$stmt = $this->db->prepare($query);

		$stmt->bindValue(':descricao', $this->__get('descricao'));
		$stmt->bindValue(':valor', $this->__get('valor'));
		$stmt->bindValue(':data', $this->__get('data'));
		$stmt->bindValue(':categoria', $this->__get('categoria'));
		$stmt->bindValue(':repetir', $this->__get('repetir'));
		$stmt->bindValue(':id', $this->__get('id'));

		$stmt->execute();

		return $this;

	}

	public function deletar() {
		$query = 'delete from dividas  
		where id_user = :id_user and id = :id
		';

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_user', $this->__get('id_user'));
		$stmt->bindValue(':id', $this->__get('id'));
		$stmt->execute();
	}


}

?>
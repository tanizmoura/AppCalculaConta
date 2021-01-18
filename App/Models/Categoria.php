<?php  

namespace App\Models;

use MF\Model\Model;

class Categoria extends Model {

	private $id;
	private $id_user;
	private $descricao;

	public function __get($atr) {
		return $this->$atr;
	}

	public function __set($atr, $valor) {
		$this->$atr = $valor;
	}

	public function getAll() {
		$query = 'select * from categoria where id_user is null OR id_user = :id_user';
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('id_user',$this->__get('id_user'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function getCategoriaById() {
		$query = 'select descricao from categoria where id = :id';
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id',$this->__get('id'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}
}

?>
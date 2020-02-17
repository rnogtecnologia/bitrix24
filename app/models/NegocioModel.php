<?php
	class NegocioModel extends Model{
		public $_tabela = "negocio";

		public function lista($where = null, $limit = null, $offset = null, $orderby = null, $sql = null, $groupby = null){
			return $this->read($where, $limit, $offset, $orderby, $sql, $groupby);
		}
		public function getNegocio($id){
			$where = array(
				"cmd" => "idnegocio = ?",
				"params" => array($id)
			);
			return $this->read($where);
		}
		public function insertNegocio($dados){
			return $this->insert($dados);
		}
		public function editNegocio($dados, $id){
			return $this->update($dados, "idnegocio = '$id'");
		}
		public function deleteNegocio($id){
			return $this->delete("idnegocio = '$id'");
		}
		public function lastInsertIdNegocio(){			
			return $this->lastInsertId();
		}
	}
	
<?php
	class EmpresaModel extends Model{
		public $_tabela = "empresa";

		public function lista($where = null, $limit = null, $offset = null, $orderby = null, $sql = null, $groupby = null){
			return $this->read($where, $limit, $offset, $orderby, $sql, $groupby);
		}
		public function getEmpresa($id){
			$where = array(
				"cmd" => "idempresa = ?",
				"params" => array($id)
			);
			return $this->read($where);
		}
		public function insertEmpresa($dados){
			return $this->insert($dados);
		}
		public function editEmpresa($dados, $id){
			return $this->update($dados, "idempresa = '$id'");
		}
		public function deleteEmpresa($id){
			return $this->delete("idempresa = '$id'");
		}
		public function lastInsertIdEmpresa(){			
			return $this->lastInsertId();
		}
	}
	
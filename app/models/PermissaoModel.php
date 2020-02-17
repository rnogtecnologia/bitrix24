<?php
	class PermissaoModel extends Model{
		public $_tabela = "permissao";

		public function lista($where = null, $limit = null, $offset = null, $orderby = null, $sql = null, $groupby = null){
			return $this->read($where, $limit, $offset, $orderby, $sql, $groupby);
		}
		public function getPermissao($id){
			$where = array(
				"cmd" => "idpermissao = ?",
				"params" => array($id)
			);
			return $this->read($where);			
		}
		public function insertPermissao($dados){
			return $this->insert($dados);
		}
		public function editPermissao($dados, $id){
			return $this->update($dados, "idpermissao = '$id'");
		}
		public function deletePermissao($id){
			return $this->delete("idpermissao = '$id'");
		}
	}

<?php
	class ContatoModel extends Model{
		public $_tabela = "usuario";

		public function lista($where = null, $limit = null, $offset = null, $orderby = null, $sql = null, $groupby = null){
			if ($where != null){
				$where['cmd'] .= " and idpermissao = ?";
				$where['params'][] = '3';
			}else{
				$where = array();
				$where['cmd'] = "idpermissao = ?";
				$where['params'] = array('3');
			}

			return $this->read($where, $limit, $offset, $orderby, $sql, $groupby);
		}
		public function getContato($id){
			$where = array(
				"cmd" => "idusuario = ?",
				"params" => array($id)
			);
			return $this->read($where);
		}
		public function insertContato($dados){
			return $this->insert($dados);
		}
		public function editContato($dados, $id){
			return $this->update($dados, "idusuario = '$id'");
		}
		public function deleteContato($id){
			return $this->delete("idusuario = '$id'");
		}
		public function lastInsertIdContato(){			
			return $this->lastInsertId();
		}
	}
	
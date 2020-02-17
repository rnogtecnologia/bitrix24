<?php
	class Negocio extends Controller{
		
		
		public $dir = "admin/";				

		public function index_action(){			

			$this->dados['breadcrumb'][] = array("name"=>"Dashboard","link"=>"/".ROOT,"active"=>"");
			$this->dados['breadcrumb'][] = array("name"=>"Negócios","link"=>"/".ROOT."negocio","active"=>"");
			$this->dados['breadcrumb'][] = array("name"=>"Lista","link"=>"","active"=>"active");

			$this->dados['title'] = 'Negócios';
			$this->dados['action_page'] = '/'.ROOT.'negocio/inserir';
			$this->dados['action_text'] = 'Inserir';
			$this->dados['action_icon'] = 'fa-plus';

			$empresa_model = new EmpresaModel();			
			$contato_model = new ContatoModel();			
			$query_model = new NegocioModel();			
			$queries = $query_model->lista($where);			
			foreach ($queries as $q => $query) {
				$empresa_info = $empresa_model->getEmpresa($query['idempresa']);
				$queries[$q]['empresa'] = $empresa_info[0]['nome'];
				$queries[$q]['cnpj'] = $empresa_info[0]['cnpj'];
				$contato_info = $contato_model->getContato($query['idusuario']);
				$queries[$q]['contato'] = $contato_info[0]['nome'];
				$queries[$q]['cpf'] = $contato_info[0]['cpf'];
				$queries[$q]['status'] = ($query['status']=='S' ? 'Sim' : 'Não');
			}
			$this->dados['queries'] = $queries;
			
			$this->view($this->dir.'negocio_list', $this->dados); 
			
		}
		public function inserir(){			

			$this->dados['breadcrumb'][] = array("name"=>"Dashboard","link"=>"/".ROOT,"active"=>"");
			$this->dados['breadcrumb'][] = array("name"=>"Negócios","link"=>"/".ROOT."negocio","active"=>"");
			$this->dados['breadcrumb'][] = array("name"=>"Cadastro","link"=>"","active"=>"active");

			$this->dados['title'] = 'Negócios';
			$this->dados['action_page'] = '/'.ROOT.'negocio';
			$this->dados['action_text'] = 'Voltar';
			$this->dados['action_icon'] = 'fa-angle-left';

			$query_model = new NegocioModel();			
			$this->dados['action'] = "/".ROOT."negocio/inserir";			

			$empresa_model = new EmpresaModel();
			$this->dados['empresas'] = $empresa_model->lista();
			$contato_model = new ContatoModel();
			$this->dados['contatos'] = $contato_model->lista();
			
			if ($_POST){				
				$request = array_combine(array_keys($_POST), array_values($_POST));	


				$query_insert = $query_model->insertNegocio($request);

				if ($query_insert) {
					$lastInsertIdNegocio = $query_model->lastInsertIdNegocio();

					$this->atualizaNegociosGanhos($request['idempresa']);
					
					$this->dados['msgSuccess'] = "Registro adicionado com sucesso.";
					
				}else{
					$this->dados['msgError'] = "Ocorreu um erro ao adicionar Registro.";
				}
				
				unset($_POST);
				//$this->index_action();
				$this->redir->goToController('negocio');
				exit;
			}

			
			$this->view($this->dir.'negocio_form',$this->dados);
		}
		public function editar(){		

			$this->dados['breadcrumb'][] = array("name"=>"Dashboard","link"=>"/".ROOT,"active"=>"");
			$this->dados['breadcrumb'][] = array("name"=>"Negócios","link"=>"/".ROOT."negocio","active"=>"");
			$this->dados['breadcrumb'][] = array("name"=>"Cadastro","link"=>"","active"=>"active");

			$this->dados['title'] = 'Negócios';
			$this->dados['action_page'] = '/'.ROOT.'negocio';
			$this->dados['action_text'] = 'Voltar';
			$this->dados['action_icon'] = 'fa-angle-left';			

			$query_model = new NegocioModel();			
			$this->dados['action'] = "/".ROOT."negocio/editar";	

			$empresa_model = new EmpresaModel();
			$this->dados['empresas'] = $empresa_model->lista();
			$contato_model = new ContatoModel();
			$this->dados['contatos'] = $contato_model->lista();		
			
			if ($_POST){				
				
				$request = array_combine(array_keys($_POST), array_values($_POST));	
			
				$query_update = $query_model->editNegocio($request, $request['idnegocio']);
				if ($query_update) {
					$this->dados['msgSuccess'] = "Registro atualizado com sucesso.";					
					$this->atualizaNegociosGanhos($request['idempresa']);
				}else{
					$this->dados['msgError'] = "Ocorreu um erro ao atualizar Registro.";
				}
				/*
				$query = $query_model->getNegocio($request['idnegocio']);
				$this->dados['queries'] = $query;
				$this->view($this->dir.'negocio_form', $this->dados);   								
				*/
				$this->redir->goToController('negocio');
				exit;
			}
						
			$query = $query_model->getNegocio($this->getParam('id'));						
									
			$this->dados['queries'] = $query;
			$this->view($this->dir.'negocio_form', $this->dados);  
		}
		public function remover(){
			$query_model = new NegocioModel();			

			$negocios_info = $query_model->getNegocio($this->getParam('id'));
			
			$query_delete = $query_model->deleteNegocio($this->getParam('id'));
			if ($query_delete) {
				$this->dados['msgSuccess'] = "Registro excluído com sucesso.";
				$this->atualizaNegociosGanhos($negocios_info[0]['idempresa']);
			}else{
				$this->dados['msgError'] = "Ocorreu um erro ao excluir Registro.";
			}
			

			$this->index_action();

		}

		public function atualizaNegociosGanhos($idempresa){

			$negocio_model = new NegocioModel();
			$empresa_model = new EmpresaModel();

			$where = array(
				"cmd" => "idempresa = ? and status = ?",
				"params" => array($idempresa, 'S') 
			);
			$negocios_info = $negocio_model->lista($where);
			$negociosGanhos = count($negocios_info);

			$empresa_info = $empresa_model->getEmpresa($idempresa);

			$requestEmpresa = array();
			$requestEmpresa['negocios'] = $negociosGanhos;
			$empresa_update = $empresa_model->editEmpresa($requestEmpresa, $idempresa);

			$this->br24 = new Bitrix24Helper();
			$retorno = $this->br24->atualizaNegociosGanhos($empresa_info[0]['idbr24'], $negociosGanhos);

		}

		
			

	}

<?php
	class Empresa extends Controller{
		
		
		public $dir = "admin/";				

		public function index_action(){			

			$this->dados['breadcrumb'][] = array("name"=>"Dashboard","link"=>"/".ROOT,"active"=>"");
			$this->dados['breadcrumb'][] = array("name"=>"Empresas","link"=>"/".ROOT."empresa","active"=>"");
			$this->dados['breadcrumb'][] = array("name"=>"Lista","link"=>"","active"=>"active");

			$this->dados['title'] = 'Empresas';
			$this->dados['action_page'] = '/'.ROOT.'empresa/inserir';
			$this->dados['action_text'] = 'Inserir';
			$this->dados['action_icon'] = 'fa-plus';

			$query_model = new EmpresaModel();			
			$queries = $query_model->lista($where);			
			
			$this->dados['queries'] = $queries;
			
			$this->view($this->dir.'empresa_list', $this->dados); 
			
		}
		public function inserir(){			

			$this->dados['breadcrumb'][] = array("name"=>"Dashboard","link"=>"/".ROOT,"active"=>"");
			$this->dados['breadcrumb'][] = array("name"=>"Empresas","link"=>"/".ROOT."empresa","active"=>"");
			$this->dados['breadcrumb'][] = array("name"=>"Cadastro","link"=>"","active"=>"active");

			$this->dados['title'] = 'Empresas';
			$this->dados['action_page'] = '/'.ROOT.'empresa';
			$this->dados['action_text'] = 'Voltar';
			$this->dados['action_icon'] = 'fa-angle-left';

			$query_model = new EmpresaModel();			
			$this->dados['action'] = "/".ROOT."empresa/inserir";			
			
			if ($_POST){				
				$request = array_combine(array_keys($_POST), array_values($_POST));	
				

				$query_insert = $query_model->insertEmpresa($request);

				if ($query_insert) {
					$lastInsertIdEmpresa = $query_model->lastInsertIdEmpresa();
					
					$this->dados['msgSuccess'] = "Registro adicionado com sucesso.";
					
				}else{
					$this->dados['msgError'] = "Ocorreu um erro ao adicionar Registro.";
				}
				
				unset($_POST);
				//$this->index_action();
				$this->redir->goToController('empresa');
				exit;
			}

			
			$this->view($this->dir.'empresa_form',$this->dados);
		}
		public function editar(){		

			$this->dados['breadcrumb'][] = array("name"=>"Dashboard","link"=>"/".ROOT,"active"=>"");
			$this->dados['breadcrumb'][] = array("name"=>"Empresas","link"=>"/".ROOT."empresa","active"=>"");
			$this->dados['breadcrumb'][] = array("name"=>"Cadastro","link"=>"","active"=>"active");

			$this->dados['title'] = 'Empresas';
			$this->dados['action_page'] = '/'.ROOT.'empresa';
			$this->dados['action_text'] = 'Voltar';
			$this->dados['action_icon'] = 'fa-angle-left';			

			$query_model = new EmpresaModel();			
			$this->dados['action'] = "/".ROOT."empresa/editar";			
			
			if ($_POST){				
				
				$request = array_combine(array_keys($_POST), array_values($_POST));	
			
				$query_update = $query_model->editEmpresa($request, $request['idempresa']);
				if ($query_update) {
					$this->dados['msgSuccess'] = "Registro atualizado com sucesso.";					
					
				}else{
					$this->dados['msgError'] = "Ocorreu um erro ao atualizar Registro.";
				}
				/*
				$query = $query_model->getEmpresa($request['idempresa']);
				$this->dados['queries'] = $query;
				$this->view($this->dir.'empresa_form', $this->dados);   								
				*/
				$this->redir->goToController('empresa');
				exit;
			}
						
			$query = $query_model->getEmpresa($this->getParam('id'));						
									
			$this->dados['queries'] = $query;
			$this->view($this->dir.'empresa_form', $this->dados);  
		}
		public function remover(){
			$query_model = new EmpresaModel();			
			
			$query_delete = $query_model->deleteEmpresa($this->getParam('id'));
			if ($query_delete) {
				$this->dados['msgSuccess'] = "Registro excluído com sucesso.";
			}else{
				$this->dados['msgError'] = "Ocorreu um erro ao excluir Registro.";
			}
			

			$this->index_action();

		}

		
			

	}

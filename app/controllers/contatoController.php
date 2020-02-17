<?php
	class Contato extends Controller{		
		
		public $dir = "admin/";				

		public function index_action(){			

			$this->dados['breadcrumb'][] = array("name"=>"Dashboard","link"=>"/".ROOT,"active"=>"");
			$this->dados['breadcrumb'][] = array("name"=>"Contatos","link"=>"/".ROOT."contato","active"=>"");
			$this->dados['breadcrumb'][] = array("name"=>"Lista","link"=>"","active"=>"active");

			$this->dados['title'] = 'Contatos';
			$this->dados['action_page'] = '/'.ROOT.'contato/inserir';
			$this->dados['action_text'] = 'Inserir';
			$this->dados['action_icon'] = 'fa-plus';

			$empresa_model = new EmpresaModel();
			$query_model = new ContatoModel();
			$where = array(
				"cmd" => "idusuario > ?",
				"params" => array('0')
			);
			$queries = $query_model->lista($where);			
			foreach ($queries as $q => $query) {
				$empresa_info = $empresa_model->getEmpresa($query['idempresa']);
				$queries[$q]['empresa'] = $empresa_info[0]['nome'];
				$queries[$q]['cnpj'] = $empresa_info[0]['cnpj'];
			}
			
			$this->dados['queries'] = $queries;					
				
			$this->view($this->dir.'contato_list', $this->dados); 
			
		}
		public function inserir(){					

			$this->dados['breadcrumb'][] = array("name"=>"Dashboard","link"=>"/".ROOT,"active"=>"");
			$this->dados['breadcrumb'][] = array("name"=>"Contatos","link"=>"/".ROOT."contato","active"=>"");
			$this->dados['breadcrumb'][] = array("name"=>"Cadastro","link"=>"","active"=>"active");

			$this->dados['title'] = 'Contatos';
			$this->dados['action_page'] = '/'.ROOT.'contato';
			$this->dados['action_text'] = 'Voltar';
			$this->dados['action_icon'] = 'fa-angle-left';

			$empresa_model = new EmpresaModel();		
			$query_model = new ContatoModel();			
			$this->dados['action'] = "/".ROOT."contato/inserir";						
			
			if ($_POST){				
				$request = array_combine(array_keys($_POST), array_values($_POST));	

				$where = array(
					"cmd" => "cnpj = ?",
					"params" => array($request['empresa_cnpj'])
				);
				$empresa_existe = $empresa_model->lista($where);

				$requestEmpresa = array();
				$requestEmpresa['nome'] = $request['empresa_nome'];
				$requestEmpresa['cnpj'] = $request['empresa_cnpj'];
				if ($empresa_existe){
					$empresa_insert = $empresa_model->editEmpresa($requestEmpresa, $empresa_existe[0]['idempresa']);
				}else{
					$empresa_insert = $empresa_model->insertEmpresa($requestEmpresa);
				}
				if ($empresa_insert){

					if ($empresa_existe){
						$lastInsertIdEmpresa = $empresa_existe[0]['idempresa'];
					}else{
						$lastInsertIdEmpresa = $empresa_model->lastInsertIdEmpresa();
					}

					
					$where = array(
						"cmd" => "cpf = ?",
						"params" => array($request['cpf'])
					);
					$contato_existe = $query_model->lista($where);
										
					$request['idempresa'] = $lastInsertIdEmpresa;
					$request['idpermissao'] = 3;				

					$contato = $request;
					unset($request['empresa_nome']);
					unset($request['empresa_cnpj']);

					if ($contato_existe){
						$request['idusuario'] = $contato_existe[0]['idusuario'];
						$query_insert = $query_model->editContato($request, $contato_existe[0]['idusuario']);
					}else{
						$query_insert = $query_model->insertContato($request);
					}

					if ($query_insert) {
						$this->dados['msgSuccess'] = "Registro adicionado com sucesso.";										

						if ($contato_existe){
							$lastInsertIdUsuario = $contato_existe[0]['idusuario'];
						}else{
							$lastInsertIdUsuario = $query_model->lastInsertIdContato();
						}

						$this->br24 = new Bitrix24Helper();
						$retorno = $this->br24->insertContato($contato, $contato_existe, $empresa_existe);
						if ($retorno['idContato']){
							$requestContato = array();
							$requestContato['idbr24'] = $retorno['idContato'];
							$query_update = $query_model->editContato($requestContato, $lastInsertIdUsuario);	
						}
						if ($retorno['idEmpresa']){
							$requestEmpresa = array();
							$requestEmpresa['idbr24'] = $retorno['idEmpresa'];
							$empresa_update = $empresa_model->editEmpresa($requestEmpresa, $lastInsertIdEmpresa);	
						}					
					}else{
						$this->dados['msgError'] = "Ocorreu um erro ao adicionar Registro.";
					}
				}else{
					$this->dados['msgError'] = "Ocorreu um erro ao adicionar Registro.";	
				}
				
				unset($_POST);
				//$this->index_action();
				$this->redir->goToController('contato');
				exit;
			}

			
			$this->view($this->dir.'contato_form',$this->dados);
		}
		public function editar(){		

			$this->dados['breadcrumb'][] = array("name"=>"Dashboard","link"=>"/".ROOT,"active"=>"");
			$this->dados['breadcrumb'][] = array("name"=>"Contatos","link"=>"/".ROOT."contato","active"=>"");
			$this->dados['breadcrumb'][] = array("name"=>"Cadastro","link"=>"","active"=>"active");

			$this->dados['title'] = 'Contatos';
			$this->dados['action_page'] = '/'.ROOT.'contato';
			$this->dados['action_text'] = 'Voltar';
			$this->dados['action_icon'] = 'fa-angle-left';
			
			$empresa_model = new EmpresaModel();	
			$query_model = new ContatoModel();			
			$this->dados['action'] = "/".ROOT."contato/editar";						
			
			if ($_POST){				
				
				$request = array_combine(array_keys($_POST), array_values($_POST));	

				$where = array(
					"cmd" => "cnpj = ?",
					"params" => array($request['empresa_cnpj'])
				);
				$empresa_existe = $empresa_model->lista($where);

				$requestEmpresa = array();
				$requestEmpresa['nome'] = $request['empresa_nome'];
				$requestEmpresa['cnpj'] = $request['empresa_cnpj'];
				if ($empresa_existe){
					$empresa_insert = $empresa_model->editEmpresa($requestEmpresa, $empresa_existe[0]['idempresa']);
				}else{
					$empresa_insert = $empresa_model->insertEmpresa($requestEmpresa);
				}
				if ($empresa_insert){

					if ($empresa_existe){
						$lastInsertIdEmpresa = $empresa_existe[0]['idempresa'];
					}else{
						$lastInsertIdEmpresa = $empresa_model->lastInsertIdEmpresa();
					}

					
					$where = array(
						"cmd" => "cpf = ?",
						"params" => array($request['cpf'])
					);
					$contato_existe = $query_model->lista($where);
										
					$request['idempresa'] = $lastInsertIdEmpresa;
					$request['idpermissao'] = 3;				

					$contato = $request;
					unset($request['empresa_nome']);
					unset($request['empresa_cnpj']);

					if ($contato_existe){
						$request['idusuario'] = $contato_existe[0]['idusuario'];
						$query_insert = $query_model->editContato($request, $contato_existe[0]['idusuario']);
					}else{
						$query_insert = $query_model->insertContato($request);
					}

					if ($query_insert) {
						$this->dados['msgSuccess'] = "Registro adicionado com sucesso.";										

						if ($contato_existe){
							$lastInsertIdUsuario = $contato_existe[0]['idusuario'];
						}else{
							$lastInsertIdUsuario = $query_model->lastInsertIdContato();
						}

						$this->br24 = new Bitrix24Helper();
						$retorno = $this->br24->insertContato($contato, $contato_existe, $empresa_existe);
						if ($retorno['idContato']){
							$requestContato = array();
							$requestContato['idbr24'] = $retorno['idContato'];
							$query_update = $query_model->editContato($requestContato, $lastInsertIdUsuario);	
						}
						if ($retorno['idEmpresa']){
							$requestEmpresa = array();
							$requestEmpresa['idbr24'] = $retorno['idEmpresa'];
							$empresa_update = $empresa_model->editEmpresa($requestEmpresa, $lastInsertIdEmpresa);	
						}					
					}else{
						$this->dados['msgError'] = "Ocorreu um erro ao adicionar Registro.";
					}
				}else{
					$this->dados['msgError'] = "Ocorreu um erro ao adicionar Registro.";	
				}
				/*
				$query = $query_model->getContato($request['idusuario']);												
				$this->dados['queries'] = $query;
				$this->view($this->dir.'contato_form', $this->dados);   								
				*/
				//$this->index_action();
				$this->redir->goToController('contato');
				exit;
			}
						
			$query = $query_model->getContato($this->getParam('id'));
			$empresa_info = $empresa_model->getEmpresa($query[0]['idempresa']);
			$query[0]['empresa_nome'] = $empresa_info[0]['nome'];
			$query[0]['empresa_cnpj'] = $empresa_info[0]['cnpj'];
			$this->dados['queries'] = $query;
			$this->view($this->dir.'contato_form', $this->dados);  
		}
		public function remover(){

			$negocio_model = new NegocioModel();
			$where = array(
				"cmd" => "idusuario = ?",
				"params" => array($this->getParam('id'))
			);
			$negocios_existe = $negocio_model->lista($where);
			if ($negocios_existe){
				$this->dados['msgError'] = "Não é possível excluir este Contato, pois possui Negócios cadastrados.";			
			}else{

				$query_model = new ContatoModel();			
				$query_info = $query_model->getContato($this->getParam('id'));
				$query_delete = $query_model->deleteContato($this->getParam('id'));
				if ($query_delete) {

					$this->dados['msgSuccess'] = "Registro excluído com sucesso.";
					$this->br24 = new Bitrix24Helper();
					$retorno = $this->br24->deleteContato($query_info[0]['idbr24']);

					$where = array(
						"cmd" => "idempresa = ?",
						"params" => array($query_info[0]['idempresa'])
					);
					$contatos_empresa = $query_model->lista($where);

					if (!($contatos_empresa)){
						/*
						$where = array(
							"cmd" => "idempresa = ?",
							"params" => array($query_info[0]['idempresa'])
						);
						$negocios_existe = $negocio_model->lista($where);
						if (!($negocios_existe)){
							*/

							$empresa_model = new EmpresaModel();	
							$empresa_info = $empresa_model->getEmpresa($query_info[0]['idempresa']);
							$empresa_delete = $empresa_model->deleteEmpresa($query_info[0]['idempresa']);
							if ($empresa_delete) {
								
								$this->br24 = new Bitrix24Helper();
								$retorno = $this->br24->deleteEmpresa($empresa_info[0]['idbr24']);
							}

						//}

					}

				}else{
					$this->dados['msgError'] = "Ocorreu um erro ao excluir Registro.";
				}			
				
			}

			$this->index_action();

		}

		public function contatosPorEmpresa(){

			$contato_model = new ContatoModel();
			$where = array(
				"cmd" => "idempresa = ?",
				"params" => array($this->getParam('id'))
			);
			$contatos = $contato_model->lista($where);
			echo json_encode($contatos);

		}
			

	}

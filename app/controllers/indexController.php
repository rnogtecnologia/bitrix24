<?php
	class Index extends Controller{
		public $dir = "index/";			
				
		public function index_action(){
			
			$this->dados['breadcrumb'][] = array("name"=>"Dashborad","link"=>"/".ROOT,"active"=>"");
			$this->dados['breadcrumb'][] = array("name"=>"Meu Dashborad","link"=>"","active"=>"active");

			//$this->dados['template_negocio'] = $this->viewHtml('admin/negocio', $this->dados);

			$negocio_model = new NegocioModel();
			$negocios_info = $negocio_model->lista();
			$this->dados['total_negocios'] = count($negocios_info);

			$contato_model = new ContatoModel();
			$contatos_info = $contato_model->lista();
			$this->dados['total_contatos'] = count($contatos_info);

			$empresa_model = new EmpresaModel();
			$empresas_info = $empresa_model->lista();
			$this->dados['total_empresas'] = count($empresas_info);
			

			$this->view($this->dir.'index', $this->dados);

		}
		
		public function login(){
			$this->dados = array();			

			if ( $this->getParam('acao') ){				
				if ( !($this->auth->setTableName('usuario')
						   ->setUserColumn('login')
						   ->setPassColumn('senha')
						   ->setAuxColumn('idpermissao')
						   ->setUser($_POST['login'])
						   ->setPass(md5($_POST['senha']))
						   ->setAux('0')
						   ->setLoginControllerAction('index', 'index')
						   ->login( "userAuth" )) ){
					$this->dados['msgError'] = "Seus dados de Login e Senha estão inválidos.";
				}
			}

			$this->viewOutput($this->dir.'login', $this->dados);
			
		}
		
		public function forgot(){
			$this->dados = array();
			
			if ( $this->getParam('acao') ){				
				$request = array_combine(array_keys($_POST), array_values($_POST));	
				echo "<pre>"; print_r($request); echo "</pre>";
			}

			$this->viewOutput($this->dir.'forgot', $this->dados);
			
		}
		public function logout(){
			$this->auth->setLogoutControllerAction("index", "login")
					   ->logout( "userAuth" );
		}

		public function denied(){
			        	
			$this->view($this->dir.'denied', $this->dados);

		}
		

	}

?>

<?php
	class Controller extends System{

		public $auth, $db, $dir, $dados;

		protected function view( $nome, $vars = null ){

			list($dir, $pag) = explode('/', $nome);

			ob_start();
      
			if ( is_array($vars) && count($vars) > 0 )
				extract($vars, EXTR_PREFIX_ALL, 'view');			

	  		require( VIEWS . $dir . '/' . $pag . '.phtml');
      
	  		$content = ob_get_contents();

      		ob_end_clean();

			$vars['content'] = $content;

			if ( is_array($vars) && count($vars) > 0 )
				extract($vars, EXTR_PREFIX_ALL, 'view');			

			//$file = VIEWS . 'shared/layout.phtml';
			$file = VIEWS . 'shared/layout_mdb2.phtml';
			
			if (!file_exists($file)){
				//die('Houve um erro. View nao existe.');
				$msg = 'Houve um erro. View não existe.';
				return require_once( 'shared/404.phtml' );	
				die;
			}

			return require_once( $file );

			exit();
		}

		protected function viewOutput( $nome, $vars = null ){

			if ( is_array($vars) && count($vars) > 0 )
				extract($vars, EXTR_PREFIX_ALL, 'view');
			
			ob_start();
      
      		list($dir, $pag) = explode('/', $nome);
      		
	  		require( VIEWS . $dir . '/' . $pag . '.phtml');
      
	  		$output = ob_get_contents();

      		ob_end_clean();
	    	
      		echo $output;
	    		    	
			exit();
		}

		protected function viewHtml( $nome, $vars = null ){

			if ( is_array($vars) && count($vars) > 0 )
				extract($vars, EXTR_PREFIX_ALL, 'view');
			
			ob_start();
      
      		list($dir, $pag) = explode('/', $nome);
      		
	  		require( VIEWS . $dir . '/' . $pag . '.phtml');
      
	  		$output = ob_get_contents();

      		ob_end_clean();
	    	
      		return $output;
	    		    	
			exit();
		}

		protected function viewJson( $result ){
			
      		echo json_encode($result);
	    		    	
			exit();
		}

		public function init(){

			$this->redir = new RedirectorHelper();	

			$this->auth = new AuthHelper();		

			$this->sess = new SessionHelper();
			
			$this->auth->setLoginControllerAction('index', 'login')
					   ->checkLogin('redirect', 'userAuth');

			$this->auth->setLoginControllerAction('index', 'denied')
					   ->checkAcl('redirect', 'userAcl');					   

			$this->db = new UsuarioModel();
			
			$this->dados['userInfo']['codigo'] = $this->auth->userData('idusuario');
			$this->dados['userInfo']['nome'] = $this->auth->userData('nome');
			$this->dados['userInfo']['permissao'] = $this->auth->userData('tipo');			


			$this->dados['controllerCurrent'] = $this->redir->getCurrentController();
			$this->dados['actionCurrent'] = $this->redir->getCurrentAction();			

			$this->sessionHelper = new SessionHelper();
			$this->br24 = new Bitrix24Helper();
		
			if ( !$this->sessionHelper->checkSession( "accessToken" ) ){
				if ( !(isset($_GET['code'])) ){	
					$url = $this->br24->getProtocol()."://".$this->br24->getDomain()."/oauth/authorize/?response_type=code&client_id=".$this->br24->getAppID()."&redirect_uri=".$this->br24->getAppUrl()."&ctrl=".$this->dados['controllerCurrent']."&act=".$this->dados['actionCurrent'];
					$this->br24->redirect($url);
				}
			

				$params = array(
					"grant_type" => "authorization_code",
					"client_id" => $this->br24->getAppID(),
					"client_secret" => $this->br24->getAppKey(),
					"redirect_uri" => $this->br24->getAppUrl(),
					"scope" => array('crm'),
					"code" => $_GET['code'],
				);

				$path = "/oauth/token/";

				$query_data = $this->br24->query("GET", $this->br24->getProtocol()."://".$this->br24->getDomain().$path, $params);

				if (isset($query_data['error'])){
					$url = $this->br24->getProtocol()."://".$this->br24->getDomain()."/oauth/authorize/?response_type=code&client_id=".$this->br24->getAppID()."&redirect_uri=".$this->br24->getAppUrl()."&ctrl=".$this->dados['controllerCurrent']."&act=".$this->dados['actionCurrent'];
					$this->br24->redirect($url);
				}

				if(isset($query_data["access_token"])) {
					$this->sessionHelper->createSession("accessToken", $query_data["access_token"]);
				}
				if(isset($query_data["refresh_token"])) {
					$this->sessionHelper->createSession("refreshToken", $query_data["refresh_token"]);
				}


			}
			

		}


	}


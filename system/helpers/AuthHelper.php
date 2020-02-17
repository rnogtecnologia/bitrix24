<?php
	class AuthHelper{
		protected $sessionHelper, $redirectorHelper, $tableName, $userColumn,
				  $passColumn, $user, $pass, $loginController = 'index', $loginAction = 'index',
				  $logoutController = 'index', $logoutAction = 'index', $auxColumn, $aux;
		public function __construct(){
			$this->sessionHelper = new SessionHelper();
			$this->redirectorHelper = new RedirectorHelper();
			return $this;
		}
		public function setTableName( $val ){
			$this->tableName = $val;
			return $this;
		}
		public function setUserColumn( $val ){
			$this->userColumn = $val;
			return $this;
		}
		public function setPassColumn( $val ){
			$this->passColumn = $val;
			return $this;
		}
		public function setUser( $val ){
			$this->user = $val;
			return $this;
		}
		public function setPass( $val ){
			$this->pass = $val;
			return $this;
		}
		public function setAuxColumn( $val ){
			$this->auxColumn = $val;
			return $this;
		}
		public function setAux( $val ){
			$this->aux = $val;
			return $this;
		}
		public function setLoginControllerAction( $controller, $action ){
			$this->loginController = $controller;
			$this->loginAction = $action;
			return $this;
		}
		public function setLogoutControllerAction( $controller, $action ){
			$this->logoutController = $controller;
			$this->logoutAction = $action;
			return $this;
		}
		public function login( $val ){
			$db = new Model();
			$db->_tabela = $this->tableName;
			//$where = $this->userColumn."='".$this->user."' and ".$this->passColumn."='".$this->pass."'";			
			/*
			// campo permissao
			if ( ( $this->auxColumn != null ) && ( $this->aux != null ) ) {
				$where .= " and ".$this->auxColumn."='".$this->aux."'";				
			}
			*/			
			$where = $this->userColumn."=? and ".$this->passColumn."=?";
			$sql = array();
			$sqlAux = "select u.*,p.*  
					   from usuario u 
					   left join permissao p on p.idpermissao = u.idpermissao 					   
					   where $where limit 1";
			$sql['cmd'] = $sqlAux;
			$sql['params'] = array($this->user, $this->pass);					
			$sql = $db->read(null, null, null, null, $sql); 
			//echo "AuthHelper->login: <pre>"; print_r($sql); echo "</pre>"; die;
			if ( count($sql) > 0 ): 
				$aclAllowed = array();
				if ($sql[0]['tipo'] == 'P') {
					$aclAllowed = array(
						"index", 
						"empresa",
						"contato",
						"negcio",
						"usuario"
					);				
				}else if ($sql[0]['tipo'] == 'A') {
					$aclAllowed = array(
						"index",						
						"usuario",
						"empresa",
						"contato",
						"negocio"
					);
				}
				
				$this->sessionHelper->createSession($val, true)
									->createSession("userData", $sql[0])
									->createSession("userAcl", $aclAllowed);
			else:
				//die('Usuario nao existe.');
				return false;
			endif;
			
			$this->redirectorHelper->goToControllerAction($this->loginController, $this->loginAction);
			return $this;
		}
		public function logout( $val ){
			$this->sessionHelper->deleteSession( $val )
								->deleteSession("userData")
								->deleteSession("userAcl");
			$this->redirectorHelper->goToControllerAction($this->logoutController, $this->logoutAction);
			return $this;
		}
		public function checkLogin( $action, $val ){
			switch ($action) {
				case 'boolean':
					if ( !$this->sessionHelper->checkSession( $val ) )
						return false;
					else
						return true;
					break;
				case 'redirect':
					if ( !$this->sessionHelper->checkSession( $val ) ) {
						if ( $this->redirectorHelper->getCurrentController() != $this->loginController || $this->redirectorHelper->getCurrentAction() != $this->loginAction ) {
							$this->redirectorHelper->goToControllerAction($this->loginController, $this->loginAction);
						}			
					}
					break;
				case 'stop':					
					if ( !$this->sessionHelper->checkSession( $val ) )
						exit;
					break;
			}

		}

		public function checkAcl( $action, $val ){
			switch ($action) {
				case 'boolean':
					if ( !$this->sessionHelper->checkSession( $val ) )
						return false;
					else
						return true;
					break;
				case 'redirect':					
					if ( $this->userAcl() ) {
					//	$this->redirectorHelper->goToControllerAction($this->loginController, $this->loginAction);
					//}else{	

						//echo $this->redirectorHelper->getCurrentController().' - '.$this->redirectorHelper->getCurrentAction().' - '.$this->redirectorHelper->getCurrentAction(); die;
						if ( (!(in_array($this->redirectorHelper->getCurrentController(), $this->userAcl()))) && 
							 //(($this->redirectorHelper->getCurrentController() != 'user') || ($this->redirectorHelper->getCurrentAction() != 'info')) && 
							 //(($this->redirectorHelper->getCurrentController() != 'aviso') || ($this->redirectorHelper->getCurrentAction() != 'info')) && 
							 (($this->redirectorHelper->getCurrentController() != 'index') || ($this->redirectorHelper->getCurrentAction() != 'login') || ($this->redirectorHelper->getCurrentAction() != 'register') )  ){
							if ( $this->redirectorHelper->getCurrentController() != $this->loginController || $this->redirectorHelper->getCurrentAction() != $this->loginAction ) {
								$this->redirectorHelper->goToControllerAction($this->loginController, $this->loginAction);
							}			
						}
					}
					break;
				case 'stop':					
					if ( !$this->sessionHelper->checkSession( $val ) )
						exit;
					break;
			}

		}

		public function userData( $key ){
			$s = $this->sessionHelper->selectSession("userData");
			return $s[$key];
		}

		public function userAcl(){
			$s = $this->sessionHelper->selectSession("userAcl");
			return $s;
		}

	}
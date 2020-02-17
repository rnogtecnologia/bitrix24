<?php
	class Usuario extends Controller{
		
		public $dir = "admin/";

		private function trocaPermissao ($entrada) {
		     $log = array("A"=>"Administrador","P"=>"Petshop","C"=>"Cliente");

		   if (isset($log[$entrada]))
		         return $log[$entrada];
		   else
		         return false;
		}
		private function trocaAtivo ($entrada) {
		     $log = array("N"=>"Não","S"=>"Sim");

		   if (isset($log[$entrada]))
		         return $log[$entrada];
		   else
		         return false;
		}		
		
		public function index_action(){			
			if ($this->dados['userInfo']['permissao'] != 'A'){
				$this->view('index/denied', $this->dados); 	
				exit;
			}
			$usuario_model = new UsuarioModel();			
			$usuarios = $usuario_model->lista();
			$permissao_model = new PermissaoModel();
			foreach ($usuarios as $u => $usuario) {
				$usuarios[$u]['ativo'] = $this->trocaAtivo($usuario['ativo']);
				$permissao = $permissao_model->getPermissao($usuario['idpermissao']);
				$usuarios[$u]['permissao'] = $this->trocaPermissao($permissao[0]['tipo']);
			}
			$this->dados['usuarios'] = $usuarios;			
			
			$this->view($this->dir.'usuario_list', $this->dados); 
			
		}
		public function inserir(){
			if ($this->dados['userInfo']['permissao'] != 'A'){
				$this->view('index/denied', $this->dados); 	
				exit;
			}

			$usuario_model = new UsuarioModel();			
			$this->dados['action'] = "/".ROOT."usuario/inserir";

			$permissao_model = new PermissaoModel();
			$where = array("cmd"=>"tipo = ?", "params" => array('A'));
			$this->dados['permissoes'] = $permissao_model->lista($where);

			$this->dados['usuarios'][0]['idpermissao'] = 1;
		
			if ($_POST){				
				
				$request = array_combine(array_keys($_POST), array_values($_POST));	

				if (empty($request['nome'])){
					$this->dados['usuarios'][0] = $request;
					$this->dados['msgError'] = "Nome deve ser informado.";
					$this->view($this->dir.'usuario_form', $this->dados);   			
					exit;
				}				

				if (empty($request['login'])){
					$this->dados['usuarios'][0] = $request;
					$this->dados['msgError'] = "Login deve ser informado.";
					$this->view($this->dir.'usuario_form', $this->dados);   			
					exit;
				}

				if (empty($request['senhaAlt'])){
					$this->dados['usuarios'][0] = $request;
					$this->dados['msgError'] = "Senha deve ser informada.";
					$this->view($this->dir.'usuario_form', $this->dados);   			
					exit;
				}		

				if (empty($request['idpermissao'])){
					$this->dados['usuarios'][0] = $request;
					$this->dados['msgError'] = "Permissão deve ser informada.";
					$this->view($this->dir.'usuario_form', $this->dados);   			
					exit;
				}/*else{
					$permissao = $permissao_model->getPermissao($request['idpermissao']);					
					if ($permissao[0]['tipo'] == 'V'){
						if (empty($request['idusuario_pai'])){
							$this->dados['usuarios'][0] = $request;
							$this->dados['msgError'] = "Gerente do vendedor deve ser informado.";
							$this->view($this->dir.'usuario_form', $this->dados);   			
							exit;
						}
					}
				}*/

				if ($request['senhaAlt']){
					$request['senha'] = md5($request['senhaAlt']);
				}
				unset($request['senhaAlt']); 				

				
				
				$usuario_insert = $usuario_model->insertUsuario($request);
				if ($usuario_insert) {
					$this->dados['msgSuccess'] = "Usuário adicionado com sucesso.";				
				}else{
					$this->dados['msgError'] = "Ocorreu um erro ao adicionar Usuário.";
				}
				
				unset($_POST);
				$this->redir->goToController('usuario');
				exit;
			}

			
			$this->view($this->dir.'usuario_form',$this->dados);
		}
		public function editar(){		

			$id = $this->getParam('id');
			if (($this->dados['userInfo']['permissao'] == 'P') && ($this->dados['userInfo']['codigo'] != $id)) {
				$this->view('index/denied', $this->dados); 	
				exit;
			}
			
			$usuario_model = new UsuarioModel();			
			$this->dados['action'] = "/".ROOT."usuario/editar";			

			$permissao_model = new PermissaoModel();
			$where = array("cmd"=>"tipo = ?", "params" => array('A'));
			$this->dados['permissoes'] = $permissao_model->lista($where);

			if ($_POST){				
				
				$request = array_combine(array_keys($_POST), array_values($_POST));	

				if (empty($request['nome'])){
					$this->dados['usuarios'][0] = $request;
					$this->dados['msgError'] = "Nome deve ser informado.";
					$this->view($this->dir.'usuario_form', $this->dados);   			
					exit;
				}				

				if (empty($request['login'])){
					$this->dados['usuarios'][0] = $request;
					$this->dados['msgError'] = "Login deve ser informado.";
					$this->view($this->dir.'usuario_form', $this->dados);   			
					exit;
				}				


				if (empty($request['idpermissao'])){
					$this->dados['usuarios'][0] = $request;
					$this->dados['msgError'] = "Permissão deve ser informada.";
					$this->view($this->dir.'usuario_form', $this->dados);   			
					exit;
				}/*else{
					$permissao = $permissao_model->getPermissao($request['idpermissao']);					
					if ($permissao[0]['tipo'] == 'V'){
						if (empty($request['idusuario_pai'])){
							$this->dados['usuarios'][0] = $request;
							$this->dados['msgError'] = "Gerente do vendedor deve ser informado.";
							$this->view($this->dir.'usuario_form', $this->dados);   			
							exit;
						}
					}
				}*/

				if ($request['senhaAlt']){
					$request['senha'] = md5($request['senhaAlt']);
				}
				unset($request['senhaAlt']); 
				
				$usuario_update = $usuario_model->editUsuario($request, $request['idusuario']);
				if ($usuario_update) {
					$this->dados['userInfo']['nome'] = $request['nome'];
					$this->dados['msgSuccess'] = "Usuário atualizado com sucesso.";				

				}else{
					$this->dados['msgError'] = "Ocorreu um erro ao atualizar Usuário.";
				}
				/*
				$usuario = $usuario_model->getUsuario($request['idusuario']);	
				if ($usuario[0]['categoria']){
					//$usuario[0]['categoria'] = json_decode($usuario[0]['categoria']);
				}		
				$this->dados['usuarios'] = $usuario;
				$this->view($this->dir.'usuario_form', $this->dados);   								
				*/
				$this->redir->goToController('usuario');
				exit;
			}
			
			if ( ($this->dir != 'admin/') && ( $this->auth->usuarioData('idusuario') != $this->getParam('id') ) ) {
				$this->dados['msgError'] = "Você não tem permissão para editar este Usuário.";
				$this->view($this->dir.'usuario_form', $this->dados);   								
				exit;
			}
			
			$usuario = $usuario_model->getUsuario($this->getParam('id'));
			
			$this->dados['usuarios'] = $usuario;
			$this->view($this->dir.'usuario_form', $this->dados);   			
		}
		public function remover(){
			if ($this->dados['userInfo']['permissao'] != 'A'){
				$this->view('index/denied', $this->dados); 	
				exit;
			}
			
			$usuario_model = new UsuarioModel();			
			
			$usuario_delete = $usuario_model->deleteUsuario($this->getParam('id'));			
			if ($usuario_delete) {
				$this->dados['msgSuccess'] = "Usuário excluído com sucesso.";
			}else{
				$this->dados['msgError'] = "Ocorreu um erro ao excluir Usuário.";
			}
			
			$this->index_action();
			 			
		}

		public function info(){
			
			$usuario_model = new UsuarioModel();			
			$this->dados['action'] = "/".ROOT."usuario/editar";			
			
			if ( ($this->dir != 'admin/') && ( $this->auth->usuarioData('idusuario') != $this->getParam('id') ) ) {
				$this->dados['msgError'] = "Você não tem permissão para editar este Usuário.";
				$this->view($this->dir.'usuario_form', $this->dados);   								
				exit;
			}
			
			$usuarios = $usuario_model->getUsuario($this->getParam('id'));			
			$permissao_model = new PermissaoModel();
					
			foreach ($usuarios as $u => $usuario) {		
				$usuarios[$u]['ativo'] = $this->trocaAtivo($usuario['ativo']);
				$permissao = $permissao_model->getPermissao($usuario['idpermissao']);
				$usuarios[$u]['permissao'] = $this->trocaPermissao($permissao[0]['tipo']);								
			}
			$this->dados['usuarios'] = $usuarios;
			$this->view($this->dir.'usuario_info', $this->dados);   			
		}
		

	}
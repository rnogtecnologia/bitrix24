<?php
	class Bitrix24Helper{
		
		public $domain = 'b24-852tz2.bitrix24.com.br';
		public $appId = 'local.5e4582b99e2514.38718191';
		public $appKey = '8TXN6tw0HH3qCNBzRJj8eWJ6EDISWChoFuGctg08z6yhNjjyuO';		
		public $path = '/index.php';
		public $appUrl = 'https://bitrix24.rnog.com.br/';
		public $scope = array('crm');
		public $protocol = 'https';

		public function getDomain(){
			return $this->domain;
		}
		public function getAppID(){
			return $this->appId;
		}
		public function getAppKey(){
			return $this->appKey;
		}
		public function getAppUrl(){
			return $this->appUrl;
		}
		public function getProtocol(){
			return $this->protocol;
		}
		
		public function redirect($url, $ctrl = null, $act = null)
		{
			//Header("HTTP 302 Found");
			Header("Location: ".$url);
			die();
		}

		public function query($method, $url, $data = null)
		{		

			$query_data = "";

			$curlOptions = array(
				CURLOPT_RETURNTRANSFER => true
			);
			if($method == "POST")
			{
				$curlOptions[CURLOPT_POST] = true;
				$curlOptions[CURLOPT_POSTFIELDS] = http_build_query($data);
				$curlOptions[CURLOPT_FOLLOWLOCATION] = true;
				$curlOptions[CURLOPT_CUSTOMREQUEST] = "POST";				
			}
			elseif(!empty($data))
			{
				$url .= strpos($url, "?") > 0 ? "&" : "?";
				$url .= http_build_query($data);
			}

			$curl = curl_init($url);
			curl_setopt_array($curl, $curlOptions);
			$result = curl_exec($curl);
			
			return json_decode($result, 1);
		}

		public function call($domain, $method, $params)
		{
			return $this->query("POST", $this->protocol."://".$domain."/rest/".$method, $params);
		}

		public function insertContato($contato, $contato_existe, $empresa_existe){

			$this->sessionHelper = new SessionHelper();
			$accessToken = $this->sessionHelper->selectSession("accessToken");
			$refreshToken = $this->sessionHelper->selectSession("refreshToken");
			//echo "accessToken<pre>$accessToken</pre>";
			//echo "refreshToken<pre>$refreshToken</pre>";

			$params = array(
				"grant_type" => "refresh_token",
				"client_id" => $this->getAppID(),
				"client_secret" => $this->getAppKey(),
				"redirect_uri" => $this->getAppUrl(),
				"scope" => array('crm'),
				"refresh_token" => $refreshToken,
			);

			$path = "/oauth/token/";

			$query_data = $this->query("GET", $this->getProtocol()."://".$this->getDomain().$path, $params);
			if(isset($query_data["access_token"])) {
				$this->sessionHelper->createSession("accessToken", $query_data["access_token"]);
			}
			if(isset($query_data["refresh_token"])) {
				$this->sessionHelper->createSession("refreshToken", $query_data["refresh_token"]);
			}			

			$accessToken = $this->sessionHelper->selectSession("accessToken");			

			if ($empresa_existe){
				$method = 'crm.company.update';
				$params = array("auth"=>$accessToken, "id" => $empresa_existe[0]['idbr24'], "fields"=>array("TITLE" => $contato['empresa_nome']));			
				$responseCompany = $this->call($this->getDomain(), $method, $params);
				$idEmpresa = $empresa_existe[0]['idbr24'];
			}else{
				$method = 'crm.company.add';
				$params = array("auth"=>$accessToken, "fields"=>array("TITLE" => $contato['empresa_nome'], "UF_CRM_1581695471" => $contato['empresa_cnpj']));			
				$responseCompany = $this->call($this->getDomain(), $method, $params);
				$idEmpresa = $responseCompany['result'];
			}			

			if ($contato_existe){
				$method = 'crm.contact.update';
				$params = array("auth"=>$accessToken, "id" => $contato_existe[0]['idbr24'], "fields"=>array("NAME" => $contato['nome'], "COMPANY_ID" => $idEmpresa));
				echo "<pre>"; print_r($params); echo "</pre>"; die;
				$responseContact = $this->call($this->getDomain(), $method, $params);
				$idContato = $contato_existe[0]['idbr24'];
			}else{
				$method = 'crm.contact.add';
				$params = array("auth"=>$accessToken, "fields"=>array("NAME" => $contato['nome'], "UF_CRM_1581695405" => $contato['cpf'], "COMPANY_ID" => $idEmpresa));			
				$responseContact = $this->call($this->getDomain(), $method, $params);
				$idContato = $responseContact['result'];
			}

			$return = array(
				"idContato" => $idContato,
				"idEmpresa" => $idEmpresa
			);

			return $return;
		}

		public function deleteContato($id){

			$this->sessionHelper = new SessionHelper();
			$accessToken = $this->sessionHelper->selectSession("accessToken");
			$refreshToken = $this->sessionHelper->selectSession("refreshToken");			

			$params = array(
				"grant_type" => "refresh_token",
				"client_id" => $this->getAppID(),
				"client_secret" => $this->getAppKey(),
				"redirect_uri" => $this->getAppUrl(),
				"scope" => array('crm'),
				"refresh_token" => $refreshToken,
			);

			$path = "/oauth/token/";

			$query_data = $this->query("GET", $this->getProtocol()."://".$this->getDomain().$path, $params);
			if(isset($query_data["access_token"])) {
				$this->sessionHelper->createSession("accessToken", $query_data["access_token"]);
			}
			if(isset($query_data["refresh_token"])) {
				$this->sessionHelper->createSession("refreshToken", $query_data["refresh_token"]);
			}			

			$accessToken = $this->sessionHelper->selectSession("accessToken");						

			$method = 'crm.contact.delete';
			$params = array("auth"=>$accessToken, "id"=>$id);			
			$responseContact = $this->call($this->getDomain(), $method, $params);			
			
		}

		public function atualizaNegociosGanhos($id, $qtde){

			$this->sessionHelper = new SessionHelper();
			$accessToken = $this->sessionHelper->selectSession("accessToken");
			$refreshToken = $this->sessionHelper->selectSession("refreshToken");			

			$params = array(
				"grant_type" => "refresh_token",
				"client_id" => $this->getAppID(),
				"client_secret" => $this->getAppKey(),
				"redirect_uri" => $this->getAppUrl(),
				"scope" => array('crm'),
				"refresh_token" => $refreshToken,
			);

			$path = "/oauth/token/";

			$query_data = $this->query("GET", $this->getProtocol()."://".$this->getDomain().$path, $params);
			if(isset($query_data["access_token"])) {
				$this->sessionHelper->createSession("accessToken", $query_data["access_token"]);
			}
			if(isset($query_data["refresh_token"])) {
				$this->sessionHelper->createSession("refreshToken", $query_data["refresh_token"]);
			}			

			$accessToken = $this->sessionHelper->selectSession("accessToken");						

			$method = 'crm.company.update';
			$params = array("auth"=>$accessToken, "id" => $id, "fields"=>array("UF_CRM_1581707850" => $qtde));			
			$responseCompany = $this->call($this->getDomain(), $method, $params);			
			
		}

		public function deleteEmpresa($id){

			$this->sessionHelper = new SessionHelper();
			$accessToken = $this->sessionHelper->selectSession("accessToken");
			$refreshToken = $this->sessionHelper->selectSession("refreshToken");			

			$params = array(
				"grant_type" => "refresh_token",
				"client_id" => $this->getAppID(),
				"client_secret" => $this->getAppKey(),
				"redirect_uri" => $this->getAppUrl(),
				"scope" => array('crm'),
				"refresh_token" => $refreshToken,
			);

			$path = "/oauth/token/";

			$query_data = $this->query("GET", $this->getProtocol()."://".$this->getDomain().$path, $params);
			if(isset($query_data["access_token"])) {
				$this->sessionHelper->createSession("accessToken", $query_data["access_token"]);
			}
			if(isset($query_data["refresh_token"])) {
				$this->sessionHelper->createSession("refreshToken", $query_data["refresh_token"]);
			}			

			$accessToken = $this->sessionHelper->selectSession("accessToken");						

			$method = 'crm.company.delete';
			$params = array("auth"=>$accessToken, "id"=>$id);			
			$responseCompany = $this->call($this->getDomain(), $method, $params);			
			
		}


	}




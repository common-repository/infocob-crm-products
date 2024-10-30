<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob\Tools;
	
	use Infocob\CRM\Products\Admin\Classes\Infocob\CloudFichier;
	use Infocob\CRM\Products\Admin\Classes\Infocob\DicoInfocob as D;
	use Infocob\CRM\Products\Admin\Classes\Infocob\InfocobDB;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Parametres20;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class GoogleDrive {
		
		protected $version = 2;
		protected $tentative = 0;
		
		protected $item_id;
		protected $fc_config = false;
		protected $fc_code;
		
		protected $account_name;
		protected $account_login;
		protected $remote_dir;
		
		protected $client_id;
		protected $client_secret;
		
		protected $refresh_token;
		protected $access_token;
		
		private $crypt_key = "infocob85180";
		
		public function __construct($config = false) {
			if(isset($_SESSION["cloudfichier_token"])) {
				unset($_SESSION["cloudfichier_token"]);
			}
			
			if($config === false) {
				$config              = Parametres20::getFcConfigForUpload();
				$this->account_name  = $config["NAME"] ?? false;
				$this->account_login = $config["LOGIN"] ?? false;
				$this->fc_config     = $config["ID"] ?? false;
				$this->remote_dir    = $config["REMOTE_DIR"] ?? false;
			} else {
				$this->fc_config = $config["FC_CONFIG"] ?? false;
				$this->loadFcConfig($config["FC_PARAM"] ?? "");
			}
			
			$this->loadParConfig();
			$this->loadClientAccess();
			$this->loadRefreshToken();
		}
		
		public function isAvailable() {
			return !empty($this->account_name && $this->account_login);
		}
		
		public function loadFcConfig($config_ini) {
			// FC_PARAM INI
			$config_fc_parsed = parse_ini_string($config_ini, true, INI_SCANNER_RAW);
			$config_fc        = $config_fc_parsed["CLOUDFICHIER_GOOGLE.GSUITE"] ?? [];
			$this->item_id    = $config_fc["ITEM_ID"] ?? false;
		}
		
		protected function loadParConfig() {
			// PAR_PARAM INI
			$result = Parametres20::getConfigCloudfichier();
			
			if($result) {
				$config_ini = $result["PAR_PARAMETRE"] ?? false;
				
				if($config_ini) {
					$config_arr     = [];
					$config_ini_arr = explode('[CLOUD_FICHIER_CONFIG]', $config_ini);
					for($i = 1; $i < count($config_ini_arr); $i ++) {
						$config_ini                  = $config_ini_arr[ $i ];
						$config                      = parse_ini_string($config_ini);
						$config_arr[ $config['ID'] ] = $config;
					}
					
					$config = $config_arr[ $this->fc_config ] ?? [];
					
					if(!empty($config)) {
						$this->account_name  = $config["NAME"] ?? false;
						$this->account_login = $config["LOGIN"] ?? false;
						$this->remote_dir    = $config["REMOTE_DIR"] ?? false;
					}
				}
			}
		}
		
		protected function loadClientAccess() {
			$sql = "SELECT * "
			       . "FROM L_TOKEN "
			       . "WHERE LTT_SERVICE = 'GOOGLE.GSUITE' "
			       . "AND LTT_IDENT = :login "
			       . "AND LTT_NOM = 'GOOGLE_APIKEY' ";
			
			$args = [
				":login" => $this->account_login
			];
			
			$result = InfocobDB::getInstance()->fetch($sql, $args);
			
			if($result) {
				$client_access = filter_var($result["LTT_VALEUR"], FILTER_VALIDATE_INT) ?? false;
				if($client_access === 0) {
					$this->client_id     = "265989456804-mfkjp796u2i2g6alhkgg554cugsm4g9q.apps.googleusercontent.com";
					$this->client_secret = "gwYrwZILWOyRfkLroMl6Szu2";
				} else if($client_access === 1) {
					$this->client_id     = "462894017359-f1b4b4avu4ugbqgurkdf76ecs6n2hjd3.apps.googleusercontent.com";
					$this->client_secret = "TqkA1wPuOtBiOgETGt5xKB0f";
				}
			}
		}
		
		protected function loadRefreshToken() {
			$sql = "SELECT * "
			       . "FROM L_TOKEN "
			       . "WHERE LTT_SERVICE = 'GOOGLE.GSUITE' "
			       . "AND LTT_IDENT = :login "
			       . "AND LTT_NOM = 'REFRESH_TOKEN' ";
			
			$args = [
				":login" => $this->account_login
			];
			
			$result = InfocobDB::getInstance()->fetch($sql, $args);
			
			if($result) {
				$token = $result["LTT_VALEUR"] ?? false;
				if($token) {
					$this->refresh_token = $this->decrypte($token, $this->crypt_key);
				}
			}
		}
		
		public function download($return = false) {
			if($this->tentative >= 3) {
				throw new \Exception("Erreur, le token d'accès est invalide !", 401);
			}
			
			if(!isset($_SESSION["cloudfichier_token"])) {
				$this->access_token = $this->getAccessToken();
			} else {
				$this->access_token = sanitize_text_field($_SESSION["cloudfichier_token"]);
			}
			
			$config_file_json = $this->getFile();
			
			if($config_file_json) {
				$config_file = json_decode($config_file_json, true);
				if(isset($config_file["error"])) {
					unset($_SESSION["cloudfichier_token"]);
					$this->download($return);
					exit();
				}
				
				if(!empty($this->fc_code)) {
					$cloudfichier = new CloudFichier();
					$cloudfichier->load($this->fc_code);
					if(!empty($cloudfichier->getID()) && !empty($cloudfichier->get(D::fc_nom_fichier))) {
						$file_name = $cloudfichier->get(D::fc_nom_fichier);
					}
				}
				
				$mime_type = $config_file["mimeType"] ?? "text/plain charset=utf-8";
				$file_name = !empty($file_name) ? trim($file_name) : trim(($config_file["title"] ?? false));
				
				$file = $this->getFile(true);
				
				if($return) {
					return [
						"success"   => true,
						"mime_type" => $mime_type,
						"file_name" => $file_name,
						"file"      => $file,
					];
				}
				
				if($file) {
					header('Content-Description: File Transfer');
					//header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Cache-Control: public, must-revalidate, max-age=0');
					header('Content-Type: ' . $mime_type . "; charset=utf-8");
					
					if($file_name) {
						header('Content-Disposition: attachment; filename="' . $file_name . '"');
					} else {
						header('Content-Disposition: attachment');
					}
					
					echo $file;
					die();
				}
			}
		}
		
		public function get() {
			if($this->tentative >= 3) {
				throw new \Exception("Erreur, le token d'accès est invalide !", 401);
			}
			
			if(!isset($_SESSION["cloudfichier_token"])) {
				$this->access_token = $this->getAccessToken();
			} else {
				$this->access_token = sanitize_text_field($_SESSION["cloudfichier_token"]);
			}
			
			$drive = new GoogleDriveApi($this->access_token);
			try {
				$file = $drive->getFile($this->item_id);
				
				return $file;
			} catch(\Exception $e) {
				unset($_SESSION["cloudfichier_token"]);
				$this->get();
				exit();
			}
		}
		
		public function delete($fc_code, $item_id) {
			if($this->tentative >= 3) {
				throw new \Exception("Erreur, le token d'accès est invalide !", 401);
			}
			
			if(!isset($_SESSION["cloudfichier_token"])) {
				$this->access_token = $this->getAccessToken();
			} else {
				$this->access_token = sanitize_text_field($_SESSION["cloudfichier_token"]);
			}
			
			$googleDriveApi = new GoogleDriveApi($this->access_token);
			
			$cloudfichier = new CloudFichier();
			$cloudfichier->load($fc_code);
			if(!empty($cloudfichier->getID())) {
				if($cloudfichier->delete()) {
					try {
						$deleted_folder = $googleDriveApi->deleteFile($item_id);
						
						return [
							"success" => true,
							"fc_code" => $cloudfichier->getID(),
							"item_id" => $item_id,
						];
					} catch(\Exception $exception) {
						if($exception->getCode() === 401) {
							unset($_SESSION["cloudfichier_token"]);
							$this->delete($fc_code, $item_id);
							
							return [
								"success" => false,
								"fc_code" => $cloudfichier->getID(),
								"item_id" => $item_id,
							];
						} else {
							throw new \Exception("Erreur lors de la suppression du fichier " . $item_id . " dans le drive", 500);
						}
					}
				}
			}
			
			throw new \Exception("Erreur lors de la suppression de l'enregistrement " . $fc_code . " de la table cloudfichier", 500);
		}
		
		protected function getFile($download = false) {
			$download_param = $download ? "?alt=media" : "";
			$url            = "https://www.googleapis.com/drive/v2/files/" . $this->item_id . $download_param;
			
			$args = [
				'headers' => [
					'Authorization' => 'Bearer ' . $this->access_token,
					'Content-Type' => 'application/json'
				]
			];
			$result = wp_remote_retrieve_body(wp_remote_get($url, $args));
			
			return $result;
		}
		
		protected function getAccessToken() {
			$this->tentative ++;
			
			$url         = "https://oauth2.googleapis.com/token";
			
			$args = [
				'body' => [
					"client_id"     => $this->client_id,
					"client_secret" => $this->client_secret,
					"grant_type"    => "refresh_token",
					"refresh_token" => $this->refresh_token,
				],
				'headers' => [
					'Authorization' => 'Bearer ' . $this->access_token,
				],
			];
			$result = wp_remote_retrieve_body(wp_remote_post($url, $args));
			
			$access_token = false;
			if($result) {
				$result       = json_decode($result, true);
				$access_token = $result["access_token"] ?? false;
				if($access_token) {
					if($_SERVER["SERVER_ADDR"] == "127.0.0.1") {
						$access_token = "ya29.a0ARrdaM-W6ulLHNHno7_B-iW8bTxBY622fu6zdFohoLXNurv5Vq2RBiiVvck2jIHSR-omNlE9ZRjK9iyXDXFAphFCrxDGJ598d2aNhmD9_LXn_en01Ng7ePaU1ir0qTqEvQWYII__w3bvfdIPfx8GuNpp2E0U"; // @TODO test
					}
					$_SESSION["cloudfichier_token"] = $access_token;
				} else {
					unset($_SESSION["cloudfichier_token"]);
				}
			}
			
			return $access_token;
		}
		
		protected function generateKey($string, $cryptKey) {
			$cryptKey = md5($cryptKey);
			$counter  = 0;
			$varTemp  = "";
			for($Ctr = 0; $Ctr < strlen($string); $Ctr ++) {
				if($counter == strlen($cryptKey)) {
					$counter = 0;
				}
				$varTemp .= substr($string, $Ctr, 1) ^ substr($cryptKey, $counter, 1);
				$counter ++;
			}
			
			return $varTemp;
		}
		
		protected function crypte($string, $key) {
			srand((double) microtime() * 1000000);
			$cryptKey = md5(rand(0, 32000));
			$counter  = 0;
			$varTemp  = "";
			for($Ctr = 0; $Ctr < strlen($string); $Ctr ++) {
				if($counter == strlen($cryptKey)) {
					$counter = 0;
				}
				$varTemp .= substr($cryptKey, $counter, 1) . (substr($string, $Ctr, 1) ^ substr($cryptKey, $counter, 1));
				$counter ++;
			}
			
			return base64_encode($this->generateKey($varTemp, $key));
		}
		
		protected function decrypte($string, $key) {
			$string  = $this->generateKey(base64_decode($string), $key);
			$varTemp = "";
			for($Ctr = 0; $Ctr < strlen($string); $Ctr ++) {
				$md5 = substr($string, $Ctr, 1);
				$Ctr ++;
				$varTemp .= (substr($string, $Ctr, 1) ^ $md5);
			}
			
			return $varTemp;
		}
		
		/**
		 * @return mixed
		 */
		public function getItemId() {
			return $this->item_id;
		}
		
		/**
		 * @return mixed
		 */
		public function getFcConfig() {
			return $this->fc_config;
		}
		
		/*
		 * exemple de la table parametres20
			[CLOUD_FICHIER_CONFIG]
			ID={BEB759E8-3A51-44B1-8434-6B0CED7B127E}
			NAME=Infocob
			DRIVE_TYPE=0
			LOGIN=drive@infocob-solutions.com
			REMOTE_DIR=INFOCOB
			UPLOAD_SPEED=0
			DOWNLOAD_SPEED=0
		 */
		
		/*
		 * exemple de la table cloudfichier
			[CLOUDFICHIER_GOOGLE.GSUITE]
			ITEM_ID=1VxGVv4_Guu1yQb8i_-mT0uMjmGqXv4VM
			DOWNLOAD_LOCATION=https://www.googleapis.com/drive/v2/files/1VxGVv4_Guu1yQb8i_-mT0uMjmGqXv4VM?alt=media&source=downloadUrl
			PARENT_ID=1-aayDIA0vw2sVLzUAoYaBYG4nFsrTZ1J
			RESUME_LOCATION=
			REMOTE_DIR=INFOCOB/CloudFichierL/0/379COB
		 */
		
		public function generateFcParam($item_id, $download_location, $parent_id, $remote_dir) {
			$header = "[CLOUDFICHIER_GOOGLE.GSUITE]\n";
			$body   = "ITEM_ID=" . $item_id . "\n";
			$body   .= "DOWNLOAD_LOCATION=" . $download_location . "\n";
			$body   .= "PARENT_ID=" . $parent_id . "\n";
			$body   .= "RESUME_LOCATION=" . "\n";
			$body   .= "REMOTE_DIR=" . $remote_dir . "\n";
			
			return $header . $body;
		}
		
		public static function getQuotaFolder() {
			$sql    = "SELECT FC_CODE FROM CLOUDFICHIER ORDER BY FC_DATECREATION DESC";
			$result = InfocobDB::getInstance()->fetch($sql);
			
			$quota_folder = 0;
			if(!empty($result["FC_CODE"])) {
				$fc_code = $result["FC_CODE"];
				preg_match('/^[0-9]+/', $fc_code, $matches);
				if(isset($matches[0])) {
					$quota_folder = (int) filter_var(floor($matches[0] / 10000), FILTER_SANITIZE_NUMBER_INT);
				}
			}
			
			return $quota_folder;
		}
		
		/**
		 * @return mixed
		 */
		public function getFcCode() {
			return $this->fc_code;
		}
		
		/**
		 * @param mixed $fc_code
		 */
		public function setFcCode($fc_code): void {
			$this->fc_code = $fc_code;
		}
		
	}

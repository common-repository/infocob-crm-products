<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob\Tools;
	
	use Infocob\CRM\Products\Admin\Classes\Infocob\CloudFichier;
	use Infocob\CRM\Products\Admin\Classes\Infocob\DicoInfocob as D;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Dictionnaire;
	use Infocob\CRM\Products\Admin\Classes\Infocob\InfocobDB;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class CloudFichierManager {
		
		protected $entity;
		protected $service = [];
		protected $config_key = false;
		protected $fc_code = false;
		
		public function __construct($fc_code = false) {
			$this->fc_code = $fc_code;
			if($this->fc_code !== false) {
				$this->loadConfig();
			}
		}
		
		protected function loadConfig() {
			$cloudfichier = new CloudFichier();
			$cloudfichier->load($this->fc_code);
			$code             = $cloudfichier->get(D::fc_code_maitre);
			$table_code       = $cloudfichier->get(D::fc_index_table);
			$table_name       = CloudFichier::$tableName;
			$this->config_key = $cloudfichier->get(D::fc_config);
			
			$sql = "SELECT * FROM "
			       . $table_name . " "
			       . "WHERE " . D::fc_index_table . " = :table_code "
			       . "AND " . D::fc_code_maitre . " = :code "
			       . "AND " . D::fc_config . " = :config_key "
			       . "AND COALESCE(" . D::fc_traite . ", " . CloudFichier::CLOUD_ETAT_PRET . ") = " . CloudFichier::CLOUD_ETAT_PRET . " "
			       . "AND " . CloudFichier::GetDroitCondition();
			
			$params = [
				":code"       => $code,
				":config_key" => $this->config_key,
				":table_code" => $table_code,
			];
			
			$results = InfocobDB::getInstance()->fetchAll($sql, $params);
			
			if(!empty($results)) {
				$this->loadService($results);
			}
		}
		
		public function download($return = false) {
			$cloudfichier = new CloudFichier();
			$cloudfichier->load($this->fc_code);
			$item_id = $cloudfichier->getItemId();
			
			if(isset($item_id)) {
				try {
					foreach($this->service as $service) {
						if($service->getItemId() === $item_id) {
							$service->setFcCode($this->fc_code);
							$file = $service->download($return);
							if($return) {
								return $file;
							}
						}
					}
				} catch(\Exception $exception) {
					if($return) {
						return [
							"success" => false,
							"message" => $exception->getMessage(),
							"code"    => $exception->getCode()
						];
					} else {
						echo esc_html($exception->getMessage() . " (" . $exception->getCode() . ")");
					}
				}
			} else {
				if($return) {
					return [
						"success" => false,
						"message" => "Cloud service not found",
						"code"    => 404
					];
				} else {
					echo "Cloud service not found";
				}
			}
		}
		
		public function get() {
			$cloudfichier = new CloudFichier();
			$cloudfichier->load($this->fc_code);
			$item_id = $cloudfichier->getItemId();
			
			if(isset($item_id)) {
				try {
					foreach($this->service as $service) {
						if($service->getItemId() === $item_id) {
							$service->setFcCode($this->fc_code);
							
							return $service->get();
						}
					}
				} catch(\Exception $exception) {
					return [
						"success" => false,
						"message" => $exception->getMessage(),
						"code"    => $exception->getCode()
					];
				}
			} else {
				return [
					"success" => false,
					"message" => "Cloud service not found",
					"code"    => 404
				];
			}
		}
		
		public function delete($fc_code, $service_id = CloudFichier::CLOUD_TYPE_GDRIVE) {
			$cloudfichier = new CloudFichier();
			$cloudfichier->load($fc_code);
			
			try {
				if($service_id === CloudFichier::CLOUD_TYPE_GDRIVE) {
					if(!empty($cloudfichier->getID())) {
						$cloudfichier->set(D::fc_droit, "XIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIII");
						if($cloudfichier->maj()) {
							return [
								"success" => true,
								"fc_code" => $fc_code,
								"item_id" => $cloudfichier->getItemId($service_id),
							];
						}
					}
				}
				
				return [
					"success" => false,
					"fc_code" => $fc_code,
					"item_id" => $cloudfichier->getItemId($service_id),
				];
			} catch(\Exception $exception) {
				return [
					"success" => false,
					"fc_code" => $fc_code,
					"item_id" => $cloudfichier->getItemId($service_id),
					"message" => $exception->getMessage()
				];
			}
		}
		
		protected function loadService($configs) {
			foreach($configs as $config) {
				$index_cloud = $config["FC_INDEXCLOUD"] ?? false;
				if($index_cloud == 4) {
					$this->service[] = new GoogleDrive($config);
				}
			}
		}
		
		public static function hasCloudService($service_id = false) {
			// @TODO others services
			if(Dictionnaire::isTableExists("PARAMETRES20") && Dictionnaire::isTableExists("CLOUDFICHIER")) {
				foreach ([CloudFichier::CLOUD_TYPE_GDRIVE] as $id) {
					if ($id === CloudFichier::CLOUD_TYPE_GDRIVE) {
						$service = new GoogleDrive();
					}
					
					if (isset($service)) {
						if ($service_id === false && $service->isAvailable()) {
							return true;
						} else if ($service_id !== false && $service_id === $id) {
							return $service->isAvailable();
						}
					}
				}
			}
			
			return false;
		}
		
	}

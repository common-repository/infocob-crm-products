<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob\Tools;
	
	use Exception;
	use Google_Client;
	use Google_Service_Drive;
	use Google_Service_Drive_DriveFile;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class GoogleDriveApi {
		
		protected $access_token = null;
		protected $service = null;
		protected $client = null;
		
		/**
		 * GoogleDriveApi constructor.
		 *
		 * @param string $access_token
		 */
		public function __construct($access_token) {
			$this->access_token = $access_token;
			
			$client = new Google_Client();
			$client->setAccessToken($this->access_token);
			$this->client  = $client;
			$this->service = new Google_Service_Drive($client);
		}
		
		/**
		 * @return bool
		 */
		public function isAccessTokenExpired() {
			return $this->client->isAccessTokenExpired();
		}
		
		/**
		 * @param string[] $paths
		 * @param null     $parent_folder_ids
		 *
		 * @return false|null
		 */
		public function createArborescence($paths, $parent_folder_ids = null) {
			if(!empty($paths)) {
				$paths[] = "CloudFichierL";
			}
			
			/*
			 * Pour chaque dossier dans l'arbo
			 */
			$last_created_file = null;
			foreach($paths as $folder_name) {
				$folders_list = [];
				$pageToken    = null;
				
				/*
				 * On recherche si le dossier existe ou non
				 */
				do {
					try {
						$parameters = [];
						if($pageToken) {
							$parameters['pageToken'] = $pageToken;
						}
						
						$parameters['q']        = "mimeType='application/vnd.google-apps.folder' and name='" . $folder_name . "' and trashed = false";
						$parameters['pageSize'] = 1000;
						
						if($parent_folder_ids !== null && isset($parent_folder_ids[0])) {
							$parameters['q'] .= " and '" . $parent_folder_ids[0] . "' in parents";
						} else {
							$parameters['q'] .= " and 'root' in parents";
						}
						
						$files = $this->service->files->listFiles($parameters);
						
						$folders_list = array_merge($folders_list, $files->getFiles());
						
						$pageToken = $files->getNextPageToken();
					} catch(\Google\Service\Exception $exception) {
						throw new Exception($exception->getMessage(), $exception->getCode());
					}
				} while($pageToken);
				
				/*
				 * Si il existe pas on le créer
				 */
				if(empty($folders_list)) {
					$file = new Google_Service_Drive_DriveFile();
					$file->setName($folder_name);
					$file->setMimeType("application/vnd.google-apps.folder");
					
					/*
					 *  Défini le dossier parent
					 */
					if($parent_folder_ids !== null) {
						$file->setParents($parent_folder_ids);
					}
					
					try {
						$createdFolder = $this->service->files->create($file, [
							'mimeType' => "application/vnd.google-apps.folder",
						]);
						
						/*
						 * Le dossier existe, on récupère son id
						 */
						$parent_folder_ids = [$createdFolder->id];
						$last_created_file = $createdFolder->id;
						
					} catch(\Google\Service\Exception $exception) {
						throw new \Exception($exception->getMessage(), $exception->getCode());
					}
					
				} else {
					/*
					 * Le dossier existe, on récupère son id
					 */
					$parent_folder_ids = [$folders_list[0]->id];
					$last_created_file = $folders_list[0]->id;
				}
				
				/*
				 * Puis on va créer le prochain dossier de l'arbo (recursive)
				 */
			}
			
			return $last_created_file;
		}
		
		/**
		 * @param $folder_name
		 * @param $parent_folder_id
		 *
		 * @return false|Google_Service_Drive_DriveFile
		 */
		public function createFolder($folder_name, $parent_folder_id) {
			$file = new Google_Service_Drive_DriveFile();
			$file->setName($folder_name);
			$file->setMimeType("application/vnd.google-apps.folder");
			
			/*
			 * Défini le dossier parent
			 */
			if($parent_folder_id != null) {
				$file->setParents([$parent_folder_id]);
			}
			
			try {
				$createdFile = $this->service->files->create($file, [
					"mimeType" => "application/vnd.google-apps.folder"
				]);
				
				return $createdFile;
			} catch(\Exception $e) {
				//var_dump($e->getMessage());
				return false;
			}
		}
		
		/**
		 * @param $item_id
		 *
		 * @return false|\GuzzleHttp\Psr7\Request
		 */
		public function deleteFile($item_id) {
			try {
				$file = $this->getFile($item_id);
				if($file !== false) {
					$parents = $file->getParents();
					if(isset($parents[0])) {
						$this->service->files->delete($parents[0]);
					}
					
					$deletedFile = $this->service->files->delete($item_id);
				}
				
				return $deletedFile ?? false;
			} catch(\Exception $e) {
				//var_dump($e->getMessage());
				return false;
			}
		}
		
		/**
		 * @param      $parent_folder_id
		 * @param      $folder_name
		 * @param bool $firstFolder
		 *
		 * @return array|false|Google_Service_Drive_DriveFile|Google_Service_Drive_DriveFile[]|mixed
		 */
		public function searchFolderByName($parent_folder_id, $folder_name, $firstFolder = true) {
			$pageToken    = null;
			$folders_list = [];
			/*
			 * On recherche si le dossier existe ou non
			 */
			do {
				try {
					$parameters = [];
					if($pageToken) {
						$parameters['pageToken'] = $pageToken;
					}
					
					$parameters['q']        = "mimeType='application/vnd.google-apps.folder' and name = '" . $folder_name . "' and trashed = false and '" . $parent_folder_id . "' in parents";
					$parameters['orderBy']  = "folder";
					$parameters['pageSize'] = 1000;
					
					$files = $this->service->files->listFiles($parameters);
					
					$folders_list = array_merge($folders_list, $files->getFiles());
					$pageToken    = $files->getNextPageToken();
				} catch(\Exception $e) {
					//var_dump($e->getMessage());
					return false;
				}
			} while($pageToken);
			
			if(!empty($folders_list)) {
				if($firstFolder) {
					return $folders_list[0];
				} else {
					return $folders_list;
				}
			} else {
				return false;
			}
		}
		
		/**
		 * @param $folder_id
		 *
		 * @return false|int
		 */
		public function countFilesInFolder($folder_id) {
			$pageToken    = null;
			$folders_list = [];
			/*
			 * On récupère les fichiers du dossier
			 */
			do {
				try {
					$parameters = [];
					if($pageToken) {
						$parameters['pageToken'] = $pageToken;
					}
					
					$parameters['q']        = "mimeType='application/vnd.google-apps.folder' and trashed = false and '" . $folder_id . "' in parents";
					$parameters['orderBy']  = "folder";
					$parameters['pageSize'] = 1000;
					
					$files = $this->service->files->listFiles($parameters);
					
					$folders_list = array_merge($folders_list, $files->getFiles());
					$pageToken    = $files->getNextPageToken();
				} catch(\Exception $e) {
					//var_dump($e->getMessage());
					return false;
				}
			} while($pageToken);
			
			if(is_array($folders_list) || is_object($folders_list)) {
				return count($folders_list);
			} else {
				return false;
			}
		}
		
		public function getFile($item_id) {
			try {
				return $this->service->files->get($item_id, ["fields" => "*"]);
			} catch(\Exception $exception) {
				return false;
			}
		}
		
		/**
		 * @return mixed
		 */
		public function getAccessToken() {
			return $this->access_token;
		}
		
		/**
		 * @param $access_token
		 */
		public function setAccessToken($access_token) {
			$this->access_token = $access_token;
		}
		
	}

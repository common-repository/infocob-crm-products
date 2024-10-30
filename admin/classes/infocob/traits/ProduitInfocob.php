<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob\Traits;
	
	use Infocob\CRM\Products\Admin\Classes\Infocob\Core\ProduitModeleFiche;
	use Infocob\CRM\Products\Admin\Classes\Infocob\DicoInfocob as D;
	use Infocob\CRM\Products\Admin\Classes\Infocob\InfocobDB;
	use Infocob\CRM\Products\Admin\Classes\Infocob\InventaireProduit;
	use Infocob\CRM\Products\Admin\Classes\Infocob\ProduitFiche;
	use Infocob\CRM\Products\Admin\Classes\ProductMeta;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	trait ProduitInfocob {
		
		protected $wp_id = 0;
		protected $wp_title = "";
		protected $wp_supp = 0;
		protected $wp_categories = [];
		protected $lang = "";
		protected $wp_table = "";
		
		public static $path_fichier = "";
		public static $url_fichier = "";
		
		protected $champs_web = [];
		
		protected $_inventaire_produit = null;
		protected static $_products = [];
		
		public function loadFromArray($res) {
			foreach($res as $key => $r) {
				if(isset($this->$key) && is_a($this->$key, "\Infocob\CRM\Products\Admin\Classes\Infocob\Champ")) {
					$this->$key->setFromLoad($r);
				} else {
					$this->champs_web[ $key ] = $r;
				}
			}
		}
		
		/**
		 * @return int
		 */
		public function getWpId(): int {
			return $this->wp_id;
		}
		
		/**
		 * @param int $wp_id
		 */
		public function setWpId(int $wp_id): void {
			$this->wp_id = $wp_id;
		}
		
		/**
		 * @return string
		 */
		public function getWpTitle(): string {
			return $this->wp_title;
		}
		
		/**
		 * @param string $wp_title
		 */
		public function setWpTitle(string $wp_title): void {
			$this->wp_title = $wp_title;
		}
		
		/**
		 * @return int
		 */
		public function getWpSupp(): int {
			return $this->wp_supp;
		}
		
		/**
		 * @param int $wp_supp
		 */
		public function setWpSupp(int $wp_supp): void {
			$this->wp_supp = $wp_supp;
		}
		
		public function getWpCategories() {
			return $this->wp_categories;
		}
		
		public function setWpCategories($cat) {
			$this->wp_categories = $cat;
		}
		
		
		public function addWpCategories($cat) {
			$this->wp_categories = array_merge_recursive($this->wp_categories, $cat);
		}
		
		/**
		 * @return string
		 */
		public function getWpTable(): string {
			return $this->wp_table;
		}
		
		/**
		 * @param string $wp_table
		 */
		public function setWpTable(string $wp_table): void {
			$this->wp_table = $wp_table;
		}
		
		public function getLang() {
			return $this->lang;
		}
		
		public function setLang($lang) {
			$this->lang = $lang;
		}
		
		public function set($field_name, $value) {
			if(isset($this->$field_name)) {
				return $this->$field_name->set($value);
			} else {
				$this->champs_web[ $field_name ] = $value;
			}
			
			return $this;
		}
		
		public function getFichiers() {
			if(!$this->getID()) {
				return [];
			}
			
			if($this->fichiers === null) {
				$basePath = static::$path_fichier . $this->getID() . "/";
				$baseUri  = static::$url_fichier . $this->getID() . "/";
				
				$this->fichiers = $this->exploreDirectory($basePath, $baseUri, "");
			}
			
			return $this->fichiers;
		}
		
		public function getInventaire() {
			if($this->_inventaire_produit === null) {
				return $this->loadInventaires();
			}
			
			return $this->_inventaire_produit;
		}
		
		public function loadInventaires() {
			if($this->_inventaire_produit !== null) {
				return $this->_inventaire_produit;
			}
			
			$sql = "SELECT IN0.ip_commentaire, TIP.tip_des_fr, fti.fti_des_fr, fti_code "
			       . " FROM INVENTAIREPRODUIT IN0 "
			       . " JOIN produitfiche P0 ON P0.p_code = IN0.IP_CODEPRODUIT "
			       . " LEFT JOIN TYPEINVENTAIREPRODUIT TIP ON TIP_CODE = IN0.ip_code_type "
			       . " LEFT JOIN FAMILLETYPEINVENTAIRE FTI ON TIP_CODEFAMILLE = FTI.fti_code "
			       . " WHERE " . ProduitFiche::GetDroitCondition() . " "
			       . " AND " . InventaireProduit::GetDroitCondition()
			       . " AND (IN0.IP_TYPEPARENT = 9 OR IN0.IP_TYPEPARENT = 0)
				 AND P0.P_CODE = :p_code
				 ORDER BY fti_des_fr, tip_des_fr, ip_commentaire";
			
			$args = [
				":p_code" => $this->get(D::p_code),
			];
			
			$res = InfocobDB::getInstance()->fetchAll($sql, $args);
			
			$inventaires = [];
			foreach($res as $r) {
				if(empty($inventaires[ $r["FTI_CODE"] ])) {
					$inventaires[ $r["FTI_CODE"] ] = [
						"libelle"     => $r["FTI_DES_FR"],
						"inventaires" => [],
					];
				}
				$inventaires[ $r["FTI_CODE"] ]["inventaires"][] = [
					"type"       => $r["TIP_DES_FR"],
					"inventaire" => $r["IP_COMMENTAIRE"],
				];
			}
			
			$this->_inventaire_produit = $inventaires;
			
			return $this->_inventaire_produit;
		}
		
		public function get($field_name, $nl2br = false) {
			if(isset($this->$field_name)) {
				return $this->$field_name->get();
			} elseif(isset($this->champs_web[ $field_name ])) {
				return $this->champs_web[ $field_name ];
			}
			
			return "";
		}
		
		public static function loadFromPcode( $p_code ) {
			if ( isset( static::$_products[ $p_code ] ) ) {
				return static::$_products[ $p_code ];
			}
			
			if(static::class === "Infocob\CRM\Products\Admin\Classes\Infocob\ProduitFiche") {
				static::$_products[ $p_code ] = new ProduitFiche();
				static::$_products[ $p_code ]->load($p_code);
			} else if(static::class === "Infocob\CRM\Products\Admin\Classes\Infocob\ProduitModeleFiche") {
				static::$_products[ $p_code ] = new ProduitModeleFiche();
				static::$_products[ $p_code ]->load($p_code);
			}
			
			if (empty(static::$_products[ $p_code ]->getID())) {
				static::$_products[ $p_code ] = false;
			}
			
			return static::$_products[ $p_code ];
		}
	}

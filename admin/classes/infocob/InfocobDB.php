<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob;
	
	use Infocob\CRM\Products\Admin\Classes\Infocob\Debug\DebugTools;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Debug\DebugWindow;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Tools\Encoding;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Tools\StringTools;
	use PDO;
	use PDOException;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class InfocobDB {
		
		public static $host = "";
		public static $base = "";
		public static $user = "";
		public static $pswd = "";
		protected static $_instance;
		protected $conn;
		
		private function __construct() {
			$base = 'firebird:dbname=' . self::$host . ':' . self::$base;
			
			if(class_exists("\Infocob\CRM\Products\Admin\Classes\Infocob\Debug\DebugTools") && DebugTools::IsDebugMode()) {
				try {
					$this->conn = new PDO($base, self::$user, self::$pswd);
				} catch(PDOException $Exception) {
					DebugTools::SendError($Exception, true);
				}
			} else {
				$this->conn = new PDO($base, self::$user, self::$pswd);
			}
			
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			if(class_exists("\Infocob\CRM\Products\Admin\Classes\Infocob\Debug\DebugTools") && DebugTools::IsDebugMode()) {
				$this->conn->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
			}
			
		}
		
		/**
		 * Singleton
		 * @return InfocobDB DB instance
		 */
		public static function getInstance() {
			if(!isset(self::$_instance)) {
				self::$_instance = new InfocobDB();
			}
			
			return self::$_instance;
		}
		
		public static function testConnection() {
			$base = 'firebird:dbname=' . self::$host . ':' . self::$base;
			
			try {
				new PDO($base, self::$user, self::$pswd);
			} catch(PDOException  $Exception) {
				DebugTools::SendError($Exception);
				
				return false;
			}
			
			return true;
		}
		
		public function prepare($sql) {
			return $this->conn->prepare($sql);
		}
		
		public function fetch($sql, $bindValues = array()) {
			$req = $this->execute($sql, $bindValues);
			$res = $req->fetch(PDO::FETCH_ASSOC);
			
			return Encoding::UTF8($res);
		}
		
		public function execute($sql, $bindValues = array()) {
			$sqliso = Encoding::ISO($sql);
			$req    = $this->conn->prepare($sql);
			
			$references = array();
			$i          = 0;
			foreach($bindValues as $parameter => $value) {
				if(gettype($value) === "object") {
					$references[ $i ] = Encoding::ISO($value->get());
					$req->bindParam($parameter, $references[ $i ], $value->getTypePDO());
				} else {
					$references[ $i ] = Encoding::ISO($value);
					$req->bindParam($parameter, $references[ $i ], PDO::PARAM_STR);
				}
				$i ++;
			}
			
			if(class_exists("\Infocob\CRM\Products\Admin\Classes\Infocob\Debug\DebugTools") && DebugTools::IsDebugMode() && $req == false) {
				DebugTools::Dump($sql, "red");
				DebugTools::Dump($this->conn->errorInfo(), "red");
			}
			
			$req->execute();
			
			return $req;
		}
		
		public function fetchAll($sql, $bindValues = array()) {
			$req = $this->execute($sql, $bindValues);
			$res = $req->fetchAll(PDO::FETCH_ASSOC);
			
			return Encoding::UTF8($res);
		}
		
		public function errorCode() {
			return $this->conn->errorCode();
		}
		
		public function errorInfo() {
			return $this->conn->errorInfo();
		}
		
		public function lastID($name = null) {
			return $this->conn->lastInsertId($name = null);
		}
		
		public function dump($sql, $args) {
			if(class_exists("\Infocob\CRM\Products\Admin\Classes\Infocob\Debug\DebugTools") && DebugTools::IsDebugMode()) {
				foreach($args as $n => $v) {
					if(gettype($v) === "object") {
						$sql = preg_replace("#" . $n . "#", "'" . StringTools::CleanInjectionFirebird($v->get()) . "'", $sql);
					} else {
						$sql = preg_replace("#" . $n . "#", "'" . StringTools::CleanInjectionFirebird($v) . "'", $sql);
					}
				}
				DebugTools::TableDump($sql);
			}
		}
		
		public function dump_popup($sql, $args) {
			if(class_exists("\Infocob\CRM\Products\Admin\Classes\Infocob\Debug\DebugTools") && DebugTools::IsDebugMode()) {
				foreach($args as $n => $v) {
					if(gettype($v) === "object") {
						$sql = preg_replace("#" . $n . "#", "'" . StringTools::CleanInjectionFirebird($v->get()) . "'", $sql);
					} else {
						$sql = preg_replace("#" . $n . "#", "'" . StringTools::CleanInjectionFirebird($v) . "'", $sql);
					}
				}
				DebugWindow::AddDumpMgs($sql);
			}
		}
		
		public function beginTransaction() {
			if($this->conn->inTransaction()) {
				$this->conn->rollBack();
			}
			
			return $this->conn->beginTransaction();
		}
		
		public function commit() {
			$retour = $this->conn->commit();
			
			//sleep(1);
			return $retour;
		}
		
		public function quote($var) {
			return $this->conn->quote($var);
		}
	}

<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob\Core;
	
	use Infocob\CRM\Products\Admin\Classes\Infocob\DicoInfocob as D;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Entities\ProduitFicheEntity;
	use Infocob\CRM\Products\Admin\Classes\Infocob\InfocobDB;
	use Infocob\CRM\Products\Admin\Classes\Infocob\ProduitFiche;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class ProduitFicheCore extends ProduitFicheEntity {
		
		public $infocob_type_produit = "";
		public $sous_infocob_type_produit = "";
		public $sous_sous_infocob_type_produit = "";
		
		public $inventaires = [];
		
		public static function getInfolibre14Produit() {
			$sql = "SELECT LTP.LPI14_CODE, LTP.LPI14_NOM "
			       . " FROM L_PRODUITINFOLIBRE14 LTP "
			       . " ORDER BY LPI14_NOM ";
			
			$res = InfocobDB::getInstance()->fetchAll($sql);
			
			$infolibre14 = [];
			foreach($res as $r) {
				$infolibre14[ $r["LPI14_CODE"] ] = $r["LPI14_NOM"];
			}
			
			return $infolibre14;
		}
		
		public static function getInfolibre16Produit() {
			$sql = "SELECT LTP.LPI16_CODE, LTP.LPI16_NOM "
			       . " FROM L_PRODUITINFOLIBRE16 LTP "
			       . " ORDER BY LPI16_NOM ";
			
			$res = InfocobDB::getInstance()->fetchAll($sql);
			
			$infolibre16 = [];
			foreach($res as $r) {
				$infolibre16[ $r["LPI16_CODE"] ] = $r["LPI16_NOM"];
			}
			
			return $infolibre16;
		}
		
		public static function getInfolibre5Produit() {
			$sql = "SELECT LTP.I5_INFOLIBRE5"
			       . " FROM T_INFOLIBRE5 LTP "
			       . " ORDER BY I5_INFOLIBRE5 ";
			
			$res = InfocobDB::getInstance()->fetchAll($sql);
			
			$infolibre5 = [];
			foreach($res as $r) {
				$infolibre5[ $r["I5_INFOLIBRE5"] ] = $r["I5_INFOLIBRE5"];
			}
			
			return $infolibre5;
		}
		
		public static function getInfolibre6Produit() {
			$sql = "SELECT LTP.I6_INFOLIBRE6"
			       . " FROM T_INFOLIBRE6 LTP "
			       . " ORDER BY I6_INFOLIBRE6 ";
			
			$res = InfocobDB::getInstance()->fetchAll($sql);
			
			$infolibre6 = [];
			foreach($res as $r) {
				$infolibre6[ $r["I6_INFOLIBRE6"] ] = $r["I6_INFOLIBRE6"];
			}
			
			return $infolibre6;
		}
		
		public static function getInfolibre7Produit() {
			$sql = "SELECT LTP.I7_INFOLIBRE7"
			       . " FROM T_INFOLIBRE7 LTP "
			       . " ORDER BY I7_INFOLIBRE7 ";
			
			$res = InfocobDB::getInstance()->fetchAll($sql);
			
			$infolibre7 = [];
			foreach($res as $r) {
				$infolibre7[ $r["I7_INFOLIBRE7"] ] = $r["I7_INFOLIBRE7"];
			}
			
			return $infolibre7;
		}
		
		public static function getInfolibre8Produit() {
			$sql = "SELECT LTP.I8_INFOLIBRE8"
			       . " FROM T_INFOLIBRE8 LTP "
			       . " ORDER BY I8_INFOLIBRE8 ";
			
			$res = InfocobDB::getInstance()->fetchAll($sql);
			
			$infolibre8 = [];
			foreach($res as $r) {
				$infolibre8[ $r["I8_INFOLIBRE8"] ] = $r["I8_INFOLIBRE8"];
			}
			
			return $infolibre8;
		}
		
		public static function getContratProduit() {
			$sql = "SELECT P.CONTRAT"
			       . " FROM PRODUITCONTRAT P "
			       . " ORDER BY CONTRAT ";
			
			$res = InfocobDB::getInstance()->fetchAll($sql);
			
			$contrats = [];
			foreach($res as $r) {
				if(!empty($r["CONTRAT"])) {
					$contrats[ $r["CONTRAT"] ] = $r["CONTRAT"];
				}
			}
			
			return $contrats;
		}
		
		public static function getTypesProduit() {
			$sql = "SELECT LTP.LTP_CODE, LTP.LTP_NOM "
			       . " FROM L_PRODUITTYPE LTP "
			       . " LEFT JOIN TJ_L_PRODUITTYPE TJT ON TJT.LTP_CODE = LTP.LTP_CODE "
			       . " WHERE TJT.LTP_CODE is null "
			       . " ORDER BY LTP_NOM ";
			
			$res = InfocobDB::getInstance()->fetchAll($sql);
			
			$typesActions = [];
			foreach($res as $r) {
				$typesActions[ $r["LTP_CODE"] ] = $r["LTP_NOM"];
			}
			
			return $typesActions;
		}
		
		public static function getSousTypesProduit($lat_code) {
			$sql = "SELECT LTP.LTP_CODE, LTP.LTP_NOM "
			       . " FROM L_PRODUITTYPE LTP "
			       . " LEFT JOIN TJ_L_PRODUITTYPE TJT ON TJT.LTP_CODE = LTP.LTP_CODE "
			       . " WHERE TJT.LTP_CODELIEA = :lta_code "
			       . " ORDER BY LTP_NOM ";
			
			$res = InfocobDB::getInstance()->fetchAll($sql, [
				":lta_code" => $lat_code,
			]);
			
			$typesActions = [];
			foreach($res as $r) {
				$typesActions[ $r["LTP_CODE"] ] = $r["LTP_NOM"];
			}
			
			return $typesActions;
		}
		
		public static function getConstructeurs() {
			$sql = "SELECT CONSTRUCTEUR
                FROM CONSTRUCTEUR
                ORDER BY CONSTRUCTEUR ";
			
			$res = InfocobDB::getInstance()->fetchAll($sql);
			
			$constructeurs = [];
			foreach($res as $r) {
				$constructeurs[ $r["CONSTRUCTEUR"] ] = $r["CONSTRUCTEUR"];
			}
			
			return $constructeurs;
		}
		
		public static function extractDataJSON($produit) {
			if(is_array($produit)) {
				$p       = $produit;
				$produit = new static();
				$produit->loadFromArray($p);
			}
			
			$datas = [
				"p_code"    => trim($produit->get(D::p_code)),
				"p_adresse" => trim($produit->get(D::p_adresse)),
				"p_nom"     => trim($produit->get(D::p_nom)),
				"p_cp"      => trim($produit->get(D::p_cp)),
				"p_ville"   => trim($produit->get(D::p_ville)),
			];
			
			return $datas;
		}
		
		public function loadFromArray($res) {
			parent::loadFromArray($res);
			
			if(!empty($res["LTP_NOM"])) {
				$this->infocob_type_produit = $res["LTP_NOM"];
			}
			
			if(!empty($res["LTP_NOM2"])) {
				$this->sous_infocob_type_produit = $res["LTP_NOM2"];
			}
			
			if(!empty($res["LTP_NOM3"])) {
				$this->sous_sous_infocob_type_produit = $res["LTP_NOM3"];
			}
		}
		
		public function load($id = null, $droitConditions = true) {
			$sql = "SELECT FIRST 1 PR0.*, "
			       . " LTP1.LTP_NOM LTP_NOM, "
			       . " LTP2.LTP_NOM LTP_NOM2, "
			       . " LTP3.LTP_NOM LTP_NOM3 "
			       . " FROM " . static::$tableName . " PR0 "
			       . " LEFT JOIN L_PRODUITTYPE LTP1 ON LTP1.LTP_CODE = " . D::p_typeproduit
			       . " LEFT JOIN L_PRODUITTYPE LTP2 ON LTP2.LTP_CODE = " . D::p_soustypeproduit
			       . " LEFT JOIN L_PRODUITTYPE LTP3 ON LTP3.LTP_CODE = " . D::p_soussoustypeproduit
			       . " WHERE " . D::p_code . " = :p_code "
			       . " AND " . static::GetDroitCondition();
			
			
			$args = [
				":p_code" => $id,
			];
			
			$res = InfocobDB::getInstance()->fetch($sql, $args);
			if(!empty($res)) {
				$this->loadFromArrayComplete($res);
			}
		}
		
		public function loadFromArrayComplete($res) {
			$this->loadFromArray($res);
		}
	}

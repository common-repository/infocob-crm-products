<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob\Entities;
	
	use Infocob\CRM\Products\Admin\Classes\Infocob\Champ;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Entity;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class FamilleTypeInventaireProduitEntity extends Entity {
		
		public Champ $FTI_CODE;
		public Champ $FTI_COMPTEUR;
		public Champ $FTI_CODESOCIETE;
		public Champ $FTI_DES_FR;
		public Champ $FTI_DES_ALL;
		public Champ $FTI_DES_ES;
		public Champ $FTI_DES_IT;
		public Champ $FTI_DES_US;
		public Champ $FTI_DATEMODIF;
		public Champ $FTI_DATECREATION;
		public Champ $FTI_EXPORT;
		public Champ $FTI_CODEFAMILLEIMPORT;
		public Champ $FTI_NBRLIBRE1;
		public Champ $FTI_NBRLIBRE2;
		public Champ $FTI_NBRLIBRE3;
		public Champ $FTI_BOOLLIBRE1;
		public Champ $FTI_BOOLLIBRE2;
		public Champ $FTI_INFOLIBRE1;
		public Champ $FTI_INFOLIBRE2;
		public Champ $FTI_NBRLIBRE4;
		public Champ $FTI_NBRLIBRE5;
		public Champ $FTI_BOOLLIBRE3;
		public Champ $FTI_BOOLLIBRE4;
		public Champ $FTI_BOOLLIBRE5;
		public Champ $FTI_DATELIBRE1;
		public Champ $FTI_DATELIBRE2;
		public Champ $FTI_DATELIBRE3;
		public Champ $FTI_DATELIBRE4;
		public Champ $FTI_DATELIBRE5;
		public Champ $FTI_COM_FR;
		public Champ $FTI_COM_ALL;
		public Champ $FTI_COM_ES;
		public Champ $FTI_COM_IT;
		public Champ $FTI_COM_US;
		public Champ $FTI_CODEVENDEUR;
		public Champ $FTI_DROIT;
		public Champ $FTI_LATITUDE;
		public Champ $FTI_LONGITUDE;
		public Champ $FTI_LIEA;
		public Champ $FTI_LIESOCIETE;
		
		public static $champsDefinitions = array(
			"FTI_CODE"              => array(
				"ID"     => true,
				"type"   => "lob",
				"length" => 20,
			),
			"FTI_COMPTEUR"          => array(
				"type"   => "lob",
				"length" => false,
			),
			"FTI_CODESOCIETE"       => array(
				"type"   => "lob",
				"length" => 5,
			),
			"FTI_DES_FR"            => array(
				"type"   => "lob",
				"length" => 50,
			),
			"FTI_DES_ALL"           => array(
				"type"   => "lob",
				"length" => 50,
			),
			"FTI_DES_ES"            => array(
				"type"   => "lob",
				"length" => 50,
			),
			"FTI_DES_IT"            => array(
				"type"   => "lob",
				"length" => 50,
			),
			"FTI_DES_US"            => array(
				"type"   => "lob",
				"length" => 50,
			),
			"FTI_DATEMODIF"         => array(
				"type"   => "lob",
				"length" => false,
			),
			"FTI_DATECREATION"      => array(
				"type"   => "lob",
				"length" => false,
			),
			"FTI_EXPORT"            => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"FTI_CODEFAMILLEIMPORT" => array(
				"type"   => "string",
				"length" => 20,
			),
			"FTI_NBRLIBRE1"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"FTI_NBRLIBRE2"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"FTI_NBRLIBRE3"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"FTI_BOOLLIBRE1"        => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"FTI_BOOLLIBRE2"        => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"FTI_INFOLIBRE1"        => array(
				"type"   => "lob",
				"length" => false,
			),
			"FTI_INFOLIBRE2"        => array(
				"type"   => "lob",
				"length" => false,
			),
			"FTI_NBRLIBRE4"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"FTI_NBRLIBRE5"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"FTI_BOOLLIBRE3"        => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"FTI_BOOLLIBRE4"        => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"FTI_BOOLLIBRE5"        => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"FTI_DATELIBRE1"        => array(
				"type"   => "datetime",
				"length" => false,
			),
			"FTI_DATELIBRE2"        => array(
				"type"   => "datetime",
				"length" => false,
			),
			"FTI_DATELIBRE3"        => array(
				"type"   => "datetime",
				"length" => false,
			),
			"FTI_DATELIBRE4"        => array(
				"type"   => "datetime",
				"length" => false,
			),
			"FTI_DATELIBRE5"        => array(
				"type"   => "datetime",
				"length" => false,
			),
			"FTI_COM_FR"            => array(
				"type"   => "lob",
				"length" => false,
			),
			"FTI_COM_ALL"           => array(
				"type"   => "lob",
				"length" => false,
			),
			"FTI_COM_ES"            => array(
				"type"   => "lob",
				"length" => false,
			),
			"FTI_COM_IT"            => array(
				"type"   => "lob",
				"length" => false,
			),
			"FTI_COM_US"            => array(
				"type"   => "lob",
				"length" => false,
			),
			"FTI_CODEVENDEUR"       => array(
				"type"   => "string",
				"length" => 20,
			),
			"FTI_DROIT"             => array(
				"type"   => "lob",
				"length" => false,
			),
			"FTI_LATITUDE"          => array(
				"type"   => "lob",
				"length" => false,
			),
			"FTI_LONGITUDE"         => array(
				"type"   => "lob",
				"length" => false,
			),
			"FTI_LIEA"              => array(
				"type"   => "string",
				"length" => 20,
			),
			"FTI_LIESOCIETE"        => array(
				"type"   => "string",
				"length" => 20,
			)
		);
		
		public static $tableName = "FAMILLETYPEINVENTAIRE";
		public static $tableCode = 10;
		public static $champPrincipalKey = "FTI_CODE";
		public static $champDroitKey = "FTI_DROIT";
	}

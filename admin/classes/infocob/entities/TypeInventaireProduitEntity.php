<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob\Entities;
	
	use Infocob\CRM\Products\Admin\Classes\Infocob\Champ;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Entity;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class TypeInventaireProduitEntity extends Entity {
		public Champ $TIP_CODE;
		public Champ $TIP_COMPTEUR;
		public Champ $TIP_CODESOCIETE;
		public Champ $TIP_DES_FR;
		public Champ $TIP_DES_ALL;
		public Champ $TIP_DES_ES;
		public Champ $TIP_DES_IT;
		public Champ $TIP_DES_US;
		public Champ $TIP_CODEFAMILLE;
		public Champ $TIP_DATEMODIF;
		public Champ $TIP_DATECREATION;
		public Champ $TIP_PRIX;
		public Champ $TIP_QUANTITE;
		public Champ $TIP_MONNAIE;
		public Champ $TIP_EXPORT;
		public Champ $TIP_CODETYPEIMPORT;
		public Champ $TIP_NOMFAMILLE;
		public Champ $TIP_CODEFAMILLEIMPORT;
		public Champ $TIP_NBRLIBRE1;
		public Champ $TIP_NBRLIBRE2;
		public Champ $TIP_NBRLIBRE3;
		public Champ $TIP_BOOLLIBRE1;
		public Champ $TIP_BOOLLIBRE2;
		public Champ $TIP_INFOLIBRE1;
		public Champ $TIP_INFOLIBRE2;
		public Champ $TIP_COULEUR;
		public Champ $TIP_INDEX_ICONE;
		public Champ $TIP_COM_FR;
		public Champ $TIP_COM_ALL;
		public Champ $TIP_COM_ES;
		public Champ $TIP_COM_IT;
		public Champ $TIP_COM_US;
		public Champ $TIP_NBRLIBRE4;
		public Champ $TIP_NBRLIBRE5;
		public Champ $TIP_NBRLIBRE6;
		public Champ $TIP_NBRLIBRE7;
		public Champ $TIP_NBRLIBRE8;
		public Champ $TIP_NBRLIBRE9;
		public Champ $TIP_NBRLIBRE10;
		public Champ $TIP_NBRLIBRE11;
		public Champ $TIP_NBRLIBRE12;
		public Champ $TIP_NBRLIBRE13;
		public Champ $TIP_NBRLIBRE14;
		public Champ $TIP_NBRLIBRE15;
		public Champ $TIP_NBRLIBRE16;
		public Champ $TIP_NBRLIBRE17;
		public Champ $TIP_NBRLIBRE18;
		public Champ $TIP_NBRLIBRE19;
		public Champ $TIP_NBRLIBRE20;
		public Champ $TIP_BOOLLIBRE3;
		public Champ $TIP_BOOLLIBRE4;
		public Champ $TIP_BOOLLIBRE5;
		public Champ $TIP_BOOLLIBRE6;
		public Champ $TIP_BOOLLIBRE7;
		public Champ $TIP_BOOLLIBRE8;
		public Champ $TIP_BOOLLIBRE9;
		public Champ $TIP_BOOLLIBRE10;
		public Champ $TIP_DATELIBRE1;
		public Champ $TIP_DATELIBRE2;
		public Champ $TIP_DATELIBRE3;
		public Champ $TIP_DATELIBRE4;
		public Champ $TIP_DATELIBRE5;
		public Champ $TIP_DATELIBRE6;
		public Champ $TIP_DATELIBRE7;
		public Champ $TIP_DATELIBRE8;
		public Champ $TIP_DATELIBRE9;
		public Champ $TIP_DATELIBRE10;
		public Champ $TIP_INFOLIBRE3;
		public Champ $TIP_INFOLIBRE4;
		public Champ $TIP_INFOLIBRE5;
		public Champ $TIP_INFOLIBRE6;
		public Champ $TIP_INFOLIBRE7;
		public Champ $TIP_INFOLIBRE8;
		public Champ $TIP_INFOLIBRE9;
		public Champ $TIP_INFOLIBRE10;
		public Champ $TIP_CODEPRODUIT;
		public Champ $TIP_CODEAFFAIRE;
		public Champ $TIP_CODECONTACT;
		public Champ $TIP_CODEINTERLOCUTEUR;
		public Champ $TIP_CODEACTION;
		public Champ $TIP_CODETYPEINVENTAIRE;
		public Champ $TIP_CODEVENDEUR;
		public Champ $TIP_DROIT;
		public Champ $TIP_LATITUDE;
		public Champ $TIP_LONGITUDE;
		public Champ $TIP_LIEA;
		public Champ $TIP_LIESOCIETE;
		
		public static $champsDefinitions = array(
			"TIP_CODE"               => array(
				"ID"     => true,
				"type"   => "string",
				"length" => 20,
			),
			"TIP_COMPTEUR"           => array(
				"length" => false,
			),
			"TIP_CODESOCIETE"        => array(
				"type"   => "string",
				"length" => 5,
			),
			"TIP_DES_FR"             => array(
				"type"   => "string",
				"length" => 50,
			),
			"TIP_DES_ALL"            => array(
				"type"   => "string",
				"length" => 50,
			),
			"TIP_DES_ES"             => array(
				"type"   => "string",
				"length" => 50,
			),
			"TIP_DES_IT"             => array(
				"type"   => "string",
				"length" => 50,
			),
			"TIP_DES_US"             => array(
				"type"   => "string",
				"length" => 50,
			),
			"TIP_CODEFAMILLE"        => array(
				"type"   => "string",
				"length" => 20,
			),
			"TIP_DATEMODIF"          => array(
				"type"   => "datetime",
				"length" => false,
			),
			"TIP_DATECREATION"       => array(
				"type"   => "datetime",
				"length" => false,
			),
			"TIP_PRIX"               => array(
				"type"   => "decimal",
				"length" => false,
			),
			"TIP_QUANTITE"           => array(
				"type"   => "decimal",
				"length" => false,
			),
			"TIP_MONNAIE"            => array(
				"type"   => "string",
				"length" => 20,
			),
			"TIP_EXPORT"             => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"TIP_CODETYPEIMPORT"     => array(
				"type"   => "string",
				"length" => 20,
			),
			"TIP_NOMFAMILLE"         => array(
				"type"   => "string",
				"length" => 20,
			),
			"TIP_CODEFAMILLEIMPORT"  => array(
				"type"   => "string",
				"length" => 20,
			),
			"TIP_NBRLIBRE1"          => array(
				"type"   => "decimal",
				"length" => false,
			),
			"TIP_NBRLIBRE2"          => array(
				"type"   => "decimal",
				"length" => false,
			),
			"TIP_NBRLIBRE3"          => array(
				"type"   => "decimal",
				"length" => false,
			),
			"TIP_BOOLLIBRE1"         => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"TIP_BOOLLIBRE2"         => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"TIP_INFOLIBRE1"         => array(
				"type"   => "string",
				"length" => false,
			),
			"TIP_INFOLIBRE2"         => array(
				"type"   => "string",
				"length" => false,
			),
			"TIP_COULEUR"            => array(
				"type"   => "int",
				"length" => false,
			),
			"TIP_INDEX_ICONE"        => array(
				"type"   => "int",
				"length" => false,
			),
			"TIP_COM_FR"             => array(
				"type"   => "lob",
				"length" => false,
			),
			"TIP_COM_ALL"            => array(
				"type"   => "lob",
				"length" => false,
			),
			"TIP_COM_ES"             => array(
				"type"   => "lob",
				"length" => false,
			),
			"TIP_COM_IT"             => array(
				"type"   => "lob",
				"length" => false,
			),
			"TIP_COM_US"             => array(
				"type"   => "lob",
				"length" => false,
			),
			"TIP_NBRLIBRE4"          => array(
				"type"   => "decimal",
				"length" => false,
			),
			"TIP_NBRLIBRE5"          => array(
				"type"   => "decimal",
				"length" => false,
			),
			"TIP_NBRLIBRE6"          => array(
				"type"   => "decimal",
				"length" => false,
			),
			"TIP_NBRLIBRE7"          => array(
				"type"   => "decimal",
				"length" => false,
			),
			"TIP_NBRLIBRE8"          => array(
				"type"   => "decimal",
				"length" => false,
			),
			"TIP_NBRLIBRE9"          => array(
				"type"   => "decimal",
				"length" => false,
			),
			"TIP_NBRLIBRE10"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"TIP_NBRLIBRE11"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"TIP_NBRLIBRE12"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"TIP_NBRLIBRE13"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"TIP_NBRLIBRE14"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"TIP_NBRLIBRE15"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"TIP_NBRLIBRE16"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"TIP_NBRLIBRE17"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"TIP_NBRLIBRE18"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"TIP_NBRLIBRE19"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"TIP_NBRLIBRE20"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"TIP_BOOLLIBRE3"         => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"TIP_BOOLLIBRE4"         => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"TIP_BOOLLIBRE5"         => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"TIP_BOOLLIBRE6"         => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"TIP_BOOLLIBRE7"         => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"TIP_BOOLLIBRE8"         => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"TIP_BOOLLIBRE9"         => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"TIP_BOOLLIBRE10"        => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"TIP_DATELIBRE1"         => array(
				"type"   => "datetime",
				"length" => false,
			),
			"TIP_DATELIBRE2"         => array(
				"type"   => "datetime",
				"length" => false,
			),
			"TIP_DATELIBRE3"         => array(
				"type"   => "datetime",
				"length" => false,
			),
			"TIP_DATELIBRE4"         => array(
				"type"   => "datetime",
				"length" => false,
			),
			"TIP_DATELIBRE5"         => array(
				"type"   => "datetime",
				"length" => false,
			),
			"TIP_DATELIBRE6"         => array(
				"type"   => "datetime",
				"length" => false,
			),
			"TIP_DATELIBRE7"         => array(
				"type"   => "datetime",
				"length" => false,
			),
			"TIP_DATELIBRE8"         => array(
				"type"   => "datetime",
				"length" => false,
			),
			"TIP_DATELIBRE9"         => array(
				"type"   => "datetime",
				"length" => false,
			),
			"TIP_DATELIBRE10"        => array(
				"type"   => "datetime",
				"length" => false,
			),
			"TIP_INFOLIBRE3"         => array(
				"type"   => "string",
				"length" => 50,
			),
			"TIP_INFOLIBRE4"         => array(
				"type"   => "string",
				"length" => 50,
			),
			"TIP_INFOLIBRE5"         => array(
				"type"   => "string",
				"length" => 50,
			),
			"TIP_INFOLIBRE6"         => array(
				"type"   => "string",
				"length" => 50,
			),
			"TIP_INFOLIBRE7"         => array(
				"type"   => "string",
				"length" => 50,
			),
			"TIP_INFOLIBRE8"         => array(
				"type"   => "string",
				"length" => 50,
			),
			"TIP_INFOLIBRE9"         => array(
				"type"   => "string",
				"length" => 50,
			),
			"TIP_INFOLIBRE10"        => array(
				"type"   => "string",
				"length" => 50,
			),
			"TIP_CODEPRODUIT"        => array(
				"type"   => "string",
				"length" => 20,
			),
			"TIP_CODEAFFAIRE"        => array(
				"type"   => "string",
				"length" => 20,
			),
			"TIP_CODECONTACT"        => array(
				"type"   => "string",
				"length" => 20,
			),
			"TIP_CODEINTERLOCUTEUR"  => array(
				"type"   => "string",
				"length" => 20,
			),
			"TIP_CODEACTION"         => array(
				"type"   => "string",
				"length" => 20,
			),
			"TIP_CODETYPEINVENTAIRE" => array(
				"type"   => "string",
				"length" => 20,
			),
			"TIP_CODEVENDEUR"        => array(
				"type"   => "string",
				"length" => 20,
			),
			"TIP_DROIT"              => array(
				"type"   => "string",
				"length" => false,
			),
			"TIP_LATITUDE"           => array(
				"type"   => "decimal",
				"length" => false,
			),
			"TIP_LONGITUDE"          => array(
				"type"   => "decimal",
				"length" => false,
			),
			"TIP_LIEA"               => array(
				"type"   => "string",
				"length" => 20,
			),
			"TIP_LIESOCIETE"         => array(
				"type"   => "string",
				"length" => 20,
			)
		);
		
		public static $tableName = "TYPEINVENTAIREPRODUIT";
		public static $tableCode = 12;
		public static $champPrincipalKey = "TIP_CODE";
		public static $champDroitKey = "TIP_DROIT";
	}

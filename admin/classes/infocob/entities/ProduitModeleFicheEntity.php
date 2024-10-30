<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob\Entities;
	
	use Infocob\CRM\Products\Admin\Classes\Infocob\Champ;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Entity;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class ProduitModeleFicheEntity extends Entity {
		protected Champ $P_CODE;
		protected Champ $P_COMPTEUR;
		protected Champ $P_CODESOCIETE;
		protected Champ $P_CODEMODELE;
		protected Champ $P_FAMILLE;
		protected Champ $P_CONSTRUCTEUR;
		protected Champ $P_NOM;
		protected Champ $P_TAILLE1_MIN;
		protected Champ $P_TAILLE1_MAX;
		protected Champ $P_TAILLE2_MIN;
		protected Champ $P_TAILLE2_MAX;
		protected Champ $P_TAILLE3_MIN;
		protected Champ $P_TAILLE3_MAX;
		protected Champ $P_NSERIE;
		protected Champ $P_NOMBAPTEME;
		protected Champ $P_DATECONSTRUCTION;
		protected Champ $P_1UTILISATION;
		protected Champ $P_DES_FR;
		protected Champ $P_DES_UK;
		protected Champ $P_DES_ALL;
		protected Champ $P_DES_ES;
		protected Champ $P_DES_IT;
		protected Champ $P_DES_INTERNE;
		protected Champ $P_PAHT;
		protected Champ $P_SESSION;
		protected Champ $P_PVHT;
		protected Champ $P_MARGE;
		protected Champ $P_TVA;
		protected Champ $P_MONNAIE;
		protected Champ $P_TTC;
		protected Champ $P_CONTRAT;
		protected Champ $P_CODECONTACT;
		protected Champ $P_DATE_ENTREE;
		protected Champ $P_DATE_SORTIE;
		protected Champ $P_LOCALISATION1;
		protected Champ $P_LOCALISATION2;
		protected Champ $P_LOCALISATIONVILLE;
		protected Champ $P_LOCALISATIONSECTEUR;
		protected Champ $P_ETAT;
		protected Champ $P_INFOLIBRE1;
		protected Champ $P_INFOLIBRE2;
		protected Champ $P_INFOLIBRE3;
		protected Champ $P_INFOLIBRE4;
		protected Champ $P_INFOLIBRE5;
		protected Champ $P_INFOLIBRE6;
		protected Champ $P_INFOLIBRE7;
		protected Champ $P_INFOLIBRE8;
		protected Champ $P_INFOLIBRE9;
		protected Champ $P_INFOLIBRE10;
		protected Champ $P_INFOLIBRE11;
		protected Champ $P_INFOLIBRE12;
		protected Champ $P_INFOLIBRE13;
		protected Champ $P_NBRLIBRE1;
		protected Champ $P_NBRLIBRE2;
		protected Champ $P_BOOLLIBRE1;
		protected Champ $P_BOOLLIBRE2;
		protected Champ $P_EXPORT;
		protected Champ $P_DATELIBRE1;
		protected Champ $P_DATELIBRE2;
		protected Champ $P_VENDEUR;
		protected Champ $P_TEXTEPRESSE;
		protected Champ $P_TEXTEVITRINE;
		protected Champ $P_REFERENCE;
		protected Champ $P_DATEMODIF;
		protected Champ $P_DATECREATION;
		protected Champ $P_REFFIXE;
		protected Champ $P_REFVARIABLE;
		protected Champ $P_ADRESSE;
		protected Champ $P_CP;
		protected Champ $P_PAYS;
		protected Champ $P_ADRESSE2;
		protected Champ $P_ADRESSE3;
		protected Champ $P_ADRESSE4;
		protected Champ $P_DROIT;
		protected Champ $P_LATITUDE;
		protected Champ $P_LONGITUDE;
		protected Champ $P_INTERLOCUTEURCONTACT;
		protected Champ $P_LIEA;
		protected Champ $P_TYPEPRODUIT;
		protected Champ $P_SOUSTYPEPRODUIT;
		protected Champ $P_SOUSSOUSTYPEPRODUIT;
		protected Champ $P_NBRLIBRE3;
		protected Champ $P_NBRLIBRE4;
		protected Champ $P_NBRLIBRE5;
		protected Champ $P_NBRLIBRE6;
		protected Champ $P_NBRLIBRE7;
		protected Champ $P_NBRLIBRE8;
		protected Champ $P_NBRLIBRE9;
		protected Champ $P_NBRLIBRE10;
		protected Champ $P_BOOLLIBRE3;
		protected Champ $P_BOOLLIBRE4;
		protected Champ $P_BOOLLIBRE5;
		protected Champ $P_BOOLLIBRE6;
		protected Champ $P_BOOLLIBRE7;
		protected Champ $P_BOOLLIBRE8;
		protected Champ $P_BOOLLIBRE9;
		protected Champ $P_BOOLLIBRE10;
		protected Champ $P_INFOLIBRE14;
		protected Champ $P_INFOLIBRE15;
		protected Champ $P_INFOLIBRE16;
		protected Champ $P_INFOLIBRE17;
		protected Champ $P_INFOLIBRE18;
		protected Champ $P_INFOLIBRE19;
		protected Champ $P_INFOLIBRE20;
		protected Champ $P_DATELIBRE3;
		protected Champ $P_DATELIBRE4;
		protected Champ $P_DATELIBRE5;
		protected Champ $P_DATELIBRE6;
		protected Champ $P_DATELIBRE7;
		protected Champ $P_DATELIBRE8;
		protected Champ $P_DATELIBRE9;
		protected Champ $P_DATELIBRE10;
		protected Champ $P_DESCRIPTION_FR;
		protected Champ $P_DESCRIPTION_UK;
		protected Champ $P_DESCRIPTION_ALL;
		protected Champ $P_DESCRIPTION_ES;
		protected Champ $P_DESCRIPTION_IT;
		protected Champ $P_CASEACOCHER1;
		protected Champ $P_CASEACOCHER2;
		protected Champ $P_CASEACOCHER3;
		protected Champ $P_CASEACOCHER4;
		protected Champ $P_CASEACOCHER5;
		protected Champ $P_CODEVENDEURGEST;
		protected Champ $P_CODEIMPORT;
		protected Champ $P_LIESOCIETE;
		
		public static $champsDefinitions = array(
			"P_CODE"                 => array(
				"ID"     => true,
				"type"   => "string",
				"length" => 20,
			),
			"P_COMPTEUR"             => array(
				"type"   => "int",
				"length" => false,
			),
			"P_CODESOCIETE"          => array(
				"type"   => "string",
				"length" => 5,
			),
			"P_CODEMODELE"           => array(
				"type"   => "string",
				"length" => 20,
			),
			"P_FAMILLE"              => array(
				"type"   => "string",
				"length" => 20,
			),
			"P_CONSTRUCTEUR"         => array(
				"type"   => "string",
				"length" => 20,
			),
			"P_NOM"                  => array(
				"type"   => "lob",
				"length" => 50,
			),
			"P_TAILLE1_MIN"          => array(
				"type"   => "decimal",
				"length" => false,
			),
			"P_TAILLE1_MAX"          => array(
				"type"   => "decimal",
				"length" => false,
			),
			"P_TAILLE2_MIN"          => array(
				"type"   => "decimal",
				"length" => false,
			),
			"P_TAILLE2_MAX"          => array(
				"type"   => "decimal",
				"length" => false,
			),
			"P_TAILLE3_MIN"          => array(
				"type"   => "decimal",
				"length" => false,
			),
			"P_TAILLE3_MAX"          => array(
				"type"   => "decimal",
				"length" => false,
			),
			"P_NSERIE"               => array(
				"type"   => "lob",
				"length" => 50,
			),
			"P_NOMBAPTEME"           => array(
				"type"   => "string",
				"length" => 30,
			),
			"P_DATECONSTRUCTION"     => array(
				"type"   => "string",
				"length" => 4,
			),
			"P_1UTILISATION"         => array(
				"type"   => "string",
				"length" => 4,
			),
			"P_DES_FR"               => array(
				"type"   => "string",
				"length" => 255,
			),
			"P_DES_UK"               => array(
				"type"   => "string",
				"length" => 255,
			),
			"P_DES_ALL"              => array(
				"type"   => "string",
				"length" => 255,
			),
			"P_DES_ES"               => array(
				"type"   => "string",
				"length" => 255,
			),
			"P_DES_IT"               => array(
				"type"   => "string",
				"length" => 255,
			),
			"P_DES_INTERNE"          => array(
				"type"   => "lob",
				"length" => false,
			),
			"P_PAHT"                 => array(
				"type"   => "decimal",
				"length" => false,
			),
			"P_SESSION"              => array(
				"type"   => "decimal",
				"length" => false,
			),
			"P_PVHT"                 => array(
				"type"   => "decimal",
				"length" => false,
			),
			"P_MARGE"                => array(
				"type"   => "decimal",
				"length" => false,
			),
			"P_TVA"                  => array(
				"type"   => "decimal",
				"length" => false,
			),
			"P_MONNAIE"              => array(
				"type"   => "string",
				"length" => 10,
			),
			"P_TTC"                  => array(
				"type"   => "string",
				"length" => 5,
			),
			"P_CONTRAT"              => array(
				"type"   => "string",
				"length" => 30,
			),
			"P_CODECONTACT"          => array(
				"type"   => "string",
				"length" => 20,
			),
			"P_DATE_ENTREE"          => array(
				"type"   => "datetime",
				"length" => false,
			),
			"P_DATE_SORTIE"          => array(
				"type"   => "datetime",
				"length" => false,
			),
			"P_LOCALISATION1"        => array(
				"type"   => "string",
				"length" => 20,
			),
			"P_LOCALISATION2"        => array(
				"type"   => "string",
				"length" => 20,
			),
			"P_LOCALISATIONVILLE"    => array(
				"type"   => "string",
				"length" => 30,
			),
			"P_LOCALISATIONSECTEUR"  => array(
				"type"   => "string",
				"length" => 30,
			),
			"P_ETAT"                 => array(
				"type"   => "string",
				"length" => 20,
			),
			"P_INFOLIBRE1"           => array(
				"type"   => "string",
				"length" => 40,
			),
			"P_INFOLIBRE2"           => array(
				"type"   => "string",
				"length" => 40,
			),
			"P_INFOLIBRE3"           => array(
				"type"   => "string",
				"length" => 40,
			),
			"P_INFOLIBRE4"           => array(
				"type"   => "string",
				"length" => 40,
			),
			"P_INFOLIBRE5"           => array(
				"type"   => "string",
				"length" => 40,
			),
			"P_INFOLIBRE6"           => array(
				"type"   => "string",
				"length" => 40,
			),
			"P_INFOLIBRE7"           => array(
				"type"   => "string",
				"length" => 40,
			),
			"P_INFOLIBRE8"           => array(
				"type"   => "string",
				"length" => 40,
			),
			"P_INFOLIBRE9"           => array(
				"type"   => "string",
				"length" => 40,
			),
			"P_INFOLIBRE10"          => array(
				"type"   => "string",
				"length" => 40,
			),
			"P_INFOLIBRE11"          => array(
				"type"   => "string",
				"length" => 40,
			),
			"P_INFOLIBRE12"          => array(
				"type"   => "string",
				"length" => 40,
			),
			"P_INFOLIBRE13"          => array(
				"type"   => "string",
				"length" => 40,
			),
			"P_NBRLIBRE1"            => array(
				"type"   => "decimal",
				"length" => false,
			),
			"P_NBRLIBRE2"            => array(
				"type"   => "decimal",
				"length" => false,
			),
			"P_BOOLLIBRE1"           => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"P_BOOLLIBRE2"           => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"P_EXPORT"               => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"P_DATELIBRE1"           => array(
				"type"   => "datetime",
				"length" => false,
			),
			"P_DATELIBRE2"           => array(
				"type"   => "datetime",
				"length" => false,
			),
			"P_VENDEUR"              => array(
				"type"   => "string",
				"length" => 20,
			),
			"P_TEXTEPRESSE"          => array(
				"type"   => "string",
				"length" => 255,
			),
			"P_TEXTEVITRINE"         => array(
				"type"   => "lob",
				"length" => false,
			),
			"P_REFERENCE"            => array(
				"type"   => "string",
				"length" => 16,
			),
			"P_DATEMODIF"            => array(
				"type"   => "datetime",
				"length" => false,
			),
			"P_DATECREATION"         => array(
				"type"   => "datetime",
				"length" => false,
			),
			"P_REFFIXE"              => array(
				"type"   => "string",
				"length" => 20,
			),
			"P_REFVARIABLE"          => array(
				"type"   => "string",
				"length" => 20,
			),
			"P_ADRESSE"              => array(
				"type"   => "string",
				"length" => 255,
			),
			"P_CP"                   => array(
				"type"   => "string",
				"length" => 10,
			),
			"P_PAYS"                 => array(
				"type"   => "string",
				"length" => 30,
			),
			"P_ADRESSE2"             => array(
				"type"   => "string",
				"length" => 50,
			),
			"P_ADRESSE3"             => array(
				"type"   => "string",
				"length" => 50,
			),
			"P_ADRESSE4"             => array(
				"type"   => "string",
				"length" => 50,
			),
			"P_DROIT"                => array(
				"type"   => "lob",
				"length" => false,
			),
			"P_LATITUDE"             => array(
				"type"   => "lob",
				"length" => false,
			),
			"P_LONGITUDE"            => array(
				"type"   => "lob",
				"length" => false,
			),
			"P_INTERLOCUTEURCONTACT" => array(
				"type"   => "string",
				"length" => 20,
			),
			"P_LIEA"                 => array(
				"type"   => "string",
				"length" => 20,
			),
			"P_TYPEPRODUIT"          => array(
				"type"   => "string",
				"length" => 20,
			),
			"P_SOUSTYPEPRODUIT"      => array(
				"type"   => "string",
				"length" => 20,
			),
			"P_SOUSSOUSTYPEPRODUIT"  => array(
				"type"   => "string",
				"length" => 20,
			),
			"P_NBRLIBRE3"            => array(
				"type"   => "decimal",
				"length" => false,
			),
			"P_NBRLIBRE4"            => array(
				"type"   => "decimal",
				"length" => false,
			),
			"P_NBRLIBRE5"            => array(
				"type"   => "decimal",
				"length" => false,
			),
			"P_NBRLIBRE6"            => array(
				"type"   => "decimal",
				"length" => false,
			),
			"P_NBRLIBRE7"            => array(
				"type"   => "decimal",
				"length" => false,
			),
			"P_NBRLIBRE8"            => array(
				"type"   => "decimal",
				"length" => false,
			),
			"P_NBRLIBRE9"            => array(
				"type"   => "decimal",
				"length" => false,
			),
			"P_NBRLIBRE10"           => array(
				"type"   => "decimal",
				"length" => false,
			),
			"P_BOOLLIBRE3"           => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"P_BOOLLIBRE4"           => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"P_BOOLLIBRE5"           => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"P_BOOLLIBRE6"           => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"P_BOOLLIBRE7"           => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"P_BOOLLIBRE8"           => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"P_BOOLLIBRE9"           => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"P_BOOLLIBRE10"          => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"P_INFOLIBRE14"          => array(
				"type"   => "string",
				"length" => 20,
			),
			"P_INFOLIBRE15"          => array(
				"type"   => "string",
				"length" => 20,
			),
			"P_INFOLIBRE16"          => array(
				"type"   => "string",
				"length" => 20,
			),
			"P_INFOLIBRE17"          => array(
				"type"   => "string",
				"length" => 20,
			),
			"P_INFOLIBRE18"          => array(
				"type"   => "string",
				"length" => 20,
			),
			"P_INFOLIBRE19"          => array(
				"type"   => "string",
				"length" => 20,
			),
			"P_INFOLIBRE20"          => array(
				"type"   => "string",
				"length" => 20,
			),
			"P_DATELIBRE3"           => array(
				"type"   => "datetime",
				"length" => false,
			),
			"P_DATELIBRE4"           => array(
				"type"   => "datetime",
				"length" => false,
			),
			"P_DATELIBRE5"           => array(
				"type"   => "datetime",
				"length" => false,
			),
			"P_DATELIBRE6"           => array(
				"type"   => "datetime",
				"length" => false,
			),
			"P_DATELIBRE7"           => array(
				"type"   => "datetime",
				"length" => false,
			),
			"P_DATELIBRE8"           => array(
				"type"   => "datetime",
				"length" => false,
			),
			"P_DATELIBRE9"           => array(
				"type"   => "datetime",
				"length" => false,
			),
			"P_DATELIBRE10"          => array(
				"type"   => "datetime",
				"length" => false,
			),
			"P_DESCRIPTION_FR"       => array(
				"type"   => "lob",
				"length" => false,
			),
			"P_DESCRIPTION_UK"       => array(
				"type"   => "lob",
				"length" => false,
			),
			"P_DESCRIPTION_ALL"      => array(
				"type"   => "lob",
				"length" => false,
			),
			"P_DESCRIPTION_ES"       => array(
				"type"   => "lob",
				"length" => false,
			),
			"P_DESCRIPTION_IT"       => array(
				"type"   => "lob",
				"length" => false,
			),
			"P_CASEACOCHER1"         => array(
				"type"   => "lob",
				"length" => false,
			),
			"P_CASEACOCHER2"         => array(
				"type"   => "lob",
				"length" => false,
			),
			"P_CASEACOCHER3"         => array(
				"type"   => "lob",
				"length" => false,
			),
			"P_CASEACOCHER4"         => array(
				"type"   => "lob",
				"length" => false,
			),
			"P_CASEACOCHER5"         => array(
				"type"   => "lob",
				"length" => false,
			),
			"P_CODEVENDEURGEST"      => array(
				"type"   => "string",
				"length" => 20,
			),
			"P_CODEIMPORT"           => array(
				"type"   => "string",
				"length" => 20,
			),
			"P_LIESOCIETE"           => array(
				"type"   => "string",
				"length" => 20,
			)
		);
		public static $tableName = "PRODUITMODELEFICHE";
		public static $tableCode = 16;
		public static $champPrincipalKey = "P_CODE";
		public static $champDroitKey = "P_DROIT";
		
	}

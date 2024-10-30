<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob\Entities;
	
	use Infocob\CRM\Products\Admin\Classes\Infocob\Champ;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Entity;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class InventaireProduitEntity extends Entity {
		
		protected Champ $IP_CODE;
		protected Champ $IP_COMPTEUR;
		protected Champ $IP_CODESOCIETE;
		protected Champ $IP_CODEPRODUIT;
		protected Champ $IP_COMMENTAIRE;
		protected Champ $IP_CODE_TYPE;
		protected Champ $IP_DATEMODIF;
		protected Champ $IP_DATECREATION;
		protected Champ $IP_PRIX;
		protected Champ $IP_QUANTITE;
		protected Champ $IP_MONNAIE;
		protected Champ $IP_TYPEPARENT;
		protected Champ $IP_EXPORT;
		protected Champ $IP_CODEIMPORT;
		protected Champ $IP_CODEMAITREIMPORT;
		protected Champ $IP_NOMTYPE;
		protected Champ $IP_CODETYPEIMPORT;
		protected Champ $IP_NOMFAMILLE;
		protected Champ $IP_CODEFAMILLEIMPORT;
		protected Champ $IP_NBRLIBRE1;
		protected Champ $IP_NBRLIBRE2;
		protected Champ $IP_NBRLIBRE3;
		protected Champ $IP_BOOLLIBRE1;
		protected Champ $IP_BOOLLIBRE2;
		protected Champ $IP_INFOLIBRE1;
		protected Champ $IP_INFOLIBRE2;
		protected Champ $IP_LIGNE;
		protected Champ $IP_LATITUDE;
		protected Champ $IP_LONGITUDE;
		protected Champ $IP_CP;
		protected Champ $IP_VILLE;
		protected Champ $IP_ADRESSE;
		protected Champ $IP_ADRESSE2;
		protected Champ $IP_ADRESSE3;
		protected Champ $IP_ADRESSE4;
		protected Champ $IP_ADRESSE5;
		protected Champ $IP_ADRESSE6;
		protected Champ $IP_PAYS;
		protected Champ $IP_LOCALISATION;
		protected Champ $IP_INDEX_ICONE;
		protected Champ $IP_COM_FR;
		protected Champ $IP_COM_ALL;
		protected Champ $IP_COM_ES;
		protected Champ $IP_COM_IT;
		protected Champ $IP_COM_US;
		protected Champ $IP_NBRLIBRE4;
		protected Champ $IP_NBRLIBRE5;
		protected Champ $IP_NBRLIBRE6;
		protected Champ $IP_NBRLIBRE7;
		protected Champ $IP_NBRLIBRE8;
		protected Champ $IP_NBRLIBRE9;
		protected Champ $IP_NBRLIBRE10;
		protected Champ $IP_NBRLIBRE11;
		protected Champ $IP_NBRLIBRE12;
		protected Champ $IP_NBRLIBRE13;
		protected Champ $IP_NBRLIBRE14;
		protected Champ $IP_NBRLIBRE15;
		protected Champ $IP_NBRLIBRE16;
		protected Champ $IP_NBRLIBRE17;
		protected Champ $IP_NBRLIBRE18;
		protected Champ $IP_NBRLIBRE19;
		protected Champ $IP_NBRLIBRE20;
		protected Champ $IP_NBRLIBRE21;
		protected Champ $IP_NBRLIBRE22;
		protected Champ $IP_NBRLIBRE23;
		protected Champ $IP_NBRLIBRE24;
		protected Champ $IP_NBRLIBRE25;
		protected Champ $IP_NBRLIBRE26;
		protected Champ $IP_NBRLIBRE27;
		protected Champ $IP_NBRLIBRE28;
		protected Champ $IP_NBRLIBRE29;
		protected Champ $IP_NBRLIBRE30;
		protected Champ $IP_NBRLIBRE31;
		protected Champ $IP_NBRLIBRE32;
		protected Champ $IP_NBRLIBRE33;
		protected Champ $IP_NBRLIBRE34;
		protected Champ $IP_NBRLIBRE35;
		protected Champ $IP_NBRLIBRE36;
		protected Champ $IP_NBRLIBRE37;
		protected Champ $IP_NBRLIBRE38;
		protected Champ $IP_NBRLIBRE39;
		protected Champ $IP_NBRLIBRE40;
		protected Champ $IP_NBRLIBRE41;
		protected Champ $IP_NBRLIBRE42;
		protected Champ $IP_NBRLIBRE43;
		protected Champ $IP_NBRLIBRE44;
		protected Champ $IP_NBRLIBRE45;
		protected Champ $IP_NBRLIBRE46;
		protected Champ $IP_NBRLIBRE47;
		protected Champ $IP_NBRLIBRE48;
		protected Champ $IP_NBRLIBRE49;
		protected Champ $IP_NBRLIBRE50;
		protected Champ $IP_BOOLLIBRE3;
		protected Champ $IP_BOOLLIBRE4;
		protected Champ $IP_BOOLLIBRE5;
		protected Champ $IP_BOOLLIBRE6;
		protected Champ $IP_BOOLLIBRE7;
		protected Champ $IP_BOOLLIBRE8;
		protected Champ $IP_BOOLLIBRE9;
		protected Champ $IP_BOOLLIBRE10;
		protected Champ $IP_BOOLLIBRE11;
		protected Champ $IP_BOOLLIBRE12;
		protected Champ $IP_BOOLLIBRE13;
		protected Champ $IP_BOOLLIBRE14;
		protected Champ $IP_BOOLLIBRE15;
		protected Champ $IP_BOOLLIBRE16;
		protected Champ $IP_BOOLLIBRE17;
		protected Champ $IP_BOOLLIBRE18;
		protected Champ $IP_BOOLLIBRE19;
		protected Champ $IP_BOOLLIBRE20;
		protected Champ $IP_DATELIBRE1;
		protected Champ $IP_DATELIBRE2;
		protected Champ $IP_DATELIBRE3;
		protected Champ $IP_DATELIBRE4;
		protected Champ $IP_DATELIBRE5;
		protected Champ $IP_DATELIBRE6;
		protected Champ $IP_DATELIBRE7;
		protected Champ $IP_DATELIBRE8;
		protected Champ $IP_DATELIBRE9;
		protected Champ $IP_DATELIBRE10;
		protected Champ $IP_DATELIBRE11;
		protected Champ $IP_DATELIBRE12;
		protected Champ $IP_DATELIBRE13;
		protected Champ $IP_DATELIBRE14;
		protected Champ $IP_DATELIBRE15;
		protected Champ $IP_DATELIBRE16;
		protected Champ $IP_DATELIBRE17;
		protected Champ $IP_DATELIBRE18;
		protected Champ $IP_DATELIBRE19;
		protected Champ $IP_DATELIBRE20;
		protected Champ $IP_INFOLIBRE3;
		protected Champ $IP_INFOLIBRE4;
		protected Champ $IP_INFOLIBRE5;
		protected Champ $IP_INFOLIBRE6;
		protected Champ $IP_INFOLIBRE7;
		protected Champ $IP_INFOLIBRE8;
		protected Champ $IP_INFOLIBRE9;
		protected Champ $IP_INFOLIBRE10;
		protected Champ $IP_INFOLIBRE11;
		protected Champ $IP_INFOLIBRE12;
		protected Champ $IP_INFOLIBRE13;
		protected Champ $IP_INFOLIBRE14;
		protected Champ $IP_INFOLIBRE15;
		protected Champ $IP_INFOLIBRE16;
		protected Champ $IP_INFOLIBRE17;
		protected Champ $IP_INFOLIBRE18;
		protected Champ $IP_INFOLIBRE19;
		protected Champ $IP_INFOLIBRE20;
		protected Champ $IP_CODEAFFAIRE;
		protected Champ $IP_CODECONTACT;
		protected Champ $IP_CODEINTERLOCUTEUR;
		protected Champ $IP_CODEACTION;
		protected Champ $IP_CODETYPEINVENTAIRE;
		protected Champ $IP_CODEVENDEUR;
		protected Champ $IP_CODEPRODUITFILS;
		protected Champ $IP_DROIT;
		protected Champ $IP_LIEA;
		protected Champ $IP_LIESOCIETE;
		
		public static $champsDefinitions = array(
			"IP_CODE"               => array(
				"ID"     => true,
				"type"   => "string",
				"length" => 20,
			),
			"IP_COMPTEUR"           => array(
				"type"   => "int",
				"length" => false,
			),
			"IP_CODESOCIETE"        => array(
				"type"   => "string",
				"length" => 5,
			),
			"IP_CODEPRODUIT"        => array(
				"type"   => "string",
				"length" => 20,
			),
			"IP_COMMENTAIRE"        => array(
				"type"   => "string",
				"length" => 80,
			),
			"IP_CODE_TYPE"          => array(
				"type"   => "string",
				"length" => 20,
			),
			"IP_DATEMODIF"          => array(
				"type"   => "datetime",
				"length" => false,
			),
			"IP_DATECREATION"       => array(
				"type"   => "datetime",
				"length" => false,
			),
			"IP_PRIX"               => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_QUANTITE"           => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_MONNAIE"            => array(
				"type"   => "string",
				"length" => 20,
			),
			"IP_TYPEPARENT"         => array(
				"type"   => "int",
				"length" => false,
			),
			"IP_EXPORT"             => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"IP_CODEIMPORT"         => array(
				"type"   => "string",
				"length" => 20,
			),
			"IP_CODEMAITREIMPORT"   => array(
				"type"   => "string",
				"length" => 20,
			),
			"IP_NOMTYPE"            => array(
				"type"   => "string",
				"length" => 50,
			),
			"IP_CODETYPEIMPORT"     => array(
				"type"   => "string",
				"length" => 20,
			),
			"IP_NOMFAMILLE"         => array(
				"type"   => "string",
				"length" => 50,
			),
			"IP_CODEFAMILLEIMPORT"  => array(
				"type"   => "string",
				"length" => 20,
			),
			"IP_NBRLIBRE1"          => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE2"          => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE3"          => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_BOOLLIBRE1"         => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"IP_BOOLLIBRE2"         => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"IP_INFOLIBRE1"         => array(
				"type"   => "string",
				"length" => 80,
			),
			"IP_INFOLIBRE2"         => array(
				"type"   => "string",
				"length" => 80,
			),
			"IP_LIGNE"              => array(
				"type"   => "int",
				"length" => false,
			),
			"IP_LATITUDE"           => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_LONGITUDE"          => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_CP"                 => array(
				"type"   => "string",
				"length" => 10,
			),
			"IP_VILLE"              => array(
				"type"   => "string",
				"length" => 30,
			),
			"IP_ADRESSE"            => array(
				"type"   => "string",
				"length" => 30,
			),
			"IP_ADRESSE2"           => array(
				"type"   => "string",
				"length" => 38,
			),
			"IP_ADRESSE3"           => array(
				"type"   => "string",
				"length" => 38,
			),
			"IP_ADRESSE4"           => array(
				"type"   => "string",
				"length" => 38,
			),
			"IP_ADRESSE5"           => array(
				"type"   => "string",
				"length" => 38,
			),
			"IP_ADRESSE6"           => array(
				"type"   => "string",
				"length" => 38,
			),
			"IP_PAYS"               => array(
				"type"   => "string",
				"length" => 30,
			),
			"IP_LOCALISATION"       => array(
				"type"   => "string",
				"length" => 255,
			),
			"IP_INDEX_ICONE"        => array(
				"type"   => "int",
				"length" => false,
			),
			"IP_COM_FR"             => array(
				"type"   => "lob",
				"length" => false,
			),
			"IP_COM_ALL"            => array(
				"type"   => "lob",
				"length" => false,
			),
			"IP_COM_ES"             => array(
				"type"   => "lob",
				"length" => false,
			),
			"IP_COM_IT"             => array(
				"type"   => "lob",
				"length" => false,
			),
			"IP_COM_US"             => array(
				"type"   => "lob",
				"length" => false,
			),
			"IP_NBRLIBRE4"          => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE5"          => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE6"          => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE7"          => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE8"          => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE9"          => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE10"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE11"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE12"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE13"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE14"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE15"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE16"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE17"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE18"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE19"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE20"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE21"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE22"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE23"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE24"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE25"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE26"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE27"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE28"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE29"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE30"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE31"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE32"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE33"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE34"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE35"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE36"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE37"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE38"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE39"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE40"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE41"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE42"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE43"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE44"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE45"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE46"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE47"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE48"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE49"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_NBRLIBRE50"         => array(
				"type"   => "decimal",
				"length" => false,
			),
			"IP_BOOLLIBRE3"         => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"IP_BOOLLIBRE4"         => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"IP_BOOLLIBRE5"         => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"IP_BOOLLIBRE6"         => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"IP_BOOLLIBRE7"         => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"IP_BOOLLIBRE8"         => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"IP_BOOLLIBRE9"         => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"IP_BOOLLIBRE10"        => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"IP_BOOLLIBRE11"        => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"IP_BOOLLIBRE12"        => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"IP_BOOLLIBRE13"        => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"IP_BOOLLIBRE14"        => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"IP_BOOLLIBRE15"        => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"IP_BOOLLIBRE16"        => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"IP_BOOLLIBRE17"        => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"IP_BOOLLIBRE18"        => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"IP_BOOLLIBRE19"        => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"IP_BOOLLIBRE20"        => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"IP_DATELIBRE1"         => array(
				"type"   => "datetime",
				"length" => false,
			),
			"IP_DATELIBRE2"         => array(
				"type"   => "datetime",
				"length" => false,
			),
			"IP_DATELIBRE3"         => array(
				"type"   => "datetime",
				"length" => false,
			),
			"IP_DATELIBRE4"         => array(
				"type"   => "datetime",
				"length" => false,
			),
			"IP_DATELIBRE5"         => array(
				"type"   => "datetime",
				"length" => false,
			),
			"IP_DATELIBRE6"         => array(
				"type"   => "datetime",
				"length" => false,
			),
			"IP_DATELIBRE7"         => array(
				"type"   => "datetime",
				"length" => false,
			),
			"IP_DATELIBRE8"         => array(
				"type"   => "datetime",
				"length" => false,
			),
			"IP_DATELIBRE9"         => array(
				"type"   => "datetime",
				"length" => false,
			),
			"IP_DATELIBRE10"        => array(
				"type"   => "datetime",
				"length" => false,
			),
			"IP_DATELIBRE11"        => array(
				"type"   => "datetime",
				"length" => false,
			),
			"IP_DATELIBRE12"        => array(
				"type"   => "datetime",
				"length" => false,
			),
			"IP_DATELIBRE13"        => array(
				"type"   => "datetime",
				"length" => false,
			),
			"IP_DATELIBRE14"        => array(
				"type"   => "datetime",
				"length" => false,
			),
			"IP_DATELIBRE15"        => array(
				"type"   => "datetime",
				"length" => false,
			),
			"IP_DATELIBRE16"        => array(
				"type"   => "datetime",
				"length" => false,
			),
			"IP_DATELIBRE17"        => array(
				"type"   => "datetime",
				"length" => false,
			),
			"IP_DATELIBRE18"        => array(
				"type"   => "datetime",
				"length" => false,
			),
			"IP_DATELIBRE19"        => array(
				"type"   => "datetime",
				"length" => false,
			),
			"IP_DATELIBRE20"        => array(
				"type"   => "datetime",
				"length" => false,
			),
			"IP_INFOLIBRE3"         => array(
				"type"   => "string",
				"length" => 80,
			),
			"IP_INFOLIBRE4"         => array(
				"type"   => "string",
				"length" => 80,
			),
			"IP_INFOLIBRE5"         => array(
				"type"   => "string",
				"length" => 80,
			),
			"IP_INFOLIBRE6"         => array(
				"type"   => "string",
				"length" => 80,
			),
			"IP_INFOLIBRE7"         => array(
				"type"   => "string",
				"length" => 80,
			),
			"IP_INFOLIBRE8"         => array(
				"type"   => "string",
				"length" => 80,
			),
			"IP_INFOLIBRE9"         => array(
				"type"   => "string",
				"length" => 80,
			),
			"IP_INFOLIBRE10"        => array(
				"type"   => "string",
				"length" => 80,
			),
			"IP_INFOLIBRE11"        => array(
				"type"   => "string",
				"length" => 80,
			),
			"IP_INFOLIBRE12"        => array(
				"type"   => "string",
				"length" => 80,
			),
			"IP_INFOLIBRE13"        => array(
				"type"   => "string",
				"length" => 80,
			),
			"IP_INFOLIBRE14"        => array(
				"type"   => "string",
				"length" => 80,
			),
			"IP_INFOLIBRE15"        => array(
				"type"   => "string",
				"length" => 80,
			),
			"IP_INFOLIBRE16"        => array(
				"type"   => "string",
				"length" => 80,
			),
			"IP_INFOLIBRE17"        => array(
				"type"   => "string",
				"length" => 80,
			),
			"IP_INFOLIBRE18"        => array(
				"type"   => "string",
				"length" => 80,
			),
			"IP_INFOLIBRE19"        => array(
				"type"   => "string",
				"length" => 80,
			),
			"IP_INFOLIBRE20"        => array(
				"type"   => "string",
				"length" => 80,
			),
			"IP_CODEAFFAIRE"        => array(
				"type"   => "string",
				"length" => 20,
			),
			"IP_CODECONTACT"        => array(
				"type"   => "string",
				"length" => 20,
			),
			"IP_CODEINTERLOCUTEUR"  => array(
				"type"   => "string",
				"length" => 20,
			),
			"IP_CODEACTION"         => array(
				"type"   => "string",
				"length" => 20,
			),
			"IP_CODETYPEINVENTAIRE" => array(
				"type"   => "string",
				"length" => 20,
			),
			"IP_CODEVENDEUR"        => array(
				"type"   => "string",
				"length" => 20,
			),
			"IP_CODEPRODUITFILS"    => array(
				"type"   => "string",
				"length" => 20,
			),
			"IP_DROIT"              => array(
				"type"   => "string",
				"length" => 100,
			),
			"IP_LIEA"               => array(
				"type"   => "string",
				"length" => 20,
			),
			"IP_LIESOCIETE"         => array(
				"type"   => "string",
				"length" => 20,
			)
		);
		
		public static $tableName = "INVENTAIREPRODUIT";
		public static $tableCode = 7;
		public static $champPrincipalKey = "IP_CODE";
		public static $champDroitKey = "IP_DROIT";
	}

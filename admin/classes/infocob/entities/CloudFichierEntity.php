<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob\Entities;
	
	use Infocob\CRM\Products\Admin\Classes\Infocob\Champ;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Entity;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class CloudFichierEntity extends Entity {
		
		protected Champ $FC_CODE;
		protected Champ $FC_DATECREATION;
		protected Champ $FC_DATEMODIF;
		protected Champ $FC_CODESOCIETE;
		protected Champ $FC_INDEXTABLE;
		protected Champ $FC_CODEMAITRE;
		protected Champ $FC_DROIT;
		protected Champ $FC_CODEMULTISOCIETE;
		protected Champ $FC_CODEVENDEUR;
		protected Champ $FC_NOMFICHIER;
		protected Champ $FC_DESCIPTION;
		protected Champ $FC_URL;
		protected Champ $FC_REPERTOIRE;
		protected Champ $FC_PARAM;
		protected Champ $FC_ICONE;
		protected Champ $FC_INDEXCLOUD;
		protected Champ $FC_PUBLIC;
		protected Champ $FC_LIEA;
		protected Champ $FC_EXPORT;
		protected Champ $FC_CODEIMPORT;
		protected Champ $FC_LATITUDE;
		protected Champ $FC_LONGITUDE;
		protected Champ $FC_TRAITE;
		protected Champ $FC_LIESOCIETE;
		protected Champ $FC_CONFIG;
		protected Champ $FC_NOMCONFIG;
		protected Champ $FC_DATEUPLOAD;
		protected Champ $FC_DATEDOWNLOAD;
		protected Champ $FC_URLPUBLIC;
		protected Champ $FC_INFOLIBRE1;
		protected Champ $FC_INFOLIBRE2;
		protected Champ $FC_PRIVE;
		protected Champ $FC_LIENFICHE;
		protected Champ $FC_TAILLE;
		protected Champ $FC_EXTENSION;
		protected Champ $FC_ICONEEXTENSION;
		protected Champ $FC_CRYPTE;
		protected Champ $FC_INDEXCRYPTAGE;
		
		public static $champsDefinitions = [
			"FC_CODE"             => [
				"ID"     => true,
				"type"   => "string",
				"length" => 20,
			],
			"FC_DATECREATION"     => [
				"type"   => "datetime",
				"length" => false,
			],
			"FC_DATEMODIF"        => [
				"type"   => "datetime",
				"length" => false,
			],
			"FC_CODESOCIETE"      => [
				"type"   => "string",
				"length" => 5,
			],
			"FC_INDEXTABLE"       => [
				"type"   => "int",
				"length" => false,
			],
			"FC_CODEMAITRE"       => [
				"type"   => "string",
				"length" => 20,
			],
			"FC_DROIT"            => [
				"type"   => "lob",
				"length" => false,
			],
			"FC_CODEMULTISOCIETE" => [
				"type"   => "string",
				"length" => 20,
			],
			"FC_CODEVENDEUR"      => [
				"type"   => "string",
				"length" => 20,
			],
			"FC_NOMFICHIER"       => [
				"type"   => "lob",
				"length" => false,
			],
			"FC_DESCIPTION"       => [
				"type"   => "lob",
				"length" => false,
			],
			"FC_URL"              => [
				"type"   => "lob",
				"length" => false,
			],
			"FC_REPERTOIRE"       => [
				"type"   => "lob",
				"length" => false,
			],
			"FC_PARAM"            => [
				"type"   => "lob",
				"length" => false,
			],
			"FC_ICONE"            => [
				"type"   => "int",
				"length" => false,
			],
			"FC_INDEXCLOUD"       => [
				"type"   => "int",
				"length" => false,
			],
			"FC_PUBLIC"           => [
				"type"   => "boolean",
				"length" => 1,
			],
			"FC_LIEA"             => [
				"type"   => "string",
				"length" => 20,
			],
			"FC_EXPORT"           => [
				"type"   => "boolean",
				"length" => 1,
			],
			"FC_CODEIMPORT"       => [
				"type"   => "string",
				"length" => 20,
			],
			"FC_LATITUDE"         => [
				"type"   => "lob",
				"length" => false,
			],
			"FC_LONGITUDE"        => [
				"type"   => "lob",
				"length" => false,
			],
			"FC_TRAITE"           => [
				"type"   => "int",
				"length" => false,
			],
			"FC_LIESOCIETE"       => [
				"type"   => "string",
				"length" => 20,
			],
			"FC_CONFIG"           => [
				"type"   => "string",
				"length" => 40,
			],
			"FC_NOMCONFIG"        => [
				"type"   => "string",
				"length" => 50,
			],
			"FC_DATEUPLOAD"       => [
				"type"   => "datetime",
				"length" => false,
			],
			"FC_DATEDOWNLOAD"     => [
				"type"   => "datetime",
				"length" => false,
			],
			"FC_URLPUBLIC"        => [
				"type"   => "lob",
				"length" => false,
			],
			"FC_INFOLIBRE1"       => [
				"type"   => "lob",
				"length" => 50,
			],
			"FC_INFOLIBRE2"       => [
				"type"   => "lob",
				"length" => 50,
			],
			"FC_PRIVE"            => [
				"type"   => "boolean",
				"length" => 1,
			],
			"FC_LIENFICHE"        => [
				"type"   => "boolean",
				"length" => 1,
			],
			"FC_TAILLE"           => [
				"type"   => "int",
				"length" => false,
			],
			"FC_EXTENSION"        => [
				"type"   => "lob",
				"length" => false,
			],
			"FC_ICONEEXTENSION"   => [
				"type"   => "lob",
				"length" => false,
			],
			"FC_CRYPTE"           => [
				"type"   => "boolean",
				"length" => 1,
			],
			"FC_INDEXCRYPTAGE"    => [
				"type"   => "lob",
				"length" => false,
			],
		
		];
		
		public static $tableName = "CLOUDFICHIER";
		public static $tableCode = 39;
		public static $champPrincipalKey = "FC_CODE";
		public static $champDroitKey = "FC_DROIT";
	}

<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob\Entities;
	
	use Infocob\CRM\Products\Admin\Classes\Infocob\Champ;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Entity;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Parametres20Entity extends Entity {
		
		protected Champ $PAR_NOM;
		protected Champ $PAR_CODESOCIETE;
		protected Champ $PAR_INTERNE;
		protected Champ $PAR_TYPE;
		protected Champ $PAR_PARAMETRE;
		protected Champ $PAR_CREATEUR;
		protected Champ $PAR_PROFILE;
		protected Champ $PAR_VERSION;
		protected Champ $PAR_DATEMODIF;
		protected Champ $PAR_DATECREATION;
		
		public static $champsDefinitions = [
			"PAR_NOM"          => [
				"ID"     => true,
				"type"   => "lob",
				"length" => 50,
			],
			"PAR_CODESOCIETE"  => [
				"type"   => "string",
				"length" => 5,
			],
			"PAR_INTERNE"      => [
				"type"   => "boolean",
				"length" => 1,
			],
			"PAR_TYPE"         => [
				"type"   => "int",
				"length" => false,
			],
			"PAR_PARAMETRE"    => [
				"type"   => "lob",
				"length" => false,
			],
			"PAR_CREATEUR"     => [
				"type"   => "string",
				"length" => 20,
			],
			"PAR_PROFILE"      => [
				"type"   => "string",
				"length" => 50,
			],
			"PAR_VERSION"      => [
				"type"   => "int",
				"length" => false,
			],
			"PAR_DATEMODIF"    => [
				"type"   => "datetime",
				"length" => false,
			],
			"PAR_DATECREATION" => [
				"type"   => "datetime",
				"length" => false,
			],
		];
		
		public static $tableName = "PARAMETRES20";
		public static $tableCode = 0;
		public static $champPrincipalKey = "PAR_NOM";
		public static $champDroitKey = "";
	}

<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob\Entities;
	
	use Infocob\CRM\Products\Admin\Classes\Infocob\Champ;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Entity;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class ParametresEntity extends Entity {
		
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
		
		public static $champsDefinitions = array(
			"PAR_NOM"          => array(
				"ID"     => true,
				"type"   => "lob",
				"length" => 50,
			),
			"PAR_CODESOCIETE"  => array(
				"type"   => "string",
				"length" => 5,
			),
			"PAR_INTERNE"      => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"PAR_TYPE"         => array(
				"type"   => "int",
				"length" => false,
			),
			"PAR_PARAMETRE"    => array(
				"type"   => "lob",
				"length" => false,
			),
			"PAR_CREATEUR"     => array(
				"type"   => "string",
				"length" => 20,
			),
			"PAR_PROFILE"      => array(
				"type"   => "string",
				"length" => 50,
			),
			"PAR_VERSION"      => array(
				"type"   => "int",
				"length" => false,
			),
			"PAR_DATEMODIF"    => array(
				"type"   => "datetime",
				"length" => false,
			),
			"PAR_DATECREATION" => array(
				"type"   => "datetime",
				"length" => false,
			)
		);
		
		public static $tableName = "PARAMETRES";
		public static $tableCode = 0;
		public static $champPrincipalKey = "PAR_NOM";
		public static $champDroitKey = "";
	}

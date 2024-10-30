<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob\Entities;
	
	use Infocob\CRM\Products\Admin\Classes\Infocob\Champ;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Entity;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class ListeTypeTypeEntity extends Entity {
		
		protected Champ $LT_CODE;
		protected Champ $LT_CODESOCIETE;
		protected Champ $LT_DATECREATION;
		protected Champ $LT_DATEMODIF;
		protected Champ $LT_NOM;
		protected Champ $LT_NOM_ALL;
		protected Champ $LT_NOM_ES;
		protected Champ $LT_NOM_IT;
		protected Champ $LT_NOM_US;
		protected Champ $LT_TYPE;
		protected Champ $LT_TYPEPARENT;
		protected Champ $LT_MASQUE;
		protected Champ $LT_CRYPTE;
		protected Champ $LT_INDEX_ICONE;
		protected Champ $LT_HINT;
		protected Champ $LT_STYLE;
		protected Champ $LT_REQUIRED;
		protected Champ $LT_READONLY;
		protected Champ $LT_MINVALUE;
		protected Champ $LT_MAXVALUE;
		protected Champ $LT_CHARCASE;
		protected Champ $LT_COLOR;
		protected Champ $LT_VISIBLE;
		protected Champ $LT_CURRENCY;
		protected Champ $LT_PRECISION;
		protected Champ $LT_INDEX_ICONE_CAPTION;
		protected Champ $LT_VISIBLEGRILLE;
		protected Champ $LT_DROIT;
		
		public static $champsDefinitions = array(
			"LT_CODE"                => array(
				"ID"     => true,
				"type"   => "string",
				"length" => 20,
			),
			"LT_CODESOCIETE"         => array(
				"type"   => "string",
				"length" => 5,
			),
			"LT_DATECREATION"        => array(
				"type"   => "datetime",
				"length" => false,
			),
			"LT_DATEMODIF"           => array(
				"type"   => "datetime",
				"length" => false,
			),
			"LT_NOM"                 => array(
				"type"   => "string",
				"length" => 40,
			),
			"LT_NOM_ALL"             => array(
				"type"   => "string",
				"length" => 40,
			),
			"LT_NOM_ES"              => array(
				"type"   => "string",
				"length" => 40,
			),
			"LT_NOM_IT"              => array(
				"type"   => "string",
				"length" => 40,
			),
			"LT_NOM_US"              => array(
				"type"   => "string",
				"length" => 40,
			),
			"LT_TYPE"                => array(
				"type"   => "lob",
				"length" => false,
			),
			"LT_TYPEPARENT"          => array(
				"type"   => "lob",
				"length" => false,
			),
			"LT_MASQUE"              => array(
				"type"   => "string",
				"length" => 40,
			),
			"LT_CRYPTE"              => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"LT_INDEX_ICONE"         => array(
				"type"   => "lob",
				"length" => 20,
			),
			"LT_HINT"                => array(
				"type"   => "lob",
				"length" => false,
			),
			"LT_STYLE"               => array(
				"type"   => "string",
				"length" => 20,
			),
			"LT_REQUIRED"            => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"LT_READONLY"            => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"LT_MINVALUE"            => array(
				"type"   => "decimal",
				"length" => false,
			),
			"LT_MAXVALUE"            => array(
				"type"   => "decimal",
				"length" => false,
			),
			"LT_CHARCASE"            => array(
				"type"   => "int",
				"length" => false,
			),
			"LT_COLOR"               => array(
				"type"   => "int",
				"length" => false,
			),
			"LT_VISIBLE"             => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"LT_CURRENCY"            => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"LT_PRECISION"           => array(
				"type"   => "int",
				"length" => false,
			),
			"LT_INDEX_ICONE_CAPTION" => array(
				"type"   => "lob",
				"length" => 20,
			),
			"LT_VISIBLEGRILLE"       => array(
				"type"   => "boolean",
				"length" => 1,
			),
			"LT_DROIT"               => array(
				"type"   => "lob",
				"length" => false,
			),
		
		);
		
		public static $tableName = "LISTETYPE_TYPE";
		public static $tableCode = - 1;
		public static $champPrincipalKey = "LT_CODE";
		public static $champDroitKey = "LT_DROIT";
	}

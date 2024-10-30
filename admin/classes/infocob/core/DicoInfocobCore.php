<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob\Core;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class DicoInfocobCore {
		
		//-- PRODUITFICHE
		const p_code                 = "P_CODE";
		const p_codecontact          = "P_CODECONTACT";
		const p_interlocuteurcontact = "P_INTERLOCUTEURCONTACT";
		const p_vendeur              = "P_VENDEUR";
		const p_typeproduit          = "P_TYPEPRODUIT";
		const p_soustypeproduit      = "P_SOUSTYPEPRODUIT";
		const p_soussoustypeproduit  = "P_SOUSSOUSTYPEPRODUIT";
		const p_nom                  = "P_NOM";
		const p_nombapteme           = "P_NOMBAPTEME";
		const p_nserie               = "P_NSERIE";
		const p_nbrlibre1            = "P_NBRLIBRE1";
		const p_infolibre5           = "P_INFOLIBRE5";
		const p_infolibre6           = "P_INFOLIBRE6";
		const p_infolibre7           = "P_INFOLIBRE7";
		const p_infolibre8           = "P_INFOLIBRE8";
		const p_infolibre14          = "P_INFOLIBRE14";
		const p_infolibre16          = "P_INFOLIBRE16";
		const p_pvht                 = "P_PVHT";
		const p_constructeur         = "P_CONSTRUCTEUR";
		const p_des_fr               = "P_DES_FR";
		const p_textepresse          = "P_TEXTEPRESSE";
		const p_textevitrine         = "P_TEXTEVITRINE";
		const p_date_entree          = "P_DATE_ENTREE";
		const p_date_sortie          = "P_DATE_SORTIE";
		const p_etat                 = "P_ETAT";
		const p_famille              = "P_FAMILLE";
		const p_adresse              = "P_ADRESSE";
		const p_adresse2             = "P_ADRESSE2";
		const p_adresse3             = "P_ADRESSE3";
		const p_adresse4             = "P_ADRESSE4";
		const p_cp                   = "P_CP";
		const p_localisationville    = "P_LOCALISATIONVILLE";
		const p_pays                 = "P_PAYS";
		const p_localisation1        = "P_LOCALISATION1";
		const p_localisation2        = "P_LOCALISATION2";
		const p_localisationsecteur  = "P_LOCALISATIONSECTEUR";
		const p_latitude             = "P_LATITUDE";
		const p_longitude            = "P_LONGITUDE";
		const p_taille1_min          = "P_TAILLE1_MIN";
		const p_datemodif            = "P_DATEMODIF";
		const p_contrat              = "P_CONTRAT";
		const p_taille2_min          = "P_TAILLE2_MIN";
		const p_nbrlibre4            = "P_NBRLIBRE4";
		const p_ville                = "P_VILLE";
		//-- END PRODUITFICHE
		
		//-- INVENTAIREPRODUIT
		const ip_code               = "IP_CODE";
		const ip_compteur           = "IP_COMPTEUR";
		const ip_codesociete        = "IP_CODESOCIETE";
		const ip_codeproduit        = "IP_CODEPRODUIT";
		const ip_commentaire        = "IP_COMMENTAIRE";
		const ip_code_type          = "IP_CODE_TYPE";
		const ip_datemodif          = "IP_DATEMODIF";
		const ip_datecreation       = "IP_DATECREATION";
		const ip_prix               = "IP_PRIX";
		const ip_quantite           = "IP_QUANTITE";
		const ip_monnaie            = "IP_MONNAIE";
		const ip_typeparent         = "IP_TYPEPARENT";
		const ip_export             = "IP_EXPORT";
		const ip_codeimport         = "IP_CODEIMPORT";
		const ip_codemaitreimport   = "IP_CODEMAITREIMPORT";
		const ip_nomtype            = "IP_NOMTYPE";
		const ip_codetypeimport     = "IP_CODETYPEIMPORT";
		const ip_nomfamille         = "IP_NOMFAMILLE";
		const ip_codefamilleimport  = "IP_CODEFAMILLEIMPORT";
		const ip_ligne              = "IP_LIGNE";
		const ip_latitude           = "IP_LATITUDE";
		const ip_longitude          = "IP_LONGITUDE";
		const ip_cp                 = "IP_CP";
		const ip_ville              = "IP_VILLE";
		const ip_adresse            = "IP_ADRESSE";
		const ip_adresse2           = "IP_ADRESSE2";
		const ip_adresse3           = "IP_ADRESSE3";
		const ip_adresse4           = "IP_ADRESSE4";
		const ip_adresse5           = "IP_ADRESSE5";
		const ip_adresse6           = "IP_ADRESSE6";
		const ip_pays               = "IP_PAYS";
		const ip_localisation       = "IP_LOCALISATION";
		const ip_codeaffaire        = "IP_CODEAFFAIRE";
		const ip_codecontact        = "IP_CODECONTACT";
		const ip_codeinterlocuteur  = "IP_CODEINTERLOCUTEUR";
		const ip_codeaction         = "IP_CODEACTION";
		const ip_codetypeinventaire = "IP_CODETYPEINVENTAIRE";
		const ip_codevendeur        = "IP_CODEVENDEUR";
		const ip_codeproduitfils    = "IP_CODEPRODUITFILS";
		const ip_droit              = "IP_DROIT";
		const ip_liea               = "IP_LIEA";
		const ip_liesociete         = "IP_LIESOCIETE";
		//-- END INVENTAIREPRODUIT
		
		//-- TYPEINVENTAIREPRODUIT
		const tip_code               = "TIP_CODE";
		const tip_compteur           = "TIP_COMPTEUR";
		const tip_codesociete        = "TIP_CODESOCIETE";
		const tip_des_fr             = "TIP_DES_FR";
		const tip_des_all            = "TIP_DES_ALL";
		const tip_des_es             = "TIP_DES_ES";
		const tip_des_it             = "TIP_DES_IT";
		const tip_des_us             = "TIP_DES_US";
		const tip_codefamille        = "TIP_CODEFAMILLE";
		const tip_datemodif          = "TIP_DATEMODIF";
		const tip_datecreation       = "TIP_DATECREATION";
		const tip_prix               = "TIP_PRIX";
		const tip_quantite           = "TIP_QUANTITE";
		const tip_monnaie            = "TIP_MONNAIE";
		const tip_export             = "TIP_EXPORT";
		const tip_codetypeimport     = "TIP_CODETYPEIMPORT";
		const tip_nomfamille         = "TIP_NOMFAMILLE";
		const tip_codefamilleimport  = "TIP_CODEFAMILLEIMPORT";
		const tip_couleur            = "TIP_COULEUR";
		const tip_index_icone        = "TIP_INDEX_ICONE";
		const tip_com_fr             = "TIP_COM_FR";
		const tip_com_all            = "TIP_COM_ALL";
		const tip_com_es             = "TIP_COM_ES";
		const tip_com_it             = "TIP_COM_IT";
		const tip_com_us             = "TIP_COM_US";
		const tip_codeproduit        = "TIP_CODEPRODUIT";
		const tip_codeaffaire        = "TIP_CODEAFFAIRE";
		const tip_codecontact        = "TIP_CODECONTACT";
		const tip_codeinterlocuteur  = "TIP_CODEINTERLOCUTEUR";
		const tip_codeaction         = "TIP_CODEACTION";
		const tip_codetypeinventaire = "TIP_CODETYPEINVENTAIRE";
		const tip_codevendeur        = "TIP_CODEVENDEUR";
		const tip_droit              = "TIP_DROIT";
		const tip_latitude           = "TIP_LATITUDE";
		const tip_longitude          = "TIP_LONGITUDE";
		const tip_liea               = "TIP_LIEA";
		const tip_liesociete         = "TIP_LIESOCIETE";
		//-- END TYPEINVENTAIREPRODUIT
		
		//-- LISTETYPE_TYPE
		const lt_code                = "LT_CODE";
		const lt_codesociete         = "LT_CODESOCIETE";
		const lt_datecreation        = "LT_DATECREATION";
		const lt_datemodif           = "LT_DATEMODIF";
		const lt_nom                 = "LT_NOM";
		const lt_nom_all             = "LT_NOM_ALL";
		const lt_nom_es              = "LT_NOM_ES";
		const lt_nom_it              = "LT_NOM_IT";
		const lt_nom_us              = "LT_NOM_US";
		const lt_type                = "LT_TYPE";
		const lt_typeparent          = "LT_TYPEPARENT";
		const lt_masque              = "LT_MASQUE";
		const lt_crypte              = "LT_CRYPTE";
		const lt_index_icone         = "LT_INDEX_ICONE";
		const lt_hint                = "LT_HINT";
		const lt_style               = "LT_STYLE";
		const lt_required            = "LT_REQUIRED";
		const lt_readonly            = "LT_READONLY";
		const lt_minvalue            = "LT_MINVALUE";
		const lt_maxvalue            = "LT_MAXVALUE";
		const lt_charcase            = "LT_CHARCASE";
		const lt_color               = "LT_COLOR";
		const lt_visible             = "LT_VISIBLE";
		const lt_currency            = "LT_CURRENCY";
		const lt_precision           = "LT_PRECISION";
		const lt_index_icone_caption = "LT_INDEX_ICONE_CAPTION";
		const lt_visiblegrille       = "LT_VISIBLEGRILLE";
		const lt_droit               = "LT_DROIT";
		//-- END LISTETYPE_TYPE
		
		//-- LISTETYPE_TYPE
		const lv_code         = "LV_CODE";
		const lv_codesociete  = "LV_CODESOCIETE";
		const lv_datecreation = "LV_DATECREATION";
		const lv_datemodif    = "LV_DATEMODIF";
		const lv_typeparent   = "LV_TYPEPARENT";
		const lv_codemaitre   = "LV_CODEMAITRE";
		const lv_codetype     = "LV_CODETYPE";
		const lv_ordre        = "LV_ORDRE";
		const lv_valeurnum    = "LV_VALEURNUM";
		const lv_valeurdate   = "LV_VALEURDATE";
		const lv_valeur       = "LV_VALEUR";
		//-- END LISTETYPE_TYPE
		
		//-- VENDEUR
		const v_code         = "V_CODE";
		const v_valide       = "V_VALIDE";
		const v_nom          = "V_NOM";
		const v_prenom       = "V_PRENOM";
		const v_droit        = "V_DROIT";
		const v_password     = "V_PASSWORD";
		const v_dictionnaire = "V_INFOLIBRE3";
		const v_admin        = "V_BOOLLIBRE1";
		const v_email        = "V_EMAIL";
		//-- END VENDEUR
		
		//-- CLOUDFICHIER
		const fc_code               = "FC_CODE";
		const fc_date_creation      = "FC_DATECREATION";
		const fc_date_modif         = "FC_DATEMODIF";
		const fc_date_upload        = "FC_DATEUPLOAD";
		const fc_code_societe       = "FC_CODESOCIETE";
		const fc_index_table        = "FC_INDEXTABLE";
		const fc_code_maitre        = "FC_CODEMAITRE";
		const fc_droit              = "FC_DROIT";
		const fc_config             = "FC_CONFIG";
		const fc_code_multi_societe = "FC_CODEMULTISOCIETE";
		const fc_code_vendeur       = "FC_CODEVENDEUR";
		const fc_nom_fichier        = "FC_NOMFICHIER";
		const fc_description        = "FC_DESCIPTION";
		const fc_url                = "FC_URL";
		const fc_repertoire         = "FC_REPERTOIRE";
		const fc_param              = "FC_PARAM";
		const fc_icone              = "FC_ICONE";
		const fc_index_cloud        = "FC_INDEXCLOUD";
		const fc_public             = "FC_PUBLIC";
		const fc_nom_config         = "FC_NOMCONFIG";
		const fc_taille             = "FC_TAILLE";
		const fc_traite             = "FC_TRAITE";
		const fc_extension          = "FC_EXTENSION";
		//-- END CLOUDFICHIER
		
	}


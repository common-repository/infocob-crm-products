<?php
	
	namespace Infocob\CRM\Products\Admin\Classes;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class ProductMeta {
		
		const FC_CODE_META_KEY        = "_infocob_fc_code";
		const FC_DATE_UPLOAD_META_KEY = "_infocob_fc_date_upload";
		const FC_TYPE_META_KEY        = "_infocob_fc_type";
		const FC_ORDER_META_KEY       = "_infocob_fc_order";
		
		const P_CODE_META_KEY        = "_infocob_p_code";
		const P_SUPP_META_KEY        = "_infocob_p_supp";
		const P_LANG_META_KEY        = "_infocob_p_lang";
		const P_TYPE_META_KEY        = "_infocob_p_type";
		const P_DATE_IMPORT_META_KEY = "_infocob_p_date_import";
		
		const P_LOCAL_PHOTO_NAME   = "_infocob_p_local_photo_name";
		const P_ID_IMPORT_META_KEY = "_infocob_p_id_config_import";
	}

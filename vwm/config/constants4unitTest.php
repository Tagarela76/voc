<?php
	//	DB CONSTANTS
	define ('DB_TYPE', 'mysql');

	define ('DB_HOST', '192.168.1.2');
	define ('DB_NAME', 'voc_unit_test');	
	define ('DB_USER', 'voc_root');
	define ('DB_PASS', '');

	
	//	DB TABLES
	define ('TB_FILTER', 'filter');
	define ('TB_COMPANY', 'company');
	define ('TB_FACILITY', 'facility');
	define ('TB_DEPARTMENT', 'department');
	define ('TB_USER', 'user');
	define ('TB_COUNTRY', 'country');
	define ('TB_STATE', 'state');
	define ('TB_INVENTORY', 'inventory');
	define ('TB_EQUIPMENT', 'equipment');
	define ('TB_PRODUCT', 'product');
	define ('TB_COMPONENT', 'component');
	define ('TB_MSDS', 'msds');
	define ('TB_DENSITY', 'density');	//	TODO: disable at AI
	define ('TB_UNITTYPE', 'unittype');
	define ('TB_RULE', 'rule');
	define ('TB_COAT', 'coat');
	define ('TB_SUBSTRATE', 'substrate');
	define ('TB_APMETHOD', 'apmethod');
	define ('TB_SUPPLIER', 'supplier');
	define ('TB_TYPE', 'type');
	define ('TB_LOL', 'list-of-lists');
	//define ('TB_PRODUCTGROUP', 'productgroup');	//	not used any more | May 14, 2010
	define ('TB_USAGE', 'mix');
	define ('TB_MIXGROUP', 'mixgroup');
	define ('TB_FORMULA', 'formula');
	define ('TB_COMPONENTGROUP', 'components_group');
	define ('TB_AGENCY', 'agency');
	define ('TB_AGENCY_BELONG', 'agency_belong');
	define ('TB_HAZARDOUS_CLASS', 'hazardous_class');
	define ('TB_GCG', 'gcg_list');
	define ('TB_ISSUE', 'issue');
	define ('TB_MSDS_FILE', 'msds_files');
	define ('TB_SELECTED_RULES_LIST', 'selected_rules_list');
	define ('TB_ACCESSORY', 'accessory');
	define ('TB_ACCESSORY2INVENTORY', 'accessory2inventory');
	define ('TB_MATERIAL2INVENTORY', 'material2inventory');
	define ('TB_USE_LOCATION2MATERIAL', 'use_location2material');
	define ('TB_DEFAULT', '`default`');	//ахтунг!
   // define ('TB_DEFAULTAPMETHODS',DefaultAPMethods);
    define ('TB_UNITCLASS', 'unit_class');
    define ('TB_MODULE','module');
	define ('TB_WASTE','waste');
	define ('TB_WASTE_STREAMS', 'waste_streams');
	define ('TB_POLLUTION', 'pollution');
	define ('TB_REDUCTION', 'reduction');
	define ('TB_USAGE_STATS', 'usage_stats');
	define ('TB_CARBON_EMISSIONS', 'carbon_emissions');
	define ('TB_EMISSION_FACTOR', 'emission_factor');
	define ('TB_CARBON_FOOTPRINT', 'carbon_footprint');	
	define ('TB_SOLVENT_MANAGEMENT', 'solvent_management');
	define ('TB_SOLVENT_OUTPUT', 'solvent_output');
	define ('TB_STORAGE', 'storage');
	define ('TB_STORAGE_EMPTY', 'storage_empty');
	define ('TB_STORAGE_DELETED','storage_deleted');
	define ('TB_NOTIFY_TIME', 'notify_time');
	define ('TB_LIMITES', 'limites');
	define ('TB_LIMIT2USER', 'limit2user');
	
	//billing system constants
	define ('TB_VPS_MODULE_BILLING','vps_module_billing');
	define ('TB_VPS_MODULE2CUSTOMER','vps_module2customer');	
	define ('TB_VPS_BILLING', 'vps_billing');
	define ('TB_VPS_CONFIG', 'vps_config');
	define ('TB_VPS_CUSTOMER', 'vps_customer');
	define ('TB_VPS_CUSTOMER_LIMIT', 'vps_customer_limit');
	define ('TB_VPS_DEACTIVATION', 'vps_deactivation');
	define ('TB_VPS_DEFINED_BP_REQUEST', 'vps_defined_bp_request');
	define ('TB_VPS_INVOICE', 'vps_invoice');	
	define ('TB_VPS_LIMIT', 'vps_limit');	
	define ('TB_VPS_LIMIT_PRICE', 'vps_limit_price');
	define ('TB_VPS_NOTIFICATION_SCRIPT', 'vps_notification_script');
	define ('TB_VPS_PAYMENT', 'vps_payment');	
	define ('TB_VPS_SCHEDULE_CUSTOMER_PLAN', 'vps_schedule_customer_plan');
	define ('TB_VPS_SCHEDULE_LIMIT', 'vps_schedule_limit');
	define ('TB_VPS_USER', 'vps_user');
	define ('TB_PAYMENT_METHOD', 'vps_payment_method');		
	define ('TB_VPS_INVOICE_ITEM', 'vps_invoice_item');
	define ('TB_VPS_CURRENCY', 'vps_currency');
	
	
			
	//	XNYO CONSTANTS
	define ('AUTH_TYPE', 'sql');
	define ('SQL_DELIMITER',';');
	define ('ADDITIONAL_INVOICE_COLUMNS_SQL',' DATEDIFF(period_end_date, CURDATE()) end_BP_days_left, DATEDIFF(period_end_date, period_start_date) days_count_at_BP ');
	
	//	FIELD CONSTANTS
	define ('LEN_CITY', 192);
	define ('LEN_PHONE', 32);
	define ('LEN_MOBILE', 32);
	define ('LEN_NAME_COMPANY', 96);
	define ('LEN_NAME_FACILITY', 32);
	define ('LEN_NAME_DEPARTMENT', 32);
	define ('LEN_USERNAME', 32);
	define ('LEN_ACCESSNAME', 32);
	define ('LEN_ADDRESS', 384);
	define ('LEN_COUNTY', 32);
	define ('LEN_FAX', 32);
	define ('LEN_CONTACT', 384);
	define ('LEN_TITLE', 192);
	define ('LEN_FACILITY_EPA', 50);
	define ('LEN_STATE', 32);
	define ('LEN_PRODUCT_NR', 50);
	define ('LEN_PRODUCT_DESC', 250);
	define ('LEN_COMPONENT_ID', 250);
	define ('LEN_DENSITYUSE', 32);
	define ('LEN_DENSITYTYPE_ID', 32);
	define ('LEN_DENSITY_TYPE', 32);
	define ('LEN_UNITTYPE_ID', 50);
	define ('LEN_RULE', 32);
	define ('LEN_RULE_NR', 32);
	define ('LEN_RULE_DESC', 200);
	define ('LEN_COAT_ID', 50);
	define ('LEN_COAT_DESC', 50);
	define ('LEN_SUBSTRATE_ID', 200);
	define ('LEN_SUBSTRATE_DESC', 200);
	define ('LEN_APMETHOD_ID', 200);
	define ('LEN_APMETHOD_DESC', 200);
	define ('LEN_INVENTORY_NAME', 50);
	define ('LEN_INVENTORY_DESC', 200);
	define ('LEN_QUANTITY', 25);
	define ('LEN_EQUIP_DESC', 50);
	define ('LEN_PERMIT', 20);
	define ('LEN_EXPIRE', 25);
	define ('LEN_VOC_DESC', 250);
	define ('LEN_PM_DESC', 250);
	define ('LEN_COUNTRY_NAME', 32);
	define ('LEN_STATE_NAME', 32);
	define ('LEN_SUPPLIER', 200);
	define ('LEN_TYPE_DESC', 50);
	define ('LEN_CAS', 50);
	define ('LEN_CAS_DESC', 250);
	define ('LEN_DESCRIPTION', 250);
	define ('LEN_COMP_NAME', 250);
	define ('LEN_PRODUCT_CODE', 32);
	define ('LEN_NAME', 50);
	define ('LEN_UNITTYPE_DESC', 200);
	define ('LEN_LOL_NAME', 32);
	define ('LEN_FORMULA', 250);
	define ('LEN_FORMULA_DESC', 200);
	define ('LEN_AGENCY_NAME', 64);
	define ('LEN_HAZARDOUS_TYPE', 64);
	
	
	//	OPTIONAL
	define ('LEN_PASSWORD', '30');
	
	
	//INTERFACE CONSTANTS
	define ('VOCNAME','VOC-WEB-MANAGER');
	
	
	//	VPS Constants	
	define ("VPS_NOTIFICATION_PERIOD",30);
	define ('VPS_SENDER_EMAIL', "vps@vocwebmanager.com");
	define ('VPS_MESSAGE_7DAYS', "7 days left");
	define ('VPS_MESSAGE_3DAYS', "3 days left");
	define ("USERS_TABLE", "user");	
	
		
	//	PATHS
	define ('DIR_PATH_LOGS', '../voc_logs/');
	define ('DIR_PATH_MODULES', 'modules/');	
	define ('DIR_PATH_DESIGN', 'modules/design/');	//	TODO: move design to protected modules
	define ('DIR_PATH_JS', 'js/');					//	TODO: move js to root folder
	define ('DIR_PATH_TMP', 'modules/tmp/');		//	TODO: move tmp to to protected modules
	
	
	//	PAGINATION
	define ('ROW_COUNT', 15);
	
	//	AUTOCOMPLETE
	define ('AUTOCOMPLETE_LIMIT', 15);
	
	//	MAINTENANCE
	define ('MAINTENANCE', 	false);
	
	
	//	LOCALIZATION
	define ('DEFAULT_REGION', 	'us');
	
	
	//	MAILER
	define ('AUTH_SENDER', 'authentification');
	define ('BACKUP_SENDER', 'backup');
	define ('VPS_SENDER', 'vps');

	//	UNITTESTING
	define ('FIXTURE_PATH', 'modules/tests/suite/fixture/');
	define ('EXPECTED_PATH', 'modules/tests/suite/expected/');	
?>
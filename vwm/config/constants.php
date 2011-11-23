<?php

	//define('VERSION','standalone');			
	define('VERSION','web');

	switch(VERSION)
	{
		case 'standalone':
			require_once('installConstants.php');		
			define ('DB_TYPE', 'mysql');
			define ('DB_IMPORT', 'new_voc');
			define ('PATH_BRIDGE_XML', '../bridge/bridge.xml');
			define ('PATH_BRIDGE_XML_SCHEMA', '../bridge/bridge.xsd');
			define ('TRACE_MYSQL', 'off'); 	//	TRACE MYSQL QUERIES
			define ('REGION', 	'us');	
			define ('DOMAIN', 	'vocwebmanager.com');
			break;			
		
		case 'web':
			define('ENVIRONMENT2LOAD','local');			//	LOCAL AREA
		//	define('ENVIRONMENT2LOAD','work');			//	KTTSOFT AREA
		//	define('ENVIRONMENT2LOAD','server');		//	DEDICATED SERVER LIVE AREA
		//	define('ENVIRONMENT2LOAD','sandbox');		//	DEDICATED SERVER SANDBOX AREA
		//	define('ENVIRONMENT2LOAD','acceptance');	//	LOCAL ACCEPTANCE TESTS AREA
		//	define('ENVIRONMENT2LOAD','localEU-UK');	//	LOCAL EUROPE - UNITED KINGDOM AREA
		//	define('ENVIRONMENT2LOAD','workEU-UK');		//	KTTSOFT EUROPE - UNITED KINGDOM AREA
		//	define('ENVIRONMENT2LOAD','serverEU-UK');	//	DEDICATED SERVER LIVE EUROPE - UNITED KINGDOM AREA
		//	define('ENVIRONMENT2LOAD','sandboxEU-UK');	//	DEDICATED SERVER SANDBOX EUROPE - UNITED KINGDOM AREA
			//	LOAD ENVIRONMENT DEPENDED CONSTANTS
			switch (ENVIRONMENT2LOAD) {
			//	LOCAL AREA
			case 'local':
				define ('DB_HOST', 'localhost');
				define ('DB_NAME', 'voc');
				define ('DB_IMPORT', 'voc_real');
				define ('DB_USER', 'root');
				define ('DB_PASS', 'rootpass');
				//actionMsdsUploader
				define ('PATH_BRIDGE_XML', '../bridge/bridge.xml');
				define ('PATH_BRIDGE_XML_SCHEMA', '../bridge/bridge.xsd');			
				define ('TRACE_MYSQL', 'off'); 	//	TRACE MYSQL QUERIES
				define ('REGION', 	'us');
				define ('DOMAIN', 	'vocwebmanager.com');
				define ('DEFAULT_CURRENCY', 1);	//	USD
                                define ('DEFAULT_DATE_FORMAT',"m/d/Y"); // USA date format
				break;				
				
			//	KTTSOFT AREA	
			case 'work':
				define ('DB_HOST', '192.168.1.2');
				define ('DB_NAME', 'voc');
				define ('DB_IMPORT', 'new_voc');
				define ('DB_USER', 'voc_root');
				define ('DB_PASS', '');
				define ('PATH_BRIDGE_XML', '../bridge/bridge.xml');
				define ('PATH_BRIDGE_XML_SCHEMA', '../bridge/bridge.xsd');
				define ('TRACE_MYSQL', 'off'); 	//	TRACE MYSQL QUERIES
				define ('REGION', 	'us');	
				define ('DOMAIN', 	'vocwebmanager.com');
				define ('DEFAULT_CURRENCY', 1);//	USD
                define ('DEFAULT_DATE_FORMAT',"m/d/Y"); // USA date format
				break;				
				
			//	DEDICATED SERVER LIVE AREA
			case 'server':
				define ('DB_HOST', 'localhost');
				define ('DB_NAME', 'vocwe0_db01');
				define ('DB_USER', 'vocwe0_Bryant');
				define ('DB_PASS', 'tAke3');
				define ('ENVIRONMENT', 'server');
				define ('PATH_BRIDGE_XML', '../bridge/bridge.xml');
				define ('PATH_BRIDGE_XML_SCHEMA', '../bridge/bridge.xsd');
				define ('TRACE_MYSQL', 'on'); 	//	TRACE MYSQL QUERIES
				define ('REGION', 	'us');
				define ('DOMAIN', 	'vocwebmanager.com');
				define ('DEFAULT_CURRENCY', 1);//	USD
                define ('DEFAULT_DATE_FORMAT',"m/d/Y"); // USA date format
				break;				
				
			//	DEDICATED SERVER SANDBOX AREA
			case 'sandbox':
				define ('DB_HOST', 'localhost');
				define ('DB_NAME', 'voc_sandbox');
				define ('DB_USER', 'admin');
				define ('DB_PASS', 'TBopU20');
				define ('ENVIRONMENT', 'sandbox');
				define ('PATH_BRIDGE_XML', '../bridge/bridge.xml');
				define ('PATH_BRIDGE_XML_SCHEMA', '../bridge/bridge.xsd');
				define ('TRACE_MYSQL', 'off'); 	//	TRACE MYSQL QUERIES
				define ('REGION', 	'us');
				define ('DOMAIN', 	'vocwebmanager.com');
				define ('DEFAULT_CURRENCY', 1);//	USD
                define ('DEFAULT_DATE_FORMAT',"m/d/Y"); // USA date format
				break;				
				
			//	LOCAL ACCEPTANCE TESTS AREA
			case 'acceptance':
				define ('DB_HOST', 'localhost');
				define ('DB_NAME', 'voc_acceptance');
				define ('DB_USER', 'root');
				define ('DB_PASS', 'fghbjhysq');
				define ('PATH_BRIDGE_XML', './modules/tests/acceptance/vps/bridge/bridge.xml');
				define ('PATH_BRIDGE_XML_SCHEMA', './modules/tests/acceptance/vps/bridge/bridge.xsd');
				define ('TRACE_MYSQL', 'off'); 	//	TRACE MYSQL QUERIES
				define ('REGION', 	'us');
				define ('DOMAIN', 	'vocwebmanager.com');
				define ('DEFAULT_CURRENCY', 1);//	USD
                define ('DEFAULT_DATE_FORMAT',"m/d/Y"); // USA date format
				break;				
				
			//	LOCAL EUROPE - UNITED KINGDOM AREA
			case 'localEU-UK':
				define ('DB_HOST', 'localhost');
				define ('DB_NAME', 'voc_uk');
				define ('DB_IMPORT', 'voc_real');
				define ('DB_USER', 'root');
				define ('DB_PASS', 'bdevelop');
				define ('PATH_BRIDGE_XML', '../bridge/bridge.xml');
				define ('PATH_BRIDGE_XML_SCHEMA', '../bridge/bridge.xsd');
				define ('TRACE_MYSQL', 'off'); 	//	TRACE MYSQL QUERIES
				define ('REGION', 	'eu_uk');
				define ('DOMAIN', 	'vocwebmanager.co.uk');
				define ('DEFAULT_CURRENCY', 2);//	GBP
                define ('DEFAULT_DATE_FORMAT',"d/m/Y"); // Europe date format
				break;				
			
			//	WORK EUROPE - UNITED KINGDOM AREA
			case 'workEU-UK':		
				define ('DB_HOST', '192.168.1.2');
				define ('DB_NAME', 'voc_uk');
				define ('DB_IMPORT', 'voc_real');
				define ('DB_USER', 'voc_root');
				define ('DB_PASS', '');
				define ('PATH_BRIDGE_XML', '../bridge/bridge.xml');
				define ('PATH_BRIDGE_XML_SCHEMA', '../bridge/bridge.xsd');
				define ('TRACE_MYSQL', 'off'); 	//	TRACE MYSQL QUERIES
				define ('REGION', 	'eu_uk');
				define ('DOMAIN', 	'vocwebmanager.co.uk');
				define ('DEFAULT_CURRENCY', 2);//	GBP
                define ('DEFAULT_DATE_FORMAT',"d/m/Y"); // Europe date format
				break;				
				
			//	DEDICATED SERVER LIVE EUROPE - UNITED KINGDOM AREA
			case 'serverEU-UK':
				define ('DB_HOST', 'localhost');
				define ('DB_NAME', 'vocwe0_db02');
				define ('DB_USER', 'vocwe0_TarasShev');
				define ('DB_PASS', 'B7136H1HI4Lf8u');
				define ('ENVIRONMENT', 'server');
				define ('PATH_BRIDGE_XML', '../bridge/bridge.xml');
				define ('PATH_BRIDGE_XML_SCHEMA', '../bridge/bridge.xsd');
				define ('TRACE_MYSQL', 'on'); 	//	TRACE MYSQL QUERIES
				define ('REGION', 	'eu_uk');
				define ('DOMAIN', 	'vocwebmanager.co.uk');
				define ('DEFAULT_CURRENCY', 2);//	GBP
                define ('DEFAULT_DATE_FORMAT',"d/m/Y"); // Europe date format
				break;				
				
			//	DEDICATED SERVER SANDBOX EUROPE - UNITED KINGDOM AREA			
			case 'sandboxEU-UK':
				define ('DB_HOST', 'localhost');
				define ('DB_NAME', 'voc_uk_sandbox');
				define ('DB_USER', 'admin');
				define ('DB_PASS', 'TBopU20');
				define ('ENVIRONMENT', 'sandbox');
				define ('PATH_BRIDGE_XML', '../bridge/bridge.xml');
				define ('PATH_BRIDGE_XML_SCHEMA', '../bridge/bridge.xsd');
				define ('TRACE_MYSQL', 'off'); 	//	TRACE MYSQL QUERIES
				define ('REGION', 	'eu_uk');
				define ('DOMAIN', 	'vocwebmanager.co.uk');
				define ('DEFAULT_CURRENCY', 2);//	GBP
                define ('DEFAULT_DATE_FORMAT',"d/m/Y"); // Europe date format
				break;			
		}	
		break;
	}
	define ('DB_TYPE', 'mysql');
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
	define ('TB_TECH_SHEET_FILE', 'tech_sheet_files');
	define ('TB_DENSITY', 'density');	//	TODO: disable at AI
	define ('TB_UNITTYPE', 'unittype');
	define ('TB_RULE', 'rule');
	define ('TB_COAT', 'coat');
	define ('TB_SUBSTRATE', 'substrate');
	define ('TB_APMETHOD', 'apmethod');
	define ('TB_SUPPLIER', 'supplier');
	define ('TB_TYPE', 'type');
	define ('TB_LOL', 'list-of-lists');
	define('TB_PRODUCT2TYPE', 'product2type');
	define('TB_INDUSTRY_TYPE', 'industry_type');
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
	define ('TB_REG_AGENCY', 'reg_agency');
	define ('TB_REG_ACTS', 'reg_acts');
	define ('TB_USERS2REGS', 'users2regs');
	define ('TB_DATE_FORMAT', 'date_format');
	define ('TB_NOTIFY_CODE', 'notify_code');
	
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
	define ('TB_VPS_BILLING2CURRENCY','vps_billing2currency');
	define ('TB_VPS_MODULE2CURRENCY','vps_module2currency');
	define ('TB_VPS_LIMIT_PRICE2CURRENCY','vps_limit_price2currency'); 
	
	define ('TB_CONTACTS','contacts');
	define ('TB_CONTACTS_TYPE','contacts_type');
	define ('TB_BOOKMARKS_TYPE','bookmarks_type');
	
	define('TB_PFP2PRODUCT','pfp2product');
	define('TB_PFP','preformulated_products');
	define('TB_PFP2COMPANY','pfp2company');
    define('TB_NEW_PRODUCT_REQUEST','new_product_request');
	define('TB_USER_REQUEST', 'user_request');
	define('TB_COMPANY_SETUP_REQUEST', 'company_setup_request');
	
			
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
	define ('ROW_COUNT', 50);
	
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
	
	//	REGUPDATES
	define ('XML_FILE_REVIEWED_RULES', 'http://www.reginfo.gov/public/do/XMLViewFileAction?f=EO_RULES_UNDER_REVIEW.xml');
	define ('XML_FILE_COMPLETED_RULES', 'http://www.reginfo.gov/public/do/XMLViewFileAction?f=EO_RULE_COMPLETED_30_DAYS.xml');
	
?>

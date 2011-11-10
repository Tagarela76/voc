<?php
class CASalesdocs extends Controller {
	
	function CASalesdocs($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='salesdocs';
		$this->parent_category='salesdocs';		
	}
	
	function runAction() {
		$this->runCommon('admin');
		$functionName='action'.ucfirst($this->action);	
			
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	private function actionBrowseCategory() {
		$ms = new ModuleSystem($this->db);
		$moduleMap = $ms->getModulesMap();
		$mDocs = new $moduleMap['docs'];

		$params = array(
			'db' => $this->db,
			'isSales' => 'yes',
			'salesID' => '1'
		);
		$result = $mDocs->prepareView($params);
		
		foreach($result as $key => $data) {
			$this->smarty->assign($key,$data);
		}
		
		$itemsCount = count($result['InfoTree']);
		$this->smarty->assign('itemsCount', $itemsCount);
		$this->smarty->assign('tpl', 'tpls/salesdocs.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionAddItem()
	{
		$request= $this->getFromRequest();
		$request['id'] = '1';
		$request['parent_category'] = 'sales';

		$ms = new ModuleSystem($this->db);
		$moduleMap = $ms->getModulesMap();
		$mDocs = new $moduleMap['docs'];

		if($_FILES)
		{
			$params = $this->getFromPost();
			$params['db'] = $this->db;
			$params['isSales'] = 'yes';
			$params['salesID'] = '1';
			$result = $mDocs->prepareAdd($params);

			foreach($result as $key => $data)
			{
				$this->smarty->assign($key,$data);
			}

		}
		else
		{
			$result = $mDocs->prepareConstants($params);
			$this->smarty->assign('info',array('folder' => 'none', 'item_type' => $result['doc_item']));
		}

		$params = array(
						'db' => $this->db,
						'isSales' => 'yes',
						'salesID' => '1'
						);
		$result = $mDocs->prepareView($params);
		foreach($result as $key => $data)
		{
			$this->smarty->assign($key,$data);
		}
		
		//	set js scripts
		$jsSources = array(
							'modules/js/addDocItem.js',
							'modules/js/listDocs.js'
						  );
		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign('tpl','tpls/addDocItem.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionDeleteItem()
	{
		$req_id=$this->getFromRequest('id');
		if (!is_array($req_id))
			$req_id=array($req_id);
		
		$ms = new ModuleSystem($this->db);
		$moduleMap = $ms->getModulesMap();
		
		$mDocs = new $moduleMap['docs'];
		if ($this->getFromPost('step') == null)
		{
			$this->smarty->assign('step','choose');
		}
		else
		{
			$params = array(
							'db' => $this->db,
							'isSales' => 'yes',
							'salesID' => '1',
							'xnyo' => $this->xnyo
							);
			$result = $mDocs->prepareViewDelete($params);
			foreach($result as $key => $data) {
				$this->smarty->assign($key,$data);
			}
		}

		$params = array(
						'db' => $this->db,
						'isSales' => 'yes',
						'salesID' => '1'
						);
		$result = $mDocs->prepareView($params);

		foreach($result as $key => $data) {
			$this->smarty->assign($key,$data);
		}

		//	set js scripts
		$jsSources = array('modules/js/listDocs.js');
		$this->smarty->assign('jsSources', $jsSources);

		$this->smarty->assign('tpl','tpls/deleteDocItem.tpl');
		$this->smarty->assign('request',$this->getFromRequest());
		$this->smarty->display("tpls:index.tpl");
		$this->finalDeleteItemCommon($itemForDelete,$linkedNotify,$count,$info);
		die();
	}
	
	private function actionConfirmDelete()
	{
		$ms = new ModuleSystem($this->db);
		$moduleMap = $ms->getModulesMap();
	
		$mDocs = new $moduleMap['docs'];
		$params = array(
							'db' => $this->db,
							'isSales' => 'yes',
							'salesID' => '1',
							'xnyo' => $this->xnyo
						);
		$successDeleteInventories = $mDocs->prepareDelete($params);	//$successDeleteInventories ?????????????

		if ($successDeleteInventories){
			header("Location: admin.php?action=browseCategory&category=salesdocs&notify=11");
		}	
	}
	
	private function actionEdit()
	{
		$ms = new ModuleSystem($this->db);
		$moduleMap = $ms->getModulesMap();
	
		$mDocs = new $moduleMap['docs'];
		$post =$this->getFromPost();
		
		if(!is_null($this->getFromPost('file')))
		{
			$params = $post;
			$params['db'] = $this->db;
			$params['isSales'] = 'yes';
			$params['salesID'] = '1';
			$result = $mDocs->prepareEdit($params);
			foreach($result as $key => $data)
			{
				$this->smarty->assign($key,$data);
			}
		}
		elseif (($post['folder']!= null) || ($post['name']!=null) || ($post['description']!=null))
		{
			$this->smarty->assign('info',$post);
			$this->smarty->assign('error','Choose the document to edit it!');
		}
		else
		{
			$result = $mDocs->prepareConstants($this->db);
			$this->smarty->assign('info',array('item_type' => $result['doc_item']));
		}
		$params = array(
							'db' => $this->db,
							'isSales' => 'yes',
							'salesID' => '1'
						);
		$result = $mDocs->prepareView($params);

		foreach($result as $key => $data)
		{
			$this->smarty->assign($key,$data);
		}

		//	set js scripts
		$jsSources = array(
								'modules/js/addDocItem.js',
								'modules/js/listDocs.js'
						  );
		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign('tpl','tpls/editDocItem.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
}
<?php
//	TODO: применить только к заголовкам табов пока. придумать UI
//	а, да! пока не используется. Взамен придумали SL (Simple Localization)
/**
 * Project:     VOC WEB MANAGER
 * File:        Localization.class.php
 *
 * Localize system to region or client  
 *
 * @copyright 2010, KaTeT-Software
 */
 
class Localization {


	private $whatToLoad;
	public $format = 'xml';
	public $localizationPath = 'modules/localization/';
	
	const DEFAULT_REGION = REGION;
	
	/**
	 * @var mixed region or company ID
	 */
    function Localization($whatToLoad = null) {
    	if ($whatToLoad === null)
    		return false;
    		
    	$this->whatToLoad = $whatToLoad;    	
    	
    	$filePath = $this->_getFilePathByWhatToLoad();
    	
    	$this->parseXML($filePath);
    }
    
    
    
    private function _formCustomizedLocale() {
    	return 'customized_'.$this->whatToLoad.'.'.$this->format;	    	
    }
    
    private function _formLocale() {
    	return $this->whatToLoad.".".$this->format;
    }
    
    private function _getFilePathByWhatToLoad() {    	
    	return ((int)$this->whatToLoad) ? $this->_formCustomizedLocale() : $filePath = $this->_formLocale();  	
    }
    
    private function _loadXML($filePath) {
    	//	add slash if needed
    	if (substr($this->localizationPath, -1) != DIRECTORY_SEPARATOR) {
    		$this->localizationPath = $this->localizationPath.DIRECTORY_SEPARATOR;
    	}    	
    	
    	$filePath = $this->localizationPath.$filePath;
    	
    	//	PARSING...
    	$doc = new DOMDocument();
    	$doc->preserveWhiteSpace = false;
      	$doc->formatOutput = true;
            	
      	if (!$doc->load(realpath($filePath))) {
      		throw new Exception('Can not load locale '.$filePath);
      	}
      	
      	return $doc;
    }
    
    /**
     * Parse XML file with translations & set constants
     * @param string path     
     */
    public function parseXML($filePath) {    	
    	$doc = $this->_loadXML($filePath);
    	      	
      	$texts = $doc->getElementsByTagName('text');
      	foreach ($texts as $text) {
      		define($text->getAttribute('id'), $text->nodeValue);      		
      	}
      	
        //	that's  all folks!
    }
    
    
    
    /**
     * Edit or add text item to XML file
     * @param string constant (id)
     * @param string localized or customized text
     */
    public function setText($id, $text) {
    	var_dump($id, $text);
    	$filePath = $this->_getFilePathByWhatToLoad();
    	$doc = $this->_loadXML($filePath);  
    	echo $this->localizationPath.'localization.xsd';
    	if (!$doc->schemaValidate($this->localizationPath.'localization.xsd')) {
    		throw new Exception('XML validation failed: '.$filePath );
    	}  	
    	var_dump($doc->getElementById($id));
    }
    
    
    public function zamusorit() {
    	$filePath = $this->_getFilePathByWhatToLoad();
    	$doc = $this->_loadXML($filePath);  
    	
    	$localization = $doc->getElementsByTagName('localization')->item(0);
    	    	
    	for ($i=0;$i<9000;$i++) {
    		$text = $doc->createElement('text');
    		$text->setAttribute('id', rand(0, 10000));
    		$node = $doc->createTextNode('blah-blah');
    		$text->appendChild($node);    		    		
    		$localization->appendChild($text);    		
    	}
    	$doc->save($this->localizationPath.$filePath);    	
    }
    
}
?>
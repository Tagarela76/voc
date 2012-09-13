<?php
	/**
	 * Exporter class
	 * voc web manager
	 *
	 * Use this class to export tables to PDF format
	 *
	 *
	 * @example
	 * $exporter = new Exporter(Exporter::PDF);
	 *
	 * //	set document title
	 * $exporter->title = "Mix list";
	 *
	 * //	setup thead
	 * $header = array('mix_id'=>'ID', 'description'=>'Description');
	 * $exporter->setThead($header);
	 *
	 * //	setup tbody
	 * $rows[] = array('description'=>1,'mix_id'=>2);
	 * $rows[] = array('description'=>3,'mix_id'=>4, 'smth'=>5);
	 * $exporter->setTbody($rows);
	 *
	 * $exporter->export();
	 *
	 *
	 */

	class Exporter {

		const PDF = "pdf";
		const DEBUG = true;

		/**
		 * Base xml document
		 * @var DOMDocument
		 */
		private $xml;

		/**
		 * file output format
		 * @var string
		 */
		private $format;

		/**
		 * title of the document
		 * @var string
		 */
		public $title;

		/**
		 * company name (will be shown at header)
		 * @var string
		 */
		public $company;

		/**
		 * facility name (will be shown at header)
		 * @var string
		 */
		public $facility;

		/**
		 * department name (will be shown at header)
		 * @var string
		 */
		public $department;

		/**
		 * users info (will be shown at header)
		 * @var string
		 */
		public $user;

		/**
		 * report date (will be shown at header)
		 * @var string
		 */
		public $date;

		/**
		 * page url (will be shown at header)
		 * @var string
		 */
		public $url;

		/**
		 * search string. If report a search result
		 * @var string
		 */
		public $search_term;

		/**
		 * part of the filter
		 * @var string
		 */
		public $field;

		/**
		 * part of the filter
		 * @var string
		 */
		public $condition;

		/**
		 * part of the filter
		 * @var string
		 */
		public $value;

		/**
		 * Array of column widths
		 * @var array
		 */
		private $columnWidths = array();

		/**
		 * Array of column names (thead)
		 * @var array
		 */
		private $thead = array();

		/**
		 * header tag
		 * @var DOMElement
		 */
		private $theadTag;

		private $tbody = array();
		/**
		 * tbody tag
		 * @var DOMElement
		 */
		private $tbodyTag;

		/**
		 * map assosiative key to column's index
		 * @var array
		 */
		private $index2keyMap = array();

		/**
		 * Where any errors?
		 * @var bool
		 */
		private $error = false;


		public function __construct($format = self::PDF) {

			if (!$this->_setFormat($format)) {
				//	unknown format
				if (self::DEBUG) {
					echo "Unknown format: <i>".$format."</i><br>";
				}
				$this->error = true;
				return false;
			}

			$this->user = $_SESSION['username'];
			$this->date = date('M j, Y');
			$this->url = $_SERVER['HTTP_REFERER'];

			$doc = new DOMDocument();
			$doc->formatOutput = true;
			$this->xml = $doc;
			return true;
		}




		/**
		 * Add column name.
		 * @param array $data - column name
		 * $data = array('columnNameKey'=>'Nice column name that will be shown to user');
		 * columnNameKey - is a key. I'll use it when mapping data to columns
		 *
		 * Example:
		 * //	using array
		 * $header = array('foo'=>'Foo', 'bar'=>'Bar', 'column_3'=>'column 3 name');
		 * $exporter->addthead($header);
		 */
		public function setThead($data) {
			if (!is_array($data)) {
				//	only arrays
				if (self::DEBUG) {
					echo "Use only assosiative arrays at addthead(): <i>You are using ".gettype($data)."</i><br>";
				}
				$this->error = true;
				return false;
			}

			if (count($this->columnWidths) > 0) {
				if (count($this->columnWidths) != count($data)) {
					//	number od column widths doesn't equal number of column names
					if (self::DEBUG) {
						echo "Number of column widths doesn't equal number of column names at addthead(): <i>Column width count is ".count($this->columnWidths).", column name count is ".count($data)."</i><br>";
					}
					$this->error = true;
					return false;
				}
			}


			$this->theadTag = $this->xml->createElement('thead');
			$tr = $this->xml->createElement('tr');

			foreach ($data as $columnKey=>$columnName) {
				//	check mismatch with column widths
				if (count($this->columnWidths) > 0) {
					if (!array_key_exists($columnKey, $this->columnWidths)) {
						//	key mismatch
						if (self::DEBUG) {
							echo "Column name to width Key mismatch: <i>No such key at width '".$columnKey."'</i><br>";
						}
						$this->error = true;
						return false;
					}
				}

				//	save to array just in case
				$this->thead[$columnKey] = $columnName;

				//	save INDEX to KEY map
				$this->index2keyMap[] = $columnKey;

				//	save as DOM
				$td = $this->xml->createElement('td');
				$width = $this->xml->createAttribute("width");
				$width->appendChild( $this->xml->createTextNode( self::protectString($this->columnWidths[$columnKey])) );
				$td->appendChild($width);
				if (is_array($columnName)) {
					foreach ($columnName as $column) {
						if (!is_array($column)) {
							$tdUp = $this->xml->createAttribute("tdUp");
							$tdUp->appendChild( $this->xml->createTextNode( self::protectString($column)) );
							$td->appendChild($tdUp);
						} else {
							foreach ($column as $key => $tdDownColumn) {
								$tdDown[$key] = $this->xml->createAttribute("tdDown_" . $key);
								$tdDown[$key]->appendChild( $this->xml->createTextNode( self::protectString($tdDownColumn)) );
								$td->appendChild($tdDown[$key]);
							}
						}
						
					}
				}
				$td->appendChild( $this->xml->createTextNode( self::protectString($columnName) ) );
				$tr->appendChild( $td );
			}
			$this->theadTag->appendChild( $tr );
			return true;
		}



		/**
		 * Add row to table. Use the same keys that were used at HEADER.
		 * @param mixed row
		 *
		 * Example:
		 * $row = array('foo'=>'Foo', 'bar'=>'Bar', 'column_3'=>'data of column number 3');
		 * $exporter->addTableRow($row);
		 */
		public function addTableRow($row) {
			if (!is_array($row)) {
				if (is_object($row)) {
					$row = get_object_vars($row);
				} else {
					//	only arrays or objects
					if (self::DEBUG) {
						echo "Use only assosiative arrays or objects at addTableRow(): <i>You are using ".gettype($row)."</i><br>";
					}
					$this->error = true;
					return false;
				}
			}

			//	this var is equals to $row but filtered for fields that are in HEADER
			$tableData = array();
			foreach ($this->thead as $key=>$columnName) {
				if (array_key_exists($key, $row)) {
					$tableData[$key] = $row[$key];
				} else {
					//	key mismatch
					if (self::DEBUG) {
						echo "Column name to data Key mismatch: <i>No such key at tbody '".$key."'</i><br>";
					}
					$this->error = true;
					return false;
				}
			}

			//	if tbody tag doesn't created yet -> create it
			if (!isset($this->tbodyTag)) {
				$this->tbodyTag = $this->xml->createElement('tbody');
			}

			//	save as array just in case
			$this->tbody[] = $tableData;

			//	save as DOM
			$tr = $this->xml->createElement('tr');
			foreach ($this->index2keyMap as $index=>$key) {
				$td = $this->xml->createElement('td');
				$td->appendChild( $this->xml->createTextNode( self::protectString($tableData[$key])) );
				$tr->appendChild( $td );
			}
			$this->tbodyTag->appendChild( $tr );
			return true;
		}


		/**
		 * Set table's body. Use the same keys for table data that were used at HEADER. Key of row does't play any role.
		 * @param array $tbody
		 *
		 * Example:
		 * $rows[] = array('description'=>1,'mix_id'=>2);
		 * $rows[] = array('description'=>3,'mix_id'=>4);
		 * $exporter->setTbody($rows);
		 */
		public function setTbody($tbody) {
			if (!is_array($tbody)) {
				//	only arrays
				if (self::DEBUG) {
					echo "Use only assosiative arrays at setTbody(): <i>You are using ".gettype($tbody)."</i><br>";
				}
				$this->error = true;
				return false;
			}

			//	reset tbody
			$this->tbody = array();
			$this->tbodyTag = $this->xml->createElement('tbody');

			foreach ($tbody as $row) {
				$this->addTableRow($row);
			}

			return true;
		}


		public function setColumnsWidth($columnWidths) {
			if (!is_array($columnWidths)) {
				//	only arrays
				if (self::DEBUG) {
					echo "Use only assosiative arrays at setColumnWidths(): <i>You are using ".gettype($columnWidths)."</i><br>";
				}
				$this->error = true;
				return false;
			}

			if (array_sum($columnWidths) != 100) {
				//	not equal 100%
				if (self::DEBUG) {
					echo "Sum of column width's doesn't equals 100%. <i>Your sum is ".array_sum($columnWidths)."</i><br>";
				}
				$this->error = true;
				return false;
			}

			if ( count($this->thead) > 0 ) {
				//	I didn't code this yet =(
				if (self::DEBUG) {
					echo "Please, <br>1.) Set column widths;<br>2.) Set column names.<br>";
				}
				$this->error = true;
				return false;
			} else {
				//	table header doesn't set yet
				foreach ($columnWidths as $columnKey=>$columnWidth) {
					//	save to property array
					$this->columnWidths[$columnKey] = $columnWidth."%";
				}
			}

			return true;
		}


		/**
		 * generate XML and finalize export procedure - currently sends PDF to user
		 */
		public function export() {

			if ($this->error) {
				return false;
			}

			$fileName = trim(str_replace(" ","_", $this->title));
			$fileName = html_entity_decode($fileName,ENT_COMPAT, 'UTF-8');
			$filePath = "tmp/".$fileName.".xml";

			if ($this->_buildXML($filePath)) {

				$xml2pdfClassFileName = 'modules/xml2pdf/Export2pdf.php';
				define( "FPDF_FONTPATH", "modules/xml2pdf/font/" );
				echo "\n\n";
				require($xml2pdfClassFileName);

				$xml2pdf = new XML2PDF( FALSE );
				$xml2pdf->Open();

				$xml2pdf->Parse($filePath);

				if (isset($this->title)) {
					$xml2pdf->Output($fileName.'.pdf', 'I');
				} else {
					$xml2pdf->Output('vocwebmanagerReport.pdf', 'I');
				}
				echo "\n\n";
				return true;
			} else {
				//	I/O error
				if (self::DEBUG) {
					echo "Cannot save XML into path: <i>'".$filePath."'</i><br>";
				}
				$this->error = true;
				return false;
			}
		}



		/**
		 * smth like white list for formats
		 * @param string $format
		 */
		private function _setFormat($format) {
			if ($format == self::PDF) {
				$this->format = $format;
				return true;
			} else {
				$this->error = true;
				return false;
			}
		}


		private function _buildXML($filePath) {
			$doc = $this->xml;

			//	setup XML
			$page = $doc->createElement( "page" );
			$doc->appendChild( $page );

			$pageOrientation = $doc->createAttribute("orientation");
			$pageOrientation->appendChild( $doc->createTextNode("l") );
			$page->appendChild($pageOrientation);

			$pageTopMargin = $doc->createAttribute("topmargin");
			$pageTopMargin->appendChild( $doc->createTextNode("10") );
			$page->appendChild($pageTopMargin);

			$pageLeftMargin = $doc->createAttribute("leftmargin");
			$pageLeftMargin->appendChild( $doc->createTextNode("10") );
			$page->appendChild($pageLeftMargin);

			$pageRightMargin = $doc->createAttribute("rightmargin");
			$pageRightMargin->appendChild( $doc->createTextNode("20") );
			$page->appendChild($pageRightMargin);
			
			$meta = $doc->createElement( "meta" );
			$page->appendChild( $meta );

			$metaName = $doc->createAttribute("name");
			$metaName->appendChild( $doc->createTextNode("basefont") );
			$meta->appendChild($metaName);

			$metaValue = $doc->createAttribute("value");
			$metaValue->appendChild( $doc->createTextNode("times") );
			$meta->appendChild($metaValue);


			//	<HEADER>
			$header = $doc->createElement( "header" );
				// each header item is a property of this class, PLS carefully
				$headerItems = array('company', 'facility', 'department', 'user', 'date', 'url');
				foreach ($headerItems as $headerItem) {
					if (!property_exists($this, $headerItem)) {
						//	no such property
						if (self::DEBUG) {
							echo "Undefined property at Exporter Class:<i>'".$headerItem."'</i><br>";
						}
						$this->error = true;
						return false;
					}
					$item = $doc->createElement( $headerItem );
					$item->appendChild( $doc->createTextNode( self::protectString($this->$headerItem) ) );
					$header->appendChild( $item );
				}
			$page->appendChild( $header );
			//	</HEADER>

			//	<BODY>
			$body = $doc->createElement( "body" );

				//	title
				$title = $doc->createElement( "title" );
				$title->appendChild( $doc->createTextNode( self::protectString($this->title) ) );
				$body->appendChild( $title );

				//	data
				$table = $doc->createElement('table');
				$table->appendChild( $this->theadTag );
				$table->appendChild( $this->tbodyTag );
				$body->appendChild($table);

			//	</BODY>

			$page->appendChild($body);

			return (!$doc->save($filePath)) ? false : true;
		}

		/**
		 * Strip dangerous elements and trunctate
		 * @param $string - string to process
		 * @return protected string
		 */
		public static function protectString($string) {
			return trim(htmlentities($string, ENT_COMPAT, 'UTF-8', false));
		}
	}
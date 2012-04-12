<?php

	class Pagination {
								
		public $url;
		
		private $currentPage = 1;
		private $count = 0;
		private $pageSize = ROW_COUNT;
		private $limit = ROW_COUNT; 
		private $offset = 0;
		private $pageCount;
		private $rangeFirst = 1;
		private $rangeLast;
		private $pageVar = 'page';
		
		
		/**		 
		 * constructor
		 * @param integer $count total number of items
		 */
		public function Pagination($count = 0) {
			$this->count = $count;
			$this->pageCount = (int)ceil($this->count/$this->pageSize);
			
			$requestedPage = (isset($_REQUEST['page'])) ? $_REQUEST['page'] : false;
			$this->currentPage = ($requestedPage > $this->pageCount || $requestedPage < 1) ? $this->currentPage : $requestedPage;
			
			$offset = ROW_COUNT * ($this->currentPage - 1);
			$this->offset = ($offset < 0) ? $this->offset : $offset;

			$this->rangeFirst = ($this->currentPage - 10 < 1) ? 1 : $this->currentPage - 10;
			$this->rangeLast = ($this->currentPage + 10 > $this->pageCount) ? $this->pageCount : $this->currentPage + 9;
		}

		
		/**		 
		 * @return integer the limit of the data. This may be used to set the LIMIT value for a SQL statement for fetching the current page of data. 
		 * This returns the same value as pageSize
		 */		
		public function getLimit() {
			return $this->limit;			
		}
		
		/**		 
		 * @return integer the offset of the data. This may be used to set the OFFSET value for a SQL statement for fetching the current page of data.
		 */
		public function getOffset() {
			return $this->offset;			
		}
		
		/**		 
		 * @return integer the zero-based index of the current page. Defaults to 1.
		 */
		public function getCurrentPage() {
			return $this->currentPage;		
		}
		
		/**		 
		 * @return integer number of pages
		 */
		public function getPageCount() {
			return $this->pageCount;
		}
		
		public function getRangeFirstPage() {
			return $this->rangeFirst;
		}
		
		public function getRangeLastPage() {
			return $this->rangeLast;
		}
	}
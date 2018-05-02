<?php
namespace Faid\Response {
	use \Faid\Response as baseResponse;
	class HttpResponse extends Response {
		protected $content = '';
		protected $mimeType = '';
		/**
		 * Sets data to display
		 * @param $html
		 */
		public function setData( $html = '' ) {
			$this->content = $html;
			parent::setData( $html );
		}
		public function setMimeType( $type ) {
			$this->mimeType = $type;
		}
		public function getMimeType() {
			return $this->mimeType;
		}
		/**
		 * Sends file
		 * @param $fileName
		 * @param $mimeType
		 * @param $content
		 */
		public function sendFile( $fileName, $mimeType = null ) {
			// We'll be outputting a PDF
			$this->sendContentTypeHeader( $mimeType );
			// It will be called downloaded.pdf
			header('Content-Disposition: attachment; filename="'.$fileName.'"');
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".strlen($this->content));
			if ( ob_get_level() ) {
				ob_clean();
			}
			// The PDF source is in original.pdf
			print $this->content;
		}

		/**
		 * Sends redirect request
		 * @param $url
		 */
		public function redirect( $url ) {
			header('Location: '. $url );
		}
		public function getData( ) {
			return $this->content;
		}
		public function send(){
			parent::send();
			if ( ob_get_level() ) {
				ob_clean();
			}
			$this->sendHeaders();
			print $this->content;
		}
		protected function sendHeaders() {
			if ( !headers_sent( )) {
				if ( !empty( $this->mimeType )) {
					$this->sendContentTypeHeader( $this->mimeType );
				}
			}
		}
		protected function sendContentTypeHeader( $mimeType = null ) {
			if ( empty( $mimeType )) {
				$mimeType = $this->mimeType;
			}
			header('Content-type: '.$mimeType);
		}
	}
}
?>
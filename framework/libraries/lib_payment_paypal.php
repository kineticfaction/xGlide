<?php

	/**
	 * Payment Paypal
	 * @author Oliver Ridgway <oli@kineticfaction.co.uk>
	 * @license http://www.gnu.org/licenses/lgpl.html GNU LESSER GENERAL PUBLIC LICENSE
	 * @version 2.0 Coffee Creme
	 * 
	 */
	class payment_paypal extends payment {
		
		protected    $flo_amount = NULL;
		function  set_flo_amount ($value) {	$this->flo_amount = $value; }
		function  get_flo_amount () {	return $this->flo_amount; }
		
		protected    $str_description = NULL;
		function  set_str_description ($value) {	$this->str_description = $value; }
		function  get_str_description () {	return $this->str_description; }
		
		protected    $str_currency = NULL;
		function  set_str_currency ($value) {	$this->str_currency = $value; }
		function  get_str_currency () {	return $this->str_currency; }
		
		protected    $str_user = NULL;
		function  set_str_user ($value) {	$this->str_user = $value; }
		function  get_str_user () {	return $this->str_user; }
		
		protected    $str_pass = NULL;
		function  set_str_pass ($value) {	$this->str_pass = $value; }
		function  get_str_pass () {	return $this->str_pass; }
		
		protected    $str_sig = NULL;
		function  set_str_sig ($value) {	$this->str_sig = $value; }
		function  get_str_sig () {	return $this->str_sig; }
		
		protected    $str_returnurl = NULL;
		function  set_str_returnurl ($value) {	$this->str_returnurl = $value; }
		function  get_str_returnurl () {	return $this->str_returnurl; }
		
		protected    $str_cancelurl = NULL;
		function  set_str_cancelurl ($value) {	$this->str_cancelurl = $value; }
		function  get_str_cancelurl () {	return $this->str_cancelurl; }
		
		protected    $str_invoice = NULL;
		function  set_str_invoice ($value) {	$this->str_invoice = $value; }
		function  get_str_invoice () {	return $this->str_invoice; }
		
		protected    $str_method = NULL;
		function  set_str_method ($value) {	$this->str_method = $value; }
		function  get_str_method () {	return $this->str_method; }
		
		protected    $str_token = NULL;
		function  set_str_token ($value) {	$this->str_token = $value; }
		function  get_str_token () {	return $this->str_token; }
		
		protected    $str_payerid = NULL;
		function  set_str_payerid ($value) {	$this->str_payerid = $value; }
		function  get_str_payerid () {	return $this->str_payerid; }
		
		protected    $connection = NULL;
		function  set_connection ($value) {	$this->connection = $value; }
		function  get_connection () {	return $this->connection; }
		
		protected    $response = NULL;
		function  set_response ($value) {	$this->response = $value; }
		function  get_response () {	return $this->response; }
		
		
		final public function __construct(
			$flo_amount,
			$str_description,
			$str_currency,
			$str_user,
			$str_pass,
			$str_sig,
			$str_returnurl,
			$str_cancelurl,
			$str_invoice,
			$str_method,
			$str_token,
			$str_payerid
		) {

			parent::__construct();

			$this->flo_amount =		(float)$flo_amount;
			$this->str_description =	(string)$str_description;
			$this->str_currency =		(string)$str_currency;
			$this->str_user =		(string)$str_user;
			$this->str_pass =		(string)$str_pass;
			$this->str_sig =		(string)$str_sig;
			$this->str_returnurl =		(string)$str_returnurl;
			$this->str_cancelurl =		(string)$str_cancelurl;
			$this->str_invoice =		(string)$str_invoice;
			$this->str_method =		(string)$str_method;
			$this->str_token =		(string)$str_token;
			$this->str_payerid =		(string)$str_payerid;

		}
		
		
		
		final public function checkout_set() {

			$this->connection = fsockopen('ssl://api-3t.paypal.com', '443');
			//$this->connection = fsockopen('ssl://api-3t.sandbox.paypal.com', '443');

			fputs($this->connection, "POST /nvp HTTP/1.1\r\n");
			fputs($this->connection, "Host: api-3t.paypal.com\r\n");
			//fputs($this->connection, "Host: api-3t.sandbox.paypal.com\r\n");
			
			$array_post = array(
				'PAYMENTACTION'	. '=' .	urlencode('Sale'),
				'AMT'		. '=' .	urlencode($this->flo_amount),
				'RETURNURL'	. '=' . urlencode($this->str_returnurl),
				'CANCELURL'	. '=' . urlencode($this->str_cancelurl),
				'DESC'		. '=' . urlencode($this->str_description),
				'NOSHIPPING'	. '=' . urlencode('1'),
				'ALLOWNOTE'	. '=' . urlencode('1'),
				'CURRENCYCODE'	. '=' . urlencode($this->str_currency),
				'METHOD'	. '=' . urlencode($this->str_method),
				'CUSTOM'	. '=' . urlencode($this->flo_amount.'|'.$this->str_currency.'|'.$this->str_invoice),
				'INVNUM'	. '=' . urlencode($this->str_invoice),
				'USER'		. '=' . urlencode($this->str_user),
				'PWD'		. '=' . urlencode($this->str_pass),
				'SIGNATURE'	. '=' . urlencode($this->str_sig),
				'VERSION'	. '=' . urlencode('52.0')
			);
			
			fputs($this->connection, "Content-length: ".strlen(implode("&", $array_post))."\r\n");
			fputs($this->connection, "Connection: close\r\n");
			fputs($this->connection, "\r\n");
			fputs($this->connection, implode("&", $array_post));
			
			$responseHeader = '';
			$responseContent = '';

			do {
				$responseHeader.= fread($this->connection, 1);
			} while (!preg_match('/\\r\\n\\r\\n$/', $responseHeader));


			if (!strstr($responseHeader, "Transfer-Encoding: chunked")) {
				while (!feof($this->connection)) {
					$responseContent.= fgets($this->connection, 128);
				}
			} else {
				while ($chunk_length = hexdec(fgets($fp))) {
					$responseContentChunk = '';
					$read_length = 0;
					while ($read_length < $chunk_length) {
						$responseContentChunk .= fread($this->connection, $chunk_length - $read_length);
						$read_length = strlen($responseContentChunk);
					}
					$responseContent.= $responseContentChunk;
					fgets($this->connection);
				}
			}

			//print($responseHeader);
			//print($responseContent);
			
			parse_str($responseContent, $this->response);
		
		}

		final public function checkout_get() {

			$this->connection = fsockopen('ssl://api-3t.paypal.com', '443');
			//$this->connection = fsockopen('ssl://api-3t.sandbox.paypal.com', '443');

			fputs($this->connection, "POST /nvp HTTP/1.1\r\n");
			fputs($this->connection, "Host: api-3t.paypal.com\r\n");
			//fputs($this->connection, "Host: api-3t.sandbox.paypal.com\r\n");
			
			$array_post = array(
				'TOKEN'		. '=' .	urlencode($this->str_token),
				'METHOD'	. '=' . urlencode($this->str_method),
				'USER'		. '=' . urlencode($this->str_user),
				'PWD'		. '=' . urlencode($this->str_pass),
				'SIGNATURE'	. '=' . urlencode($this->str_sig),
				'VERSION'	. '=' . urlencode('52.0')
			);
			
			fputs($this->connection, "Content-length: ".strlen(implode("&", $array_post))."\r\n");
			fputs($this->connection, "Connection: close\r\n");
			fputs($this->connection, "\r\n");
			fputs($this->connection, implode("&", $array_post));
			
			$responseHeader = '';
			$responseContent = '';

			do {
				$responseHeader.= fread($this->connection, 1);
			} while (!preg_match('/\\r\\n\\r\\n$/', $responseHeader));


			if (!strstr($responseHeader, "Transfer-Encoding: chunked")) {
				while (!feof($this->connection)) {
					$responseContent.= fgets($this->connection, 128);
				}
			} else {
				while ($chunk_length = hexdec(fgets($fp))) {
					$responseContentChunk = '';
					$read_length = 0;
					while ($read_length < $chunk_length) {
						$responseContentChunk .= fread($this->connection, $chunk_length - $read_length);
						$read_length = strlen($responseContentChunk);
					}
					$responseContent.= $responseContentChunk;
					fgets($this->connection);
				}
			}

			//print($responseHeader);
			//print($responseContent);
			
			parse_str($responseContent, $this->response);
			
		}

		final public function checkout_do() {

			$this->connection = fsockopen('ssl://api-3t.paypal.com', '443');
			//$this->connection = fsockopen('ssl://api-3t.sandbox.paypal.com', '443');

			fputs($this->connection, "POST /nvp HTTP/1.1\r\n");
			fputs($this->connection, "Host: api-3t.paypal.com\r\n");
			//fputs($this->connection, "Host: api-3t.sandbox.paypal.com\r\n");
			
			$array_post = array(
				'TOKEN'					. '=' .	urlencode($this->str_token),
				'PAYERID'				. '=' .	urlencode($this->str_payerid),
				'METHOD'				. '=' . urlencode($this->str_method),
				'USER'					. '=' . urlencode($this->str_user),
				'PWD'					. '=' . urlencode($this->str_pass),
				'SIGNATURE'				. '=' . urlencode($this->str_sig),
				'VERSION'				. '=' . urlencode('76.0'),
				'PAYMENTREQUEST_0_AMT'			. '=' . urlencode($this->flo_amount),
				'PAYMENTREQUEST_0_CURRENCYCODE'		. '=' . urlencode($this->str_currency),
				'PAYMENTREQUEST_0_PAYMENTACTION'	. '=' . urlencode('Sale'),
			);
			
			fputs($this->connection, "Content-length: ".strlen(implode("&", $array_post))."\r\n");
			fputs($this->connection, "Connection: close\r\n");
			fputs($this->connection, "\r\n");
			fputs($this->connection, implode("&", $array_post));
			
			$responseHeader = '';
			$responseContent = '';

			do {
				$responseHeader.= fread($this->connection, 1);
			} while (!preg_match('/\\r\\n\\r\\n$/', $responseHeader));


			if (!strstr($responseHeader, "Transfer-Encoding: chunked")) {
				while (!feof($this->connection)) {
					$responseContent.= fgets($this->connection, 128);
				}
			} else {
				while ($chunk_length = hexdec(fgets($fp))) {
					$responseContentChunk = '';
					$read_length = 0;
					while ($read_length < $chunk_length) {
						$responseContentChunk .= fread($this->connection, $chunk_length - $read_length);
						$read_length = strlen($responseContentChunk);
					}
					$responseContent.= $responseContentChunk;
					fgets($this->connection);
				}
			}

			//print($responseHeader);
			//print($responseContent);
			
			parse_str($responseContent, $this->response);
						
		}
		
	}

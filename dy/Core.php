<?php
	/**
	 * The XMLRPC Libaries with which the backend was tested
	 * 
	 * @link http://dy.republik-dionysos.de/backendinfo.php
	 */
	require_once 'xmlrpc/xmlrpc.inc';
	require_once 'XMLRPCException.php';
	require_once 'simple_html_dom.php';
	
	error_reporting(E_ALL);
	ini_set('error_reporting',E_ALL);
	ini_set('display_errors', 1);
	/** 
	 * The Core class, defining some basic functionality
	 * which is needed by most of the sub-classes
	 *
	 * @package DionyAPI
	 * @author lazlow (aka. Ioannes Amadopolis) <lorenzo.van.matterhorn@gmx.de>
	 */
	
	class Core {
		/**
		 * Class Instance holding the XMLRPC Client
		 * 
		 * @var xmlrpc_client
		 */
		private static $xmlrpc = NULL;
		
		/**
		 * Simple Singleton funtion.
		 * 
		 * @link http://en.wikipedia.org/wiki/Singleton_pattern 
		 * @return xmlrpc_client Instance of the XMLRPC Client
		 */
		public static function getXMLRPCInstance() {
			if (self::$xmlrpc == NULL)
				self::$xmlrpc = new xmlrpc_client('backend/identitaeten.php', 'republik-dionysos.de', 80);
			return self::$xmlrpc;
		}
		/**
		 * Checks the XMLRPC-Result on the most common errors
		 * 
		 * @param xmlrpcresult $result
		 */		
		public static function checkXMLRPCResult($result) {
			if (!$result)
				throw new XMLRPCException('Result was null or false', 1);
			else if (!$result->value())
				throw new XMLRPCException('Result Value was null or false', 2);
			else
				return true;
		}
	}
?>
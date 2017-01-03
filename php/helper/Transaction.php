<?php
include_once ('Conection.php');
final class Transaction {
	private static $instance = null;
	/*
	 * Para nao criar varias instancias da classe transaction
	 */
	private function __construct() {
	}
	public static function open($database) {
		try {
			
			if (is_null ( self::$instance )) {
				self::$instance = Connection::open ( $database );
				
				session_start ();
			} else {
				self::$instance;
			}
		} catch ( Exception $e ) {
			throw new Exception ( $e->getMessage () );
		}
	}
	public static function getInstance() {
		try {
			if (! is_null ( self::$instance )) {
				return self::$instance;
			} else {
				throw new Exception ( 'Object PDO not found' );
			}
		} catch ( Exception $e ) {
			throw new Exception ( $e->getMessage () );
		}
	}
	public static function close() {
		self::$instance = null;
		session_destroy ();
	}
}
?>

<?php
abstract class Connection extends PDO {
	public static function open($database) {
		$filename = __DIR__ . '/' . sprintf ( '%s.ini', $database );
		if (file_exists ( $filename )) {
			$db = parse_ini_file ( $filename );
		} else {
			throw new Exception ( sprintf ( 'Arquivo "%s" nao encontrado !', __DIR__ . ' - ' . $database ) );
		}
		try {
			$type = isset ( $db ['type'] ) ? $db ['type'] : null;
			$host = isset ( $db ['host'] ) ? $db ['host'] : null;
			$port = isset ( $db ['port'] ) ? $db ['port'] : null;
			$user = isset ( $db ['user'] ) ? $db ['user'] : null;
			$pass = isset ( $db ['pass'] ) ? $db ['pass'] : null;
			$name = isset ( $db ['name'] ) ? $db ['name'] : null;
			
			switch ($type) {
				case 'pgsql' :
					$conn = new PDO ( sprintf ( 'pgsql:dbname=%s; user=%s; password=%s; host=%s; port=%s', $name, $user, $pass, $host, $port ) );
					break;
				case 'mysql' :
					$conn = new PDO ( sprintf ( 'mysql:host=%s; port=%s; dbname=%s', $host, $port, $name ), $user, $pass );
					break;
				case 'sqlite' :
					$conn = new PDO ( sprintf ( 'sqlite:%s', $name ) );
					break;
				case 'ibase' :
					$conn = new PDO ( sprintf ( 'firebird:dbname=%s', $name ), $user, $pass );
					break;
				case 'oci8' :
					$conn = new PDO ( sprintf ( 'oci:dbname=%s', $name ), $user, $pass );
					break;
				case 'mssql' :
					$conn = new PDO ( sprintf ( 'mssql:host=%s,1433; dbname=%s', $host, $name ), $user, $pass );
					break;
			}
			if ($conn instanceof PDO) {
				$conn->setAttribute ( PDO::ATTR_CASE, PDO::CASE_LOWER );
				$conn->setAttribute ( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );
				$conn->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
				$conn->setAttribute ( PDO::ATTR_AUTOCOMMIT, false );
				// $conn->setAttribute( PDO::ATTR_AUTOCOMMIT, true );
				$conn->setAttribute ( PDO::ATTR_TIMEOUT, 10 );
			}
		} catch ( Exception $e ) {
			throw new Exception ( $e->getMessage () );
		}
		return $conn;
	}
}
?>

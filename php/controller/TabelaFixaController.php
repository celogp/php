<?php
require_once ('../model/ModelTabelaFixa.php');
require_once ('Controller.php');
class TabelaFixaController extends Controller {
	public function __construct() {
		parent::__construct ( new ModelTabelaFixa () );
	}
	public function getSexo() {
		$record = $this->objModel->selectTabelaFixa ( 'TSEXO' );
		$success = true;
		$msg = "";
		echo $this->json->encode ( array (
				"success" => $success,
				"msg" => $msg,
				"rows" => $record 
		) );
	}
	public function getAtivo() {
		$record = $this->objModel->selectTabelaFixa ( 'TATIVO' );
		$success = true;
		$msg = "";
		echo $this->json->encode ( array (
				"success" => $success,
				"msg" => $msg,
				"rows" => $record 
		) );
	}
	public function getEstadoCivil() {
		$record = $this->objModel->selectTabelaFixa ( 'TESTADOCIVIL' );
		$success = true;
		$msg = "";
		echo $this->json->encode ( array (
				"success" => $success,
				"msg" => $msg,
				"rows" => $record 
		) );
	}
}
new TabelaFixaController ();
?>
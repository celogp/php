<?php
require_once ('Model.php');
class ModelTabelaFixa extends Model {
	public function __construct() {
		parent::__construct ( 'TABELAFIXA' );
	}
	public function selectTabelaFixa($tabela) {
		$sql = 'select t.chave, t.descricao, t.opcdefault from tabelafixa t where t.tabela = ' . "'" . $tabela . "'";
		$stmt = $this->conn->prepare ( $sql );
		$stmt->execute ();
		return ($stmt->fetchALL ());
	}
}
?>
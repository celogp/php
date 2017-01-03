<?php
require_once ('Model.php');
class ModelUsuario extends Model {
	private $idUsuario;
	private $loginUsuario;
	public function __construct() {
		parent::__construct ( 'TUSUARIO' );
		$this->idUsuario = - 1;
	}
	public function getRowsPk($records) {
		$qtdRow = 0;
		foreach ( $records as $record ) {
			$qtdRow += parent::getRowsPk ( $record );
		}
		return ($qtdRow);
	}
	public function getRowsFiltro($filtro) {
		return (parent::getRowsFiltro ( $filtro ));
	}
	public function selectRowsFiltro($filtro, $start, $limit) {
		return (parent::selectRowsFiltro ( $filtro, $start, $limit ));
	}
	public function insertRow($record) {
		parent::insertRow ( $record );
		$this->insertFullMenu ( $record->id );
		return ($record);
	}
	public function updateRow($record) {
		parent::updateRow ( $record );
		return ($record);
	}
	public function deleteRow($record) {
		$this->deleteFullMenu ( $record->id );
		parent::deleteRow ( $record );
		return ($record);
	}
	private function insertFullMenu() {
		$sql = 'insert into tmenuusuario (id_usuario, menu_id, submenu_id, podeacessar, podesalvar, podeincluir, podeexcluir) select ' . $this->idUsuario . ', i.menu_id, i.id, "N", "N", "N", "N" from tmenu i where not exists (select 1 from tmenuusuario m where m.id_usuario = ' . $this->idUsuario . ')';
		$stmt = $this->conn->prepare ( $sql );
		$stmt->execute ();
	}
	private function deleteFullMenu() {
		$sql = 'delete from tmenuusuario where id_usuario =  ' . $this->idUsuario;
		$stmt = $this->conn->prepare ( $sql );
		$stmt->execute ();
	}
	public function logar($loginUsuario, $loginSenha) {
		$sql = "SELECT MAX(ID) FROM TUSUARIO TUSUA WHERE TUSUA.LOGIN = '$loginUsuario' AND TUSUA.SENHA = '$loginSenha'";
		$stmt = $this->conn->prepare ( $sql );
		$stmt->execute ();
		$this->idUsuario = $stmt->fetchColumn ();
		$this->loginUsuario = $loginUsuario;
		if ($this->idUsuario > 0) {
			$_SESSION ['id'] = $this->idUsuario;
			$_SESSION ['loginUsuario'] = ucwords ( strtolower ( $this->loginUsuario ) );
			$_SESSION ['autenticado'] = true;
		}
		return (array (
				"idUsuario" => $this->idUsuario,
				"loginUsuario" => $this->loginUsuario 
		));
	}
	public function getMenuHome() {
		$sql = 'select distinct tmi.descrmenu as titulo, tmi.referenciaMenu as referencia from tmenu tmi where tabela is null order by tmi.menu_id';
		$stmt = $this->conn->prepare ( $sql );
		$stmt->execute ();
		return ($stmt->fetchALL ());
	}
	public function getMenuCadastros() {
		$sql = 'select tmi.id as id, tmi.titulosubmenu as titulo, tmi.referencia as referencia, tmi.tabela as tabela, 0 as qtdreg, tmi.descrsubmenu as submenu from tmenu tmi  where tmi.menu_id = 1 order by tmi.submenu_id, tmi.titulosubmenu';
		$stmt = $this->conn->prepare ( $sql );
		$stmt->execute ();
		$rows = $stmt->fetchALL ();
		$ind = 0;
		foreach ( $rows as $row ) {
			if ($row ['tabela'] != null) {
				$sql = 'select count(1) as qtdreq from ' . $row ['tabela'];
				$stmt = $this->conn->prepare ( $sql );
				$stmt->execute ();
				$rows [$ind] ['qtdreg'] = $stmt->fetchColumn ();
			}
			$ind += 1;
		}
		return ($rows);
	}
	public function getMenuRotinas() {
		$sql = 'select tmi.id as id, tmi.titulosubmenu as titulo, tmi.referencia as referencia, tmi.tabela as tabela, 0 as qtdreg, tmi.descrsubmenu as submenu from tmenu tmi where tmi.menu_id = 2  order by tmi.submenu_id, tmi.titulosubmenu';
		$stmt = $this->conn->prepare ( $sql );
		$stmt->execute ();
		$rows = $stmt->fetchALL ();
		$ind = 0;
		foreach ( $rows as $row ) {
			if ($row ['tabela'] != null) {
				$sql = 'select count(1) as qtdreq from ' . $row ['tabela'];
				$stmt = $this->conn->prepare ( $sql );
				$stmt->execute ();
				$rows [$ind] ['qtdreg'] = $stmt->fetchColumn ();
			}
			$ind += 1;
		}
		return ($rows);
	}
	public function getMenuConsultas() {
		$sql = 'select tmi.id as id, tmi.titulosubmenu as titulo, tmi.referencia as referencia, tmi.tabela as tabela, 0 as qtdreg, tmi.descrsubmenu as submenu from tmenu tmi  where tmi.menu_id = 3  order by tmi.submenu_id, tmi.titulosubmenu';
		$stmt = $this->conn->prepare ( $sql );
		$stmt->execute ();
		$rows = $stmt->fetchALL ();
		$ind = 0;
		foreach ( $rows as $row ) {
			if ($row ['tabela'] != null) {
				$sql = 'select count(1) as qtdreq from ' . $row ['tabela'];
				$stmt = $this->conn->prepare ( $sql );
				$stmt->execute ();
				$rows [$ind] ['qtdreg'] = $stmt->fetchColumn ();
			}
			$ind += 1;
		}
		return ($rows);
	}
}
?>
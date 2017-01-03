<?php
require_once ('../helper/Transaction.php');
class Model {
	private $tabela;
	private $AutoIncrement = 'N';
	private $columnPk;
	private $columnsPk;
	private $columns;
	private $podeIncluir;
	private $podeSalvar;
	private $podeExcluir;
	
	// variaveis utilizadas nas classes herdadas quando precisar sobrescrever algum metodo ou criar um adicional.
	protected $conn;
	protected $qtdRows;
	protected $row;
	public function __construct($tabela) {
		try {
			$this->podeIncluir = 'N';
			$this->podeSalvar = 'N';
			$this->podeExcluir = 'N';
			
			Transaction::open ( 'ServerCfg' );
			$this->conn = Transaction::getInstance ();
			$this->tabela = $tabela;
			$this->lerInfoTabela ();
			if (isset ( $_SESSION ['id'] )) {
				$this->setPermissaoTabela ( $_SESSION ['id'] );
			}
		} catch ( Exception $e ) {
			throw new Exception ( $e->getMessage () );
		}
	}
	private function lerInfoTabela() {
		$sql = "SHOW COLUMNS FROM $this->tabela";
		$this->stmt = $this->conn->prepare ( $sql );
		$this->stmt->execute ();
		$this->columns = $this->stmt->fetchall ( PDO::FETCH_ASSOC );
		foreach ( $this->columns as $column ) {
			if ($column ['key'] == 'PRI') {
				$this->columnsPk [$column ['field']] = $column ['field'];
				$this->columnPk = $column ['field'];
				if ($column ['extra'] == 'auto_increment') {
					$this->AutoIncrement = 'S';
				}
			}
		}
	}
	protected function getRowsPk($record) {
		$pk = $this->setWherePk ( $record );
		$sql = "SELECT count(1) FROM $this->tabela where $pk";
		$stmt = $this->conn->prepare ( $sql );
		$stmt->execute ();
		$this->qtdRows = $stmt->fetchColumn ();
		return $this->qtdRows;
	}
	protected function getRowsFiltro($filtro) {
		if (is_null ( $filtro )) {
			$sql = "SELECT count(1) FROM $this->tabela";
		} else {
			$sql = "SELECT count(1) FROM $this->tabela where $filtro";
		}
		// print_r( $sql );
		$stmt = $this->conn->prepare ( $sql );
		$stmt->execute ();
		$this->qtdRows = $stmt->fetchColumn ();
		return $this->qtdRows;
	}
	protected function selectRowsFiltro($filtro, $start, $limit) {
		if (is_null ( $filtro )) {
			$sql = "SELECT * FROM $this->tabela LIMIT $start,  $limit";
		} else {
			$sql = "SELECT * FROM $this->tabela where $filtro LIMIT $start,  $limit";
		}
		$stmt = $this->conn->prepare ( $sql );
		$stmt->execute ();
		$this->row = $stmt->fetchall ();
		return $this->row;
	}
	protected function insertRow($record) {
		$columnPk = $this->columnPk;
		$newPk = 0;
		$fields = '(';
		$values = '(';
		foreach ( $record as $key => $value ) {
			$fields .= $key . ', ';
		}
		$fields = substr ( $fields, 0, - 2 ) . ')';
		
		foreach ( $record as $key => $value ) {
			$valueInsert = (gettype ( $value ) == 'integer') ? $value : "'$value'";
			if (($this->AutoIncrement == 'N') && ($columnPk == $key)) {
				$newPk = $this->setNovoCodigo ( $valueInsert );
				$values .= $newPk . ', ';
			} else {
				$values .= $valueInsert . ', ';
			}
		}
		$values = substr ( $values, 0, - 2 ) . ')';
		$sql = "INSERT INTO $this->tabela $fields VALUES $values";
		$stmt = $this->conn->prepare ( $sql );
		// print_r ($sql);
		// quando usa auto incremento no banco mysql para pegar o ultimo da tabela
		// Sempre será uma coluna apenas de autoincremento.
		if ($this->AutoIncrement == 'S') {
			$newPk = $stmt->execute ();
			$newPk = $newPk ? $this->conn->lastInsertId () : $newPk;
		} else {
			$stmt->execute ();
		}
		$record->$columnPk = $newPk;
		$this->row = $record;
		return $this->row;
	}
	protected function updateRow($record) {
		$pk = $this->setWherePk ( $record );
		$values = '';
		foreach ( $record as $key => $value ) {
			if (! $this->fexisteNaPk ( $key )) {
				$valueUpdate = (gettype ( $value ) == 'integer') ? $value : "'$value'";
				$values .= "$key = $valueUpdate,";
			}
		}
		$values = substr ( $values, 0, - 1 );
		$sql = "UPDATE $this->tabela SET $values
					 WHERE $pk";
		$stmt = $this->conn->prepare ( $sql );
		$this->row = $stmt->execute ();
		$this->row = $record;
		// print_r ($sql);
		return $this->row;
	}
	
	/*
	 * Recebe um objeto do tipo Json
	 */
	protected function deleteRow($record) {
		$pk = $this->setWherePk ( $record );
		$sql = "DELETE FROM $this->tabela WHERE $pk";
		$stmt = $this->conn->prepare ( $sql );
		$this->row = $stmt->execute ();
		return $record;
	}
	private function setNovoCodigo($codigo) {
		$automatico = 'S';
		$ultimocodigo = 0;
		$ultimotabela = 0;
		$proximocodigo = $codigo;
		$sql = "select tnint.tnint_ultimo, tnint.tnint_automatico from tseqnroint tnint where tnint.tnint_tabela = '$this->tabela'";
		$this->stmt = $this->conn->prepare ( $sql );
		$this->stmt->execute ();
		$row = $this->stmt->fetchAll ();
		if ($this->stmt->rowcount () > 0) {
			$ultimocodigo = $row [0] ['tnint_ultimo'];
			$automatico = $row [0] ['tnint_automatico'];
			if ($automatico == 'S') {
				$sql = "select max($this->columnPk) from $this->tabela";
				$this->stmt = $this->conn->prepare ( $sql );
				$this->stmt->execute ();
				$proximocodigo = $ultimocodigo + 1;
				$ultimotabela = $this->stmt->fetchcolumn () + 1;
				if ($proximocodigo < $ultimotabela) {
					$proximocodigo = $ultimotabela;
				}
			}
			$sql = "update tseqnroint set tnint_ultimo = $proximocodigo where tnint_tabela = '$this->tabela'";
			$this->stmt = $this->conn->prepare ( $sql );
			$this->stmt->execute ();
		} else if ($this->stmt->rowcount () == 0) {
			if (is_null ( $proximocodigo ) or ($proximocodigo == 0)) {
				$sql = "select max($this->columnPk) from $this->tabela";
				$this->stmt = $this->conn->prepare ( $sql );
				$this->stmt->execute ();
				$ultimotabela = $this->stmt->fetchcolumn ();
				$proximocodigo = (( int ) $ultimotabela) + 1;
			}
			$sql = "insert into tseqnroint (tnint_tabela, tnint_ultimo, tnint_automatico) values ('$this->tabela', $proximocodigo, 'S')";
			$this->stmt = $this->conn->prepare ( $sql );
			$this->stmt->execute ();
		}
		return ($proximocodigo);
	}
	public function setStartTransaction() {
		$this->conn->beginTransaction ();
	}
	public function setCommitTransaction() {
		$this->conn->commit ();
	}
	public function setRollBackTransaction() {
		$this->conn->rollBack ();
	}
	/*
	 * private function setWherePk($records){
	 * $valuesPk = ''; $record;
	 * foreach ($records as $record) {
	 * foreach($this->columnsPk as $column) {
	 * if (isset($record->$column)) {
	 * $value = $record->$column;
	 * $valuesPk .= "( $column = $value ) and ";
	 * }
	 * }
	 * $valuesPk = substr($valuesPk, 0, -4);
	 * $valuesPk .= ' or ';
	 * }
	 *
	 * $valuesPk = substr($valuesPk, 0, -4);
	 * return ($valuesPk);
	 * }
	 */
	private function setWherePk($record) {
		$valuesPk = '';
		foreach ( $this->columnsPk as $column ) {
			if (isset ( $record->$column )) {
				$value = $record->$column;
				$valuesPk .= "( $column = $value ) and ";
			}
		}
		$valuesPk = substr ( $valuesPk, 0, - 4 );
		return ($valuesPk);
	}
	private function fexisteNaPk($column) {
		$existe = false;
		foreach ( $this->columnsPk as $pk ) {
			if ($column == $pk) {
				$existe = true;
				break;
			}
		}
		return ($existe);
	}
	
	/*
	 * Le os acessos do usuario logado na tabela
	 */
	public function setPermissaoTabela($codUsuario) {
		$sql = "select podeincluir, podesalvar, podeexcluir from vmenuusuario where id_usuario = $codUsuario and tabela = " . "'" . $this->tabela . "'";
		$this->stmt = $this->conn->prepare ( $sql );
		$this->stmt->execute ();
		$this->result = $this->stmt->fetchall ( PDO::FETCH_ASSOC );
		if (! empty ( $this->result )) {
			$this->podeIncluir = $this->result [0] ['podeincluir'];
			$this->podeSalvar = $this->result [0] ['podesalvar'];
			$this->podeExcluir = $this->result [0] ['podeexcluir'];
		}
	}
	public function getPodeIncluir() {
		return $this->podeIncluir;
	}
	public function getPodeSalvar() {
		return $this->podeSalvar;
	}
	public function getPodeExcluir() {
		return $this->podeExcluir;
	}
}
?>
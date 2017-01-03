<?php
/*
 * Todas as funções CRUD recebem um array de objetos do tipo json e repassa ao Model um elemento de cada vez.
 */
require_once ('../lib/JSON.php');
class Controller {
	private $start;
	private $limit;
	private $pagin;
	private $filtro;
	
	// variaveis utilizadas nas classes herdadas quando precisar sobrescrever algum metodo ou criar um adicional.
	protected $objModel;
	protected $records;
	protected $json;
	public function __construct($objModel) {
		$this->objModel = $objModel;
		$this->json = new Services_JSON ();
		if (isset ( $_REQUEST ['rows'] )) {
			$this->records = $this->json->decode ( $_REQUEST ['rows'] );
		}
		if (isset ( $_REQUEST ['start'] )) {
			$this->start = $_REQUEST ['start'];
			$this->limit = $_REQUEST ['limit'];
			$this->pagin = $_REQUEST ['page'];
		} else {
			$this->start = 0;
			$this->limit = 100;
			$this->pagin = 1;
		}
		if (isset ( $_REQUEST ['filtro'] )) {
			$this->filtro = $_REQUEST ['filtro'];
		}
		if (method_exists ( $this, $_REQUEST ['action'] )) {
			call_user_func ( array (
					$this,
					$_REQUEST ['action'] 
			) );
		}
	}
	public function read() {
		$success = true;
		$qtdRows = $this->objModel->getRowsFiltro ( $this->filtro );
		$records = $this->objModel->selectRowsFiltro ( $this->filtro, $this->start, $this->limit );
		echo $this->json->encode ( $records );
	}
	public function add() {
		$msg = 'Inclusão feita com sucesso !';
		$success = true;
		if ($this->objModel->getPodeIncluir () == 'S') {
			try {
				$this->objModel->setStartTransaction ();
				foreach ( $this->records as $record ) {
					$records = $this->objModel->insertRow ( $record );
				}
				$this->objModel->setCommitTransaction ();
			} catch ( Exception $e ) {
				$this->objModel->setRollBackTransaction ();
				$success = false;
				$msg = $e->getMessage ();
			}
		} else {
			$msg = 'Voce NAO tem permissao para fazer a inclusao !';
			$success = false;
		}
		echo $this->json->encode ( array (
				"rows" => $this->records,
				"success" => $success,
				"msg" => $msg 
		) );
	}
	public function save() {
		$msg = 'Alteracao feita com sucesso !';
		$success = true;
		if ($this->objModel->getPodeSalvar () == 'S') {
			if ($this->objModel->getRowsPk ( $this->records ) > 0) {
				try {
					$this->objModel->setStartTransaction ();
					foreach ( $this->records as $record ) {
						$records = $this->objModel->updateRow ( $record );
					}
					$this->objModel->setCommitTransaction ();
				} catch ( Exception $e ) {
					$this->objModel->setRollBackTransaction ();
					$success = false;
					$msg = $e->getMessage ();
				}
			} else {
				$msg = 'Atenção ! registro NÃO existe no banco de dados para fazer a alteração !';
				$success = false;
			}
		} else {
			$msg = 'Voce NAO tem permissao para fazer a alteracao !';
			$success = false;
		}
		
		echo $this->json->encode ( array (
				"rows" => $this->records,
				"success" => $success,
				"msg" => $msg 
		) );
	}
	public function destroy() {
		$msg = 'Exclusao feita com sucesso !';
		$success = true;
		if ($this->objModel->getPodeExcluir () == 'S') {
			if ($this->objModel->getRowsPk ( $this->records ) > 0) {
				try {
					$this->objModel->setStartTransaction ();
					foreach ( $this->records as $record ) {
						$records = $this->objModel->deleteRow ( $record );
					}
					$this->objModel->setCommitTransaction ();
				} catch ( Exception $e ) {
					$this->objModel->setRollBackTransaction ();
					$success = false;
					$msg = $e->getMessage ();
				}
			} else {
				$msg = 'Atenção ! registro NÃO existe no banco de dados para fazer a exclusão !';
				$success = false;
			}
		} else {
			$msg = 'Voce NAO tem permissao para fazer a exclusao !';
			$success = false;
		}
		echo $this->json->encode ( array (
				"rows" => $this->records,
				"success" => $success,
				"msg" => $msg 
		) );
	}
}
?>
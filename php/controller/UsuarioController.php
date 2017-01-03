<?php
require_once ('../model/ModelUsuario.php');
require_once ('Controller.php');
class UsuarioController extends Controller {
	public function __construct() {
		parent::__construct ( new ModelUsuario () );
	}
	public function login() {
		$record = $this->objModel->logar ( $this->records [0]->loginUsuario, $this->records [0]->loginSenha );
		$success = (($record ["idUsuario"] == 0) ? false : true);
		$msg = (($record ["idUsuario"] == 0) ? "Login nao autenticado !." : "Login autenticado !.");
		echo $this->json->encode ( array (
				"success" => $success,
				"msg" => $msg,
				"rows" => $record 
		) );
	}
	public function carregaMenuHome() {
		$record = $this->objModel->getMenuHome ();
		$success = true;
		$msg = "";
		echo $this->json->encode ( array (
				"success" => $success,
				"msg" => $msg,
				"rows" => $record 
		) );
	}
	public function carregaMenuCadastros() {
		$record = $this->objModel->getMenuCadastros ();
		$success = true;
		$msg = "";
		echo $this->json->encode ( array (
				"success" => $success,
				"msg" => $msg,
				"rows" => $record 
		) );
	}
	public function carregaMenuRotinas() {
		$record = $this->objModel->getMenuRotinas ();
		$success = true;
		$msg = "";
		echo $this->json->encode ( array (
				"success" => $success,
				"msg" => $msg,
				"rows" => $record 
		) );
	}
	public function carregaMenuConsultas() {
		$record = $this->objModel->getMenuConsultas ();
		$success = true;
		$msg = "";
		echo $this->json->encode ( array (
				"success" => $success,
				"msg" => $msg,
				"rows" => $record 
		) );
	}
}
new UsuarioController ();
?>
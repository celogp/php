$(document).ready(function () {
	
	$('#btnCrudSalvarUsuario').on('click', function(e) { crudSalvaUsuario(e); });
	$('#btnCrudExcluiUsu').on('click', function(e) { crudExcluiUsuario(e); });
	$('#btnCrudEditUsu').on('click', function(e) { crudEditarUsuario(e); });
	
	$('#pnlCrudDetalheUsu').on('click', function(e) { crudEditarUsuario(e); });
	
	$('#btnCrudPesqUsu').on('click', function(e) { crudPesqUsuario(e); });
	$('#btnCrudAddExprUsu').on('click', function(e) { crudAddExprUsuario(e); });
	$('#btnCrudExcExprUsu').on('click', function(e) { crudExcExprUsuario(e); });
	
	/* 
		Alimenta tabelas fixas
	*/
	CarregaTabelasFixasUsuario();
});

function crudAddExprUsuario(e) {
	AddExprFitro('#pbxCrudFiltroUsu', $('#txtCrudCpoPesquisaUsu').val(), $('#txtCrudCondPesquisaUsu').val(), $('#txtCrudPesquisaUsu').val() );
	doGetOpcLst('#select-teste');
	doGetOpcLst('#select-ativo');
return ($('#pbxCrudFiltroUsu').pillbox('itemCount'));
};

function crudExcExprUsuario(e){
	ExcExpressao('#pbxCrudFiltroUsu');
return true;
};

function crudPesqUsuario(e) {
	var urlCrudUsuario = "../../php/Controller/UsuarioController.php", 
		QtdFilUsu=$('#pbxCrudFiltroUsu').pillbox('itemCount'), 
		strFilUsu='';
		
	if (QtdFilUsu==0) {
		QtdFilUsu = AddExprFitro('#pbxCrudFiltroUsu', $('#txtCrudCpoPesquisaUsu').val(), $('#txtCrudCondPesquisaUsu').val(), $('#txtCrudPesquisaUsu').val() );
	}
	if (QtdFilUsu==0) {
		setMsg('Atenção ! é preciso definir um filtro para pesquisar.','warning');
	}else {
		strFilUsu = criaCondicaoFiltro($('#pbxCrudFiltroUsu').pillbox('items'));
		
		$('#tblCrudPesquisaUsu').bootstrapTable('showLoading');
		$.post(urlCrudUsuario, { action: "read", filtro : strFilUsu}
		).done(function( jsonData ) {
			var parCrudUsuarioDadosPesq = eval('(' + jsonData + ')');
			
			$('#tblCrudPesquisaUsu').bootstrapTable('destroy');
			$('#tblCrudPesquisaUsu').bootstrapTable({
				data : parCrudUsuarioDadosPesq,
				height: 300,
				pageList: [10],
				cache: false,
				striped: true,
				pagination: true,
				clickToSelect: true,
				search: false,
				showColumns: false,
				showRefresh: false,		
				columns: [{
							field: 'state',
							checkbox: true
						},
						{
							field: 'id',
							title: 'Codigo',
							sortable: true
						},{
							field: 'login',
							title: 'Login',
							sortable: true
						},{
							field: 'senha',
							title: 'Senha',
							sortable: true
						}
				]
			});
			$('#tblCrudPesquisaUsu').bootstrapTable('refresh');
			$('#tblCrudPesquisaUsu').bootstrapTable('hideLoading');
		});
	};
return false;
};

function crudSalvaUsuario(e) {
	var crudCodigoUsuario = $('#crudCodigoUsuario').val(),
	    crudLoginUsuario = $('#crudLoginUsuario').val(),
	    crudSenhaUsuario = $('#crudSenhaUsuario').val(),
	    urlCrudUsuario = '../../php/Controller/UsuarioController.php',
		parCrudUsuarioDados = JSON.stringify([ { id: crudCodigoUsuario, login: crudLoginUsuario, senha: crudSenhaUsuario} ]),
		parCrudUsuarioAction = (crudCodigoUsuario==0) ? 'add' : 'save';
	e.preventDefault();
	if (crudLoginUsuario != '') {
		$('#btnCrudSalvarUsuario').attr('disabled', 'disabled');
		$.post(urlCrudUsuario, {action:parCrudUsuarioAction, rows:parCrudUsuarioDados}
		).fail(function(jsonData,string) { 
			console.log ('erro de 404');
		}).done(function (jsonData){
			var jd = eval('(' + jsonData + ')')
			if(jd.success == true){
				setMsg(jd.msg, 'success',
					$('#btnCrudSalvarUsuario').removeAttr('disabled')
				);
				$('#crudCodigoUsuario').val(jd.rows[0].id);
			}else{
				setMsg(jd.msg, 'danger',
					$('#btnCrudSalvarUsuario').removeAttr('disabled')
				);
			};
		});		
	}
return false;
};

function crudExcluiUsuario(e) {
	var parCrudUsuarioDados=[], ind,
		objCrudUsuarioDados =  $('#tblCrudPesquisaUsu').bootstrapTable('getSelections'),
	    urlCrudUsuario = '../../php/Controller/UsuarioController.php',
		parCrudUsuarioAction = 'destroy';
	
	if (($('#pbxCrudFiltroUsu').pillbox('itemCount')>0) && 
	   ((objCrudUsuarioDados != null) && (objCrudUsuarioDados != undefined))) {

		for (ind in objCrudUsuarioDados) {
			parCrudUsuarioDados.push(objCrudUsuarioDados[ind]);
		};		
		parCrudUsuarioDados = JSON.stringify( parCrudUsuarioDados ) ;
		if (parCrudUsuarioDados != null && parCrudUsuarioDados !== undefined) {
			$('#btnCrudExcluiUsu').attr('disabled', 'disabled');
			$.post(urlCrudUsuario, {action:parCrudUsuarioAction, rows:parCrudUsuarioDados}
			).fail(function(jsonData,string) { 
				console.log ('erro de 404');
			}).done(function (jsonData){
				var jd = eval('(' + jsonData + ')')
				if(jd.success == true){
					setMsg(jd.msg, 'success',
						$('#btnCrudExcluiUsu').removeAttr('disabled')
					);
					$('#crudCodigoUsuario').val(jd.rows[0].id);
				}else{
					setMsg(jd.msg, 'danger',
						$('#btnCrudExcluiUsu').removeAttr('disabled')
					);
				};
			});		
		}
   }
return false;
};

function crudEditarUsuario(e) {
	var parCrudUsuarioDados = $('#tblCrudPesquisaUsu').bootstrapTable('getSelections')[0];
	e.preventDefault(); 
	if (($('#pbxCrudFiltroUsu').pillbox('itemCount')>0) && 
	   ((parCrudUsuarioDados != null) && (parCrudUsuarioDados != undefined))) {
		$('#crudCodigoUsuario').val(parCrudUsuarioDados["id"]);
		$('#crudLoginUsuario').val(parCrudUsuarioDados["login"]);
		$('#crudSenhaUsuario').val(parCrudUsuarioDados["senha"]);
	};
return false;
};

function CarregaTabelasFixasUsuario() {
	lerTabelaFixa('getSexo', '#select-beast');
	lerTabelaFixa('getAtivo', '#select-ativo');
	lerTabelaFixa('getEstadoCivil', '#estadocivil');
return false;
};

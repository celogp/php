function setMsg(msg, opc) {
		if (opc == 'success') {
			document.getElementById("msgSuccess").innerHTML = msg;
			$(".alert-success").fadeTo(0, 1);
			$(".alert-success").fadeTo(1000, 0).slideUp(1000, function(){
				$(this).hide(); 
			});
		} else if (opc == 'info') {
			document.getElementById("msgInfo").innerHTML = msg;
			$(".alert-info").fadeTo(0, 1);
			$(".alert-info").fadeTo(2000, 0).slideUp(1000, function(){
				$(this).hide(); 
			});
		} else if (opc == 'warning') {
			document.getElementById("msgWarning").innerHTML = msg;
			$(".alert-warning").fadeTo(0, 1);
			$(".alert-warning").fadeTo(3000, 0).slideUp(1000, function(){
				$(this).hide(); 
			});
		} else if (opc == 'danger') {
			document.getElementById("msgDanger").innerHTML = msg
			$(".alert-danger").fadeTo(0, 1);
			$(".alert-danger").fadeTo(3000, 0).slideUp(2000, function(){
				$(this).hide(); 
			});
		}else {
			alert(msg);
		}
	return false;
};

function AddExprFitro(pbxFiltro, txtCpoPesquisa, txtCondPesquisa, txtPesquisa) {
	var strFiltro = txtCpoPesquisa + ' ' + txtCondPesquisa + ' '+ txtPesquisa, 
		valFiltro = txtCpoPesquisa + ' ' + trocaCondicaoFiltro(txtCondPesquisa, txtPesquisa);
	if (strFiltro.length>2){	
		$(pbxFiltro).pillbox('addItems', -1, [{ text: strFiltro, value: valFiltro}]);
	};
return ($(pbxFiltro).pillbox('itemCount'));
};

function trocaCondicaoFiltro(condicao, conteudo){
	var strCondicao;
	switch (condicao) {
	  case 'Igual':
		strCondicao = '= ' + conteudo;
		break;
	  case 'Maior Igual':
		strCondicao = '>= ' + conteudo;
		break;
	  case 'Menor Igual':
		strCondicao = '<= ' + conteudo;
		break;
	  case 'Contendo':
		strCondicao = 'like "%' + conteudo + '%"';
		break;
	  case 'Sequencia':
		strCondicao = 'in (' + conteudo + ')';
		break;
	  default:
		strCondicao = '= ' + conteudo;
		break;
	};
	return strCondicao;
};

function ExcExpressao(pbxFiltro){
	$(pbxFiltro).pillbox('removeItems');
return true;
};

function criaCondicaoFiltro(objItens) {
	var strFilVal, x;
	for (x in objItens) {
		if (x==0) {
			strFilVal = objItens[x].value;
		}else{
			strFilVal += ' and ' + objItens[x].value;
		}
	};
return strFilVal;
};

function lerTabelaFixa(tabela, divResult) {
	var urlTabelas = '../../php/controller/TabelaFixaController.php',
		parTabelasAction = tabela;
		$.post(urlTabelas, {action:parTabelasAction}
		).fail(function(jsonData,string) { 
			console.log ('erro de 404');
		}).done(function (jsonData){
			var jd = eval('(' + jsonData + ')');
			if(jd.success == true){
				$(divResult).selectize({
					create: false,
					sortField: {
						field: 'title',
						direction: 'asc'
					},
					maxItems: 1,
					maxOptions: 10,
					valueField: 'chave',
					labelField: 'descricao',
					searchField: 'descricao',
					sortField: 'descricao',
					options: jd.rows,
					dropdownParent: 'body'
				});			
			}
		});
return true;
};
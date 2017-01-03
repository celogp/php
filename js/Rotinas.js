$(document).ready(function () {
	onMenuCadastros();
});

function onMenuCadastros(){
	var textHtml = "";
	var textAuxMeio = "<span class= badge>";
	var textAuxFim = "</span></a>";
	var urlCadastros = '../php/Controller/UsuarioController.php',
	parCadastrosAction = 'carregaMenuRotinas';
	
	$.post(urlCadastros, {action:parCadastrosAction}
	).fail(function(jsonData,string) { 
		$("#msgWarning")[0].innerHTML = string;
	}).done(function (jsonData){
		var jd = eval('(' + jsonData + ')');
		if(jd.success == true){
			var menu = jd.rows;
				$.each(menu,function(k,value){
					var textAux = "<a href=" + value.referencia + " class= list-group-item>" + value.titulo + " " +textAuxMeio + " " + value.qtdreg + " "+textAuxFim;
					textHtml += textAux;
				});
				$('#mntRotinas').append( textHtml );
		}
		else
		{
			$("#msgWarning")[0].innerHTML = jd.msg;
			$(".alert-warning").fadeTo(0, 1);
			$(".alert-warning").fadeTo(2000, 0).slideUp(1000, function(){
				$(this).hide(); 
			});			
		}
	});
	
	return true;
};

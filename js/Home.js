$(document).ready(function () {
	onMenuHome();
});

function onMenuHome(){
	var textHtml = "";
	var textAuxFim = "<span class= 'badge glyphicon glyphicon-list'> </span> </a>";
	var urlLogin = '../php/Controller/UsuarioController.php',
	parLoginAction = 'carregaMenuHome';
	
	$.post(urlLogin, {action:parLoginAction}
	).fail(function(jsonData,string) { 
		$("#msgWarning")[0].innerHTML = string;
	}).done(function (jsonData){
		var jd = eval('(' + jsonData + ')');
		if(jd.success == true){
			var menu = jd.rows;
				$.each(menu,function(k,value){
					var textAux = "<a href=" + value.referencia + " class= list-group-item>" + value.titulo + " " +textAuxFim;
					textHtml += textAux;
				});
				$('#mntHome').append( textHtml );
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

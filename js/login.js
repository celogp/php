$(document).ready(function () {
	$('#btnLogin').on('click', function() { onFazerLogin(); });
});

function onFazerLogin(){
	var usuarioLogin = $('#usuarioLogin').val(),
		usuarioSenha = $('#usuarioSenha').val(),
		urlLogin = 'php/Controller/UsuarioController.php',
		parLoginAction = 'login', 
		parLoginDados = JSON.stringify([ { loginUsuario: usuarioLogin, loginSenha: usuarioSenha } ])
	
	$.post(urlLogin, {action:parLoginAction, rows:parLoginDados}
	).fail(function(jsonData,string) { 
		$("#msgWarning")[0].innerHTML = string;
	}).done(function (jsonData){
		var jd = eval('(' + jsonData + ')');
		if(jd.success == true){
			location.href="http://localhost/home/view/home.html";
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
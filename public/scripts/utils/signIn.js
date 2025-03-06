function signIn(){
	let userValue = $('#inputUser').val() || null;
	let passwordValue = $('#inputPassword').val() || null;
	if(!userValue || !passwordValue){
		showReplyMessage(1, "Debe ingresar su usuario y contraseña para iniciar sesión", "Usuario y Contraseña requerido", null);
	}
	console.log("Todos los controles de parte del JS pasados");
	let response = sendPost('signIn', { user: userValue, password: passwordValue});
	console.log(response)
	if(response.result == 2){
		window.location.href = getSiteURL();
	}else {
		showReplyMessage(response.result, response.message, "Iniciar sesión", null);
	}
}

function keyPressSignIn(eventEnter, inputValue, size){
	if(eventEnter.keyCode == 13){
		if(eventEnter.srcElement.id == "inputUser")
			$('#inputPassword').focus();
		else if(eventEnter.srcElement.id == "inputPassword")
			$('#buttonConfirm').click();
	}else if(inputValue != null && inputValue.length == size) return false;
}

function getSiteURL(){
	let url = window.location.href;
	if(url.includes("localhost") || url.includes("intranet.gargano") )
		return '/ordernet/public/';
	else
		return '/';
}

function getBase64(file) {
	return new Promise((resolve, reject) => {
		const reader = new FileReader();
		reader.readAsDataURL(file);
		reader.onload = () => resolve(reader.result);
		reader.onerror = error => reject(error);
	});
}

//evento para ocultar y mostrar las opciones del menú
function showMenuClient(menuShow){
	if(menuShow == "CLIENTES"){
		if($('#listClientMenu').css('display') == 'block')
			$('#listClientMenu').css('display', 'none');
		else	
			$('#listClientMenu').css('display', 'block');
	}
}

function loadPrograssBar(){
    //console.log("loadPrograssBar")
    var width = 0;
    var progressId = setInterval(() => {
		if(width < 100){
			//console.log("loadPrograssBar setInterval")
			width++;
			document.getElementById("progressbarLine").style.width = width + "%";
		}else if(width == 100){
			width = 0;
		}
    }, 20);
    return progressId;
}

function stopPrograssBar(progressBarIdProcess){
	//console.log("stopPrograssBar");
	clearInterval(progressBarIdProcess);
}

function adjustMax(element) {
    let value = parseFloat($(element).val());
    let max = 99;
    let min = 1;
    if (value > max) {
        $(element).val(max);
    } else if (value < min) {
        $(element).val(min);
    }
}

function mostrarLoader(valor){
	if(valor){
		$('.loaderback').css('display', 'block')
		$('.loader').css('display', 'block')
	} else {
		$('.loaderback').css('display', 'none')
		$('.loader').css('display', 'none')
	}
}
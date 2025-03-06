let lastID = 0;
let textToSearch = null;

function loadTableListContent(){
	let response = sendPost("loadTableListContent", {lastID: lastID, textToSearch: textToSearch});
	if(response.result == 2){
		if(response.newLastID != lastID)
			lastID = response.newLastID;
		let list = response.listResult;
		for (var i = 0; i < list.length; i++) {
			$('#tbodyTable').append(newRowTable());
		}
	}
}

function newRowTable(){
	let row = "<tr>";
	row += "</tr>";
	return row;
}

function searchContentTable(){
	let textTemp = $('#inputToSearch').val() || null;
	if(textTemp && textTemp.length > 2){
		textToSearch = textTemp;
		lastID = 0;
		$('#tbodyTable').empty();
		loadTableListContent();
	}else if(textTemp.length == 0){
		textToSearch = null;
		lastID = 0;
		$('#tbodyTable').empty();
		loadTableListContent();
	}
}
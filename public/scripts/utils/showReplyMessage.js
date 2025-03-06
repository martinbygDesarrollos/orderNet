function showReplyMessage(typeColour, message, title, currentModal){
	// $('#modalButtonResponse').focus();
	$("#modalButtonResponse").off('click');
	if(currentModal)
		$('#' + currentModal).modal('hide');

	$('#modalColourResponse').removeClass('alert-success');
	$('#modalColourResponse').removeClass('alert-warning');
	$('#modalColourResponse').removeClass('alert-danger');

	if(typeColour == 0)
		$('#modalColourResponse').addClass('alert-danger');
	else if(typeColour == 2)
		$('#modalColourResponse').addClass('alert-success');
	else if(typeColour == 1)
		$('#modalColourResponse').addClass('alert-warning');

	$('#modalTitleResponse').html(title);
	$('#modalMessageResponse').html(message);

	$('#modalButtonResponse').click(function(){
		$('#modalResponse').modal('hide');
		if(currentModal && typeColour != 2)
			$('#' + currentModal).modal();
	});

	$("#modalResponse").modal();
	// $('#modalButtonResponse').focus();s
}

// $('#modalResponse').on('shown.bs.modal', function() {
// 	$('#modalButtonResponse').focus();
// })

function openLoadModal(animation){
	$('#progressBarRestoreFile').removeClass('loadProgressBar');
	if(animation)
		$('#progressBarRestoreFile').addClass('loadProgressBar');
	$('#modalLoad').modal({backdrop: 'static', keyboard: false});
}

function showMessageOkCancel(typeColour, message, title, currentModal){
	$("#modalOkButton").off('click');
	if(currentModal)
		$('#' + currentModal).modal('hide');
	$('#modalOkCancelColour').removeClass();
	if(typeColour == 0)
		$('#modalOkCancelColour').addClass('modal-header alert-danger');
	else if(typeColour == 2)
		$('#modalOkCancelColour').addClass('modal-header alert-success');
	else if(typeColour == 1)
		$('#modalOkCancelColour').addClass('modal-header alert-warning');
	$('#modalOkCancelTitle').html(title);
	$('#modalOkCancelResponse').html(message);
	$('#modalOkButton').click(function(){
		forceNewClient = true;
		$('#modalMessageOkCancel').modal('hide');
		if ($('#buttonModalNewClient').length) {
			$('#buttonModalNewClient').click();
			// console.log(forceNewClient);
		}// else {
		// 	console.error('Button with ID buttonModalNewClient not found');
		// }
	});
	$('#modalCancelButton').click(function(){
		$('#modalMessageOkCancel').modal('hide');
		if(currentModal && typeColour != 2)
			$('#' + currentModal).modal();
	});
	$("#modalMessageOkCancel").modal();
}
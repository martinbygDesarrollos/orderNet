// function getUserData(){
//     sendAsyncPost("getUserData", null)
//     .then((response)=>{
//         if (response.result == 2){
//             // $('#vistas' + idDebitoGLOBAL).removeClass("d-none")
//             // console.log(response.preferences)
//             // showCorrectsSwitches(response.preferences)
//             $('#mailUsuario').val(response.usuario.truemail)
//             // console.log(response.usuario)
//         } else {
//             showReplyMessage(response.result, response.message, "Notificaciones", null);
//             // $('#vistas' + idDebitoGLOBAL).removeClass("d-none")
//             // $('#vistas' + idDebitoGLOBAL).addClass("d-none")
//         }
//     })
//     .catch((error)=>{
//         console.log("catch :"+error);
//     })
// }

// function updateUserMail(){
//     let email = $('#mailUsuario').val();
//     sendAsyncPost("updateUserMail", {email: email})
//     .then((response)=>{
//         showReplyMessage(response.result, response.message, "Notificaciones", null);
//     })
//     .catch((error)=>{
//         console.log("catch :"+error);
//     })
// }

// function getDebitoInfo(idDebito){
//     sendAsyncPost("getDebitoInfo", {idDebito: idDebito})
//     .then((response)=>{
//         if (response.result == 2){
//             $('#otros' + idDebitoGLOBAL).removeClass("d-none")
//             $('#mailReceptorBPS').prop( "disabled", false );
//             $('#mailReceptorBPS').val(response.debito.email)
//             // console.log(response.debito)
//         } else {
//             $('#mailReceptorBPS').prop( "disabled", true );
//             showReplyMessage(response.result, response.message, "Notificaciones", null);
//         }
//     })
//     .catch((error)=>{
//         // $('#mailReceptorBPS').prop( "disabled", true );
//         console.log("catch :"+error);
//     })
// }

// function updateBPSMail(){
//     let mail = $('#mailReceptorBPS').val();
//     sendAsyncPost("updateMail", {idDebito: idDebitoGLOBAL, mail: mail})
//     .then((response)=>{
//         showReplyMessage(response.result, response.message, "Notificaciones", null);
//     })
//     .catch((error)=>{
//         console.log("catch :"+error);
//     })
// }

// function showCorrectsSwitches(preferences){
//     // Iterate over each attribute of the object
//     for (var key in preferences) {
//         if (preferences.hasOwnProperty(key)) {
//             if (preferences[key] == 2){
//                 // console.log("No mostrar");
//                 $('.' + key).removeClass("d-none")
//                 $('.' + key).addClass("d-none")
//             } else if (preferences[key] == 1) {
//                 // console.log("Mostrar y activar");
//                 $('.' + key).removeClass("d-none")
//                 $('#' + key + idDebitoGLOBAL).prop('checked', true)
//             } else {
//                 $('.' + key).removeClass("d-none")
//                 // console.log("Mostrar y desactivar");
//                 $('#' + key + idDebitoGLOBAL).prop('checked', false)
//             }
//             // console.log(key + ": " + preferences[key]);
//         }
//     }
// }

// function updateImportsVETERINARIANAN() {
//     mostrarLoader(true)
//     // poner loader
//     sendAsyncPost("updateImportsVETERINARIANAN", {idDebito: idDebitoGLOBAL})
//     .then((response)=>{
//         mostrarLoader(false)
//         console.log(response)
//         showReplyMessage(response.result, response.message, "Notificaciones", null);
//         // showReplyMessage(response.result, response.message, "Notificaciones", null);
        
//     })
//     .catch((error)=>{
//         mostrarLoader(false)
//         console.log("catch :"+error);
//     })
// }
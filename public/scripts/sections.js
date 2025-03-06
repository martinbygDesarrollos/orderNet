$('#btnNewInventario').on('click', function(){
    $('#inputSectionName').val('')
    $('#modalNewSection').modal('show')
})

// Make sure this event handler is bound after the DOM is fully loaded
$(document).ready(function() {
    $('#buttonModalNewSection').on('click', function(e){
        console.log("CREAR NUEVA SECCION")
        let nombre = $('#inputSectionName').val().trim() || null
        if(nombre) {
            newSection(nombre)
        }
    })
    $('#tableSections tbody tr td:first-child').on('click', function() {
        var id = $(this).parent('tr').attr('id');
        console.log('ID de la Seccion seleccionada:', id);
        window.location.href = getSiteURL() + `secciones/${id}`;
    });
})

function newSection(nombre){
    mostrarLoader(true)
    sendAsyncPost("newSection", { nombre: nombre })
        .then((response)=>{
            mostrarLoader(false)
            if (response.result == 2){
                window.location.href = getSiteURL() + "secciones";
            } else {
                console.log("error")
            }
        })
        .catch((error)=>{
            mostrarLoader(false)
            console.log("catch :"+error);
        })
}

function addRelationUserSection(sectionId, sectionName) {
    $('#relationSection').text(sectionName)//.attr('data-id', sectionId)
    $('#relationUsers').empty()

    mostrarLoader(true)
    sendAsyncPost("getAllNormalUsers", {})
        .then((response)=>{
            mostrarLoader(false)
            if (response.result == 2){
                console.log(response.usuarios)
                response.usuarios.forEach(user => {
                    $('#relationUsers').append(`<option value="${user.id}"> ${user.usuario} </option>`)
                });

                $('#buttonModalAddRelation').off('click').on('click', function(){
                    createNewRelationUserSection($('#relationUsers option:selected').val(), sectionId);
                })

                $('#modalAddRelUserSection').modal()
            } else {
                console.log("error")
            }
        })
        .catch((error)=>{
            mostrarLoader(false)
            console.log("catch :"+error);
        })
}
function createNewRelationUserSection(userId, sectionId){
    mostrarLoader(true)
    sendAsyncPost("addRelationUserSection", {userId: userId, sectionId: sectionId})
        .then((response)=>{
            mostrarLoader(false)
            if (response.result == 2){
                window.location.href = getSiteURL() + "secciones";
            } else {
                console.log("error")
            }
        })
        .catch((error)=>{
            mostrarLoader(false)
            console.log("catch :"+error);
        })
    console.log(`Usuario: ${userId}, Inventario: ${sectionId}`)
}
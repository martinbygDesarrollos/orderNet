$('#btnNewProvider').on('click', function(){
    $('#inputProviderName').val('')
    $('#inputProviderRUT').val('')
    $('#modalNewProvider').modal('show')
})

// Make sure this event handler is bound after the DOM is fully loaded
$(document).ready(function() {
    $('#buttonModalNewProvider').on('click', function(e){
        console.log("CREAR NUEVO PROVEEDOR")
        let nombre = $('#inputProviderName').val().trim() || null
        let RUT = $('#inputProviderRUT').val().trim() || null
        if(nombre && RUT) {
            newProvider(nombre, RUT)
        }
    })

    $('#tableProviders tbody tr td:first-child').on('click', function() {
        var id = $(this).parent('tr').attr('id');
        console.log('ID del proveedor seleccionado:', id);
        showModalEditProvider(id)
    });
})

function newProvider(nombre, RUT){
    mostrarLoader(true)
    sendAsyncPost("newProvider", { nombre: nombre, rut: RUT })
        .then((response)=>{
            mostrarLoader(false)
            if (response.result == 2){
                window.location.href = getSiteURL() + "proveedores";
            } else {
                console.log("error")
            }
        })
        .catch((error)=>{
            mostrarLoader(false)
            console.log("catch :"+error);
        })
}

function showModalEditProvider(id){
    sendAsyncPost("getProvider", { idProvider: id })
        .then((response)=>{
            mostrarLoader(false)
            if (response.result == 2){
                console.log(response)
                $('#inputEditProviderName').val(response.proveedor.nombre)
                $('#inputEditProviderRUT').val(response.proveedor.RUT)

                $('#buttonModalEditProvider').off().on('click', function(){
                    if($('#inputEditProviderName').val().trim() != "" && $('#inputEditProviderRUT').val().trim() != "") // Si los campos no estan vacios
                        editProvider($('#inputEditProviderName').val(), $('#inputEditProviderRUT').val(), id)
                })
                $('#modalEditProvider').modal()
                // window.location.href = getSiteURL() + "proveedores";
            } else {
                console.log("error")
            }
        })
        .catch((error)=>{
            // mostrarLoader(false)
            console.log("catch :"+error);
        })
}

function editProvider(nombre, rut, id){
    sendAsyncPost("editProvider", { nombre: nombre, rut: rut, idProvider: id })
        .then((response)=>{
            if (response.result == 2){
                window.location.href = getSiteURL() + "proveedores";
            } else {
                console.log("error")
            }
        })
        .catch((error)=>{
            // mostrarLoader(false)
            console.log("catch :"+error);
        })
}
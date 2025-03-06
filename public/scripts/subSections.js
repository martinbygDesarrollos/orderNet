$('#btnNewSubSection').on('click', function(){
    $('#inputSubSectionName').val('')
    $('#modalNewSubSection').modal('show')
})

// Make sure this event handler is bound after the DOM is fully loaded
$(document).ready(function() {
    $('#buttonModalNewSubSection').on('click', function(e){
        console.log("CREAR NUEVA SUBSECCION")
        let nombre = $('#inputSubSectionName').val().trim() || null
        if(nombre) {
            newSubSection(nombre)
        }
    })
    $('#tableSubSections tbody tr').on('click', function() {
        var id = $(this).attr('id');
        console.log('ID de la subseccion seleccionada:', id);
        window.location.href = getSiteURL() + `subsecciones/${id}`;
    });
})


function newSubSection(nombre){
    mostrarLoader(true)
    sendAsyncPost("newSubSection", { nombre: nombre })
        .then((response)=>{
            mostrarLoader(false)
            if (response.result == 2){
                window.location.href = getSiteURL() + "subsecciones";
            } else {
                console.log("error")
            }
        })
        .catch((error)=>{
            mostrarLoader(false)
            console.log("catch :"+error);
        })
}
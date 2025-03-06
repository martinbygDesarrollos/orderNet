$('#btnNewItem').on('click', function(){
    console.log('New ITEM');
    $('#inputItemCantidad').val('')
    openModalNewItem();
})

function openModalNewItem(){
    mostrarLoader(true)
    sendAsyncPost("getAllArticlesSimple", {})
        .then((response)=>{
            mostrarLoader(false)
            if (response.result == 2){
                console.log(response)
                $('#selectItemArticle').empty()
                response.articulos.forEach(articulo => {
                    option = `<option value="${articulo.id}"> ${articulo.detalle}  </option>`
                    $('#selectItemArticle').append(option)
                });
                $('#modalNewItem').modal('show')
            } else {
                console.log("error")
            }
        })
        .catch((error)=>{
            mostrarLoader(false)
            console.log("catch :"+error);
        })
}

// Make sure this event handler is bound after the DOM is fully loaded
$(document).ready(function() {
    $('#buttonModalNewItem').on('click', function(e){
        console.log("CREAR NUEVO ITEM")
        let articulo = $('#selectItemArticle option:selected').val() || null
        let cantidad = $('#inputItemCantidad').val().trim() || null
        
        if(cantidad && articulo) {
            newItem(articulo, cantidad)
        }
    })
})

function newItem(articulo, cantidad){
    subseccion = $('#tableItems').attr('subseccion')
    mostrarLoader(true)
    sendAsyncPost("newItem", { articulo: articulo, cantidad: cantidad, subseccion: subseccion})
        .then((response)=>{
            mostrarLoader(false)
            if (response.result == 2){
                window.location.href = getSiteURL() + `subsecciones/${subseccion}`;
            } else {
                console.log("error")
            }
        })
        .catch((error)=>{
            mostrarLoader(false)
            console.log("catch :"+error);
        })
}
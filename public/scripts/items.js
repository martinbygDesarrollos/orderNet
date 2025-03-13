let firstMoveDone = false;
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

    $('#btn_savePositions').on('click', function(){
        subseccion = $('#tableItems').attr('subseccion')
        console.log("guardar nuevas posiciones")
        var rowPositions = [];

        $('tbody tr').each(function(index) {
            // Get the row ID
            var rowId = $(this).attr('id');
            
            // Add to array as an object with id and position
            rowPositions.push({
                id: rowId,
                position: index + 1
            });
        });

        mostrarLoader(true)
        sendAsyncPost("newItemOrder", { positions: rowPositions})
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

        console.log(rowPositions)
    })

    // Move row up
    $('.btn_up').click(function() {
        var currentRow = $(this).closest('tr');
        var previousRow = currentRow.prev('tr');
        
        // Only move if there's a previous row
        if (previousRow.length) {
            currentRow.insertBefore(previousRow);
            $('#btn_savePositions').removeClass('hide').addClass('show')
        }
    });
    
    // Move row down
    $('.btn_down').click(function() {
        var currentRow = $(this).closest('tr');
        var nextRow = currentRow.next('tr');
        
        // Only move if there's a next row
        if (nextRow.length) {
            currentRow.insertAfter(nextRow);
            $('#btn_savePositions').removeClass('hide').addClass('show')
        }
    });
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
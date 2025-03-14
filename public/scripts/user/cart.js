$(document).ready(function() {
    
})

// function show_hideItems(element){
//     console.log($(element).closest('tr').attr('id'));

//     let $currentRow = $(element).closest('tr'); // Get the clicked provider-header row
//     let $nextRows = $currentRow.nextAll('tr'); // Get all next rows

//     $nextRows.each(function () {
//         if ($(this).hasClass('provider-header')) {
//             return false; // Stop the loop when encountering another provider-header
//         }

//         let $row = $(this);
//         if ($row.hasClass('hidden-with-animation')) {
//             // Force reflow before removing class for a smooth transition
//             $row[0].offsetHeight;
//             $row.removeClass('hidden-with-animation');
//         } else {
//             $row.addClass('hidden-with-animation');
//         }
//     });
// }

function show_hideItems(element) {
    console.log($(element).closest('tr').attr('id'));

    let $currentRow = $(element).closest('tr'); // Get the clicked provider-header row
    let $nextRows = $currentRow.nextAll('tr'); // Get all next rows
    
    // Check if the icon should be updated
    let $button = $(element);
    let isHidden = false;
    
    $nextRows.each(function() {
        if ($(this).hasClass('provider-header')) {
            return false; // Stop the loop when encountering another provider-header
        }
        
        let $row = $(this);
        if ($row.hasClass('hidden-completely') || $row.hasClass('hiding')) {
            isHidden = true;
        }
    });
    
    if (isHidden) {
        // Show rows
        $nextRows.each(function() {
            if ($(this).hasClass('provider-header')) {
                return false; // Stop the loop when encountering another provider-header
            }
            
            let $row = $(this);
            
            // First make display visible but still with 0 opacity
            if ($row.hasClass('hidden-completely')) {
                $row.removeClass('hidden-completely');
                $row.addClass('showing');
                
                // Force reflow for transition to work
                $row[0].offsetHeight;
            }
            
            // Then animate in
            $row.removeClass('hiding showing');
        });
        
        // Update button icon to "hide"
        $button.html('<i class="fas fa-eye-slash"></i>');
    } else {
        // Hide rows
        $nextRows.each(function() {
            if ($(this).hasClass('provider-header')) {
                return false; // Stop the loop when encountering another provider-header
            }
            
            let $row = $(this);
            $row.addClass('hiding');
            
            // After animation completes, apply display: none
            $row.one('transitionend', function() {
                $row.removeClass('hiding');
                $row.addClass('hidden-completely');
            });
        });
        
        // Update button icon to "show"
        $button.html('<i class="fas fa-eye"></i>');
    }
}

function export_order(element){
    id = $(element).closest('tr').attr('id')
    nombre = $(element).closest('tr').find('td div span').text();

    let $currentRow = $(element).closest('tr'); // Get the clicked provider-header row
    let $nextRows = $currentRow.nextAll('tr'); // Get all next rows
    let articles = []
    $nextRows.each(function() {
        if ($(this).hasClass('provider-header')) {
            return false; // Stop the loop when encountering another provider-header
        }
        let $row = $(this);
        // console.log($row.attr('id').split("_")[1])
        articles.push( {id: $row.attr('id').split("_")[1], nombre:  $row.find('td:nth(0)').text(), codigo:  $row.find('td:nth(1)').text(), cantidad:  $row.find('td:nth(2)').text()})
    });

    // console.log(id + " " + nombre);
    // console.log(articles)
    provider = {
        id : id,
        nombre : nombre
    }
    dataToSend = {
        provider: provider,
        articles: articles,
    };
    
    // console.log(dataToSend)
    mostrarLoader(true)
    sendAsyncPost("exportOrder" , dataToSend)
        .then((response)=>{
            // stopPrograssBar(progressBarIdProcess);
            mostrarLoader(false)
            console.log(response)
            // $('#progressbar').modal("hide");
            if ( response.result != 2 ){
                showReplyMessage(response.result, response.message, "Detalle de exportacion", null);
            }else if ( response.result == 2 ){
                // showReplyMessage(response.result, response.message, "Detalle de exportacion", null);
                // window.location.href = getSiteURL() + 'downloadExcel.php?n='+response.name;
                window.location.href = getSiteURL() + 'downloadExcel.php?n='+response.name + '&a=' + response.finalName ;
            }
        })
        .catch((error)=>{
            mostrarLoader(false)
            console.log("catch :"+error);
        })
}
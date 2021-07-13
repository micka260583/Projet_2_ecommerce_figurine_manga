jQuery(function() {
    //Clicks
    $('#confirmed-sm').click(() => {
        let result = prepare(setCommandes())
        rearrangeDisplay(result['confirmed'], result['prepare'], result['sent'])
    })
    $('#prepare-sm').click(() => {
        let result = prepare(setCommandes())
        rearrangeDisplay(result['prepare'], result['confirmed'], result['sent'])
    })
    $('#sent-sm').click(() => {
        let result = prepare(setCommandes())
        rearrangeDisplay(result['sent'], result['confirmed'], result['prepare'])
    })
    $('#confirmed-lg').click(() => {
        let result = prepare(setCommandes())
        rearrangeDisplay(result['confirmed'], result['prepare'], result['sent'])
    })
    $('#prepare-lg').click(() => {
        let result = prepare(setCommandes())
        rearrangeDisplay(result['prepare'], result['confirmed'], result['sent'])
    })
    $('#sent-lg').click(() => {
        let result = prepare(setCommandes())
        rearrangeDisplay(result['sent'], result['confirmed'], result['prepare'])
    })
})

//Get the data //
function setCommandes(){
    let commandes = []
    for (let i = 0; i < $('#orders').children().length; i++)
    {
        commandes.push([
            i,
            $('#id' + (i + 1)).html(),
            $('#date' + (i + 1)).html(),
            $('#status' + (i + 1)).html()
        ])
    }
    return commandes
}
// END //

//Sort data by order status //
function prepare(commandes) {
    let confirmedCommandes = []
    let preparingCommandes = []
    let sentCommandes = []
    commandes.forEach((index) => {
        index[3] == "En préparation" ? preparingCommandes.push(index) : false;
        index[3] == "Confirmée" ? confirmedCommandes.push(index) : false;
        index[3] == "Envoyée" ? sentCommandes.push(index) : false;
    })
    return {
        "confirmed": confirmedCommandes,
        "prepare": preparingCommandes, 
        "sent": sentCommandes,
    }
}

function rearrangeDisplay(firstArray, secondArray, thirdArray) {
    insertTr(firstArray)
    insertTr(secondArray)
    insertTr(thirdArray)
}

function insertTr(myArray) {
    for (let i = 0; i < myArray.length; i++) {
        let item = '#item'+(myArray[i][0]+1)
        $('#orders').append($(item))
    }
}
// END//


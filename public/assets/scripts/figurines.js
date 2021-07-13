//    Clicks    //
jQuery(function() {
    setLicenses()
    //Clicks figurines
    $('#figurinesIdASC').click(() => {
        orderFigurines(setFigurines().sort(sortByIdASC))
    })
    $('#figurinesIdDESC').click(() => {
        orderFigurines(setFigurines().sort(sortByIdDESC))
    })
    $('#figurinesNameASC').click(() => {
        orderFigurines(setFigurines().sort(sortByNameASC))
    })
    $('#figurinesNameDESC').click(() => {
        orderFigurines(setFigurines().sort(sortByNameDESC))
    })
    $('#figurinesPriceASC').click(() => {
        orderFigurines(setFigurines().sort(sortByPriceASC))
    })
    $('#figurinesPriceDESC').click(() => {
        orderFigurines(setFigurines().sort(sortByPriceDESC))
    })
    $('#figurinesLicenseASC').click(() => {
        orderFigurines(setFigurines().sort(sortByLicenseASC))
    })
    $('#figurinesLicenseDESC').click(() => {
        orderFigurines(setFigurines().sort(sortByLicenseDESC))
    })
    $('#figurinesMakerASC').click(() => {
        orderFigurines(setFigurines().sort(sortByMakerASC))
    })
    $('#figurinesMakerDESC').click(() => {
        orderFigurines(setFigurines().sort(sortByMakerDESC))
    })
    //Clicks licenses
    $('#licensesIdASC').click(() => {
        orderLicenses(setLicenses().sort(sortByIdASC))
    })
    $('#licensesIdDESC').click(() => {
        orderLicenses(setLicenses().sort(sortByIdDESC))
    })
    $('#licensesNameASC').click(() => {
        orderLicenses(setLicenses().sort(sortByNameASC))
    })
    $('#licensesNameDESC').click(() => {
        orderLicenses(setLicenses().sort(sortByNameDESC))
    })
    //Clicks makers
    $('#makersIdASC').click(() => {
        orderMakers(setMakers().sort(sortByIdASC))
    })
    $('#makersIdDESC').click(() => {
        orderMakers(setMakers().sort(sortByIdDESC))
    })
    $('#makersNameASC').click(() => {
        orderMakers(setMakers().sort(sortByNameASC))
    })
    $('#makersNameDESC').click(() => {
        orderMakers(setMakers().sort(sortByNameDESC))
    })
})
//    END     //

//     Get data     //
//Figurines
function setFigurines() {
    let figurines = [];
    let i = 0
    let n = 0
    while(figurines.length < $('#figurines').children().length) {
        if ($('#tr' + i).length) {
            figurines.push([
                n,
                $('#id' + i).html(),
                $('#name-sm' + i).html(),
                $('#name-lg' + i).html(),
                $('#price' + i).html(),
                $('#license' + i).html(),
                $('#maker' + i).html()
            ])
            n++
        }
        i++
    }
    return figurines
}
function orderFigurines(myArray) {
    let i = 0
    while (i < myArray.length) {
        if ($('#tr' + myArray[i][1]).length) {
            let item = '#tr'+myArray[i][1]
            $('#figurines').append($(item))
            i++
        }
    }
}
//Licenses
function setLicenses() {
    let licenses = []
    let i = 0
    let n = 0
    while (licenses.length < $('#licenses').children().length) {
        if ($('#trLis' + i).length) {
            licenses.push([
                n,
                $('#lisId' + i).html(),
                $('#lisName' + i).html()
            ])
            n++
        }
        i++
    }
    return licenses 
}
function orderLicenses(myArray) {
    let i = 0
    while (i < myArray.length) {
        if ($('#trLis' + myArray[i][1]).length) {
            let item = '#trLis'+myArray[i][1]
            $('#licenses').append($(item))
            i++
        }
    }
}
//Makers
function setMakers() {
    let makers = []
    let i = 0
    let n = 0
    while (makers.length < $('#makers').children().length) {
        if ($('#trMak' + i).length) {
            makers.push([
                n,
                $('#makId' + i).html(),
                $('#makName' + i).html()
            ])
            n++
        }
        i++
    }
    return makers 
}
function orderMakers(myArray) {
    let i = 0
    while (i < myArray.length) {
        if ($('#trMak' + myArray[i][1]).length) {
            let item = '#trMak'+myArray[i][1]
            $('#makers').append($(item))
            i++
        }
    }
}
//     END      //

//    Sorts     //
function sortByIdASC(a, b) {
    return parseInt(a[1]) - parseInt(b[1])
}
function sortByIdDESC(a,b ) {
    return parseInt(b[1]) - parseInt(a[1])
}
function sortByNameASC(a, b) {
    let nameA = a[2].toLowerCase()
    let nameB = b[2].toLowerCase()
    if(nameA === nameB) return 0; 
    return nameA > nameB ? 1 : -1;
}
function sortByNameDESC(a, b) {
        let nameA = a[2].toLowerCase()
        let nameB = b[2].toLowerCase()
        if(nameA === nameB) return 0; 
        return nameA < nameB ? 1 : -1;
}
function sortByPriceASC(a, b) {
    let priceA = parseFloat(a[4].replaceAll('€', ''))
    let priceB = parseFloat(b[4].replaceAll('€', ''))
    if(priceA === priceB) return 0;
    return priceA > priceB ? 1 : -1;
}
function sortByPriceDESC(a, b) {
    let priceA = parseFloat(a[4].replaceAll('€', ''))
    let priceB = parseFloat(b[4].replaceAll('€', ''))
    if(priceA === priceB) return 0;
    return priceA < priceB ? 1 : -1;
}
function sortByLicenseASC(a, b) {
    let nameA = a[5].toLowerCase()
    let nameB = b[5].toLowerCase()
    if(nameA === nameB) return 0; 
    return nameA > nameB ? 1 : -1;
}
function sortByLicenseDESC(a, b) {
    let nameA = a[5].toLowerCase()
    let nameB = b[5].toLowerCase()
    if(nameA === nameB) return 0; 
    return nameA < nameB ? 1 : -1;
}
function sortByMakerASC(a, b) {
    let nameA = a[6].toLowerCase()
    let nameB = b[6].toLowerCase()
    if(nameA === nameB) return 0; 
    return nameA > nameB ? 1 : -1;
}
function sortByMakerDESC(a, b) {
    let nameA = a[6].toLowerCase()
    let nameB = b[6].toLowerCase()
    if(nameA === nameB) return 0; 
    return nameA < nameB ? 1 : -1;
}
//   END   //
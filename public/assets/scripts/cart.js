jQuery(function() {
    getListOfFigurines().forEach((index) => {
        $("#form" + index).submit(function(event) {
            let currentQty = 0
            if ($('#qty-group' + index).children('select').children('option').is(':selected')) {
                currentQty = $('#qty-group' + index).children('select').val()
            }
            let currentId = $('#qty-group' + index).data('id')

            $(".form-group").removeClass("has-error");
            $(".help-block").remove();
            var formData = {
                id: currentId,
                qty: currentQty
            };

            $.ajax({
                type: "POST",
                url: "/cart/add",
                data: formData,
                dataType: "json",
                encode: true,
            }).done(function(data) {
                let newPrice = (currentQty * $('#card' + index).find('var').data('price')).toFixed(2)
                $('#card' + index).find('var').html(newPrice + ' €');
                $('dd').html(setTotalPrice(getListOfFigurines()) + " €")
                $('#addToCartAlert').toast('show')
                $('#totalFig').html(data['total'])
                $('#contentH5').html('Quantité de <strong>' + data['name'] + '</strong>: ' + currentQty)
            });
            event.preventDefault();
        });
    })
    getListOfFigurines().forEach((index) => {
        $('#card' + index).find('form[data-delete=true]').submit(() => {
            let currentId = $('#card' + index).find('input[type=submit]')[2].getAttribute('data-id')
            var formData = {
                id: currentId
            }
            $.ajax({
                type: "POST",
                url: "/cart/deleteFromCart",
                data: formData,
                dataType: "json",
                encode: true,
            }).done(function(data) {
                if (data) {
                    $('dd').html(setTotalPrice(getListOfFigurines()) + " €")
                    $('#removeFromCartAlert').toast('show')
                    $('#totalFig').html(data['total'])
                    $('#contentDeleteH5').html("<strong>" + data['name'] + "</strong>" + " retiré du panier.")
                }
            });
            $('#card' + index).remove()
        })
    })

    $('[data-close=true]').click(() => {
        $('#addToCartAlert').toast('hide')
        $('#removeFromCartAlert').toast('hide')
    })
});

function getListOfFigurines() {
    let i = 0
    let n = 0
    let myArr = []
    while (i < $('#cartContent').children().length) {
        if ($('tr').find('#form' + n).length) {
            myArr.push(n)
            i++
        }
        n++
    }
    return myArr
}

function setTotalPrice(myArray) {
    let tmpArray = []
    myArray.forEach((value) => {
        tmpArray.push([
            $('#qty-group' + value).children('select').val(),
            $('#card' + value).find('var').data('price')
        ])
    })
    let totalPrice = 0
    tmpArray.forEach((subArray) => {
        totalPrice += parseInt(subArray[0]) * subArray[1]
    })
    return totalPrice.toFixed(2)
}
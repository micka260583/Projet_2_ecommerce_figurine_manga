jQuery(function() {
    $('#figurineForm').submit((event) => {
        let currentId = $('#figurineForm').children().data('id')
        var formData = {
            id: currentId,
        };

        $.ajax({
            type: "POST",
            url: "/cart/addToCart",
            data: formData,
            dataType: "json",
            encode: true,
        }).done(function(data) {
            $('#addToCartAlert').toast('show')
            $('#totalFig').html(data['total'])
            if (data['error']) {
                $('#audio-fail').get(0).play()
                $('#contentH5').html(data['error'])
            } else {
                $('#audio-success').get(0).play()
                $('#contentH5').html('<strong>' + data['name'] + '</strong> ajouté au panier.')
            }
        });
        event.preventDefault();
    })

    getListOfFigurines().forEach((index) => {
        $('#form' + index).submit((event) => {
            let currentId = $('#form' + index).children().data('id')
            var formData = {
                id: currentId,
            };
            $.ajax({
                type: "POST",
                url: "/cart/addToCart",
                data: formData,
                dataType: "json",
                encode: true,
            }).done(function(data) {
                $('#addToCartAlert').toast('show')
                $('#totalFig').html(data['total'])
                $('#contentH5').html('<strong>' + data['name'] + '</strong> ajouté au panier.')
                if (data['error']) {
                    $('#audio-fail').get(0).play()
                    $('#contentH5').html(data['error'])
                } else {
                    $('#audio-success').get(0).play()
                    $('#contentH5').html('<strong>' + data['name'] + '</strong> ajouté au panier.')
                }
            });
            event.preventDefault();
        })
    })
    $('[data-close=true]').click(() => {
        $('#addToCartAlert').toast('hide')
    })
})

function getListOfFigurines() {
    let i = 0
    let n = 0
    let myArr = []
    while (i < $('#listFigurines').children().length) {
        if ($('#form' + n).length) {
            myArr.push(n)
            i++
        }
        n++
    }
    return myArr
}
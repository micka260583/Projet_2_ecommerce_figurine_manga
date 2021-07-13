jQuery(function() {
    //Add to cart
    getListOfFigurines().forEach((index, elem) => {
        $('#form' + index).submit(function(event) {
            let currentId = $('#form' + index).children().data('id')
            var formData = {
                id: currentId
            }
            $.ajax({
                type: "POST",
                url: "/cart/addToCart",
                data: formData,
                dataType: "json",
                encode: true,
            }).done(function(data) {
                if (data) {
                    $('#addToCartAlert').toast('show')
                    $('#totalFig').html(data['total'])
                    if (data['error']) {
                        $('#audio-fail').get(0).play()
                        $('#contentH5').html(data['error'])
                    } else {
                        $('#audio-success').get(0).play()
                        $('#contentH5').html('<strong>'+ data['name'] + '</strong> ajouté au panier.' )
                    }
                   
                }
            });
            event.preventDefault();
        })
    })
    //Toast
    $('[data-close=true]').click(() => {
        $('#addToCartAlert').toast('hide')
    })
    //Filtre
    $('[type=checkbox]').change(function() {
        hideAllFigurines()
        showSelectedFigurines()
    })
    $('[data-lis]').change(function() {
        disableMakerCheckbox()
        allUnchecked($('[data-lis]'), $('[data-mak]'))
    })
    $('[data-mak]').change(function() {
        disableLicenseCheckbox()
        allUnchecked($('[data-mak]'), $('[data-lis]'))
    })
})

function getListOfFigurines() {
    let i = 0
    let n = 0
    let myArr = []
    while (i < $('#figurines').children().length) {
        if ($('#figurines').find('#form' + n).length) {
            myArr.push(n)
            i++
        }
        n++
    }
    return myArr
}

function getSponsorship() {
    let sponsorship = []
    let sponsorshipJson = []
    $('#figurines').children().each(function() {
        let spon = [
            parseInt($(this).data('license-id')),
            parseInt($(this).data('maker-id'))
        ]
        if (
            !sponsorshipJson.includes(JSON.stringify(spon)) &&
            !$(this).hasClass('visually-hidden')
            ) {
            sponsorshipJson.push(JSON.stringify(spon))
            sponsorship.push(spon)
        }
    })
    return {sponsorship, sponsorshipJson}
}

function listsOfChecked() {
    let checkedLis = []
    let checkedMak = []
    $('[data-lis]').each(function() {
        if ($(this).is(':checked')) {
            checkedLis.push($(this).data('id'))
        }
    })
    $('[data-mak]').each(function() {
        if ($(this).is(':checked')) {
            checkedMak.push($(this).data('id'))
        }
    })
    return {checkedLis, checkedMak}
}

function hideAllFigurines() {
    $('#figurines').children().each(function() {
        $(this).addClass('visually-hidden')
    })
}

function showSelectedFigurines() {
    let checked = listsOfChecked()
    $('#figurines').children().each(function() {
        if (
            (
                checked.checkedLis.includes($(this).data('license-id')) ||
                checked.checkedLis.length == 0
            ) && (
                checked.checkedMak.includes($(this).data('maker-id')) ||
                checked.checkedMak.length == 0
            )
            ) {
            $(this).removeClass('visually-hidden')
        }
    })
}

function disableMakerCheckbox() {
    let spon = getSponsorship().sponsorship
    let makers = []
    spon.forEach(couple => {
        if (!makers.includes(couple[1])) {
            makers.push(couple[1])
        }
    })
    $('[data-mak]').each(function() {
        $(this).attr('disabled', true)
        if (!makers.includes($(this).data('id'))) {
            $(this).prop('checked', false)
        } else {
            $(this).removeAttr('disabled')
        }
    })
}

function disableLicenseCheckbox() {
    let spon = getSponsorship().sponsorship
    let licenses = []
    spon.forEach(couple => {
        if (!licenses.includes(couple[0])) {
            licenses.push(couple[0])
        }
    })
    $('[data-lis]').each(function() {
        $(this).attr('disabled', true)
        if (!licenses.includes($(this).data('id'))) {
            $(this).prop('checked', false)
        } else {
            $(this).removeAttr('disabled')
        }
    })
}

function allUnchecked(selector, otherSelector) {
    let checked = 0
    selector.each(function() {
        if ($(this).is(':checked')) {
            checked++
        }
    })
    if (!checked) {
        selector.each(function() {
            if ($(this).prop('disabled')) {
                $(this).prop('disabled', false)
            }
        })
        reenable(otherSelector)
    }
}

function reenable(selector) {
    selector.each(function() {
        if ($(this).prop('disabled')) {
            $(this).removeAttr('disabled')
        }
    })
}

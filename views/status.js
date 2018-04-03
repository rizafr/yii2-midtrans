function snapCheckStatus(orderId, onSuccess) {
    return $.getJSON('<?=$statusUri?>', {'orderId': orderId}, onSuccess || function (res) {
        if (res.status == 'ok') {
            window.location.reload();
        }
    })
}

$(document).on('click', '.action-midtrans-status', function () {
    var el = $(this);
    var orderId = el.attr('data-order-id');
    var oriText = el.html();
    el.attr('disabled', 'true').html("Mohon tunggu...");
    snapCheckStatus(orderId)
        .fail(function () {
            swal('','Terjadi kesalahan, silahkan ulangi lagi','error')
        })
        .always(function () {
            el.removeAttr('disabled').html(oriText)
        })
});
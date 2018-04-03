
var options = {
    onSuccess: function(result){
        console.log(result);
        snapCheckStatus(result.order_id);
    },
    onPending: function(result){
        snapCheckStatus(result.order_id);
    },
    onError: function(result){console.log('error');console.log(result);},
    onClose: function(){console.log('customer closed the popup without finishing the payment');}
};

function snapCheckStatus(orderId){
    $.getJSON('<?=$statusUri?>',{'orderId' : orderId}, function (res) {
        if(res.status == 'ok'){
            window.location.reload();
        }
    })
}

$(document).on('click','.action-midtrans-checkout', function () {
    swal({
        text: 'Masukan kata sandi akun anda untuk melanjutkan pembayaran',
        icon:'warning',
        content: {
            element: "input",
            attributes: {
                type: "password"
            }
        },
        buttons: {
            cancel: true,
            confirm: "Lanjutkan"
        }
    }).then(function(input){
        if(input != null && input != ""){
            if(typeof  snap == 'undefined'){
                swal('',"Terjadi kesalahan, silahkan refresh halaman ini", 'error');
                return;
            }
            snap.show();
            $.ajax({
                url: '<?=$tokenUri?>',
                type: 'post',
                dataType: 'json',
                data: {password: input},
                success: function (response) {
                    if (response.status == 'ok') {
                        snap.pay(response.token, options);
                    } else {
                        swal('',response.message,'error');
                        snap.hide();
                    }
                },
                error: function () {
                    snap.hide();
                    swal('',"Terjadi kesalahan internal, cobalah beberapa saat lagi.",'error');
                },
                complete: function () {

                }
            });
        }
    })
});
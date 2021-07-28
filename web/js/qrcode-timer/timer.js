function invoiceUpdate(e, totalSeconds) {
    prc = calcolaPerc(e.offset.totalSeconds,totalSeconds);
    // console.log('[countdown bar prc]',prc);

    checkInvoiceStatus();

    $('.timer-progress').attr('aria-valuenow', prc).css('width', prc+"%");
    if (prc > 90) {
        $('.timer-progress').addClass('bg-danger');
        $('.button-spinner').addClass('bg-danger');
        $('.progressbar-text').text(yiiOptions.progressbarText);
    }
}

function calcolaPerc(a,t) {
    perc = 100 - a/t*100;
    if (perc < 9) perc = 9;
    return perc.toFixed(0);
}
function invoiceExpired(){
    $.ajax({
        url: yiiOptions.invoiceExpiredUrl,
        type: "POST",
        data: {
            'id': yiiOptions.invoiceId,
        },
        dataType: "json",
        success:function(data){
            location.reload();
        },
        error: function(j){
            console.log('error');
        }
    });

}
// questa funzione fa il check dell'avvenuto o meno pagamento
function checkInvoiceStatus(){
    $.ajax({
        url: yiiOptions.checkInvoiceUrl,
        type: "POST",
        data: {
            'id': yiiOptions.invoiceId,
        },
        dataType: "json",
        success:function(data){
            // console.log('[countdown checkinvoice]',data.status);

            switch (data.status){
                case 'expired':
                    $('.order-details').hide();
                    $('.timer-progress').attr('aria-valuenow', prc).css('width', "100%");
                    $('.timer-progress').addClass('bg-danger');
                    $('.progressbar-text').text(yiiOptions.progressbarTextExpired);
                    $('#countdown-ticker').countdown('stop');
                    $('.contentQrcode').hide();
                    $('.button-spinner').hide();
                    $('#invoiceExpired').show();
                    break;


                case 'complete':
                    $('.order-details').hide();
                    $('.timer-progress').attr('aria-valuenow', prc).css('width', "100%");
                    $('.timer-progress').addClass('bg-success');
                    $('.progressbar-text').text(yiiOptions.progressbarTextComplete);
                    $('#countdown-ticker').countdown('stop');
                    $('.contentQrcode').hide();
                    $('.button-spinner').hide();
                    $('#invoicePaid').show();

                    break;
            }
        },
        error: function(j){
            console.log('error');
        }
    });
}

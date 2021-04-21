// generate invoice

var tokenInvoice = document.querySelector('#token-invoice');


tokenInvoice.addEventListener('click', function(){
    event.preventDefault();

    var amount_val = $('#keypad-output').text();
    if (amount_val == 0 || amount_val == '')
        return false;

    if (yiiOptions.tokenAuth == false){
      showError(yiiOptions.msgWalletError);
      return false;
    }


    $.ajax({
        url: yiiOptions.invoiceCreate,
        type: "POST",
        beforeSend: function() {
            $('.waiting_span').hide();
            $('.waiting_span').after(yiiOptions.spinner);
        },
        data:{
            'amount' : amount_val,
            'posId' : yiiOptions.posId,
        },
        dataType: "json",
        success:function(data){
            $('.button-spinner').remove();
            $('.waiting_span').show();

            if (data.error){
              showError(data.error);
              return false;
            }
        },
        error: function(j){
            console.log('Something was wrong!');
        }
    });
});

// start
show_keypad();

function show_keypad() {
    var keypad = `
        <div class="keypad-frame" id="keypad-frame">
            <div class="keypad-container">
              <div class="keypad-frame-output-container">
                <center>
                <div class="keypad-output-container">
                  <div class="rounded m-1 p-2 alert-info error-message">&zwnj;</div>

                  <div class='keypad-output-container-content'>
                    <p class="keypad-output" id="keypad-output">0</p>
                  </div>
                </div>
                </center>
              </div>

                <div class="keypad-number-container">
                    <table border=0>
                        <tr>
                            <td width="33%"><span onclick="keypadnum(1)">1</span></td>
                            <td width="33%"><span onclick="keypadnum(2)">2</span></td>
                            <td width="33%"><span onclick="keypadnum(3)">3</span></td>
                        </tr>
                        <tr>
                            <td><span onclick="keypadnum(4)">4</span></td>
                            <td><span onclick="keypadnum(5)">5</span></td>
                            <td><span onclick="keypadnum(6)">6</span></td>

                        </tr>
                        <tr>
                            <td><span onclick="keypadnum(7)">7</a></td>
                            <td><span onclick="keypadnum(8)">8</a></td>
                            <td><span onclick="keypadnum(9)">9</a></td>
                        </tr>
                        <tr>
                            <td><span onclick="keypadnum(\'.\')">.</span></td>
                            <td><span onclick="keypadnum(0)">0</span></td>
                            <td><span class="del" id="del" onclick="keypad_del()"><i class="fa fa-undo"></i></span></td>
                        </tr>
                    </table>
                    <center>

                        <button class="button circle block blue token" id="token" name='token' type='button'>
                          <i class="fa fa-coins"></i>&nbsp;<span class="waiting_span" id='token-invoice'>`+yiiOptions.confirm+`</span>
                        </button>

                    </center>
                </div>
            </div>
        </div>
    `;
    $('.keypad-get').append(keypad);
}

var countDecimals = function(value) {
    console.log('[keypad] countDecimals:',Math.floor(value),value);
    if (Math.floor(value) != value)
        return value.toString().split(".")[1].length || 0;
    return 0;
}

function showError(message){
    $('.error-message').html(message);
    setTimeout(function(){
        $('.error-message').html("&zwnj;"); //blank javascript char
    }, 10000);
}



function keypadnum(num) {
    console.log('[keypad] key pressed', num );
    event.stopPropagation();

    navigator.vibrate = navigator.vibrate || navigator.webkitVibrate || navigator.mozVibrate || navigator.msVibrate;
    if (navigator.vibrate) {
        navigator.vibrate(60);
    }

    var keypad_num_text = $('#keypad-output').text();

    if (keypad_num_text.length > 10)
      return false;


    if (isNaN(num)){
        if(!keypad_num_text.includes('.'))
            //$('#keypad-output').text(keypad_num_text+num);
            $('#keypad-output').append(num);
    }else{
        if (eval(keypad_num_text) == 0){
            if (keypad_num_text.includes('.')){
                $('#keypad-output').append(num);
            } else if (num != 0){
                $('#keypad-output').text(num);
            }
        } else {
            $('#keypad-output').append(num);
        }
    }
    if (countDecimals($('#keypad-output').text()) > yiiOptions.poaDecimals){
        showError(yiiOptions.msgDecimalError);
        $('#keypad-output').text(keypad_num_text);
    }

    console.log('[keypad] text result', $('#keypad-output').text() );
}

function keypad_del() {
    event.preventDefault();
    var keypad_output_val = $('#keypad-output').text();
    var keypad_output_val_deleted = keypad_output_val.slice(0, -1);
    if (keypad_output_val_deleted == '')
        keypad_output_val_deleted = 0;
    $('#keypad-output').text(keypad_output_val_deleted);
    showError("&zwnj;"); //blank javascript char
}

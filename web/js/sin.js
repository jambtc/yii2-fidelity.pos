var sin_address;
readAllData('sin')
.then(function(data) {
	console.log('Sin',data);
	if (typeof data[0] !== 'undefined') {
		if (null !== data[0].id){
			sin_address = data[0].id;
			console.log('Sin recuperato: ',sin_address);
			$('#loginform-sin').val(sin_address);
		}
	}else{
		console.log('sin non trovato!');
	}
})

// intercetta il pulsante logout se esiste
if ($('.logout').length){
    var logoutButton = document.querySelector('.logout');
    logoutButton.addEventListener('click', function(){
        var post = {
            id	:  yiiGlobalOptions.userSin, //sin
        };
        writeData('sin', post)
        .then(function() {
            console.log('SIN saved in indexedDB', post);
        })
    });
}

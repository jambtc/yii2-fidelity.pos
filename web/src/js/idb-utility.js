var dbPromise = idb.open('fidelity-pos', 1, function(db) {

	if (!db.objectStoreNames.contains('sin')) {
	 	db.createObjectStore('sin', {keyPath: 'id'});
	}

});


// datas with same id will be overwritten, otherwise
// wil be added
function writeData(table, data) {
	console.log('[IndexedDb storing datas]', table, data);
	return dbPromise
		.then(function(db) {
			var tx = db.transaction(table, 'readwrite');
			var store = tx.objectStore(table);
			store.put(data);
			return tx.complete;
		});
}

function readAllData(table) {
	console.log("[IndexedDb read table]", table);
	return dbPromise
		.then(function(db) {
			var tx = db.transaction(table, 'readonly');
			var store = tx.objectStore(table);
			return store.getAll();
		});
}

function readFromId(table,id) {
	console.log("[IndexedDb read table from id]", table, id);
	return dbPromise
		.then(function(db) {
			var tx = db.transaction(table, 'readonly');
			var store = tx.objectStore(table);
			return store.getAll(id);
		});
}

function clearAllData(table) {
	console.log("[IndexedDb delete table]", table);
  return dbPromise
    .then(function(db) {
      var tx = db.transaction(table, 'readwrite');
      var store = tx.objectStore(table);
      store.clear();
      return tx.complete;
    });
}


function deleteItemFromData(table, id){
	return dbPromise
		.then(function(db){
			var tx = db.transactions(table, 'readwrite');
			var store = tx.objectStore(table);
			store.delete(id);
			return tx.complete;
		})
		.then(function(){
			console.log('Item deleted');
		});
}

function urlBase64ToUint8Array(base64String) {
  var padding = '='.repeat((4 - base64String.length % 4) % 4);
  var base64 = (base64String + padding)
    .replace(/\-/g, '+')
    .replace(/_/g, '/');

  var rawData = window.atob(base64);
  var outputArray = new Uint8Array(rawData.length);

  for (var i = 0; i < rawData.length; ++i) {
    outputArray[i] = rawData.charCodeAt(i);
  }
  return outputArray;
}

function dataURItoBlob(dataURI) {
  var byteString = atob(dataURI.split(',')[1]);
  var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0]
  var ab = new ArrayBuffer(byteString.length);
  var ia = new Uint8Array(ab);
  for (var i = 0; i < byteString.length; i++) {
    ia[i] = byteString.charCodeAt(i);
  }
  var blob = new Blob([ab], {type: mimeString});
  return blob;
}

// Generate random entropy for the seed based on crypto.getRandomValues.
function generateEntropy(length) {
	var charset = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
	var i;
	var result = "";

	values = new Uint32Array(length);
	window.crypto.getRandomValues(values);
	for(var i = 0; i < length; i++)
	{
		result += charset[values[i] % charset.length];
	}
	return result;
}

function WordCount(str) {
  return str.split(" ").length;
}


function displayPushNotification(options){
	if ('serviceWorker' in navigator) {
		navigator.serviceWorker.ready
			.then(function(swreg) {
				swreg.showNotification(options.title, options);
			});

	}
}

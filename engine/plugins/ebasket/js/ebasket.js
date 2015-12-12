function rpcBasketRequest(method, params) {

 var linkTX = new sack();
 linkTX.requestFile = '/engine/rpc.php';
 linkTX.setVar('json', '1');
 linkTX.setVar('methodName', method);
 linkTX.setVar('params', json_encode(params));
 linkTX.method='POST';
 linkTX.onComplete = function() {
	linkTX.onHide();
	if (linkTX.responseStatus[0] == 200) {
		var resTX;
        try {
  	 		resTX = eval('('+linkTX.response+')');
  		} catch (err) { alert('{l_fmsg.save.json_parse_error} '+linkTX.response); }

  		// First - check error state
  		if (!resTX['status']) {
  			// ERROR. Display it
  			alert('Error ('+resTX['errorCode']+'): '+resTX['errorText']);
  		} else {
            //$('body').css('overflow', 'hidden');
            document.getElementById('tinyBask').innerHTML = resTX['update'];
  		}
  	} else {
  		alert('{l_fmsg.save.httperror} '+linkTX.responseStatus[0]);
	}
 }
 linkTX.onShow();
 linkTX.runAJAX();
}


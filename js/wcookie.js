	function wcookie_set_wCookie(){

		console.log('wcookies set');
		var expiryDate = new Date();
		expiryDate.setMonth(expiryDate.getMonth() + 1);
		document.cookie = 'jsp-wCookie=true; expires=' + expiryDate.toGMTString();

		jQuery(".cookie_container").remove();

	}



	 jQuery(function(){


	   jQuery(".cookie_container").hide();



	    setTimeout(function(){

	        jQuery('.cookie_container').slideDown();

	    },1000);



	});
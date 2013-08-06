(function($){
	var mets = {
		"init":function(opt){

		},
		"typeahead":function(opt,callback){
			return this.each(function(){
				var synclock=false,timer=null;
				$(this).keyup(function(){
					if(timer){
						clearTimeout(timer);
					}
					var len=$(this).val().length;
					if(len>2&&len<20){
						var args={'text':$(this).val()};
						if(synclock==false){
							timer = setTimeout(function() {
								synclock=true;
                				var jqxhr = $.getJSON("http://54.217.222.192/v0/tags?callback=?", args,
								function(data){
									if(typeof callback=='function' && data!=null){
										callback.call(this,data);
									}
									synclock=false;
								});
								if(jqxhr.error){
									jqxhr.error(function(){synclock=false;});
								}
            				}, 200);

						}
					}
				});
			});
		}
	};
	$.fn.shash = function(m) {
		if ( mets[m] ) {
			return mets[m].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof m === 'object' || ! m ) {
			return mets.init.apply(this,arguments);
		} else {
			$.error('Method '+m+' does not exist on jQuery.shash' );
		}
	};
})(jQuery);

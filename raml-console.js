(function() {
    tinymce.create("tinymce.plugins.raml_console_button_plugin", {

        //url argument holds the absolute url of our plugin directory
        init : function(ed, url) {

            //add new button
            ed.addButton("raml_console", {
                title : "RAML Console",
                image : url + "/img/mulesoft-icon.png",
                icon: false,
	              onclick: function() {
                  ed.windowManager.open({
                    title: 'Insert Root RAML URL',
        						body: [{
        							type: 'textbox',
        							name: 'link',
        							label: 'Root RAML URL'
        						}],
        						onsubmit: function( e ) {
                      if (e.data.link == ''){
                        ed.insertContent( '[raml-console]' );
                      } else {
                        ed.insertContent( '[raml-console file="' + e.data.link + '"]' );
                      }
        						}
                  });
                }
            });
        },

        createControl : function(n, cm) {
            return null;
        },

        getInfo : function() {
            return {
                longname : "RAML Console",
                author : "Dejim Juang",
                version : "1.0"
            };
        }
    });

    tinymce.PluginManager.add("raml_console_button_plugin", tinymce.plugins.raml_console_button_plugin);
})();

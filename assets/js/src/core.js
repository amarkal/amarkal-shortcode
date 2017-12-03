var $ = window.jQuery;

exports.Shortcode = {
    init: function() {
        // Parse the list of shortcodes the was printed by PHP in JSON
        var shortcodes = JSON.parse($("#amarkal-shortcode-json").html());
        tinymce.create('tinymce.plugins.amarkal_shortcode', {
            init: function (ed, url) {
                for (var id in shortcodes) {
                    var shortcode = new Shortcode(id, shortcodes[id], ed);
                    shortcode.init();
                }
            }
        });

        // Register this plugin
        tinymce.PluginManager.add('amarkal_shortcode', tinymce.plugins.amarkal_shortcode);
    }
};

$(document).ready(function(){
    Amarkal.Shortcode.init();
});
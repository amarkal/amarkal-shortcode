/**
 * A utility class for the editor popup
 */
function Popup(shortcode) {
    this.shortcode = shortcode;
}

/**
 * Initiate the form and set the values. Called when the popup is opened.
 * 
 * @param {array} values The list of values to use for the fields after the popup loads
 */
Popup.prototype.onOpen = function (values) {
    var $form = $('#' + this.shortcode.id + '.mce-window').find('#amarkal-shortcode-form'),
        $components = $form.find('.amarkal-ui-component');
    
    $form.amarkalUIForm('setData', values);
    
    // Hide invisible components initially
    $components.each(function(){
        if(!$form.amarkalUIForm('isVisible', $(this).amarkalUIComponent('getName'))) {
            $(this).parents('.amarkal-shortcode-field').hide();
        }
    });

    // Show hide component wrappers
    $components.on('amarkal.show', function(){
        $(this).parents('.amarkal-shortcode-field').show();
        $(this).amarkalUIComponent('refresh');
    }).on('amarkal.hide', function(){
        $(this).parents('.amarkal-shortcode-field').hide();
    });
};

/**
 * Called when the "Close" button is clicked
 */
Popup.prototype.onClose = function () {
    this.shortcode.ed.windowManager.close();
};

/**
 * Called when the "Insert" button is clicked
 */
Popup.prototype.onInsert = function () {
    var values = {},
        encode = this.shortcode.placeholder.encodeValue,
        $node  = $(this.shortcode.ed.selection.getNode());

    $('#' + this.shortcode.id + '.mce-window').find('.amarkal-ui-component').each(function () {
        var value = $(this).amarkalUIComponent('getValue'),
            name = $(this).attr('amarkal-component-name');
        values[name] = encode(value);
    });
    
    // Remove he previous amarkal shortcode if it was selected
    if($node.attr('data-amarkal-shortcode') !== undefined) {
        $node.remove();
    }

    this.shortcode.ed.selection.setContent(this.shortcode.parseTemplate(this.shortcode.config.template, values));
    this.shortcode.ed.windowManager.close();
};
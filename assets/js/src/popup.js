/**
 * A utility class for the editor popup
 */
function Popup(shortcode) {
    this.shortcode = shortcode;
}

/**
 * Called when the popup is opened.
 * 
 * @param {array} values The list of values to use for the fields after the popup loads
 */
Popup.prototype.onOpen = function (values) {
    for (name in values) {
        var $comp = $('#' + this.shortcode.id + '.mce-window').find('[amarkal-component-name="' + name + '"]'),
            value = values[name];
        $comp.amarkalUIComponent().amarkalUIComponent('setValue', value);
    }
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
        encode = this.shortcode.placeholder.encodeValue;
    $('#' + this.shortcode.id + '.mce-window').find('.amarkal-ui-component').each(function () {
        var value = $(this).amarkalUIComponent('getValue'),
            name = $(this).attr('amarkal-component-name');
        values[name] = encode(value);
    });

    this.shortcode.ed.selection.setContent(this.shortcode.parseTemplate(this.shortcode.config.template, values));
    this.shortcode.ed.windowManager.close();
};
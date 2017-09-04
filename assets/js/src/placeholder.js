/**
 * A utility class for the tinymce placeholder
 */
function Placeholder(shortcode) {
    this.shortcode = shortcode;
}
    
/**
 * Let the user edit the shortcode corresponding to the given placeholder by
 * opening the shortcode editor popup
 */
Placeholder.prototype.edit = function($placeholder) {
    var esc = $placeholder.attr('data-amarkal-shortcode'),
        sc = wp.shortcode.next(this.shortcode.id, window.decodeURIComponent(esc)),
        values = $.extend({}, sc.shortcode.attrs.named, { content: sc.shortcode.content });

    this.shortcode.ed.execCommand(this.shortcode.config.cmd, this.decodeValues(values));
    this.shortcode.ed.selection.select($placeholder[0]);
};

/**
 * Delete the shortcode corresponding to the given placeholder
 */
Placeholder.prototype.delete = function($placeholder) {
    $placeholder.remove();
};

/**
 * Decode a placeholder attribute
 */
Placeholder.prototype.decodeValue = function(value) {
    return JSON.parse(decodeURIComponent(value));
};

/**
 * Decode an array of placeholder attributes
 */
Placeholder.prototype.decodeValues = function(values) {
    var decode = this.decodeValue,
        decodedValues = {};
    Object.keys(values).forEach(function(name){
        decodedValues[name] = decode(values[name]);
    });
    return decodedValues;
};

/**
 * Encode a placeholder attribute
 */
Placeholder.prototype.encodeValue = function(value) {
    return encodeURIComponent(JSON.stringify(value));
};

/**
 * Encode an array of placeholder attributes
 */
Placeholder.prototype.encodeValues = function(values) {
    var encode = this.encodeValue,
        encodedValues = {};
    Object.keys(values).forEach(function(name){
        encodedValues[name] = encode(values[name]);
    });
    return encodedValues;
};
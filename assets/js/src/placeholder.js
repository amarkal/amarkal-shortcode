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
    // Attempt to decode a value. If an error occurs, return the undecoded attribute value.
    // This solves backward compatibility issues, where attributes are not JSON encoded.
    try {
        return JSON.parse(decodeURIComponent(value));
    }
    // This part will only be reached if the value is not JSON encoded
    catch(err) {
        return this.decodeNonJSONEncodedValue(value);
    }
};

Placeholder.prototype.decodeNonJSONEncodedValue = function(value) {
    // Non encoded arrays are stored as a string of comma delimited tokens
    if(-1 !== value.indexOf(',')) {
        return value.split(',');
    }
    return value;
}

/**
 * Decode an array of placeholder attributes
 */
Placeholder.prototype.decodeValues = function(values) {
    var _this = this,
        decodedValues = {};
    Object.keys(values).forEach(function(name){
        decodedValues[name] = _this.decodeValue(values[name]);
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
    var _this = this,
        encodedValues = {};
    Object.keys(values).forEach(function(name){
        encodedValues[name] = this.encodeValue(values[name]);
    });
    return encodedValues;
};
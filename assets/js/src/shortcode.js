/**
 * The shortcode class
 * 
 * @param {string} id 
 * @param {object} config 
 * @param {TinyMCE} ed 
 */
function Shortcode(id, config, ed) {
    this.id = id;
    this.config = config;
    this.ed = ed;
    this.popup = new Popup(this);
    this.placeholder = new Placeholder(this);
}

/**
 * Initiate the shortcode by adding the events to the active editor
 */
Shortcode.prototype.init = function () {
    var _this = this;

    // Add a command to the active editor
    this.ed.addCommand(this.config.cmd, function (values) {
        if (typeof values === 'undefined') values = _this.getDefaultValues();
        
        // Open a popup to edit this shortcode
        _this.ed.windowManager.open({
            title: _this.config.title,
            onOpen: _this.popup.onOpen.bind(_this.popup, values),
            width: _this.config.width,
            height: _this.config.height,
            id: _this.id,
            html: _this.config.html,
            buttons: [{
                text: 'Insert',
                classes: 'widget btn primary first abs-layout-item',
                disabled: false,
                onclick: _this.popup.onInsert.bind(_this.popup)
            }, {
                text: 'Close',
                onclick: _this.popup.onClose.bind(_this.popup)
            }]
        });

        // Refresh the components after the window is opened
        $('#'+_this.id+' .amarkal-ui-component').each(function(){
            $(this).amarkalUIComponent('refresh');
        });
    });

    // Replace shortcodes with placeholders
    _this.ed.on('BeforeSetContent', function (event) {
        event.content = _this.replaceShortcodes(event.content);
    });

    // Restore shortcodes when switching to the text editor, or before saving
    _this.ed.on('PostProcess', function (event) {
        if (event.get) {
            event.content = _this.restoreShortcodes(event.content);
        }
    });

    // Add edit/delete button functionality
    _this.ed.on('click', function (event) {
        var $self = $(event.target),
            $parent = $(event.target.parentElement),
            shortcode = decodeURIComponent($parent.attr('data-amarkal-shortcode'));

        // Make sure that a placeholder button was clicked
        if (event.target.nodeName === 'I' 
            && $parent.hasClass('amarkal-shortcode-placeholder')
            && wp.shortcode.next(_this.id, shortcode)) { // Verify that the shortcode has the correct tag

            // Delete this shortcode
            if ($self.is(':first-child'))
                _this.placeholder.delete.call(_this.placeholder, $parent);

            // Edit this shortcode
            if ($self.is(':last-child')) {
                _this.placeholder.edit.call(_this.placeholder, $parent);
            }
        }
    });
};

/**
 * Get the default values for the shortcode's fields as an object
 * where the keys are the field names, and the values are the defaults
 */
Shortcode.prototype.getDefaultValues = function () {
    var defaults = {};
    for (var i = 0; i < this.config.fields.length; i++) {
        var field = this.config.fields[i];
        if (typeof field.default !== 'undefined') {
            defaults[field.name] = field.default
        }
    }
    return defaults;
};

/**
 * Parse a given template, replacing the tokens with the given values.
 * Tokens are specified using the notation {{ placeholder_name }}.
 * The value in 'values.placeholder_name' will replace the token.
 * 
 * @param {string} template
 * @param {object} values
 * @returns {string} parsed template.
 */
Shortcode.prototype.parseTemplate = function (template, values) {
    return template.replace(/(\{\{([\w\d-]*)\}\})/g, function (match, p1, p2) {
        return values[p2.trim()];
    });
};

/**
 * Replace all shortcodes into a visual HTML paceholder
 * 
 * @see https://lkwdwrd.com/wp-shortcode-wp-html-wordpress-shortcodes-javascript/
 * @param {string} content
 * @returns {string}
 */
Shortcode.prototype.replaceShortcodes = function (content) {
    var _this = this;
    return wp.shortcode.replace(this.id, content, function (sc) { 
        return _this.html(sc); 
    });
}

/**
 * Restore all shortcodes by converting the visual placeholders
 * into their corresponding shortcodes.
 * 
 * @param {string} content
 * @returns {string}
 */
Shortcode.prototype.restoreShortcodes = function (content) {

    // Parse a given HTML element to retrieve the value of a certain attribute
    function getAttr(str, name) {
        name = new RegExp(name + '=\"([^\"]+)\"').exec(str);
        return name ? window.decodeURIComponent(name[1]) : '';
    }

    // Replace all placeholders into their corresponding shortcodes
    return content.replace(/(<div( [^>]+)?>)*<i><\/i><i><\/i>\.(?:<\/div>)*/g, function (match, div) {
        var data = getAttr(div, 'data-amarkal-shortcode');

        if (data) {
            return '<p>' + data + '</p>';
        }

        return match;
    });
}

/**
 * Convert the given shortcode into its corresponding placeholder.
 * 
 * @param {wp.shortcode} sc The shortcode object
 */
Shortcode.prototype.html = function (sc) {
    var cls = this.config.placeholder_class ? ' ' + this.config.placeholder_class : '';
    return wp.html.string({
        tag: "div",
        content: "<i></i><i></i>.",
        attrs: {
            class: "amarkal-shortcode-placeholder mceItem" + cls,
            'data-amarkal-shortcode': window.encodeURIComponent(sc.string()),
            'data-title': this.config.title,
            'data-subtitle': this.config['placeholder_subtitle'] ? this.parseTemplate(this.config['placeholder_subtitle'],this.placeholder.decodeValues(sc.attrs.named)) : '', // Visible field value
            style: this.config['placeholder_icon'] ? 'background-image:url('+this.config['placeholder_icon']+')' : ''
        }
    });
}
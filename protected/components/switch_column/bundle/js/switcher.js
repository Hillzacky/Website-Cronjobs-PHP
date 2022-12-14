var Switcher = (function($) {
    "use strict";

    var conf = {
        switchSelector : null,
        enabledValue: 1,
        disabledValue: 0,
        attribute: 'switch',
        beforeSend: function() {},
        afterSend: function() {},
        error: function() {}
    };

    function processSwitcher(event, state) {
        var val = state ? conf.enabledValue : conf.disabledValue;
        var params = {};
        params[conf.attribute] = val;
        var self = this;

        var url = $(this).data("url");

        conf.beforeSend.apply(self);

        $.getJSON(url, params, function(content) {
            conf.afterSend.apply(self, [content]);
        }).fail(function(jqXHR, textStatus, errorThrown ) {
            conf.error.apply(self, [jqXHR, textStatus, errorThrown]);
        });
    }

    return {
        init: function(c) {
            $.extend(true, conf, c || {});
            if(!conf.switchSelector) {
                throw "Please specify Switch Selector";
            }
            $('input[name="'+ conf.switchSelector +'"]').bootstrapSwitch().on("switchChange.bootstrapSwitch", processSwitcher);
        }
    };
})(jQuery);

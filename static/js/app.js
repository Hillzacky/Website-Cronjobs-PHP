var WebCronApp = (function($) {
    "use strict";

    var UiText = {};

    function handleUrlTabs() {
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var currentTab = $(e.target);
            var url = currentTab.data("url");
            if(url) {
                history.pushState(null, null, url);
                currentTab.closest(".nav-tabs-custom").find('div[class*="active"]').find("form").attr("action", url);
            }
        });
    }

    function pushNotification(cl, text) {
        var $content = $('#app-notification');
        $content.html('<div class="alert alert-'+ cl +'"><button data-dismiss="alert" class="close" type="button">×</button>'+ text +'</div>');
    }

    function getUiText(k, d) {
        return UiText[k] || d;
    }

    var CronUI = {
        emptyAttr: "@",
        conf: {
            command: null,
            ui: {
                invalid_minutes: "Input minutes are not valid.",
                invalid_hours: "Input hours are not valid.",
                invalid_dom: "Input days are not valid.",
                invalid_month: "Input months are not valid.",
                invalid_dow: "Input weekdays are not valid."
            }
        },
        stack: ["minutes", "hours", "dom", "month", "dow"],
        init: function(conf) {
            $.extend(true, this.conf, conf || {});
            var self = this;

            if(this.conf.command) {
                this.restore(this.conf.command);
            } else {
                this.stack.map(function(s) {
                    self.disableStackInputs(s);
                });
            }
            this.stack.map(function(s) {
                self.setStackListeners(s);
            });
        },

        restore: function(line) {
            var command = line.split(" "),
                min = command[0],
                hours = command[1],
                dom = command[2],
                month = command[3],
                dow = command[4];

            this.restoreMin(min);
            this.restoreHours(hours);
            this.restoreDom(dom);
            this.restoreMonth(month);
            this.restoreDow(dow);
        },

        restoreMin: function(min) {
            var $starRadio = $("#min_all"),
                $rangeRadio = $('[data-group="minutes_range"]'),
                $intervalRadio = $('[data-group="minutes_interval"]');

            if(this.is.star(min)) {
                this.enableStackUI("minutes", $starRadio);
                $starRadio.attr("checked", true);
            } else if(this.is.range(min)) {
                this.enableStackUI("minutes", $rangeRadio);
                $rangeRadio.attr("checked", true);
                $rangeRadio.val(min);
                $('[name="minutes_range"]').val(min.split(","));
            } else if(this.is.interval(min)) {
                this.enableStackUI("minutes", $intervalRadio);
                $intervalRadio.attr("checked", true);
                $intervalRadio.val(min);
                $('[name="minutes_interval"]').val(min);
            } else {
                throw "Unsupported minutes value";
            }
        },

        restoreHours: function(hours) {
            var $starRadio = $("#hours_all"),
                $rangeRadio = $('[data-group="hours_range"]'),
                $intervalRadio = $('[data-group="hours_interval"]');

            if(this.is.star(hours)) {
                this.enableStackUI("hours", $starRadio);
                $starRadio.attr("checked", true);
            } else if(this.is.range(hours)) {
                this.enableStackUI("hours", $rangeRadio);
                $rangeRadio.attr("checked", true);
                $rangeRadio.val(hours);
                $('[name="hours_range"]').val(hours.split(","));
            } else if(this.is.interval(hours)) {
                this.enableStackUI("hours", $intervalRadio);
                $intervalRadio.attr("checked", true);
                $intervalRadio.val(hours);
                $('[name="hours_interval"]').val(hours);
            } else {
                throw "Unsupported hours value";
            }
        },

        restoreDom: function(dom) {
            var $starRadio = $("#dom_all"),
                $lastRadio = $("#dom_last"),
                $rangeRadio = $('[data-group="dom_range"]'),
                $nearestRadio = $("#dom_nearest"),
                $intervalRadio = $('[data-group="dom_interval"]');

            if(this.is.star(dom)) {
                this.enableStackUI("dom", $starRadio);
                $starRadio.attr("checked", true);
            } else if(this.is.range(dom)) {
                this.enableStackUI("dom", $rangeRadio);
                $rangeRadio.attr("checked", true);
                $rangeRadio.val(dom);
                $('[name="dom_range"]').val(dom.split(","));
            } else if(this.is.interval(dom)) {
                this.enableStackUI("dom", $intervalRadio);
                $intervalRadio.attr("checked", true);
                $intervalRadio.val(dom);
                $('[name="dom_interval"]').val(dom);
            } else if(this.is.nearest(dom)) {
                this.enableStackUI("dom", $nearestRadio);
                $nearestRadio.attr("checked", true);
                $nearestRadio.val(dom);
                $('[name="dom_nearest"]').val(dom);
            } else if(this.is.last(dom)) {
                this.enableStackUI("dom", $lastRadio);
                $lastRadio.attr("checked", true);
            }
            else {
                throw "Unsupported dom value";
            }
        },

        restoreMonth: function(month) {
            var $starRadio = $("#month_all"),
                $rangeRadio = $('[data-group="month_range"]');

            if(this.is.star(month)) {
                this.enableStackUI("month", $starRadio);
                $starRadio.attr("checked", true);
            } else if(this.is.range(month)) {
                this.enableStackUI("month", $rangeRadio);
                $rangeRadio.attr("checked", true);
                $rangeRadio.val(month);
                $('[name="month_range"]').val(month.split(","));
            } else {
                throw "Unsupported month value";
            }
        },

        restoreDow: function (dow) {
            var $starRadio = $("#dow_all"),
                $rangeRadio = $('[data-group="dow_range"]'),
                $posSelect = $("#dow_position"),
                $daySelect = $("#dow_day"),
                $dayOfRadio = $('[data-group="dow_day"]');

            if(this.is.star(dow)) {
                this.enableStackUI("dow", $starRadio);
                $starRadio.attr("checked", true);
            } else if(this.is.range(dow)) {
                this.enableStackUI("dow", $rangeRadio);
                $rangeRadio.attr("checked", true);
                $rangeRadio.val(dow);
                $('[name="dow_range"]').val(dow.split(","));
            } else if(this.is.dayOf(dow)) {
                var day, pos;
                if(dow.indexOf("#") !== -1) {
                    var parts = dow.split("#");
                    day = parts[0];
                    pos = parts[1];
                } else {
                    day = dow.substring(0, 3);
                    pos = dow.substr(-1);
                }
                this.enableStackUI("dow", $dayOfRadio);
                $dayOfRadio.attr("checked", true);
                $dayOfRadio.val(dow);
                $posSelect.val(pos);
                $daySelect.val(day);
            } else {
                throw "Unsupported dow value";
            }
        },

        is: {
            range: function(command) {
                return command.indexOf(",") !== -1 || /^(\d+|\w{3})$/i.test(command);
            },
            star: function(command) {
                return command === "*";
            },
            interval: function(command) {
                return command.match(/\*\//);
            },
            nearest: function(command) {
                return command.match(/\dW/i);
            },
            last: function(command) {
                return command === "L";
            },
            dayOf: function(command) {
                return command.indexOf("#") !== -1 || /^\w{4}$/i.test(command);
            }
        },

        enableStackUI: function(stack, $radio) {
            this.disableStackInputs(stack);
            var childName = $radio.data("group");
            var $children = $("." + stack + '_stack select[name="'+ childName+'"]');
            $children.attr("disabled", false);
        },

        setValueCallback: {
            default: function($currentEl, $children) {
                var values = [];
                $children.each(function(index, select){
                    var i = $(select).val();
                    if($.isArray(i)) {
                        i.map(function(x) { values.push(x); });
                    } else if(i) {
                        values.push(i);
                    }
                });

                if(values.length > 0) {
                    return values.toString();
                } else {
                    return $children.length ? this.emptyAttr : $currentEl.val();
                }
            },
            dow_day: function($currentEl, $children) {
                var $pos = $("#dow_position");
                var $day = $("#dow_day");
                if($pos.val() == "L") {
                    return $day.val().concat($pos.val());
                } else {
                    return $day.val() + "#" + $pos.val();
                }
            }
        },

        applyValue: function($input, $children) {
            var callback = $input.data("group");
            var val;
            if(typeof this.setValueCallback[callback] === "function") {
                val = this.setValueCallback[callback].apply(this, [$input, $children]);
            } else {
                val = this.setValueCallback.default.apply(this, [$input, $children]);
            }
            $input.val(val);
            var expr = this.getCronExpression();
            $(document).trigger("cronui.modified", [expr]);
        },

        validate: function() {
            var expr = this.getCronExpression().split(" ");
            var errors = [];
            if(expr[0]==this.emptyAttr) {
                errors.push({
                    minutes: this.conf.ui.invalid_minutes
                });
            }
            if(expr[1]==this.emptyAttr) {
                errors.push({
                    hours: this.conf.ui.invalid_hours
                });
            }
            if(expr[2]==this.emptyAttr) {
                errors.push({
                    dom: this.conf.ui.invalid_dom
                });
            }
            if(expr[3]==this.emptyAttr) {
                errors.push({
                    month: this.conf.ui.invalid_month
                });
            }
            if(expr[4]==this.emptyAttr) {
                errors.push({
                    dow: this.conf.ui.invalid_dow
                });
            }
            return errors;
        },

        getCronExpression: function() {
            var expression = [];
            this.stack.map(function(stack) {
                var val = $('input[name="'+ stack +'"]:checked').val();
                expression.push(val);
            });
            return expression.join(" ");
        },

        disableStackInputs: function(stack) {
            $("." + stack + '_stack select:not(:radio)').attr("disabled", true);
        },

        setStackListeners: function(stack) {
            var self = this;
            $('[name="'+ stack +'"').on("change", function() {
                var $this = $(this);
                var childName = $this.data("group");
                self.disableStackInputs(stack);

                var $children = $("." + stack + '_stack select[name="'+ childName+'"]');
                $children.attr("disabled", false);

                self.applyValue($this, $children);
            });

            $("." + stack + "_stack select").on("change", function() {
                var $this = $(this);
                var $parent = $('input[data-group="'+ $this.attr("name") +'"]');
                var $children = $('[name="'+$this.attr("name")+'"]');

                self.applyValue($parent, $children);
            });
        }
    };

    var CronParams = {
        conf: {},
        init: function(conf) {
            $.extend(true, this.conf, conf || {});
            var self = this;

            // Handle Add POST/Cookie param
            $(".add-param").on("click", function() {
                var $this = $(this),
                    tmpl = $(this).data("tmpl");
                $("#"+tmpl+"-param-container").append(self.conf[tmpl+"Tmpl"]);
            });

            // Handle Remove attr
            $("body").on("click", ".remove-param", function() {
                $(this).closest(".form-group").remove();
            });
        }
    };

    var runCron = {
        conf: {
            execUrl: null
        },
        init: function(conf) {
            $.extend(true, this.conf, conf || {});
            if(!conf.execUrl) {
                throw "conf.execUrl must be specified";
            }
            var self = this;
            $.get(conf.execUrl, function(response) {
                $("#cron-job-test-result").remove();
                $("#log-list tbody").append(response);
                $("#view-cron-job-output").on("click", function(e) {
                    e.preventDefault();
                    $("#output-row").toggle();
                });
                $("#view-cron-job-output-error").on("click", function(e) {
                    e.preventDefault();
                    $("#output-error-row").toggle();
                });
            });
        }
    };

    var cronIndex = {
        conf: {
            execUrl: null
        },
        init: function(conf) {
            $.extend(true, this.conf, conf || {});
            this.bulkAction();
        },

        bulkAction: function() {
            var $checkbox = $(".grid-view").find('input[data-type="action"], input[name="selection_all"]');
            var $bulkAction = $("#bulk-action");
            var $bulkGroup = $('input[name="bulk-group"]');
            var $bulkValue = $('input[name="bulk-value"]');

            $checkbox.on("change", function() {
                var checked = false;
                $checkbox.map(function(i, el) {
                    var $el = $(el);
                    if($el.is(":checked")) {
                        checked = true;
                        $el.closest(".data-row").addClass("warning");
                    } else {
                        $el.closest(".data-row").removeClass("warning");
                    }
                });

                if(checked) {
                    $bulkAction.show();
                } else {
                    $bulkAction.hide();
                }
            });

            $bulkAction.on("change", function() {
                var $this = $(this),
                    $opt = $this.find(":selected"),
                    $optGroup = $opt.parent(),
                    confirmTxt = $opt.data("confirm"),
                    $form = $(this).closest('form');

                $bulkGroup.val($optGroup.data("name"));
                $bulkValue.val($this.val());

                if(confirmTxt) {
                    if(confirm(confirmTxt)) {
                        $form.submit();
                    }
                } else {
                    $form.submit();
                }
            });
        }
    };

    var CronHelper = {
        getExpression: function() {
            var selector = $(".inline-cron-ui").find("input[type='radio']:checked").data("association");
            return $(selector).val();
        }
    };

    return {
        setUi: function(ui) {
            $.extend(true, UiText, ui || {});
        },
        init: function() {
            handleUrlTabs();
        },
        cronJob: function(conf) {
            var $hiddenExpr = $("#hidden-expression");

            CronUI.init(conf.ui || {});
            $(document).on("cronui.modified", function(e, expression) {
                //console.log(CronUI.validate());
                //console.log(expression);
                $hiddenExpr.val(expression);
            });

            CronParams.init(conf.params || {});

            $(".inline-cron-ui").find("input[type='radio']").on("change", function() {
                if($(this).data("gui")) {
                    $("#cron_gui").slideDown();
                    $hiddenExpr.val(CronUI.getCronExpression());
                } else {
                    $("#cron_gui").slideUp();
                }
            });

            $('select[name="cron_alias"], input[name="cron_expression"]').on("change keyup", function() {
                var $radio = $(this).closest(".radio-wrapper").find('input[type="radio"]');
                $radio.prop("checked", true);
                $radio.trigger("change");
            });

            var $prBtn = $("#get-prediction-log");
            var $prTxt = $("#prediction-log");
            $('.affect-expression input, .affect-expression select').on("change", function() {
                $prBtn.show();
                $prTxt.html('');
            });

            $("#schedule-start_at_user-datetime").on("changeDate", function () {
                $prBtn.show();
                $prTxt.html('');
            });

            $prBtn.on("click", function(e) {
                e.preventDefault();

                var $this = $(this),
                    url = conf.predictionUrl,
                    expr = CronHelper.getExpression();

                $this.attr("disabled", true);
                $.getJSON(url, {expression: expr, start_at: $("#schedule-start_at_user").val() }, function(response) {
                    $this.attr("disabled", false).hide();
                    $prTxt.html(response.output);
                }).fail(function() {
                    $this.attr("disabled", false);
                });
            });
        },

        cronRun: function(conf) {
            runCron.init(conf);
        },

        cronIndex: function(conf) {
            cronIndex.init(conf);
        },

        cronLog: function(conf) {
            $(".show-row").on("click", function(e) {
                e.preventDefault();
                var id = $(this).data("id");
                var row = $(this).data("show-row");
                var obj = $('[data-row="'+ row +'"][data-key="'+ id +'"]');
                obj.toggle();
            });
        },

        switcher: {
            beforeSend: function() {
                pushNotification('info', getUiText('loading', 'Loading...'));
            },
            afterSend: function(content) {
                if(content.success) {
                    pushNotification('success', content.success);
                } else {
                    $(this).bootstrapSwitch("toggleState", true);
                    pushNotification('danger', content.error);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $(this).bootstrapSwitch("toggleState", true);
                pushNotification('danger', jqXHR.responseText);
            }
        }

    }
})(jQuery);
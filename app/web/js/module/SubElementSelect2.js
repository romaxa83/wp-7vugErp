(function ($) {
    var element;
    var option;
    //core
    function initSelect(data) {
        element = $(element).select2({
            placeholder: option['placeholder']['text'],
            width: option['width'],
            data: data,
            minimumResultsForSearch: -1,
            formatSelection: function (item) {
                return item.text;
            },
            templateResult: formatResult
        });
        $(element).val(element.attr('data-value')).trigger('change');
        $(element).on('select2:open', function () {
            $(this).val(null).trigger('change');
        });
        $(element).on('select2:selecting', collapseSubItem);
        if (option.open) {
            element.select2('open');
        }
        return element;
    }

    function formatResult(data, container) {
        $(container).attr('data-level', data.level);
        $(container).attr('data-group', data.group);
        $(container).addClass('sub-select2-item');
        if(data.hasChild){
            buttonCollapse = '<span class="btn-collapse fa fa-plus"></span>';
        }else{
            buttonCollapse = '';
        }
        return $('<span style="padding-left:' + (20 * data.level) + 'px;">' + data.text + '</span>' + buttonCollapse);
    }

    function collapseSubItem(event) {
        var target = $(event.params.args.originalEvent.target);
        if (target.hasClass('btn-collapse')) {
            event.preventDefault();
            var parent = target.parent();
            var parentLevel = parseInt(parent.attr('data-level'));
            var parentGroup = parseInt(parent.attr('data-group'));
            var exit = false;
            if (target.hasClass('fa-plus')) {
                target.removeClass('fa-plus').addClass('fa-minus');
                parent.nextAll('[data-group=' + parentGroup + ']').each(function(index,element){
                    if($(element).attr('data-level') == (parentLevel + 1)){
                        $(element).show();
                    }else if($(element).attr('data-level') == parentLevel){
                        return false;
                    }
                });
            } else {
                target.removeClass('fa-minus').addClass('fa-plus');
                var elementGroup = parent.nextAll('[data-group=' + parentGroup + ']').filter(function () {
                    var currElement = $(this);
                    var currLevel = parseInt(currElement.attr('data-level'));
                    (currLevel === parentLevel) ? exit = true : '';
                    if (exit) {
                        return false;
                    }
                    if (currLevel > parentLevel) {
                        return true;
                    }
                });
                elementGroup.find('.btn-collapse').removeClass('fa-minus').addClass('fa-plus');
                elementGroup.hide();
            }
        }
    }
    //core
    
    $.fn.SubSelect2 = function (setting) {
        option = setting || {};

        if (option === 'destroy') {
            $(element).off('select2:selecting');
            $(element).off('select2:close');
            element.select2('destroy').hide();
        }

        element = $(this);

        if (option.flag) {
            if (!$(option.flagTarget).prop('checked')) {
                return false;
            }
        }

        if (typeof option.data === 'object') {
            data = $.ajax({
                url: option.data.url,
                async: false,
                type: option.data.type
            }).responseText;
            return initSelect(JSON.parse(data));
        }
    };
})(jQuery);
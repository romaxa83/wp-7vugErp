// отображение input для изменения названия характеристики
$('body').on('click', '.update-char', function () {
    var obj = $(this);
    var text = obj.parents('.input-group').find('.expand-plus.form-control').text();
    obj.parents('.input-group').find('.expand-plus.form-control').empty();
    obj.parents('.input-group').find('.expand-plus.form-control').prepend('<input name="characteristic-name" value="' + text + '">');
    obj.removeClass('update-char');
    obj.find('i').removeClass('fa-pencil');
    obj.find('i').addClass('fa-floppy-o');
    obj.addClass('save-char');
    return false;
});
//сохранения характеристики с формы категорий
$('#category-form').on('click','.send-characteristic', function (e) {
    e.preventDefault();
    var form = $('#characteristic-form');
    $.ajax({
        url: form.attr('action'),
        type:'post',
        data: form.serialize(),
        success: function (res) {
            res = JSON.parse(res);
            if(res.type !== 'error'){
                $('#category-charsname').append('<label> <input name="Category[charsName][]" value="'+ res.id +'" type="checkbox">' + res.name + '</label>');
                form[0].reset();
            }
            ShowMSG(res.msg);
        }
    });
});
// сохранение именни характеристики
$('body').on('click', '.save-char', function () {
    var obj = $(this);
    var data = {
        'name': obj.parents('.input-group').find('input[name="characteristic-name"]').val()
    };
    $.ajax({
        type: 'POST',
        url: obj.attr('href'),
        data: data,
        success: function (res) {
            res = JSON.parse(res);
            if(res.type !== 'error'){
                obj.parents('.input-group').find('.expand-plus.form-control').empty().prepend(data.name);
                obj.removeClass('save-char');
                obj.find('i').removeClass('fa-floppy-o');
                obj.find('i').addClass('fa-pencil');
                obj.addClass('update-char');
            }
            ShowMSG(res.msg);
        },
        error: function (res) {
            ShowMSG(res);
        }
    });
    return false;
});
// удаление характеристики
$('body').on('click', '.remove-char', function () {
    var obj = $(this);
    $.ajax({
        type: 'POST',
        url: obj.attr('href'),
        success: function (res) {
            res = JSON.parse(res);
            if(res.type !== 'error'){
                obj.parents('.input-group').parent().remove();
                $('#custom' + obj.data('category-id')).remove();
                ShowMSG(res.msg);
            } else {
                res.products.forEach(function (item) {
                    $('.products-name-modal').append('<h4>' + item + '</h4>');
                });

                $('#toggle-modal').click();
                $('.close-modal').on('click', function () {
                    $('.products-name-modal').empty();
                });
            }
        },
        error: function (res) {
            ShowMSG(res);
        }
    });
    return false;
});
// добавление значения характеристики
$('.add-characteristic').on('click', function () {
    var obj = $(this);
    $.ajax({
        type: 'POST',
        url: obj.attr('href'),
        success: function (res) {
            obj.parents('.collapse.in').find('.characteristic-list').append(res);
        },
        error: function (res) {
            warning("ERROR", res);
        }
    });
    return false;
});
// удаление значения характеристики
$('body').on('click', '.remove-characteristic', function () {
    var obj = $(this);
    $.ajax({
        type: 'POST',
        url: obj.attr('href'),
        success: function (res) {
            res = JSON.parse(res);
            if(res.type !== 'error'){
                obj.parents('.form-group.level-2').remove();
                ShowMSG(res.msg);
            } else {
                res.products.forEach(function (item) {
                    $('.products-name-modal').append('<h4>' + item + '</h4>');
                });

                $('#toggle-modal').click();
                $('.close-modal').on('click', function () {
                    $('.products-name-modal').empty();
                });
            }
        }
    });

    return false;
});
// создание нового значения характеристики
$('body').on('click', '.create-characteristic', function () {
    var obj = $(this);
    var data = {
        'name': obj.parents('.input-group').find('input[name="characteristic-name"]').val(),
        'status': obj.parents('.collapse').attr('data-id')
    };
    $.ajax({
        type: 'POST',
        url: obj.attr('href'),
        data: data,
        success: function (res) {
            res = JSON.parse(res);
            if(res.type !== 'error'){
                if (!$.trim(data.name)) {
                    obj.parents('.input-group').parent().remove();
                } else {
                    obj.parents('.input-group').find('.expand-plus.form-control').remove();
                    obj.parents('.input-group').prepend('<a class="expand-plus form-control" data-toggle="collapse" href="#custom12" aria-expanded="true">' + data.name + '</a>');
                    obj.parent().empty().append(res.html);
                }
            }
            ShowMSG(res.msg);
        },
        error: function (res) {
            ShowMSG(res);
        }
    });
    return false;
});
// изменение значения характеристики
$('body').on('click', '.update-characteristic', function () {
    var obj = $(this);
    var text = obj.parents('.input-group').find('.expand-plus.form-control').text();
    obj.parents('.input-group').find('.expand-plus.form-control').empty();
    obj.parents('.input-group').find('.expand-plus.form-control').prepend('<input name="characteristic-name" value="' + text + '">');
    obj.removeClass('update-characteristic');
    obj.find('i').removeClass('fa-pencil');
    obj.find('i').addClass('fa-floppy-o');
    obj.addClass('save-characteristic');
    return false;
});
// сохранение значения характеристики
$('body').on('click', '.save-characteristic', function () {
    var obj = $(this);
    var data = {
        'name': obj.parents('.input-group').find('input[name="characteristic-name"]').val()
    };
    $.ajax({
        type: 'POST',
        url: obj.attr('href'),
        data: data,
        success: function (res) {
            res = JSON.parse(res);
            if(res.type !== 'error'){
                obj.parents('.input-group').find('.expand-plus.form-control').empty().prepend(res.name);
                obj.removeClass('save-characteristic');
                obj.find('i').removeClass('fa-floppy-o');
                obj.find('i').addClass('fa-pencil');
                obj.addClass('update-characteristic');
            }
            ShowMSG(res.msg);
        },
        error: function (res) {
            ShowMSG(res);
        }
    });
    return false;
});



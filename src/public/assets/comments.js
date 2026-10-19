(function ($) {
    'use strict';

    var state = { page: 1, sort: 'date', direction: 'desc' };
    var $list = $('#comments-list');
    var $notice = $('#notice');
    var $form = $('#comment-form');

    function setNotice(message, isError) {
        $notice.text(message).toggleClass('error', !!isError).prop('hidden', !message);
    }

    function setError(field, message) {
        $('#' + field + '-error').text(message || '');
        $('#' + field).toggleClass('is-invalid', !!message).attr('aria-invalid', !!message);
    }

    function clearErrors() {
        ['name', 'date', 'text'].forEach(function (field) { setError(field, ''); });
    }

    function csrfData() {
        var data = {};
        data[$('meta[name="csrf-name"]').attr('content')] = $('meta[name="csrf-hash"]').attr('content');
        return data;
    }

    function updateCsrf(response) {
        if (response && response.csrfHash) $('meta[name="csrf-hash"]').attr('content', response.csrfHash);
    }

    function formatDate(value) {
        var parts = String(value).split('-');
        return parts.length === 3 ? parts[2] + '.' + parts[1] + '.' + parts[0] : value;
    }

    function renderComment(comment) {
        var $row = $('<article class="comment"></article>');
        $('<span class="comment-number"></span>').text('#' + comment.id).appendTo($row);
        var $content = $('<div class="comment-content"></div>').appendTo($row);
        $('<h3 class="comment-email"></h3>').text(comment.name).appendTo($content);
        $('<p class="comment-text"></p>').text(comment.text).appendTo($content);
        var $side = $('<div class="comment-side"></div>').appendTo($row);
        $('<time class="comment-date"></time>').attr('datetime', comment.date).text(formatDate(comment.date)).appendTo($side);
        $('<button type="button" class="delete-button">Удалить</button>').attr('data-id', comment.id).attr('aria-label', 'Удалить комментарий ' + comment.id).appendTo($side);
        return $row;
    }

    function renderPagination(data) {
        var $pagination = $('#pagination').empty();
        if (data.pages <= 1) return;
        function button(label, page, disabled, active) {
            $('<button type="button" class="page-button"></button>')
                .text(label).attr('data-page', page).prop('disabled', disabled)
                .toggleClass('active', !!active)
                .attr('aria-current', active ? 'page' : null)
                .appendTo($pagination);
        }
        button('←', data.page - 1, data.page === 1, false);
        for (var page = 1; page <= data.pages; page++) button(String(page), page, false, page === data.page);
        button('→', data.page + 1, data.page === data.pages, false);
    }

    function loadComments() {
        $list.attr('aria-busy', 'true');
        $.getJSON('/comments', state).done(function (data) {
            state.page = data.page;
            $('#comment-count').text(data.total);
            $list.empty();
            if (!data.comments.length) {
                $('<p class="list-state">Пока нет комментариев. Начните обсуждение первым.</p>').appendTo($list);
            } else {
                data.comments.forEach(function (comment) { renderComment(comment).appendTo($list); });
            }
            renderPagination(data);
        }).fail(function () {
            setNotice('Не удалось загрузить комментарии. Попробуйте обновить страницу.', true);
        }).always(function () { $list.attr('aria-busy', 'false'); });
    }

    function validEmail(value) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
    }

    $('#name').on('input blur', function () {
        var value = $.trim($(this).val());
        setError('name', !value || !validEmail(value) ? 'Укажите корректный адрес электронной почты.' : '');
    });
    $('#date, #text').on('input change', function () {
        if ($(this).val()) setError(this.id, '');
    });
    $('#sort, #direction').on('change', function () {
        state.sort = $('#sort').val();
        state.direction = $('#direction').val();
        state.page = 1;
        loadComments();
    });
    $('#pagination').on('click', '[data-page]', function () {
        state.page = Number($(this).attr('data-page'));
        loadComments();
    });
    $list.on('click', '.delete-button', function () {
        var $button = $(this).prop('disabled', true);
        $.post('/comments/' + $button.attr('data-id') + '/delete', csrfData()).done(function (response) {
            updateCsrf(response);
            setNotice('Комментарий удалён.', false);
            loadComments();
        }).fail(function (xhr) {
            updateCsrf(xhr.responseJSON);
            setNotice('Не удалось удалить комментарий. Попробуйте ещё раз.', true);
            $button.prop('disabled', false);
        });
    });
    $form.on('submit', function (event) {
        event.preventDefault();
        clearErrors();
        setNotice('', false);
        var values = { name: $.trim($('#name').val()), date: $('#date').val(), text: $.trim($('#text').val()) };
        var valid = true;
        if (!validEmail(values.name)) { setError('name', 'Укажите корректный адрес электронной почты.'); valid = false; }
        if (!values.date) { setError('date', 'Выберите дату.'); valid = false; }
        if (!values.text) { setError('text', 'Введите комментарий.'); valid = false; }
        if (!valid) { $form.find('.is-invalid').first().trigger('focus'); return; }
        var $submit = $('#submit-button').prop('disabled', true).text('Публикуем…');
        $.post('/comments', $.extend(values, csrfData())).done(function (response) {
            updateCsrf(response);
            $form[0].reset();
            state.page = 1;
            state.sort = 'date';
            state.direction = 'desc';
            $('#sort').val('date');
            $('#direction').val('desc');
            setNotice('Комментарий опубликован.', false);
            loadComments();
            document.getElementById('list-title').scrollIntoView({
                behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth'
            });
        }).fail(function (xhr) {
            var response = xhr.responseJSON || {};
            updateCsrf(response);
            if (response.errors) {
                Object.keys(response.errors).forEach(function (field) { setError(field, response.errors[field]); });
                $form.find('.is-invalid').first().trigger('focus');
            } else {
                setNotice('Не удалось опубликовать комментарий. Попробуйте ещё раз.', true);
            }
        }).always(function () { $submit.prop('disabled', false).html('Опубликовать <span aria-hidden="true">↗</span>'); });
    });

    loadComments();
}(jQuery));

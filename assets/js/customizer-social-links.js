(function ($) {
    function syncLinks(container) {
        var data = [];

        container.find('.editorial-starter-social-link').each(function () {
            var item = $(this);
            var label = item.find('.editorial-starter-social-label').val().trim();
            var url = item.find('.editorial-starter-social-url').val().trim();
            var icon = item.find('.editorial-starter-social-icon').val().trim();

            if (label.length === 0 && url.length === 0 && icon.length === 0) {
                return;
            }

            data.push({ label: label, url: url, icon: icon });
        });

        container.find('.editorial-starter-social-input').val(JSON.stringify(data)).trigger('change');
    }

    function addLink(container) {
        var template = container.find('.editorial-starter-social-link-template').html();

        if (!template) {
            return;
        }

        container.find('.editorial-starter-social-links-list').append(template);

        var appended = container.find('.editorial-starter-social-links-list .editorial-starter-social-link').last();

        if (appended.length) {
            appended.find('.editorial-starter-social-label').focus();
        }

        container.trigger('editorial-starter:link-added');
        syncLinks(container);
    }

    $(document).on('click', '.editorial-starter-social-links .add-social-link', function (event) {
        event.preventDefault();

        var container = $(this).closest('.editorial-starter-social-links');
        addLink(container);
    });

    $(document).on('click', '.editorial-starter-remove-social-link', function (event) {
        event.preventDefault();

        var container = $(this).closest('.editorial-starter-social-links');
        $(this).closest('.editorial-starter-social-link').remove();
        syncLinks(container);
    });

    $(document).on('input change', '.editorial-starter-social-label, .editorial-starter-social-url, .editorial-starter-social-icon', function () {
        var container = $(this).closest('.editorial-starter-social-links');
        syncLinks(container);
    });

    wp.customize.bind('ready', function () {
        $('.editorial-starter-social-links').each(function () {
            var container = $(this);
            var list = container.find('.editorial-starter-social-links-list');

            if (list.children().length === 0) {
                addLink(container);
            }
        });
    });
})(jQuery);

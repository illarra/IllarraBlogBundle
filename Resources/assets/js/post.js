function postFormUpdate() {
    if ($('[data-post-translations] [data-post-translation]').length > 1) {
        $('[data-post-delete-translation]').show();
    } else {
        $('[data-post-delete-translation]').hide();
    }

    if ($('[data-post-translations] [data-post-translation]').length != $('[data-post-add-new-locale]').length) {
        $('[data-post-new-locale-section]').show();
    } else {
        $('[data-post-new-locale-section]').hide();
    }
}

function postTranslationDelete(e) {
    if (confirm($(this).data('post-delete-confirm'))) {
        if ($('[data-post-translation]').length > 1) {
            $(this).closest('[data-post-translation]').remove();
        }

        postFormUpdate();
    }

    e.preventDefault();
}

$('[data-post-add-new-locale]').click(function(e) {
    var locale  = $(this).data('post-locale')
      , section = $('[data-post-section-tpl]').html().replace(/__name__/g, locale)
      ;

    e.preventDefault();

    function strcmp(str1, str2) {
        return ((str1 == str2) ? 0 : ((str1 > str2) ? 1 : -1));
    }

    if (!$('a[href=#' + locale + ']').length) {
        $('[data-post-translations] [data-post-translation]').each(function (i, el) {
            var next = $(el).next('[data-post-translation]')
              , cmp  = strcmp($(el).data('post-translation-locale'), locale)
              ;

            if (cmp == 0 || cmp == 1) {
                $(el).before(section);
                return false;
            }

            if (next.length == 0) {
                $('[data-post-new-locale-section]').before(section);
                return false;
            }
        });
    }
    
    postFormUpdate();
});

$('[data-post-translations]').on('click', '[data-post-delete-translation] a', postTranslationDelete);
$('[data-post-delete-translation] a').click(postTranslationDelete);

postFormUpdate();
/*
 * jquery.formyeah
 * https://github.com/artpolikarpov/jquery.formyeah
 *
 * Copyright © 2014 Artem Polikarpov
 * Licensed under the MIT
 */
(function ($, undefined) {
  var localStorage = window.localStorage || {setItem: $.noop, getItem: $.noop},
      formyeahStr = 'formyeah',
      formyeahExp = new RegExp('^(?:(.+)\\.)?' + formyeahStr + '\\.(.+)');

  function encodeInputSelector ($field, pageIdentifier) {
    return (pageIdentifier ? pageIdentifier + '.' : '') + formyeahStr + '.' + '#' + $field.attr('id');
  }

  function decodeInputSelector (key, pageIdentifier) {
    var matched = key.match(formyeahExp);
    return matched && matched[1] === (pageIdentifier + '' || undefined) && matched[2];
  }

  function isRadioOrCheckbox ($field) {
    var type = $field.attr('type');
    return type === 'radio' || type === 'checkbox';
  }

  function emptyStorage ($fields, pageIdentifier) {
    $fields.each(function () {
      delete localStorage[encodeInputSelector($(this), pageIdentifier)];
    });
  }

  $.fn.formyeah = function () {
    var action = arguments[0],
        _options = $.extend({
          pageIdentifier: [location.pathname, location.search, location.hash].join('')
        }, arguments[1] || typeof action === 'object' && action);

    return this.each(function () {
      var $parent = $(this),
          fieldSelector = ':input[id]:not([data-recover="false"]):not([type="password"])',
          $fields = $(fieldSelector, $parent),
          parentData = $parent.data(),
          options = $.extend({}, _options, parentData);

        if (action === 'emptyStorage') {
          // Empty storage for fields in the selected $element
          emptyStorage($fields, options.pageIdentifier);
          return;
        }

        // Allowed only once
        if (parentData.formyeahInitialized) {
          return;
        }

        parentData.formyeahInitialized = true;

        // Restore saved values
        $.each(localStorage, function (key, value) {
          var $field = $(decodeInputSelector(key, options.pageIdentifier)).filter(fieldSelector);

          if (!$field.length) {
            return;
          }

          if (!isRadioOrCheckbox($field)) {
            if (value) {
              $field.val(value.split(','));
            }
          } else {
            $field.attr('checked', value === 'true');
          }
        });

        // Record changes
        $parent.on('input change', fieldSelector, function () {
          var $field = $(this),
              items = [],
              value;

        if (isRadioOrCheckbox($field)) {
            value = $field.is(':checked');

          if ($field.attr('type') === 'radio' && value) {
            var $form = $field.parents('form');

            $('input[type="radio"][name="' + $field.attr('name') +'"]', $form.length && $form).not($field).each(function () {
              items.push({$field: $(this), value: false});
            });
          }
        } else {
          value = $field.val();
        }

        items.push({$field: $field, value: value});

        $.each(items, function (i, object) {
          localStorage.setItem(encodeInputSelector(object.$field, options.pageIdentifier), object.value);
        });
      });

      $fields.change();

      $parent.on('submit reset', function () {
        emptyStorage($fields, options.pageIdentifier);
      });
    });
  };

  $(function () {
    // Auto initialization
    $('.' + formyeahStr).formyeah();
  });
}(jQuery));

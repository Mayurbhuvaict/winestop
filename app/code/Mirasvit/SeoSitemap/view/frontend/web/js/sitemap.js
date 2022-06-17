define([
    'jquery',
    'underscore'
], function ($, _) {
    'use strict';

    $.widget('mst.sitemap', {

        providerSelector: '[data-element=provider]',
        letterSelector:   '[data-element=letter]',
        notFoundSelector: '[data-element=notFound]',

        _create: function () {
            const $searchBox = $('[data-element = search]', this.element);

            $searchBox.on('change keyup', function () {
                let isFound = false;
                const query = $searchBox.val().toLowerCase();

                const $items = $('[data-element=item]', this.element);

                resetHighlight($items);

                if (query === '') {
                    isFound = true;
                    $items.css('display', '');
                    $(this.providerSelector, this.element).show();
                    $(this.letterSelector, this.element).show();
                } else {
                    $(this.providerSelector, this.element).hide();
                    $(this.letterSelector, this.element).hide();

                    _.each($items, function (item) {
                        const $item = $(item);
                        const text = $item.text().toLowerCase();

                        if (text.indexOf(query) !== -1) {
                            isFound = true;
                            $item.show();
                            $item.closest(this.providerSelector).show();
                            $item.closest(this.letterSelector).show();

                            _.each($('span', $item), function (span) {
                                highlight($(span), query);
                            });
                        } else {
                            $item.hide();
                        }
                    }.bind(this))
                }
                if (isFound) {
                    $(this.notFoundSelector, this.element).hide();
                } else {
                    $(this.notFoundSelector, this.element).show();
                }
            }.bind(this));
        }
    });

    return $.mst.sitemap;

    function highlight($element, q) {
        let arSpecialChars = [
            {'key': 'a', 'value': '(à|â|ą|a)'},
            {'key': 'c', 'value': '(ç|č|c)'},
            {'key': 'e', 'value': '(è|é|ė|ê|ë|ę|e)'},
            {'key': 'i', 'value': '(î|ï|į|i)'},
            {'key': 'o', 'value': '(ô|o)'},
            {'key': 's', 'value': '(š|s)'},
            {'key': 'u', 'value': '(ù|ü|û|ū|ų|u)'}
        ];

        let text = $element.text();

        if (q) {
            let word = q.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, '\\$&');

            arSpecialChars.forEach(function (match, idx) {
                word = word.replace(new RegExp(match.key, 'g'), match.value);
            });

            if ('span'.indexOf(word.toLowerCase()) < 0) {
                text = text.replace(new RegExp('(' + word + '(?![^<>]*>))', 'ig'), function ($1, match) {
                    return '<i class="_highlight">' + match + '</i>';
                });
            }
        }

        $element.html(text);
    }

    function resetHighlight($items) {
        _.each($items, function (el) {
            let html = $(el).html();
            html = html.replace(/<\/?i[^>]*>/g, '');

            $(el).html(html);
        })
    }
})

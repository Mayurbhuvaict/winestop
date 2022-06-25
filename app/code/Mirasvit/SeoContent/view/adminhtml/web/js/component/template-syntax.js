define([
    'jquery',
    'underscore',
    'ko',
    'uiComponent'
], function ($, _, ko, Component) {
    'use strict';

    return Component.extend({
        hideTimeout: null,
        $wrapper:    null,

        defaults: {
            template:      'Mirasvit_SeoContent/component/template-syntax',
            childSelector: '.mst-seo-content__global-template-syntax input, .mst-seo-content__global-template-syntax textarea',
            wrapperClass:  'mst-seo-content__component-template-syntax',
            vars:          [],
            ruleType:      0,
            activeItem:    '',
            caretPosition: 0,
            suggestUrl:    '',
        },

        initialize: function (config) {
            this._super();
            
            this.suggestUrl = config.suggestUrl;

            var templatesTimer = setInterval(function () {
                if ($('.mst-seo-content__component-template-syntax-wrapper').length
                    && $(this.childSelector).length) {
                    clearInterval(templatesTimer);
                    this.init();
                    this.loadSuggestions();
                }
            }.bind(this), 250);

            return this;
        },

        loadSuggestions: function () {
            var self = this;

            var ruleType = require('uiRegistry').get('index = rule_type').value();

            if (!Array.isArray(ruleType)) {
                self.ruleType = ruleType;
            }

            $.ajax({
                method: 'GET',
                url: self.suggestUrl,
                data: {
                    rule_type: self.ruleType
                }
            }).done(function (result) {
                self.vars = result.suggestion;
            });
        },

        init: function () {
            var html = $('.mst-seo-content__component-template-syntax-wrapper').html();

            this.$wrapper = $('<div/>')
                .addClass(this.wrapperClass)
                .html(html);

            $('body').append(this.$wrapper);

            _.each($(this.childSelector), function (item) {
                this.attachEvents($(item));
            }.bind(this));

            // initialize variables insert by double click
            $('._variable').dblclick(function (e) {
                e.preventDefault();

                var variable   = '[' + e.target.innerText + ']';
                var fieldItem  = $(this.activeItem);
                var fieldValue = fieldItem.val().trim();
                var position   = this.caretPosition > fieldValue.length
                    ? fieldValue.length
                    : this.caretPosition;
                
                fieldValue = [fieldValue.slice(0, position), variable, fieldValue.slice(position)]
                    .map(function (part) {
                        return part.trim();
                    })
                    .filter(function (part) {
                        return part.length > 0;
                    })
                    .join(' ');

                fieldItem.val(fieldValue);

                this.caretPosition = position + variable.length + 1;
            }.bind(this));
        },

        attachEvents: function ($item) {
            $item.on('click', function (e) {
                this.activeItem    = e.target;
                this.caretPosition = e.target.selectionStart;

                var ruleType = require('uiRegistry').get('index = rule_type').value();
                ruleType = Array.isArray(ruleType) ? 0 : ruleType;

                if (ruleType != this.ruleType) {
                    this.loadSuggestions(); //update suggested variables if rule type changed
                }
            }.bind(this));

            $item.on('focus', function () {
                clearTimeout(this.hideTimeout);

                this.$wrapper.addClass('_visible');
            }.bind(this));

            $item.on('blur', function () {
                this.hideTimeout = setTimeout(function () {
                    this.$wrapper.removeClass('_visible');
                }.bind(this), 100);
            }.bind(this));

            this.$wrapper.on('click', function () {
                clearTimeout(this.hideTimeout);
            }.bind(this));

            $('.close', this.$wrapper).on('click', function () {
                this.$wrapper.removeClass('_visible');
            }.bind(this));

            $('body').on('click', function (e) {
                var isTargetNotTemplateHelper = e.target.nodeName != 'INPUT'
                    && e.target.nodeName != 'TEXTAREA'
                    && !e.target.closest('.mst-seo-content__component-template-syntax');

                var isTargetNotTemplateField = (e.target.nodeName == 'INPUT' && e.target.nodeName == 'TEXTAREA')
                    && !e.target.closest('.mst-seo-content__global-template-syntax')
                    && !e.target.closest('.mst-seo-content__component-template-syntax-wrapper');

                if (isTargetNotTemplateHelper || isTargetNotTemplateField) {
                    this.$wrapper.removeClass('_visible');
                }
            }.bind(this));

            // typeahead
            $item.on('keyup', function (e) {
                var isVariableChar = (e.keyCode >= 48 && e.keyCode <= 57)
                    || (e.keyCode >= 65 && e.keyCode <= 90)
                    || (e.keyCode >= 96 && e.keyCode <= 105)
                    || e.key == '_';

                if (!isVariableChar) {
                    return;
                }

                var inputText        = $item.val();
                var caretPos         = e.target.selectionStart;
                var textAfterCarret  = inputText.slice(caretPos);

                var isInsideVariable = textAfterCarret.indexOf(']') >= 0
                    && (
                        textAfterCarret.indexOf(']') < textAfterCarret.indexOf('[')
                        || textAfterCarret.indexOf(']') <= /\W/.exec(textAfterCarret).index
                    );

                if (isInsideVariable) {
                    return;
                }

                var lastOpenSquareBracketPosBeforeCaret = inputText.slice(0, caretPos).lastIndexOf('[');
                var textForSuggest                      = inputText.slice(lastOpenSquareBracketPosBeforeCaret, caretPos);

                if (textForSuggest.indexOf(']') > 0) {
                    textForSuggest = '';
                }

                if (textForSuggest.length >= 2) {
                    var suggestion = this.vars.filter(function (variable) {
                        return variable.indexOf(textForSuggest) == 0;
                    })

                    if (suggestion.length) {
                        inputText = [
                            inputText.slice(0, lastOpenSquareBracketPosBeforeCaret),
                            suggestion[0],
                            inputText.slice(caretPos)
                        ].join('');

                        $item.val(inputText);

                        e.target.selectionStart = caretPos;
                        e.target.selectionEnd = lastOpenSquareBracketPosBeforeCaret + suggestion[0].length;
                    }
                
                    this.caretPosition = e.target.selectionEnd;
                }
            }.bind(this));
        }
    });
});

define(
    ['ko'],
    function (ko) {
        'use strict';

        return {
            code: ko.observable(''),
            isApplied: ko.observable(false)
        }
    }
);

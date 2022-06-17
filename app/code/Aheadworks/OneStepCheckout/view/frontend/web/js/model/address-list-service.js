define(
    ['ko'],
    function (ko) {
        'use strict';

        var isLoading = ko.observable(false);

        return {
            isLoading: isLoading
        };
    }
);

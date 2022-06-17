define(
    [
        'ko'
    ],
    function(ko) {
        'use strict';

        var isShownFlag = ko.observable(false);

        return {
            isShown: isShownFlag
        };
    }
);

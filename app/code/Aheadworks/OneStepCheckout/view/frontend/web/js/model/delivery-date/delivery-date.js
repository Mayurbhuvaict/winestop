define(
    ['ko'],
    function (ko) {
        'use strict';

        var date = ko.observable(''),
            timeSlot = ko.observable('');

        return {
            date: date,
            timeSlot: timeSlot
        };
    }
);

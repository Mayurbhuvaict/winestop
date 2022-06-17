define(
    ['underscore'],
    function (_) {
        'use strict';

        return function (abstractTotal) {

            /**
             * @inheritdoc
             */
            return _.extend(abstractTotal, {
                isFullMode: function () {
                    return !!this.getTotals();
                }
            });
        }
    }
);

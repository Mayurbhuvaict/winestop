define([
    'uiCollection',
    'uiRegistry',
    'mage/translate'
], function (Collection, registry, $t) {
    'use strict';

    return Collection.extend({
        defaults: {
            template: 'Aheadworks_AdvancedReports/ui/grid/controls/report/settings/button',
            displayArea: 'dataGridActions',
            title: $t('Report Settings')
        },

        /**
         * Apply action on target component
         */
        applyAction: function () {
            var targetName = this.action.targetName,
                params = this.action.params || [],
                actionName = this.action.actionName,
                target;

            if (registry.has(targetName)) {
                target = registry.async(targetName);

                if (target && typeof target === 'function' && actionName) {
                    params.unshift(actionName);
                    target.apply(target, params);
                }
            }
        }
    });
});

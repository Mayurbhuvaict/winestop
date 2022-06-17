define([
    'Magento_Ui/js/form/components/group',
    'underscore'
], function (Group, _) {
    'use strict';
    
    return Group.extend({
        defaults: {
            toggle: {
                selector: '',
                value:    ''
            }
        },
        
        initialize: function () {
            this._super();
            
            this.setLinks({
                toggleVisibility: this.toggle.selector
            }, 'imports');
        },
        
        toggleVisibility: function (selected) {
            if (_.indexOf(this.toggle.value.split(','), selected) >= 0) {
                this.visible(true);
            } else {
                this.visible(false);
            }
        }
    });
});
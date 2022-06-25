define([
    'Magento_Ui/js/form/components/fieldset',
    'underscore'
], function (Fieldset, _) {
    'use strict';
    
    return Fieldset.extend({
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
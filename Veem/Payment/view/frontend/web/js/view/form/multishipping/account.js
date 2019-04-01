define(
    [
        'Veem_Payment/js/view/form/account'
    ], function(Component) {
        return Component.extend(
            {
                defaults: {
                    links: {}
                },

                initialize: function() {
                    this._super();
                }
            }
        );
    }
);
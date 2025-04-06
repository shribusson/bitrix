(function () {
    'use strict';

    BX.namespace('BX.iTrack.Chart.StaffDetail');

    BX.iTrack.Chart.StaffDetail = {
        init: function (parameters) {
            this.result = parameters.result || {};
            this.ajaxUrl = parameters.ajaxUrl || '';
            this.signedParams = parameters.signedParams;
            this.link_utm = parameters.link_utm;
            this.elementId = parameters.elementId;

            this.closeBtn = document.querySelector('.js-close-btn');
            this.chatBtn = document.querySelector('.js-chat-link');
            this.callBtn = document.querySelector('.js-call-link');

            this.bindEvents();
        },

        initPull: function () {
            BX.addCustomEvent('onPullEvent', BX.delegate(function (module_id, command, params) {
                if (module_id === 'local.fwink' && command === 'update_staff_detail') {
                    if (params.ELEMENT_ID === this.elementId) {
                        //this.list();
                    }
                }
            }, this));
        },

        bindEvents: function() {
            BX.bind(this.closeBtn, 'click', this.close);
            BX.bind(this.chatBtn, 'click', BX.delegate(this.openChat, this));
            BX.bind(this.callBtn, 'click', BX.delegate(this.openCall, this));
        },

        close: function() {
            window.BX24.closeApplication();
        },

        openChat: function() {
            window.BX24.im.openMessenger(this.elementId);
        },

        openCall: function() {
            window.BX24.im.callTo(this.elementId, true);
        },

        openPost: function(id) {
            id = id || 0;
            if(parseInt(id) > 0) {
                window.BX24.openApplication({
                    mode: 'pages',
                    page: 'post',
                    element_id: id,
                    "link_utm": this.link_utm,
                    bx24_width: 550
                });
            }
        },
        openBlock: function(id) {
            id = id || 0;
            if(parseInt(id) > 0) {
                /*window.BX24.openApplication({
                    mode: 'pages',
                    page: 'companyblock',
                    element_id: this.elementId,
                    "link_utm": this.link_utm,
                    bx24_width: 550
                });*/
            }
        },
        openUser: function(id) {
            id = id || 0;
            if(parseInt(id) > 0) {
                window.BX24.openApplication({
                    mode: 'pages',
                    page: 'staff',
                    element_id: id,
                    "link_utm": this.link_utm,
                    bx24_width: 870
                });
            }
        }
    };
})();

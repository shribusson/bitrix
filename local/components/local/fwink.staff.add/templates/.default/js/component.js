(function () {
    'use strict';

    BX.namespace('BX.iTrack.Chart.StaffNew');

    BX.iTrack.Chart.StaffNew = {
        init: function (parameters) {
            this.result = parameters.result || {};
            this.ajaxUrl = parameters.ajaxUrl || '';
            this.signedParams = parameters.signedParams;
            this.link_utm = parameters.link_utm;
            this.sign = parameters.sign;
            this.message = parameters.message;
            this.formname = 'staff_new_form';
            this.buttonname = 'save-btn';
            this.closeBtn = document.querySelector('.js-close-btn');
            this.loader = BX('loader');

            BX.bind(BX(this.formname), 'submit', function (e) {
                BX.PreventDefault(e);
                this.showLoader();
                var oData = this.collectformdata(e.target);
                BX.ajax({
                    method: 'POST',
                    dataType: 'json',
                    url: e.target.action,
                    data: oData,
                    onsuccess: BX.delegate(function (result) {
                        this.hideLoader();
                        if(result.hasOwnProperty('status')) {
                            if(result.status == 'success') {
                                this.processSuccess();
                            } else if(result.status == 'error') {
                                this.processError(result.message)
                            }
                        } else {
                            this.processError('Внутренняя ошибка');
                        }
                    }, this)
                });
            }.bind(this));
            BX.bind(this.closeBtn, 'click', BX.delegate(this.close, this));
        },

        processError: function(message) {
            message = message || '';
            let popup = new BX.UI.Dialogs.MessageBox(
                {
                    title: 'Приглашение пользователя',
                    message: 'Ошибка: ' + message,
                    modal: true,
                    buttons: BX.UI.Dialogs.MessageBoxButtons.OK,
                    onOk: BX.delegate(function (messageBox) {
                        messageBox.close();
                    }, this)
                });
            popup.show();
        },

        processSuccess: function() {
            let popup = new BX.UI.Dialogs.MessageBox(
                {
                    title: 'Приглашение пользователя',
                    message: 'Пользователю выслано приглашение на портал, форма будет закрыта',
                    modal: true,
                    buttons: BX.UI.Dialogs.MessageBoxButtons.OK,
                    onOk: BX.delegate(function (messageBox) {
                        messageBox.close();
                        window.BX24.closeApplication();
                    }, this)
                });
            popup.show();
        },

        collectformdata: function (form) {
            if (typeof form != "object")
                return false;
            let oData = {
                "AJAX_CALL" : "Y",
                "action":"add",
                "link_utm":this.link_utm,
                "signedParams":this.signedParams
            };

            for (var ii = 0; ii < form.elements.length; ii++) {
                if (form.elements[ii] && form.elements[ii].name) {
                    if (form.elements[ii].type && form.elements[ii].type == "checkbox") {
                        if (form.elements[ii].checked == true)
                            oData[form.elements[ii].name] = form.elements[ii].value;
                    } else {
                        oData[form.elements[ii].name] = form.elements[ii].value;
                    }
                }
            }

            return oData;

        },

        close: function() {
            window.BX24.closeApplication();
        },
        showLoader: function() {
            this.loader.classList.add('active');
        },
        hideLoader: function() {
            this.loader.classList.remove('active');
        },
        createPost: function() {
            window.BX24.openApplication({
                add: 'new',
                mode: 'pages',
                page: 'post',
                bx24_width: 550,
                link_utm: this.sign,
            }, function(){
                this.reloadPosts();
            }.bind(this));
        },
        reloadPosts: function() {
            this.showLoader();
            BX.ajax.runAction('local:fwink.api.post.list',{
                data: {
                    sign: this.sign
                }
            }).then((response) => {
                if(response.hasOwnProperty('data')) {
                    let newOptions = '<option>Выбрать</option>';
                    for(let i = 0; i < response.data.length; i++) {
                        newOptions += '<option value="' + response.data[i].id + '">' + response.data[i].name + '</option>';
                    }
                    document.querySelector('[name="POST"]').innerHTML = newOptions;
                }
                this.hideLoader();
            });
        }
    };
})();

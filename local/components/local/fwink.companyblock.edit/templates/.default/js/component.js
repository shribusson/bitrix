(function () {
    'use strict';

    BX.namespace('BX.iTrack.Chart.CompanyBlockNew');

    BX.iTrack.Chart.CompanyBlockNew = {
        init: function (parameters) {
            this.mode = parameters.mode || 'add';
            this.result = parameters.result || {};
            this.ajaxUrl = parameters.ajaxUrl || '';
            this.signedParams = parameters.signedParams;
            this.link_utm = parameters.link_utm;
            this.message = parameters.message;
            this.formname='block-form';
            this.savebtn=document.querySelector('.js-edit-btn');
            this.deleteBtn = document.querySelector('.js-delete-btn');
            this.closeBtn = document.querySelector('.js-close-btn');
            this.parentColorChecker = document.querySelector('[name="COLOR_BY_PARENT"]');
            this.colorBlockInput = document.querySelector('[name="COLOR_BLOCK"]');
            this.loader = BX('loader');

            this.bindEvents();
            this.initColorPicker();
            this.initSelect();
        },

        bindEvents: function() {
            BX.bind(BX(this.formname), 'submit', BX.delegate(this.save, this));
            BX.bind(this.savebtn, 'click', BX.delegate(this.save, this));
            BX.bind(this.closeBtn, 'click', BX.delegate(this.close, this));
            BX.bind(this.deleteBtn, 'click', BX.delegate(this.delete, this));
            BX.bind(this.parentColorChecker, 'change', BX.delegate(this.changeParentColorMode, this));
        },

        initColorPicker: function() {
            let inputs = document.querySelectorAll('.js-color-input');
            inputs.forEach(function(element) {
                BX.bind(element, 'click', function () {
                    new BX.ColorPicker({
                        bindElement: element, // Элемент, к которому будет прикреплена область с выбором цвета
                        defaultColor: '#FF6600', // Цвет по-умолчанию
                        allowCustomColor: true, // Разрешить указывать произвольный цвет
                        onColorSelected: function (item) {
                            element.value = item // Вызывается при выборе цвета
                            element.style.background = item;
                        },
                        popupOptions: {
                            angle: true, // треугольник
                            autoHide: true, // Закрытие по клику вне области
                            closeByEsc: true, // Закрытие по esc
                            events: {
                                onPopupClose: function () {
                                    // Вызывается при закрытии окна
                                }
                            }
                        }
                    }).open();
                })
            });
        },
        initSelect: function() {
            $('select.postdetail-input').select2({width: '100%'});
        },
        changeParentColorMode: function(e) {
            if(e.target.checked) {
                this.colorBlockInput.disabled = true;
                this.colorBlockInput.value = this.result.FIELDS.COLOR_BY_PARENT.parent_value;
                this.colorBlockInput.style.background = this.result.FIELDS.COLOR_BY_PARENT.parent_value;
            } else {
                this.colorBlockInput.disabled = false;
            }
        },

        delete: function() {
            let popup = new BX.UI.Dialogs.MessageBox(
                {
                    title: 'Удаление',
                    message: 'Вы действительно хотите удалить блок ' + this.result.FIELDS.NAME.value + '? Дочерние блоки поднимутся на уровень текущего.',
                    modal: true,
                    buttons: BX.UI.Dialogs.MessageBoxButtons.OK_CANCEL,
                    onOk: BX.delegate(function (messageBox) {
                        messageBox.close();
                        this.showLoader();
                        BX.ajax({
                            method: 'POST',
                            dataType: 'json',
                            url: this.ajaxUrl,
                            data: {
                                "AJAX_CALL": "Y",
                                "action": "delete",
                                "ID": this.result.ID,
                                "link_utm": this.link_utm,
                                "signedParams": this.signedParams
                            },
                            onsuccess: BX.delegate(function (result) {
                                this.hideLoader();
                                if(result.hasOwnProperty('status')) {
                                    if(result.status == 'success') {
                                        this.processSuccess('Блок успешно удален. Форма будет закрыта', 'Удаление блока');
                                    } else if(result.status == 'error') {
                                        this.processError(result.message, 'Удаление блока')
                                    }
                                } else {
                                    this.processError('Внутренняя ошибка');
                                }
                            }, this)
                        });
                    }, this),
                    onCancel: function (messageBox) {
                        messageBox.close();
                    }
                });
            popup.show();
        },

        save: function(e) {
            BX.PreventDefault(e);
            this.showLoader();
            var oData = this.collectformdata(BX(this.formname));
            BX.ajax({
                method: 'POST',
                dataType: 'json',
                url: this.ajaxUrl,
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
        },

        applyColorToChilds: function (e) {
            BX.PreventDefault(e);
            this.showLoader();
            let oData = {
                "AJAX_CALL": "Y",
                "action": "applyChildColors",
                "ID": this.result.ID,
                'color': document.querySelector('[name="COLOR_BLOCK"]').value,
                "link_utm": this.link_utm,
                "signedParams": this.signedParams
            };
            BX.ajax({
                method: 'POST',
                dataType: 'json',
                url: this.ajaxUrl,
                data: oData,
                onsuccess: BX.delegate(function (result) {
                    this.hideLoader();
                    if(result.hasOwnProperty('status')) {
                        if(result.status == 'success') {
                            this.processSuccess('Готово','Изменить цвета дочерних блоков', false);
                        } else if(result.status == 'error') {
                            this.processError(result.message)
                        }
                    } else {
                        this.processError('Внутренняя ошибка');
                    }
                }, this)
            });
        },

        collectformdata:function (form) {
            if (typeof form != "object")
                return false;
            let oData;

            if(this.mode === 'add') {
                oData = {
                    "AJAX_CALL": "Y",
                    "action": "add",
                    "link_utm": this.link_utm,
                    "signedParams": this.signedParams
                };
            } else {
                oData = {
                    "AJAX_CALL": "Y",
                    "action": "edit",
                    "ID": this.result.ID,
                    "link_utm": this.link_utm,
                    "signedParams": this.signedParams
                };
            }

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
        showLoader: function() {
            this.loader.classList.add('active');
        },
        hideLoader: function() {
            this.loader.classList.remove('active');
        },
        close: function() {
            window.BX24.closeApplication();
        },
        processError: function(message, title) {
            message = message || '';
            title = title || 'Изменение блока'
            let popup = new BX.UI.Dialogs.MessageBox(
                {
                    title: title,
                    message: 'Ошибка: ' + message,
                    modal: true,
                    buttons: BX.UI.Dialogs.MessageBoxButtons.OK,
                    onOk: BX.delegate(function (messageBox) {
                        messageBox.close();
                    }, this)
                });
            popup.show();
        },

        processSuccess: function(message, title, close) {
            message = message || 'Блок успешно изменен. Форма будет закрыта';
            title = title || 'Изменение блока';
            close = close !== false;
            let popup = new BX.UI.Dialogs.MessageBox(
                {
                    title: title,
                    message: message,
                    modal: true,
                    buttons: BX.UI.Dialogs.MessageBoxButtons.OK,
                    onOk: BX.delegate(function (messageBox) {
                        messageBox.close();
                        if(close) {
                            window.BX24.closeApplication();
                        }
                    }, this)
                });
            popup.show();
        },

        addMoreOption: function(code) {
            BX.PreventDefault();
            let current = document.querySelectorAll('[data-field="' + code +'"]');
            let last = current[current.length - 1].nextSibling;
            let childs = [BX.create('option', {props: {value: 0}, text: 'Выбрать'})];
            for(let i in this.result.FIELDS[code].values) {
                childs.push(BX.create('option', {
                        props: {
                            value: i
                        },
                        text: this.result.FIELDS[code].values[i]
                    }));
            }
            let newSelect = BX.create('select', {
                props: {
                    name: code + '[' + current.length + ']',
                    className: 'postdetail-input'
                },
                dataset: {
                    field: code
                },
                children: childs
            });
            BX.insertAfter(newSelect, last);
            BX.insertAfter(BX.create('br'), last);
            BX.insertAfter(BX.create('br'), last);
            $(newSelect).select2({width: '100%'});
        },

        createPost: function() {
            window.BX24.openApplication({
                add: 'new',
                mode: 'pages',
                page: 'post',
                bx24_width: 550,
                link_utm: this.link_utm,
            }, function(){
                this.reloadPosts();
            }.bind(this));
        },
        reloadPosts: function() {
            this.showLoader();
            BX.ajax.runAction('local:fwink.api.post.list',{
                data: {
                    sign: this.link_utm
                }
            }).then((response) => {
                if(response.hasOwnProperty('data')) {
                    const selects = document.querySelectorAll('[data-field="POSTS"]');
                    for(let i = 0; i < response.data.length; i++) {
                        if(!this.result.FIELDS.POSTS.values[response.data[i].id]) {
                            this.result.FIELDS.POSTS.values[response.data[i].id] = response.data[i].name;
                        }

                        selects.forEach((select) => {
                            if(!select.querySelector('[value="' + response.data[i].id + '"]')) {
                                let option = document.createElement('option');
                                option.value = response.data[i].id;
                                option.text = response.data[i].name;
                                select.add(option);
                                select.dispatchEvent(new Event('change'));
                            }
                        });
                    }
                }
                this.hideLoader();
            });
        }
    }


})();

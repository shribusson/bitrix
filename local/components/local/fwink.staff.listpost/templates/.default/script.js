(function () {
    BX.namespace('BX.iTrack.Chart.PostList');

    BX.iTrack.Chart.PostList = {
        gridId: null,
        grid: null,
        sign: '',
        init: function(params) {
            this.gridId = params.gridId || null;
            this.serviceUrl = params.serviceUrl || null;
            this.sign = params.sign || '';
            this.signedParamsEdit = params.signedParamsEdit || '';
            this.editUrl = params.editUrl || '';

            if(this.gridId) {
                this.processGrid();
            }
        },
        processGrid: function() {
            var self = this;
            (new Promise(function (resolve, reject) {
                // Объявим переменную в которой будем хранить количество попыток
                var checkCount = 0;

                // Объявим рекурсивную функцию для поиска инстанса
                var checkInstance = function () {
                    if (20 < ++checkCount) {
                        return reject();
                    }

                    if(typeof BX.Main.gridManager === 'undefined'){
                        return setTimeout(checkInstance, 500);
                    }

                    var instance = BX.Main.gridManager.getInstanceById(self.gridId);

                    if(!instance) {
                        return setTimeout(checkInstance, 500);
                    }

                    // Успех вернем сожержимое менеджера
                    return resolve(instance);
                };

                // Запустим поиск менеджера карточки
                checkInstance();
            })).then(function(grid) {
                if(grid.getId() == this.gridId) {
                    grid.baseUrl = this.serviceUrl;
                    this.grid = grid;
                }
            }.bind(this));
        },
        newPost: function () {
            window.BX24.openApplication({
                add: 'new',
                mode: 'pages',
                page: 'post',
                bx24_width: 550,
                link_utm: this.sign,
            }, BX.delegate(this.reloadGrid, this))
        },
        openPost: function(id) {
            window.BX24.openApplication({
                mode: 'pages',
                page: 'post',
                element_id: id,
                link_utm: this.sign,
                bx24_width: 550
            })
        },
        openUser: function(id) {
            /*window.BX24.openApplication({
                mode: 'pages',
                page: 'staff',
                element_id: id,
                link_utm: this.sign,
                bx24_width: 870
            })*/
            window.BX24.openPath('/company/personal/user/' + id + '/');
        },
        reloadGrid: function() {
            this.grid.reload();
        },
        delete: function(id) {
            let popup = new BX.UI.Dialogs.MessageBox(
                {
                    title: 'Удаление',
                    message: 'Вы действительно хотите удалить выбранную должность?',
                    modal: true,
                    buttons: BX.UI.Dialogs.MessageBoxButtons.OK_CANCEL,
                    onOk: BX.delegate(function (messageBox) {
                        let data = {};
                        data.action = 'delete';
                        data.signedParamsString = this.signedParamsEdit;
                        data.link_utm = this.sign;
                        data.ID = id;
                        BX.ajax({
                            method: 'POST',
                            dataType: 'json',
                            url: this.editUrl,
                            data: data,
                            async: false,
                            onsuccess: BX.delegate(function (result) {
                                if (result.REDIRECT) {
                                    window.location.href = result.REDIRECT;
                                }
                                if (result.STATUS === 'SUCCESS') {
                                    switch(data.action) {
                                        case 'delete':
                                            this.reloadGrid();
                                            messageBox.close();
                                            break;
                                        default:
                                            break;
                                    }
                                }
                                if (result.STATUS === 'ERROR') {
                                    this.processError(result.MESSAGE);
                                }
                            }, this),
                            onfailure: BX.delegate(function () {this.hideLoader();}, this),
                        });
                    }, this), onCancel: function (messageBox) {
                        messageBox.close();
                    }
                }
            );
            popup.show();
        }
    };
})();
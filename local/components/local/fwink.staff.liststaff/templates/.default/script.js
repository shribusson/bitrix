(function () {
    BX.namespace('BX.iTrack.Chart.StaffList');

    BX.iTrack.Chart.StaffList = {
        gridId: null,
        grid: null,
        sign: '',
        init: function(params) {
            this.gridId = params.gridId || null;
            this.serviceUrl = params.serviceUrl || null;
            this.sign = params.sign || '';

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
        add: function () {
            window.BX24.openApplication({
                add: 'new',
                mode: 'pages',
                page: 'staff',
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
        }
    };
})();
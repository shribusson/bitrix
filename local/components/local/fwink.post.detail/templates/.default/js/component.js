(function () {
    'use strict';

    BX.namespace('BX.iTrack.Chart.PostDetail');

    BX.iTrack.Chart.PostDetail = {
        init: function (parameters) {
            this.mode = parameters.mode || 'detail';
            this.result = parameters.result || {};
            this.ajaxUrl = parameters.ajaxUrl || '';
            this.signedParams = parameters.signedParams;
            this.message = parameters.message;
            this.elementId = parameters.elementId;
            this.link_utm = parameters.link_utm;
            this.emptyParentPost = parameters.emptyParentPost || false;
            this.popup = '';
            this.objsvisor = {};
            this.glViewMode = true;
            this.currentPost = (this.result.hasOwnProperty('DEPARTMENT') && parseInt(this.result.DEPARTMENT) > 0) ? this.result.DEPARTMENT : 0;
            this.isManager = this.result.hasOwnProperty('RES') ? this.result.RES.IS_MANAGER_POST === 'Y' : false;
            this.nodes = {
                editBtn: document.querySelector('.js-edit-link'),
                deleteBtn: document.querySelector('.js-delete-link'),
                parentBlock: document.querySelector('.js-parent-block'),
                closeBtn: document.querySelector('.js-close-btn'),
                loader: document.querySelector('#loader'),
                nameBlock: document.querySelector('.js-post-name'),
                isManagerBlock: document.querySelector('.js-post-ismanager'),
                employeesBlock: document.querySelector('.js-post-employees'),
                managerBlock: document.querySelector('.js-post-shief')
            };

            this.bindEvents();
            if(this.mode === 'add') {
                this.toggleEdit();
            }
        },

        bindEvents: function() {
            BX.bind(this.nodes.editBtn, 'click', BX.delegate(this.changeModeClick, this));
            BX.bind(this.nodes.deleteBtn, 'click', BX.delegate(this.confirm, this));
            BX.bind(this.nodes.closeBtn, 'click', BX.delegate(this.closeClick, this));
            if(this.nodes.isManagerBlock) {
                BX.bind(this.nodes.isManagerBlock.querySelector('input[type="checkbox"]'), 'change', BX.delegate(this.changePostRole, this));
            }
        },
        getForm: function() {
            let form = {}
            let objsajax = document.querySelectorAll('[name]');
            for (let [p, v] of Object.entries(objsajax)) {
                (typeof v === 'object' && v !== null)
                    ? form[v.getAttribute('name')] = v.value
                    : ''
            }
            form['ID_JOB_FOLDERB24name'] = document.querySelector('[name="ID_JOB_FOLDERB24"]').options[document.querySelector('[name="ID_JOB_FOLDERB24"]').selectedIndex].text;
            form['link_utm'] = this.link_utm;
            form['IS_MANAGER_POST'] = this.isManager ? 'Y' : 'N';
            return form;
        },
        update: function (data = {}) {
            let form = this.getForm();
            form['action'] = 'update';
            this.sendRequest(form);
        },

        list: function () {
            let data = {};
            data.action = 'list';
            data['link_utm'] = this.link_utm;
            data['signedParamsString'] = this.signedParams;

            this.sendRequest(data);
        },

        confirm: function () {
            let popup = new BX.UI.Dialogs.MessageBox(
                {
                    title: 'Удаление',
                    message: 'Вы действительно хотите удалить должность ' + this.result.RES.NAME_POST + '?',
                    modal: true,
                    buttons: BX.UI.Dialogs.MessageBoxButtons.OK_CANCEL,
                    onOk: BX.delegate(function (messageBox) {
                        this.delete();
                    }, this), onCancel: function (messageBox) {
                        messageBox.close();
                    }
                }
            );
            popup.show();
        },
        processSuccessAdd: function() {
            let popup = new BX.UI.Dialogs.MessageBox(
                {
                    title: 'Новая должность',
                    message: 'Должность успешно добавлена, форма будет закрыта',
                    modal: true,
                    buttons: BX.UI.Dialogs.MessageBoxButtons.OK,
                    onOk: BX.delegate(function (messageBox) {
                        messageBox.close();
                        window.BX24.closeApplication();
                    }, this)
                });
            popup.show();
        },
        processSuccessUpdate: function() {
            this.toggleView();
            let popup = new BX.UI.Dialogs.MessageBox(
                {
                    title: 'Сохранение',
                    message: 'Изменения успешно сохранены',
                    modal: true,
                    buttons: BX.UI.Dialogs.MessageBoxButtons.OK,
                    onOk: BX.delegate(function (messageBox) {
                        messageBox.close();
                    }, this)
                });
            popup.show();
        },
        processSuccessDelete: function() {
            let popup = new BX.UI.Dialogs.MessageBox(
                {
                    title: 'Удаление',
                    message: 'Должность успешно удалена',
                    modal: true,
                    buttons: BX.UI.Dialogs.MessageBoxButtons.OK,
                    onOk: BX.delegate(function (messageBox) {
                        messageBox.close();
                        window.BX24.closeApplication();
                    }, this)
                });
            popup.show();
        },
        processError: function(message) {
            message = message || '';
            let popup = new BX.UI.Dialogs.MessageBox(
                {
                    title: 'Сохранение',
                    message: 'Ошибка: ' + message,
                    modal: true,
                    buttons: BX.UI.Dialogs.MessageBoxButtons.OK,
                    onOk: BX.delegate(function (messageBox) {
                        messageBox.close();
                    }, this)
                });
            popup.show();
        },

        delete: function () {
            let data = {};
            data.action = 'delete';
            data.signedParamsString = this.signedParams;
            data.link_utm = this.link_utm;
            data.ID = this.result.RES.ID;

            this.sendRequest(data);
        },

        add: function () {
            let form = this.getForm();
            form['action'] = 'add';
            this.sendRequest(form);
        },

        showLoader: function() {
            this.nodes.loader.classList.add('active');
        },
        hideLoader: function() {
            this.nodes.loader.classList.remove('active');
        },

        sendRequest: function (data, action = '', element) {
            data._session = 'anysession';
            this.showLoader();
            BX.ajax({
                method: 'POST',
                dataType: 'json',
                url: this.ajaxUrl,
                data: data,
                onsuccess: BX.delegate(function (result) {
                    this.hideLoader();
                    if (result.REDIRECT) {
                        window.location.href = result.REDIRECT;
                    }
                    if (result.STATUS === 'SUCCESS') {
                        switch(data.action) {
                            case 'add':
                                this.processSuccessAdd();
                                break;
                            case 'update':
                                try {
                                    this.updateSupervisorInf(result.SUPERVISOR);
                                } catch (e) {
                                    console.warn('..SUPERVISOR op is faked');
                                }
                                try{
                                    this.changeDiskFolder();
                                    //$('#team-disk-view').val(result.AFTERRECORD.ID_JOB_FOLDERB24name);
                                } catch (e) {}
                                this.processSuccessUpdate();
                                break;
                            case 'delete':
                                this.processSuccessDelete();
                                break;
                            default:
                                this.toggleView();
                                break;
                        }
                    }
                    if (result.STATUS === 'ERROR') {
                        this.processError(result.MESSAGE);
                    }
                }, this),
                onfailure: BX.delegate(function () {this.hideLoader();}, this),
            });
        },

        updateSupervisorInf: function (data) {
            this.objsvisor = {
                svisorinputid: data.SHIEF_ID,
                svisorimg: data.SHIEF_IMG,
                svisorname: data.SHIEF_NAME,
                svisoractivestatusimg: '',
                svisorsection: data.DEPARTMENT_NAME,
                svisorfunction: data.NAME
            }

            var nextstep = 1;


        },

        getData: function () {
            let data = {};
            data['signedParamsString'] = this.signedParams;
            data['link_utm'] = this.link_utm;

            return data;
        },

        removeEmployee: function(id) {
            let block = document.querySelector('[data-id="userblock' + id + '"]');
            block.remove();
            let currentIds = document.querySelector('input[name="ID_STAFF"]').value.split(',');
            console.log(currentIds);
            console.log(id);
            console.log(currentIds.indexOf(id));
            currentIds.splice(currentIds.indexOf(id), 1);
            document.querySelector('input[name="ID_STAFF"]').value = currentIds.join(',');
        },

        changeEmployees: function() {
            // todo: check rights
            if(this.glViewMode){
                return;
            }
            window.BX24.selectUsers(
                function (result) {
                    var datapresent = JSON.stringify(
                        result
                    );
                    // typeof thizdebug !== 'undefined' && thizdebug && console.log(datapresent);
                    var data = {
                        name: '',
                        id: ''
                    };

                    var parentStaff = $('#post_staff_row');
                    var cnt = result.length, iter = -1;
                    var htmlstrings = parentStaff.html();
                    while (cnt--) {
                        iter++;
                        var staffone = result[cnt];
                        if (staffone.photo == '') {
                            //staffone.photo = 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAQDAwMDAgQDAwMEBAQFBgoGBgUFBgwICQcKDgwPDg4MDQ0PERYTDxAVEQ0NExoTFRcYGRkZDxIbHRsYHRYYGRj/2wBDAQQEBAYFBgsGBgsYEA0QGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBj/wAARCAAvADIDASIAAhEBAxEB/8QAGwAAAQUBAQAAAAAAAAAAAAAABgADBQcIAQL/xAAwEAABAwMDAwIEBQUAAAAAAAABAgMEAAURBhIhBzFBIlETFDJhFTNSgZEIQqGx0f/EABgBAAMBAQAAAAAAAAAAAAAAAAQFBgID/8QAIBEAAQQDAAIDAAAAAAAAAAAAAQACAxEEEiEUMQVBUf/aAAwDAQACEQMRAD8A2LGjPTHktMYwe59qJ4dtZhpBAClgeoqGaZskFLMNDqgQpXq58DxUorJPGea7SyEmggsaAAbFVVr/AK12fRGoxYRb5FzmI2l5LRSkNhQBAJJ+rByB7VzSvXjSeqtTsWGPHnQpD4w2qSgJRuHgEeaCdWaCiO9Xr/ddQtJnC4vN/KIQsp+Gn4SUndyOfTx+9Dtx6Twnpny2l3pUG5JUlxlx9zLec8YGcgjmp/z3iXWlYN+LifjiT7palfiR5rBDqEk8p3gciheZAdgL2LJUnJws+aLILbrUBpp5ZW4htKVK/UQOTTNzhplwltkeoDKTT6KT0pTIhBtCO1X6T/FKmyX0KKCDkcUqPsJVoEcxhiK2POwU4r60nPYGmIDodgNLzyUCmbpc4Fptz1wuM1iHFZQpbj7yglKABkkk/bNLjd8TkEFoQprSI0LjHkkYDwLalYzhXGOPJ71H6ehMP6laDCi6htJXvWMH27dxQqx1LtfUqQuPbXGAy0huQw0VkPFBBIWc+fBA5BBBrr+q4Wgon4y/KYCeABIUcvjn0oA7nPmkEkRGXrqqiCYeBtt+q8W1JIyk5Brq+U1BaY1XZtWadZu9nltutLA+IjeCplR7oWPBHappxWxpS1cYGeae1RUwetJQk+2r5pzg/Uf90qbclgvLO8ck+aVG9SncLv4+mxaemy5SHXm47C3wlsFRO0dsD3z/AIrEXUHqXqTqBeBKvs5z5BTxLVvbJQ02nBCc47nnz71tUhCk7VnkkbU4zk1UHUXoNZtT/M3GwSmrNcnF7nWlNlUVxfcZCfUhXB9ScgexovGEbDblgyuIpZwtL9zg3dM+yyTDmMIcdSADuc5SVD24/wC1KSb3db/eF32/TXZK4idyUrOEoXg7AB27nJ+wqUm9Lddaf1bbo86CncpxCRIjSG1g71JGfUoKIAyeRzgDbVhdROhNwt0i1WrRzz9wD61h1l1TbaAcA7jvOCSSojA49h5KuDawOrBfMG63xU5o/XF90rqt+7adujiHkPlTjaVEtvnZ6krR5B+3mtw6X103rPp3bb2ywY7ktsh1sggJUk7TjP8AaSCR9jVGaL/pxhW1xV11nLYeBXg2+Ckhndx+crusHAyEgA+avdpluPGaYYjojsNpCG2kcBAHgAcD9qFyWMfwewtiZzRQ9L1vaz+Uf4pUqVcKcuPV/9k=';
                            staffone.photo = '/local/apps/img/ui-user.svg';
                        }

                        staffone.data_num = iter;
                        // console.log('staffone:', staffone);
                        htmlstrings += `<div class="postdetail-block_user-wrapper" data-id="userblock${staffone.id}">
<a href="javascript:void(0);" class="postdetail-block_user" id="staff-${staffone.id}" data-num="${staffone.data_num}" title="${staffone.name}">
                            <img title="${staffone.name}" src="${staffone.photo}" />
                        </a><div class="postdetail-block_user-delete js-user-delete-btn" data-id="${staffone.id}" onclick="BX.iTrack.Chart.PostDetail.removeEmployee('${staffone.id}');"></div></div>`
                    }


                    parentStaff.html(htmlstrings);

                    //var arIDs = []
                    let currentIds = document.querySelector('input[name="ID_STAFF"]').value.split(',');
                    for (const [key, value] of Object.entries(result)) {
                        currentIds.push(value.id)
                    }
                    let arIDs = currentIds.filter(function(value, index, self) {
                        return self.indexOf(value) === index;
                    });

                    document.querySelector('input[name="ID_STAFF"]').value = arIDs.join(",");

                    /*if(arIDs.length>0){
                        $('.js-post-shief').hide();
                    } else {
                        $('.js-post-shief').show();
                    }*/

                }
            );
        },
        changeShief: function() {
            if(BX.iTrack.Chart.PostDetail.glViewMode){
                return;
            }
            //todo: check rights
            window.BX24.selectUser(
                function (result) {
                    if (result.photo == '') {
                        result.photo = '/local/apps/img/ui-user.svg';
                    }

                    var htmlstrings = `<a href="javascript:void(0);" class="postdetail-block_user" id="shief-${result.id}" title="${result.name}">
                            <img title="${result.name}" src="${result.photo}" />
                        </a>`

                    var parentStaff = $('#post_shief_row');
                    parentStaff.html(htmlstrings);

                    document.querySelector('input[name="ID_SHIEF_POST_USERB24"]').value = result.id;
                    /*if((result.id.toString()).length>0){
                        $('.js-post-employees').hide();
                    } else {
                        $('.js-post-employees').show();
                    }*/
                }
            );
        },
        showEditFunctions(e) {
            if(BX.iTrack.Chart.PostDetail.glViewMode){
                return;
            }

            var divHtml = $(e); // notice "this" instead of a specific #myDiv

            divHtml.find('.js-functions-block-content').css({"display": "none"})
            divHtml.find('textarea').css({"display": "block"});
        },
        closeEditFunctions(e) {
            var divHtml = $(e).parent()
                , t2dxYyxvpdivread = divHtml.find('.js-functions-block-content')
                , t2dxYyxvptexareaedit = divHtml.find('textarea');
            t2dxYyxvpdivread.css({"display": "block"})
                , t2dxYyxvptexareaedit.css({"display": "none"})
                , t2dxYyxvpdivread.find('div').html(t2dxYyxvptexareaedit.val());
        },
        showEditCkp(e) {
            if(BX.iTrack.Chart.PostDetail.glViewMode){
                return;
            }
            var divHtml = $(e); // notice "this" instead of a specific #myDiv

            divHtml.find('.js-ckp-block-content').css({"display": "none"})
            divHtml.find('textarea').css({"display": "block"});
        },
        closeEditCkp(e) {
            var divHtml = $(e).parent()
                , t2dxYyxvpdivread = divHtml.find('.js-ckp-block-content')
                , t2dxYyxvptexareaedit = divHtml.find('textarea');
            t2dxYyxvpdivread.css({"display": "block"})
                , t2dxYyxvptexareaedit.css({"display": "none"})
                , t2dxYyxvpdivread.find('div').html(t2dxYyxvptexareaedit.val());
        },
        changeDiskFolder: function() {
            BX24.callMethod(
                'disk.folder.get',
                {id: $('#team-disk-edit').val()},
                function(result) {
                    if(result.data()) {
                        $('#team-disk-view').attr('href', result.data()['DETAIL_URL']);
                        $('#team-disk-view').html(result.data()['NAME']);
                    }
                });
        },
        changeTitle: function(e) {
            document.getElementById('main_namepost').innerText = e.value;
        },
        showParentPostView: function(html) {
            html = html || '';
            BX.adjust(this.nodes.parentBlock, {html: html});
            if (this.emptyParentPost) {
                BX.adjust(this.nodes.parentBlock, {style: {'display': 'none'}});
            }
            return html;
        },
        getPosts: function() {
            return Object.entries(this.result.RESTDEPARTMENTS).map(([id, value]) => {
                return `<option ${(parseInt(value.id) === parseInt(this.currentPost) ? "selected" : "")} value=${(value.id)}>${
                    (value.name)}</option>`;
            });
        },
        getEditPostsContent: function() {
            return `<div class="postdetail-block">
                        <div class="postdetail-block_title-wrapper">
                            <div class="postdetail-block_title">Вышестоящая должность</div>
                        </div>
                        <div class="postdetail-block_inner-row">
                            <select name="ID_SUPERVISOR_POST" class="postdetail-input" id="ID_SUPERVISOR_POST" class="input-hH9ZkX border-1px-alto"
                                value="${BX.iTrack.Chart.PostDetail.currentPost}">
                                <option value="0">Выбрать должность</option>
                                ${BX.iTrack.Chart.PostDetail.getPosts().join('')}
                            </select>
                        </div>
                    </div>`;
        },
        getViewPostsContent: function(objsvisor) {
            return `<div class="postdetail-block">
                    <div class="postdetail-block_title-wrapper">
                        <div class="postdetail-block_title">РУКОВОДИТЕЛЬ</div>
                    </div>
                    <div class="postdetail-parent_user">
                        <div class="postdetail-parent_photo">
                            <a href="javascript:void(0);" onclick="BX.iTrack.Chart.PostDetail.openUser('${objsvisor.svisorinputid}');">
                                <img id="svisorimg" src="${objsvisor.svisorimg}" />
                            </a>
                        </div>
                        <div>
                            <a href="javascript:void(0);" onclick="BX.iTrack.Chart.PostDetail.openUser('${objsvisor.svisorinputid}');" class="postdetail-parent_username">
                                <div id="svisorname">${objsvisor.svisorname}</div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="postdetail-block">
                    <div class="postdetail-block_title-wrapper">
                        <div class="postdetail-block_title">ВЫШЕСТОЯЩАЯ ДОЛЖНОСТЬ</div>
                    </div>
                    <input type="hidden" name="ID_SUPERVISOR_POST" id="svisorid" value="${objsvisor.svisorinputid}">
                    <div class="postdetail-parent">
                        <a href="javascript:void(0);" onclick="BX.iTrack.Chart.PostDetail.openPost('${objsvisor.svisorinputid}');" id="svisorfunction" class="postdetail-parent_info-post">${objsvisor.svisorfunction}</a>
                    </div>
                </div>`;
        },
        getParentPostInfo: function() {
            let objsvisor = {};
            if (Object.keys(BX.iTrack.Chart.PostDetail.objsvisor).length != 0) {
                objsvisor = {
                    svisorinputid: BX.iTrack.Chart.PostDetail.objsvisor.svisorinputid,
                    svisorimg: BX.iTrack.Chart.PostDetail.objsvisor.svisorimg,
                    svisorname: BX.iTrack.Chart.PostDetail.objsvisor.svisorname,
                    svisoractivestatusimg: '',
                    svisorsection: BX.iTrack.Chart.PostDetail.objsvisor.svisorsection,
                    svisorfunction: BX.iTrack.Chart.PostDetail.objsvisor.svisorfunction
                }
            } else {
                if(this.result.hasOwnProperty('SUPERVISOR')) {
                    objsvisor = {
                        svisorinputid: parseInt(this.result.SUPERVISOR.SHIEF_ID) > 0 ? '' : this.result.SUPERVISOR.SHIEF_ID,
                        svisorimg: this.result.SUPERVISOR.SHIEF_IMG.length ? this.result.SUPERVISOR.SHIEF_IMG : '/local/apps/img/ui-user.svg',
                        svisorname: this.result.SUPERVISOR.SHIEF_NAME.length ? this.result.SHIEF_NAME : '',
                        svisoractivestatusimg: '',
                        svisorsection: this.result.SUPERVISOR.DEPARTMENT_NAME.length ? this.result.SUPERVISOR.DEPARTMENT_NAME : '',
                        svisorfunction: this.result.SUPERVISOR.NAME
                    };
                }
            }
            return objsvisor;
        },
        showParentPostEdit: function (htmlSVSedit) {
            htmlSVSedit = htmlSVSedit || '';
            var _t = $('.js-parent-block');
            _t.html(htmlSVSedit);
            _t.show();
            return htmlSVSedit;
        },
        changeModeClick: function() {
            if (this.glViewMode) {
                this.toggleEdit();
            } else if (!this.glViewMode) {
                if(this.mode === 'add') {
                    this.add();
                } else {
                    this.update();
                }
            }
        },
        toggleEdit: function() {
            this.glViewMode = false;
            $('.js-edit-link').html('Сохранить');
            $('.js-close-btn').html('Отменить');
            $('input[name="NAME_POST"]').prop('disabled', false);
            $('input[name="SORT"]').prop('disabled', false);
            this.nodes.nameBlock.style.display = 'block';
            $('.js-edit-link').animate({"opacity": "1"});
            this.nodes.isManagerBlock.style.display = 'block';
            this.showParentPostEdit(this.getEditPostsContent());
            $('.js-plus-btn').each(function (i, e) {
                $(e).animate({"opacity": "1"});
            });
            $('#team-disk-view').hide(),$('#team-disk-edit').show();
            document.querySelectorAll('.js-user-delete-btn').forEach(function(el) {
               el.style.display = 'block';
            });
        },
        toggleView: function() {
            this.glViewMode = true;
            $('.js-edit-link').html('Редактировать');
            $('input[name="NAME_POST"]').prop('disabled', true);
            $('input[name="SORT"]').prop('disabled', true);
            this.nodes.nameBlock.style.display = 'none';
            this.nodes.isManagerBlock.style.display = 'none';
            this.showParentPostView(this.getViewPostsContent(this.getParentPostInfo()));
            $('.js-plus-btn').each(function (i, e) {
                $(e).animate({"opacity": "0"});
            })
            $('#team-disk-view').show(),$('#team-disk-edit').hide();
            document.querySelectorAll('.js-user-delete-btn').forEach(function(el) {
                el.style.display = 'none';
            });
        },
        changePostRole:  function(e) {
            if(e.target.checked) {
                this.nodes.employeesBlock.style.display = 'none';
                document.querySelector('[name="ID_STAFF"]').value = '';
                BX.adjust(BX('post_staff_row'),{html: ''});
                this.nodes.managerBlock.style.display = 'block';
                this.isManager = true;
            } else {
                this.nodes.employeesBlock.style.display = 'block';
                this.nodes.managerBlock.style.display = 'none';
                document.querySelector('[name="ID_SHIEF_POST_USERB24"]').value = '';
                BX.adjust(BX('post_shief_row'),{html: ''});
                this.isManager = false;
            }
        },
        closeClick: function() {
            if(this.glViewMode || this.mode === 'add') {
                window.BX24.closeApplication();
            } else {
                this.toggleView();
            }
        },
        openPost: function(id) {
            id = id || 0;
            if(parseInt(id) > 0) {
                window.BX24.openApplication({
                    mode: 'pages',
                    page: 'post',
                    element_id: id,
                    link_utm: this.link_utm,
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
                /*window.BX24.openApplication({
                    mode: 'pages',
                    page: 'staff',
                    element_id: id,
                    link_utm: this.link_utm,
                    bx24_width: 870
                });*/
                window.BX24.openPath('/company/personal/user/' + id + '/');
            }
        }
    };
})();

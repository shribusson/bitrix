(function () {
	BX.namespace('BX.iTrack.Chart.Settings');

	BX.iTrack.Chart.Settings = {
		sign: null,
		settings: null,
		saveBtn: null,
		loader: null,
		init(params) {
			this.sign = params.sign || '';
			this.settings = params.settings || {};
			this.loader = document.getElementById('loader');
			this.saveBtn = document.querySelector('.js-save-btn');

			this.initEditUsersSelector();
			this.bindEvents();
		},
		bindEvents() {
			this.saveBtn.addEventListener('click', this.save.bind(this));
		},
		initEditUsersSelector() {
			let users = [];
			if(this.settings.hasOwnProperty('usersCanEdit')) {
				for(let i in this.settings.usersCanEdit) {
					users.push(['user',this.settings.usersCanEdit[i]]);
				}
			}
			const dialog = new window.BX.UI.EntitySelector.TagSelector({
				dialogOptions: {
					context: 'CHART_SETTINGS_CONTEXT',
					entities: [
						{
							id: 'user', // пользователи
							options: {
								inviteEmployeeLink: false // не выводить ссылку "Пригласить сотрудника"
							}
						},
						{
							id: 'department', // структура компании
						}
					],
					multiple: this.multiple ?? true,
					preselectedItems: users,//preselected,
					preload: true,
					allowCreateItem: false
				},
				events: {
					onTagAdd: (event) => {
						//const selector = event.getTarget();
						const { tag } = event.getData();
						this.selectEditUser(tag.id);
					},
					onTagRemove: (event) => {
						const { tag } = event.getData();
						this.removeEditUser(tag.id);
					}
				}
			});
			dialog.renderTo(document.getElementById('settings_users_edit'));
		},
		selectEditUser(userId) {
			if(!this.settings.hasOwnProperty('usersCanEdit')) {
				this.settings.usersCanEdit = [];
			}
			this.settings.usersCanEdit.push(userId);
		},
		removeEditUser(userId) {
			for(let i = 0; i < this.settings.usersCanEdit.length; i++) {
				if(parseInt(this.settings.usersCanEdit[i]) === parseInt(userId)) {
					this.settings.usersCanEdit.splice(i, 1);
					break;
				}
			}
		},
		save() {
			this.showLoader();
			BX.ajax.runAction('local:fwink.api.settings.save', {
				data: {
					sign: this.sign,
					settings: this.settings
				}
			}).then(function(response){
				if(response.hasOwnProperty('status')) {
					if(response.status == 'success') {
						this.processSuccess();
					} else if(response.status == 'error') {
						this.processError(response.message)
					}
				} else {
					this.processError('Внутренняя ошибка');
				}
			}.bind(this));
		},
		showLoader: function() {
			this.loader.classList.add('active');
		},
		hideLoader: function() {
			this.loader.classList.remove('active');
		},
		processError: function(message, title) {
			message = message || '';
			title = title || 'Изменение настроек'
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
			message = message || 'Настройки успешно сохранены';
			title = title || 'Изменение настроек';
			close = close !== false;
			let popup = new BX.UI.Dialogs.MessageBox(
				{
					title: title,
					message: message,
					modal: true,
					buttons: BX.UI.Dialogs.MessageBoxButtons.OK,
					onOk: BX.delegate(function (messageBox) {
						messageBox.close();
					}, this)
				});
			popup.show();
		},
	}
})();
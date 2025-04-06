<?php


namespace Local\Fwink\Helpers;


use Bitrix\Main\Web\Json;

class TemplateForm
{
	public $create_options_item = '';
	public $create_oneselectui = '';
	public $create_multiselectui = '';
	public $tryingoptions_item = '';
	public $create_time_block = '';
	public $simpleblock = '';
	public $create_group_create_info = '';
	public $item_upload = '';
	public $create_button='';
	public $create_input='';
	public $create_onetime_block='';
	public $create_activeinput='';
	public $create_oneactiveinput='';
	public $create_multiactiveinput='';

//	public static function

	function __construct()
	{
		//$this->getCreateOptionsItem();
	}

	/**
	 * @return string
	 */
	public function getCreateActiveinput($inputname='',$blocktitle='',$blockcode=''): string
	{
		$inputname=!empty($inputname)?$inputname:'DEFAULT_NAME_SIMPLEINPUT';
		$opthtml=<<<HTML
                                        <div id="scrum-block" class="social-group-create-options-item">
                                            <div class="social-group-create-options-item-column-left">
                                                <div class="social-group-create-options-item-name">
                                                    SCRUM мастер
                                                </div>
                                            </div>
                                            <div class="social-group-create-options-item-column-right">
                                                <div class="social-group-create-options-item-column-one social-group-create-form-control-block">
										<span id="main-user-selector-group_create_scrum_master_mqleQW" data-multi="0"
                                              class="main-user-selector-wrap">
			<input type="hidden" id="SCRUM_MASTER_CODE" name="SCRUM_MASTER_CODE" value="">

	<script type="text/javascript">
	BX.ready(function () {
        new BX.UI.TileSelector({
            "containerId": "ui-tile-selector-group_create_scrum_master_mqleQW",
            "id": "group_create_scrum_master_mqleQW",
            "duplicates": false,
            "readonly": false,
            "multiple": false,
            "manualInputEnd": true,
            "fireClickEvent": false,
            "caption": "\u041d\u0430\u0437\u043d\u0430\u0447\u0438\u0442\u044c \u043c\u0430\u0441\u0442\u0435\u0440\u0430",
            "captionMore": "\u0421\u043c\u0435\u043d\u0438\u0442\u044c"
        });
    });

    BX.message({
        UI_TILE_SELECTOR_MORE: 'еще #NUM#'
    });
</script>
<span id="ui-tile-selector-group_create_scrum_master_mqleQW" class="ui-tile-selector-selector-wrap">
	<span id="ui-tile-selector-group_create_scrum_master_mqleQW-mask" class="ui-tile-selector-selector-mask"></span>
	<script data-role="tile-template" type="text/html">
			<span data-role="tile-item" data-bx-id="%id%" data-bx-data="%data%"
                  class="ui-tile-selector-item ui-tile-selector-item-%type% ui-tile-selector-item-readonly-%readonly%"
                  style="%style%">
		<span data-role="tile-item-name">%name%</span>
							<span data-role="remove" class="ui-tile-selector-item-remove"></span>
			</span>
		</script>

	<script data-role="popup-category-template" type="text/html">
		<div class="ui-tile-selector-searcher-sidebar-item">%name%</div>
	</script>

	<script data-role="popup-item-template" type="text/html">
		<div class="ui-tile-selector-searcher-content-item" title="%name%">%name%</div>
	</script>

	<script data-role="popup-template" type="text/html">
		<div class="ui-tile-selector-searcher">
			<div class="ui-tile-selector-searcher-container">
				<div data-role="popup-title" class="ui-tile-selector-searcher-title"></div>
				<div class="ui-tile-selector-searcher-inner">
					<div class="ui-tile-selector-searcher-main ui-tile-selector-searcher-inner-shadow">
						<div data-role="popup-item-list" class="ui-tile-selector-searcher-content"
                             style="display: none;"></div>
						<svg data-role="popup-loader" class="ui-tile-selector-searcher-circular" viewBox="25 25 50 50">
							<circle class="ui-tile-selector-searcher-path" cx="50" cy="50" r="20" fill="none"
                                    stroke-miterlimit="10"/>
							<circle class="ui-tile-selector-searcher-inner-path" cx="50" cy="50" r="20" fill="none"
                                    stroke-miterlimit="10"/>
						</svg>
					</div>
					<div data-role="popup-category-list" class="ui-tile-selector-searcher-sidebar"
                         style="display: none;"></div>
				</div>
			</div>
		</div>
	</script>

	<span data-role="tile-container" class="ui-tile-selector-selector">
				<span data-role="tile-more" class="ui-tile-selector-more" style="display: none;">
			<span data-role="tile-item-name">...</span>
		</span>
		<input data-role="tile-input" type="text" class="ui-tile-selector-input" autocomplete="off"
               style="display: none;" id="SCRUM_MASTER_CODE-group_create_scrum_master_mqleQW-search-input"
               data-bxchangehandler="Y">

					<span class="ui-tile-selector-select-container">
				<span data-role="tile-select" class="ui-tile-selector-select">
											Назначить мастера									</span>
			</span>
					</span>
	</span>
                                            <!--'start_frame_cache_RnsMPi'--><script>
	BX.ready(function () {

        var f = function (params) {
            var selectorId = 'group_create_scrum_master_mqleQW';
            var inputId = (
                BX.type.isNotEmptyObject(params)
                && BX.type.isNotEmptyString(params.inputId)
                    ? params.inputId
                    : 'main-user-selector-group_create_scrum_master_mqleQW');
            var inputBoxId = false;
            var inputContainerId = false;
            var containerId = (typeof params != 'undefined' && params.containerId != 'undefined' ? params.containerId : false);
            var bindId = (containerId ? containerId : inputId);
            var openDialogWhenInit = (
                typeof params == 'undefined'
                || typeof params.openDialogWhenInit == 'undefined'
                || !!params.openDialogWhenInit
            );

            var fieldName = false;

            if (
                BX.type.isNotEmptyObject(params)
                && typeof params.id != 'undefined'
                && params.id != selectorId
            ) {
                return;
            }

            BX.Main.SelectorV2.create({
                apiVersion: 3,
                id: selectorId,
                fieldName: fieldName,
                pathToAjax: '/bitrix/components/bitrix/main.ui.selector/ajax.php',
                inputId: inputId,
                inputBoxId: inputBoxId,
                inputContainerId: inputContainerId,
                bindId: bindId,
                containerId: containerId,
                tagId: BX(''),
                openDialogWhenInit: openDialogWhenInit,
                bindNode: BX('main-user-selector-group_create_scrum_master_mqleQW'),
                options: {
                    'useNewCallback': 'Y',
                    'eventInit': 'BX.Main.User.SelectorController::init',
                    'eventOpen': 'BX.Main.User.SelectorController::open',
                    'userSearchArea': false,
                    'contextCode': 'U',
                    'context': 'GROUP_INVITE_OWNER',
                    'lazyLoad': 'N',
                    'multiple': 'N',
                    'extranetContext': false,
                    'useSearch': 'N',
                    'userNameTemplate': '#NAME# #LAST_NAME#',
                    'useClientDatabase': 'Y',
                    'allowEmailInvitation': 'N',
                    'enableAll': 'N',
                    'enableDepartments': 'Y',
                    'enableSonetgroups': 'N',
                    'departmentSelectDisable': 'Y',
                    'allowAddUser': 'N',
                    'allowAddCrmContact': 'N',
                    'allowAddSocNetGroup': 'N',
                    'allowSearchEmailUsers': 'N',
                    'allowSearchCrmEmailUsers': 'N',
                    'allowSearchNetworkUsers': 'N'
                },
                callback: {
                    select: BX.Main.User.SelectorController.select,
                    unSelect: BX.Main.User.SelectorController.unSelect,
                    openDialog: BX.Main.User.SelectorController.openDialog,
                    closeDialog: BX.Main.User.SelectorController.closeDialog,
                    openSearch: BX.Main.User.SelectorController.openSearch,
                    closeSearch: BX.Main.User.SelectorController.closeSearch,
                    openEmailAdd: null,
                    closeEmailAdd: null
                },
                callbackBefore: {
                    select: null,
                    openDialog: null,
                    context: null,
                },
                items: {
                    selected: [],
                    undeletable: [],
                    hidden: []
                },
                entities: {
                    users: '',
                    groups: '',
                    sonetgroups: '',
                    department: ''
                }
            });

            BX.removeCustomEvent(window, "BX.Main.User.SelectorController::init", arguments.callee);
        };

        BX.addCustomEvent(window, "BX.Main.User.SelectorController::init", f);

    });
</script>

                                            <!--'end_frame_cache_RnsMPi'-->
	<script type="text/javascript">
		BX.ready(function () {
            try {
                new BX.Main.User.Selector({
                    "containerId": "main-user-selector-group_create_scrum_master_mqleQW",
                    "id": "group_create_scrum_master_mqleQW",
                    "duplicates": false,
                    "inputName": "SCRUM_MASTER_CODE",
                    "isInputMultiple": false,
                    "useSymbolicId": true,
                    "openDialogWhenInit": false,
                    "lazyload": false
                });
            } catch (e) {
                console.log(e.name + ': ' + e.message);
            }
        });
	</script>
</span></div>
                                            </div>
                                        </div>
	
		
HTML;
		$this->create_activeinput=$opthtml;
		return $opthtml;
	}

	public function getCreateOneActiveinput($inputname='',$blocktitle='',$blockcode=''): string
	{
		$inputname=!empty($inputname)?$inputname:'DEFAULT_NAME_SIMPLEINPUT';
		$miniblocktitle=!empty($blocktitle)?mb_strtolower($blocktitle):'-no title-';
		$miniblockcode=!empty($blockcode)?mb_strtolower($blockcode):'-no title-';
		$opthtml=<<<HTML
                                        <div id="{$miniblockcode}-block" class="social-group-create-options-item">
                                            <div class="social-group-create-options-item-column-left">
                                                <div class="social-group-create-options-item-name">
                                                    {$blocktitle}
                                                </div>
                                            </div>
                                            <div class="social-group-create-options-item-column-right">
                                                <div class="social-group-create-options-item-column-one social-group-create-form-control-block">
										<span id="main-user-selector-group_create_{$miniblockcode}_master_mqleQW"
                                              class="main-user-selector-wrap">
			<input type="hidden" id="{$blockcode}" name="{$blockcode}" value="">

	<script type="text/javascript">
	BX.ready(function () {
        new BX.UI.TileSelector({
            "containerId": "ui-tile-selector-group_create_{$miniblockcode}_master_mqleQW",
            "id": "group_create_{$miniblockcode}_master_mqleQW",
            "duplicates": false,
            "readonly": false,
            "multiple": false,
            "manualInputEnd": true,
            "fireClickEvent": false,
            "caption": "{$blocktitle}",
            "captionMore": "\u0421\u043c\u0435\u043d\u0438\u0442\u044c"
        });
    });

    BX.message({
        UI_TILE_SELECTOR_MORE: 'еще #NUM#'
    });
</script>
<span id="ui-tile-selector-group_create_{$miniblockcode}_master_mqleQW" class="ui-tile-selector-selector-wrap">
	<span id="ui-tile-selector-group_create_{$miniblockcode}_master_mqleQW-mask" class="ui-tile-selector-selector-mask"></span>
	<script data-role="tile-template" type="text/html">
			<span data-role="tile-item" data-bx-id="%id%" data-bx-data="%data%"
                  class="ui-tile-selector-item ui-tile-selector-item-%type% ui-tile-selector-item-readonly-%readonly%"
                  style="%style%">
		<span data-role="tile-item-name">%name%</span>
							<span data-role="remove" class="ui-tile-selector-item-remove"></span>
			</span>
		</script>

	<script data-role="popup-category-template" type="text/html">
		<div class="ui-tile-selector-searcher-sidebar-item">%name%</div>
	</script>

	<script data-role="popup-item-template" type="text/html">
		<div class="ui-tile-selector-searcher-content-item" title="%name%">%name%</div>
	</script>

	<script data-role="popup-template" type="text/html">
		<div class="ui-tile-selector-searcher">
			<div class="ui-tile-selector-searcher-container">
				<div data-role="popup-title" class="ui-tile-selector-searcher-title"></div>
				<div class="ui-tile-selector-searcher-inner">
					<div class="ui-tile-selector-searcher-main ui-tile-selector-searcher-inner-shadow">
						<div data-role="popup-item-list" class="ui-tile-selector-searcher-content"
                             style="display: none;"></div>
						<svg data-role="popup-loader" class="ui-tile-selector-searcher-circular" viewBox="25 25 50 50">
							<circle class="ui-tile-selector-searcher-path" cx="50" cy="50" r="20" fill="none"
                                    stroke-miterlimit="10"/>
							<circle class="ui-tile-selector-searcher-inner-path" cx="50" cy="50" r="20" fill="none"
                                    stroke-miterlimit="10"/>
						</svg>
					</div>
					<div data-role="popup-category-list" class="ui-tile-selector-searcher-sidebar"
                         style="display: none;"></div>
				</div>
			</div>
		</div>
	</script>

	<span data-role="tile-container" class="ui-tile-selector-selector">
				<span data-role="tile-more" class="ui-tile-selector-more" style="display: none;">
			<span data-role="tile-item-name">...</span>
		</span>
		<input data-role="tile-input" type="text" class="ui-tile-selector-input" autocomplete="off"
               style="display: none;" id="{$blockcode}-group_create_{$miniblockcode}_master_mqleQW-search-input"
               data-bxchangehandler="Y">

					<span class="ui-tile-selector-select-container">
				<span data-role="tile-select" class="ui-tile-selector-select">
											Установить {$miniblocktitle}									</span>
			</span>
					</span>
	</span>
                                            <!--'start_frame_cache_RnsMPi'--><script>
	BX.ready(function () {

        var f = function (params) {
            var selectorId = 'group_create_{$miniblockcode}_master_mqleQW';
            var inputId = (
                BX.type.isNotEmptyObject(params)
                && BX.type.isNotEmptyString(params.inputId)
                    ? params.inputId
                    : 'main-user-selector-group_create_{$miniblockcode}_master_mqleQW');
            var inputBoxId = false;
            var inputContainerId = false;
            var containerId = (typeof params != 'undefined' && params.containerId != 'undefined' ? params.containerId : false);
            var bindId = (containerId ? containerId : inputId);
            var openDialogWhenInit = (
                typeof params == 'undefined'
                || typeof params.openDialogWhenInit == 'undefined'
                || !!params.openDialogWhenInit
            );

            var fieldName = false;

            if (
                BX.type.isNotEmptyObject(params)
                && typeof params.id != 'undefined'
                && params.id != selectorId
            ) {
                return;
            }

            BX.Main.SelectorV2.create({
                apiVersion: 3,
                id: selectorId,
                fieldName: fieldName,
                pathToAjax: '/bitrix/components/bitrix/main.ui.selector1/ajax.php',
                inputId: inputId,
                inputBoxId: inputBoxId,
                inputContainerId: inputContainerId,
                bindId: bindId,
                containerId: containerId,
                tagId: BX(''),
                openDialogWhenInit: openDialogWhenInit,
                bindNode: BX('main-user-selector-group_create_{$miniblockcode}_master_mqleQW'),
                options: {
                    'useNewCallback': 'Y',
                    'eventInit': 'BX.Main.User.SelectorController::init',
                    'eventOpen': 'BX.Main.User.SelectorController::open',
                    'userSearchArea': false,
                    'contextCode': 'U',
                    'context': 'GROUP_INVITE_OWNER',
                    'lazyLoad': 'N',
                    'multiple': 'N',
                    'extranetContext': false,
                    'useSearch': 'N',
                    'userNameTemplate': '#NAME# #LAST_NAME#',
                    'useClientDatabase': 'Y',
                    'allowEmailInvitation': 'N',
                    'enableAll': 'N',
                    'enableDepartments': 'Y',
                    'enableSonetgroups': 'N',
                    'departmentSelectDisable': 'Y',
                    'allowAddUser': 'N',
                    'allowAddCrmContact': 'N',
                    'allowAddSocNetGroup': 'N',
                    'allowSearchEmailUsers': 'N',
                    'allowSearchCrmEmailUsers': 'N',
                    'allowSearchNetworkUsers': 'N'
                },
                callback: {
                    select: BX.Main.User.SelectorController.select,
                    unSelect: BX.Main.User.SelectorController.unSelect,
                    openDialog: BX.Main.User.SelectorController.openDialog,
                    closeDialog: BX.Main.User.SelectorController.closeDialog,
                    openSearch: BX.Main.User.SelectorController.openSearch,
                    closeSearch: BX.Main.User.SelectorController.closeSearch,
                    openEmailAdd: null,
                    closeEmailAdd: null
                },
                callbackBefore: {
                    select: null,
                    openDialog: null,
                    context: null,
                },
                items: {
                    selected: [],
                    undeletable: [],
                    hidden: []
                },
                entities: {
                    users: '',
                    groups: '',
                    sonetgroups: '',
                    department: ''
                }
            });

            BX.removeCustomEvent(window, "BX.Main.User.SelectorController::init", arguments.callee);
        };

        BX.addCustomEvent(window, "BX.Main.User.SelectorController::init", f);

    });
</script>

                                            <!--'end_frame_cache_RnsMPi'-->
	<script type="text/javascript">
		BX.ready(function () {
            try {
                new BX.Main.User.Selector({
                    "containerId": "main-user-selector-group_create_{$miniblockcode}_master_mqleQW",
                    "id": "group_create_{$miniblockcode}_master_mqleQW",
                    "duplicates": false,
                    "inputName": "{$blockcode}",
                    "isInputMultiple": false,
                    "useSymbolicId": true,
                    "openDialogWhenInit": false,
                    "lazyload": false
                });
            } catch (e) {
                console.log(e.name + ': ' + e.message);
            }
        });
	</script>
</span></div>
                                            </div>
                                        </div>
	
		
HTML;
		$this->create_oneactiveinput=$opthtml;
		return $opthtml;
	}

	public function getCreateMultiactiveinput($inputname='',$blocktitle='',$blockcode=''): string
	{
		$inputname=!empty($inputname)?$inputname:'DEFAULT_NAME_SIMPLEINPUT';
		$miniblocktitle=!empty($blocktitle)?mb_strtolower($blocktitle):'-no title-';
		$miniblockcode=!empty($blockcode)?mb_strtolower($blockcode):'-no title-';
		$opthtml=<<<HTML
                                        <div id="{$miniblockcode}-block" class="social-group-create-options-item">
                                            <div class="social-group-create-options-item-column-left">
                                                <div class="social-group-create-options-item-name">
                                                    {$blocktitle}
                                                </div>
                                            </div>
                                            <div class="social-group-create-options-item-column-right">
                                                <div class="social-group-create-options-item-column-one social-group-create-form-control-block">
										<span id="main-user-selector-group_create_{$miniblockcode}_master_mqleQW"
                                              class="main-user-selector-wrap">
			<input type="hidden" id="{$blockcode}" name="{$blockcode}" value="">

	<script type="text/javascript">
	BX.ready(function () {
        new BX.UI.TileSelector({
            "containerId": "ui-tile-selector-group_create_{$miniblockcode}_master_mqleQW",
            "id": "group_create_{$miniblockcode}_master_mqleQW",
            "duplicates": false,
            "readonly": false,
            "multiple": true,
            "manualInputEnd": true,
            "fireClickEvent": false,
            "caption": "{$blocktitle}",
            "captionMore": "\u0421\u043c\u0435\u043d\u0438\u0442\u044c"
        });
    });

    BX.message({
        UI_TILE_SELECTOR_MORE: 'еще #NUM#'
    });
</script>
<span id="ui-tile-selector-group_create_{$miniblockcode}_master_mqleQW" class="ui-tile-selector-selector-wrap">
	<span id="ui-tile-selector-group_create_{$miniblockcode}_master_mqleQW-mask" class="ui-tile-selector-selector-mask"></span>
	<script data-role="tile-template" type="text/html">
			<span data-role="tile-item" data-bx-id="%id%" data-bx-data="%data%"
                  class="ui-tile-selector-item ui-tile-selector-item-%type% ui-tile-selector-item-readonly-%readonly%"
                  style="%style%">
		<span data-role="tile-item-name">%name%</span>
							<span data-role="remove" class="ui-tile-selector-item-remove"></span>
			</span>
		</script>

	<script data-role="popup-category-template" type="text/html">
		<div class="ui-tile-selector-searcher-sidebar-item">%name%</div>
	</script>

	<script data-role="popup-item-template" type="text/html">
		<div class="ui-tile-selector-searcher-content-item" title="%name%">%name%</div>
	</script>

	<script data-role="popup-template" type="text/html">
		<div class="ui-tile-selector-searcher">
			<div class="ui-tile-selector-searcher-container">
				<div data-role="popup-title" class="ui-tile-selector-searcher-title"></div>
				<div class="ui-tile-selector-searcher-inner">
					<div class="ui-tile-selector-searcher-main ui-tile-selector-searcher-inner-shadow">
						<div data-role="popup-item-list" class="ui-tile-selector-searcher-content"
                             style="display: none;"></div>
						<svg data-role="popup-loader" class="ui-tile-selector-searcher-circular" viewBox="25 25 50 50">
							<circle class="ui-tile-selector-searcher-path" cx="50" cy="50" r="20" fill="none"
                                    stroke-miterlimit="10"/>
							<circle class="ui-tile-selector-searcher-inner-path" cx="50" cy="50" r="20" fill="none"
                                    stroke-miterlimit="10"/>
						</svg>
					</div>
					<div data-role="popup-category-list" class="ui-tile-selector-searcher-sidebar"
                         style="display: none;"></div>
				</div>
			</div>
		</div>
	</script>

	<span data-role="tile-container" class="ui-tile-selector-selector">
				<span data-role="tile-more" class="ui-tile-selector-more" style="display: none;">
			<span data-role="tile-item-name">...</span>
		</span>
		<input data-role="tile-input" type="text" class="ui-tile-selector-input" autocomplete="off"
               style="display: none;" id="{$blockcode}-group_create_{$miniblockcode}_master_mqleQW-search-input"
               data-bxchangehandler="Y">

					<span class="ui-tile-selector-select-container">
				<span data-role="tile-select" class="ui-tile-selector-select">
											Установить {$miniblocktitle}									</span>
			</span>
					</span>
	</span>
                                            <!--'start_frame_cache_RnsMPi'--><script>
	BX.ready(function () {

        var f = function (params) {
            var selectorId = 'group_create_{$miniblockcode}_master_mqleQW';
            var inputId = (
                BX.type.isNotEmptyObject(params)
                && BX.type.isNotEmptyString(params.inputId)
                    ? params.inputId
                    : 'main-user-selector-group_create_{$miniblockcode}_master_mqleQW');
            var inputBoxId = false;
            var inputContainerId = false;
            var containerId = (typeof params != 'undefined' && params.containerId != 'undefined' ? params.containerId : false);
            var bindId = (containerId ? containerId : inputId);
            var openDialogWhenInit = (
                typeof params == 'undefined'
                || typeof params.openDialogWhenInit == 'undefined'
                || !!params.openDialogWhenInit
            );

            var fieldName = false;

            if (
                BX.type.isNotEmptyObject(params)
                && typeof params.id != 'undefined'
                && params.id != selectorId
            ) {
                return;
            }

            BX.Main.SelectorV2.create({
                apiVersion: 3,
                id: selectorId,
                fieldName: fieldName,
                pathToAjax: '/bitrix/components/bitrix/main.ui.selector1/ajax.php',
                inputId: inputId,
                inputBoxId: inputBoxId,
                inputContainerId: inputContainerId,
                bindId: bindId,
                containerId: containerId,
                tagId: BX(''),
                openDialogWhenInit: openDialogWhenInit,
                bindNode: BX('main-user-selector-group_create_{$miniblockcode}_master_mqleQW'),
                options: {
                    'useNewCallback': 'Y',
                    'eventInit': 'BX.Main.User.SelectorController::init',
                    'eventOpen': 'BX.Main.User.SelectorController::open',
                    'userSearchArea': false,
                    'contextCode': 'U',
                    'context': 'GROUP_INVITE_OWNER',
                    'lazyLoad': 'N',
                    'multiple': 'N',
                    'extranetContext': false,
                    'useSearch': 'N',
                    'userNameTemplate': '#NAME# #LAST_NAME#',
                    'useClientDatabase': 'Y',
                    'allowEmailInvitation': 'N',
                    'enableAll': 'N',
                    'enableDepartments': 'Y',
                    'enableSonetgroups': 'N',
                    'departmentSelectDisable': 'Y',
                    'allowAddUser': 'N',
                    'allowAddCrmContact': 'N',
                    'allowAddSocNetGroup': 'N',
                    'allowSearchEmailUsers': 'N',
                    'allowSearchCrmEmailUsers': 'N',
                    'allowSearchNetworkUsers': 'N'
                },
                callback: {
                    select: BX.Main.User.SelectorController.select,
                    unSelect: BX.Main.User.SelectorController.unSelect,
                    openDialog: BX.Main.User.SelectorController.openDialog,
                    closeDialog: BX.Main.User.SelectorController.closeDialog,
                    openSearch: BX.Main.User.SelectorController.openSearch,
                    closeSearch: BX.Main.User.SelectorController.closeSearch,
                    openEmailAdd: null,
                    closeEmailAdd: null
                },
                callbackBefore: {
                    select: null,
                    openDialog: null,
                    context: null,
                },
                items: {
                    selected: [],
                    undeletable: [],
                    hidden: []
                },
                entities: {
                    users: '',
                    groups: '',
                    sonetgroups: '',
                    department: ''
                }
            });

            BX.removeCustomEvent(window, "BX.Main.User.SelectorController::init", arguments.callee);
        };

        BX.addCustomEvent(window, "BX.Main.User.SelectorController::init", f);

    });
</script>

                                            <!--'end_frame_cache_RnsMPi'-->
	<script type="text/javascript">
		BX.ready(function () {
            try {
                new BX.Main.User.Selector({
                    "containerId": "main-user-selector-group_create_{$miniblockcode}_master_mqleQW",
                    "id": "group_create_{$miniblockcode}_master_mqleQW",
                    "duplicates": false,
                    "inputName": "{$blockcode}",
                    "isInputMultiple": false,
                    "useSymbolicId": true,
                    "openDialogWhenInit": false,
                    "lazyload": false
                });
            } catch (e) {
                console.log(e.name + ': ' + e.message);
            }
        });
	</script>
</span></div>
                                            </div>
                                        </div>
	
		
HTML;
		$this->create_multiactiveinput=$opthtml;
		return $opthtml;
	}


	public function getCreateButton($inputname='',$blocktitle='',$blockcode='')
	{
		$inputname=!empty($inputname)?$inputname:'DEFAULT_NAME_SIMPLEINPUT';
		$opthtml=<<<HTML
<div class="sonet-slider-footer-fixed">
                                        <!--<input type="hidden" name="SONET_USER_ID" value="1">
                                        <input type="hidden" name="SONET_GROUP_ID" id="SONET_GROUP_ID" value="0">-->
                                        <input type="hidden" name="TAB" value="">
                                        <div class="social-group-create-buttons"><span class="sonet-ui-btn-cont sonet-ui-btn-cont-center"><button class="ui-btn ui-btn-success ui-btn-md" id="sonet_group_create_popup_form_button_submit" bx-action-type="create">Создать </button><button class="ui-btn ui-btn-link" id="sonet_group_create_popup_form_button_step_2_back">Отмена</button></span></div>
                                    </div>		
		
HTML;
		$this->create_button = $opthtml;
		return $opthtml;

	}

	/**
	 * @return string
	 */
	public function getCreateOptionsItem($inputname='',$blocktitle='',$blockcode='',$incArray=[])
	{

		if(empty($incArray)){
			$incArray=[
				123=>'asdf',
				234=>'gdwq'
			];
		}

		$customhtml='';
        if(empty($customhtml)){
			$customhtml='<select name="'.$blockcode.'" id="'.$blockcode.'"
                                                            class="
											social-group-create-field social-group-create-field-select">';
			$customhtml .= '<option value="">-</option>';
										foreach ($incArray as $key=>$value) {
											$customhtml .= '<option value="' . $key . '">' . $value . '</option>';
										}
			$customhtml.='</select>';
				}
		$foobar = ``;
		$opthtml = <<<HTML
	    <div id="scrum-block" class="social-group-create-options-item">
	     									<div class="social-group-create-options-item-column-left">
                                                <div class="social-group-create-options-item-name">
                                                    {$blocktitle}
                                                </div>
                                            </div>
               								<div class="social-group-create-options-item-column-right">
                                                <div class="social-group-create-field-block">
                                                    <!--<select name="SCRUM_SPRINT_DURATION" id="SCRUM_SPRINT_DURATION"
                                                            class="
											social-group-create-field social-group-create-field-select">
                                                        <option value="604800">1 неделя</option>
                                                        <option value="1209600">2 недели</option>
                                                        <option value="1814400">3 недели</option>
                                                        <option value="2419200">4 недели</option>
                                                    </select>-->
                                                    {$customhtml}
                                                </div>
                                            </div>
         </div>
HTML;
		$this->create_options_item = $opthtml;
		return $opthtml;
//        return `1234`;
	}


	/**
	 * @return string
	 */
	public function getCreateInput($inputname='',$blocktitle='',$blockcode=''): string
	{
		$opthtml = '';
		$two_block_items = [
			['NAME' => 'Первый вариант', 'VALUE' => '1'],
			['NAME' => 'Второй вариант', 'VALUE' => '2'],
		];
		$two_block_title = 'is input';
		$two_block_code = $blockcode;

		$inputname=!empty($inputname)?$inputname:'DEFAULT_NAME_SIMPLEINPUT';
		$blocktitle=!empty($blocktitle)?$blocktitle:$two_block_title;


		$jsontwo_block_items = Json::encode($two_block_items);
		$jsondata_params = Json::encode(['isMulti' => false]);
		$opthtml = <<<HTML
                <div id="{$blockcode}" class="social-group-create-options-item">
                                                            <div class="social-group-create-options-item-column-left">
                                                                <div class="social-group-create-options-item-name">
                                                                    {$blocktitle}
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="social-group-create-options-item-column-right">
                                                                <div data-name="BLOCK_TWO_SELECT_SINGLE" 
                                                                		id="{$blockcode}" class="main-ui-control1 main-ui-select1">

                                                                    <span class="main-ui-control1 main-ui-text main-grid-panel-date1">
																	<span class="main-ui-text-button"></span>
																	<input 
																			type="text" 
																			name="{$inputname}" 
																			autocomplete="off" data-time="" 
																			class="main-ui-control-input main-ui-date-input" 
																			value=""
																			style="border-width: thin;">
																		<div class="main-ui-control-value-delete main-ui-hide">
																			<span class="main-ui-control-value-delete-item"></span>
																		</div>
																	</span>
                                                                </div>

                                                            </div>
                                                        </div>
HTML;
		$this->create_input = $opthtml;
		return $opthtml;
	}

	/**
	 * @return string
	 */
	public function getCreateTimeBlock($inputname1='',$inputname2='')
	{
		$inputname1=!empty($inputname1)?$inputname1:'DEFAULT_NAME_SIMPLEINPUT1';
		$inputname2=!empty($inputname2)?$inputname2:'DEFAULT_NAME_SIMPLEINPUT2';
		$opthtml = <<<HTML
<div id="IS_PROJECT_block" class="">
                                            <div class="social-group-create-options-item sgcp-flex-project1">
                                                <div class="social-group-create-options-item-column-left">
                                                    <div class="social-group-create-options-item-name">Срок проекта
                                                    </div>
                                                </div>
                                                <div class="social-group-create-options-item-column-right">
                                                    <div class="social-group-create-options-item-column-one">
                                                        <div class="social-group-create-field-container social-group-create-field-container-datetime social-group-create-field-datetime">
													<span class="main-ui-control main-ui-date main-grid-panel-date">
														<span class="main-ui-date-button"></span>
														<input type="text" name="{$inputname1}" autocomplete="off"
                                                               data-time=""
                                                               class="main-ui-control-input main-ui-date-input"
                                                               value="">
														<div class="main-ui-control-value-delete main-ui-hide">
															<span class="main-ui-control-value-delete-item"></span>
														</div>
													</span>
                                                            <div class="social-group-create-field-block social-group-create-field-block-between"></div>
                                                            <span class="main-ui-control main-ui-date main-grid-panel-date">
														<span class="main-ui-date-button"></span>
														<input type="text" name="{$inputname2}" autocomplete="off"
                                                               data-time=""
                                                               class="main-ui-control-input main-ui-date-input"
                                                               value="">
														<div class="main-ui-control-value-delete main-ui-hide">
															<span class="main-ui-control-value-delete-item"></span>
														</div>
													</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div></div>
                                        </div>
HTML;

		$this->create_time_block = $opthtml;
		return $opthtml;
	}

	/**
	 * @return string
	 */
	public function getCreateOneTimeBlock($inputname='',$blocktitle='',$blockcode='')
	{
		$blocktitle=!empty($blocktitle)?$blocktitle:'Time';
		$blockcode=!empty($blockcode)?$blockcode:'blockcodeonetime';
		$inputname=!empty($inputname)?$inputname:'DEFAULT_NAME_SIMPLEINPUT';
		$opthtml = <<<HTML
<div id="IS_PROJECT_block" class="">
                                            <div class="social-group-create-options-item sgcp-flex-project1">
                                                <div class="social-group-create-options-item-column-left">
                                                    <div class="social-group-create-options-item-name">{$blocktitle}
                                                    </div>
                                                </div>
                                                <div class="social-group-create-options-item-column-right">
                                                    <div class="social-group-create-options-item-column-one">
                                                        <div class="social-group-create-field-container social-group-create-field-container-datetime social-group-create-field-datetime">
													<span class="main-ui-control main-ui-date main-grid-panel-date">
														<span class="main-ui-date-button"></span>
														<input type="text" name="{$inputname}" autocomplete="off"
                                                               data-time=""
                                                               class="main-ui-control-input main-ui-date-input"
                                                               value="">
														<div class="main-ui-control-value-delete main-ui-hide">
															<span class="main-ui-control-value-delete-item"></span>
														</div>
													</span>
                                                            <div class="social-group-create-field-block social-group-create-field-block-between"></div>
                                                           
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div></div>
                                        </div>
HTML;

		$this->create_onetime_block = $opthtml;
		return $opthtml;
	}


	/**
	 * @return string
	 */
	public function getCreateGroupCreateInfo()
	{
		$opthtml = <<<HTML
  <div class="social-group-create-info">
                                        <div class="social-group-create-info-panel">
                                            <div class="social-group-create-info-panel-title">
                                                <input type="text" id="GROUP_NAME_input" name="GROUP_NAME" value=""
                                                       placeholder="Введите название  либо заголовок">
                                            </div>
                                        </div>
                                        <div class="social-group-create-info-editor">
                                            <div class="social-group-create-add-task">
                                                <textarea name="GROUP_DESCRIPTION"
                                                          class="social-group-create-description" cols="30"
                                                          rows="10"></textarea>
                                                <div class="social-group-create-separator-line"></div>
                                            </div>
                                        </div>
                                    </div>
HTML;


		$this->create_group_create_info = $opthtml;
		return $opthtml;
	}


	/**
	 * @return string
	 */
	public function getCreateOneselectui()
	{
		$opthtml = '';
		$two_block_items = [
			['NAME' => 'Первый вариант', 'VALUE' => '1'],
			['NAME' => 'Второй вариант', 'VALUE' => '2'],
		];
		$two_block_title = 'is one select UI';
		$two_block_code = 'two_block_code';
		$jsontwo_block_items = Json::encode($two_block_items);
		$jsondata_params = Json::encode(['isMulti' => false]);
		$opthtml = <<<HTML
                <div id="{$two_block_code}" class="social-group-create-options-item">
                                                            <div class="social-group-create-options-item-column-left">
                                                                <div class="social-group-create-options-item-name">
                                                                    {$two_block_title}
                                                                </div>
                                                            </div>
                                                            <div class="social-group-create-options-item-column-right">
                                                                <div data-name="BLOCK_TWO_SELECT_SINGLE"
                                                                     data-items='{$jsontwo_block_items}'
                                                                     data-params='{$jsondata_params}'
                                                                     id="{$two_block_code}" class="main-ui-control main-ui-select">

                                                                    <span class="main-ui-select-name">Выберите</span>
                                                                    <span class="main-ui-square-search">
                                                                        <input
                                                                                type="text"
                                                                                tabindex="2"
                                                                                name="main-ui-square-search-item"
                                                                                id="main-ui-square-search-item"
                                                                                class="main-ui-square-search-item">
                                                                    </span>
                                                                </div>

                                                            </div>
                                                        </div>
HTML;
		$this->create_oneselectui = $opthtml;
		return $opthtml;
	}

	/**
	 * @return string
	 */
	public function getCreateMultiselectui()
	{
		$opthtml = '';
		$three_block_title = 'is multi select UI';
		$three_block_code = 'three_block_code';
		$three_block_items = [
			['NAME' => 'Первый вариант', 'VALUE' => '1'],
			['NAME' => 'Второй вариант', 'VALUE' => '2'],
		];
		$multidata_items = Json::encode($three_block_items);
		$multidata_params = Json::encode(['isMulti' => true]);
		$opthtml = <<<HTML
<div id="scrum-block" class="social-group-create-options-item">
                                            <div class="social-group-create-options-item-column-left">
                                                <div class="social-group-create-options-item-name">
													{$three_block_title}
                                                </div>
                                            </div>
                                            <div class="social-group-create-options-item-column-right">
                                                <div data-name="SELECT_MULTIPLE"
                                                     data-items='{$multidata_items}';
                                                     data-params='{$multidata_params}'
                                                     id="{$three_block_code}" class="main-ui-control main-ui-select">

                                                    <span class="main-ui-square-container"></span>
                                                    <span class="main-ui-square-search">
                                                        <input type="text" tabindex="2" class="main-ui-square-search-item">
                                                    </span>
                                                    <span class="main-ui-hide main-ui-control-value-delete">
                                                        <span class="main-ui-control-value-delete-item"></span>
                                                    </span>
            										</span>
                                                </div>

                                            </div>
                                        </div>

HTML;


		$this->create_multiselectui = $opthtml;
		return $opthtml;
	}

	/**
	 * @return string
	 */
	public function getTryingoptionsItem()
	{
		$opthtml = '';
		$one_block_id = 'oneblockid';
		$one_block_name = 'oneblockname';
		$one_block_code = 'oneblockcode';
		$one_block_option_name = 'is option name';
		$one_block_titledy = 'title block';
		$one_block_caption_byunicode = 'block-caption';
		$one_block_caption_more_byunicode = 'block-caprion-more-code';
		$one_block_title = 'title block it';

		$opthtml = <<<HTML
        <div class="social-group-create-options-item">
                                            <div id="{$one_block_id}"
                                                 class="social-group-create-options-item-column-left">
                                                <div class="social-group-create-options-item-name {$one_block_name}">
													{$one_block_title}
                                                </div>
                                                <div class="social-group-create-options-item-name sgcp-block-project">
                                                    -------abcd------
                                                </div>
                                            </div>

                                            <div class="social-group-create-options-item-column-right">
                                                <div class="social-group-create-options-item-column-one social-group-create-form-control-block"><span
                                                            id="main-user-selector-group_create_owner_{$one_block_id}"
                                                            class="main-user-selector-wrap">
			<input type="hidden" id="OWNER_CODE" name="OWNER_CODE" value="">

				<script type="text/javascript">
				BX.ready(function () {
					new BX.UI.TileSelector({
						"containerId": "ui-tile-selector-group_create_owner_{$one_block_id}",
						"id": "group_create_owner_{$one_block_id}",
						"duplicates": false,
						"readonly": false,
						"multiple": false,
						"manualInputEnd": true,
						"fireClickEvent": false,
						"caption": "{$one_block_caption_byunicode}",
						"captionMore": "{$one_block_caption_more_byunicode}"
					});
				});
			
				BX.message({
					UI_TILE_SELECTOR_MORE: 'еще #NUM#'
				});
			</script>
<span id="ui-tile-selector-group_create_owner_{$one_block_id}" class="ui-tile-selector-selector-wrap">
	<span id="ui-tile-selector-group_create_owner_{$one_block_id}-mask"
          class="ui-tile-selector-selector-mask">
	</span>
		
				<script data-role="tile-template" type="text/html">
					<span data-role="tile-item" data-bx-id="%id%" data-bx-data="%data%"
						  class="ui-tile-selector-item ui-tile-selector-item-%type% ui-tile-selector-item-readonly-%readonly%"
						  style="%style%">
						<span data-role="tile-item-name">%name%</span>
						<span data-role="remove" class="ui-tile-selector-item-remove"></span>
					</span>
				</script>

	<script data-role="popup-category-template" type="text/html">
		<div class="ui-tile-selector-searcher-sidebar-item">%name%</div>
	</script>

	<script data-role="popup-item-template" type="text/html">
		<div class="ui-tile-selector-searcher-content-item" title="%name%">%name%</div>
	</script>

	<script data-role="popup-template" type="text/html">
		<div class="ui-tile-selector-searcher">
			<div class="ui-tile-selector-searcher-container">
				<div data-role="popup-title" class="ui-tile-selector-searcher-title"></div>
				<div class="ui-tile-selector-searcher-inner">
					<div class="ui-tile-selector-searcher-main ui-tile-selector-searcher-inner-shadow">
						<div data-role="popup-item-list" class="ui-tile-selector-searcher-content"
                             style="display: none;"></div>
						<svg data-role="popup-loader" class="ui-tile-selector-searcher-circular" viewBox="25 25 50 50">
							<circle class="ui-tile-selector-searcher-path" cx="50" cy="50" r="20" fill="none"
                                    stroke-miterlimit="10"/>
							<circle class="ui-tile-selector-searcher-inner-path" cx="50" cy="50" r="20" fill="none"
                                    stroke-miterlimit="10"/>
						</svg>
					</div>
					<div data-role="popup-category-list" class="ui-tile-selector-searcher-sidebar"
                         style="display: none;"></div>
				</div>
			</div>
		</div>
	</script>

	<span data-role="tile-container" class="ui-tile-selector-selector">
				<span data-role="tile-more" class="ui-tile-selector-more" style="display: none;">
			<span data-role="tile-item-name">...</span>
		</span>
		<input data-role="tile-input" type="text" class="ui-tile-selector-input" autocomplete="off"
               style="display: none;" id="OWNER_CODE-group_create_owner_{$one_block_id}-search-input"
               data-bxchangehandler="Y">

					<span class="ui-tile-selector-select-container">
				<span data-role="tile-select" class="ui-tile-selector-select">
											{$one_block_titledy}									</span>
			</span>
					</span>
	</span>
                                                        <!--'start_frame_cache_b6FSBG'-->
                 <script>
						BX.ready(function () {
					
							var f = function (params) {
								var selectorId = 'group_create_owner_{$one_block_id}';
								var inputId = (
									BX.type.isNotEmptyObject(params)
									&& BX.type.isNotEmptyString(params.inputId)
										? params.inputId
										: 'main-user-selector-group_create_owner_{$one_block_id}');
								var inputBoxId = false;
								var inputContainerId = false;
								var containerId = (typeof params != 'undefined' && params.containerId != 'undefined' ? params.containerId : false);
								var bindId = (containerId ? containerId : inputId);
								var openDialogWhenInit = (
									typeof params == 'undefined'
									|| typeof params.openDialogWhenInit == 'undefined'
									|| !!params.openDialogWhenInit
								);
					
								var fieldName = false;
					
								if (
									BX.type.isNotEmptyObject(params)
									&& typeof params.id != 'undefined'
									&& params.id != selectorId
								) {
									return;
								}
					
								BX.Main.SelectorV2.create({
									apiVersion: 3,
									id: selectorId,
									fieldName: fieldName,
									pathToAjax: '',
									inputId: inputId,
									inputBoxId: inputBoxId,
									inputContainerId: inputContainerId,
									bindId: bindId,
									containerId: containerId,
									tagId: BX(''),
									openDialogWhenInit: openDialogWhenInit,
									bindNode: BX('main-user-selector-group_create_owner_{$one_block_id}'),
									options: {
										'useNewCallback': 'Y',
										'eventInit': 'BX.Main.User.SelectorController::init',
										'eventOpen': 'BX.Main.User.SelectorController::open',
										'userSearchArea': false,
										'contextCode': 'U',
										'context': 'GROUP_INVITE_OWNER',
										'lazyLoad': 'N',
										'multiple': 'N',
										'extranetContext': false,
										'useSearch': 'N',
										'userNameTemplate': '#NAME# #LAST_NAME#',
										'useClientDatabase': 'Y',
										'allowEmailInvitation': 'N',
										'enableAll': 'N',
										'enableDepartments': 'Y',
										'enableSonetgroups': 'N',
										'departmentSelectDisable': 'Y',
										'allowAddUser': 'N',
										'allowAddCrmContact': 'N',
										'allowAddSocNetGroup': 'N',
										'allowSearchEmailUsers': 'N',
										'allowSearchCrmEmailUsers': 'N',
										'allowSearchNetworkUsers': 'N'
									},
									callback: {
										select: BX.Main.User.SelectorController.select,
										unSelect: BX.Main.User.SelectorController.unSelect,
										openDialog: BX.Main.User.SelectorController.openDialog,
										closeDialog: BX.Main.User.SelectorController.closeDialog,
										openSearch: BX.Main.User.SelectorController.openSearch,
										closeSearch: BX.Main.User.SelectorController.closeSearch,
										openEmailAdd: null,
										closeEmailAdd: null
									},
									callbackBefore: {
										select: null,
										openDialog: null,
										context: null,
									},
									items: {
										selected: {'U1': 'users'},
										undeletable: [],
										hidden: []
									},
									entities: {
										users: {
											'U1': {
												'id': 'U1',
												'entityId': '1',
												'email': 'rzimin@itrack.ru',
												'name': 'admin',
												'avatar': '',
												'desc': '&nbsp;',
												'isExtranet': 'N',
												'isEmail': 'N',
												'isCrmEmail': 'N',
												'checksum': '1fa88a07c549e75bbd01c27a169bf357'
											}
										},
										groups: [],
										sonetgroups: [],
										department: []
									}
								});
					
								BX.removeCustomEvent(window, "BX.Main.User.SelectorController::init", arguments.callee);
							};
					
							BX.addCustomEvent(window, "BX.Main.User.SelectorController::init", f);
					
						});
				</script>

                                                        <!--'end_frame_cache_b6FSBG'-->
				<script type="text/javascript">
					BX.ready(function () {
						try {
							new BX.Main.User.Selector({
								"containerId": "main-user-selector-group_create_owner_{$one_block_id}",
								"id": "group_create_owner_{$one_block_id}",
								"duplicates": false,
								"inputName": "OWNER_CODE",
								"isInputMultiple": false,
								"useSymbolicId": true,
								"openDialogWhenInit": false,
								"lazyload": false
							});
						} catch (e) {
							console.log(e.name + ': ' + e.message);
						}
					});
				</script>
			</span>
					<span id="GROUP_MODERATORS_SWITCH_LABEL_block" class="social-group-create-text">
											<a id="GROUP_MODERATORS_switch" href="#"
                                               class="social-group-create-text-link sgcp-inlineblock-nonproject">Модераторы</a>
											<a id="GROUP_MODERATORS_PROJECT_switch" href="#"
                                               class="social-group-create-text-link sgcp-inlineblock-project">Помощники руководителя</a>
					</span>
					</div>
                                            </div>
                                        </div>
HTML;


		$this->tryingoptions_item = $opthtml;
		return $opthtml;
	}


	/**
	 * @return string
	 */
	public function getSimpleblock(): string
	{
		$opthtml = <<<HTML
                        <div id="simpleblock">
                            <div style="padding: 100px" id="filter">
                                <div data-name="SELECT_SINGLE"
                                     class="main-ui-filter-wield-with-label main-ui-filter-date-group main-ui-control-field-group">
                                    <span class="main-ui-control-field-label">Одиночный выбор</span>
                                    <div data-name="SELECT_SINGLE"
                                         data-items="[{&quot;NAME&quot;:&quot;\u041f\u0435\u0440\u0432\u044b\u0439 \u0432\u0430\u0440\u0438\u0430\u043d\u0442&quot;,&quot;VALUE&quot;:&quot;1&quot;},{&quot;NAME&quot;:&quot;\u0412\u0442\u043e\u0440\u043e\u0439 \u0432\u0430\u0440\u0438\u0430\u043d\u0442&quot;,&quot;VALUE&quot;:&quot;2&quot;}]"
                                         data-params="{&quot;isMulti&quot;:false}" id="select"
                                         class="main-ui-control main-ui-select">

                                        <span class="main-ui-select-name">Выберите</span>
                                        <span class="main-ui-square-search">
                                    <input type="text" tabindex="2" class="main-ui-square-search-item">
                                </span>
                                    </div>


                                </div>

                                <div data-name="SELECT_MULTIPLE"
                                     class="main-ui-filter-wield-with-label main-ui-filter-date-group main-ui-control-field-group">
                                    <span class="main-ui-control-field-label">Множественный выбор</span>
                                    <div data-name="SELECT_MULTIPLE"
                                         data-items="[{&quot;NAME&quot;:&quot;\u041f\u0435\u0440\u0432\u044b\u0439 \u0432\u0430\u0440\u0438\u0430\u043d\u0442&quot;,&quot;VALUE&quot;:&quot;1&quot;},{&quot;NAME&quot;:&quot;\u0412\u0442\u043e\u0440\u043e\u0439 \u0432\u0430\u0440\u0438\u0430\u043d\u0442&quot;,&quot;VALUE&quot;:&quot;2&quot;}]"
                                         data-params="{&quot;isMulti&quot;:true}" id="select2"
                                         class="main-ui-control main-ui-multi-select">

                                        <span class="main-ui-square-container"></span>
                                        <span class="main-ui-square-search"><input type="text" tabindex="2"
                                                                                   class="main-ui-square-search-item"></span>
                                        <span class="main-ui-hide main-ui-control-value-delete"><span
                                                    class="main-ui-control-value-delete-item"></span></span>
                                    </div>
                                </div>

                                <span class="ui-btn-primary ui-btn" id="update_filter">Найти</span>
                            </div>
                        </div>

HTML;

		$this->simpleblock = $opthtml;
		return $opthtml;
	}

	/**
	 * @return string
	 */
	public function getItemUpload()
	{
		$opthtml = <<<HTML
<div class="social-group-create-options-item social-group-create-options-item-upload">
                                            <div class="social-group-create-options-item-column-left">
                                                <div class="social-group-create-options-item-name">Изображение</div>
                                            </div>
                                            <div class="social-group-create-options-item-column-right">
                                                <div class="social-group-create-options-item-column-one">
                                                    <div id="GROUP_IMAGE_ID_block"
                                                         class="social-group-create-link-upload">
                                                        <div class="file-input" dropzone="copy f:*/*">
                                                            <ol class="webform-field-upload-list webform-field-upload-list-single webform-field-upload-icon-view"
                                                                id="mfi-GROUP_IMAGE_ID"></ol>
                                                            <div class="webform-field-upload"
                                                                 id="mfi-GROUP_IMAGE_ID-button"><span
                                                                        class="webform-small-button webform-button-upload">Загрузить изображение</span><span
                                                                        class="webform-small-button webform-button-replace">Заменить изображение</span><input
                                                                        type="button" id="mfi-GROUP_IMAGE_ID-editor">
                                                            </div>
                                                            <script type="text/javascript">
                                                                BX.message({
                                                                    'MFI_THUMB': '<div class=\"webform-field-item-wrap\"><span class=\"webform-field-upload-icon webform-field-upload-icon-#ext#\" data-bx-role=\"file-preview\"><\/span>\n<a \href=\"#\" target=\"_blank\" data-bx-role=\"file-name\" class=\"upload-file-name\">#name#<\/a><span class=\"upload-file-size\" data-bx-role=\"file-size\">#size#<\/span><i><\/i><del data-bx-role=\"file-delete\">&#215;<\/del><\/div>',
                                                                    'MFI_THUMB2': '<div class=\"webform-field-item-wrap\"><span class=\"webform-field-upload-icon webform-field-upload-icon-#ext#\"><img src=\"#preview_url#\" onerror=\"BX.remove(this);\" /><\/span>\n<a href=\"#url#\" target=\"_blank\" data-bx-role=\"file-name\" class=\"upload-file-name\">#name#<\/a><span class=\"upload-file-size\" data-bx-role=\"file-size\">#size#<\/span><i><\/i><del data-bx-role=\"file-delete\">&#215;<\/del>\n<input id=\"file-#file_id#\" data-bx-role=\"file-id\" type=\"hidden\" name=\"#input_name#\" value=\"#file_id#\" /><\/div>',
                                                                    'MFI_UPLOADING_ERROR': 'Ошибка загрузки файла.'
                                                                })
                                                                BX.ready(function () {
                                                                    try {
                                                                        BX.MFInput.init({
                                                                            'controlId': 'GROUP_IMAGE_ID',
                                                                            'controlUid': 'e52a6e2577833e4dfec6ef4e95cd6c08',
                                                                            'controlSign': '00eed8a602b0c88c980d21695dafdada18a5699a880dbe2df08b9f39e04deb3d',
                                                                            'inputName': 'GROUP_IMAGE_ID',
                                                                            'maxCount': '1',
                                                                            'moduleId': 'main',
                                                                            'forceMd5': false,
                                                                            'allowUpload': 'I',
                                                                            'allowUploadExt': '',
                                                                            'uploadMaxFilesize': '0',
                                                                            'enableCamera': false,
                                                                            'urlUpload': '/bitrix/components/bitrix/main.file.input/ajax.php?mfi_mode=upload&cid=e52a6e2577833e4dfec6ef4e95cd6c08&sessid=99fc846d5c8a19edf440aac1a1cf4e71&s=547246928f122c277298ab6ed02ffac4f02d08da1847836fa6ff8bd56021912c'
                                                                        });
                                                                    } catch (e) {

                                                                    }
                                                                });
                                                            </script>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
HTML;

		$this->item_upload = $opthtml;
		return $opthtml;
	}

	public function getNotUseIt_IsHidden()
	{
		$opthtml = <<<HTML
                                        <div class="social-group-create-openable-block-outer invisible"
                                             id="GROUP_MODERATORS_block_container">
                                            <div class="social-group-create-options-item" id="GROUP_MODERATORS_block">
                                                <div id="GROUP_MODERATORS_LABEL_block"
                                                     class="social-group-create-options-item-column-left">
                                                    <div class="social-group-create-options-item-name sgcp-block-nonproject">
                                                        Модераторы группы
                                                    </div>
                                                    <div class="social-group-create-options-item-name sgcp-block-project">
                                                        Помощники руководителя проекта
                                                    </div>
                                                </div>

                                                <div class="social-group-create-options-item-column-right">
                                                    <div class="social-group-create-options-item-column-one social-group-create-form-control-block"><span
                                                                id="main-user-selector-group_create_moderators_INlkx5"
                                                                class="main-user-selector-wrap">

	<script type="text/javascript">
	BX.ready(function () {
        new BX.UI.TileSelector({
            "containerId": "ui-tile-selector-group_create_moderators_INlkx5",
            "id": "group_create_moderators_INlkx5",
            "duplicates": false,
            "readonly": false,
            "multiple": true,
            "manualInputEnd": true,
            "fireClickEvent": false,
            "caption": "\u0414\u043e\u0431\u0430\u0432\u0438\u0442\u044c \u0441\u043e\u0442\u0440\u0443\u0434\u043d\u0438\u043a\u0430",
            "captionMore": "\u0414\u043e\u0431\u0430\u0432\u0438\u0442\u044c \u0435\u0449\u0435"
        });
    });

    BX.message({
        UI_TILE_SELECTOR_MORE: 'еще #NUM#'
    });
</script>
<span id="ui-tile-selector-group_create_moderators_INlkx5" class="ui-tile-selector-selector-wrap">
	<span id="ui-tile-selector-group_create_moderators_INlkx5-mask" class="ui-tile-selector-selector-mask"></span>
	<script data-role="tile-template" type="text/html">
			<span data-role="tile-item" data-bx-id="%id%" data-bx-data="%data%"
                  class="ui-tile-selector-item ui-tile-selector-item-%type% ui-tile-selector-item-readonly-%readonly%"
                  style="%style%">
		<span data-role="tile-item-name">%name%</span>
							<span data-role="remove" class="ui-tile-selector-item-remove"></span>
			</span>
		</script>

	<script data-role="popup-category-template" type="text/html">
		<div class="ui-tile-selector-searcher-sidebar-item">%name%</div>
	</script>

	<script data-role="popup-item-template" type="text/html">
		<div class="ui-tile-selector-searcher-content-item" title="%name%">%name%</div>
	</script>

	<script data-role="popup-template" type="text/html">
		<div class="ui-tile-selector-searcher">
			<div class="ui-tile-selector-searcher-container">
				<div data-role="popup-title" class="ui-tile-selector-searcher-title"></div>
				<div class="ui-tile-selector-searcher-inner">
					<div class="ui-tile-selector-searcher-main ui-tile-selector-searcher-inner-shadow">
						<div data-role="popup-item-list" class="ui-tile-selector-searcher-content"
                             style="display: none;"></div>
						<svg data-role="popup-loader" class="ui-tile-selector-searcher-circular" viewBox="25 25 50 50">
							<circle class="ui-tile-selector-searcher-path" cx="50" cy="50" r="20" fill="none"
                                    stroke-miterlimit="10"/>
							<circle class="ui-tile-selector-searcher-inner-path" cx="50" cy="50" r="20" fill="none"
                                    stroke-miterlimit="10"/>
						</svg>
					</div>
					<div data-role="popup-category-list" class="ui-tile-selector-searcher-sidebar"
                         style="display: none;"></div>
				</div>
			</div>
		</div>
	</script>

	<span data-role="tile-container" class="ui-tile-selector-selector">
				<span data-role="tile-more" class="ui-tile-selector-more" style="display: none;">
			<span data-role="tile-item-name">...</span>
		</span>
		<input data-role="tile-input" type="text" class="ui-tile-selector-input" autocomplete="off"
               style="display: none;" id="MODERATOR_CODES[]-group_create_moderators_INlkx5-search-input"
               data-bxchangehandler="Y">

					<span class="ui-tile-selector-select-container">
				<span data-role="tile-select" class="ui-tile-selector-select">
											Добавить сотрудника									</span>
			</span>
					</span>
	</span>
                                                            <!--'start_frame_cache_0fyr6K'--><script>
	BX.ready(function () {

        var f = function (params) {
            var selectorId = 'group_create_moderators_INlkx5';
            var inputId = (
                BX.type.isNotEmptyObject(params)
                && BX.type.isNotEmptyString(params.inputId)
                    ? params.inputId
                    : 'main-user-selector-group_create_moderators_INlkx5');
            var inputBoxId = false;
            var inputContainerId = false;
            var containerId = (typeof params != 'undefined' && params.containerId != 'undefined' ? params.containerId : false);
            var bindId = (containerId ? containerId : inputId);
            var openDialogWhenInit = (
                typeof params == 'undefined'
                || typeof params.openDialogWhenInit == 'undefined'
                || !!params.openDialogWhenInit
            );

            var fieldName = false;

            if (
                BX.type.isNotEmptyObject(params)
                && typeof params.id != 'undefined'
                && params.id != selectorId
            ) {
                return;
            }

            BX.Main.SelectorV2.create({
                apiVersion: 3,
                id: selectorId,
                fieldName: fieldName,
                pathToAjax: '/bitrix/components/bitrix/main.ui.selector/ajax.php',
                inputId: inputId,
                inputBoxId: inputBoxId,
                inputContainerId: inputContainerId,
                bindId: bindId,
                containerId: containerId,
                tagId: BX(''),
                openDialogWhenInit: openDialogWhenInit,
                bindNode: BX('main-user-selector-group_create_moderators_INlkx5'),
                options: {
                    'useNewCallback': 'Y',
                    'eventInit': 'BX.Main.User.SelectorController::init',
                    'eventOpen': 'BX.Main.User.SelectorController::open',
                    'contextCode': 'U',
                    'context': 'GROUP_INVITE_MODERATORS',
                    'lazyLoad': 'N',
                    'multiple': 'Y',
                    'extranetContext': false,
                    'useSearch': 'N',
                    'userNameTemplate': '#NAME# #LAST_NAME#',
                    'useClientDatabase': 'Y',
                    'allowEmailInvitation': 'N',
                    'enableAll': 'N',
                    'enableDepartments': 'Y',
                    'enableSonetgroups': 'N',
                    'departmentSelectDisable': 'Y',
                    'allowAddUser': 'N',
                    'allowAddCrmContact': 'N',
                    'allowAddSocNetGroup': 'N',
                    'allowSearchEmailUsers': 'N',
                    'allowSearchCrmEmailUsers': 'N',
                    'allowSearchNetworkUsers': 'N'
                },
                callback: {
                    select: BX.Main.User.SelectorController.select,
                    unSelect: BX.Main.User.SelectorController.unSelect,
                    openDialog: BX.Main.User.SelectorController.openDialog,
                    closeDialog: BX.Main.User.SelectorController.closeDialog,
                    openSearch: BX.Main.User.SelectorController.openSearch,
                    closeSearch: BX.Main.User.SelectorController.closeSearch,
                    openEmailAdd: null,
                    closeEmailAdd: null
                },
                callbackBefore: {
                    select: null,
                    openDialog: null,
                    context: null,
                },
                items: {
                    selected: [],
                    undeletable: [],
                    hidden: []
                },
                entities: {
                    users: '',
                    groups: '',
                    sonetgroups: '',
                    department: ''
                }
            });

            BX.removeCustomEvent(window, "BX.Main.User.SelectorController::init", arguments.callee);
        };

        BX.addCustomEvent(window, "BX.Main.User.SelectorController::init", f);

    });
</script>

                                                            <!--'end_frame_cache_0fyr6K'-->
	<script type="text/javascript">
		BX.ready(function () {
            try {
                new BX.Main.User.Selector({
                    "containerId": "main-user-selector-group_create_moderators_INlkx5",
                    "id": "group_create_moderators_INlkx5",
                    "duplicates": false,
                    "inputName": "MODERATOR_CODES[]",
                    "isInputMultiple": true,
                    "useSymbolicId": true,
                    "openDialogWhenInit": false,
                    "lazyload": false
                });
            } catch (e) {
                console.log(e.name + ': ' + e.message);
            }
        });
	</script>
</span></div>
                                                </div>
                                            </div>
                                        </div>

HTML;

	}


}



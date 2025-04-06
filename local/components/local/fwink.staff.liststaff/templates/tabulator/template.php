<?
/** @noinspection PhpIncludeInspection */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 * @var string $templateFolder
 */

use Bitrix\Main\Grid\Panel\Snippet;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Json;

//$mains=new LocalFwink();
$pathmodule = $_SERVER['DOCUMENT_ROOT'] . LocalFwink::MODULE_PATCH . LocalFwink::MODULE_ID;

$this->addExternalJs('/bitrix/js/local.fwink/tabulator/tabulator.min.js');
$this->addExternalJs('/bitrix/js/local.fwink/tabulator/jquery3.3.1.min.js');
$this->addExternalCss('/bitrix/css/local_fwink/tabulator/tabulator.min.css');
$this->addExternalCss($templateFolder . "/styles.upper_search_menu.css");
$this->addExternalCss('/bitrix/js/ui/buttons/src/css/ui.buttons.css');
Bitrix\Main\Page\Asset::getInstance()->addString('<link href="/local/js/local.fwink/dist/bundle.css" rel="stylesheet" />', true);
$this->addExternalJs('//api.bitrix24.com/api/v1/');

$whichdomainsendedrequest = $GLOBALS['FWINK']['DOMAIN'];
$wdsr = strrev($whichdomainsendedrequest);
$randstring = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 1, 4);
$enc = \Local\Fwink\SimpleEncDecriptStringToNumber::encrypt($whichdomainsendedrequest) . $randstring;
//minWidth
array_unshift($arResult['COLUMNS'], [
	"formatter" => 'funcdeleteIcon',
	"hozAlign" => 'center',
	"sortable" => 'false',
	"cellClick" => "globalClick",
	"width" => "40px"
]);

foreach ($arResult['COLUMNS'] as $key => $val) {
	if ($arResult['COLUMNS'][$key]['field'] == 'NAME') {
//		$arResult['COLUMNS'][$key]['width'] = '200';
	}
	if ($arResult['COLUMNS'][$key]['field'] == 'ID') {
		$arResult['COLUMNS'][$key]['width'] = '20';
		$arResult['COLUMNS'][$key]['title'] = '';
	}
	if ($arResult['COLUMNS'][$key]['field'] == 'EMAIL') {
		$arResult['COLUMNS'][$key]['width'] = '200';
	}
}

?>
<div>
    <table class="bx-layout-inner-inner-table top-menu-mode grid-mode">

        <tbody>
        <tr class="bx-layout-inner-inner-top-row">
            <td class="bx-layout-inner-inner-cont">
                <div class="page-header">

					<?
					//require_once(__DIR__.'../../localfwinkmenu.php');
					?>


                    <div id="uiToolbarContainer" class="ui-toolbar">


                        <div class="ui-toolbar-filter-box">
                            <!-- Final :: Search -->
                            <div class="main-ui-filter-search main-ui-filter-theme-default"
                                 id="INTRANET_USER_LIST_s1_search_container">
                                <!--<div class="main-ui-filter-search-square main-ui-filter-search-square-preset main-ui-square">
                                    <div class="main-ui-square-item">Rjvgfybzz
                                    </div>
                                    <div class="main-ui-item-icon main-ui-square-delete"></div>
                                </div>-->
                                <input type="text" tabindex="1" name="FIND" placeholder="поиск"
                                       class="main-ui-filter-search-filter" id="INTRANET_USER_LIST_s1_search"
                                       autocomplete="off">
                                <div class="main-ui-item-icon-block main-ui-show">
                                    <span class="main-ui-item-icon main-ui-search"></span>
                                    <span class="main-ui-item-icon main-ui-delete"></span>
                                </div>
                            </div>

                        </div>


                        <div class="ui-toolbar-right-buttons">
                            <!--<button class="ui-btn ui-btn-light-border ui-btn-icon-setting ui-btn-themes"
                                    id="intranet_user_grid_s1_toolbar_button" data-btn-uniqid="uibtn-gv91n5l0"></button>-->
                            <button class="ui-btn ui-btn-primary ui-btn-icon-add"
                                    onclick="BX.HelpsDesk.Listext.openPageAddUser()"
                                    data-btn-uniqid="uibtn-w50xenhp"
                                    data-json-options="{&quot;events&quot;:{&quot;click&quot;:{&quot;handler&quot;:&quot;jsBXIUL.showInvitation&quot;,&quot;context&quot;:&quot;jsBXIUL&quot;}}}">
                                <span class="ui-btn-text" idn="117">Добавить</span>
                            </button>
                        </div>


                    </div>


                </div>
            </td>


        </tr>

        <tr>
            <td>
                <div id="example-table" idn="10667"></div>

            </td>

        </tr>


        </tbody>
    </table>
</div>

<!--<link href="tabulatorcustom.min.css" rel="stylesheet">-->

<script>

    $(document).ready(function () {

        var funcdeleteIcon = function (cell, formatterParams) {
            return `<span class="main-grid-cell-content">
                <a href="#"
                    class="main-grid-row-action-button"
                    data-actions="[{'ICON':'view','LINK':'user_edit.php?lang=ru&amp;ID=1','href':'user_edit.php?lang=ru&amp;ID=1','text':'Посмотреть','default':true},{'ICON':'','LINK':'user_admin.php?lang=ru&amp;ID=1&amp;action=authorize&amp;sessid=a231e5edd7e1826dd504480be04866b6','href':'user_admin.php?lang=ru&amp;ID=1&amp;action=authorize&amp;sessid=a231e5edd7e1826dd504480be04866b6','title':'Авторизоваться под данным пользователем','text':'Авторизоваться'},{'ICON':'','LINK':'user_admin.php?lang=ru&amp;ID=1&amp;action=logout_user&amp;sessid=a231e5edd7e1826dd504480be04866b6','href':'user_admin.php?lang=ru&amp;ID=1&amp;action=logout_user&amp;sessid=a231e5edd7e1826dd504480be04866b6','title':'Выполнить выход пользователя на всех устройствах','text':'Выполнить выход'}]">
                 </a></span>`;
        }

        var globalClick = function (e, cell) {
            // alert('123');
            var idCell = cell.getRow().getData().ID;
            cell.getElement().setAttribute("id", "cell" + idCell);
            /* console.log('cellEdited',cell.getValue()
				 ,cell.getOldValue()
				 ,cell.getRow().getData().ID
				 ,cell.getElement()
			 );*/
            // console.log(e, cell);
            // console.log(cell.getElement(),typeof cell.getElement())

            // console.log('cellEdited',cell.getValue(),cell.getOldValue(),cell.getRow().getData().id,cell.getElement());
            // $("#example-table").tabulator("getRow", 24).update({name:"steve");
            // cell.getElement().attr("id", "cellid1");
            BX.HelpsDesk.Listext.anyfunc("cell" + idCell, e, cell);


        }

        var COLUMNS = <?=\CUtil::PhpToJSObject($arResult['COLUMNS']);?>

        COLUMNS.map(function (e, i) {
            // e.isApproved = true;
            if (typeof e.cellClick !== 'undefined') {
                try {
                    e.cellClick = eval(e.cellClick)
                } catch (e) {
                }
            }
            if (typeof e.width !== 'undefined') {
                try {
                    e.width = parseInt(e.width)
                } catch (e) {
                }
            }
            if (typeof e.formatter !== 'undefined'
                && e.formatter.includes('func')) {
                try {
                    e.formatter = eval(e.formatter)
                } catch (e) {

                }
            }
        });

        var funcs = function (e, cell) {
            console.log("cell click..")
        }


        // $.get(url, function(response) {
        var tableinf = new Tabulator('#example-table', {
            data: <?=\CUtil::PhpToJSObject($arResult['DATAROW']);?>,
            layout: 'fitColumns',
            pagination: 'local',
            crossDomain: true,
            paginationSize: 8,
            initialSort: [
                {column: 'rating', dir: 'desc'}
            ],
            columns: COLUMNS
        });
        // });


        const input = document.getElementById("INTRANET_USER_LIST_s1_search");
        input.addEventListener("keyup", function () {
            tableinf.setFilter(matchAny, {value: input.value});
            if (input.value == " ") {
                tableinf.clearFilter()
            }
        });

        function matchAny(data, filterParams) {
            //data - the data for the row being filtered
            //filterParams - params object passed to the filter
            //RegExp - modifier "i" - case insensitve

            var match = false;
            const regex = RegExp(filterParams.value, 'i');

            for (var key in data) {
                if (regex.test(data[key]) == true) {
                    match = true;
                }
            }
            return match;
        }


    });
</script>


<script>
    /*BX.addCustomEvent('onPullEvent', function (module_id, command) {
        if (module_id === 'local.fwink' && command === 'update_staff_list') {
            BX.HelpsDesk.Main.reloadGrid('STAFF_LIST');
        }
    });*/

    /*    var menu = [];

		menu.push({
			text: "Бизнес процессы",
			title: "Бизнес процессы",
			href: "#",
			onclick: function(e, item){
				// BX.PreventDefault(e);
				// Событие при клике на пункт
				// popupMenu.popupWindow.show();
				popupMenu.popupWindow.close();
				console.log('..click:'+idCell)
			}
		});
		menu.push({
			text: "Сервисы",
			title: "Сервисы",
			href: "#",
			onclick: function(e, item){
				// BX.PreventDefault(e);
				// Событие при клике на пункт
				console.log('..click:'+idCell)
				popupMenu.popupWindow.close();
			}
		});

		var params = {
			offsetLeft: 20,
			closeByEsc: true,
			angle: {
				position: 'top'
			},
			events: {
				onPopupClose : function(){
					//обработка событии при закрытии меню
					console.log('..close:'+idCell)
				}
			}
		}
		var popupMenu = new BX.PopupMenuWindow(
			"cell"+idCell,
			BX("menuBtn"),
			menu,
			params
		);

		popupMenu.popupWindow.show();*/
</script>


<?php
/*
 * [
                { title: 'id', field: 'itemId', visible: false },
                {
                    title: 'Статья',
                    field: 'title',
                    width: 550,
                    formatter: function(cell) {
                        var data = cell.getData(),
                            href = 'https://webdevkin.ru/index.php?id=' + data.itemId;

                        return '<a href="' + href + '" target="_blank">' + data.title + '</a>';
                    }
                },
                { title: 'Количество голосов', field: 'countRates', align: 'left', formatter: 'progress', tooltip: true },
                { title: 'Средняя оценка', field: 'rating' },
                {
                    title: 'Рейтинг статьи',
                    field: 'rating',
                    formatter: function(cell) {
                        var data = cell.getData();
                        return Math.round(data.countRates * data.rating);
                    },
                    sorter: function(a, b, aRow, bRow) {
                        var data1 = aRow.getData(),
                            data2 = bRow.getData(),
                            value1 = Math.round(data1.countRates * data1.rating),
                            value2 = Math.round(data2.countRates * data2.rating);
                        return value1 - value2;
                    }
                }
            ]
 * */
?>
<script>

    (function () {
        'use strict';
        console.log('..start BX.HelpsDesk.Listext from template');

        BX.namespace('BX.HelpsDesk.Listext');

        BX.HelpsDesk.Listext = {
            ids: {
                // template_ticket_download: 'helpdesk-ticket-list-template-download',
            },

            init: function (parameters) {
                // this.ajaxUrl = parameters.ajaxUrl || '';
                // this.message = parameters.message;
                this.cellpopupMenu = '';
            },

            getReport: function (element) {
                let data = {};
                data.report_export = 'Y';
                this.sendRequest(data, element);
            },

            openPageAddUser: function () {
                var url = document.location.pathname + 'add/';
                var url = '?add=new&mode=pages&page=staff&access=whateverdomaintokenfordemo';
                /*BX.SidePanel.Instance.open(url, {
                    cacheable: false
                });*/
                console.log('..send dictionary');
                // window.BX24.openApplication(url);
                window.BX24.openApplication({
                    add: 'new',
                    mode:'pages',
                    page:'staff',
                    bx24_width:710
                })
            },

            createModal: function (result) {
                let template = document.getElementById(this.ids.template_ticket_download).innerHTML;
                let orderItemHtml = Mustache.render(template, result);
                let popup = BX.PopupWindowManager.create('helpdesk-popup-report', null, {
                    content: orderItemHtml,
                    zIndex: 100,
                    closeIcon: {opacity: 1},
                    titleBar: this.message.export.title,
                    closeByEsc: true,
                    darkMode: false,
                    autoHide: true,
                    draggable: false,
                    resizable: false,
                    lightShadow: true,
                    angle: false,
                    overlay: {
                        backgroundColor: 'black',
                        opacity: 500,
                    },
                    events: {
                        onPopupClose: function () {
                            popup.destroy();
                        },
                    },
                    buttons: [
                        new BX.PopupWindowButton({
                            text: this.message.export.button.cancel,
                            id: 'cancel-btn',
                            className: 'ui-btn ui-btn-link',
                            events: {
                                click: BX.delegate(function () {
                                    popup.destroy();
                                }, this),
                            },
                        }),
                    ],
                });
                popup.show();
            },

            sendRequest: function (data, element) {
                BX.HelpsDesk.Main.preloaderButton(element);
                BX.ajax({
                    method: 'POST',
                    dataType: 'json',
                    url: this.ajaxUrl,
                    data: data,
                    async: true,
                    onsuccess: BX.delegate(function (result) {
                        this.createModal(result);
                        BX.HelpsDesk.Main.preloaderButton(element);
                    }, this), onfailure: BX.delegate(function () {

                    }, this),
                });
            },
            anyfunc: function (idelementCell, e, cell) {
                console.log(idCell, e, cell);
                // var cellpopupMenu
                var idCell = cell.getRow().getData().ID;


                BX.PopupMenu.show('demo-popup-menu', BX(idelementCell), [
                    {
                        text: 'Просмотреть', // Название пункта
                        href: '#', // Ссылка
                        className: 'menu-popup-item menu-popup-no-icon idnsksd', // Дополнительные классы
                        /*className: 'menu-popup-item menu-popup-item-accept',*/
                        onclick: function (e, item) {
                            //BX.PreventDefault(e);
                            // Событие при клике на пункт
                            // console.log('..click:' + idCell) /helpdsk/staff/959/
                            console.log('/helpdsk/staff/' + idCell + '/');
                            var url = document.location.pathname + '' + cell + '/';
                            /*BX.SidePanel.Instance.open('/helpdsk/staff/' + idCell + '?link_utm=<?=$enc;?>', {
                                cacheable: false
                            });*/
                            /*console.log('..send dictionary 449');
                            // window.BX24.openApplication(url);
                            window.BX24.openApplication({
                                mode: 'pages',
                                page:'post',
                                link_utm:'<?=$enc;?>',
                                bx24_width:710
                            })*/
                            BX.PopupMenu.destroy('demo-popup-menu')
                        }
                    },
                    {
                        text: 'Редактировать', // Название пункта
                        href: '#', // Ссылка
                        className: 'menu-popup-item menu-popup-no-icon idnsheb', // Дополнительные классы
                        /*className: 'menu-popup-item menu-popup-item-accept',*/
                        onclick: function (e, item) {
                            //BX.PreventDefault(e);
                            var url = document.location.pathname + '' + cell + '/';
                            /*BX.SidePanel.Instance.open('?element_id=' + idCell + '&mode=pages&page=staff&access=whateverdomaintokenfordemo&link_utm=<?=$enc;?>', {
                                cacheable: false
                            });*/
                            console.log('..send dictionary 4771');
                            console.log('window.BX24 is:',typeof window.BX24)
                            console.log('BX24 is:',typeof BX24)
                            // window.BX24.openApplication(url);
                            window.BX24.openApplication({
                                mode: 'pages',
                                page:'staff',
                                element_id:idCell,
                                link_utm:'<?=$enc;?>',
                                bx24_width:710
                            })
                            BX.PopupMenu.destroy('demo-popup-menu')
                        }
                    },
                    {
                        text: 'Удалить', // Название пункта
                        href: '#', // Ссылка
                        className: 'menu-popup-item menu-popup-no-icon idnehsbd', // Дополнительные классы
                        /*className: 'menu-popup-item menu-popup-item-accept',*/
                        onclick: function (e, item) {
                            //BX.PreventDefault(e);
                            // Событие при клике на пункт
                            console.log('..click delete:' + cell);
                            alert(' Event by delete this USER...')


                            BX.PopupMenu.destroy('demo-popup-menu')
                        }
                    },
                ], {
                    autoHide: true, // Закрытие меню при клике вне меню
                    offsetTop: 0, // смещение от элемента по Y
                    zIndex: 10000, // z-index
                    offsetLeft: 100,  // смещение от элемента по X
                    angle: {offset: 45}, // Описание уголка, при null – уголка не будет
                    events: {
                        onPopupShow: function () {
                            // Событие при показе меню
                        },
                        /* onPopupClose : function(){
							 // Событие при закрытии меню
						 },
						 onPopupClose : function(){
							 // Событие при уничтожении объекта меню
						 }*/
                    }
                });
                //


            }
        };
    })();


</script>

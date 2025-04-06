<?php


namespace Local\Fwink\Helpers;


class TemplateFormAdditional
{

	public $headerblockadditional;
	public $createadditional_block_features = '';
	public $createadditional_block_initperms = '';
	public $createadditional_block_tags = '';
	public $createadditional_block_type = '';


	function __construct()
	{
//		$this->getHeaderblockadditional();
	}

	/**
	 * @return mixed
	 */
	public function getHeaderblockadditional()
	{
		$opthtml = '';
		$opthtml = <<<HTML
<div class="social-group-create-additional-alt" id="switch_additional">
                                            <div class="social-group-create-additional-alt-more">Дополнительно</div>
                                            <div class="social-group-create-additional-alt-promo">
                                                <span class="social-group-create-additional-alt-promo-text"
                                                      bx-block-id="features">Возможности</span>
                                                <span class="social-group-create-additional-alt-promo-text"
                                                      bx-block-id="initperms">Права</span><span
                                                        class="social-group-create-additional-alt-promo-text"
                                                        bx-block-id="tags">Теги</span> <span
                                                        class="social-group-create-additional-alt-promo-text"
                                                        bx-block-id="type">Тип</span>
                                            </div>
                                        </div>
HTML;

		$this->headerblockadditional = $opthtml;
		return $opthtml;
	}

	function __destruct()
	{

	}

	public function setHeaderAdditionalBlock()
	{
		$output = <<<HTML
<div class="social-group-create-additional-block">
HTML;
		$output .= $this->getHeaderblockadditional();
		$output .= <<<HTML
 <div class="social-group-create-openable-block-outer invisible"
                                             id="block_additional">
                                            <div class="social-group-create-options social-group-create-options-more social-group-create-openable-block"
                                                 id="block_additional_inner">
HTML;

		return $output;

	}

	public function setFooterAdditionalBlock()
	{
		$output = <<<HTML

<div class="sonet-slider-footer-fixed">
                                        <input type="hidden" name="SONET_USER_ID" value="1">
                                        <input type="hidden" name="SONET_GROUP_ID" id="SONET_GROUP_ID" value="0">
                                        <input type="hidden" name="TAB" value="">
                                        <div class="social-group-create-buttons"><span
                                                    class="sonet-ui-btn-cont sonet-ui-btn-cont-center"><button
                                                        class="ui-btn ui-btn-success ui-btn-md"
                                                        id="sonet_group_create_popup_form_button_submit"
                                                        bx-action-type="create">Создать группу</button><button
                                                        class="ui-btn ui-btn-link"
                                                        id="sonet_group_create_popup_form_button_step_2_back">Отмена</button></span>
                                        </div>
                                    </div>
                                    


 </div>
                                        </div>
                                    </div>
HTML;


	}

	/**
	 * @return string
	 */
	public function getCreateadditionalBlockFeatures()
	{
		$opthtml = '';

		$opthtml = <<<HTML
<div class="social-group-create-additional-block-item"
                                                     id="additional-block-features">
                                                    <div class="social-group-create-options-item social-group-create-form-field-list-block">
                                                        <div class="social-group-create-options-item-column-left">
                                                            <div class="social-group-create-options-item-name">
                                                                Возможности
                                                            </div>
                                                        </div>
                                                        <div class="social-group-create-options-item-column-right">
                                                            <div class="social-group-create-options-item-column-one">
                                                                <div class="social-group-create-form-field-list">
                                                                    <div class="social-group-create-form-field-list-item">
                                                                        <input name="tasks_active" type="checkbox"
                                                                               class="social-group-create-form-field-list-input"
                                                                               value="Y" checked="">
                                                                        <span class="social-group-create-form-field-list-name"><label
                                                                                    class="social-group-create-form-field-list-label">Задачи</label></span>
                                                                        <input type="text" name="tasks_name"
                                                                               class="social-group-create-form-field-input-text"
                                                                               value="">
                                                                        <span class="social-group-create-form-pencil"></span>
                                                                        <span class="social-group-create-form-field-cancel"></span>
                                                                    </div>
                                                                    <div class="social-group-create-form-field-list-item">
                                                                        <input name="calendar_active" type="checkbox"
                                                                               class="social-group-create-form-field-list-input"
                                                                               value="Y" checked="">
                                                                        <span class="social-group-create-form-field-list-name"><label
                                                                                    class="social-group-create-form-field-list-label">Календарь</label></span>
                                                                        <input type="text" name="calendar_name"
                                                                               class="social-group-create-form-field-input-text"
                                                                               value="">
                                                                        <span class="social-group-create-form-pencil"></span>
                                                                        <span class="social-group-create-form-field-cancel"></span>
                                                                    </div>
                                                                    <div class="social-group-create-form-field-list-item">
                                                                        <input name="files_active" type="checkbox"
                                                                               class="social-group-create-form-field-list-input"
                                                                               value="Y" checked="">
                                                                        <span class="social-group-create-form-field-list-name"><label
                                                                                    class="social-group-create-form-field-list-label">Диск</label></span>
                                                                        <input type="text" name="files_name"
                                                                               class="social-group-create-form-field-input-text"
                                                                               value="">
                                                                        <span class="social-group-create-form-pencil"></span>
                                                                        <span class="social-group-create-form-field-cancel"></span>
                                                                    </div>
                                                                    <div class="social-group-create-form-field-list-item">
                                                                        <input name="chat_active" type="checkbox"
                                                                               class="social-group-create-form-field-list-input"
                                                                               value="Y" checked="">
                                                                        <span class="social-group-create-form-field-list-name"><label
                                                                                    class="social-group-create-form-field-list-label">Чат</label></span>
                                                                        <input type="text" name="chat_name"
                                                                               class="social-group-create-form-field-input-text"
                                                                               value="">
                                                                        <span class="social-group-create-form-pencil"></span>
                                                                        <span class="social-group-create-form-field-cancel"></span>
                                                                    </div>
                                                                    <div class="social-group-create-form-field-list-item">
                                                                        <input name="forum_active" type="checkbox"
                                                                               class="social-group-create-form-field-list-input"
                                                                               value="Y">
                                                                        <span class="social-group-create-form-field-list-name"><label
                                                                                    class="social-group-create-form-field-list-label">Обсуждения</label></span>
                                                                        <input type="text" name="forum_name"
                                                                               class="social-group-create-form-field-input-text"
                                                                               value="">
                                                                        <span class="social-group-create-form-pencil"></span>
                                                                        <span class="social-group-create-form-field-cancel"></span>
                                                                    </div>
                                                                    <div class="social-group-create-form-field-list-item">
                                                                        <input name="blog_active" type="checkbox"
                                                                               class="social-group-create-form-field-list-input"
                                                                               value="Y" checked="">
                                                                        <span class="social-group-create-form-field-list-name"><label
                                                                                    class="social-group-create-form-field-list-label">Сообщения</label></span>
                                                                        <input type="text" name="blog_name"
                                                                               class="social-group-create-form-field-input-text"
                                                                               value="">
                                                                        <span class="social-group-create-form-pencil"></span>
                                                                        <span class="social-group-create-form-field-cancel"></span>
                                                                    </div>
                                                                    <div class="social-group-create-form-field-list-item">
                                                                        <input name="photo_active" type="checkbox"
                                                                               class="social-group-create-form-field-list-input"
                                                                               value="Y">
                                                                        <span class="social-group-create-form-field-list-name"><label
                                                                                    class="social-group-create-form-field-list-label">Фотогалерея</label></span>
                                                                        <input type="text" name="photo_name"
                                                                               class="social-group-create-form-field-input-text"
                                                                               value="">
                                                                        <span class="social-group-create-form-pencil"></span>
                                                                        <span class="social-group-create-form-field-cancel"></span>
                                                                    </div>
                                                                    <div class="social-group-create-form-field-list-item">
                                                                        <input name="group_lists_active" type="checkbox"
                                                                               class="social-group-create-form-field-list-input"
                                                                               value="Y">
                                                                        <span class="social-group-create-form-field-list-name"><label
                                                                                    class="social-group-create-form-field-list-label">������</label></span>
                                                                        <input type="text" name="group_lists_name"
                                                                               class="social-group-create-form-field-input-text"
                                                                               value="">
                                                                        <span class="social-group-create-form-pencil"></span>
                                                                        <span class="social-group-create-form-field-cancel"></span>
                                                                    </div>
                                                                    <div class="social-group-create-form-field-list-item">
                                                                        <input name="marketplace_active" type="checkbox"
                                                                               class="social-group-create-form-field-list-input"
                                                                               value="Y" checked="">
                                                                        <span class="social-group-create-form-field-list-name"><label
                                                                                    class="social-group-create-form-field-list-label">Приложения</label></span>
                                                                        <input type="text" name="marketplace_name"
                                                                               class="social-group-create-form-field-input-text"
                                                                               value="">
                                                                        <span class="social-group-create-form-pencil"></span>
                                                                        <span class="social-group-create-form-field-cancel"></span>
                                                                    </div>
                                                                    <div class="social-group-create-form-field-list-item">
                                                                        <input name="search_active" type="checkbox"
                                                                               class="social-group-create-form-field-list-input"
                                                                               value="Y">
                                                                        <span class="social-group-create-form-field-list-name"><label
                                                                                    class="social-group-create-form-field-list-label">Поиск</label></span>
                                                                        <input type="text" name="search_name"
                                                                               class="social-group-create-form-field-input-text"
                                                                               value="">
                                                                        <span class="social-group-create-form-pencil"></span>
                                                                        <span class="social-group-create-form-field-cancel"></span>
                                                                    </div>
                                                                    <div class="social-group-create-form-field-list-item">
                                                                        <input name="landing_knowledge_active"
                                                                               type="checkbox"
                                                                               class="social-group-create-form-field-list-input"
                                                                               value="Y" checked="">
                                                                        <span class="social-group-create-form-field-list-name"><label
                                                                                    class="social-group-create-form-field-list-label">���� ������</label></span>
                                                                        <input type="text" name="landing_knowledge_name"
                                                                               class="social-group-create-form-field-input-text"
                                                                               value="">
                                                                        <span class="social-group-create-form-pencil"></span>
                                                                        <span class="social-group-create-form-field-cancel"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
HTML;

		$this->createadditional_block_features = $opthtml;
		return $opthtml;
	}

	/**
	 * @param string $createadditional_block_initperms
	 */
	public function getCreateadditionalBlockInitperms()
	{
		$opthtml = '';

		$opthtml = <<<HTML
                                                <div class="social-group-create-additional-block-item"
                                                     id="additional-block-initperms">
                                                    <div class="social-group-create-options-item">
                                                        <div class="social-group-create-options-item-column-left">
                                                            <div id="GROUP_INVITE_PERMS_LABEL_block"
                                                                 class="social-group-create-options-item-name">
                                                                <div class="sonet-group-create-popup-form-add-title sgcp-block-nonproject">
                                                                    Приглашать в группу могут
                                                                </div>
                                                                <div class="sonet-group-create-popup-form-add-title sgcp-block-project">
                                                                    Приглашать в проект могут
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div id="GROUP_INVITE_PERMS_block"
                                                             class="social-group-create-options-item-column-right">
                                                            <div class="social-group-create-field-block sgcp-flex-nonproject">
                                                                <select name="GROUP_INITIATE_PERMS"
                                                                        id="GROUP_INITIATE_PERMS"
                                                                        class="social-group-create-field social-group-create-field-select">
                                                                    <option value="">-Не выбрано-</option>
                                                                    <option id="GROUP_INITIATE_PERMS_OPTION_A"
                                                                            value="A">value A
                                                                    </option>
                                                                    <option id="GROUP_INITIATE_PERMS_OPTION_E"
                                                                            value="E">value E
                                                                    </option>
                                                                    <option id="GROUP_INITIATE_PERMS_OPTION_K" value="K"
                                                                            selected="">value K
                                                                    </option>
                                                                </select>
                                                            </div>
                                                            <div class="social-group-create-field-block sgcp-flex-project">
                                                                <select name="GROUP_INITIATE_PERMS_FLEX"
                                                                        id="GROUP_INITIATE_PERMS_FLEX_PROJECT"
                                                                        class="social-group-create-field social-group-create-field-select">
                                                                    <option value="">-Не выбрано-</option>
                                                                    <option id="GROUP_INITIATE_PERMS_OPTION_PROJECT_A"
                                                                            value="A">value A
                                                                    </option>
                                                                    <option id="GROUP_INITIATE_PERMS_OPTION_PROJECT_E"
                                                                            value="E">value E
                                                                    </option>
                                                                    <option id="GROUP_INITIATE_PERMS_OPTION_PROJECT_K"
                                                                            value="K" selected="">value K
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <input type="hidden" value="K" name="GROUP_SPAM_PERMS">
HTML;


		$this->createadditional_block_initperms = $opthtml;
		return $opthtml;
	}

	/**
	 * @return string
	 */
	public function getCreateadditionalBlockTags()
	{
		$opthtml = '';
		$opthtml = <<<HTML
                                                <div class="social-group-create-additional-block-item"
                                                     id="additional-block-tags">
                                                    <div class="social-group-create-options-item">
                                                        <div class="social-group-create-options-item-column-left">
                                                            <div class="social-group-create-options-item-name">Теги
                                                            </div>
                                                        </div>
                                                        <div class="social-group-create-options-item-column-right">
                                                            <div class="social-group-create-control-inner social-group-create-form-field inline t-filled tdp-mem-sel-is-empty-false t-min tdp-mem-sel-is-min">
													<span class="social-group-create-form-field-controls"
                                                          id="group-tags-container">
																												<a href="javascript:void(0);"
                                                                                                                   id="group-tags-add-new"
                                                                                                                   class="js-id-tdp-mem-sel-is-open-form social-group-create-form-field-when-filled social-group-create-form-field-link add">Добавить еще</a>
													</span>
                                                                <input type="hidden" name="GROUP_KEYWORDS"
                                                                       id="GROUP_KEYWORDS" value=",">
                                                            </div>
                                                            <div id="sgcp-tags-popup-content" style="display: none;">
                                                                <script>
                                                                    BX.ready(function () {
                                                                        var input = BX("GROUP_KEYWORDS_popup_input67abSn");
                                                                        if (input)
                                                                            try {
                                                                                new JsTc(input, 'pe:10,sort:cnt,site_id:s1');
                                                                            } catch (e) {
                                                                                console.log('..not init tags libray')
                                                                            }
                                                                    });
                                                                </script>
                                                                <input name="GROUP_KEYWORDS-popup-input"
                                                                       id="GROUP_KEYWORDS_popup_input67abSn" value=""
                                                                       class="search-tags" type="text"
                                                                       autocomplete="off">
                                                                <script>
                                                                    try {
                                                                        new BX.BXGCETagsForm({
                                                                            containerNodeId: 'group-tags-container',
                                                                            hiddenFieldId: 'GROUP_KEYWORDS',
                                                                            addNewLinkId: 'group-tags-add-new',
                                                                            popupContentNodeId: 'sgcp-tags-popup-content'
                                                                        });
                                                                    } catch (e) {

                                                                    }
                                                                </script>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

HTML;

		$this->createadditional_block_tags = $opthtml;
		return $opthtml;
	}

	/**
	 * @return string
	 */
	public function getCreateadditionalBlockType()
	{
		$opthtml = '';
		$opthtml = <<<HTML
                                                <div class="social-group-create-additional-block-item"
                                                     id="additional-block-type">
                                                    <div class="social-group-create-options-item social-group-create-form-field-list-block">

                                                        <div id="GROUP_TYPE_LABEL_block"
                                                             class="social-group-create-options-item-column-left">
                                                            <div class="social-group-create-options-item-name sgcp-block-nonproject">
                                                                Тип группы
                                                            </div>
                                                            <div class="social-group-create-options-item-name sgcp-block-project">
                                                                Тип проекта
                                                            </div>
                                                        </div>
                                                        <div class="social-group-create-options-item-column-right">
                                                            <div class="social-group-create-options-item-column-one">
                                                                <div class="social-group-create-form-field-list">
                                                                    <div class="social-group-create-form-field-list-item">
                                                                        <label class="social-group-create-form-field-list-label"
                                                                               id="GROUP_VISIBLE_LABEL_block">
                                                                            <input type="checkbox" id="GROUP_VISIBLE"
                                                                                   name="GROUP_VISIBLE"
                                                                                   class="social-group-create-form-field-list-input"
                                                                                   value="Y" checked="">
                                                                            <span class="social-group-create-form-field-list-name sgcp-inlineblock-nonproject"
                                                                                  title="Наличие группы видимо всем пользователям, а не только ее участникам">Видимая</span>
                                                                            <span class="social-group-create-form-field-list-name sgcp-inlineblock-project"
                                                                                  title="Наличие проекта видимо всем пользователям, а не только его участникам">Видимый</span>
                                                                        </label>
                                                                    </div>
                                                                    <div class="social-group-create-form-field-list-item">
                                                                        <label class="social-group-create-form-field-list-label"
                                                                               id="GROUP_OPENED_LABEL_block">
                                                                            <input type="checkbox" id="GROUP_OPENED"
                                                                                   value="Y" name="GROUP_OPENED"
                                                                                   class="social-group-create-form-field-list-input">
                                                                            <span class="social-group-create-form-field-list-name sgcp-inlineblock-nonproject"
                                                                                  title="В группу можно вступить без одобрения модератором">Открытая</span>
                                                                            <span class="social-group-create-form-field-list-name sgcp-inlineblock-project"
                                                                                  title="В проект можно вступить без одобрения руководителем или его помощником">Открытый</span>
                                                                        </label>
                                                                    </div>
                                                                    <input type="hidden" value="N" name="GROUP_CLOSED">
                                                                    <div class="social-group-create-form-field-list-item">
                                                                        <label class="social-group-create-form-field-list-label">
                                                                            <input type="checkbox" id="GROUP_PROJECT"
                                                                                   name="GROUP_PROJECT" value="Y"
                                                                                   class="social-group-create-form-field-list-input"
                                                                                   onclick="BXSwitchProject(this.checked)">
                                                                            <span class="social-group-create-form-field-list-name">Проект</span>
                                                                        </label>
                                                                    </div>
                                                                    <div class="social-group-create-form-field-list-item">
                                                                        <label class="social-group-create-form-field-list-label">
                                                                            <input type="checkbox" id="GROUP_LANDING"
                                                                                   name="GROUP_LANDING" value="Y"
                                                                                   class="social-group-create-form-field-list-input">
                                                                            <span class="social-group-create-form-field-list-name">Для публикации</span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

HTML;


		$this->createadditional_block_type = $opthtml;
		return $opthtml;
	}

}


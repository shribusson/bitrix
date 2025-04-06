<?

namespace Local\Fwink\Fields;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Config\ConfigurationException;
use Bitrix\Main\Localization\Loc;
use Local\Fwink\Fields\Value\Base;
use Local\Fwink\Fields\Value\ValueInterface;
use Local\Fwink\Fields\Views\Title\Material;
use Local\Fwink\Fields\Views\ViewInterface;
use LogicException;

class Field
{
    private $info;
    private $gridList;
    /** @var  ViewInterface $editView */
    private $editView;
    /** @var  ViewInterface $showView */
    private $showView;
    private $titleView;
    private $select;
    /** @var Base $value */
    private $value;
    private $valueForEdit;
    private $isEditable = false;
    private $isReadable = false;

    private $urlTemplate;

    /**
     * Field constructor.
     *
     * @param array $fieldParameters
     *
     * @throws ArgumentException
     */
    public function __construct(array $fieldParameters)
    {
        $parametersForFieldInfo = [];

        if (isset($fieldParameters['ENTITY_FIELD'])) {
            $parametersForFieldInfo = FieldInfo::getParametersForFieldInfo($fieldParameters['ENTITY_FIELD']);
        }

        if (isset($fieldParameters['INFO'])) {
            $parametersForFieldInfo = $fieldParameters['INFO'];
        }

        $this->setInfo(new FieldInfo($parametersForFieldInfo));
        $this->setGridList(new GridList($fieldParameters['GRID_LIST'], $this->getInfo()->getName()));

        if ($fieldParameters['EDIT_VIEW']) {
            $this->setEditView($fieldParameters['EDIT_VIEW']);
        }

        if ($fieldParameters['SHOW_VIEW']) {
            $this->setShowView($fieldParameters['SHOW_VIEW']);
        }

        if (isset($fieldParameters['SELECT'])) {
            $this->setSelect($fieldParameters['SELECT']);
        }

        if (isset($fieldParameters['VALUE'])) {
            $this->setValueObject($fieldParameters['VALUE']);
        }

        if (isset($fieldParameters['URL_TEMPLATES'])) {
            $this->setUrlTemplate($fieldParameters['URL_TEMPLATES']);
        }

        $this->setTitleView();
    }

    /**
     * Возвращает информацию о поле в виде объекта.
     *
     * @return mixed
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param FieldInfo $info
     */
    public function setInfo(FieldInfo $info): void
    {
        $this->info = $info;
    }

    /**
     * Устанавливает для поля объект для обработки значения.
     *
     * @param ValueInterface $value
     */
    public function setValueObject(ValueInterface $value): void
    {
        $this->value = $value;
    }

    /**
     * Возвращает объект для работы со списком.
     *
     * @return mixed
     */
    public function getGridList()
    {
        return $this->gridList;
    }

    /**
     * @param GridList $info
     */
    public function setGridList(GridList $info): void
    {
        $this->gridList = $info;
    }

    /**
     * Получить шаблон для отображения заголовка поля.
     *
     * @return Material
     */
    public function getTitleView(): Material
    {
        $this->titleView->setField($this->info);

        return $this->titleView;
    }

    /**
     */
    public function setTitleView(): void
    {
        $this->titleView = new Material();
    }

    /**
     * @return mixed
     */
    public function getSelect()
    {
        return $this->select;
    }

    /**
     * @param mixed $select
     */
    public function setSelect($select): void
    {
        $this->select = $select;
    }

    /**
     * Получение объекта для редактирования поля.
     *
     * @return ViewInterface
     */
    public function getEditView(): ViewInterface
    {
        if ($this->editView === null) {
            throw new LogicException(Loc::getMessage('LOCAL_FIELDS_FIELD_NO_EDIT_FIELD'));
        }

        if ($this->valueForEdit !== null) {
            $this->editView->setValue($this->valueForEdit);
        }

        $this->editView->setField($this->info);

        return $this->editView;
    }

    /**
     * Устанавливает нужный объект для редактирования поля.
     *
     * @param ViewInterface $view
     */
    public function setEditView(ViewInterface $view): void
    {
        $this->editView = $view;
        $this->isEditable = true;
    }

    /**
     * Получение объекта для отображения поля.
     *
     * @return ViewInterface
     * @throws ConfigurationException
     */
    public function getShowView(): ViewInterface
    {
        if ($this->showView === null) {
            throw new ConfigurationException(Loc::getMessage('LOCAL_FIELDS_FIELD_NO_TEMPLATE_FIELD'));
        }

        $this->value->setDataToView($this->showView);

        return $this->showView;
    }

    /**
     * Устанавливает нужный объект для отображения поля.
     *
     * @param ViewInterface $view
     */
    public function setShowView(ViewInterface $view): void
    {
        $this->showView = $view;
        $this->isReadable = true;
    }

    /**
     * Передать объекту для обработки значения поля результат из функции getList().
     *
     * @param array $row
     */
    public function setValueFromDb(array $row): void
    {
        $this->value->setValueFromDb($row, $this->select);
    }

    /**
     * Можно ли отредактировать значение поля?
     *
     * @return bool
     */
    public function isEditable(): bool
    {
        return $this->isEditable;
    }

    /**
     * Получить объект для обработки значения поля.
     *
     * @return ValueInterface
     */
    public function getValue(): ValueInterface
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isReadable(): bool
    {
        return $this->isReadable;
    }

    /**
     * @return mixed
     */
    public function getUrlTemplate()
    {
        return $this->urlTemplate;
    }

    /**
     * @param mixed $urlTemplate
     */
    public function setUrlTemplate($urlTemplate): void
    {
        $this->urlTemplate = $urlTemplate;
    }
}

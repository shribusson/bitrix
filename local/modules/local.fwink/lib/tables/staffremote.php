<?

namespace Local\Fwink\Tables;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data;
use Bitrix\Main\Type;
use Bitrix\Main\ORM\Fields;
use Exception;

class StaffremoteTable extends Data\DataManager
{
    public static function getTableName(): string
    {
        return 'itrack_chart_staffremote';
    }

    /**
     * @return array
     * @throws Exception
     */
    public static function getMap(): array
    {
		return  [
			'ID' => array(
				'primary' => true,
				'autocomplete' => true,
				'data_type' => 'integer',
				'title' => 'ID',
			),
			'ID_PORTAL' => array(
				'data_type' => 'integer',
				'title' => 'ID_PORTAL',
			),
			'ID_STAFF' => array(
				'data_type' => 'integer',
				'title' => 'ID_STAFF',
			),
            'FULL_NAME' => array(
                'data_type' => 'string'
            ),
			'DATE_TRAIN_END' => array(
				'data_type' => 'string',
				'title' => 'DATE_TRAIN_END',
			),
			'TIMESTAMP_X' => array(
				'data_type' => 'string',
				'title' => 'TIMESTAMP_X',
			),
			'LOGIN' => array(
				'data_type' => 'string',
				'title' => 'LOGIN',
			),
			'PASSWORD' => array(
				'data_type' => 'string',
				'title' => 'PASSWORD',
			),
			'CHECKWORD' => array(
				'data_type' => 'string',
				'title' => 'CHECKWORD',
			),
			'ACTIVE' => array(
				'data_type' => 'string',
				'title' => 'ACTIVE',
			),
			'NAME' => array(
				'data_type' => 'string',
				'title' => 'NAME',
			),
			'LAST_NAME' => array(
				'data_type' => 'string',
				'title' => 'LAST_NAME',
			),
			'EMAIL' => array(
				'data_type' => 'string',
				'title' => 'EMAIL',
			),
			'LAST_LOGIN' => array(
				'data_type' => 'string',
				'title' => 'LAST_LOGIN',
			),
			'DATE_REGISTER' => array(
				'data_type' => 'string',
				'title' => 'DATE_REGISTER',
			),
			'LID' => array(
				'data_type' => 'string',
				'title' => 'LID',
			),
			'PERSONAL_PROFESSION' => array(
				'data_type' => 'string',
				'title' => 'PERSONAL_PROFESSION',
			),
			'PERSONAL_WWW' => array(
				'data_type' => 'string',
				'title' => 'PERSONAL_WWW',
			),
			'PERSONAL_ICQ' => array(
				'data_type' => 'string',
				'title' => 'PERSONAL_ICQ',
			),
			'PERSONAL_GENDER' => array(
				'data_type' => 'string',
				'title' => 'PERSONAL_GENDER',
			),
			'PERSONAL_BIRTHDATE' => array(
				'data_type' => 'string',
				'title' => 'PERSONAL_BIRTHDATE',
			),
			'PERSONAL_PHOTO' => array(
				'data_type' => 'string',
				'title' => 'PERSONAL_PHOTO',
			),
			'PERSONAL_PHONE' => array(
				'data_type' => 'string',
				'title' => 'PERSONAL_PHONE',
			),
			'PERSONAL_FAX' => array(
				'data_type' => 'string',
				'title' => 'PERSONAL_FAX',
			),
			'PERSONAL_MOBILE' => array(
				'data_type' => 'string',
				'title' => 'PERSONAL_MOBILE',
			),
			'PERSONAL_PAGER' => array(
				'data_type' => 'string',
				'title' => 'PERSONAL_PAGER',
			),
			'PERSONAL_STREET' => array(
				'data_type' => 'string',
				'title' => 'PERSONAL_STREET',
			),
			'PERSONAL_MAILBOX' => array(
				'data_type' => 'string',
				'title' => 'PERSONAL_MAILBOX',
			),
			'PERSONAL_CITY' => array(
				'data_type' => 'string',
				'title' => 'PERSONAL_CITY',
			),
			'PERSONAL_STATE' => array(
				'data_type' => 'string',
				'title' => 'PERSONAL_STATE',
			),
			'PERSONAL_ZIP' => array(
				'data_type' => 'string',
				'title' => 'PERSONAL_ZIP',
			),
			'PERSONAL_COUNTRY' => array(
				'data_type' => 'string',
				'title' => 'PERSONAL_COUNTRY',
			),
			'PERSONAL_NOTES' => array(
				'data_type' => 'string',
				'title' => 'PERSONAL_NOTES',
			),
			'WORK_COMPANY' => array(
				'data_type' => 'string',
				'title' => 'WORK_COMPANY',
			),
			'WORK_DEPARTMENT' => array(
				'data_type' => 'string',
				'title' => 'WORK_DEPARTMENT',
			),
			'WORK_POSITION' => array(
				'data_type' => 'string',
				'title' => 'WORK_POSITION',
			),
			'WORK_WWW' => array(
				'data_type' => 'string',
				'title' => 'WORK_WWW',
			),
			'WORK_PHONE' => array(
				'data_type' => 'string',
				'title' => 'WORK_PHONE',
			),
			'WORK_FAX' => array(
				'data_type' => 'string',
				'title' => 'WORK_FAX',
			),
			'WORK_PAGER' => array(
				'data_type' => 'string',
				'title' => 'WORK_PAGER',
			),
			'WORK_STREET' => array(
				'data_type' => 'string',
				'title' => 'WORK_STREET',
			),
			'WORK_MAILBOX' => array(
				'data_type' => 'string',
				'title' => 'WORK_MAILBOX',
			),
			'WORK_CITY' => array(
				'data_type' => 'string',
				'title' => 'WORK_CITY',
			),
			'WORK_STATE' => array(
				'data_type' => 'string',
				'title' => 'WORK_STATE',
			),
			'WORK_ZIP' => array(
				'data_type' => 'string',
				'title' => 'WORK_ZIP',
			),
			'WORK_COUNTRY' => array(
				'data_type' => 'string',
				'title' => 'WORK_COUNTRY',
			),
			'WORK_PROFILE' => array(
				'data_type' => 'string',
				'title' => 'WORK_PROFILE',
			),
			'WORK_LOGO' => array(
				'data_type' => 'string',
				'title' => 'WORK_LOGO',
			),
			'WORK_NOTES' => array(
				'data_type' => 'string',
				'title' => 'WORK_NOTES',
			),
			'ADMIN_NOTES' => array(
				'data_type' => 'string',
				'title' => 'ADMIN_NOTES',
			),
			'STORED_HASH' => array(
				'data_type' => 'string',
				'title' => 'STORED_HASH',
			),
			'XML_ID' => array(
				'data_type' => 'string',
				'title' => 'XML_ID',
			),
			'PERSONAL_BIRTHDAY' => array(
				'data_type' => 'string',
				'title' => 'PERSONAL_BIRTHDAY',
			),
			'EXTERNAL_AUTH_ID' => array(
				'data_type' => 'string',
				'title' => 'EXTERNAL_AUTH_ID',
			),
			'CHECKWORD_TIME' => array(
				'data_type' => 'string',
				'title' => 'CHECKWORD_TIME',
			),
			'SECOND_NAME' => array(
				'data_type' => 'string',
				'title' => 'SECOND_NAME',
			),
			'CONFIRM_CODE' => array(
				'data_type' => 'string',
				'title' => 'CONFIRM_CODE',
			),
			'LOGIN_ATTEMPTS' => array(
				'data_type' => 'string',
				'title' => 'LOGIN_ATTEMPTS',
			),
			'LAST_ACTIVITY_DATE' => array(
				'data_type' => 'string',
				'title' => 'LAST_ACTIVITY_DATE',
			),
			'AUTO_TIME_ZONE' => array(
				'data_type' => 'string',
				'title' => 'AUTO_TIME_ZONE',
			),
			'TIME_ZONE' => array(
				'data_type' => 'string',
				'title' => 'TIME_ZONE',
			),
			'TIME_ZONE_OFFSET' => array(
				'data_type' => 'string',
				'title' => 'TIME_ZONE_OFFSET',
			),
			'TITLE' => array(
				'data_type' => 'string',
				'title' => 'TITLE',
			),
			'BX_USER_ID' => array(
				'data_type' => 'string',
				'title' => 'BX_USER_ID',
			),
			'LANGUAGE_ID' => array(
				'data_type' => 'string',
				'title' => 'LANGUAGE_ID',
			),
			'BLOCKED' => array(
				'data_type' => 'string',
				'title' => 'BLOCKED',
			),
			'EXTERNAL_GUID' => array(
				'data_type' => 'string',
				'title' => 'EXTERNAL_GUID',
			),
		];
	}
}

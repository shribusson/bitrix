<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

global $USER_FIELD_MANAGER;

$fields = $USER_FIELD_MANAGER->GetUserFields("TASKS_TASK");

echo "<h1>Пользовательские поля TASKS_TASK</h1><pre>";

foreach ($fields as $field) {
    echo "FIELD_NAME: " . $field['FIELD_NAME'] . PHP_EOL;
    echo "USER_TYPE_ID: " . $field['USER_TYPE_ID'] . PHP_EOL;
    echo "MULTIPLE: " . $field['MULTIPLE'] . PHP_EOL;
    echo "MANDATORY: " . $field['MANDATORY'] . PHP_EOL;

    // Если это список (enumeration) — покажем элементы
    if ($field['USER_TYPE_ID'] === 'enumeration') {
        echo "ENUM VALUES:" . PHP_EOL;
        $rsEnum = CUserFieldEnum::GetList([], ["USER_FIELD_ID" => $field["ID"]]);
        while ($arEnum = $rsEnum->Fetch()) {
            echo "- [{$arEnum['ID']}] " . $arEnum["VALUE"] . PHP_EOL;
        }
    }

    echo str_repeat("-", 30) . PHP_EOL;
}

echo "</pre>";
?>


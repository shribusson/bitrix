// <?php
// require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
// $APPLICATION->SetTitle("Все задачи (SPA)");
// 
// if (CModule::IncludeModule('tasks')) {
//     // Установка фильтра для получения всех задач
//     $arFilter = array(
//         "CHECK_PERMISSIONS" => "Y"
//     );
//     
//     // Отключаем проверку на участие в задаче
//     global $USER;
//     if ($USER->IsAdmin()) {
//         // Для администраторов добавляем специальные настройки
//         $arFilter["SUBORDINATE_TASKS"] = "Y";
//         $arFilter["FAVORITE"] = "N";
//         $arFilter["OVERDUED"] = "N";
//         $arFilter["MARKED"] = "N";
//         $arFilter["MEMBER"] = "N"; // Не является участником задачи
//     }
//     
//     // Используем стандартный компонент task.list
//     $APPLICATION->IncludeComponent(
//         "bitrix:tasks.task.list",
//         ".default",
//         array(
//             "USER_ID" => 0,
//             "GROUP_ID" => 0,
//             "SHOW_TOOLBAR" => "Y",
//             "SHOW_SEARCH_FIELD" => "Y",
//             "SHOW_NAVIGATION" => "Y",
//             "SHOW_CREATE_TASK_BUTTON" => "Y",
//             "FILTER_ID" => "all_tasks_filter",
//             "FILTER" => $arFilter,
//             "SET_TITLE" => "Y",
//             "PATH_TO_USER_PROFILE" => "/company/personal/user/#user_id#/",
//             "NAME_TEMPLATE" => "#LAST_NAME# #NAME#",
//             // Дополнительные параметры
//             "TASKS_ALWAYS_SHOW_CONTROLS" => "Y",
//             "SHOW_USER_FIELD_CHANGE_BTN" => "Y",
//             "COMPANY_WORKTIME" => array(
//                 "HOURS" => array(9, 10, 11, 12, 13, 14, 15, 16, 17, 18),
//                 "WEEK_DAYS" => array("MO", "TU", "WE", "TH", "FR")
//             )
//         ),
//         false
//     );
// } else {
//     echo "Модуль задач не установлен";
// }
// 
// require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
// ?>

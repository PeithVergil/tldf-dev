<?php
global $lang_poll, $weekday_poll, $months_poll, $color_array_poll;
/* Кодування */
$lang_poll["charset"]   = "windows-1251";

/* Загальне */
$lang_poll["Logout"]    = "Вихід";
$lang_poll["FormUndo"]  = "Відмінити зміни";
$lang_poll["FromClear"] = "Очистити";
$lang_poll["FormEnter"] = "Введіть ім’я користувача та пароль";
$lang_poll["FormWrong"] = "Невірне ім’я користувача або пароль";
$lang_poll["FormOK"]    = "OK";
$lang_poll["Updated"]   = "Зміни внесено!";
$lang_poll["NoUpdate"]  = "Помилка! Зміни не внесено!";
$lang_poll["Confirm"]   = "Ви впевнені?";
$lang_poll["NavNext"]   = "Наступна сторінка";
$lang_poll["NavPrev"]   = "Попередня сторінка";
$lang_poll["License"]   = "Ліцензійна угода";
$lang_poll["ScrollTxt"] = "Натисніть кнопку PAGE DOWN щоб прочитати решту угоди.";

/* Шаблони */
$lang_poll["Templates"]  = "Шаблони";
$lang_poll["tpl_exist"]  = "Ім’я шаблону вже існує.";
$lang_poll["tpl_new"]    = "Додати новий набір шаблонів.";
$lang_poll["tpl_succes"] = "Запис успішно додано!"; 
$lang_poll["tpl_bad"]    = "Невірне ім’я шаблону!";
$lang_poll["tpl_save"]   = "Зберегти";
$lang_poll["preview"]    = "Переглянути";
$lang_poll["newtpl"]     = "Новий набір шаблонів";

/* Список голосувань */
$lang_poll["IndexTitle"]  = "Список голосувань";
$lang_poll["IndexQuest"]  = "Запитання";
$lang_poll["IndexID"]     = "ID голосування";
$lang_poll["IndexDate"]   = "Дата";
$lang_poll["IndexDays"]   = "Днів";
$lang_poll["IndexExp"]    = "Завершення";
$lang_poll["IndexExpire"] = "завершено";
$lang_poll["IndexNever"]  = "ніколи";
$lang_poll["IndexStat"]   = "Статистика";
$lang_poll["IndexCom"]    = "коментарі";
$lang_poll["IndexAct"]    = "Дії";
$lang_poll["IndexDel"]    = "витерти";

/* Створити нове голосування */
$lang_poll["NewTitle"]  = "Створити нове голосування";
$lang_poll["NewOption"] = "Варіант";
$lang_poll["NewNoQue"]  = "Ви забули ввести запитання";
$lang_poll["NewNoOpt"]  = "Ви забули ввести варіанти відповідей";

/* Редагувати голосування */
$lang_poll["EditStat"]  = "Статус";
$lang_poll["EditText"]  = "Редагувати це голосування";
$lang_poll["EditReset"] = "Очистити результати цього голосування";
$lang_poll["EditOn"]    = "вкл.";
$lang_poll["EditOff"]   = "викл.";
$lang_poll["EditHide"]  = "заховане";
$lang_poll["EditLgOff"] = "log викл.";
$lang_poll["EditLgOn"]  = "log вкл.";
$lang_poll["EditAdd"]   = "Додати варіанти";
$lang_poll["EditNo"]    = "Варіант(и) не додано!";
$lang_poll["EditOk"]    = "Варіант(и) додано!";
$lang_poll["EditSave"]  = "Зберегти зміни";
$lang_poll["EditOp"]    = "Необхідно, принаймні, два варіанти!";
$lang_poll["EditMis"]   = "Не визначене запитання та відповіді!";
$lang_poll["EditDel"]   = "Щоб видалити варіант - залишіть поле пустим";
$lang_poll["EditCom"]   = "Дозволити коментарі";

/* Основні настройки */
$lang_poll["SetTitle"]   = "Основні настройки";
$lang_poll["SetOption"]  = "Настройки таблиць, шрифтів та кольорів";
$lang_poll["SetMisc"]    = "Різне";
$lang_poll["SetText"]    = "Редагувати основні настройки";
$lang_poll["SetURL"]     = "URL папки з графікою";
$lang_poll["SetBURL"]    = "URL папки з голосуванням";
$lang_poll["SetNo"]      = "Без слешу вкінці";
$lang_poll["SetLang"]    = "Мова";
$lang_poll["SetPoll"]    = "Назва голосування";
$lang_poll["SetButton"]  = "Кнопка для голосування";
$lang_poll["SetResult"]  = "Лінк до результатів";
$lang_poll["SetVoted"]   = "Вже проголосували";
$lang_poll["SetComment"] = "Ваш коментар";
$lang_poll["SetTab"]     = "Ширина таблиці";
$lang_poll["SetBarh"]    = "Висота кольорової стрічки результату";
$lang_poll["SetBarMax"]  = "Maкс. довжина стрічки";
$lang_poll["SetTabBg"]   = "Колір фону таблиці";
$lang_poll["SetFrmCol"]  = "Колір рамки";
$lang_poll["SetFontCol"] = "Колір шрифту";
$lang_poll["SetFace"]    = "Шрифт";
$lang_poll["SetShow"]    = "Показувати результати як";
$lang_poll["SetPerc"]    = "відсотки";
$lang_poll["SetVotes"]   = "голоси";
$lang_poll["SetCheck"]   = "Перевірка";
$lang_poll["SetNoCheck"] = "не перевіряти";
$lang_poll["SetIP"]      = "таблиця IP адресів";
$lang_poll["CheckIP"]       = "Check IP";
$lang_poll["CheckUsername"] = "Check username";
$lang_poll["SetTime"]    = "час для заблокування";
$lang_poll["SetHours"]   = "годин";
$lang_poll["SetOffset"]  = "Різниця в часі з сервером";
$lang_poll["SetEntry"]   = "Кількість коментарів на сторінці";
$lang_poll["SetSubmit"]  = "Внести зміни";
$lang_poll["SetEmpty"]   = "Невірне значення";
$lang_poll["SetSort"]    = "Порядок";
$lang_poll["SetAsc"]     = "по зростанню";
$lang_poll["SetDesc"]    = "по спаданню";
$lang_poll["Setusort"]   = "не сортувати";
$lang_poll["SetOptions"] = "Варіанти в новому голосуванні";
$lang_poll["SetPolls"]   = "Голосувань на сторінку";

/* Зміна паролю */
$lang_poll["PwdTitle"] = "Змінити пароль";
$lang_poll["PwdText"]  = "Змінити ім’я користувача або пароль";
$lang_poll["PwdUser"]  = "Ім’я користувача";
$lang_poll["PwdPass"]  = "Пароль";
$lang_poll["PwdConf"]  = "Підтвердити пароль";
$lang_poll["PwdNoUsr"] = "Ви не ввели імені користувача";
$lang_poll["PwdNoPwd"] = "Ви не ввели пароль";
$lang_poll["PwdBad"]   = "Пароль не співпадає";

/* Статистика голосувань */
$lang_poll["StatCrea"]  = "Створено";
$lang_poll["StatAct"]   = "Активне";
$lang_poll["StatReset"] = "Витерти статистику";
$lang_poll["StatDis"]   = "log. виключено для цього голосування";
$lang_poll["StatTotal"] = "Загалом проголосувало";
$lang_poll["StatDay"]   = "голосів за день";

/* Коментарі до голосувань */
$lang_poll["ComTotal"]  = "Загальна кількість коментарів";
$lang_poll["ComName"]   = "Ім’я";
$lang_poll["ComPost"]   = "надіслано";
$lang_poll["ComDel"]    = "Ви дійсно хочете витерти повідомлення?";

/* Допомога */
$lang_poll["Help"]       = "Допомога";
$lang_poll["HelpPoll"]   = "Щоб додати голосування до Вашої сторінки, вставте відповідний код, поданий нижче";
$lang_poll["HelpRand"]   = "Відображати будь-яке випадкове голосування";
$lang_poll["HelpNew"]    = "Завжди показувати останнє голосування";
$lang_poll["HelpSyntax"] = "Синтаксис";

/* Дні */
$weekday_poll[0] = "Неділя";
$weekday_poll[1] = "Понеділок";
$weekday_poll[2] = "Вівторок";
$weekday_poll[3] = "Середа";
$weekday_poll[4] = "Четвер";
$weekday_poll[5] = "П’ятниця";
$weekday_poll[6] = "Субота";

/* Місяці */
$months_poll[0]  = "Січень";
$months_poll[1]  = "Лютий";
$months_poll[2]  = "Березень";
$months_poll[3]  = "Квітень";
$months_poll[4]  = "Травень";
$months_poll[5]  = "Червень";
$months_poll[6]  = "Липень";
$months_poll[7]  = "Серпень";
$months_poll[8]  = "Вересень";
$months_poll[9]  = "Жовтень";
$months_poll[10] = "Листопад";
$months_poll[11] = "Грудень";

/* translating this array does not change the reference */
$color_array_poll[0]  = "голубий";
$color_array_poll[1]  = "синій";
$color_array_poll[2]  = "коричневий";
$color_array_poll[3]  = "темно-зелений";
$color_array_poll[4]  = "золотий";
$color_array_poll[5]  = "зелений";
$color_array_poll[6]  = "сірий";
$color_array_poll[7]  = "оранжевий";
$color_array_poll[8]  = "рожевий";
$color_array_poll[9]  = "малиновий";
$color_array_poll[10] = "червоний";
$color_array_poll[11] = "жовтий";

?>
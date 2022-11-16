<?php

if (isset($_GET['action']) && !empty(isset($_GET['action'])) ) {
    $action = $_GET['action'];
    switch( $action ) {
        case "first": return validateHtml();
        case "second": return highlightWords();
    }
}

function validateHtml(){
    if (isset($_POST["message"])) {
        $user_post = $_POST["message"];
        $user_post_len = strlen($user_post);

        //Регексп для проверки целого тэга на валидность в соответствии с задачей
        $tag_check_regex = "/^(<\s*(code|i|strike|strong|(a\s+((href=\".*\"\s+title=\".*\")|(title=\".*\"\s+href=\".*\"))))\s*>)|(<\/\s*(code|i|strike|strong|a)\s*>)$/";
        //Регексп для вычленения имени тэга
        $tag_content_regex = "/((?<=<\/?\s*)(code|i|strike|strong)(?=\s*>))|((?<=<\/?\s*)a(?=\s+((href=\".*\"\s+title=\".*\")|(title=\".*\"\s+href=\".*\"))\s*>))/";
        $tag_content_regex = "/(?<=<\/?\s*)(a|code|i|strike|strong)(?=.*)/";


        //Текущий записываемый посимвольно тэг
        $current_tag = "";
        //Стэк открытых тэгов
        $tag_stack = [];

        //Обходим строку посимвольно
        for ($i=0; $i < $user_post_len; $i++) {
            $char = $user_post[$i];

            //Проверяем открытие любого тэга
            if ($char === "<") {
                //Если мы пишем в данный момент какой-то тэг, то повторное открытие внутри него запрещено
                if ($current_tag !== "") { echo "invalid"; return; }
                $current_tag .= $char;
            }
            //Проверяем закрытие любого тэга
            else if ($char === ">") {
                //Если мы не писали какой-то тэг, то закрывать нечего
                if ($current_tag === "") { echo "invalid"; return; }
                $current_tag .= $char;
                //Проверяем найденный закрытый тэг на валидность
                if (preg_match($tag_check_regex, $current_tag) !== 1) { echo "invalid"; return; }
                //Вычленяем название тэга (регэксп на positive lookahead/lookbehind не работает не в каком виде, хотя он верный)
                $current_tag_no_slash = str_replace("/", "", $current_tag);
                $tag_name = str_starts_with($current_tag_no_slash, "<a") ? "a"
                    : (str_starts_with($current_tag_no_slash, "<strong") ? "strong"
                    : (str_starts_with($current_tag_no_slash, "<strike") ? "strike"
                        : (str_starts_with($current_tag_no_slash, "<i") ? "i"
                            : (str_starts_with($current_tag_no_slash, "<code") ? "code" : ""))));
                //Если тэг является закрывающим, проверяем что ему есть что закрывать
                if (str_starts_with($current_tag, "</")) {
                    //Если имена предыдущего открытого тэги и этого закрывающего тэга не совпадают, валидация провалена
                    $last_opened_tag = array_peek($tag_stack);
                    if ($last_opened_tag === null ||  $last_opened_tag !== $tag_name) { echo "invalid"; return; }
                    //Если все нормально, удаляем открывающий тэг из предыдущих
                    array_pop($tag_stack);
                }
                //Если тэг открывающий, добавим его к предыдущим открывающим тэгам
                else
                    array_push($tag_stack, $tag_name);
                //Сбрасываем текущий записываемый тэг, т.к. он закрыт
                $current_tag = "";
            }
            //Если мы пишем какой-то тэг, то добавляем символ в него
            else if ($current_tag !== "") $current_tag .= $char;
        }

        //Если строка закончилась, а какой-то тэг остался открытым, то валидация провалена
        if ($current_tag !== "") { echo "invalid"; return; }
        //Если строка закончилась, а у открывающего тэга нет закрытия, то валидация провалена
        if (count($tag_stack) !== 0) { echo "invalid"; return; }

        echo "valid";
    }
    else
        echo "invalid";
}

function highlightWords(){
    if (isset($_POST["string"]) and isset($_POST["words"])) {
        $text = $_POST["string"];
        $array_of_words = explode(",",$_POST["words"]);
        // проходим массив слов
        foreach($array_of_words as $word){
            //с помощью функции PHP preg_replace и регулярного выражения находим слово с учетом регистра и добавляем к найденному слову скобки
            $text =  preg_replace('/\b' . $word . '\b/ui', '['.'$0'.']', $text);
        }
        echo $text;
    }

}

function array_peek($array) {
    return count($array) === 0 ? null : $array[count($array)-1];
}
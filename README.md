Реализация функции для валидации постов юзера. 
Пользователь в сообщениях может использовать только следующие HTML теги и только с такими атрибутами:
<a href="" title=""> </a>
<code> </code>
<i> </i>
<strike> </strike>
<strong> </strong>
Реализована проверка на закрытие тегов и корректную вложенность тегов, а также на валидность XHTML.
----
строка $text в UTF-8 кодировке и массив слов $array_of_words (в той же самой кодировке). 
Реализаовано выделение первых вхождений каждого из слов с помощью квадратных скобок (Вася заменить на [Вася])
функция function highlightWords($text, $array_of_words).



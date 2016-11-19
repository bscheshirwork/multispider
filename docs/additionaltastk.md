##Дополнительное задание на проверку sql

В БД MySQL есть 2 таблицы (фотографии и комментарии к ним):
```
CREATE TABLE `photos` (
  `photo_id` int(11) NOT NULL,
  `photo_title` tinytext NOT NULL,
  PRIMARY KEY (`photo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `photo_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `comment_text` text NOT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;
```
Напишите SQL-запрос, который выведет список фотографий, с указанием количества комментариев, отсортированные в порядке убывания их количества, у которых меньше 5 пользователей-комментаторов.



###Запрос:

```
SELECT `photos`.`photo_title`, count(`comments`.`comment_id`)
FROM `photos` LEFT JOIN `comments` ON (`photos`.`photo_id` = `comments`.`photo_id`)
WHERE `photos`.`photo_id` IN (
  SELECT `comments`.`photo_id` FROM `comments` GROUP BY (`comments`.`photo_id`) HAVING count(DISTINCT `comments`.`user_id`) < 5
)
GROUP BY `photos`.`photo_id`
ORDER BY count(`comments`.`comment_id`) DESC
;
```
###Выборка:
```
photo_title;count(`comments`.`comment_id`)
Шестое фото;11
Третье фото;8
Четвёртое фото;6
Пятое фото;3
Восьмое фото;1
```
###Промежуточные запросы, исходные данные:

Создать тестовую среду
```
CREATE TABLE `photos` (
  `photo_id` int(11) NOT NULL,
  `photo_title` tinytext NOT NULL,
  PRIMARY KEY (`photo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `photo_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `comment_text` text NOT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;
;
insert into `photos` values
(1,'Первое фото'),
(2,'Второе фото'),
(3,'Третье фото'),
(4,'Четвёртое фото'),
(5,'Пятое фото'),
(6,'Шестое фото'),
(7,'Седьмое фото'),
(8,'Восьмое фото'),
(9,'Девятое фото')
;
insert into `comments` values
(1,1,1,'Первое фото - комментарий 1 пользователя 1'),
(2,1,1,'Первое фото - комментарий 2 пользователя 1'),
(3,1,2,'Первое фото - комментарий 1 пользователя 2'),
(4,1,2,'Первое фото - комментарий 2 пользователя 2'),
(5,1,3,'Первое фото - комментарий 1 пользователя 3'),
(6,1,4,'Первое фото - комментарий 1 пользователя 4'),
(7,1,5,'Первое фото - комментарий 1 пользователя 5'),

(8,2,1,'Второе фото - комментарий 1 пользователя 1'),
(9,2,2,'Второе фото - комментарий 1 пользователя 2'),
(10,2,3,'Второе фото - комментарий 1 пользователя 3'),
(11,2,4,'Второе фото - комментарий 1 пользователя 4'),
(12,2,5,'Второе фото - комментарий 1 пользователя 5'),

(13,3,1,'Третье фото - комментарий 1 пользователя 1'),
(14,3,1,'Третье фото - комментарий 2 пользователя 1'),
(15,3,2,'Третье фото - комментарий 1 пользователя 2'),
(16,3,2,'Третье фото - комментарий 2 пользователя 2'),
(17,3,3,'Третье фото - комментарий 1 пользователя 3'),
(18,3,3,'Третье фото - комментарий 2 пользователя 3'),
(19,3,4,'Третье фото - комментарий 1 пользователя 4'),
(20,3,4,'Третье фото - комментарий 2 пользователя 4'),

(21,4,1,'Четвёртое фото - комментарий 1 пользователя 1'),
(22,4,1,'Четвёртое фото - комментарий 2 пользователя 1'),
(23,4,1,'Четвёртое фото - комментарий 3 пользователя 1'),
(24,4,1,'Четвёртое фото - комментарий 4 пользователя 1'),
(25,4,2,'Четвёртое фото - комментарий 1 пользователя 2'),
(26,4,2,'Четвёртое фото - комментарий 2 пользователя 2'),

(27,5,2,'Пятое фото - комментарий 1 пользователя 2'),
(28,5,3,'Пятое фото - комментарий 1 пользователя 3'),
(29,5,4,'Пятое фото - комментарий 1 пользователя 4'),

(30,6,1,'Шестое фото - комментарий 1 пользователя 1'),
(31,6,1,'Шестое фото - комментарий 2 пользователя 1'),
(32,6,1,'Шестое фото - комментарий 3 пользователя 1'),
(33,6,1,'Шестое фото - комментарий 4 пользователя 1'),
(34,6,1,'Шестое фото - комментарий 5 пользователя 1'),
(35,6,2,'Шестое фото - комментарий 1 пользователя 2'),
(36,6,2,'Шестое фото - комментарий 2 пользователя 2'),
(37,6,2,'Шестое фото - комментарий 3 пользователя 2'),
(38,6,2,'Шестое фото - комментарий 4 пользователя 2'),
(39,6,3,'Шестое фото - комментарий 1 пользователя 3'),
(40,6,3,'Шестое фото - комментарий 2 пользователя 3'),

(41,8,5,'Восьмое фото - комментарий 1 пользователя 5')
;
```

При построении запроса проверяем логику
```
SELECT `comments`.`photo_id`,count(DISTINCT (`comments`.`user_id`)) FROM `comments` GROUP BY (`comments`.`photo_id`)
```
```
photo_id;count(DISTINCT (`comments`.`user_id`))
1;5
2;5
3;4
4;2
5;3
6;3
8;1
```

```
SELECT `comments`.`photo_id` FROM `comments` GROUP BY (`comments`.`photo_id`) HAVING count(DISTINCT (`comments`.`user_id`)) < 5
```
```
photo_id
3
4
5
6
8
```

```
SELECT `photos`.`photo_title`, count(`comments`.`comment_id`) FROM `photos` LEFT JOIN `comments` ON (`photos`.`photo_id` = `comments`.`photo_id`) GROUP BY `photos`.`photo_id` ORDER BY count(`comments`.`comment_id`) DESC
```
```
photo_title;count(`comments`.`comment_id`)
Шестое фото;11
Третье фото;8
Первое фото;7
Четвёртое фото;6
Второе фото;5
Пятое фото;3
Восьмое фото;1
Седьмое фото;0
Девятое фото;0
```

```
SELECT `photos`.`photo_title`, count(`comments`.`comment_id`) 
FROM `photos` LEFT JOIN `comments` ON (`photos`.`photo_id` = `comments`.`photo_id`) 
WHERE `photos`.`photo_id` IN (
	SELECT `comments`.`photo_id` FROM `comments` GROUP BY (`comments`.`photo_id`) HAVING count(DISTINCT `comments`.`user_id`) < 5
)
GROUP BY `photos`.`photo_id` 
ORDER BY count(`comments`.`comment_id`) DESC
;
```
```
photo_title;count(`comments`.`comment_id`)
Шестое фото;11
Третье фото;8
Четвёртое фото;6
Пятое фото;3
Восьмое фото;1
```

USE `627089-doingsdone`;

-- Добавляет пользователей 
INSERT INTO user(name, email, password) 
VALUES
	('Александр', 'alexander@mail.ru', 'password1'),
	('Екатерина', 'katya@mail.ru', 'password2');

-- Добавляет проект и пользователя, создавшего его
INSERT INTO category(name, user_id) 
VALUES
	('Входящие', 1),
	('Учеба', 1),
	('Работа', 2),
	('Домашние дела', 1),
	('Авто', 1);

-- Добавляет задачи 
INSERT INTO task(name, dt_due, category_id, user_id) 
VALUES
	('Собеседование в IT компании', '2019-12-01 09:00:00', 3, 2),
	('Выполнить тестовое задание', '2019-12-25 08:30:00', 3, 2),
	('Сделать задание первого раздела', '2019-12-21 15:00:00', 2, 1),
	('Встреча с другом', '2019-12-22 11:00:00', 1, 1),
	('Купить корм для кота', NULL, 4, 1),
	('Заказать пиццу', NULL , 4, 1);
    
-- Возвращает список проектов для одного пользователя
SELECT name FROM category WHERE user_id = 1;

-- Возвращает список задач для одного проекта 
SELECT name FROM task WHERE category_id = 4;

-- Помечает задачу как выполненную
UPDATE task SET dt_complete = '2019-02-16 16:30:00' WHERE id = 6;

-- Обновляет название задачи по ее идентификатору
UPDATE task SET name = 'Купить корм для рыбок' WHERE id = 5;


# 1. Требования

а. Erlang не ниже 18 из репозитория:
https://www.erlang-solutions.com/resources/download.html

или скомпилированый из исходников:
http://www.erlang.org/downloads

б. GIT
в. make
г. libssl
д. имя машины должно быть с доменом (fully qualified)

## 1.1 Настройка:

Все настройки хранятся в файле src/cmd_server.app.src
При компиляции данные переносятся в ebin/cmd_server.app.
Можно непосредственно редактировать файл настроек в ebin, но там без комментариев всё и данные могут быть перезаписаны из src директории.

Все значимые модули после компиляции находятся в ebin. Все компилированные зависимости находятся в deps/*/ebin.
Можно взять все файлы из всех каталогов ebin и положить всё в одну директорию. Работать будет.

Директорию src можно удалить, она не нужна для исполнения.

# Команды

## 2. Сборка
make all

#3. Запуск в консоли
make run

4. Запуск демоном
make daemon

5. Убить сервер
make kill

6. Настройки
находятся в ebin/cmd_server.app этот файл генерируется из srv/cmd_server.app.src во время сборки.

7. Логирование
Логирует в каталог log/.
Доступны 3 уровня логирования (3 разных файла).

console.log -- сообщения уровня info и выше.
error.log -- сообщения уровня error и выше.
crash.log -- падения внутренних процессов из-за чего-либо.

8. База данных
make dump -- сделать дамп базы (только схема) и положить в database.sql
make db -- восстановить базу из дампа.

9. GeoIP.
make geoip

Обновить геоданные.

Файлы с гео данными от MaxMind находятся в
deps/geoip/priv

При каждом развертывании проекта следует их обвноялть. Сервер перезагрузить.

10. Очистка логов
make clean

11. Обновление MaxMing GEOIP данных
make geoip
данная команда должна выполнятся при запущенном сервере.
осуществляет обновление файлов + обновление данных в памяти безе перезагрузки сервера

12. Очистка базы данных.
make clear_db

13. Загрузка файла в базу
make insert_file filename=excfg data=/path_to_file priority=1

Доступные параметры:
  filename - имя файла в базе
  data -- путь к файлу(колонка data будет содержать данные из этого файла)
  country -- страна
  sys_ver -- версия системы
	client_id -- клиент ID
	importance_low
	importance_high
	userdefined_low
	userdefined_high
	priority -- приоритет

Пример:

	make insert_file priority=1 data=./Makefile filename=excfg --- записать новый файл
	make insert_file priority=1 data=./Makefile filename=excfg id=21 --- изменить сведения о файле с id=21 (либо вставить новую запись)

14. Удаление файла
make delete_file id=ID

Пример:
	make delete_file id=21 -- удалить файл с ID=21


15. Вставить конфиг
make insert_config

Доступные параметры:
	version -- версия
	data -- путь к файлу(колонка data будет содержать данные из этого файла)
	group
	country
	sys_ver
	importance_low
	importance_high
	userdefined_low
	userdefined_high
	client_id

Пример:
	make insert_config version=1 data=./Makefile id=21 --- изменить сведения о конфиге с id=21 (либо вставить новую запись)

16. Удалить конфиг
make delete_config id=ID

Пример:
	make delete_config id=21

17. Обновить правила для importance
make update_importance

18. Обновить блеклист айпишников клиентов.
make update_blacklist

19. Обновить фильтры
make update_filters

Запуск
    docker compose up --build -d

    Apache в контейнере работает с uid/gid 1000, чтобы записывать загруженные фото
    в примонтированную папку. Если uid пользователя не 1000, собрать так:
        docker compose build --build-arg UID=$(id -u) --build-arg GID=$(id -g)

Адреса
    http://localhost:8080  - сайт
    http://localhost:8081  - Adminer (сервер: db, пользователь: user, пароль: password, база: appdb)

Админ-панель (/edit)
    Вход по отдельному паролю. Его хэш задаётся в docker-compose.yml
    (ADMIN_PASSWORD_HASH_B64). Сгенерировать новый:
    php -r 'echo base64_encode(password_hash("новый_пароль", PASSWORD_DEFAULT));'

    CRUD сотрудников: каждая операция в своём файле, отдельный JSON-запрос
    (видно в DevTools -> Network -> Fetch/XHR):
        api/read    GET   - список сотрудников
        api/create  POST  - добавление
        api/update  POST  - изменение
        api/delete  POST  - удаление
        api/get_job_titles GET - список должностей
        api/upload_photo POST - загрузка фото (multipart: id, photo)

    Бэкап: кнопки "Скачать бэкап" (/export) и "Загрузить бэкап" (/import).

База данных
    arch.sql загружается автоматически при первом запуске.
    Если таблиц нет (база уже была создана раньше), загрузить вручную:
        docker compose exec -T db mysql -uuser -ppassword appdb < arch.sql
    Проверить:
        docker compose exec db mysql -uuser -ppassword appdb -e "SHOW TABLES"

Перезапуск
    docker compose down
    docker compose up --build -d

Полный сброс базы (удалит все данные)
    docker compose down -v
    docker compose up --build -d

Фото сотрудников
    Хранятся в public/catalogue_images/, имя файла: <id сотрудника>_<время загрузки>.<расширение>,
    например 16_2026-03-19_00-13-33.jpg. Загружаются из админки (/edit) при добавлении
    или редактировании сотрудника; старое фото при замене и при удалении сотрудника удаляется.

Структура
    public/   - корень сайта (DocumentRoot Apache): страницы, api/, css/, js/, images/,
                catalogue_images/. Снаружи доступно только содержимое этой папки.
    includes/, db.php, logic.php - общий PHP-код, подключается из public/ через require.
    arch.sql, docker/ - база и окружение, через веб недоступны.
    Новые страницы и статические файлы класть в public/, всё служебное - вне её.

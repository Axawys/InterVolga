Запуск
    docker compose up --build -d

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

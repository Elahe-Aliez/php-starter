# PHP starter

An empty PHP project that is already set up: URLs, controllers, HTML pages, a MySQL database, Docker and tests. No framework. Clone it, start it, and write your own code.

## Start

You need Docker Desktop, and it must be running.

```bash
docker compose up -d --build
docker compose exec app composer install
```

Open http://localhost:8080. The page shows "It works", the PHP version and the database status "connected".

| Good to know | Details |
| --- | --- |
| First start | It downloads PHP and MySQL and takes a few minutes. |
| Database says "not connected" | MySQL needs about 30 seconds after its first start. Wait and reload. |
| `composer install` | It is needed only for the tests. The app also runs without it. |
| `composer.lock` | The first `composer install` creates this file. Add it to Git. |
| Stop | `docker compose down` |

### Start without Docker

You need PHP 8.1 or newer.

```bash
php -S localhost:8000 -t public
```

Open http://localhost:8000. For the database, put the address, user and password of your own MySQL into `config/config.php`.

## Where things are

| Path | What it is for |
| --- | --- |
| `public/index.php` | Every request starts here. You rarely change it. |
| `config/routes.php` | The list of URLs. Add one line per URL. |
| `config/config.php` | The settings: database, debug mode. |
| `src/Controller` | Your controllers. One method per URL. |
| `src/Repository` | Your classes that talk to the database (SQL). |
| `src/Service` | Your classes with logic and calculations. |
| `src/Model` | Your data classes, if you need them. |
| `src/Core` | The small engine: router, request, response, view, database, session. |
| `templates` | Your HTML pages. `layout.php` is the page around all of them. |
| `public/css/style.css` | The style of the pages. |
| `db/init.sql` | Your tables. MySQL runs this file at its first start. |
| `tests` | Your tests. |
| `bin/console.php` | A place for command line scripts. |
| `docs/examples.md` | Ready code: a page, a table with a form, a JSON answer, a login, a test. |

## How a request travels

1. The browser asks for a URL, for example `/notes/5`.
2. `public/index.php` starts and loads `config/routes.php`.
3. The router finds the line for this URL and calls the controller method.
4. The controller reads the request, uses a repository or a service, and returns a response.
5. The response is an HTML page from `templates`, or JSON, or a redirect.

## Everyday commands

| Command | What it does |
| --- | --- |
| `docker compose up -d --build` | Build and start the app and the database. |
| `docker compose down` | Stop everything. The database data stays. |
| `docker compose down -v` | Stop everything and delete the database data. |
| `docker compose logs -f app` | Show the output and the errors of the app. Leave with Ctrl+C. |
| `docker compose exec app composer test` | Run the tests. |
| `docker compose exec app composer require name/package` | Add a package. |
| `docker compose exec app php bin/console.php` | Run the command line script. |
| `docker compose exec app bash` | Open a terminal inside the app container. |
| `docker compose exec db mysql -uapp -psecret app` | Open the MySQL console. Leave with `exit`. |

## Security

| Topic | Rule in this project |
| --- | --- |
| Text from users in HTML | Print it with `e()`. |
| SQL | Use `prepare()` with `?`. Never put a variable into the SQL text. |
| Passwords | Store `password_hash()`. Check with `password_verify()`. |
| Login | Call `Session::regenerate()` right after a successful login. |
| Changes | Use POST for everything that changes data, never GET. |
| Forms from other websites (CSRF) | The session cookie is set to `SameSite=Lax`, which blocks most of it. A secret token in each form is the full protection. It is not built in. |
| Debug mode | Set `APP_DEBUG` to `"0"` in `docker-compose.yml` for a finished project. |

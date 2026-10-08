# Examples

Ready code for the most common jobs. Copy an example, then change the names.

| Example | What it shows |
| --- | --- |
| 1 | A simple page: URL, controller, template |
| 2 | A database table with a list page and a form |
| 3 | A JSON answer (API) |
| 4 | Login and protected pages |
| 5 | A class with a test |

## Example 1: a simple page

Add the URL in `config/routes.php`. Put the `use` line at the top of the file, next to the other one.

```php
use App\Controller\HelloController;

$router->get('/hello/{name}', [HelloController::class, 'show']);
```

Create the file `src/Controller/HelloController.php`:

```php
<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;

final class HelloController extends Controller
{
    /** GET /hello/{name} */
    public function show(Request $request, string $name): Response
    {
        return $this->view('hello', ['title' => 'Hello', 'name' => $name]);
    }
}
```

Create the file `templates/hello.php`:

```php
<h1>Hello, <?= e($name) ?>!</h1>
```

Open http://localhost:8080/hello/World.

Always print values with `e()`. It makes text from users safe.

## Example 2: a table, a list page and a form

This is the full path: table, repository, controller, templates, routes.

**1.** Add the table to `db/init.sql`:

```sql
CREATE TABLE notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    body TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO notes (title, body) VALUES
    ('First note', 'Hello from the database');
```

Build the database again, because `init.sql` runs only at the first start:

```bash
docker compose down -v
docker compose up -d
```

**2.** Create the file `src/Repository/NoteRepository.php`. All SQL for notes lives here.

```php
<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class NoteRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    /** @return list<array<string, mixed>> */
    public function all(): array
    {
        return $this->pdo->query('SELECT * FROM notes ORDER BY created_at DESC, id DESC')->fetchAll();
    }

    /** @return array<string, mixed>|null */
    public function find(int $id): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM notes WHERE id = ?');
        $statement->execute([$id]);

        return $statement->fetch() ?: null;
    }

    public function create(string $title, string $body): int
    {
        $statement = $this->pdo->prepare('INSERT INTO notes (title, body) VALUES (?, ?)');
        $statement->execute([$title, $body]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, string $title, string $body): void
    {
        $statement = $this->pdo->prepare('UPDATE notes SET title = ?, body = ? WHERE id = ?');
        $statement->execute([$title, $body, $id]);
    }

    public function delete(int $id): void
    {
        $statement = $this->pdo->prepare('DELETE FROM notes WHERE id = ?');
        $statement->execute([$id]);
    }
}
```

**3.** Create the file `src/Controller/NoteController.php`:

```php
<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;
use App\Core\Database;
use App\Core\HttpError;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Repository\NoteRepository;

final class NoteController extends Controller
{
    private NoteRepository $notes;

    public function __construct()
    {
        $this->notes = new NoteRepository(Database::connection());
    }

    /** GET /notes */
    public function index(Request $request): Response
    {
        return $this->view('notes/index', [
            'title' => 'Notes',
            'notes' => $this->notes->all(),
        ]);
    }

    /** GET /notes/{id} */
    public function show(Request $request, int $id): Response
    {
        $note = $this->notes->find($id) ?? throw new HttpError(404, 'Note not found');

        return $this->view('notes/show', ['title' => $note['title'], 'note' => $note]);
    }

    /** POST /notes */
    public function store(Request $request): Response
    {
        $title = trim((string) $request->input('title', ''));
        $body = trim((string) $request->input('body', ''));

        if ($title === '' || $body === '') {
            Session::flash('error', 'Title and text are required.');

            return $this->redirect('/notes');
        }

        $this->notes->create($title, $body);
        Session::flash('success', 'Note saved.');

        return $this->redirect('/notes');
    }

    /** POST /notes/{id}/delete */
    public function delete(Request $request, int $id): Response
    {
        $this->notes->delete($id);
        Session::flash('success', 'Note deleted.');

        return $this->redirect('/notes');
    }
}
```

**4.** Create the folder `templates/notes` and the file `templates/notes/index.php`:

```php
<h1>Notes</h1>

<?php foreach ($notes as $note): ?>
    <h2><a href="/notes/<?= e($note['id']) ?>"><?= e($note['title']) ?></a></h2>
    <p><?= e($note['created_at']) ?></p>
<?php endforeach; ?>

<h2>New note</h2>

<form method="post" action="/notes">
    <label for="title">Title</label>
    <input id="title" name="title">

    <label for="body">Text</label>
    <textarea id="body" name="body" rows="4"></textarea>

    <button type="submit">Save</button>
</form>
```

Create the file `templates/notes/show.php`:

```php
<h1><?= e($note['title']) ?></h1>

<p><?= nl2br(e($note['body'])) ?></p>

<form method="post" action="/notes/<?= e($note['id']) ?>/delete">
    <button type="submit">Delete</button>
</form>

<p><a href="/notes">Back to all notes</a></p>
```

**5.** Add the URLs in `config/routes.php`:

```php
use App\Controller\NoteController;

$router->get('/notes', [NoteController::class, 'index']);
$router->get('/notes/{id}', [NoteController::class, 'show']);
$router->post('/notes', [NoteController::class, 'store']);
$router->post('/notes/{id}/delete', [NoteController::class, 'delete']);
```

Open http://localhost:8080/notes.

| Rule | Why |
| --- | --- |
| SQL always with `prepare()` and `?` | It protects against SQL injection. |
| After a form was saved, return `redirect()` | A reload of the page does not send the form again. |
| `Session::flash('success', '...')` | The message shows one time on the next page. |
| Form values are strings | Convert numbers yourself: `(int) $request->input('age')`. |

## Example 3: a JSON answer (API)

Add the URLs in `config/routes.php`:

```php
use App\Controller\NoteApiController;

$router->get('/api/notes', [NoteApiController::class, 'index']);
$router->post('/api/notes', [NoteApiController::class, 'store']);
```

Create the file `src/Controller/NoteApiController.php`. It uses the repository from example 2.

```php
<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Repository\NoteRepository;

final class NoteApiController extends Controller
{
    /** GET /api/notes */
    public function index(Request $request): Response
    {
        $notes = new NoteRepository(Database::connection());

        return $this->json($notes->all());
    }

    /** POST /api/notes with a JSON body like {"title": "Hi", "body": "Text"} */
    public function store(Request $request): Response
    {
        $title = trim((string) $request->input('title', ''));
        $body = trim((string) $request->input('body', ''));

        if ($title === '' || $body === '') {
            return $this->json(['error' => 'title and body are required'], 422);
        }

        $notes = new NoteRepository(Database::connection());
        $id = $notes->create($title, $body);

        return $this->json(['id' => $id], 201);
    }
}
```

Open http://localhost:8080/api/notes. Errors on URLs that start with `/api/` are answered as JSON too.

## Example 4: login

**1.** Add the table to `db/init.sql`, then build the database again as in example 2.

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL
);

-- The password of this user is "secret".
INSERT INTO users (email, password_hash) VALUES
    ('admin@example.com', '$2y$10$PAyJ97PWJ/vfFiawyJ3wP.dt/zbg7DrnfqFjnEHdW2QVvBFZwbdUO');
```

A password is never stored as plain text. Create the hash of another password like this:

```bash
docker compose exec app php -r "echo password_hash('my-password', PASSWORD_DEFAULT);"
```

**2.** Create the file `src/Controller/AuthController.php`:

```php
<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

final class AuthController extends Controller
{
    /** GET /login */
    public function form(Request $request): Response
    {
        return $this->view('login', ['title' => 'Login']);
    }

    /** POST /login */
    public function login(Request $request): Response
    {
        $statement = Database::connection()->prepare('SELECT * FROM users WHERE email = ?');
        $statement->execute([(string) $request->input('email', '')]);
        $user = $statement->fetch();

        $password = (string) $request->input('password', '');

        if (!$user || !password_verify($password, $user['password_hash'])) {
            Session::flash('error', 'Wrong email or password.');

            return $this->redirect('/login');
        }

        Session::regenerate();
        Session::set('user_id', $user['id']);

        return $this->redirect('/');
    }

    /** POST /logout */
    public function logout(Request $request): Response
    {
        Session::destroy();

        return $this->redirect('/login');
    }
}
```

**3.** Create the file `templates/login.php`:

```php
<h1>Login</h1>

<form method="post" action="/login">
    <label for="email">Email</label>
    <input id="email" name="email" type="email">

    <label for="password">Password</label>
    <input id="password" name="password" type="password">

    <button type="submit">Log in</button>
</form>
```

**4.** Add the URLs in `config/routes.php`:

```php
use App\Controller\AuthController;

$router->get('/login', [AuthController::class, 'form']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);
```

**5.** To protect a page, put these lines at the start of its controller method:

```php
if (Session::get('user_id') === null) {
    return $this->redirect('/login');
}
```

Open http://localhost:8080/login and log in with `admin@example.com` and `secret`.

## Example 5: a class with a test

Create the file `src/Service/PriceCalculator.php`:

```php
<?php

declare(strict_types=1);

namespace App\Service;

final class PriceCalculator
{
    /**
     * @param list<float|int> $prices
     */
    public function total(array $prices, float $discountPercent = 0): float
    {
        $sum = array_sum($prices);

        return round($sum * (1 - $discountPercent / 100), 2);
    }
}
```

Create the file `tests/PriceCalculatorTest.php`:

```php
<?php

declare(strict_types=1);

namespace Tests;

use App\Service\PriceCalculator;
use PHPUnit\Framework\TestCase;

final class PriceCalculatorTest extends TestCase
{
    public function testItAddsThePricesAndTakesOffTheDiscount(): void
    {
        $calculator = new PriceCalculator();

        $this->assertSame(54.0, $calculator->total([10, 20, 30], 10));
    }
}
```

Run the tests:

```bash
docker compose exec app composer test
```

For a task without web pages, write your code in `bin/console.php` and run it with `docker compose exec app php bin/console.php`.

## Problems

| You see | Fix |
| --- | --- |
| Database: not connected | Wait 30 seconds after the first start and reload. Check with `docker compose ps` that `db` is running. |
| "Table 'app.notes' doesn't exist" | The table was added after the first start. Run `docker compose down -v`, then `docker compose up -d`. |
| "Class ... not found" or "Class ... does not exist" | The file name must be the class name, and the namespace must follow the folder: `src/Controller/NoteController.php` is `App\Controller\NoteController`. In `config/routes.php` the `use` line of the controller must be there. |
| 404 Page not found | The URL is missing in `config/routes.php`. |
| "Template not found" | The name in `$this->view('notes/index')` must be the file `templates/notes/index.php`. |
| "port is already allocated" | Something else uses port 8080. Change `8080:8000` to `8081:8000` in `docker-compose.yml` and open http://localhost:8081. |
| A changed `Dockerfile` has no effect | Run `docker compose up -d --build`. |
| You want to see what is inside a variable | Put `dd($variable);` into the code and reload the page. It prints the value and stops. |
| An error page without details | Run `docker compose logs app`. The full error is there. |

# TaskManager-Docker

A small task manager built from two cooperating services: a **Laravel** web app (UI + validation) that talks to a **Go REST API** (task storage). The whole stack runs with a single `docker compose up`.

## Tech Stack

| Service | Technology |
|---|---|
| Web app | PHP 8.3, Laravel 12, Livewire 4, Vite + Tailwind CSS 4, SQLite |
| API | Go (standard library `net/http`), in-memory storage |
| Containers | Docker, Docker Compose (multi-stage builds, healthchecks) |
| CI | GitHub Actions |

## Architecture

```
Browser ──► Laravel (:8000) ──HTTP──► Go API (:8080)
              /go-tasks                /tasks, /health
```

## Quick Start (Docker)

Requirements: Docker 24+ with the Compose plugin, Git.

```bash
git clone https://github.com/<your-username>/TaskManager-Docker.git
cd TaskManager-Docker
cp .env.example .env        # optional: change ports / APP_URL
docker compose up -d --build
docker compose ps           # both services should become "healthy"
```

Or with the Makefile: `make up`.

## How to Test

1. Open the UI: <http://localhost:8000/go-tasks> and add / delete a task.
2. Check the API directly:

```bash
curl http://localhost:8080/health                      # -> OK
curl -X POST -H "Content-Type: application/json" \
     -d '{"title":"Write README"}' http://localhost:8080/tasks
curl http://localhost:8080/tasks                       # -> [{"id":1,"title":"Write README"}]
```

3. Laravel health endpoint: <http://localhost:8000/up>
4. Run everything at once: `make test`

## Stop & Clean Up

```bash
docker compose down        # stop and remove containers (data volume is kept)
docker compose down -v     # also delete the data volume
make clean                 # remove containers, local images and volume
```

## Configuration

Set values in `.env` (copied from `.env.example`):

| Variable | Default | Description |
|---|---|---|
| `APP_PORT` | `8000` | Host port for the Laravel app |
| `GO_PORT` | `8080` | Host port for the Go API |
| `APP_URL` | `http://localhost:8000` | Public URL of the app (set to your server URL on a VPS) |
| `APP_ENV` / `APP_DEBUG` | `production` / `false` | Laravel mode |
| `APP_KEY` | *(empty)* | Optional. If empty, a key is generated once and stored in the `/data` volume |

Laravel uses SQLite stored in the `laravel_data` volume (`/data/database.sqlite`). Migrations run automatically on start. The Go API keeps tasks in memory, so its tasks reset when that container restarts.

## Makefile Commands

`make help` · `make up` · `make down` · `make logs` · `make ps` · `make test` · `make clean`

## Docker Highlights

- **Multi-stage builds**: Node builds the assets, Composer installs production dependencies, and only the results are copied into the final runtime image. The Go service ships as a static binary on Alpine.
- **Healthchecks** on both services; Laravel waits for the Go API to be healthy.
- **`.dockerignore`** keeps `.git`, `vendor`, `node_modules`, secrets and local databases out of the build context.

## Project Structure

```
.
├── src/
│   ├── laravel-app/        # Laravel application
│   └── go-backend/         # Go REST API (+ its own Dockerfile)
├── docker/entrypoint.sh    # Laravel container startup (env sync, migrate, serve)
├── docs/
│   ├── screenshots/
│   └── notes.md
├── .github/workflows/docker-build.yml
├── Dockerfile              # Laravel image (multi-stage)
├── docker-compose.yml
├── Makefile
└── README.md
```

## CI

`.github/workflows/docker-build.yml` runs the Laravel tests, builds both images, starts the stack, waits for the healthchecks and runs a smoke test on every push and pull request to `main`.

## Screenshots

Evidence screenshots are stored in [`docs/screenshots/`](docs/screenshots/).

## Attribution

Built on the [Laravel](https://laravel.com) skeleton (MIT) and [Livewire](https://livewire.laravel.com) (MIT).

## License

MIT — see [LICENSE](LICENSE).

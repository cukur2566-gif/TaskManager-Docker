# Technical Notes

> Draft based on the actual setup — edit the wording so it reflects your own experience.

## Biggest Docker problem and how I solved it

`php artisan serve` does not pass most environment variables (such as `APP_KEY`, `DB_DATABASE`, `SESSION_DRIVER`) to the PHP server process it starts; that process reads its configuration from the `.env` file only. Setting `environment:` in `docker-compose.yml` therefore had no effect and the app failed on first request (missing key, wrong database path, `database` session driver).

Solution: a small `docker/entrypoint.sh` that creates `.env` from `.env.example`, writes the values from the container environment into it, generates and persists `APP_KEY` in the `/data` volume when none is provided, runs migrations, and only then starts the server. The SQLite file lives in a named volume so data survives container re-creation.

## Most important Git/GitHub lesson

Keep the repository clean from the start: stray copies of files (`Dockerfile.txt`, `(2).gitignore`), local `.env` files and databases should never be committed. A correct `.gitignore`/`.dockerignore` plus a `.gitattributes` forcing LF line endings avoids hard-to-debug problems when files created on Windows run inside Linux containers. Small, well-named commits (`docker:`, `docs:`, `fix:`) also make the history easy to review.

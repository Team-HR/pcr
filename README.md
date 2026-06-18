# SPMS Online (PCR) — New Server Setup

This app runs in Docker (PHP 8.2 + Apache). Redis runs in a separate container.

## 1. Prerequisites

- Docker and Docker Compose installed
- Access to the MySQL database server
- The Redis container (`ihris-redis`) running on the external network `ohrmd_docker_apps_backend`

Verify the Redis network exists:

```bash
docker network ls | grep ohrmd_docker_apps_backend
```

If it is missing, start the stack that provides `ihris-redis` first, or remove the `redis_net` block from `docker-compose.yml` and point `REDIS_HOST` to a reachable host.

## 2. Clone the repository

```bash
git clone <repo-url> pcr
cd pcr
```

## 3. Create the environment file

`.env` is gitignored and must be created manually:

```bash
cat > .env << 'EOF'
# Database Configuration
DB_HOST=<db-host>
DB_USER=<db-user>
DB_PASSWORD=<db-password>
DB_NAME=<db-name>
DB_PORT=<db-port>

# Redis Configuration (container hostname on the shared network)
REDIS_HOST=ihris-redis
REDIS_PORT=6379

# Web Service Port (host port the app is served on)
WEB_PORT=<web-port>
EOF
```

## 4. Create the database connection file

`_connect.db.php` is gitignored and must be created manually:

```bash
cat > _connect.db.php << 'EOF'
<?php
date_default_timezone_set("Asia/Manila");
$host = "<db-host>";
$user = "<db-user>";
$password = "<db-password>";
$database = "<db-name>";
$port = "<db-port>";
EOF
```

## 5. Build the image

The PHP extensions (including `bcmath`, `gd`, `pdo_mysql`, and `redis`) are installed automatically by the `Dockerfile` — no manual `apt-get` steps are needed.

```bash
docker compose up -d --build
```

> **If the build fails with an IPv6 error** (`cannot assign requested address` / `network is unreachable` when pulling images), build using the host network instead:
>
> ```bash
> ./build-with-host-network.sh
> docker compose up -d
> ```

## 6. Verify

```bash
# Container is running
docker compose ps

# Redis extension loaded and reachable
docker exec spms-online php -m | grep redis
docker exec spms-online php -r '$r=new Redis(); var_dump($r->connect("ihris-redis",6379));'
```

The app is served at `http://<server-ip>:<WEB_PORT>` (default `8093`).

## Troubleshooting

- **`Class "Redis" not found`** — the image was built without the redis extension. Rebuild (step 5). The app also runs without Redis (caching is bypassed).
- **`mysqli_sql_exception: Connection refused`** — check `DB_HOST`/`DB_PORT` in `.env` and `_connect.db.php`, and that the DB server is reachable from the container.
- **Container name already in use** — `docker rm -f spms-online` then re-run step 5.

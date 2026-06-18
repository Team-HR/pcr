# PCR Project Docker Separation Plan

Create a standalone Docker configuration for the PCR project, separating it from the shared ohrmd_docker_apps infrastructure. Database and Redis will remain remote services configured via environment variables.

## Files to Create

1. **docker-compose.yml** - Standalone compose file with only the PCR web service
2. **Dockerfile** - PHP 7.4 Apache image (copied from existing configuration)
3. **.env.example** - Template for all required environment variables (DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT, REDIS_HOST, REDIS_PORT)
4. **.dockerignore** - Exclude unnecessary files from build context
5. **docker/apache/vhost.conf** - Apache virtual host configuration
6. **docker/php/php.ini** - PHP configuration overrides
7. **docker/php/custom.ini** - Additional PHP settings

## Files to Modify

1. **_connect.db.php** - Update to read database credentials from environment variables using getenv()
2. **assets/libs/config_class.php** - Update Redis connection to use REDIS_HOST and REDIS_PORT environment variables

## Implementation Steps

1. Create Docker directory structure with config files
2. Copy and adapt the existing Dockerfile from the shared images folder
3. Create docker-compose.yml mapping port 8082:80, with environment variables from .env file
4. Create .env.example with all required variables
5. Modify _connect.db.php to use getenv() for database connection
6. Modify config_class.php constructor to use getenv() for Redis connection
7. Test build with `docker-compose build` and verify configuration

## Environment Variables Required

- DB_HOST - Database hostname
- DB_USER - Database username
- DB_PASSWORD - Database password
- DB_NAME - Database name
- DB_PORT - Database port (default 3306)
- REDIS_HOST - Redis hostname
- REDIS_PORT - Redis port (default 6379)
- WEB_PORT - Web service port mapping (default 8082)

## Post-Implementation

1. Copy .env.example to .env and fill in actual values
2. Update any deployment scripts or CI/CD configurations
3. Test connectivity to remote database and Redis
4. Document the new setup in README.md

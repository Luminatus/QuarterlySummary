# Quarterly Summary test project
A native PHP project implementing a simple Controller method for displaying quarterly email sending statistics of various companies.

## Requirements
- [Docker](https://www.docker.com/) is required for building and executing the runtime environment.
## Installation

### Starting up the docker containers
At the root directory, execute
```bash
docker-compose build
```
This will pull/build the associated docker images. Then, to start the containers, use
```bash
docker-compose up -d
```

After the containers are running, you can start an interactive shell with the following command:
```bash
docker exec -it w_app bash
```

### Creating the database
An example database export is included in the `test_db.sql` file, which can be easily imported using the PMA included in the container. 
- PMA can be accessed at `localhost:9191`, the default root password is `root`.
- The `test_db.sql` file should be imported into the empty `quarterly_summary` database, as that is the default database the application uses.

### Installing the application
Navigate to the project root directory within the `app` container, and install the project dependencies with
```bash
composer install
```

That's it, you're good to go!

### Usage
By default, the application is accessible on `localhost`. You may use a browser, or an API client (like **Postman**) to access its features.

The service has a single endpoint, which is used to query the quarterly summaries of a single company for a single year. The endpoint uses the following pattern:
```
localhost/{company_id}/{year}
```
- `company_id`: The ID of the company
- `year`: The year to be queried.

## Alternate configurations
The project comes with a preconfigured runtime environment, which may not suit your needs perfectly. Below are detailed some of the configuration settings you may wish to alter:

### Database connection
If you wish to use a separate database connection instead of the one provided within the configuration, you simply have to change the following parameters within the `data/.env` file:
```
DB_DRIVER=mysql
DB_HOST=mysql
DB_PORT=
DB_DATABASE=quarterly_summary
DB_USERNAME=root
DB_PASSWORD=root
```

Using a separate database connection could also make the built-in implementation obsolete, so it is advised to remove the `mysql` service configuration from the `docker-compose.yml` file, along with all references to it (E.g. `links` fields).

### Host name
The application uses `localhost` as its default host. The following steps must be made in order to change it:
- First, you may need to register the new host name in your operating system's respective `hosts` file.
- Then, you must change the `server_name` directive's value to your desired host in the Nginx configuration (located in `nginx_conf/default.conf`)
  ```nginx
  server {
    server_name my_custom_host;
    ...
  ```
- Changing the Nginx configuration requires Nginx to be restarted. This can be done by running
  ```bash
  docker-compose restart nginx
  ```
- You application should be accessible on your custom host name now.
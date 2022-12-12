# OrderManagerService
A Laravel based service handling orders, created as a test project, supplied with a docker configuration for an out of the box runtime environment.

## Requirements
- [Docker](https://www.docker.com/) is required for building and executing the runtime environment.
- If you wish to run the provided test calls, `cURL` must be installed on your host machine.
## Installation

### Starting up the docker containers
At the root directory, execute
```bash
docker-compose build
```
This pull/build the associated docker images. Then, to start the containers, use
```bash
docker-compose up -d
```

After the containers are running, you can start an interactive shell with the following command:
```bash
docker exec -it app bash
```

### Installing the application
Navigate to the project root directory within the `app` container, and install the project dependencies with
```bash
composer install
```

Then, run the following command to create the underlying database schema:
```bash
php artisan migrate
```

Optionally, if you wish the load the database with test data, you can run
```bash
php artisan db:seed
```

That's it, you're good to go!

### Usage
By default, the application is accessible on `localhost`. You may use a browser, or an API client (like **Postman**) to access its features.

The service communicates exclusively through POST requests with JSON payloads. All requests to the endpoints below must specify `application/json` within their `Content-Type` header.

The application provides the following API endpoints:
- **`POST`** `/api/order/create`: Create a new order with an arbitrary number of products. Orders are initially created in a `new` status.
- **`POST`** `api/order/update`: Update the status of an existing order (could be either `new` or `completed`)
- **`POST`** `api/order/list`: List all orders with the following optional filters:
  - `order_id`: Return a specific order by its Order ID. Due to the uniqueness of the field, supplying this filter will return either one or zero entries.
  - `status`: Return orders of a specific status (`new`, or `completed`)
  - `start_date`: Return orders that were created after `start_date`. This parameter must not be in the future, and has to be before `end_date` if `end_date` is also specified.
  - `end_date`: Return orders that were created before `end_date`. If this field is omitted, the current time is considered as `end_date`.

You can find the detailed documentation of the API here: http://localhost/api/doc

## Alternate configurations
The project comes with a preconfigured runtime environment, which may not suit your needs perfectly. Below are detailed some of the configuration settings you may wish to alter:

### Database connection
If you wish to use a separate database connection instead of the one provided within the configuration, you simply have to change the following parameters within the `data/.env` file:
```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=order_manager
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
- You application should be accessible on your custom host name now. However, in order for the API documentation to fully work, you must also change the server url in your Swagger documentation (located in `data/public/swagger/swagger.yaml`):
  ```yaml  
  servers:
    - url: "http://my_custom_host"
  ```
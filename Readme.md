1. In order to mysql database run, you need to specify its root 
password in .env file. Just copy .env.example to .env and 
specify password inside. This same password should be entered
in backend application:

/www/calc-backend-storage/config/autoload/database.local.php

this last file should be copied from: database.local.php.dist

Afterwards, run docker-compose.

2. Frontend application might run in development mode and in
production, when all code is compiled.

To run it in development, you might use a command:

docker run --rm -it -v{pwd}:/app -p 8081:8080 node bash -c 'cd /app && yarn serve'

This command should be run from the root of web app, where
package.json file is situated. The app will run on port 8081,
because port 8080 is reserved by nginx for the same app running in 
production mode.

In order to build production version of web-app, you need to run 
command:

docker run --rm -it -v{pwd}:/app node bash -c 'cd /app && yarn build'

Again, from the web app root directory.

3. You need to manually create a database within mysql container.
For this run:

docker exec -it {mysql_container_id} bash
mysql -u root -p

then specify password from .env file. Afterwards:

CREATE DATABASE `calculator`;
use calculator;

And copy-paste script from /www/calc-backend-store/upgrade/sql/ .
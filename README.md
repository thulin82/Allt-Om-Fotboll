# Allt om fotboll (project in course: phpmvc at BTH, Sweden)

This project is built using Anax-MVC as the foundation.
Licenses and information about Anax-MVC is found [here](https://github.com/mosbth/Anax-MVC)

## Install and setup

Clone repository from GitHub.

Run `composer install --no-dev` to download all required dependencies.

### Setting up database

After setup, please go to the following URL:s to setup the database:  
/allt-om-fotboll/webroot/users/setup  
/allt-om-fotboll/webroot/questions/setup  
/allt-om-fotboll/webroot/answers/setup  
/allt-om-fotboll/webroot/tags/setup  
/allt-om-fotboll/webroot/comments/setup

After that, you're ready to go!

## Docker

### Build

```bash
docker build -t alltomfotboll .
```

### Run

```bash
docker run -d -p 8000:80 alltomfotboll
```

### Instructions

The docker build command builds an image in all of its php 5.6 glory. This was the main version when this application originally was written.
Visit the routes above for seeding the data.

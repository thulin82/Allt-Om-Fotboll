# Allt om fotboll (project in course: phpmvc at BTH, Sweden)

[![GitHub Actions](https://github.com/thulin82/Allt-Om-Fotboll/actions/workflows/github-actions.yml/badge.svg)](https://github.com/thulin82/Allt-Om-Fotboll/actions/workflows/github-actions.yml)

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
docker build -t clog .
```

### Instructions

The docker build command builds an image in all of its php 5.6 glory. This was the main version when this application originally was written.
Set write access to both webroot/db folder and webroot/db/db.sqlite file for the databse stuff to work. And visit the routes above for seeding the data.

# web-scraper-demo
Simple project with 2 apps - an api and a front-end for querying

## API
Scrapes data found at:
https://openpaymentsdata.cms.gov/dataset/General-Payment-Data-with-Identifying-Recipient-In/hrpy-hqv8

Use the API to initiate web scraping, which pushes the data into a MongoDB databse. This command can be run as often as needed without duplicating data.

Use the API to add new users for the front-end.

## Front-end
* Login page.
* Search page.
* Export data to XLS.

## Requirements
PHP 5.6, MongoDB 3.0, PHP extension: mongo

## Install
Checkout the project. From root of project, run:
$> composer install
(requires composer, which you can get at: https://getcomposer.org/doc/00-intro.md)

Make sure your virtual host is set to point to:
<project-root>/web


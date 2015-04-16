# web-scraper-demo
Simple project with 3 apps - Console interface, small RESTful API, and a web front-end for basic querying

## The Code
The project code is primarily in src/EIdeas/OpenPayments/ScraperBundle


## Requirements
PHP 5.6, local MongoDB 3.0, PHP extension: mongo
* Note: If your mongo database is protected by a password, make sure to update app/config/config.yml, around line 50.


## Install
1. Checkout the project. From root of project, run:
> composer install
*(requires composer, which you can get at: https://getcomposer.org/doc/00-intro.md)*

2. Make sure your virtual host is set to point to:
```
<project-root>/web
```

3. Make sure the cache and logs dirs are readable by your console user (typically you) and the web user (ie, www-data).
```
<project-root>/app/cache
<project-root>/app/logs
```


## Console Interface
This is where you should start after a fresh install, as you'll need data.

1. Run the scraper job

From the command line, cd into the project root. Then run:
> php app/console scraper:run

This command will import 50 records from Open Payments Data:
[https://openpaymentsdata.cms.gov/dataset/General-Payment-Data-with-Identifying-Recipient-In/hrpy-hqv8] (https://openpaymentsdata.cms.gov/dataset/General-Payment-Data-with-Identifying-Recipient-In/hrpy-hqv8)

If you want to import more or less than 50 records, set the limit option:
> php app/console scraper:run --limit=1000

Each time you run the scraper job, it starts with the latest records. Therefore, running the job over and over will only import records the first time, and nothing after that. Unless new payment records are added.

Therefore, if you want to continue to add older records, use the offset option. This will skip *n* amount of records.
> php app/console scraper:run --offset=1000 --limit=1000

2. Query the data

Get a full record count:
> php app/console payment:query --count

Get searchable field names:
> php app/console payment:query --aggregate-columns

Get aggregate values for a specific searchable field:
> php app/console payment:query --aggregate=teaching_hospital_name

Get a dump of all records (use with caution if you imported a lot):
> php app/console payment:query

Paginate all records, 100 records per page:
> php app/console payment:query --page=3

Filter for a set of records:
> php app/console payment:query physician_last_name Mar

Get a count of records matching a filter:
> php app/console payment:query --count physician_last_name Smith

Lastly, grab a record by its transaction id:
> php app/console payment:query --transaction_id=816617



## RESTful API
The API is used by the web front-end. Use your browser's inspect tool to monitor the network activity for each action.


## Front-end
* Record list page with a "type-ahead" search box
* Export recordset to XLS button




# greyareas
GovHack 2016 Entry

## Whats this do?
Greyareas is a web site/app which aims to point out places in australia in which the aged population is catered for compared to the rest of australia.

We have obtained a database of australian postcodes and geomapped them onto a map, and provided a score on different factors in the following categories

 - cultural
 - social
 - connected
 - economic
 - active
 - average

The end result is a static HTML site with a PHP api to retrieve data for a post code where you can explore what we have curated.

## Uses
  - PHP 7
  - MySQL
  - Slim Framework
  - EzSQL For talking to mysql
  - in modifying the data, nodeJS was used to manipulate things into more readable forms, to run these scripts nodejs is needed, with lodash and xmljs
  - Postcodes were added to the database using a freeware tool called ogr2ogr on the abs database of posted listed below

## In action
You can see it running at http://greyareas.online

## Installing
Greyareas runs on a generic lamp stack (For example we used ubuntu serve 16.04 and `sudo tasksel install lamp-server`) we set up a database, imported the SQL into the database from the data directory, and pointed the apache server to the public folder.

A completed database of whats on the live site is in data under live-data.sql.gz.

To run this on your own server, set up a database, edit the mysql config in src/routes.php install php composer (https://getcomposer.org/), run `composer.phar install` in the root directory and you should be good to go!

Data is obtained and hacked at from the following sources:
(All the modifided/massaged data has been plonked into /data)

 - http://qldspatial.information.qld.gov.au/catalogue/custom/search.page?q=%22Community%20facilities%20-%20Queensland%22
 - https://data.qld.gov.au/dataset/business-discount-directory
 - https://data.qld.gov.au/dataset/business-discount-directory
 - https://data.gov.au/dataset/dss-payment-demographic-data
 - https://data.qld.gov.au/dataset/built-features-queensland-series/resource/4f7efafa-3432-4043-b9ea-2da81b589d27
 - https://data.qld.gov.au/dataset/war-memorial-sites-in-queensland
 - https://data.qld.gov.au/dataset/world-war-ii-historic-places-in-queensland
 - https://data.qld.gov.au/dataset/public-libraries/resource/7c68ac60-cbb4-4450-89fb-89d917ae3b34
 - https://data.qld.gov.au/dataset/residential-parks-with-manufactured-homes-recorded-with-the-department-of-housing-and-public-works
 - http://qldspatial.information.qld.gov.au/catalogue/custom/detail.page?fid={06482585-D4AA-48E3-A77B-CB0125C190AD}
 - http://qldspatial.information.qld.gov.au/catalogue/custom/detail.page?fid={8F24D271-EE3B-491C-915C-E7DD617F95DC}
 - http://qldspatial.information.qld.gov.au/catalogue/custom/detail.page?fid={6EA75AF6-7FA2-4A87-AA5C-286C5C4A2AB6}
 - http://qldspatial.information.qld.gov.au/catalogue/custom/detail.page?fid={5A785159-1613-464E-82D7-42926E786337}
 - http://www.abs.gov.au/AUSSTATS/abs@.nsf/DetailsPage/1270.0.55.003July%202011?OpenDocument

## THE CHALLENGE

The goal of this case study is to get insights into your approaches of building modern, accessible, scalable and performant APIs and services.

We recommend you to set yourself a time limit of 5 up to 8 hours and submit your latest state as a minimal viable product (MVP) you feel comfortable with, even if some features are missing or disabled by intention (not finished, not ready for use by customers / rough edges). 

## Introduction

As a backend software engineer for the marketing apps team your daily work will take you around a lot of APIs and SaaS that help our team to develop sustainable and bullet proof solutions to provide reliable data for our frontend team.

In trivago we work everyday to fulfill our mission "To be the traveler’s first and independent source of information for finding the ideal hotel." So for this task you can expect a lot of "hotel - trivago" jargon.

## Important notes

    • Even when you can use any programming language you like for this task, you should know that most of our backend services are PHP based, so we strongly encourage you to use PHP to complete this task.
    • You can use any major PHP Framework, be sure to not abuse code generation, we want to see your code.
    • Your API should follow as close as possible the RESTful design pattern.
    • You can use any library of package that you think suits best for your API.
    • Your commit history tells us the story of your code, so please create relevant commits with descriptive messages.

## The Task

The Frontend team is working on a new application for accommodation listings, in this app any hotelier can manipulate the information that they want to display on our platforms.

Your assignment is to implement an API to allow them to interact with a storage layer.

## Acceptance criteria

    • I can get all the items for the given hotelier.
    • I can get a single item.
    • I can create new entries.
    • I can update information of any of my items.
    • I can delete any item.
    • A booking endpoint than whenever is called reduces the accommodation availability, and that fails if there is no availability. 

What is an item? An item is the entity which refers to any type of accommodation and has the  schema you can find in the ressources (item.json).

```
{
    "name": "Example name",
    "rating": 5,
    "category": "hotel",
    "location": {
        "city": "Cuernavaca",
        "state": "Morelos",
        "country": "Mexico",
        "zip_code": "62448",
        "address": "Boulevard Díaz Ordaz No. 9 Cantarranas"
    },
    "image": "image-url.com", 
    "reputation": 8990, 
    "reputationBadge": "green", 
    "price": 1000, 
    "availability": 10 
}

```

## Requirements

    1. Design your API using the OpenAPI Spec, you can provide the specification in YAML or JSON.
    2. Create the database schema, you can choose any DB technology you like (SQL or NoSQL).
    3. When a user tries to save some data, you must validate against the following constraints:
        a. A hotel name cannot contain the words ["Free", "Offer", "Book", "Website"] and it should be longer than 10 characters
        b. The rating MUST accept only integers, where rating is >= 0 and <= 5.
        c. The category can be one of [hotel, alternative, hostel, lodge, resort, guest-house] and it should be a string
        d. The location MUST be stored on a separate table.
            i. The zip code MUST be an integer and must have a length of 5.
        e. The image MUST be a valid URL
        f. The reputation MUST be an integer >= 0 and <= 1000.
            i. If reputation is <= 500 the value is red
            ii. If reputation is <= 799 the value is yellow
            iii. Otherwise the value is green
        g. The reputation badge is a calculated value that depends on the reputation
        h. The price must be an integer
        i. The availability must be an integer
    4. Provide as many tests as possible, at trivago we aim for at least 85% code coverage.
    5. All of your application errors and exceptions MUST be returned following the RFC7807 spec.
    6. Provide detailed instructions on how to execute your code but please notice that we are going to run the execution on a fresh VM or PC using the latest Ubuntu or macOS.

## Bonus

This is not mandatory for considering the case study as DONE, but it will give you some extra credits with our team.

    • Provide a docker environment for running your API. (with a docker-compose runner or a Makefile).
    • Add to your API the ability to retrieve information according to some filters:
        ◦ Retrieve my items with rating = X
        ◦ Retrieve my items located in X city
        ◦ Retrieve my items with X reputationBadge
        ◦ Retrieve my items with availability of more/less than X
        ◦ Retrieve my items with X category
    • What happens if an user wants to check an item that is not of his property? Can you provide a solution for this case?
    • What about CACHE? Can you implement this layer on your service?
    • Your code complies to PSR-2 linting and Static (PHPStan) code analysis.

## =======================================================================================================

## APIs

Kindly see the  /swagger-generator/ folder all swagger and Open API standards json/yml file are there. though I have tried to write the php document for all swagger as well in code API class. But couldn't completed due to lack of time. And intentionally kept in primary level for this test.
This swagger documents generated from Postman json to swagger generator. To get final yml and json output file. You can test it in any swagger output.

## Development

Itentionally not used ORM due to lack of time and to show the primary sql skills. Which can be converted easily to ORM. 

Whole application is based on MVC framework which I myself developped. And used the similar high class libraries as Symfony or Laravel etc used. All validation done. 

Used /src/.env file for most of the environment variables.

As per direction not created git neither commits. 

Whole development based on my own Docker files which run 4 containers Nginx, PHP-fpm, redis and mysql. So no need to install anything just need to run docker.

Composer, mysql demo data along with structure will be installed and configured automatically. 

## Testing

I have created all API test cases /tests/ folder. Not crated small unit test cases due to lack of time. 

## How to install 

1. Unzip the file into a folder eg. trivago

2. Follow the commands:

"""
sudo docker-compose build --force-rm --no-cache --pull

sudo docker-compose up -d --force-recreate --remove-orphans

"""

## How to test

After docker container run fully. Test with following commands:

"""

sudo docker ps

sudo docker inspect <mysql container id>    [to get the 'IPAddress' to connect to your workbench easily, didn't create phpmyadmin etc here. You will find that whole demo DB with some data is already present for you.]

./vendor/bin/phpunit      [phpunit tests]

"""

## How to call APIs

I have used/run from postman client for API.

Get the details from swagger. But for your help follow the bellow instructions.
1. Import this file /swagger-generator/trivago-test.postman_collection.json to your postman. 
2. Then you can run each of the API. You can change the parameters as well. 

## Which part can be improved

Many a things. I have created within 10 hours. You can add ORM, careate more test cases, write php docs for swagger and many more. 
But still this whole application all working perfect and can be used as dice to start for production of your company easily. 
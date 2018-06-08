# Tweets-on-a-Google-Map

I did a walk around the Rustafa district in Valencia, taking photos with my DSLR (Nikon D40x). This camera doesn't have geolocation facility for the images, so for each image I also recorded a waypoint with my Etrex Vista HCX GPS device. 

I combined this data together and then wanted to show the photos geolocated on the google map with an infomarker displaying the image, title and info about the image. 

## Prerequisite

- Twitter CONSUMER_KEY, CONSUMER_SECRET
- PHP 5.6+

## Notes

Uses https://github.com/abraham/twitteroauth

## Install

- git clone this repo to a php webserver
- cd into directory
- composer install
- create a .env file in the root
- add CONSUMER_KEY, CONSUMER_SECRET to it
- visit index.php


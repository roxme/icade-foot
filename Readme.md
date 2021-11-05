# ICADE-FOOTBALL
The site must list all football matchs from the French league. 

## Features
In this list, you will show the 2 teams, the match score and summary stats. 
When you click on a match, you will show the game summary stats and the last games for both teams.
You can also search a team via the search bar which an autocomplete input and filter the game list.
This will rely on the follwoing API  
https://www.api-football.com

## Installer
  Create .env and add your football API_KEY there (you have to signup on the website mentionned above for that) 
  On a command line window in the project root folder :  
  * Run `composer install`
  * Run `npm install`
  * npm run build

And that's it :)

## Troubleshoot
The native symfony cache is used by the system to optimize API call :
https://symfony.com/doc/current/components/cache.html

That may not be appropriate if you want to get live datas.
In that case, you have to "flush" this cache accordingly on a regular basis.
For that it is recommend to use the symfony console.

## Comming soon
In a future version we'll add the following features :
 - Activation of the cache will be set as a parameter
 - Provide unit tests runable via phpunit command
 - Add additional information

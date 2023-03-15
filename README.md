# FruitsTask
Fruits Tech task
After downloading project from github.com , please run this commands
1. composer install  
2. create .env file
3. copy env.example file to .env new created file(check database connection in .env file for your DB credentials)
4. php bin/console doctrine:migrations:migrate  // for creating the database
5. after this please run command "php bin/console get:fruits"  // for parsing data to local DB
6.And after that you can host the application in somewhere and check functionalities // 

url/fruits -- to view the fruits and add to favorites
url/favorite-fruits -- to view selected favorite fruits
 

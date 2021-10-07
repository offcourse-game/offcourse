![OffCourse Logo](public/images/logos/lowRes.png "OffCourse")
<!--
         __  __  ____
   ___  / _|/ _|/ ___|___  _   _ _ __ ___  ___
  / _ \| |_| |_| |   / _ \| | | | '__/ __|/ _ \
 | (_) |  _|  _| |__| (_) | |_| | |  \__ \  __/
  \___/|_| |_|  \____\___/ \__,_|_|  |___/\___|

-->

# Installation Guide OffCourse

#### Note:
- this guide is written for ubuntu 18.04 or windows subsystem for linux (wsl) with ubuntu 18.04, but should also work with newer versions

#### Requirements:
- php 7.2, composer 1.6, mysql DB should be already installed and running (XAMPP and GitBash recommended for windows, if you do not use wsl)
- this code is written for laravel 6.18
- You need the following packages: php, composer, mysql-server, php-mysql, php-mbstring, php-xml, php-zip, php_com_dotnet (on Windows), php-ldap and for testing we need: php-curl)
- on windows: if you have php installed via xampp simply uncomment the line extension=ldap; and add
    [COM_DOT_NET]
    extension=php_com_dotnet.dll
    in your php.ini
  to get ldap login and the bots to work

#### Installation:
- clone the git repository or unzip the file with the source code
- run "composer install --no-dev" in the project directory
- modify the .env file so it can connect to your DB
- in .env set SHELL option to powershell or bash depending on your operating system
- run "php artisan key:generate" to generate an application key
- run "php artisan storage:link" to make image upload work
- migrate the db with: php artisan migrate:fresh

#### Run for development:
- use "php artisan serve" to run a test instance
- use "php artisan db:seed" to fill database with some test data and a test user (username: test@test.de with password: 2YhwqTB)
- point your browser to 127.0.0.1:8000/ if you have not configured otherwise
- register an account in the app (127.0.0.1:8000/register) to get started with adding questions
-> only possible with an already active account, use our test account!

#### Testing (not really used ..):
- follow (never run theses command on production server): https://laravel.com/docs/5.8/dusk#installation
- to run the test use: php artisan dusk

#### Notes on running this app in production:
- disable the debug options in the .env file (https://laravel.com/docs/5.8/errors)
- laravel has a few cache options to speed it up, have a look at https://laravel.com/docs/5.8/deployment
- delete the test account or change the password

#### Updates to dependencies:
- run "composer update --no-dev" from time to time to get laravel updates! See: https://laravel.com/docs/5.8/releases
- Moreover we have the following js/css-dependencies, which should also be update by changing the version numbers in:
  - mainParent.blade.php: bootstrap, jquery, iconify
  - public/js: swiper.min.js, confetti.min.js
  - public/css/mobile: swiper.min.css
  - Statistics.blade.php: plotly
  - flower.blade.php: gsap (GreenSock Animation Platform)

#### Where are the most relevant files?
We follow the code structure defined by laravel:

- app/Http/Controllers/ -- all controllers
- app/Http/models/ -- here are all models, with contains only static functions, that connect to the DB
- public/ -- all public static css, js and image files
- public/storage/ -- this directory is linked to /storge/app/public/. All uploaded images are stored there under uploads/
- routes/web.php -- all routes
- views/ -- all views

----
![Character](public/images/characterPair_lowRes.png "Character")

This project was programmend (& documented) by Andreas Edler, Tammo Heuzeroth, Felix Stein, Phillip Szelat
and documented by Malte Sommer and David Dreyer as an educational project in Apr to Aug 2018.
Since then it is maintained and developed by Felix Stein and Phillip Szelat under lead of Dr. Henrik Wesseloh for nearly two years.

This software artifact was used in various research publications:

- Wesseloh, H.: Einsatz von Gamification zum Fördern intrinsischer Motivation - Empirische Erkenntnisse und Gestaltungsempfehlungen, Göttingen 2021. (https://cuvillier.de/de/shop/publications/8507-einsatz-von-gamification-zum-fordern-intrinsischer-motivation-empirische-erkenntnisse-und-gestaltungsempfehlungen)

- Wesseloh, H., Buddensiek, N., Pantel, T., Stein, F. M., Szelat, P., Schumann, M.: The role of gameful perception as a mediator for intrinsically motivating gamification, in: 5th International GamiFIN Conference, Levi, 2021, S. 80-89. (erhielt Best Paper Award) (http://ceur-ws.org/Vol-2883/paper9.pdf)

- Wesseloh, H., Stein, F. M., Szelat, P., Schumann, M.: Bossfights in Lectures! - A Longitidunal Study on a Gamified Application for Testing Factual Knowledge, in: 4th International GamiFIN Conference, Levi, 2020, S. 31-40. (https://publikationen.as.wiwi.uni-goettingen.de/getfile?DateiID=748)

This software is licensed under the GNU Affero General Public License version 3 (see the file LICENSE).

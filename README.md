# Symfony Sample Application

This application is based on the one developed in
[*Ruby on Rails Tutorial*](http://railstutorial.org/)
by [Michael Hartl](http://michaelhartl.com/)
and written using Symfony PHP framework.

## Installation

	git clone https://github.com/OneTwo7/symfony_sample_app
	cd symfony_sample_app
	composer install
	npm install

After that set your database parameters in app/config/parameters.yml
and execute following commands:

	php app/console doctrine:schema:update --force
	php app/console doctrine:fixtures:load

Application is installed and ready to go. To set up your local server
consult [Configuring a Web Server](http://symfony.com/doc/current/setup/web_server_configuration.html) and [Setting up or Fixing File Permissions](http://symfony.com/doc/current/setup/file_permissions.html) pages.

## Running application on heroku

	heroku create
	git push heroku master
	heroku run 'php app/console doctrine:schema:update --force'
	heroku addons:create sendgrid:starter

To use Amazon S3 bucket for picture upload set up environmental variables
with your parameters:

	heroku config:set S3_ACCESS_KEY=<access key>
	heroku config:set S3_SECRET_KEY=<secret key>
	heroku config:set S3_BUCKET=<bucket name>
	heroku config:set S3_REGION=<region>

[Application demo](https://secret-chamber-53101.herokuapp.com/)
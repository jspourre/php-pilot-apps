#Installation guide

## Requirement

1. docker and docker-compose 1.24.0


## Installation 

### From git

1. clone project:

		git clone git@github.com:jspourre/php-pilot-apps.git

2. go to the folder:

		cd php-pilot-apps
		
###Other way

1. Extract archive
2. Go to the folder extracted

### Build
This commands should be done inside project folder

		docker-compose build
		docker-compose up 

### Finishing

We need to go inside php image

		docker exec -it -u dev pilot_apps_php bash

Inside image:		
		
	cd sf4/ 
	./install.sh
	php bin/console doctrine:database:create
	php bin/console doctrine:schema:update --force

Now, still inside image, we can use two new command

1. First command to import users:

		php bin/console app:import-user
		
2. Second command to import user's posts:

		php bin/console app:import-posts
	
### Conclusion

Now we can access to the app with http://localhost/
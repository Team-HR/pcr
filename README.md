When newly setting up the server make sure to install php-bcmath extension (To prevent server error 500 due to no php-bcmath preinstalled):

Execute command

``
sudo apt-get install php-bcmath
``

then

``
sudo service apache2 restart
``

Students:
Sunny Li              g3sunny
Thanasi Karachotzitis g3karach

*** AMI ID ***
//TODO
*** Source Location ***
//TODO
/var/www/html/estore

*** Setup Instructions ***
//TODO

// Recursively change the image permissions to allow for upload
chmod 777 -R /var/www/html/estore/images/product

// Start apache
sudo service apache2 restart

Goto localhost in the browser

*** Side Notes ***
-Be sure to open all the required ports for MySQL and SMTP
-The the EC2 ip address may be blocked from sending email, if this
 is the case, please retry on another server with different ip address.

*** Browser Details ***
Please use Google Chrome or Firefox

*** How It Works ***

Controllers:
//TODO
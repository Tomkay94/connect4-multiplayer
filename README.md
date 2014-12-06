Students:

    Sunny Li                g3sunny
    Thanasi Karachotzitis   g3karach

*** AMI ID ***  
//TODO

*** Source Location ***  
//TODO

*** Setup Instructions ***  
//TODO

// Start apache  
sudo service apache2 restart

*** Side Notes ***

For server setup:

- For Securimage Captcha, please make sure php is compiled with the gd library.
- Be sure to allow outgoing SMTP send requests.
- The EC2 ip address may be blocked from sending email, if this is the case,
  please retry on another server with different ip address.

For application logic, the following assumptions were made:

- A user can only be involved in 1 match at a time
- Once a match starts, it will continue until it finishes
- An invitation will be answered eventually

*** Browser Details ***  
Please use Google Chrome or Firefox

*** How It Works ***  
//TODO

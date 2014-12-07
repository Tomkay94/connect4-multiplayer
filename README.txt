Students:
    Sunny Li                g3sunny
    Thanasi Karachotzitis   g3karach

*** AMI ID ***  
//TODO

*** Source Location ***  
/var/www/connect4

*** Setup Instructions ***  
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

===========
Controllers
===========

Arcade Controller:
This controller acts as the game lobby. From this controller, there are various 
actions relating to sending, accepting, declining and checking invitations from 
other users. The way these methods are called is from within the mainPage.php and
 availableUsers.php via AJAX requests (in order to prevent refreshing and redirecting).
 When a user accepts an invitation, a new Match object is created and the empty game 
 board is instatiated. Once the game has started, the controller changes the status 
 of both playing users to "PLAYING"so that future invitations for games cannot be 
 accepted.

Account Controller:
This controller allows users to register to the game site. Users are considered
 "logged in" by being stored in a session variable. With regards to registered 
 users, this controller also handles the integration of the SecurImage library, 
 which requires users to input a correct captcha in order to create accounts on 
 the website.

In addition to the typical session-handling functions which allow for logout and 
login, as well as those which allow users to be created, this controller also contains
 the actions for handling the forgot password, and password update update views. 

Board Controller:
This controller provides two main functionalities:
	1) connect4 game functionality to both players. 
	2) chat functionality between both players during the game.

	*** 1) Game Functionality ***
	The board is represented as a two dimensional PHP matrix. On the game board, an
	 empty tile is represented as a 0, and an occupied tile is represented by the 
	 playing user's ID for that tile in the matrix. This allows us to easily keep 
	 track of where each users pieces are, and allows for other functions to easily 
	 determine whether a user has one or not. Winning is determined through one of 
	 the following three scenarios: 1) a horizontal connection of 4 identical 
	 (same user ID) tiles, which are not 0, 2) a vertical connection of 4 identical 
	 tiles, which are not 0, and 3) a diagonal connection of 4 identical tiles, which 
	 are not 0. The matrix representation allows us to easily and efficiently scan the 
	 matrix for these game-ending scenarios. It should be noted that for efficiency 
	 sake, the functions that check for winning only do so after a player's turn has 
	 ended, and before the next player's turn starts. This keeps us from repeatedly 
	 sending requests to the server to check for game ending scenarios.

	*** 2) Chat Functionality ***
	Chat is implemented via AJAX requests checking for user form submissions 
	(sent chat messages). The AJAX requests poll the server every 2 seconds 
	for new sent chat messages from either player, and appends them to the 
	chat room dom element to allow for seamless viewing of messaging without 
	refreshes to updates the page.

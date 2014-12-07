<?php

class Board extends CI_Controller {
  
  const NUM_COLUMNS = 7;
  const NUM_ROWS = 6;

  function __construct() {
    // Call the Controller constructor
    parent::__construct();
    session_start();
  }

  public function _remap($method, $params = array()) {
    // enforce access control to protected functions
    $protected = array('index', 'postMsg', 'getMsg');

    if (in_array($method,$protected) && !isset($_SESSION['user'])) {
      $this->session->set_flashdata('warning', 'You need to sign in first!');
      redirect('account/loginForm', 'refresh');
    }

    return call_user_func_array(array($this, $method), $params);
  }

  function index() {
    $user = $_SESSION['user'];

    $this->load->model('user_model');
    $this->load->model('invite_model');
    $this->load->model('match_model');

    $user = $this->user_model->get($user->login);
    $invite = $this->invite_model->get($user->invite_id);

    if ($user->user_status_id == User::WAITING) {
      $otherUser = $this->user_model->getFromId($invite->user2_id);
    }
    
    else if ($user->user_status_id == User::PLAYING) {
      $match = $this->match_model->get($user->match_id);
      if ($match->user1_id == $user->id)
        $otherUser = $this->user_model->getFromId($match->user2_id);
      else
        $otherUser = $this->user_model->getFromId($match->user1_id);
    }

    if (!isset($otherUser)) {
      $this->session->set_flashdata('warning', "To play, you have to select a worthy opponent!");
      redirect('', 'refresh');
      return;
    }

    $data = array(
      'title' => 'Connect 4 game area',
      'main' => 'match/board',
      'user' => $user,
      'otherUser' => $otherUser
    );

    switch($user->user_status_id) {
      case User::PLAYING:	
        $data['status'] = 'playing';
        break;
      case User::WAITING:
        $data['status'] = 'waiting';
        break;
    }

    $this->load->view('template', $data);
  }

 	function postMsg() {
 		$this->load->library('form_validation');
 		$this->form_validation->set_rules('msg', 'Message', 'required');
 		
    if ($this->form_validation->run() == TRUE) {
 			$this->load->model('user_model');
 			$this->load->model('match_model');

 			$user = $_SESSION['user'];
 			 
 			$user = $this->user_model->getExclusive($user->login);
 			if ($user->user_status_id != User::PLAYING) {	
				$errormsg="Not in PLAYING state";
 				goto error;
 			}
 			
 			$match = $this->match_model->get($user->match_id);			
 			
 			$msg = $this->input->post('msg');
 			
 			if ($match->user1_id == $user->id)  {
 				$msg = $match->u1_msg == ''? $msg :  $match->u1_msg . "\n" . $msg;
 				$this->match_model->updateMsgU1($match->id, $msg);
 			}
 			else {
 				$msg = $match->u2_msg == ''? $msg :  $match->u2_msg . "\n" . $msg;
 				$this->match_model->updateMsgU2($match->id, $msg);
 			}
 				
 			echo json_encode(array('status'=>'success'));
 			 
 			return;
 		}
		
 		$errormsg="Missing argument";
 		
		error:
			echo json_encode(array('status'=>'failure','message'=>$errormsg));
 	}
 
	function getMsg() {
 		$this->load->model('user_model');
 		$this->load->model('match_model');
 			
 		$user = $_SESSION['user'];
 		 
 		$user = $this->user_model->get($user->login);
 		if ($user->user_status_id != User::PLAYING) {	
 			$errormsg="Not in PLAYING state";
 			goto error;
 		}
 		// start transactional mode  
 		$this->db->trans_begin();
 			
 		$match = $this->match_model->getExclusive($user->match_id);			
 			
 		if ($match->user1_id == $user->id) {
			$msg = $match->u2_msg;
 			$this->match_model->updateMsgU2($match->id,"");
 		}
 		else {
 			$msg = $match->u1_msg;
 			$this->match_model->updateMsgU1($match->id,"");
 		}

 		if ($this->db->trans_status() === FALSE) {
 			$errormsg = "Transaction error";
 			goto transactionerror;
 		}
 		
 		// if all went well commit changes
 		$this->db->trans_commit();
 		
 		echo json_encode(array('status'=>'success','message'=>$msg));
		return;
		
		transactionerror:
		$this->db->trans_rollback();
		
		error:
		echo json_encode(array('status'=>'failure','message'=>$errormsg));
 	}

  /* Takes a coordintate, finds where to put the 
     players piece and returns the updated game board */
  function update() {
    $chip_column = $this->input->post('col');
    if ($chip_column < 1 ||
        $chip_column > self::NUM_COLUMNS) {
      $errormsg = "Invalid Column!";
      goto error;
    }

    $user = $_SESSION['user'];
    $this->load->model('match_model');

    // TRANSACTION
    $this->db->trans_begin();

    $match = $this->match_model->getExclusive($user->match_id);
    if ($match->match_status_id != Match::ACTIVE) {
      $errormsg = "Game already ended.";
      goto transactionerror;
    }

    $matrix = unserialize($match->board_state);

    // figure out which row to drop the player piece into
    $row = 0;
    while($row < self::NUM_ROWS - 1 && 
          $matrix[$row + 1][$chip_column - 1] == 0) {
      $row++;
    }

    if ($row >= self::NUM_ROWS) {
      $errormsg = "Invalid Move!";
      goto transactionerror;
    }

    $matrix[$row][$chip_column -1] = $user->id;

    // insert matrix back to db
    // serialization taken care of in function
    $this->match_model->updateBoard($match->id, $matrix);

    // check if winning move
    $win = (
      $this->check_horizontal() ||
      $this->check_vertical() ||
      $this->check_diagonal()
    );

    if ($win) {
      if ($match->user1_id == $user->id) {
        $this->match_model->updateStatus($match->id, Match::U1WON);
      } else {
        $this->match_model->updateStatus($match->id, Match::U2WON);
      }
    }

    // TODO: check for tie!!
    // $match->$match_status_id = Match::TIE;

    if ($this->db->trans_status() === FALSE) {
        $errormsg = "Transaction error, please try again.";
        goto transactionerror;
    }

    $this->db->trans_commit();

    echo json_encode(array('board'=>$matrix));
    return;

    transactionerror:
      $this->db->trans_rollback();

    error:
      echo json_encode(array('status'=>'failure', 'message'=>$errormsg));
  }

  function refreshBoard() {
    $user = $_SESSION['user'];
    $this->load->model('match_model');
    $matrix = unserialize($this->match_model->getExclusive($user->match_id)->board_state);
    echo json_encode(array('board'=>$matrix));
  }

  /* Check if a player has won */
  function check_if_winner() {
    $has_winner = false;
    echo json_encode(
      array(
        'winner_found'=> $has_winner,
        'winner' => false
      )
    );
    return;
  }

  /* Checks for a horizontal sequence of a player's chips */
  function check_horizontal() {
    $this->load->model('match_model');
    $user = $_SESSION['user'];
    $match = $this->match_model->getExclusive($user->match_id);
    $matrix = unserialize($match->board_state);
    
    for ($row = 0; $row < self::NUM_ROWS; $row++) {
      for ($col = 0; $col < self::NUM_COLUMNS - 3; $col++) {     
        if (
          $matrix[$row][$col] == $user->id &&
          $matrix[$row][$col + 1] == $user->id &&
          $matrix[$row][$col + 2] == $user->id &&
          $matrix[$row][$col + 3] == $user->id) {
            return true;
        }
      } // End foreach row as column
    } // End foreach matrix as row
    return false;
  }

  /* Checks for a vertical sequence of a player's chips */
  function check_vertical() {
    $user = $_SESSION['user'];
    $this->load->model('match_model');
    $matrix = unserialize($this->match_model->getExclusive($user->match_id)->board_state);
    
    for ($row = 0; $row < self::NUM_ROWS - 3; $row++) {
      for ($col = 0; $col < self::NUM_COLUMNS; $col++) {      
        if (
          $matrix[$row][$col] == $user->id &&
          $matrix[$row + 1][$col] == $user->id &&
          $matrix[$row + 2][$col] == $user->id &&
          $matrix[$row + 3][$col] == $user->id) {
            return true;
        }
      } // End foreach row as column
    } // End foreach matrix as row
    return false;
  } // End check_vertical


  /* Checks for a lower or upper diagonal sequence of a player's chips   */
  function check_diagonal() {
    $user = $_SESSION['user'];
    $this->load->model('match_model');
    $matrix = unserialize($this->match_model->getExclusive($user->match_id)->board_state);
    
    // check for a diagonal from an upper left to a lower right
    for ($row = 0; $row < self::NUM_ROWS - 3; $row++) { 
      for ($col = 0; $col < self::NUM_COLUMNS - 3; $col++) { 
        if ( 
          $matrix[$row][$col] == $user->id && 
          $matrix[$row][$col] == $matrix[$row + 1][$col + 1] && 
          $matrix[$row][$col] == $matrix[$row + 2][$col + 2] &&
          $matrix[$row][$col] == $matrix[$row + 3][$col + 3]) {
            return true;
        }
      }
    }
    // check for a diagonal from a lower left to an upper right
    for ($row = self::NUM_ROWS - 1; $row >= 3; $row--) { 
      for ($col = 0; $col < self::NUM_COLUMNS - 3; $col++) {
        if (
          $matrix[$row][$col] == $user->id && 
          $matrix[$row][$col] == $matrix[$row - 1][$col + 1] && 
          $matrix[$row][$col] == $matrix[$row - 2][$col + 2] &&
          $matrix[$row][$col] == $matrix[$row - 3][$col + 3]) {
            return true;
        }
      }
    }
    return false;
  } // End check_diagonal}
}


<script>
  var status = "<?= $status ?>",
      user = "<?= $user->login ?>",
      userID = "<?= $user->id ?>",
      otherUser = "<?= $otherUser->login ?>",
      otherUserID = "<?= $otherUser->id ?>",
      myTurn = false,
      processing = "yellow",
      yellow = "#f1c40f", // self
      red    = "#e74c3c"; // opponent

  $(function(){
    // used after AJAX requests to update board
    function updateBoard(board) {
      var grid = board;
      for (row = 0; row < 6; row++)
        for (col = 0; col < 7; col++) {
          // TODO: need to figure out the ordering!
          if (grid[5-row][col] == 0) {
            $('#'+(col+1)+'-'+(row+1)).css("background-color", 'transparent');
          } else if (grid[5-row][col] == userID) {
            $('#'+(col+1)+'-'+(row+1)).css("background-color", yellow);
          } else {
            $('#'+(col+1)+'-'+(row+1)).css("background-color", red);
          }
        }
    }

    $('body').everyTime(2000,function(){

      // waiting for player
      if (status == 'waiting') {
        $.getJSON('<?= base_url() ?>arcade/checkInvitation', function(data, text, jqZHR){
          if (data && data.status=='rejected') {
            alert("Sorry, your invitation to play was declined!");
            window.location.href = '<?= base_url() ?>arcade/index';
          }
          if (data && data.status=='accepted') {
            status = 'playing';
            $('#status').html('Playing ' + otherUser);
          }
        });
      }

      // chat update
      $.getJSON("<?= base_url() ?>board/getMsg", function (data,text,jqXHR){
        if (data && data.status=='success') {
          var conversation = $('[name=conversation]').val();
          var msg = data.message;
          if (msg.length > 0) {
            $('[name=conversation]').val(conversation + "\n" + otherUser + ": " + msg);
            $('textarea').scrollTop($('textarea')[0].scrollHeight);
          }
        }
      });

      // board update
      $.getJSON("<?= base_url() ?>board/refreshBoard", function (data,text,jqXHR){
        if (data) {
          // get back board object matrix
          updateBoard(data.board);
        }
      });

      // board update
      $.getJSON("<?= base_url() ?>board/get_status", function (data,text,jqXHR){
        if (data) {
          // get back board object matrix
          if (data.player) {
            var winner = [user, otherUser].sort()[data.player - 1];
            $('#status').html(
              'Player ' + winner + ' wins<br>'
              + "Go back to the lobby to play against others!"
            );
          } else {
            $('#status').html("It's a tie!");
          }
        }
      });

    });

    // chat
    $('form').submit(function(){
      var arguments = $(this).serialize();
      $.post("<?= base_url() ?>board/postMsg", arguments, function (data,textStatus,jqXHR){
        var conversation = $('[name=conversation]').val();
        var msg = $('[name=msg]').val();
        $('[name=conversation]').val(conversation + "\n" + user + ": " + msg);
        $('input[name=msg]').val("");
      });
      return false;
    });

    // board click
    $('#board .piece').click(function(e) {
      // highlight the clicked location
      $('#'+e.target.id).css("background-color", processing);

      // put move
      var col_num = e.target.id.split('-')[0];
      $.post("<?= base_url() ?>board/update", {'col': col_num}, function (data,text,jqXHR){
        if (data) {
          data = JSON.parse(data);
          if (data.status == 'success') {
            // update board immediately
            console.log('updating');
            updateBoard(data.board);
          } else 
          if (data.status == 'failure') {
            alert(data.message);    
          }
        }
      });

    });

  });
</script>

<h2>Game Area</h2>

<div class="row">
  <div class="col-md-8">

    <div id='status'> 
      You are currently 
      <?php
        if ($status == "playing") {
          echo "playing against ".$otherUser->login;
        } else {
          echo "Wating on ".$otherUser->login;
        }
      ?>
    </div>

    <br>
    <p>Click on a column to put down a piece in that column.</p>
    <p>Yellow - You, Red - Opponent.</p>
    <br>

    <div class="col-md-1"></div>

    <div class="col-md-11" id="board">

      <div class="row">
      <?php
        for ($row = 6; $row > 0; $row--) {
          echo '<div class="row">';
          for ($col = 1; $col < 8; $col++) {
      ?>

      <div class="col-xs-1 col-sm-1 col-md-1 piece" id="<?= $col.'-'.$row ?>"></div>

      <?php
          }
          echo '</div>';
        }
      ?>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <h4>Chat with your opponent</h4>
    <?php
      echo form_textarea('conversation', null, 'class="form-control"');
      echo form_open('','class="form-inline"');
      echo form_input('msg', '', 'class="form-control"');
      echo form_submit('Send', 'Send', 'class="btn btn-default"');
      echo form_close();
    ?>
  </div>
</div>


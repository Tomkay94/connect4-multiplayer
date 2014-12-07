
<script>
  var status = "<?= $status ?>",
      user = "<?= $user->login ?>",
      userID = "<?= $user->id ?>",
      otherUser = "<?= $otherUser->login ?>",
      otherUserID = "<?= $otherUser->id ?>",
      myTurn = false,
      yellow = "#f1c40f", // self
      red    = "#e74c3c"; // opponent

  $(function(){
    // used after AJAX requests to update board
    function updateBoard(board) {
      var grid = board;
      for (row = 0; row < 6; row++)
        for (col = 0; col < 7; col++) {
          // TODO: need to figure out the ordering!
          if (grid[row][col] == 0) {
            $('#'+(col+1)+'-'+(row+1)).css("background-color", 'transparent');
          } else if (grid[row][col] == userID) {
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
      $.getJSON("<?= base_url() ?>board/update", function (data,text,jqXHR){
        if (data && data.status=='success') {
          // get back board object matrix
          updateBoard(data.board);
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
      console.log(e.target.id);
      // demo
      $('#'+e.target.id).css("background-color", yellow);

      // post clicked coloumn to server
      $.getJSON("<?= base_url() ?>board/post", function (data,text,jqXHR){
        if (data && data.status=='success') {
          updateBoard(data.board);

        } else if (data && data.status=='invalid') {
          // tell user that they made an invalid move
          alert(data.message);
        }
      });

      // Check if the player won
      $.getJSON("<?= base_url() ?>board/check_if_winner", function (data,text,jqXHR){
        if (data && data.status == 1) {
            alert("Congrats, you won the game!");    
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


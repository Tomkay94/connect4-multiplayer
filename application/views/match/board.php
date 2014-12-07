
<script>
  var otherUser = "<?= $otherUser->login ?>";
  var user = "<?= $user->login ?>";
  var status = "<?= $status ?>";

  $(function(){
    $('body').everyTime(2000,function(){
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

      $.getJSON("<?= base_url() ?>board/getMsg", function (data,text,jqXHR){
        if (data && data.status=='success') {
          var conversation = $('[name=conversation]').val();
          var msg = data.message;
          if (msg.length > 0)
            $('[name=conversation]').val(conversation + "\n" + otherUser + ": " + msg);
        }
      });
    });

    $('form').submit(function(){
      var arguments = $(this).serialize();
      $.post("<?= base_url() ?>board/postMsg", arguments, function (data,textStatus,jqXHR){
        var conversation = $('[name=conversation]').val();
        var msg = $('[name=msg]').val();
        $('[name=conversation]').val(conversation + "\n" + user + ": " + msg);
      });
      return false;
    });
  });
</script>

<h2>Game Area</h2>

<div id='status'> 
  You are currently 
<?php
  if ($status == "playing") {
    echo "playing ".$otherUser->login;
  } else {
    echo "Wating on ".$otherUser->login;
  }
?>
</div>

<div class="row">
  <div class="col-md-9"></div>
  <div class="col-md-3">
    <h4>Chat with your opponent</h4>
    <?php
      echo form_textarea('conversation');
      echo form_open();
      echo form_input('msg');
      echo form_submit('Send', 'Send');
      echo form_close();
    ?>
  </div>
</div>


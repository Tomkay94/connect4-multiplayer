
<script>
  $(function(){
    $('#availableUsers').everyTime(500,function(){
      $('#availableUsers').load('<?= base_url() ?>arcade/getAvailableUsers');

      $.getJSON('<?= base_url() ?>arcade/getInvitation',function(data, text, jqZHR){

        if (data && data.invited) {
          var user = data.login,
              time = data.time;

          if(confirm('Play ' + user)) {

            $.getJSON('<?= base_url() ?>arcade/acceptInvitation',
              function(data, text, jqZHR){
                if (data && data.status == 'success')
                  window.location.href = '<?= base_url() ?>board/index'
              });

          } else {
            $.post("<?= base_url() ?>arcade/declineInvitation");
          }
        }

      });
    });
  });
</script>

<h3>
  Hello <?= $user->fullName() ?>, start a game with available users below:
</h3><br>

<div id="availableUsers"></div>



<h1>Recover Password</h1>
<div class="block-input">
<?php
  echo form_open('account/recoverPassword');
  echo form_label('Email'); 
  echo form_error('email');
  echo form_input('email',set_value('email'),"required");
  echo form_submit('submit', 'Recover Password');
  echo form_close();
?>
</div>


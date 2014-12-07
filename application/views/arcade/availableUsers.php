
<table class="table table-striped">
  <thead>
    <tr>
      <th>Available Users</th>
    </tr>
  </thead>
  <tbody>
<?php
if ($availableUsers) {
  foreach ($availableUsers as $user) {
    if ($user->id != $currentUser->id) {
?>
      <tr>
        <td>
          <?= anchor("arcade/invite?login=".$user->login, $user->fullName()) ?>
        </td>
      </tr>
<?php
      }
    }
  }
?>
  </tbody>
</table>


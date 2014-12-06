<h2>Available Users</h2>

<table>
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
</table>

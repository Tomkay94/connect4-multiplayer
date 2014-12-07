<form class="form-signin" role="form" method="post" action="<?php base_url() ?>/account/login">
  <h2 class="form-signin-heading">Sign in</h2>
  <label for="username" class="sr-only">Username</label>
  <input type="text" id="username" class="form-control" name="username"
         placeholder="Your username" required autofocus>
  <label for="password" class="sr-only">Password</label>
  <input type="password" id="password" class="form-control" name="password"
         placeholder="Password" required>
  <button class="btn btn-lg btn-primary btn-block" type="submit">
    Sign in
  </button>
  <a class="btn btn-lg btn-info btn-block" href="<?php base_url() ?>/account/recoverPasswordForm">
    Forgot Password
  </a>
</form>


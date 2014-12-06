<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $title; ?></title>
    <meta name="description" content="Gotta connect 'em 4s">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- asset imports -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="<?= base_url(); ?>css/template.css">
    <link href='//fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>

    <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
    <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
    <script src="<?= base_url() ?>/js/jquery.timers.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
  </head>

  <body>
    <nav>
      <ul>
        <li>
          <a href="<?= base_url(); ?>">Connect 4</a>
        </li>
        <?php
          if (isset($_SESSION['user'])) {
        ?>
            <li>
              <a href="<?= base_url(); ?>account/updatePasswordForm">Change Password</a>
            </li>
            <li>
              <a href="<?= base_url(); ?>account/logout">Sign Out</a>
            </li>
        <?php
          } else { // not signed in
        ?>
            <li>
              <a href="<?= base_url(); ?>account/loginForm">Sign In</a>
            </li>
        <?php
          }
        ?>
      </ul>
    </nav>

    <?php
      // flash notifications
      if ($this->session->flashdata('error') ||
          $this->session->flashdata('warning') ||
          $this->session->flashdata('info')) {
    ?>
      <br /><br />

      <?php
        if ($this->session->flashdata('error')) {
      ?>
        <div class="alert alert-danger" role="alert">
          <?= $this->session->flashdata('error') ?>
        </div>

      <?php
        }
        if ($this->session->flashdata('warning')) {
      ?>
        <div class="alert alert-warning" role="alert">
          <?= $this->session->flashdata('warning') ?>
        </div>

      <?php
        }
        if ($this->session->flashdata('info')) {
      ?>
        <div class="alert alert-info" role="alert">
          <?= $this->session->flashdata('info') ?>
        </div>

    <?php
        }
      }
    ?>

    <div id="main">
      <?php $this->load->view($main); ?>
    </div>

    <div class="container">
      <hr />
      <!-- Footer -->
      <footer>
        <div class="row">
          <div class="col-lg-12">
            <p>Copyright &copy; C4 2014</p>
          </div>
        </div>
      </footer>
    </div>
  </body>
</html>

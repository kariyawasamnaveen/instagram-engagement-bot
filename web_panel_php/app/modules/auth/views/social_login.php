<?php if (isset($google_auth['status']) && $google_auth['status']) : ?>

  <style>
    .social-login {
      margin-bottom: 1rem;
    }
    .social-login .btn-google a {
      text-decoration: none;
    } 
    .social-login .btn-google {
      background: #fff;
      padding: 13px 20px 12px;
      color: #555!important;
      border-color: #e2e8f0;
      border-radius: 0.5rem;
      transition: box-shadow 0.3s ease;
    }
    .social-login .btn-google:hover {
      border-color:rgb(168, 168, 168);
      box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    }
    .social-login .btn-google:focus  {
      border-color:rgb(168, 168, 168);
      box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    }
    .social-login .google-logo {
      max-height: 25px;
      width: auto; 
      margin-right: 0.5rem;
    }
  </style>
  <hr>
  <div class="social-login">
    <a href="<?= $google_auth['url'] ?>" class="btn btn-outline-success btn-google btn-block">
      <img class="google-logo" src="<?= base_url('assets/images/google-logo.png')?>" alt="Google Logo">
      <?= $google_auth['button_title'] ?>
    </a>
  </div>
<?php endif; ?>
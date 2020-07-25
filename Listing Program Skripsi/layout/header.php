<?php ob_start(); ?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo (defined("SITE_TITLE")) ? $config['app_name'] . " - " . SITE_TITLE : $config['app_name'] ; ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/plugins/select2/dist/css/select2.min.css">
  <link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/adminlte.min.css">

  <style type="text/css">
    .brand-img{
      border-bottom: 1px solid #ccc;
    }
  </style>

  <?php if (isset($css)) {
    foreach ($css as $c) { ?>
      <link rel="stylesheet" href="<?php echo base_url($c); ?>">
  <?php }
} ?>

  <?php
  function getUserByID($user_id) {
      global $db;
      $query = $db->prepare('SELECT * FROM pengguna WHERE id = :id');
      $query->bindValue(':id', $user_id, PDO::PARAM_STR);
      $query->execute();
      return $query->fetchAll(PDO::FETCH_OBJ);
  } 

  function is_kepala_divisi($user_id) {
      global $db;
      $query = $db->prepare('SELECT * FROM pengguna JOIN divisi ON kepala_id = pengguna.id WHERE pengguna.id = :id');
      $query->bindValue(':id', $user_id, PDO::PARAM_STR);
      $query->execute(); 
      return $query->rowCount();
  }

  function get_divisi_by_id($user_id) {
      global $db;
      $query = $db->prepare('SELECT * FROM divisi WHERE kepala_id = :id');
      $query->bindValue(':id', $user_id, PDO::PARAM_STR);
      $query->execute(); 
      return $query->fetchObject();
  }

  $nama_divisi = "";
  $is_kepala_divisi = is_kepala_divisi(get_session('user_id')); 
  if($is_kepala_divisi){
    $nama_divisi = get_divisi_by_id(get_session('user_id'))->nama;
  }
  $data_user = getUserByID(get_session('user_id'));
  $data_user = ($data_user) ? $data_user[0] : '';
  ?>


  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/custom.css">

  <script>
    var BASE_URL = '<?php echo base_url(); ?>';
  </script>

  <style type="text/css">
    .sidebar-dark-primary .nav-treeview>.nav-item>.nav-link.active,
    .sidebar-dark-primary .nav-treeview>.nav-item>.nav-link.active:hover {
      color: #696f75;
      background-color: transparent;
      cursor: not-allowed;
    }

    .sidebar-dark-primary .nav-treeview>.nav-item>.nav-link.active i {
      color: #555 !important;
    }

    .sidebar-dark-primary .nav-treeview>.nav-item>.nav-link:hover {
      background: transparent;
    }

    .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-treeview {
      background: #1c1c1c;
      border-radius: 3px;
    }
  </style>
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>

<body class="sidebar-mini">
  <!-- Site wrapper -->
  <div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-light">
      <ul class="navbar-nav">
        <li class="nav-item d-none pl-3 d-sm-inline-block">
          <a class="nav-link disabled text-light btn-sm"><span class="hidden-xs"><span class="text-muted"><?php echo get_hak_akses($data_user->tipe) . '  - </span>' . $data_user->nama; ?></span></a>
        </li>
      </ul>

      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a class="nav-link mr-4 btn btn-sm btn-outline-light" href="<?php echo base_url('login/destroy.php'); ?>">
            <i class="fa fa-sign-out"></i> Keluar akun</a>
          </a>
        </li>
      </ul>
    </nav> <!-- .navbar -->


    <?php
    require_once("sidebar_$data_user->tipe.php");

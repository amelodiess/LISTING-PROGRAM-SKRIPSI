 

   <aside class="main-sidebar sidebar-light-primary elevation-0">
    <a class="brand-img">
      <div class="brand-img p-2 text-light font-weight-light text-left">
        <img  height="40px" src="<?= base_url("assets/images/logo-mini.png") ?>"/> 
        <span>SIM KASTILAND</span>
      </div>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <div class=" px-2  pt-3 font-weight-light h4 mb-0" style="min-height: 100px">
        <div class="pt-1 small">Menu Human Resource Development</div>
      </div>

      <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu"
        data-accordion="false">

        <li class="nav-item border-top">
          <a class="nav-link <?php echo (defined("ON_DASHBOARD")) ? 'active' : '' ; ?>" href="<?php echo base_url('users/hrd') ?>">
          <img class="img-icon" src="<?= base_url("assets/images/icon/home-outline.svg") ?>"><p>Beranda</p></a>
        </li> 

        <li class="nav-item border-top">
          <a class="nav-link <?php echo (defined("ON_DATA_PENGAJUAN")) ? 'active' : '' ; ?>" href="<?php echo base_url('users/hrd/data_pengajuan.php') ?>">
          <img class="img-icon" src="<?= base_url("assets/images/icon/document-text-outline.svg") ?>"><p>Keputusan</p></a>
        </li> 

        <li class="nav-item">
          <a class="nav-link <?php echo (defined("ON_HASIL_PENILAIAN")) ? 'active' : ''; ?>"
            href="<?php echo base_url('users/hrd/penilaian_hasil.php') ?>">
            <img class="img-icon" src="<?= base_url("assets/images/icon/document-text-outline.svg") ?>"><p>Hasil Penilaian</p>
          </a>
        </li> 
 

      </ul>

  </aside>
<?php
require_once '../../system/engine.php';

define("ON_DATA_ADMIN", true);
define("SITE_TITLE", 'Master pengguna');

$css = array('assets/plugins/datatable/datatables.bootstrap4.min.css');
$js = array('assets/plugins/datatable/datatables.min.js');

if (!get_session("user_login")) {
    set_flashdata("info", "Silahkan login untuk melanjutkan");
    redirect(base_url('login'));
}

require_once '../layout/header.php';

function getDataPengguna()
{
    global $db;
    $query = $db->prepare('SELECT * FROM pengguna');
    $query->execute();
    return $query->fetchAll(PDO::FETCH_OBJ);
}

$data_pengguna = getDataPengguna();

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Data pengguna
    </h1>
  </section>


  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <?php
if (has_flashdata('sukses')) {
    echo alert_sukses(get_flashdata('sukses'));
}
if (has_flashdata('error')) {
    echo alert_error(get_flashdata('error'));
}
if (has_flashdata('warning')) {
    echo alert_warning(get_flashdata('warning'));
}
if (has_flashdata('info')) {
    echo alert_info(get_flashdata('info'));
}
?>
            <a class="btn btn-sm btn-outline-dark btn_add_mod"
              href="<?php echo base_url('users/administrator/data_pengguna_tambah.php') ?>"><i class="fa fa-plus"></i>
              Tambahkan pengguna</a>
          </div><!-- .box-header -->
          <div class="card-body">
            <table id="data_table" class="table table-stripped table-bordered">
              <thead>
                <tr>
                  <th>Nama </th>
                  <th width="14%">NIK</th>
                  <th width="14%">Username</th>
                  <th width="14%">Email</th>
                  <th width="15%">Tipe akses</th>
                  <th width="15%"></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($data_pengguna as $dtp): ?>
                <tr>
                  <td><?php echo $dtp->nama; ?></td>
                  <td><?php echo $dtp->nik; ?></td>
                  <td><?php echo $dtp->username; ?></td>
                  <td><?php echo $dtp->email; ?></td>
                  <td><?php echo get_hak_akses($dtp->tipe); ?></td>
                  <td class="text-center">
                    <a class="btn btn-xs btn-outline-dark"
                      href="<?php echo base_url('users/administrator/data_pengguna_edit.php?id=') . $dtp->id ?>"><i
                        class="fa fa-pencil"></i> Edit</a>
                    <a class="dalete_user btn btn-xs btn-outline-danger" data-pengguna="<?php echo $dtp->nama ?>"
                      href="<?php echo base_url('users/administrator/data_pengguna_delete.php?id=') . $dtp->id ?>"><i
                        class="fa fa-trash"></i> Hapus</a>
                  </td>
                </tr>
                <?php endforeach;?>
              </tbody>
            </table>
          </div><!-- .box-body -->
        </div><!-- .box -->
      </div><!-- .col -->
    </div><!-- .row -->
  </section>
  <!-- .content -->
</div>
<!-- .content-wrapper -->

<?php require_once '../layout/footer.php';?>

<script type="text/javascript">
$(document).ready(function() {
  $(".dalete_user").click(function() {
    var pengguna = $(this).data('pengguna');
    if (!confirm("Apakah anda yakini ingin menghapus pengguna " + pengguna + ' ?')) {
      return false;
    }
  });
});
</script>
<?php
require_once '../../system/engine.php';

define("ON_DATA_DIVISI", true);
define("SITE_TITLE", 'Master divisi');

$css = array('assets/plugins/datatable/datatables.bootstrap4.min.css');
$js = array('assets/plugins/datatable/datatables.min.js');

if (!get_session("user_login")) {
    set_flashdata("info", "Silahkan login untuk melanjutkan");
    redirect(base_url('login'));
}

require_once '../layout/header.php';

function getDataDivisi()
{
    global $db;
    $query = $db->prepare('SELECT id,nama, (SELECT nama from pengguna WHERE pengguna.id = kepala_id) as kepala, (SELECT count(*) FROM pengguna WHERE divisi_id = divisi.id) as total FROM divisi');
    $query->execute();
    return $query->fetchAll(PDO::FETCH_OBJ);
}

$data_divisi = getDataDivisi();

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Data divisi
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
              href="<?php echo base_url('users/administrator/data_divisi_tambah.php') ?>"><i class="fa fa-plus"></i>
              Tambahkan divisi</a>
          </div><!-- .box-header -->
          <div class="card-body">
            <table id="data_table" class="table table-stripped table-bordered">
              <thead>
                <tr>
                  <th>Nama divisi </th>
                  <th width="22%">Kepala</th>
                  <th width="14%">pegawai</th>
                  <th width="15%"></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($data_divisi as $dtp): ?>
                <tr>
                  <td><?php echo $dtp->nama; ?></td>
                  <td><?php echo $dtp->kepala; ?></td>
                  <td><?php echo $dtp->total; ?></td>
                  <td class="text-center">
                    <a class="btn btn-xs btn-outline-dark"
                      href="<?php echo base_url('users/administrator/data_divisi_edit.php?id=') . $dtp->id ?>"><i
                        class="fa fa-pencil"></i> Edit</a>
                    <a class="dalete_user btn btn-xs btn-outline-danger" data-divisi="<?php echo $dtp->nama ?>"
                      href="<?php echo base_url('users/administrator/data_divisi_delete.php?id=') . $dtp->id ?>"><i
                        class="fa fa-trash"></i> Bubarkan</a>
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
    var divisi = $(this).data('divisi');
    if (!confirm("Apakah anda yakini ingin menghapus divisi " + divisi + ' ?')) {
      return false;
    }
  });
});
</script>
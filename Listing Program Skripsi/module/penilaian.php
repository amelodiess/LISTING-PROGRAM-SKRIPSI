<?php
require_once '../../system/engine.php';

define("ON_PENILAIAN", true);
define("SITE_TITLE", 'Kelola Data Pegawai');

$css = array('assets/plugins/datatable/datatables.bootstrap4.min.css');
$js = array('assets/plugins/datatable/datatables.min.js');

if (!get_session("user_login")) {
    set_flashdata("info", "Silahkan login untuk melanjutkan");
    redirect(base_url('login'));
}

require_once '../layout/header.php';

function getDataPegawai()
{
    global $db;
    $query = $db->prepare('SELECT pengguna.*, divisi.nama as divisi FROM pengguna JOIN divisi ON divisi.id = pengguna.divisi_id WHERE kepala_id = :id');
    $query->bindValue(':id', get_session('user_id'), PDO::PARAM_STR);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_OBJ);
}

function getTerakhirDinilai($id)
{
    global $db;
    $query = $db->prepare('SELECT waktu FROM penilaian WHERE pegawai_id = :id ORDER BY waktu ASC LIMIT 1');
    $query->bindValue(':id', $id, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);
    return $result ? $result->waktu : false;
}

$data_pegawai = getDataPegawai();

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Penilaian pegawai <small><small><small class="font-weight-bold text-muted"> DIVISI <?= strtoupper($nama_divisi) ?></small></small></small> 
    </h1>
  </section>


  <!-- Main content -->
  <section class="content">
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
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
          <div class="btn_add_mod">
            <a class="btn btn-sm btn-outline-danger reset_pen"
                href="<?php echo base_url('users/module/reset_penilaian.php') ?>"><i class="fa fa-refresh"></i>
                Reset penilaian
            </a>
          </div>
          </div><!-- .box-header -->
          <div class="card-body">
            <table id="data_table" class="table table-stripped table-bordered">
              <thead>
                <tr>
                  <th>Nama </th>
                  <th width="17%">NIK</th>
                  <th width="15%">divisi</th>
                  <th width="15%">Terakhir dinilai</th>
                  <th width="15%"></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($data_pegawai as $dtp): ?>
                <tr>
                  <td><?php echo $dtp->nama; ?></td>
                  <td><?php echo $dtp->nik; ?></td>
                  <td><?php echo $dtp->divisi; ?></td>
                  <td><?php echo format_tanggal_waktu(getTerakhirDinilai($dtp->id)); ?></td>
                  <td class="text-center">
                    <a class="btn btn-xs btn-outline-dark"
                      href="<?php echo base_url('users/module/penilaian_kelola.php?id=') . $dtp->id ?>"> buat penilaian</a>
                    <!-- <?php if (date('m') == '02' || date('m') == '08') {?>
                                        <a class="btn btn-xs btn-primary" href="<?php echo base_url('users/penilai/penilaian_kelola.php?id=') . $dtp->id ?>"><i class="fa fa-file-o"></i> penilaian</a>
                                        <?php } else {?>
                                        Penilaian sudah berkahir
                                        <?php }?> -->
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
$(".reset_pen").click(function() {
    if (!confirm("Apakah anda yakini ingin mereset penilaian?" )) {
      return false;
    }
  });
</script>
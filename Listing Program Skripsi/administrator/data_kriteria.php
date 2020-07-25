<?php
require_once '../../system/engine.php';

define("ON_DATA_KRITERIA", true);
define("SITE_TITLE", 'Data kriteria');

$css = array('assets/plugins/datatable/datatables.bootstrap4.min.css');
$js = array('assets/plugins/datatable/datatables.min.js');

if (!get_session("user_login")) {
    set_flashdata("info", "Silahkan login untuk melanjutkan");
    redirect(base_url('login'));
}

require_once '../layout/header.php';

function getDataKriteria()
{
    global $db;
    $query = $db->prepare('SELECT * FROM kriteria');
    $query->execute();
    return $query->fetchAll(PDO::FETCH_OBJ);
}

$data_kriteria = getDataKriteria();

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Data kriteria
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
            <a href="#" data-toggle="modal" data-target="#modal-tambah" class="btn btn-sm btn-outline-dark btn_add_mod"><i class="fa fa-plus-circle"></i> Tambah Kriteria</a>
          </div>
          <div class="card-body">
            <table id="data_table" class="table table-bordered">
              <thead>
                <tr>
                  <th width="">Kriteria</th>
                  <th width="15%">Bobot</th>
                  <!-- <th width="15%">data real</th>   -->
                  <!-- <th width="">Bilangan Fuzzy</th>   -->
                  <th width="15%"></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($data_kriteria as $dtp): ?>
                  <tr>
                    <td><?=$dtp->nama_kriteria;?></td>
                    <td><?=$dtp->bobot;?></td>
                    <td class="text-center">
                      <a href="#" data-toggle="modal" data-target="#modal-edit" class="btn btn-edit btn-xs btn-outline-dark" data-id="<?=$dtp->id?>" data-nama="<?=$dtp->nama_kriteria?>" data-bobot="<?=$dtp->bobot?>"><i class="fa fa-pencil"></i> Edit</a>
                      <a data-nama="<?=$dtp->nama_kriteria?>" class="delete_btn btn btn-xs btn-outline-danger" href="<?=base_url('users/administrator/data_kriteria_delete.php?id=' . $dtp->id)?>"><i class="fa fa-trash"></i> Hapus</a>
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


<div class="modal fade" id="modal-edit">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah data kriteria</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" method="post" action="<?=base_url('users/administrator/data_kriteria_edit.php');?>">
          <div class="form-group">
            <label class="col-sm-12 control-label">Nama kriteria</label>
            <div class="col-sm-12">
              <input id="edit_nama" class="form-control" type="text" name="nama" required="true">
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-12 control-label">Bobot</label>
            <div class="col-sm-12">
              <input id="edit_bobot" class="form-control only-number" max="100" type="text" name="bobot" required="true">
            </div>
          </div>

      </div>
      <div class="modal-footer justify-content-between d-flex">
        <input type="hidden" value="1" name="data_real">
        <button type="button" class="btn btn-default" data-dismiss="modal">Batalkan</button>
        <button type="submit" name="submit" value="1" class="btn btn-primary">Simpan</button>
        <input type="hidden" name="id" value="" id="edit_id">
        </form>
      </div>
    </div>
    <!-- .modal-content -->
  </div>
  <!-- .modal-dialog -->
</div>
<!-- .modal -->

<div class="modal fade" id="modal-tambah">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah data kriteria</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" method="post" action="<?=base_url('users/administrator/data_kriteria_tambah.php');?>">
          <div class="form-group">
            <label class="col-sm-12 control-label">Nama</label>
            <div class="col-sm-12">
              <input class="form-control" type="text" name="nama" required="true">
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-12 control-label">Bobot</label>
            <div class="col-sm-12">
              <input class="form-control only-number" max="100" type="text" name="bobot" required="true">
            </div>
          </div>

      </div>
      <div class="modal-footer justify-content-between d-flex">
        <input type="hidden" value="1" name="data_real">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" name="submit" value="1" class="btn btn-primary">Simpan</button>
        </form>
      </div>
    </div>
  </div>
</div><!-- .modal -->

<?php require_once '../layout/footer.php';?>
<script type="text/javascript">
  $(document).ready(function() {
    $("#ck_real").change(function() {
      if ($('#ck_real').is(":checked")) {
        $('.fz_tambah').addClass("hidden");
      } else {
        $('.fz_tambah').removeClass("hidden");
      }
    });

    $("#edit_ck_real").change(function() {
      if ($('#edit_ck_real').is(":checked")) {
        $('.fz_edit').addClass("hidden");
      } else {
        $('.fz_edit').removeClass("hidden");
      }
    });

    $(".delete_btn").click(function() {
      var kriteria = $(this).data('nama');
      if (!confirm("Apakah anda yakini ingin menghapus data kriteria " + kriteria + ' ?')) {
        return false;
      }
    });

    $('.btn-edit').click(function() {
      $('#edit_id').val($(this).data('id'));
      $('#edit_nama').val($(this).data('nama'));
      $('#edit_bobot').val($(this).data('bobot'));
    })
  });
</script>
<?php
require_once '../../system/engine.php';

define("ON_DATA_DIVISI", true);
define("SITE_TITLE", 'Master divisi');

if (!get_session("user_login")) {
    set_flashdata("info", "Silahkan login untuk melanjutkan");
    redirect(base_url('login'));
}

function get_data_kepala()
{
    global $db;
    $query = $db->prepare('SELECT * FROM pengguna WHERE tipe = "kepala_produksi" OR tipe = "kepala_bagian"');
    $query->execute();
    return $query->fetchAll(PDO::FETCH_OBJ);
}

function get_data_pegawai()
{
    global $db;
    $query = $db->prepare('SELECT * FROM pengguna WHERE divisi_id IS NULL and tipe = "pegawai"');
    $query->execute();
    return $query->fetchAll(PDO::FETCH_OBJ);
}

$js = array('assets/plugins/jquery-validator/dist/jquery.validate.min.js', 'assets/plugins/datatable/datatables.min.js');

$css = array('assets/plugins/datatable/datatables.bootstrap4.min.css');

require_once '../layout/header.php';

if (isset($_POST['submit'])) {
    trim_validate($_POST, array('password'));

    $data_nama = $_POST['nama'];
    $data_kepala = $_POST['kepala'];
    $pegawai_terpilih = $_POST['pegawai_terpilih'];

    //insert data
    $data['nama'] = $data_nama;
    $data['kepala_id'] = $data_kepala;
    $query = insert_db($data, 'divisi');

    if ($query) {
        foreach ($pegawai_terpilih as $key => $val) {
            $update_pegawai['divisi_id'] = $query;
            update_db($update_pegawai, 'pengguna', "id = " . escape($val));
        }
        set_flashdata('sukses', 'berhasil menambah divisi');
        redirect(base_url('users/administrator/data_divisi.php'));
    } else {
        set_flashdata('error', 'Gagal Menambah divisi');
        redirect(base_url('users/administrator/data_divisi_input.php'));
    }
}

?>
<style>
    .table-scroll {
        height: 293px;
        overflow-y: scroll;
    }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <a href="<?=base_url('users/administrator/data_divisi.php')?>">
            <h1>Data divisi</h1>
        </a>
        <h5>
            <i class="fa fa-caret-right"></i>
            Tambah data divisi
        </h5>
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
                    </div><!-- .box-header -->
                    <form id="form-input" action="" method="post">
                        <div class="card-body">
                            <div class="row">

                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="control-label">Nama divisi</label>
                                        <input required class="form-control" placeholder="Nama divisi" type="text" name="nama" value="">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Kepala divisi</label>
                                        <select required class="form-control select2" name="kepala">
                                            <option value="">pilih kepala divisi..</option>
                                            <?php foreach (get_data_kepala() as $key => $val) {
    echo "<option value='$val->id'>$val->nama</option>";
}?>
                                        </select>
                                    </div>
                                </div><!-- col -->
                                <hr class="w-100" />
                                <hr class="my-4" />
                                <div class="col-md-6 pr-4 border-right">
                                    <h5 class="text-success" style="padding-bottom: 45px">Pegawai terpilih</h5>
                                    <div style="padding-right: 17px; background: #eee">
                                        <table class="table bg-white table-stripped table-bordered mb-0">
                                            <thead>
                                                <tr>
                                                    <th width="70" style="border-width: 1px">Aksi</th>
                                                    <th style="border-width: 1px">Nama </th>
                                                    <th width="150" style="border-width: 1px">NIK</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <div class="table-scroll">
                                        <table class="table table-stripped table_terpilih table-bordered mb-0">
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                                <div class="col-md-6 pl-4">
                                    <h5>Pilih pegawai</h5>
                                    <table id="data_table" class="table table-stripped table-bordered">
                                        <thead>
                                            <tr>
                                                <th width="5%">Aksi</th>
                                                <th>Nama </th>
                                                <th width="16%">NIK</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach (get_data_pegawai() as $dtp): ?>
                                                <tr class="source_<?=$dtp->id?>">
                                                    <td><button type="button" class="btn btn-outline-primary btn-xs" onclick='pilih_pegawai(<?=json_encode($dtp)?>)'>pilih</button></td>
                                                    <td><?php echo $dtp->nama; ?></td>
                                                    <td><?php echo $dtp->nik; ?></td>
                                                </tr>
                                            <?php endforeach;?>
                                        </tbody>
                                    </table>
                                </div><!-- .box-col -->
                            </div><!-- row -->
                        </div><!-- .box-body -->

                        <div class="card-footer">
                            <button type="submit" name="submit" value="1" class="pull-right btn btn-lg btn-primary">
                                <i class="fa fa-save"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div><!-- .box -->
            </div><!-- .col -->
        </div><!-- .row -->
    </section>
    <!-- .content -->
</div>
<!-- .content-wrapper -->

<?php require_once '../layout/footer.php';?>
<script type="text/javascript">
    function pilih_pegawai(data) {
        console.log(data);
        const element = `<tr class="row_${data.id}">
                <td width="70"><button type="button" class="btn btn-outline-danger btn-xs" onclick="destroy_row(${data.id})">batal</button></td>
                <td>${data.nama}</td>
                <td width="150">
                ${data.nik}
                <input type="hidden" name="pegawai_terpilih[]" value="${data.id}"/>
                </td>
            </tr>`;
        if (!$(`.row_${data.id}`).length) {
            $('.table_terpilih tbody').append(element);
            $(`.source_${data.id}`).addClass("hidden");
        }
    }

    function destroy_row(id) {
        $(`.row_${id}`).remove();
        $(`.source_${id}`).removeClass("hidden");
    }

    $('#form-input').submit(function(e) {
        console.log($(".table_terpilih tbody tr").length)
        if ($(".table_terpilih tbody tr").length) {
            $(this).submit();
        } else {
            e.preventDefault();
            alert("pilih setidaknya satu pegawai");
        }
    })
    

    $('.select2').on('change', function(){
        var id = $(this).val();
        $(`.tcs`).removeClass("hidden");
        $(`.tcs`).removeClass("tcs");
        $(`.source_${id}`).addClass("hidden");
        $(`.source_${id}`).addClass("tcs");
    })
</script>
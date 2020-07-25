<?php
require_once('../../system/engine.php');

define("ON_DATA_ADMIN", true);
define("SITE_TITLE", 'Master pengguna');


if (!get_session("user_login")) {
    set_flashdata("info", "Silahkan login untuk melanjutkan");
    redirect(base_url('login'));
}

$js = array('assets/plugins/jquery-validator/dist/jquery.validate.min.js');

require_once('../layout/header.php');

if (isset($_POST['submit'])) {

    trim_validate($_POST, array('password'));

    $data_nama = $_POST['nama'];
    $data_username = $_POST['username'];
    $data_email = $_POST['email'];
    $data_password = $_POST['password'];
    $data_hak_akses = $_POST['hak_akses'];

    //insert data
    $data['nama'] = $data_nama;
    $data['nik'] = $_POST['nik'];
    $data['username'] = $data_username;
    $data['email'] = $data_email;
    $data['password'] = encrypt_password($data_password);
    $data['tipe'] = $data_hak_akses;
    $query = insert_db($data, 'pengguna');

    if ($query) {
        set_flashdata('sukses', 'berhasil menambah pengguna');
        redirect(base_url('users/administrator/data_pengguna.php'));
    } else {
        set_flashdata('error', 'Gagal Menambah pengguna');
        redirect(base_url('users/administrator/data_pengguna_input.php'));
    }
}

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <a href="<?= base_url('users/administrator/data_pengguna.php') ?>">
            <h1>Data pengguna</h1>
        </a>
        <h5>
            <i class="fa fa-caret-right"></i>
            Tambah data pengguna
        </h5>
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
                    </div><!-- .box-header -->
                    <form id="form-input" action="" method="post">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-5">

                                    <div class="form-group">
                                        <label class="control-label">Nama</label>
                                        <input required class="form-control" placeholder="Nama lengkap" type="text" name="nama" value="">
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">NIK</label>
                                        <input required class="form-control" placeholder="Nomor Induk Pegawai" type="text" name="nik" value="">
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Username</label>
                                        <input required class="form-control" placeholder="Nama User Login" type="text" name="username" value="">
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Email</label>
                                        <input required class="form-control" placeholder="Email" type="text" name="email" value="">
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Tipe akses</label>
                                        <select required class="form-control" name="hak_akses">
                                            <option value="">--- Pilih hak akses</option>
                                            <?php
                                            foreach (get_list_akses() as $key => $value) {
                                                echo "<option value='$value[value]'>$value[label]</option>\n";
                                            } ?>
                                        </select>
                                    </div>

                                </div><!-- col -->
                                <div class="col-md-5 offset-md-1">
                                    <div class="form-group">
                                        <label class="control-label">Password</label>
                                        <input class="form-control" id="password" placeholder="" type="password" name="password" value="">
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Konfirmasi Password</label>
                                        <input class="form-control" placeholder="" type="password" name="cpassword" value="">
                                    </div>
                                </div><!-- col -->
                            </div><!-- row -->

                        </div><!-- .box-body -->
                        <div class="card-footer"> <button type="submit" name="submit" value="1" class="pull-right btn btn-lg btn-primary">
                                <i class="fa fa-save"></i> Simpan</button></div>
                    </form>
                </div><!-- .box -->
            </div><!-- .col -->
        </div><!-- .row -->
    </section>
    <!-- .content -->
</div>
<!-- .content-wrapper -->

<?php require_once('../layout/footer.php'); ?>


<script type="text/javascript">
    $(document).ready(function() {


    });
</script>
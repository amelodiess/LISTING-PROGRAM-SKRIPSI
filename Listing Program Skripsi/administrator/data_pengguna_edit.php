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

if ($_GET['id'] == '') {
    redirect(base_url('users/administrator/data_pengguna.php'));
}

$user_id = $_GET['id'];

function getDataPenggunaByID($id_user)
{
    global $db;
    $query = $db->prepare('SELECT * FROM pengguna WHERE id = :id');
    $query->bindValue(':id', $id_user, PDO::PARAM_STR);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_OBJ);
}
$data_pengguna = getDataPenggunaByID($user_id);
if ($data_pengguna) {
    $data_pengguna = $data_pengguna[0];
} else {
    set_flashdata('info', 'Data Pengguna tidak ditemukan!');
    redirect(base_url('users/administrator/data_pengguna.php'));
}


if (isset($_POST['submit'])) {

    trim_validate($_POST);
    global $db;
    $data_edit_nama = $_POST['nama'];
    $data_edit_nik = $_POST['nik'];
    $data_edit_username = $_POST['username'];
    $data_edit_email = $_POST['email'];
    $data_edit_password = $_POST['password'];
    $data_edit_hak_akses = $_POST['hak_akses'];

    $validate_password = ($data_edit_password != '') ? true : false;
    //update data
    $data['nama'] = $data_edit_nama;
    $data['nik'] = $data_edit_nik;
    $data['username'] = $data_edit_username;
    $data['email'] = $data_edit_email;
    $data['tipe'] = $data_edit_hak_akses;
    if ($validate_password) {
        $data['password'] = encrypt_password($data_edit_password);
    }

    $query = update_db($data, 'pengguna', "id = " . escape($user_id));

    if ($query) {
        set_flashdata('sukses', 'berhasil mengubah pengguna');
        redirect(base_url('users/administrator/data_pengguna_edit.php?id=' . $user_id));
    } else {
        set_flashdata('error', 'Gagal mengubah pengguna');
        redirect(base_url('users/administrator/data_pengguna.php'));
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
            Edit data pengguna
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
                                        <input class="form-control" placeholder="Nama" type="text" name="nama" value="<?php echo $data_pengguna->nama ?>">
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">NIK</label>
                                        <input class="form-control" placeholder="Nomor Induk Pegawai" type="text" name="nik" value="<?php echo $data_pengguna->nik ?>">
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Username</label>
                                        <input class="form-control" placeholder="Nama User Login" type="text" name="username" value="<?php echo $data_pengguna->username ?>">
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Email</label>
                                        <input class="form-control" placeholder="Email" type="text" name="email" value="<?php echo $data_pengguna->email ?>">
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Hak akses</label>
                                        <select class="form-control" name="hak_akses">
                                            <option value="">--- Pilih hak akses</option>
                                            <?php
                                            foreach (get_list_akses() as $key => $value) {
                                                $selected =  ($data_pengguna->tipe == $value['value']) ? 'selected' : '';
                                                echo "<option $selected value='$value[value]'>$value[label]</option>\n";
                                            } ?>
                                        </select>
                                    </div>

                                </div><!-- col -->
                                <div class="col-md-5 offset-md">
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
                        <div class="card-footer"> <button type="submit" name="submit" value="1" class="btn btn-lg btn-primary">
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

        $.validator.addMethod("checkUserName",
            function(value, element) {

                var result = false;
                var varexcept = "<?php echo $data_pengguna->username ?>";
                if ($(element).val() != varexcept) {
                    $.ajax({
                        type: "POST",
                        async: false,
                        url: "<?php echo (base_url('system/validate.php')) ?>",
                        data: {
                            username: value,
                            except: varexcept
                        },
                        beforeSend: function() {
                            $(element).before('<i style="position: absolute;right: 25px;top: 10px;" class="fa fa-refresh fa-spin fa-fw"></i>');
                        },
                        success: function(data) {
                            $(element).prev().remove();
                            result = (data == true) ? true : false;
                        }
                    });
                } else {
                    result = true;
                }
                return result;
            },
            "Username Sudah ada yang menggunakan!"
        );


        $("#form-input").validate({
            onkeyup: false,
            rules: {
                nama: "required",
                hak_akses: "required",
                username: {
                    required: true,
                    checkUserName: true
                },
                password: {
                    minlength: 6
                },
                cpassword: {
                    equalTo: "#password"
                },
                email: {
                    required: true,
                    email: true
                }
            },
            messages: {
                nama: "Bagian Nama wajib diisi!",
                hak_akses: "Bagian Hak akses wajib diisi!",
                password: {
                    minlength: "Password minimal 6 karakter!"
                },
                cpassword: {
                    equalTo: "Bagian Konfirmasi password wajib sesuai dengan Password"
                },
                email: "Silahkan masukan Email yang valid!"
            },
            errorElement: "span",
            errorPlacement: function(error, element) {
                // Add the `help-block` class to the error element
                error.addClass("help-block");

                if (element.prop("type") === "checkbox") {
                    error.insertAfter(element.parent("label"));
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function(element, errorClass, validClass) {
                $(element).parents(".form-group").addClass("has-error").removeClass("has-success");
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).parents(".form-group").removeClass("has-error"); //.addClass( "has-success" );
            }
        });

    });
</script>
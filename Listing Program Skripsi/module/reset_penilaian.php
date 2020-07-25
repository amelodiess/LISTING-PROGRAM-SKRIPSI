<?php
require_once '../../system/engine.php';

if (!get_session("user_login")) {
    set_flashdata("info", "Silahkan login untuk melanjutkan");
    redirect(base_url('login'));
}

global $db;
$query = $db->prepare('DELETE FROM `penilaian`');
$query->execute();

if ($query) {
    set_flashdata('sukses', 'berhasil mereset penilaian');
} else {
    set_flashdata('error', 'Gagal  mereset penilaian');
}
redirect(base_url('users/module/penilaian.php'));

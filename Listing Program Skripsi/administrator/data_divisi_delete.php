<?php
require_once '../../system/engine.php';

if (!get_session("user_login")) {
    set_flashdata("info", "Silahkan login untuk melanjutkan");
    redirect(base_url('login'));
}

$divisi_id = $_GET['id'];
if ($divisi_id != '') {
    global $db;
    $query = $db->prepare('DELETE FROM `divisi` WHERE id = :id');
    $query->bindValue(':id', $divisi_id, PDO::PARAM_STR);
    $query->execute();

    if ($query) {
        $data_hapus_pengguna['divisi_id'] = null;
        update_db($data_hapus_pengguna, 'pengguna', "divisi_id = " . escape($divisi_id));

        set_flashdata('sukses', 'berhasil menghapus divisi');
    } else {
        set_flashdata('error', 'Gagal menghapus divisi');
    }
}
redirect(base_url('users/administrator/data_divisi.php'));

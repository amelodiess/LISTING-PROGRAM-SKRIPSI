<?php
    require_once('../../system/engine.php');

    if(!get_session("user_login")){ 
        set_flashdata("info", "Silahkan login untuk melanjutkan");
        redirect(base_url('login'));
    }

	$id = $_GET['id']; 
	if($id != ''){ 
			
		global $db;
		$query = $db->prepare('DELETE FROM `kriteria` WHERE id = :id');
		$query->bindValue(':id', $id, PDO::PARAM_STR);
		$query->execute();

		if($query){
		    set_flashdata('sukses', 'berhasil menghapus Kriteria ');
		}else{
		    set_flashdata('error', 'Gagal menghapus Kriteria ');
		} 
	}
    redirect(base_url('users/administrator/data_kriteria.php'));
	




?>
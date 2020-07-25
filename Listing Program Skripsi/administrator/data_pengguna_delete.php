<?php  
    require_once('../../system/engine.php');

    if(!get_session("user_login")){
        set_flashdata("info", "Silahkan login untuk melanjutkan");
        redirect(base_url('login'));
    }

	$user_id = $_GET['id'];
	if($user_id != ''){
		if($user_id == get_session('user_id')){
		    set_flashdata('info', 'Tidak dapat penghapus diri sendiri');
		}else{
			
			global $db;
			$query = $db->prepare('DELETE FROM `admin` WHERE id = :id');
			$query->bindValue(':id', $user_id, PDO::PARAM_STR);
			$query->execute();

			if($query){
			    set_flashdata('sukses', 'berhasil menghapus admin');
			}else{
			    set_flashdata('error', 'Gagal menghapus admin');
			}
		} 
	}
	redirect(base_url('users/administrator/data_admin.php'));
	




?>
<?php  
    require_once('../../system/engine.php');

    if(!get_session("user_login")){
        set_flashdata("info", "Silahkan login untuk melanjutkan");
        redirect(base_url('login'));
    }
 	
 	if(isset($_POST['id'])){ 
		$data['status'] = 9;   
	 	$id = $_POST['id'];
		$data['alasan_tolak'] = $_POST['keterangan_tolak'];  
		if(update_db($data, 'pengajuan', 'id = ' . escape($id))){
		    set_flashdata('sukses', 'Berhasil menolak pengajuan');
		}else{
		    set_flashdata('error', 'Gagal menolak pengajuan');
		}   
 	}

 	if(isset($_GET['id'])){
	 	$status = $_GET['s'] == 1 ? 2 : 9 ;
	 	$id = $_GET['id'];
		$data['status'] = $status;  
 
		if(update_db($data, 'pengajuan', 'id = ' . escape($id))){
		    set_flashdata('sukses', 'Berhasil menerima pengajuan');
		}else{
		    set_flashdata('error', 'Gagal menerima pengajuan');
		}  
	} 
	redirect(base_url('users/hrd/data_pengajuan.php'));
	




?>
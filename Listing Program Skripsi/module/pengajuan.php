<?php  
    require_once('../../system/engine.php');

    if(!get_session("user_login")){
        set_flashdata("info", "Silahkan login untuk melanjutkan");
        redirect(base_url('login'));
    }	  
		$data['pegawai_id'] = $_POST['pegawai_id'];
		$data['alasan'] = $_POST['alasan'];
		$data['keterangan'] = $_POST['keterangan'];
		$data['admin_id'] = get_session('user_id'); 
		$data['status'] = 1; 
		$data['tipe'] = $_POST['tipe_pengajuan']; 	 

	if(isset($_POST['submit'])){
		if(insert_db($data, 'pengajuan')){
		    set_flashdata('sukses', 'Berhasil mengajukan pegawai');
		}else{
		    set_flashdata('error', 'Gagal mengajukan pegawai');
		} 
	} 
	redirect(base_url('users/module/data_pengajuan.php'));
	 

?>
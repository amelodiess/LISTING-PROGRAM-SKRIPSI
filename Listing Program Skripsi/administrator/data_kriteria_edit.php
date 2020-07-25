<?php
    require_once('../../system/engine.php');

    if(!get_session("user_login")){ 
        set_flashdata("info", "Silahkan login untuk melanjutkan");
        redirect(base_url('login'));
    }
 
    if(isset($_POST['submit'])){
         
        $id = $_POST['id']; 
        trim_validate($_POST);
        
        global $db;
        $data['nama_kriteria'] = $_POST['nama'] ;  
        $data['bobot'] = $_POST['bobot'] ;    

        $query = update_db($data, 'kriteria', "id = ".escape($id));

        if($query){
            set_flashdata('sukses', 'berhasil mengubah Kriteria '); 
        }else{
            set_flashdata('error', 'Gagal mengubah Kriteria '); 
        }
    }
    redirect(base_url('users/administrator/data_kriteria.php'));

    ?> 
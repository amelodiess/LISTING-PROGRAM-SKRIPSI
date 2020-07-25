<?php
    require_once('../../system/engine.php');
  
    if(!get_session("user_login")){ 
        set_flashdata("info", "Silahkan login untuk melanjutkan");
        redirect(base_url('login'));
    }
   

    if(isset($_POST['submit'])){ 
        //insert data
        $data['nama_kriteria'] = $_POST['nama'] ;  
        $data['bobot'] = $_POST['bobot'] ;   
        $query = insert_db($data, 'kriteria');

        if($query){
            set_flashdata('sukses', 'berhasil menambah Kriteria '); 
        }else{
            set_flashdata('error', 'Gagal Menambah Kriteria ');
        }
    }
    redirect(base_url('users/administrator/data_kriteria.php'));

    ?>
 
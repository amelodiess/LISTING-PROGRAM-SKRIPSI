<?php
    require_once('../../system/engine.php');

    define("ON_DATA_PENGAJUAN", true);
    define("SITE_TITLE", 'Kelola Data Pegawai');


    $css = array('assets/plugins/datatable/datatables.bootstrap4.min.css');
    $js = array('assets/plugins/datatable/datatables.min.js');

    if(!get_session("user_login")){ 
        set_flashdata("info", "Silahkan login untuk melanjutkan");
        redirect(base_url('login'));
    }
    
    $bulan = 'february';
    $bulan_number = 2;
    $tahun = date('Y'); 

    if(date('Y-m-d') > date('Y-m-d', strtotime(date('Y') . '-08-01'))){
        $bulan = 'agustus';
        $bulan_number = 8;
    } 
    if(date('m') == '01'){  
        $bulan = 'agustus';
        $bulan_number = 8;
        $tahun = date('Y', strtotime('-1 years'));
    }

    require_once('../layout/header.php');
    function get_data(){
        global $db; 
        $query = $db->prepare("SELECT *, (SELECT nama from pengguna where id = admin_id) as admin FROM pengajuan JOIN pengguna ON pengguna.id = pengajuan.pegawai_id WHERE admin_id =" . escape(get_session('user_id')));
        $query->execute();
        return $query->fetchAll(PDO::FETCH_OBJ);
    }  

    function getDataPenilaian($bulan, $tahun, $pegawai_id, $kriteria_id){
        //echo $bulan . ' -- ' . $tahun . ' -- ' . $pegawai_id . ' -- ' . $kriteria_id . ' -- ' . $tipe;
        global $db;
        $textQ = '
            SELECT penilaian.*, penilaian.id as penilaian_id 
            FROM penilaian 
            JOIN kriteria ON kriteria.id = penilaian.kriteria_id  
            WHERE pegawai_id = :pegawai_id AND kriteria_id = :kriteria_id
            AND tahun = :tahun AND bulan = :bulan'; 
        $query = $db->prepare($textQ);  
        $query->bindValue(':kriteria_id', $kriteria_id, PDO::PARAM_STR);
        $query->bindValue(':pegawai_id', $pegawai_id, PDO::PARAM_STR);
        $query->bindValue(':tahun', $tahun, PDO::PARAM_STR);
        $query->bindValue(':bulan', $bulan, PDO::PARAM_STR);
        $query->execute(); 
        return $query->fetch(PDO::FETCH_OBJ); 
    }

    function getDataKriteria(){ 
        global $db;
        $query = $db->prepare('
            SELECT kriteria.*, kriteria.id as kriteria_id 
            FROM kriteria'
            ); 
        $query->execute();
        return $query->fetchAll(PDO::FETCH_OBJ); 
    } 

    function getPenilaianHistory($exclude_bulan, $exculude_tahun){
        global $db;
        $query = $db->prepare('SELECT * FROM penilaian WHERE bulan != :bulan OR tahun != :tahun GROUP BY bulan, tahun WHERE pegawai_id = :pegawai_id');
        $query->bindValue(':bulan', $exclude_bulan, PDO::PARAM_STR);
        $query->bindValue(':tahun', $exculude_tahun, PDO::PARAM_STR);
        $query->bindValue(':pegawai_id', $pegawai_id, PDO::PARAM_STR);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_OBJ); 

    } 
    $pengajuan = get_data();
    $kriteria = getDataKriteria();




    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Data Pengajuan  
            </h1> 
        </section>


        <!-- Main content -->
        <section class="content">
        <?php
            if(has_flashdata('sukses')){
                echo alert_sukses(get_flashdata('sukses')); 
            } 
            if(has_flashdata('error')){    
                echo alert_error(get_flashdata('error'));  
            }
            if(has_flashdata('warning')){
                echo alert_warning(get_flashdata('warning'));  
            }
            if(has_flashdata('info')){
                echo alert_info(get_flashdata('info'));    
            }
        ?>
           <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                           <h3 class="card-title">Status pengajuan</h3> 
                        </div><!-- .box-header -->
                        <div class="card-body"> 
                            <table id="data_table"  class="table table-stripped table-bordered">
                                <thead>
                                <tr> 
                                    <th width="20%">Nama pegawai </th> 
                                    <th width="10%">Tipe</th>
                                    <th width="">Alasan</th> 
                                    <th width="">Keterangan</th>
                                    <th width="10%">status</th>  
                                </tr>
                                </thead>
                                <tbody>
                                  <?php  foreach ($pengajuan as $idx => $dtp): ?>
                                    <tr> 
                                      <td><?php echo $dtp->nama; ?></td> 
                                      <td><?php echo $dtp->tipe; ?></td> 
                                      <td><?php echo $dtp->alasan; ?></td>  
                                      <td><?php echo $dtp->keterangan; ?></td> 
                                      <td><?php echo get_status_pengajuan($dtp->status); 
                                            if($dtp->status == 9){
                                                echo " <Br/>alasan : $dtp->alasan_tolak";
                                            }
                                       ?></td> 
                                    </tr>
                                  <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div><!-- .box-body -->
                    </div><!-- .box -->
                </div><!-- .col -->
            </div><!-- .row -->
        </section>
        <!-- .content -->
    </div>
    <!-- .content-wrapper -->

    <form class="form-horizontal" method="post" action="<?= base_url('users/penilai/pengajuan.php'); ?>"> 
    <div class="modal fade" id="modal_pengajuan">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="height: 20px; border: none">
                    <h5 class="modal-title">Ajukan karyawan ini</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">  
                    <table class="table">
                        <tr>
                            <td>Nama</td>
                            <td>:</td>
                            <td><span id="pn_nama"></span></td>
                        </tr>
                        <tr>
                            <td>Penilai</td>
                            <td>:</td>
                            <td><span id="pn_penilai"></span></td>
                        </tr>
                        <tr>
                            <td>Status Kepegawaian</td>
                            <td>:</td>
                            <td><span id="pn_status_kepegawaian"></span></td>
                        </tr>
                        <tr>
                            <td>Tipe</td>
                            <td>:</td>
                            <td><span id="pn_tipe"></span></td>
                        </tr>
                        <tr>
                            <td>Dari</td>
                            <td>:</td>
                            <td><span id="pn_dari"></span></td>
                        </tr>
                        <tr>
                            <td>Ke</td>
                            <td>:</td>
                            <td><span id="pn_ke"></span></td>
                        </tr>
                        <tr>
                            <td>Keterangan</td>
                            <td>:</td>
                            <td><span id="pn_keterangan"></span></td>
                        </tr>
                    </table>

                    <div class="row border-top border-bottom my-4 py-4">
                        <div class="col-md-6 text-center">
                            <a href="" class="btn terima btn-lg btn-success">
                                <i class="fa fa-check"></i> Terima pengajuan
                            </a> 
                        </div>
                        <div class="col-md-6 text-center">
                            <a href="" class="btn tolak btn-lg btn-danger">
                                <i class="fa fa-times"></i> Tolak pengajuan
                            </a> 
                        </div>
                    </div>
                    <div id="tb_pen">
                        <h5>Hasil Penilaian</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kriteria</th>
                                    <th>Nilai</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer"> 
                    <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-secondary">close</button> 
                </div>
            </div> 
        </div> 
    </div><!-- .modal --> 
    </form>

 

    <?php  require_once('../layout/footer.php'); ?>
    <script type="text/javascript">
       $('.btn_pengajuan').click( function() {    
         $('#pn_nama').html($(this).data('nama'));  
         $('#pn_penilai').html($(this).data('penilai'));  
         $('#pn_status_kepegawaian').html($(this).data('status_kepegawaian'));  
         $('#pn_tipe').html($(this).data('tipe'));  
         $('#pn_dari').html($(this).data('dari'));  
         $('#pn_ke').html($(this).data('ke'));  
         $('#pn_status').html($(this).data('status'));  
         $('#pn_keterangan').html($(this).data('keterangan'));  
         var id = $(this).data('id');
         var tb_pen = $(this).data('nilai');
         if(tb_pen.length){
            $('#tb_pen').show();
            $('#tb_pen table tbody').html("");
            tb_pen.map(tp => {
                $('#tb_pen table tbody').append(`
                    <tr>
                        <td>${tp.no}</td>
                        <td>${tp.kriteria}</td>
                        <td>${tp.nilai}</td>
                    </tr>
                `);
            })
         }else{
            $('#tb_pen').hide();
         }
         $('#modal_pengajuan').modal('show'); 
         $('.terima').attr('href', `${BASE_URL}users/module/pengajuan.php?s=1&id=${id}`);
         $('.tolak').attr('href', `${BASE_URL}users/module/pengajuan.php?s=0&id=${id}`);
     })

    </script>

    
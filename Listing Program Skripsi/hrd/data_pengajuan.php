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
        $query = $db->prepare("SELECT *, pengajuan.id as pengajuan_id, (SELECT nama from pengguna where id = admin_id) as admin FROM pengajuan JOIN pengguna ON pengguna.id = pengajuan.pegawai_id");
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
                                    <th width="10%">Alasan</th> 
                                    <th width="25%">Keterangan</th>
                                    <th width="">status</th> 
                                    <th width="">aksi</th> 
                                </tr>
                                </thead>
                                <tbody>
                                  <?php  foreach ($pengajuan as $idx => $dtp): ?>
                                    <tr> 
                                      <td><?php echo $dtp->nama; ?></td> 
                                      <td><?php echo $dtp->tipe; ?></td> 
                                      <td><?php echo $dtp->alasan; ?></td>  
                                      <td><?php echo $dtp->keterangan; ?></td> 
                                      <td><?php echo get_status_pengajuan($dtp->status); ?></td> 
                                      <td class="text-center">
                                          <form method="post" action="pengajuan.php">
                                          <div class="hd hd_<?= $dtp->pengajuan_id ?>">  
                                              <a href="pengajuan.php?id=<?= $dtp->pengajuan_id ?>&s=1" class="btn terima btn-xs btn-success">
                                                  <i class="fa fa-check"></i> Terima
                                              </a> 
                                              <button type="button" data-id="<?= $dtp->pengajuan_id ?>" class="btn tolak btn-xs btn-danger">
                                                  <i class="fa fa-times"></i> Tolak
                                              </button> 
                                          </div>
                                          <div class="sd text-left sd_<?= $dtp->pengajuan_id ?> hidden">  
                                            <b>Alasan menolak</b>
                                            <textarea class="form-control mb-2" name="keterangan_tolak"></textarea>
                                            <input type="hidden" name="id" value="<?= $dtp->pengajuan_id ?>">
                                              <button type="submit" class="btn btn-xs btn-danger">
                                                  <i class="fa fa-times"></i> Tolak pengajuan
                                              </button> 
                                              <button type="button" data-id="<?= $dtp->pengajuan_id ?>" class="btn batal btn-xs btn-default">
                                                  <i class="fa fa-times"></i> Batalkan 
                                              </button> 
                                          </div>
                                          </form>
                                      </td> 
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
 
 

    <?php  require_once('../layout/footer.php'); ?>
    <script type="text/javascript">
         $('.tolak').click(function(){
            $(`.hd_${$(this).data('id')}`).addClass("hidden");
            $(`.sd_${$(this).data('id')}`).removeClass("hidden");
         }) 

         $('.batal').click(function(){
            $(`.hd_${$(this).data('id')}`).removeClass("hidden");
            $(`.sd_${$(this).data('id')}`).addClass("hidden");
         })
    </script>

    
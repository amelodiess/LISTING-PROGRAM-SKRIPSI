<?php
require_once '../../system/engine.php';

define("ON_PENILAIAN", true);
define("SITE_TITLE", 'Penilaian Pegawai');

$css = array('assets/plugins/datatable/datatables.bootstrap4.min.css');
$js = array('assets/plugins/datatable/datatables.min.js');

if (!get_session("user_login")) {
    set_flashdata("info", "Silahkan login untuk melanjutkan");
    redirect(base_url('login'));
}
if ($_GET['id'] == '') {
    redirect(base_url('users/module/penilaian.php'));
}

$id = $_GET['id'];
$bulan = 'april';
$bulan_number = 4;
$tahun = date('Y');

$query = false;
if (isset($_POST['submit'])) {
    foreach ($_POST['kriteria_id'] as $key => $val) {
        $data['pegawai_id'] = $id;
        $data['tahun'] = $tahun;
        $data['bulan'] = $bulan_number;
        $data['kriteria_id'] = $val;
        $data['nilai'] = $_POST['penilaian'][$key];
        $penilaian_id = getDataPenilaian($bulan_number, $tahun, $id, $val);
        $penilaian_id = (isset($penilaian_id->penilaian_id)) ? $penilaian_id->penilaian_id : false;
        if ($penilaian_id) {
            $query = update_db($data, 'penilaian', "id = $penilaian_id");
        } else {
            $query = insert_db($data, 'penilaian');
        }
    }
    if ($query) {
        set_flashdata('sukses', 'berhasil mengupdate penilaian');
    } else {
        set_flashdata('error', 'Gagal mengupdate penilaian');
    }
}

require_once '../layout/header.php';

function getDataPenilaian($bulan, $tahun, $pegawai_id, $kriteria_id)
{
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
    $query = $db->prepare(
        '
            SELECT kriteria.*, kriteria.id as kriteria_id
            FROM kriteria'
    );
    $query->execute();
    return $query->fetchAll(PDO::FETCH_OBJ);
}

function getDataPegawai($id){
    global $db;
    $query = $db->prepare('SELECT *, (SELECT nama from divisi where divisi.id = divisi_id) as divisi FROM pengguna WHERE id = :id');
    $query->bindValue(':id', $id, PDO::PARAM_STR);
    $query->execute();
    return $query->fetch(PDO::FETCH_OBJ);
}

function getPenilaianHistory($exclude_bulan, $exculude_tahun){
    global $db;
    $query = $db->prepare('SELECT * FROM penilaian WHERE bulan != :bulan OR tahun != :tahun GROUP BY bulan, tahun');
    $query->bindValue(':bulan', $exclude_bulan, PDO::PARAM_STR);
    $query->bindValue(':tahun', $exculude_tahun, PDO::PARAM_STR);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_OBJ);
}

$history = getPenilaianHistory($bulan_number, $tahun);
$krywn = getDataPegawai($id);
$kriteria = getDataKriteria();
 

?>


<style>
.control-nilai {
  position: relative;
}

.control-nilai .control-label {
  position: absolute;
  top: 1px;
  left: 1px;
  padding: 12px 10px;
  width: 200px;
  border-right: 1px solid #ced4da;
  background: #eee;
  height: 36px;
}

.control-nilai .ct-label {
  position: absolute;
  top: 8px;
  font-size: 12px;
  right: 10px;
  color: #aaa;
  padding: 1px 5px;
  border-radius: 10px;
  display: inline-block;
  border: 1px solid #ddd;
}

.control-nilai .form-control {
  padding-left: 210px;
  box-shadow: none !important;
}

li {
  margin-bottom: 10px;
}
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <a href="<?=base_url('users/module/penilaian.php')?>">
      <h1>Penilaian pegawai</h1>
    </a>
    <h5>
      <i class="fa fa-caret-right"></i>
      Buat penilaian
    </h5>
  </section>


  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <form method="post" action="">
          <div class="card">
            <div class="card-header">
              <?php
if (has_flashdata('sukses')) {
    echo alert_sukses(get_flashdata('sukses'));
}
if (has_flashdata('error')) {
    echo alert_error(get_flashdata('error'));
}
if (has_flashdata('warning')) {
    echo alert_warning(get_flashdata('warning'));
}
if (has_flashdata('info')) {
    echo alert_info(get_flashdata('info'));
}
?>
              <div class="">
                <button class="btn btn-lg btn-primary" type="submit" name="submit" value="1"><i class="fa fa-save"></i>
                  Buat penilaian</button>
              </div>
            </div><!-- .box-header -->
            <div class="card-body">
              <div class="row">
                <div class="col-lg-6 col-md-6">
                  <div class="w-100">
                    <div class="py-2"><b class="d-inline-block" style="width: 150px">Nama</b> :
                      <?=$krywn->nama?></div>
                    <div class="py-2"><b class="d-inline-block" style="width: 150px">nik</b> :
                      <?=$krywn->nik?></div>
                    <div class="py-2"><b class="d-inline-block" style="width: 150px">divisi</b> :
                      <?=$krywn->divisi?>
                    </div>
                    <br>
                  </div>
                </div>
                <div class="w-100 pt-3"></div>
                <div class="col-lg-6 col-md-6">
                  <h6 class="mb-4"><b>Penilaian bulan <?=ucfirst($bulan)?> <?=$tahun?></b></h6>
                  <?php foreach ($kriteria as $krit): ?>
                  <?php $nilai = (isset(getDataPenilaian($bulan_number, $tahun, $id, $krit->kriteria_id)->penilaian_id)) ? getDataPenilaian($bulan_number, $tahun, $id, $krit->kriteria_id)->nilai : '';?>
                  <div class="w-100 mb-3 control-nilai">
                    <label class="control-label"><?=$krit->nama_kriteria?></label>
                    <input type="hidden" name="kriteria_id[]" value="<?=$krit->kriteria_id;?>">
                    <input class="only_number form-control" placeholder="masukan nilai disini" type="text"
                      name="penilaian[]" data-a-sep="." data-a-dec="," data-v-max="100" value="<?=$nilai?>">
                    <span class="ct-label">1 - 100</span>
                  </div>
                  <?php endforeach?>
                </div>

                <div class="col-md-6 pl-5">
                  <ul>
                    <li>Penilaian kinerja pegawai dilakukan oleh atasan seperti HDR atau kepala bagian di setiap divisi
                      bagianya.</li>
                    <li>Penilaian kinerja dilakukan pada periode yang telah ditentukan oleh HRD.</li>
                    <li>Penilaian kinerja yang terjadwal saat ini dilaksanakan setiap tahunnya yaitu pada bulan april
                      dalam rentang waktu 2 minggu.</li>
                    <li>Jika rentang waktu sudah melewati batas waktu 2 minggu yang telah ditentukan, maka penilaian
                      tidak bisa dilakukan lagi.</li>
                    <li>Jika hasil penilaian buruk, maka pegawai akan diberikan sanksi berupa sidipliner, surat
                      peringatan hingga di PHK.</li>
                  </ul>
                </div>
              </div>
              <div class="w-100 py-3"></div>
              <?php foreach ($history as $key => $hist): ?>
              <h6 class="text-center"><b>Penilaian bulan <?=get_nama_bulan($hist->bulan)?> tahun <?=$hist->tahun?></b>
              </h6>
              <table class="table table-bordered mb-5">
                <tr>
                  <th>No</th>
                  <th>Kriteria</th>
                  <th>Nilai</th> 
                  <th>Waktu</th>
                </tr>
                <?php foreach ($kriteria as $idx => $krit): ?>
                <?php $dt_nilai = getDataPenilaian($hist->bulan, $hist->tahun, $id, $krit->kriteria_id);
                if (isset($dt_nilai->nilai)) {
                    ?>
                                <tr>
                                  <td><?=($idx + 1)?></td>
                                  <td><?=$krit->nama_kriteria?></td>
                                  <td><?=$dt_nilai->nilai?></td> 
                                  <td><?=format_tanggal_waktu($dt_nilai->waktu)?></td>
                                </tr>
                                <?php
                } else {
                    echo "<tr><td colspan='5'><center>tidak ada penilaian</center></td></tr>";
                }
                endforeach?>
              </table>
              <?php endforeach?>
            </div><!-- .box-body -->
        </form>
      </div><!-- .box -->
    </div><!-- .col -->
</div><!-- .row -->
</section>
<!-- .content -->
</div>
<!-- .content-wrapper -->
<?php require_once '../layout/footer.php';?>
<script type="text/javascript" src="<?=base_url()?>/assets/js/moment.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>/assets/js/autoNumeric.js"></script>

<script type="text/javascript">
$('.only_number').autoNumeric('init', {
  mDec: '0'
});
</script>
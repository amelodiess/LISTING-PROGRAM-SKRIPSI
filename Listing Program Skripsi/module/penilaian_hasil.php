<?php
require_once '../../system/engine.php';

define("ON_HASIL_PENILAIAN", true);
define("SITE_TITLE", 'Kelola Data Pegawai');

$css = array('assets/plugins/datatable/datatables.bootstrap4.min.css');
$js = array('assets/plugins/datatable/datatables.min.js');

if (!get_session("user_login")) {
    set_flashdata("info", "Silahkan login untuk melanjutkan");
    redirect(base_url('login'));
}

$bulan_text = 'april';
$bulan = 4;
$tahun = date('Y');

// echo "$bulan  - $tahun";die;

require_once '../layout/header.php';

function get_rank($array)
{
    $ordered_values = $array;
    rsort($ordered_values);
    $rank = [];
    foreach ($array as $key => $val) {
        foreach ($ordered_values as $ordered_key => $ordered_value) {
            if ($val === $ordered_value) {
                $key = $ordered_key;
                break;
            }
        }
        if ($val > 0) {
            $rank[] = ((int) $key + 1);
        } else {
            $rank[] = 0;
        }
    }
    return $rank;
}
function get_opsi_rekomendasi($rank, $length)
{
    switch ($rank) {
        case 1:
            return "Bonus Tunjangan Jabatan";
            break;
        case 2:
            return "Bonus Tunjangan Jabatan & Pembinaan kemampuan";
            break;
        case 3:
            return "Pelatihan Keterampilan & Pembinaan Keterampilan";
            break;
        case $length - 1:
            return "Mutasi / Coaching";
            break;
        case $length:
            return "Pemberian Surat Peringatan / PHK";
            break;

        default:
            return "-";
            break;
    }
}

function getDataPegawai()
{
    global $db; 
    $query = $db->prepare('SELECT pengguna.*, divisi.nama as divisi FROM pengguna JOIN divisi ON divisi.id = pengguna.divisi_id WHERE kepala_id = :id');
    $query->bindValue(':id', get_session('user_id'), PDO::PARAM_STR);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_OBJ);
}
function hitung_weight($user_id, $bulan, $tahun, $bobot)
{
    global $db;
    $query = $db->prepare("SELECT :bobot / sum(kriteria.bobot) as tot FROM penilaian
        JOIN kriteria ON kriteria.id = penilaian.kriteria_id WHERE pegawai_id = :id and bulan = :bulan and tahun = :tahun");
    $query->bindValue(':id', $user_id, PDO::PARAM_STR);
    $query->bindValue(':bulan', $bulan, PDO::PARAM_STR);
    $query->bindValue(':tahun', $tahun, PDO::PARAM_STR);
    $query->bindValue(':bobot', $bobot, PDO::PARAM_STR);
    $query->execute();
    return $query->fetch(PDO::FETCH_OBJ)->tot;
}
function getPenilaian($user_id, $bulan, $tahun)
{
    global $db;
    $query = $db->prepare("SELECT *, (SELECT bobot from kriteria WHERE kriteria.id = penilaian.kriteria_id) as bobot FROM penilaian WHERE pegawai_id = :id and bulan = :bulan and tahun = :tahun");
    $query->bindValue(':id', $user_id, PDO::PARAM_STR);
    $query->bindValue(':bulan', $bulan, PDO::PARAM_STR);
    $query->bindValue(':tahun', $tahun, PDO::PARAM_STR);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_OBJ);
}
$data_pegawai = getDataPegawai();

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Hasil penilaian pegawai
    </h1>
  </section>


  <!-- Main content -->
  <section class="content">

    <div class="row">
      <div class="col-md-12">
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
          </div><!-- .box-header -->
          <div class="card-body">
            <table id="data_table" class="table table-stripped table-bordered">
              <thead>
                <tr>
                  <th>Nama </th>
                  <th width="15%">NIK</th>
                  <th width="10%">Akumulasi</th>
                  <th width="10%">Rata-rata</th>
                  <th width="10%">Vektor S</th>
                  <th width="10%">Vektor V</th>
                  <th width="10%">Ranking</th>
                  <th width="15%">Opsi Rekomendasi</th>
                  <th width="7%"></th>
                </tr>
              </thead>
              <tbody>
                <?php
$vektor_s = array();
$vektor_v = array();
$average = array();
$total = array();
foreach ($data_pegawai as $dtp) {
    $w = array();
    $n = array();
    $n_raw = array();
    // echo $dtp->nama . " ";
    foreach (getPenilaian($dtp->id, $bulan, $tahun) as $key => $pen) {
        $weight = floatval(hitung_weight($dtp->id, $bulan, $tahun, $pen->bobot));
        // echo $pen->nilai . "^" . $weight . " ";
        $nilai = pow($pen->nilai, $weight);
        $n[] = round($nilai, 4);
        $w[] = $weight;
        $n_raw[] = round($pen->nilai, 4);
    }
    // print_r($n);
    // echo "<br/>";

    $average[$dtp->id] = (array_sum($n_raw) > 0) ? array_sum($n_raw) / count($n_raw) : 0;
    $total[$dtp->id] = array_sum($n_raw);
    $vektor_s[$dtp->id] = (count($w) > 0) ? array_product($n) : 0;
}
foreach ($data_pegawai as $dtp) {
    if (array_sum(array_values($vektor_s)) > 0) {
        $vektor_v[$dtp->id] = $vektor_s[$dtp->id] / array_sum(array_values($vektor_s));
    } else {
        $vektor_v[$dtp->id] = 0;
    }
}
$rank = get_rank($vektor_v);
foreach ($data_pegawai as $idx => $dtp):
    if (floatval($vektor_v[$dtp->id]) > 0) {
        ?>
			    <tr>
			      <td><?php echo $dtp->nama; ?></td>
			      <td><?php echo $dtp->nik; ?></td>
			      <td><?php echo round($total[$dtp->id], 3); ?></td>
			      <td><?php echo round($average[$dtp->id], 3); ?></td>
			      <td><?php echo round($vektor_s[$dtp->id], 3); ?></td>
			      <td><?php echo round($vektor_v[$dtp->id], 3); ?></td>
			      <td><?php echo $rank[$idx]; ?></td>
			      <td class="text-center">
              <?=get_opsi_rekomendasi($rank[$idx], count($rank));?>
			      </td>
            <td class="text-right"> 
              <?php if ($rank[$idx] <= 5) {?>
              <button data-nama="<?=$dtp->nama?>" data-rekomendasi="<?=get_opsi_rekomendasi($rank[$idx], count($rank));?>" data-id="<?=$dtp->id?>" type="button"
                class="btn btn-xs btn-outline-dark btn_pengajuan" href="#"> rekomendasikan
              </button>
              <?php }?>
            </td>
			    </tr>
    <?php
    }
endforeach;?>
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

<?php require_once '../layout/footer.php';?>

<form class="form-horizontal" method="post" action="<?=base_url('users/module/pengajuan.php');?>">
  <div class="modal fade" id="modal_pengajuan">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Ajukan pegawai</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div>NAMA : <span id="pengajuan_nama"></span></div><Br>
          <div class="form-group">
            <label class="col-sm-12 control-label">Tipe pengajuan</label>
            <div class="col-sm-12">
             <input id="pengajuan_tipe" readonly="" type="" class="form-control bg-white" name="tipe_pengajuan">
            </div>
          </div>
          
          <div class="form-group">
            <label class="col-sm-12 control-label">Alasan Pengajuan</label>
            <div class="col-sm-12">
              <textarea class="form-control" name="alasan"></textarea>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-12 control-label">Keterangan</label>
            <div class="col-sm-12">
              <textarea class="form-control" name="keterangan"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <input type="hidden" value="" id="pengajuan_id" name="pegawai_id">
          <input type="hidden" value="tetap" name="tipe">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Batal</button>
          <button type="submit" name="submit" value="1" class="btn btn-primary">Ajukan</button>
        </div>
      </div>
    </div>
  </div><!-- .modal -->
</form>

<script type="text/javascript">
$('.btn_pengajuan').click(function() {
  $('#pengajuan_id').val($(this).data('id'));
  $('#pengajuan_nama').html($(this).data('nama'));
  $('#pengajuan_tipe').val($(this).data('rekomendasi'));
  $('#modal_pengajuan').modal('show');
})

$('.cs').change(function() {
  if ($(this).val() == 'PELATIHAN' || $(this).val() == 'PEMBINAAN') {
    $('.xat').hide();
  } else {
    $('.xat').show();
  }
})



    data_table.order([6, 'asc']).draw();
</script>
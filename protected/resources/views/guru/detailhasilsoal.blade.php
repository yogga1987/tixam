@extends('layouts/guru_baru')
@section('title', 'Detail Soal per Kelas.')
@section('content')
<script src="{{ url('/assets/assets/vendor/jquery.min.js') }}"></script>
<?php
  include(app_path().'/functions/koneksi.php');
  $sapaan = Auth::user()->jk;
  if ($sapaan == "L") {
    $sapaan = "Pak";
  }else{
    $sapaan = "Ibu";
  }
?>
<div class="col-md-12 dash-left">
  <ol class="breadcrumb">
    <li><a href="{{ url('/guru') }}">Home</a></li>
    <li><a href="{{url('hasil-guru')}}">Laporan</a></li>
    <li><a href="{{url('detail-hasil/'.$soal->id)}}">Detail Kelas</a></li>
    <li class="active">Hasil Ujian Kelas {{$kelas->nama}}</li>
  </ol>
  <div class="panel panel-default">
    <div class="panel-heading">Detail Ujian Kelas <b>{{$kelas->nama}}</b> Paket Soal <b>{{$soal->paket}}</b></div>
    <div class="panel-body">
      <div class="alert alert-info" role="alert"><b><i class="fa fa-info-circle"></i> Info: </b>Dibawah ini detail siswa kelas {{$kelas->nama}} yang telah mengerjakan paket soal <b>{{$soal->paket}}</b></div>
      <table class="table table-bordered table-hover table-condensed" id="tabelsoal">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Nilai</th>
            <th width="170px">Aksi</th>
          </tr>
        @if($jawabs->count())
        <?php $no = 1; ?>
        @foreach($jawabs as $jawab)
        <?php
          $id_user = $jawab->id_user;
          $sql="SELECT sum(score) as total FROM jawabs WHERE id_kelas='$jawab->id_kelas' AND id_soal='$jawab->id_soal' AND id_user='$jawab->id_user'";
          $datatotal = $conn->query($sql);
          while ($row = mysqli_fetch_assoc($datatotal))
          {
            $nilai = $row['total'];
          }
          $sql = "SELECT id, nama FROM users WHERE id='$id_user'";
          $result = $conn->query($sql);
          if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
          ?>
          <input type="hidden" id="ik{{ $jawab->id_kelas }}" value="{{ $jawab->id_kelas }}">
          <input type="hidden" id="is{{ $jawab->id_soal }}" value="{{ $jawab->id_soal }}">
          <input type="hidden" id="iu{{ $jawab->id_user }}" value="{{ $jawab->id_user }}">
          <?php if ($soal->kkm > $nilai) { 
            echo "<tr class='danger'>";
          }else{ ?>
          <tr class="success">
          <?php } ?>
            <td width="20px">{{ $no++ }}</td>
            <td>{{ $row['nama'] }}</td>
            <td width="70px" align="center"><?= $nilai; ?></td>
            <td align="center"><a href="#" id="btndetail{{$jawab->id_user}}" class="btn btn-xs btn-primary"  data-toggle="tooltip" title="Detail rekap ujian per kelas."><i class="fa fa-search"></i> Detail</a> | <a href="#" id="btnhapus{{$jawab->id_user}}" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Akan menghapus seluruh hasil ujian dari kelas yang dipilih. Data yang dihapus tidak bisa dikembalikan."><i class="fa fa-trash-o"></i> Hapus</a></td>
          </tr>
          <tr id="detail{{$jawab->id_user}}">
            <td colspan="4"><table class="table table-condensed table-bordered table-hover table-striped">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Soal</th>
                    <th>Kunci</th>
                    <th>Jawab</th>
                    <th>Score</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $no_detail = 1;
                    $sql = "SELECT * FROM jawabs WHERE id_kelas='$jawab->id_kelas' AND id_soal='$jawab->id_soal' AND id_user='$jawab->id_user' ORDER BY no_soal_id ASC";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                  while($row = $result->fetch_assoc()) {
                ?>
                  <tr>
                    <td width="20px">{{$no_detail++}}</td>
                    <td><?php
                      $sqlsoal = "SELECT * FROM detailsoals WHERE id='$row[no_soal_id]'";
                $resultsoal = $conn->query($sqlsoal);
                if($resultsoal->num_rows > 0){
                  while($rowsoal = $resultsoal->fetch_assoc()){
                    echo $rowsoal['soal'];
                  }
                }
                      ?></td>
                    <td><?php
                      $sqlsoal = "SELECT * FROM detailsoals WHERE id='$row[no_soal_id]'";
                $resultsoal = $conn->query($sqlsoal);
                if($resultsoal->num_rows > 0){
                  while($rowsoal = $resultsoal->fetch_assoc()){
                    echo $rowsoal['kunci'];
                  }
                }
                      ?></td>
                    <td>{{ $row['pilihan'] }}</td>
                    <td>{{ $row['score'] }}</td>
                  </tr>
                  <?php }} ?>
                </tbody>
              </table>
            </td>
          </tr>
          <tr id="notifdel{{ $jawab->id_user }}">
            <td colspan="4" class="alert alert-danger">Data diatas berhasil dihapus. <i>Refresh</i> halaman untuk melihat perubahannya.</td>
          </tr>
          <script type="text/javascript">
            $(document).ready(function() {
              $("#detail{{$jawab->id_user}}").hide();
              $("#btndetail{{$jawab->id_user}}").click(function() {
                $("#detail{{$jawab->id_user}}").toggle();
                return false;
              });

              $("#notifdel{{ $jawab->id_user }}").hide();
              $("#btnhapus{{$jawab->id_user}}").click(function() {
                if (!confirm('Yakin akan dihapus?')) return false;
                var id_kelas = $("#ik{{ $jawab->id_kelas }}").val();
                var id_soal = $("#is{{ $jawab->id_soal }}").val();
                var id_user = $("#iu{{ $jawab->id_user }}").val();
                var datastring = "id_kelas="+id_kelas+"&id_soal="+id_soal+"&id_user="+id_user;
                $.ajax({
                  type: "POST",
                  url: "{{ url('/hapusjawabsiswa') }}",
                  data: datastring,
                  success: function(data){
                    $("#notifdel{{ $jawab->id_user }}").show();
                  }
                });
              });
            });
          </script>
        <?php } } ?>
        @endforeach
        @endif
        </thead>
      </table>
    </div>
  </div>
</div>
<script src="{!! url('vendor/twbs/bootstrap/dist/js/bootstrap.min.js') !!}"></script>
<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  $(document).ready(function() {
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })
    $("#loading").hide();
    $("#salah").hide();
    $("#benar").hide();
    $('.collapse').collapse();
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })

    $("#btnsimpansiswa").click(function() {
      $(this).hide();
      $("#loading").show();
      var paket = $("#paket").val();
      var deskripsi = $("#deskripsi").val();
      var kkm = $("#kkm").val();
      var waktu = $("#waktu").val();
      var datastring = "paket="+paket+"&deskripsi="+deskripsi+"&kkm="+kkm+"&waktu="+waktu;
      $.ajax({
        type: "POST",
        url: "{{ url('/simpanformsoal') }}",
        data: datastring,
        success: function(data){
          if(data == "berhasil"){
            $("#loading").hide();
            $("#salah").hide();
            $("#benar").show();
            $("#btnsimpansiswa").show();
          }else{
            $("#loading").hide();
            $("#benar").hide();
            $("#salah").html(data).show();
            $("#btnsimpansiswa").show();
          }
        }
      });
      return false;
    });
  });
</script>
@endsection
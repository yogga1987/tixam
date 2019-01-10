@extends('layouts/guru_baru')
@section('title', 'Detail soal')
@section('content')

<script src="{{ url('/assets/assets/vendor/jquery.min.js') }}"></script>
<?php
  if (Auth::user()->status != "S" or Auth::user()->status != "C") {
  include(app_path() . '/functions/koneksi.php');
?>
<div class="col-md-12 content">
  <ol class="breadcrumb">
    <li><a href="{{url('guru')}}">Home</a></li>
    <li><a href="{{ url('soal-guru') }}">Soal</a></li>
    <li class="active">{{ $soal->paket }}</li>
  </ol>
  <div class="panel panel-default">
    <div class="panel-heading" style="background: #072047; color: #fff">Detail Paket Soal</div>
    <div class="panel-body"> <a href="#" id="btsoal">
      <button type="button" class="btn btn-primary" data-toggle="tooltip" title="Tambah soal">Tambah Soal</button>
      </a>
      <?php if ($soal->jenis == 1) { ?>
        <a href="#" id="btkelas">
        <button type="button" class="btn btn-primary" data-toggle="tooltip" title="Lihat Kelas">Lihat Kelas</button>
        </a>
        <div id="wrapdistribusisoal" style="margin:15px 0 0 0; display:none;">
          <div class="panel panel-default">
            <div class="panel-body">
              <h3 class="text-center">Daftar Kelas</h3>
              <div class="alert alert-info" role="alert"><b>Perhatian!</b> Dibawah ini adalah daftar seluruh kelas dari <b>{{$school->nama}}</b>, Pilihlah kelas mana yang dapat melihat soal Anda dengan cara klik checkbox disisi kiri nama kelas. Tampilkan soal saat menjelang ujian dan sembunyikan soal saat sudah selesai diujikan.</div>
              <div class="well">
              @if($kelas->count())
              @foreach($kelas as $data)
                <?php
                  $conn = new mysqli($hostdb, $userdb, $passdb, $namedb);
                  if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                  }
                  $sql = "SELECT * FROM distribusisoals WHERE id_soal = '$soal->id' AND id_kelas = '$data->id'";
                  $result = $conn->query($sql);
                ?>
                <input type="hidden" name="id_soal{{$data->id}}" id="id_soal{{$data->id}}" value="{{$soal->id}}">
                <input type="hidden" name="id_kelassimpan{{$data->id}}" id="id_kelassimpan{{$data->id}}" value="{{$data->id}}">
                <input type="checkbox" name="id_kelas{{$data->id}}" id="id_kelas{{$data->id}}" value="{{$data->id}}"
                <?php
                  if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                      echo "checked";
                    }
                  }
                ?>
                > {{ $data->nama }}<br>
                <script>
                  $(document).ready(function() {
                    $("#id_kelas{{$data->id}}").change(function() {
                      if(this.checked) {
                        var id_kelas = $("#id_kelassimpan{{$data->id}}").val();
                        var id_soal = $("#id_soal{{$data->id}}").val();
                        var datastring = "id_kelas="+id_kelas+"&id_soal="+id_soal;
                        $.ajax({
                          type: "POST",
                          url: "{{ url('/simpandistribusikelas') }}",
                          data: datastring,
                        });
                      }else{
                        var id_kelas = $("#id_kelassimpan{{$data->id}}").val();
                        var id_soal = $("#id_soal{{$data->id}}").val();
                        var datastring = "id_kelas="+id_kelas+"&id_soal="+id_soal;
                        $.ajax({
                          type: "POST",
                          url: "{{ url('/hapusdistribusikelas') }}",
                          data: datastring,
                        });
                      }
                    });
                  });
                </script>
              @endforeach
              @endif
              </div>
            </div>
          </div>
        </div>
      <?php } ?>
      <div id="wrapsoal" style="margin:15px 0 0 0; display:none;">
        <div class="well" style="background:#fff;"> {!! Form::open(['url' => 'simpansiswa', 'class' => 'form-horizontal']) !!}
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">Soal</label>
            <div class="col-sm-10">
              <input type="hidden" name="paket" id="paket" value="{{ $id_soal }}">
              <textarea class="form-control" name="soal" id="soal" placeholder="Soal"></textarea>
            </div>
          </div>
          <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">Pilihan A</label>
            <div class="col-sm-10">
              <textarea class="form-control" name="pila" id="pila" placeholder="Pilihan A"></textarea>
            </div>
          </div>
          <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">Pilihan B</label>
            <div class="col-sm-10">
              <textarea class="form-control" name="pilb" id="pilb" placeholder="Pilihan B"></textarea>
            </div>
          </div>
          <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">Pilihan C</label>
            <div class="col-sm-10">
              <textarea class="form-control" name="pilc" id="pilc" placeholder="Pilihan C"></textarea>
            </div>
          </div>
          <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">Pilihan D</label>
            <div class="col-sm-10">
              <textarea class="form-control" name="pild" id="pild" placeholder="Pilihan D"></textarea>
            </div>
          </div>
          <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">Pilihan E</label>
            <div class="col-sm-10">
              <textarea class="form-control" name="pile" id="pile" placeholder="Pilihan E"></textarea>
            </div>
          </div>
          <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">Kunci</label>
            <div class="col-sm-10">
              <select name="kunci" id="kunci" class="form-control" style="width: 50%;">
                <option value="">-- Pilih Kunci Jawaban --</option>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
                <option value="E">E</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">Score</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="score" id="score" placeholder="Score">
            </div>
          </div>
          <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">Status</label>
            <div class="col-sm-10">
              <select name="status" id="status" class="form-control select2" style="width: 50%;">
                <option value="">-- Pilih Status Soal --</option>
                <option value="Y">Tampil</option>
                <option value="N">Tidak Tampil</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
              <button type="submit" class="btn btn-primary" id="btnsimpansoal">Simpan</button>
              <img src="{{ url('img/ajax-loader.gif') }}" alt="Loading" id="loading"> </div>
          </div>
          <div class="col-sm-offset-2 col-sm-10 alert alert-danger" id="salah"></div>
          <div class="col-sm-offset-2 col-sm-10 alert alert-info" id="benar"><b>Sukses </b>Soal berhasil di buat.</div>
          </form>
        </div>
      </div>
      <hr class="clearfix">
      <table class="table table-responsive table-condensed table-hover table-bordered">
        <caption>
        Daftar soal untuk paket soal <b>{!! $soal->paket !!}</b>
        </caption>
        <thead>
          <tr>
            <th>NO</th>
            <th>Soal</th>
            <th>Kunci</th>
            <th style="text-align:center;">Score</th>
            <th style="text-align:center;">Status</th>
            <th style="text-align:center;">Aksi</th>
          </tr>
        </thead>
        <tbody>
        <?php $no=1; ?>
        @if($detailsoals->count())
        @foreach($detailsoals as $detailsoal)
        <input type="hidden" name="id_soal{{ $detailsoal->id }}" id="id_soal{{ $detailsoal->id }}" value="{{ $detailsoal->id }}">
        <tr>
          <td>{{ $no++ }}</td>
          <td>{!! $detailsoal->soal !!}</td>
          <td align="center">{!! $detailsoal->kunci !!}</td>
          <td align="center">{!! $detailsoal->score !!}</td>
          <td align="center" valign="midle"><?php
            if($detailsoal->status == "Y"){echo "<span style='background:#008000; color:#fff; padding:5px'>Tampil</span>";}else{echo "<span style='background:#cc0000; color:#fff; padding:5px'>Tidak</span>";}
          ?></td>
          <td width="120px" align="center"><a href="{{url('ubah-detail-soal', $detailsoal->id)}}" title="">Ubah</a> | <a href="#" id="hapussoal{{ $detailsoal->id }}" data-toggle="tooltip" title="Data soal yang dihapus tidak bisa dikembalikan.">Hapus</a></td>
        </tr>
        <tr class="alert alert-danger" style="display: none;" id="wrapth{{ $detailsoal->id }}">
          <td colspan="6" id="tampilhapus{{ $detailsoal->id }}"></td>
        </tr>
        <script>
          $(document).ready(function() {
            $("#tampilhapus{{ $detailsoal->id }}").hide();
            $('#hapussoal{{ $detailsoal->id }}').click(function () {
              if (!confirm('Are you sure?')) return false;
                var id_soal = $('#id_soal{{ $detailsoal->id }}').val();
                var datastring = "id_soal="+id_soal;
              $.ajax({
                type: "POST",
                url: "{{ url('hapusdetailsoal') }}",
                data: datastring,
                success: function(data){
                  $("#wrapth{{ $detailsoal->id }}").show();
                  $("#tampilhapus{{ $detailsoal->id }}").html(data).show();
                  location.reload();
                }
              });
              return false;
            });
          });
        </script> 
        @endforeach
        @else
        <tr><td colspan="6" class="alert alert-danger">Belum ada data untuk ditampilkan.</td></tr>
        @endif
        </tbody>
      </table>
    </div>
  </div>
</div>
<!-- <script src="{{ url('lib/bootstrap/js/bootstrap.js') }}"></script> -->
<!-- <script src="{{url('lib/summernote/summernote.js')}}"></script> -->
<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  $(document).ready(function() {

    $("#btsoal").click(function() {
      $("#wrapsoal").toggle();
      return false;
    });

    $("#btkelas").click(function(){
    $("#wrapdistribusisoal").toggle();
    return false;
    });

      $("#loading").hide();
      $("#salah").hide();
      $("#benar").hide();
      $('.collapse').collapse();
      $('#kunci').select2();
      $('#status').select2();

        $("#soal").summernote({ height: 150 });
        $("#pila").summernote({ height: 150 });
        $("#pilb").summernote({ height: 150 });
        $("#pilc").summernote({ height: 150 });
        $("#pild").summernote({ height: 150 });
        $("#pile").summernote({ height: 150 });

    $("#btnsimpansoal").click(function() {
      $(this).hide();
      $("#loading").show();
      var paket = $("#paket").val();
      var soal = encodeURIComponent($("#soal").code());
      var pila = encodeURIComponent($("#pila").code());
      var pilb = encodeURIComponent($("#pilb").code());
      var pilc = encodeURIComponent($("#pilc").code());
      var pild = encodeURIComponent($("#pild").code());
      var pile = encodeURIComponent($("#pile").code());
      var kunci = $("#kunci").val();
      var score = $("#score").val();
      var status = $("#status").val();

      var datastring = "paket="+paket+"&soal="+soal+"&pila="+pila+"&pilb="+pilb+"&pilc="+pilc+"&pild="+pild+"&pile="+pile+"&kunci="+kunci+"&score="+score+"&status="+status;
      $.ajax({
        type: "POST",
        url: "{{ url('/simpanformdetailsoal') }}",
        data: datastring,
        success: function(data){
          if(data == "berhasil"){
            $("#loading").hide();
            $("#salah").hide();
            $("#kunci").val("");
            $("#score").val("");
            $("#status").val("");
            $("#paket").val("");
            $("#soal").code("");
            $("#pila").code("");
            $("#pilb").code("");
            $("#pilc").code("");
            $("#pild").code("");
            $("#pile").code("")
            $("#benar").show();
            $("#btnsimpansoal").show();
            location.reload();
          }else{
            $("#loading").hide();
            $("#benar").hide();
            $("#salah").html(data).show();
            $("#btnsimpansoal").show();
          }
        }
      });
      return false;
    });
    });
</script>
<?php
  }else{
    return redirect('url(siswa)');
  }
?>
@endsection
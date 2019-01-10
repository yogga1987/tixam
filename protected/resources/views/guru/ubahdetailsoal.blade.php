@extends('layouts/guru_baru')
@section('title', 'Detail soal')
@section('content')
<script src="{{ url('/assets/assets/vendor/jquery.min.js') }}"></script>
<?php
	if (Auth::user()->status != "S" or Auth::user()->status != "C") {
?>
<div class="col-md-12 content">
  <ol class="breadcrumb">
    <li><a href="#">Home</a></li>
    <li><a href="{{ url('soal-guru') }}">Soal</a></li>
    <li><a href="{{ url('detail-soal', $soal->id) }}">Detail Paket Soal</a></li>
    <li class="active">{{ $soal->paket }}</li>
  </ol>
  <div class="panel panel-default">
    <div class="panel-heading" style="background: #072047; color: #fff">Ubah Daftar Soal</div>
    <div class="panel-body">
      {!! Form::open(['url' => 'simpansiswa', 'class' => 'form-horizontal']) !!}
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="form-group">
          <label for="inputEmail3" class="col-sm-2 control-label">Soal</label>
          <div class="col-sm-10">
            <input type="hidden" name="paket" id="paket" value="{{ $id_soal }}">
            <input type="hidden" name="id_soal" id="id_soal" value="{{ $detailsoals->id }}">
            <textarea class="form-control" name="soal" id="soal" placeholder="Soal">{!!$detailsoals->soal!!}</textarea>
          </div>
        </div>
        <div class="form-group">
          <label for="inputEmail3" class="col-sm-2 control-label">Pilihan A</label>
          <div class="col-sm-10">
            <textarea class="form-control" name="pila" id="pila" placeholder="Pilihan A">{!!$detailsoals->pila!!}</textarea>
          </div>
        </div>
        <div class="form-group">
          <label for="inputEmail3" class="col-sm-2 control-label">Pilihan B</label>
          <div class="col-sm-10">
            <textarea class="form-control" name="pilb" id="pilb" placeholder="Pilihan B">{!!$detailsoals->pilb!!}</textarea>
          </div>
        </div>
        <div class="form-group">
          <label for="inputEmail3" class="col-sm-2 control-label">Pilihan C</label>
          <div class="col-sm-10">
            <textarea class="form-control" name="pilc" id="pilc" placeholder="Pilihan C">{!!$detailsoals->pilc!!}</textarea>
          </div>
        </div>
        <div class="form-group">
          <label for="inputEmail3" class="col-sm-2 control-label">Pilihan D</label>
          <div class="col-sm-10">
            <textarea class="form-control" name="pild" id="pild" placeholder="Pilihan D">{!!$detailsoals->pild!!}</textarea>
          </div>
        </div>
        <div class="form-group">
          <label for="inputEmail3" class="col-sm-2 control-label">Pilihan E</label>
          <div class="col-sm-10">
            <textarea class="form-control" name="pile" id="pile" placeholder="Pilihan E">{!!$detailsoals->pile!!}</textarea>
          </div>
        </div>
        <div class="form-group">
          <label for="inputEmail3" class="col-sm-2 control-label">Kunci</label>
          <div class="col-sm-10">
            <select name="kunci" id="kunci" class="form-control">
              <option value="{!!$detailsoals->kunci!!}">{!!$detailsoals->kunci!!}</option>
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
            <input type="text" class="form-control" name="score" id="score" placeholder="Score" value="{!!$detailsoals->score!!}">
          </div>
        </div>
        <div class="form-group">
          <label for="inputEmail3" class="col-sm-2 control-label">Status</label>
          <div class="col-sm-10">
            <select name="status" id="status" class="form-control">
              <option value="{!!$detailsoals->status!!}">
              <?php 
                  	if ($detailsoals->status == "Y") {
                  	 	echo "Tampil";
                  	 }else{
                  	 	echo "Tidak Tampil";
                  	 } ?>
              </option>
              <option value="Y">Tampil</option>
              <option value="N">Tidak Tampil</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-primary" id="btnubahsoal">Ubah</button>
            <img src="{{ url('img/ajax-loader.gif') }}" alt="Loading" id="loading"> </div>
        </div>
        <div class="col-sm-offset-2 col-sm-10 alert alert-danger" id="salah"></div>
        <div class="col-sm-offset-2 col-sm-10 alert alert-info" id="benar"><b>Sukses </b>Soal berhasil di ubah.</div>
      </form>
      <hr class="clearfix">
    </div>
  </div>
</div>
<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
	$(document).ready(function() {
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

		$("#btnubahsoal").click(function() {
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
			var id_soal = $("#id_soal").val();

			var datastring = "paket="+paket+"&soal="+soal+"&pila="+pila+"&pilb="+pilb+"&pilc="+pilc+"&pild="+pild+"&pile="+pile+"&kunci="+kunci+"&score="+score+"&status="+status+"&id_soal="+id_soal;
			$.ajax({
			  type: "POST",
			  url: "{{ url('/ubahformdetailsoal') }}",
			  data: datastring,
			  success: function(data){
			    if(data == "berhasil"){
			      $("#loading").hide();
			      $("#salah").hide();
			      $("#benar").show();
			      $("#btnubahsoal").show();
            window.location.href = "{{ url('/detail-soal/'.$detailsoals->id_soal) }}";
			    }else{
			      $("#loading").hide();
			      $("#benar").hide();
			      $("#salah").html(data).show();
			      $("#btnubahsoal").show();
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
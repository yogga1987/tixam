@extends('layouts/guru_baru')
@section('title', 'Input absen')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<link rel="stylesheet" href="{{ url('lib/datepicker/dist/css/bootstrap-datepicker.min.css') }}">
<style type="text/css">
	.list{
		float: left; list-style: none; padding: 10px; margin-left: 10px; background: #c3c7e7;
	}
</style>
<div class="col-md-12 content">
  <ol class="breadcrumb">
    <li><a href="{{url('guru')}}">Home</a></li>
    <li><a href="{{ url('data-absen') }}">Absen</a></li>
    <li class="active">Input absen</li>
  </ol>
  <div class="panel panel-default">
    <div class="panel-heading" style="background: #072047; color: #fff"><i class="fa fa-edit"></i> Input Absen</div>
    <div class="panel-body">
	    @if($kelas->count())
	    	<div class="wellx form-horizontal" style="margin-bottom: 0">
	    		<div class="form-group">
            <label for="id_kelas" class="col-sm-2 control-label">Kelas</label>
            <div class="col-sm-10">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <select class="form-control" id="id_kelas">
              	<option value="">-- Pilih Kelas --</option>
              	@foreach($kelas as $data_kelas)
              		<option value="{{ $data_kelas->id }}">{{ $data_kelas->nama }}</option>
              	@endforeach
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="jam" class="col-sm-2 control-label">Tanggal</label>
            <div class="col-sm-10">
              <input type="text" id="tanggal" value="{{ Date('Y-m-d') }}" class="form-control datepicker">
            </div>
          </div>
          <div class="form-group" style="margin-bottom: 0">
            <label for="jam" class="col-sm-2 control-label">Jam</label>
            <div class="col-sm-10">
              <select class="form-control" id="jam">
              	<option value="">-- Pilih Jam --</option>
              	<option value="1">Jam ke 1</option>
              	<option value="2">Jam ke 2</option>
              	<option value="3">Jam ke 3</option>
              	<option value="4">Jam ke 4</option>
              	<option value="5">Jam ke 5</option>
              	<option value="6">Jam ke 6</option>
              	<option value="7">Jam ke 7</option>
              	<option value="8">Jam ke 8</option>
              	<option value="9">Jam ke 9</option>
              	<option value="10">Jam ke 10</option>
              </select>
            </div>
          </div>
	    	</div>
	    	<div id="wrap-siswa" style="display: none; margin-top: 15px">
	    		
	    	</div>
	    @else
	    	<div class="alert alert-danger">Anda belum menginput data kelas. Klik <a data-toggle="tooltip" href="{{ url('/kelas') }}" title="Klik untuk menginput data kelas" style="color: #9ba6fe;">Disini</a> untuk menginput data kelas</div>
	    @endif
    </div>
	</div>
</div>
<script src="{{ url('/assets/assets/vendor/jquery.min.js') }}"></script>
<script src="{{ url('lib/datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script>
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});
$('.datepicker').datepicker({
  format: 'yyyy-mm-dd'
})
$(document).ready(function() {
	$('#id_kelas').select2();
	$('#jam').select2();

	$("#jam").change(function(){
    var id_kelas = $("#id_kelas").val();
    var jam = $("#jam").val();
    var tanggal = $("#tanggal").val();
    var dataString = 'id_kelas='+id_kelas+'&jam='+jam+'&tanggal='+tanggal;
    $.ajax({
      type: "POST",
      url: "{{ url('/ajax/get_siswa_absen') }}",
      data: dataString,
      success: function(data){
        $("#wrap-siswa").hide().html(data).fadeIn(350);
        console.log('ok');
      }
    })
	});
});
</script>
@endsection
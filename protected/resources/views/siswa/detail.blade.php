@extends('layouts/siswa_baru')
@section('title', 'Detail hasil latihan')
@section('breadcrumb')
  <li><a href="{{ url('/siswa') }}">Home</a></li>
  <li><a href="{{ url('/hasil-siswa')}}">Hasil latihan</a></li>
  <li class="active">Detail hasil latihan</li>
@endsection
@section('content')
<?php
  include(app_path() . '/functions/koneksi.php');
?>
<div class="col-md-12">
  <div class="card">
    <div class="card-header bg-white">
      <div class="media">
        <div class="media-body">
          <h4 class="card-title">Detail jawaban</h4>
          <p class="card-subtitle"></p>
        </div>
      </div>
    </div>
    <div style="padding: 15px">
      <table class="table table-condensed table-hover table-bordered">
        <thead>
          <tr>
            <th class="center">No</th>
            <th class="center">Soal</th>
            <th class="center">Kunci</th>
            <th class="center">Jawab</th>
            <th class="center">Score</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1; ?>
          @if($jawabs->count())
          @foreach($jawabs as $jawab)
          <?php $id_soal = $jawab->id_soal; $id_user = Auth::user()->id; ?>
          <tr>
            <td width="40px">{{ $no++ }}</td>
            <td>{!! $jawab->soal !!}</td>
            <td class="center">{{ $jawab->kunci }}</td>
            <td class="center">{{ $jawab->pilihan }}</td>
            <td class="center">{{ $jawab->score }}</td>
          </tr>
          @endforeach
          @endif
          <tr>
            <td colspan="4" class="right">Total</td>
            <td class="center">
              <?php
                $sql_nilai = $conn->query("SELECT SUM(score) AS total FROM jawabs WHERE id_user = '$id_user' AND id_soal = '$id_soal'");
                while($row_nilai = $sql_nilai->fetch_assoc()){
                  echo "<b>".$row_nilai['total']."<b>";
                }
              ?>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
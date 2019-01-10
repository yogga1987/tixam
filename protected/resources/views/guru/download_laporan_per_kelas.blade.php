<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css" media="screen">
.table td, .table th {
    background-color: #fff;
 }
table {
  border-spacing: 0;
  border-collapse: collapse;
}
.table-bordered th,.table-bordered td {
    border: 1px solid #ddd;
 }
</style>

<table>
	<tbody>
		<tr>
			<td><b>Paket Soal</b></td>
			<td colspan="4">{{ $paket_soal->paket }}</td>
		</tr>
		<tr>
			<td><b>Kelas</b></td>
			<td colspan="4">{{ $jawab->nama_kelas }}</td>
		</tr>
	</tbody>
</table>

<table class="table table-bordered">
	<thead>
		<tr>
			<th style="text-align: center;">NIS</th>
			<th style="text-align: center;">Nama</th>
			<th style="text-align: center;">Jumlah Soal</th>
			<th style="text-align: center;">Jawaban Benar</th>
			<th style="text-align: center;">Nilai</th>
		</tr>
	</thead>
	<tbody>
		@if($jawabs->count())
		@foreach($jawabs as $data)
		<tr>
			<td>{{ $data->user->no_induk }}</td>
			<td>{{ $data->user->nama }}</td>
			<td>{{ $jumlah_soal }}</td>
			<td>
				<?php
					$jumlah_benar = App\Jawab::where('id_kelas', $data->id_kelas)
										              ->where('id_soal', $data->id_soal)
										              ->where('id_user', $data->id_user)
										              ->where('status', 'Y')
										              ->where('score', '!=', 0)
										              ->count();
		      echo $jumlah_benar;
				?>
			</td>
			<td>{{ $data->score }}</td>
		</tr>
		@endforeach
		@endif
	</tbody>
</table>
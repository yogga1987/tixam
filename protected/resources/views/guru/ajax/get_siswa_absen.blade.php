<style type="text/css">
	.align-normal{
		margin-bottom: 15px;
		border: solid thin #333;
	}
</style>
<meta name="csrf-token" content="{{ csrf_token() }}" />
<script src="{{ url('/assets/assets/vendor/jquery.min.js') }}"></script>
<!-- <script src="{{ url('/js/cookie.js') }}"></script> -->

<?php include(app_path().'/functions/koneksi.php') ?>

<table class="table table-striped table-hover">
	<thead>
		<tr>
			<th style="width: 15px; text-align: center">#</th>
			<th>Nama</th>
			<th>NIS</th>
			<th>Kelas</th>
			<th colspan="4" style="text-align: center">Keterangan</th>
		</tr>
	</thead>
	<tbody id="wrap-data">
	@if($siswas->count())
		<?php $no = 1; ?>
		@foreach($siswas as $siswa)
		<?php
			$id_guru = Auth::user()->id;
			
			$q_absen = $conn->query("SELECT absen FROM absens WHERE id_guru='$id_guru' AND id_kelas='$siswa->id_kelas' AND id_siswa='$siswa->id' AND jam='$jam' GROUP BY id_siswa");
			if ($q_absen->num_rows > 0) {
				while ($d_absen = $q_absen->fetch_assoc()) {
					$absen = $d_absen['absen'];
				};
			}else{
				$absen = 'N';
			}
			
		?>
		<!-- <button id="tombol{{ $siswa->id }}" class="btn btn-primary btn-xs">Klik</button>&nbsp; -->
		<tr>
			<td style="text-align: center">{{ $no++ }}</td>
			<td>{{ $siswa->nama }}</td>
			<td>{{ $siswa->no_induk }}</td>
			<td>{{ $siswa->nama_kelas }}</td>
			<td width="60px" style="text-align: center">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" id="id_siswa{{ $siswa->id }}" value="{{ $siswa->id }}">
				<label id="pil_a{{ $siswa->id }}">
          <input  name="absen{{ $siswa->id }}" value="A" id="a{{ $siswa->id }}" type="radio" <?php if($absen == 'A'){echo "checked";} ?>><span> A</span>
        </label>
			</td>
			<td width="60px" style="text-align: center">
				<label id="radio{{ $siswa->id }}i" data-toggle="tooltip" title="Izin">
          <input name="absen{{ $siswa->id }}"  value="I" id="i{{ $siswa->id }}" type="radio" <?php if($absen == 'I'){echo "checked";} ?>><span> I</span>
        </label>
			</td>
			<td width="60px" style="text-align: center">
				<label id="radio{{ $siswa->id }}s" data-toggle="tooltip" title="Sakit">
          <input name="absen{{ $siswa->id }}"  value="S" id="s{{ $siswa->id }}" type="radio" <?php if($absen == 'S'){echo "checked";} ?>><span> S</span>
        </label>
			</td>
			<td width="60px" style="text-align: center">
				<label id="radio{{ $siswa->id }}m" data-toggle="tooltip" title="Masuk">
          <input name="absen{{ $siswa->id }}"  value="M" id="m{{ $siswa->id }}" type="radio" <?php if($absen == 'M'){echo "checked";} ?>><span> M</span>
        </label>
			</td>
		</tr>
		<script>
			$(document).ready(function(){
				$("#tombol{{ $siswa->id }}").click(function(){
					$("#tombol{{ $siswa->id }}").addClass('active')
					alert('haha');
				});

				$("#pil_a{{ $siswa->id }}").click(function(){
					// console.log('hehe');

					var absen = $("#a{{ $siswa->id }}").val();
					var tanggal = $("#tanggal").val();
					var jam = $("#jam").val();
					var id_kelas = $("#id_kelas").val();
					var id_siswa = $("#id_siswa{{ $siswa->id }}").val();
					var dataString = 'absen='+absen+'&jam='+jam+'&id_kelas='+id_kelas+'&id_siswa='+id_siswa+'&tanggal='+tanggal;
					$.ajax({
						type: "POST",
						url: "{{ url('/crud/input-absen') }}",
						data: dataString,
						success: function(data){
							console.log(data);
						}
					});
				});
				$("#radio{{ $siswa->id }}i").click(function(){
					var absen = $("#i{{ $siswa->id }}").val();
					var tanggal = $("#tanggal").val();
					var jam = $("#jam").val();
					var id_kelas = $("#id_kelas").val();
					var id_siswa = $("#id_siswa{{ $siswa->id }}").val();
					var dataString = 'absen='+absen+'&jam='+jam+'&id_kelas='+id_kelas+'&id_siswa='+id_siswa+'&tanggal='+tanggal;
					$.ajax({
						type: "POST",
						url: "{{ url('/crud/input-absen') }}",
						data: dataString,
						success: function(data){
							console.log(data);
						}
					});
				});
				$("#radio{{ $siswa->id }}s").click(function(){
					var absen = $("#s{{ $siswa->id }}").val();
					var tanggal = $("#tanggal").val();
					var jam = $("#jam").val();
					var id_kelas = $("#id_kelas").val();
					var id_siswa = $("#id_siswa{{ $siswa->id }}").val();
					var dataString = 'absen='+absen+'&jam='+jam+'&id_kelas='+id_kelas+'&id_siswa='+id_siswa+'&tanggal='+tanggal;
					$.ajax({
						type: "POST",
						url: "{{ url('/crud/input-absen') }}",
						data: dataString,
						success: function(data){
							console.log(data);
						}
					});
				});
				$("#radio{{ $siswa->id }}m").click(function(){
					var absen = $("#m{{ $siswa->id }}").val();
					var tanggal = $("#tanggal").val();
					var jam = $("#jam").val();
					var id_kelas = $("#id_kelas").val();
					var id_siswa = $("#id_siswa{{ $siswa->id }}").val();
					var dataString = 'absen='+absen+'&jam='+jam+'&id_kelas='+id_kelas+'&id_siswa='+id_siswa+'&tanggal='+tanggal;
					$.ajax({
						type: "POST",
						url: "{{ url('/crud/input-absen') }}",
						data: dataString,
						success: function(data){
							console.log(data);
						}
					});
				});
			});
		</script>
		@endforeach
		@else
		<tr>
			<td colspan="8" class="alert alert-info">Data siswa untuk kelas yang dipilih masih kosong.</td>
		</tr>
		@endif
	</tbody>
</table>
<hr>
<a href="{{ url('/rekap-absen') }}" class="btn btn-primary btn-md pull-right"><i class="fa fa-check-square-o" aria-hidden="true"></i> Selesai</a>
<script>
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});
$(document).ready(function() {
	
});

</script>

<?php include(app_path().'/functions/koneksi.php'); ?>
<table class="table table-striped table-condensed" style="font-size: 11pt">
  <thead>
    <tr>
      <th class="center">#</th>
      <th>Paket Soal</th>
      <th>Deskripsi Soal</th>
      <th class="center">KKM</th>
      <th class="center">Status</th>
      <th>Tanggal</th>
    </tr>
  </thead>
  <tbody>
    <?php $no = 1; ?>
    @if($jawabs->count())
    @foreach($jawabs as $jawab)
    <?php
      if ($jawab->kkm >= $jawab->count) {
        $status = "<span style='color:#009900; font-size: 18px; font-weight: bold; text-align:center'>Lulus</<span>";
      }else{
        $status = "<span style='color:#e60000; font-size: 18px; font-weight: bold; text-align: center;'>Gagal</<span>";
      }
      if($jawab->created_at != "" and $jawab->created_at != "0000-00-00"){
        $tanggal = explode(" ", $jawab->created_at);
        $tanggal = explode("-", $tanggal[0]);
        $tanggal = $tanggal[2].' '.$bulanpendek[$tanggal[1]].' '.$tanggal[0];
      }else{
        $tanggal = 'tidak valid';
      }
    ?>
    <tr>
      <td class="center">{{ $no++ }}</td>
      <td>{{ $jawab->paket }}</td>
      <td>{{ $jawab->deskripsi }}</td>
      <td class="center">{{ $jawab->kkm }}</td>
      <td class="center">{!! $status !!}</td>
      <td>{{ $tanggal }}</td>
    </tr>
    @endforeach
    @endif
  </tbody>
</table>
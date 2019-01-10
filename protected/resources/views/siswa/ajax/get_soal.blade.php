<style type="text/css">
  .benar{
    padding: 15px;
    background: #045ff2;
    color: #fff;
  }
</style>
<?php
  if ($cek_jawaban != "") {
    $pilihan = $cek_jawaban->pilihan;
  }else{
    $pilihan = 'ER';
  }
?>
<table class="table table-condensed" style="padding:0; margin: 0">
  <tbody>
    <tr>
      <input type="hidden" name="id_soaljawab" id="id_soaljawab" value="{{ $detailsoal->id_soal }}">
      <input type="hidden" name="id_soal{{ $detailsoal->id }}" id="id_soal{{ $detailsoal->id }}" value="{{ $detailsoal->id_soal }}">
      <input type="hidden" name="no_soal_id{{ $detailsoal->id }}" id="no_soal_id{{ $detailsoal->id }}" value="{{ $detailsoal->id }}">

      <!-- <td style="width: 15px">1</td> -->
      <td colspan="2">{!! $detailsoal->soal !!}</td>
    </tr>
    <tr id="wrap_pil_a" <?php if ($pilihan == 'A') { echo "class='benar'"; } ?>>
      <!-- <td>&nbsp;</td> -->
      <td style="width: 10px"><input type="radio" name="pilih{{ $detailsoal->id }}" value="A" data-toggle='tooltip' title="Klik untuk menjawab." <?php if ($pilihan == 'A') { echo "checked"; } ?>></td>
      <td>{!! $detailsoal->pila !!} </td>
    </tr>
    <tr id="wrap_pil_b" <?php if ($pilihan == 'B') { echo "class='benar'"; } ?>>
      <!-- <td>&nbsp;</td> -->
      <td><input type="radio" name="pilih{{ $detailsoal->id }}" value="B" data-toggle='tooltip' title="Klik untuk menjawab." <?php if ($pilihan == 'B') { echo "checked"; } ?>></td>
      <td>{!! $detailsoal->pilb !!} </td>
    </tr>
    <tr id="wrap_pil_c" <?php if ($pilihan == 'C') { echo "class='benar'"; } ?>>
      <!-- <td>&nbsp;</td> -->
      <td><input type="radio" name="pilih{{ $detailsoal->id }}" value="C" data-toggle='tooltip' title="Klik untuk menjawab." <?php if ($pilihan == 'C') { echo "checked"; } ?>></td>
      <td>{!! $detailsoal->pilc !!} </td>
    </tr>
    <tr id="wrap_pil_d" <?php if ($pilihan == 'D') { echo "class='benar'"; } ?>>
      <!-- <td>&nbsp;</td> -->
      <td><input type="radio" name="pilih{{ $detailsoal->id }}" value="D" data-toggle='tooltip' title="Klik untuk menjawab." <?php if ($pilihan == 'D') { echo "checked"; } ?>></td>
      <td>{!! $detailsoal->pild !!} </td>
    </tr>
    <tr id="wrap_pil_e" <?php if ($pilihan == 'E') { echo "class='benar'"; } ?>>
      <!-- <td>&nbsp;</td> -->
      <td><input type="radio" name="pilih{{ $detailsoal->id }}" value="E" data-toggle='tooltip' title="Klik untuk menjawab." <?php if ($pilihan == 'E') { echo "checked"; } ?>></td>
      <td>{!! $detailsoal->pile !!} </td>
    </tr>

    <script>
      $(document).ready(function(){
        $("input[name=pilih{{ $detailsoal->id }}]").click(function(){
          var pilihan = $("input[name=pilih{{ $detailsoal->id }}]:checked").val();
          var id_soal = $("#id_soal{{ $detailsoal->id }}").val();
          var no_soal_id = $("#no_soal_id{{ $detailsoal->id }}").val();
          var id_user = $("#id_user{{ $detailsoal->id }}").val();
          var datastring = "pilihan="+pilihan+"&id_soal="+id_soal+"&no_soal_id="+no_soal_id+"&id_user="+id_user;
          $.ajax({
            type: "POST",
            url: "{!! url('simpanjawabankliksiswa') !!}",
            data: datastring,
            success: function(data){
              if (data == 'A') {
                $("#wrap_pil_b").removeClass('benar');
                $("#wrap_pil_c").removeClass('benar');
                $("#wrap_pil_d").removeClass('benar');
                $("#wrap_pil_e").removeClass('benar');
                $("#wrap_pil_a").addClass('benar');
              }else if(data == 'B'){
                $("#wrap_pil_a").removeClass('benar');
                $("#wrap_pil_c").removeClass('benar');
                $("#wrap_pil_d").removeClass('benar');
                $("#wrap_pil_e").removeClass('benar');
                $("#wrap_pil_b").addClass('benar');
              }else if(data == 'C'){
                $("#wrap_pil_b").removeClass('benar');
                $("#wrap_pil_a").removeClass('benar');
                $("#wrap_pil_d").removeClass('benar');
                $("#wrap_pil_e").removeClass('benar');
                $("#wrap_pil_c").addClass('benar');
              }else if(data == 'D'){
                $("#wrap_pil_b").removeClass('benar');
                $("#wrap_pil_c").removeClass('benar');
                $("#wrap_pil_a").removeClass('benar');
                $("#wrap_pil_e").removeClass('benar');
                $("#wrap_pil_d").addClass('benar');
              }else if(data == 'E'){
                $("#wrap_pil_b").removeClass('benar');
                $("#wrap_pil_c").removeClass('benar');
                $("#wrap_pil_d").removeClass('benar');
                $("#wrap_pil_a").removeClass('benar');
                $("#wrap_pil_e").addClass('benar');
              }
              $("#get-soal{{ $detailsoal->id }}").removeClass('page gradient').addClass('page active');
            }
          })
        });
      });
    </script>
  </tbody>
</table>
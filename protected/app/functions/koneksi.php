<?php
	$hostdb = 'localhost';
    $userdb = 'root';
    $passdb = '123456';
    $namedb = 'ujian';

    $conn = new mysqli($hostdb, $userdb, $passdb, $namedb);
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}
	$conn->set_charset("utf8");

    $bulan["01"] = "Januari";
	$bulan["02"] = "Februari";
	$bulan["03"] = "Maret";
	$bulan["04"] = "April";
	$bulan["05"] = "Mei";
	$bulan["06"] = "Juni";
	$bulan["07"] = "Juli";
	$bulan["08"] = "Agustus";
	$bulan["09"] = "September";
	$bulan["10"] = "Oktober";
	$bulan["11"] = "Nopember";
	$bulan["12"] = "Desember";

	$bulanpendek["01"] = "Jan";
	$bulanpendek["02"] = "Feb";
	$bulanpendek["03"] = "Mar";
	$bulanpendek["04"] = "Apr";
	$bulanpendek["05"] = "Mei";
	$bulanpendek["06"] = "Jun";
	$bulanpendek["07"] = "Jul";
	$bulanpendek["08"] = "Ags";
	$bulanpendek["09"] = "Sep";
	$bulanpendek["10"] = "Okt";
	$bulanpendek["11"] = "Nov";
	$bulanpendek["12"] = "Des";

	/*function jenisKelamin($jk){
		if($jk != ""){
			if($jk == 'L'){
				$jenis_kelamin = "Laki-laki";
			}elseif ($jk == 'P') {
				$jenis_kelamin = "Perempuan";
			}else{
				$jenis_kelamin = "Tidak valid";
			}
		}else{
			$jenis_kelamin = "Tidak valid";
		}
		return $jenis_kelamin;
	}*/


	/*function rupiah($rp){
		$format="";
		$p=strlen($rp);
		while($p>3){
			$format=".".substr($rp,-3).$format;
			$k=strlen($rp)-3;
			$rp=substr($rp,0,$k);
			$p=strlen($rp);
		}
		$format= $rp.$format."";
		return $format;
	}*/

?>
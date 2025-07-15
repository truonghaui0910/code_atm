<?php

function labelgridGetPresignedPut($filePath, $contentType) {
    $curl = curl_init();
//    $input = '{"filePath":"xxtest1.jpg","contentType":"image/jpeg","operation":"putObject"}';
    $input = '{"filePath":"' . $filePath . '","contentType":"' . $contentType . '","operation":"putObject"}';
//    error_log("labelgridArtist " . $putObject);
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.labelgrid.com/api/v3/getPresignedPut',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $input,
        CURLOPT_HTTPHEADER => array(
            'authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI1MTAyMDNiYy03MDk4LTQ4NDgtYWEyMC01YjBhYTg5Y2EyMDAiLCJqdGkiOiI3YTVmMWI2ZTg3YzY0MmQ5NWE2MWU3YjFiZTA2ZGEzM2NmNjI0N2JjMTYwNmY4ZGYyMTJhODRmOTlkNjQxMDcxODVkZGM4NGUxYTJmZTMyMSIsImlhdCI6MTY1NDE1NjA5NS4zMzA4OTQ5NDcwNTIwMDE5NTMxMjUsIm5iZiI6MTY1NDE1NjA5NS4zMzA5MDA5MDc1MTY0Nzk0OTIxODc1LCJleHAiOjE5Njk3NzUyOTUuMjUyMjk5MDcwMzU4Mjc2MzY3MTg3NSwic3ViIjoiMTQzIiwic2NvcGVzIjpbInVzZXIudmlldy1jYXRhbG9nIiwidXNlci5nYXRlLXVzZSJdfQ.F8WBZmWETwEmSFumMKbFpCHtJReUjmsk6HQPBX-QWzVmTXHL6W3HbnXG-flaVWT044aRoYJ3wzZgH1abUshFVCKK4cID8fC5o69A1626GkIp2Iy8OTHvasHswAR-NFensKGIMEOYxD-NGs7rxZqwXeNX_LINlHXEu5aWPoKWSvznUP9VrEKB0ehCo50kwW9QbrAW_r7UzNynOaE29KQ493H4wRPvF2nztoykQjb3E-jqVkZxsNEuLNaEfwV1PpRVClF66PWRhg1Drd42WjWlcPAm6fVrrNiXF9rwRGepKZDNAORjOqGQt3VPeT5SzglJjpt-CkgNAFpsTzAp2cjNQa_g6gYVdYm5eO6ypj1RXZ3-R6jDV2EuTCww8HPGILc_jkE9XOP5eXIQXZyWPLCh4znNbk2pusan_m-jEq4zvyX9uJ-Cd9VNEjdj7AiHnJ7q1nWWHlrbgTfgpNPK0bE4K1rxUvANTKFCs1FRpsvSdMewNeYDB0yGcDeN7CNtnLVbmNNmrXvH_0ONF8H1KZmjTP2VGfiglIMNHpmukkSukkE43AcLSJSsIf5jFCLRWFx_x-3Fbj4127zOK8aqgX_Ya3lb_ellKlsZpXz3Vx5-S_fDKKNCAynBDI1MDutvV9-GkM9yuw4hKaiZ56fJshUJKYC4vO2IstLMOlJyBbRpn2U',
            'content-type: application/json'
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}

function labelgridFetchS3File($data) {
    $curl = curl_init();
//    $input = '{"releaseId":'.$data->releaseId.',"filename":"","status":"complete","percentage":0,"bucket":"tmp-lgdz","objectKey":"'.$data->objectKey.'","context":{"field":"frontCover","recordId":'.$data->releaseId.',"storeName":"Releases"},"$isDirty":true,"$isNew":false}';
    $input = '{"trackId":57245,"filename":"too-young-take45-dirty-good.wav","status":"complete","percentage":0,"bucket":"tmp-lgdz","objectKey":"' . $data->objectKey . '","context":{"field":"fileWAV","recordId":57245,"storeName":"Tracks"},"$isDirty":true,"$isNew":false}';
    error_log("labelgridArtist " . $input);
    curl_setopt_array($curl, array(
//        CURLOPT_URL => 'https://api.labelgrid.com/api/v3/releases/'.$data->releaseId.'/fetchS3File',
        CURLOPT_URL => ' https://api.labelgrid.com/api/v3/tracks/57245/fetchS3File',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $input,
        CURLOPT_HTTPHEADER => array(
            'authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI1MTAyMDNiYy03MDk4LTQ4NDgtYWEyMC01YjBhYTg5Y2EyMDAiLCJqdGkiOiI3YTVmMWI2ZTg3YzY0MmQ5NWE2MWU3YjFiZTA2ZGEzM2NmNjI0N2JjMTYwNmY4ZGYyMTJhODRmOTlkNjQxMDcxODVkZGM4NGUxYTJmZTMyMSIsImlhdCI6MTY1NDE1NjA5NS4zMzA4OTQ5NDcwNTIwMDE5NTMxMjUsIm5iZiI6MTY1NDE1NjA5NS4zMzA5MDA5MDc1MTY0Nzk0OTIxODc1LCJleHAiOjE5Njk3NzUyOTUuMjUyMjk5MDcwMzU4Mjc2MzY3MTg3NSwic3ViIjoiMTQzIiwic2NvcGVzIjpbInVzZXIudmlldy1jYXRhbG9nIiwidXNlci5nYXRlLXVzZSJdfQ.F8WBZmWETwEmSFumMKbFpCHtJReUjmsk6HQPBX-QWzVmTXHL6W3HbnXG-flaVWT044aRoYJ3wzZgH1abUshFVCKK4cID8fC5o69A1626GkIp2Iy8OTHvasHswAR-NFensKGIMEOYxD-NGs7rxZqwXeNX_LINlHXEu5aWPoKWSvznUP9VrEKB0ehCo50kwW9QbrAW_r7UzNynOaE29KQ493H4wRPvF2nztoykQjb3E-jqVkZxsNEuLNaEfwV1PpRVClF66PWRhg1Drd42WjWlcPAm6fVrrNiXF9rwRGepKZDNAORjOqGQt3VPeT5SzglJjpt-CkgNAFpsTzAp2cjNQa_g6gYVdYm5eO6ypj1RXZ3-R6jDV2EuTCww8HPGILc_jkE9XOP5eXIQXZyWPLCh4znNbk2pusan_m-jEq4zvyX9uJ-Cd9VNEjdj7AiHnJ7q1nWWHlrbgTfgpNPK0bE4K1rxUvANTKFCs1FRpsvSdMewNeYDB0yGcDeN7CNtnLVbmNNmrXvH_0ONF8H1KZmjTP2VGfiglIMNHpmukkSukkE43AcLSJSsIf5jFCLRWFx_x-3Fbj4127zOK8aqgX_Ya3lb_ellKlsZpXz3Vx5-S_fDKKNCAynBDI1MDutvV9-GkM9yuw4hKaiZ56fJshUJKYC4vO2IstLMOlJyBbRpn2U',
            'content-type: application/json'
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}

function labelgridPutFile($url, $path, $contentType) {
    $curl = curl_init();
//        $input = '{"filePath":"xxtest1.jpg","contentType":"image/jpeg","operation":"putObject"}';
//        error_log("labelgridArtist " . $input);
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_POSTFIELDS => file_get_contents($path),
        CURLOPT_HTTPHEADER => array(
            "Content-Type: $contentType"
        ),
    ));

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    return $httpCode;
}

//     $x = labelgridGetPresignedPut([]);
//     echo $x;
//     $y = labelgridPutFile();
//     echo $y;
//$z = labelgridFetchS3File([]);
//echo $z;
//$x1 = labelgridGetPresignedPut("too-young-take45-dirty-good.wav", "audio/wav");
$x1 = '{"url":"https:\/\/tmp-lgdz.s3-accelerate.amazonaws.com\/1655719986137_too_young_take45_dirty_good.wav?Content-Type=audio%2Fwav&X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=ASIAUPPIDHQGQTEKA4YX%2F20220620%2Fus-west-2%2Fs3%2Faws4_request&X-Amz-Date=20220620T101306Z&X-Amz-Expires=36000&X-Amz-Security-Token=IQoJb3JpZ2luX2VjEBIaCXVzLXdlc3QtMiJHMEUCICU6xRrkMsTk4qzpg1J7sIIpjBMa%2Bdhyna0VfpsOzv11AiEAuAaZLFDTcK1sK71YeyGVLd5q3EYtgo8K5lJsOQrl6x0qkQIIKxADGgwzMDgxMTM3ODM4MjEiDJ%2BnJBcg9F9c342K0yruAeZ2%2Bom1gpFASog%2FKSpvhxH6swHd3Gq1OhCKdyiOpO0vS0QuBc1G9yEHkmxe38T0MZlMYZ1FB1Mw1cc%2BUIeGWkFjfNMI7lkgFeIyNNdsz04ANpEWekNhikYWgyu%2FP2TIG8q%2FrdWlHtlTn49svZdNkjLNV3JuRvcfqlg1JmcZKQa%2F0z3KHJ6octW%2BsoP1Kd8SuIZVsYwky6FlEcuflsIoUeEImzHYB9%2BAOGYUg8dZlD6npl2BcSvt5WiVveBxIehnO1KZTPk%2FIf5%2Bcek5vNMrU%2FgoEgMkeQy1BP5oC9%2F8BsyKYlW3zBySuJ854L4P5l8wsZDBlQY6mgF5kskntKf4VZxNNlM2I1x6IS4g7ZPSMBnnwH2quhpHPD7OiipbHiqLZMZXzKnFK%2Fkh5ZfzU4V%2FjlScRjDfZuKiem12Q%2FOOR3lynDGsMhnAmEleg9YTxnZTUgpUOfUzcTnjiFWuJB6CWpERK3CEwSwO%2BdpRSr8HvR%2BilqwjIEtocki1dWfZObiD%2FLtN4OMi5LZd0HJUH67%2FTKq%2B&X-Amz-Signature=99ad5d97c80fd7a8cf1d24256f247e59219b36dd1b5177f2997eef8f038f2b61&X-Amz-SignedHeaders=host%3Bx-amz-tagging&x-amz-tagging=userId%3D0%26test%3D1","params":{"Bucket":"tmp-lgdz","Key":"1655719986137_too_young_take45_dirty_good.wav","Tagging":"userId=0&test=1","Expires":36000,"ContentType":"audio\/wav"}}';
echo $x1;
$responsePut = json_decode($x1);
$y1 = labelgridPutFile($responsePut->url, "D:/too-young-take45-dirty-good.wav", $responsePut->params->ContentType);
echo $y1;
//$data = (object)[
//    "releaseId"=>"20762",
//    "objectKey"=>$responsePut->params->Key
//];
//$z1 = labelgridFetchS3File($data);

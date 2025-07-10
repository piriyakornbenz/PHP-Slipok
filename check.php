<?php

function responseJson($status, $msg)
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => $status, 'msg' => $msg]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $slip = $_FILES['slip'] ?? null;

    if (!$slip || $slip['error'] !== UPLOAD_ERR_OK) {
        responseJson("error", "กรุณาอัปโหลดสลิป");
    }

    $allowed = ['image/png', 'image/jpeg'];
    if (!in_array($slip['type'], $allowed)) {
        responseJson("error", "อนุญาตเฉพาะไฟล์ PNG และ JPG เท่านั้น");
    }

    $ext = pathinfo($slip['name'], PATHINFO_EXTENSION);
    $newFileName = uniqid('slip_', true) . '.' . $ext;
    $uploadPath = __DIR__ . "/slips/" . $newFileName;

    move_uploaded_file($slip['tmp_name'], $uploadPath);

    // *** config api ***
    $apiKey = '';
    $url = 'https://api.slipok.com/api/line/apikey';

    $fields = [
        'files' => new CURLFile($uploadPath),
        'log' => true,
    ];
    $headers = [
        'x-authorization: ' . $apiKey
    ];

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $fields,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);

    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    // if (isset($uploadPath)) unlink($uploadPath);

    if ($response) {
        $json_response = json_decode($response);

        if ($http_code === 200 && $json_response->{'success'} === true) {
            if ($json_response->data->amount > 0) {
                responseJson("success", "สลิปถูกต้อง จำนวนเงิน: " . $json_response->data->amount . " บาท");
            } else {
                responseJson("error", "ไม่พบยอดเงินในสลิป");
            }
        } else {
            responseJson("error", $json_response->{'message'} ?? 'เกิดข้อผิดพลาดจาก API');
        }
    } else {
        responseJson("error", "เกิดข้อผิดพลาดไม่ทราบสาเหตุ กรุณาลองใหม่อีกครั้ง");
    }
}
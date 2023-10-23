<?php
    $ip = !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
    $hashed_ip = hash('sha256', $ip);
    $today = date('d-m-Y');
    $yesterday = date('d-m-Y', strtotime('-1 day'));
    $hash_db = 'hash_db.json';
    $count_db = 'count_db.json';

    $decodedHashJson = file_exists($hash_db) ? json_decode(file_get_contents($hash_db), true) : [];
    $decodedCountJson = file_exists($count_db) ? json_decode(file_get_contents($count_db), true) : [];

    if (!isset($decodedHashJson[$today])) {
        $decodedHashJson[$today] = [];
    }

    if (!in_array($hashed_ip, $decodedHashJson[$today])) {
        $decodedHashJson[$today][] = $hashed_ip;
        $decodedCountJson[$today] = isset($decodedCountJson[$today]) ? $decodedCountJson[$today] + 1 : 1;
        $decodedCountJson["total"] = isset($decodedCountJson["total"]) ? $decodedCountJson["total"] + 1 : 1;
    }

    file_put_contents($hash_db, json_encode($decodedHashJson, JSON_PRETTY_PRINT));
    file_put_contents($count_db, json_encode($decodedCountJson, JSON_PRETTY_PRINT));

    $max_visitors = 0;
    $max_day = "";
    foreach($decodedCountJson as $day => $visitors) {
      if($day == "total") continue;
      if($visitors > $max_visitors) {
        $max_visitors = $visitors;
        $max_day = $day;
      }
    }
?>

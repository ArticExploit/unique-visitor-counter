<?php
    $ip = $_SERVER['REMOTE_ADDR'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($ips[0]);
    }
    
    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        die("Invalid IP address");
    }

    $hashed_ip = hash('sha256', $ip);
    
    $today = date('d-m-Y');
    $yesterday = date('d-m-Y', strtotime('-1 day'));

    $hash_db = 'hash_db.json';
    $count_db = 'count_db.json';

    $lock_file = fopen("lockfile.txt", "c");

    if(flock($lock_file, LOCK_EX)) {
        $decodedHashJson = file_exists($hash_db) ? json_decode(file_get_contents($hash_db), true) : [];
        $decodedCountJson = file_exists($count_db) ? json_decode(file_get_contents($count_db), true) : [];

        if ($decodedHashJson === null || $decodedCountJson === null) {
            die("Error decoding JSON");
        }

        if (!isset($decodedHashJson[$today])) {
            $decodedHashJson[$today] = [];
        }

        if (!in_array($hashed_ip, $decodedHashJson[$today])) {
            $decodedHashJson[$today][] = $hashed_ip;
            $decodedCountJson[$today] = isset($decodedCountJson[$today]) ? $decodedCountJson[$today] + 1 : 1;
        }

        if (file_put_contents($hash_db, json_encode($decodedHashJson, JSON_PRETTY_PRINT)) === false) {
            die("Error writing to hash_db.json");
        }
        if (file_put_contents($count_db, json_encode($decodedCountJson, JSON_PRETTY_PRINT)) === false) {
            die("Error writing to count_db.json");
        }

        $max_visitors = 0;
        $max_day = "";
        foreach($decodedCountJson as $day => $visitors) {
            if($day == "total") continue;
            if($visitors > $max_visitors) {
                $max_visitors = $visitors;
                $max_day = $day;
            }
        }
        
        flock($lock_file, LOCK_UN); 
    }

    fclose($lock_file);
?>

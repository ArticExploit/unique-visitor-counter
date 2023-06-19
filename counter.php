<?php
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    $hashed_ip = hash('sha256', $ip);
    $today = date('d-m-Y');
    $yesterday = date('d-m-Y',strtotime("-1 days"));
    $hash_db = 'hash_db.json';
    $count_db = 'count_db.json';

    // Decode the JSON content into a PHP array
    $decodedHashJson = json_decode(file_get_contents($hash_db), true);
    $decodedCountJson = json_decode(file_get_contents($count_db), true);

    // Check if today's hash array is present
    $hashArrayFound = false;
    if (isset($decodedHashJson[$today]) && is_array($decodedHashJson[$today])) {
        $hashArrayFound = true;
    }

    // Check if the total count array is present
    $countArrayFound = false;
    if (isset($decodedCountJson["total"])) {
        $countArrayFound = true;
    }

    // Add the total count array
    if ($countArrayFound) {}
        else {
            $decodedCountJson["total"] = 1;
            file_put_contents($count_db, json_encode($decodedCountJson, JSON_PRETTY_PRINT));
        }
    

    if ($hashArrayFound) {

        // Check if hashed ip is present in today's array
        if (array_key_exists($today, $decodedHashJson )) {
            $specificArray = $decodedHashJson[$today];
            if (in_array($hashed_ip, $specificArray)) {
            } else {

                // Adding hash to array and adding +1 to the counters
                $decodedHashJson[$today][] = $hashed_ip;
                $jsonData = json_encode($decodedHashJson, JSON_PRETTY_PRINT);
                file_put_contents($hash_db, $jsonData);
                if (isset($decodedCountJson[$today])) {
                    $decodedCountJson[$today] += 1;
                }
                if (isset($decodedCountJson["total"])) {
                    $decodedCountJson["total"] += 1;
                }
                file_put_contents($count_db, json_encode($decodedCountJson, JSON_PRETTY_PRINT));
            }
        }
    } else {

        // Create arrays, add ip hash, setting the today's counter to 1 and adding +1 to the total counter
        $decodedHashJson[$today] = [$hashed_ip];
        file_put_contents($hash_db, json_encode($decodedHashJson, JSON_PRETTY_PRINT));
        $decodedCountJson[$today] = 1;
        if (isset($decodedCountJson["total"])) {
            $decodedCountJson["total"] += 1;
        }
        file_put_contents($count_db, json_encode($decodedCountJson, JSON_PRETTY_PRINT));
    }

    // Find the day with most visitors
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
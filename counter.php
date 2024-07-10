<?php
    // Get the IP address of the client
    $ip = !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
    
    // Hash the IP address
    $hashed_ip = hash('sha256', $ip);
    
    // Get the current date and the date of yesterday
    $today = date('d-m-Y');
    $yesterday = date('d-m-Y', strtotime('-1 day'));

    // Define the file names
    $hash_db = 'hash_db.json';
    $count_db = 'count_db.json';

    // Open a file for locking
    $lock_file = fopen("lockfile.txt", "w+");

    // Attempt to acquire an exclusive lock
    if(flock($lock_file, LOCK_EX)) {
        // Read and decode the contents of the files
        $decodedHashJson = file_exists($hash_db) ? json_decode(file_get_contents($hash_db), true) : [];
        $decodedCountJson = file_exists($count_db) ? json_decode(file_get_contents($count_db), true) : [];

        // Initialize the array for today if it doesn't exist
        if (!isset($decodedHashJson[$today])) {
            $decodedHashJson[$today] = [];
        }

        // If the hashed IP is not in the array for today, add it and increment the count
        if (!in_array($hashed_ip, $decodedHashJson[$today])) {
            $decodedHashJson[$today][] = $hashed_ip;
            $decodedCountJson[$today] = isset($decodedCountJson[$today]) ? $decodedCountJson[$today] + 1 : 1;
            $decodedCountJson["total"] = isset($decodedCountJson["total"]) ? $decodedCountJson["total"] + 1 : 1;
        }

        // Write the contents back to the files
        file_put_contents($hash_db, json_encode($decodedHashJson, JSON_PRETTY_PRINT));
        file_put_contents($count_db, json_encode($decodedCountJson, JSON_PRETTY_PRINT));

        // Determine the day with the maximum number of visitors
        $max_visitors = 0;
        $max_day = "";
        foreach($decodedCountJson as $day => $visitors) {
            if($day == "total") continue;
            if($visitors > $max_visitors) {
                $max_visitors = $visitors;
                $max_day = $day;
            }
        }
        
        // Release the lock
        flock($lock_file, LOCK_UN); 
    }

    // Close the lock file
    fclose($lock_file);
?>

## Unique Daily Visitor Counter
A simple but complete visitor counter to track unique daily visitors on your site!  
No cookies are used, instead it gets the client's ip, hashes it for extra privacy and uses that to determine if a new visit should be added to the counter.  
The total counter is the sum of all the visits received by unique visitors on a daily basis, not the "true" and total amount of unique visitors.

## Demo
Working demo available [here](https://articexploit.xyz/), on my site.

## Installation
- Install php
- Clone/Download the repo
- Place the files in the directory of your webserver where you want the counter to be
- Make sure the "_db.json" files and "lockfile.txt" are writable and readable by your webserver

## Implementation into your website
The Counter works as is. Only thing you need to figure out on your own is some html and css to make it look pretty.
You can also add the `<?php include 'counter.php'; ?>` snippet in every page of your website, this way you will catch the traffic from all of them and not just the main one.

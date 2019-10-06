<?php

 $file_path = __DIR__ . "/config.php";
                    $myfile = fopen($file_path, "r") or exit("Unable to open file!");
                    $content = fread($myfile, filesize($file_path));
                    echo nl2br(htmlentities($content));
                    fclose($myfile);
                    exit("</pre>");


?>
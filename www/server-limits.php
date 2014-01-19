<?php

echo phpinfo();

echo "<h1>Allowed limits</h1>";

echo "upload_max_filesize: " . ini_get("upload_max_filesize");
echo "<br/>";

echo "post_max_size: " . ini_get("post_max_size");
echo "<br/>";

echo "max_execution_time: " . ini_get("max_execution_time");
echo "<br/>";

echo "max_input_time: " . ini_get("max_input_time");
echo "<br/>";

echo "memory_limit: " . ini_get("memory_limit");
echo "<br/>";



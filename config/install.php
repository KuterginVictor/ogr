<?php

require_once __DIR__."/../database.php";

try {
    $db = DB::getInstance();

    $sql = file_get_contents(__DIR__."/database.sql");
    $result = $db->exec($sql);
    if ($result !== false) {
        echo "Tables created successfully!";
    } else {
        throw new PDOException($db->errorInfo()[2]);
    }

} catch (PDOException $e) {
    echo $e->getMessage();
}
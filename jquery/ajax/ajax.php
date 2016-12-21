<?php
header("content-type:application/json");
sleep(10);
echo json_encode($_POST);
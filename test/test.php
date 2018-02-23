<?php
require_once dirname(__DIR__) . "/src/DataCenterRpc.php";

$instance = DataCenterRpc::instance();

$result = $instance->getUserList(['user_id'=>1,'page'=>1]);
var_dump($result);
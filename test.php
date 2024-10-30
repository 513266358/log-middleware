<?php
use Requiem\LogMiddleware\LogClass;
$logClass   = new LogClass("system_log");
$logClass->output("123","info");
$logClass->info("debug");

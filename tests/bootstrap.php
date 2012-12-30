<?php
session_start();
set_include_path(dirname(__FILE__) . '/../' . PATH_SEPARATOR . get_include_path());
date_default_timezone_set('Europe/Warsaw');

require 'vendor/autoload.php';
require 'tests/Strong/Provider/ProviderTesting.php';
require 'tests/Mock/PDOMock.php';
require 'tests/Mock/StmtMock.php';
require 'tests/Mock/ProviderMock.php';
require 'tests/Mock/ProviderAbstractMock.php';
require 'tests/Mock/ProviderInvalid.php';
require 'tests/Mock/UserActiverecordMock.php';
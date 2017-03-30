<?php

//define("MODULE_ROUTE", "$_SERVER[DOCUMENT_ROOT]/" );
define("MODULE_ROUTE", "$_SERVER[DOCUMENT_ROOT]/backend-modules/" );
define("CONFIGURATIONS_ROUTE", MODULE_ROUTE . "Configurations/" );
echo MODULE_ROUTE;

require_once( CONFIGURATIONS_ROUTE . "debugging.php" );
require_once( MODULE_ROUTE . "Loaders/LOADMODULE_Functionals.php" );
require_once( MODULE_ROUTE . "Loaders/LOADMODULE_SQL.php" );
require_once( MODULE_ROUTE . "Loaders/LOADMODULE_Accounts.php" );
require_once( MODULE_ROUTE . "Loaders/LOADMODULE_Email.php" );

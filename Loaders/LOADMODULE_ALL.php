<?php

//define("MODULE_ROUTE", "$_SERVER[DOCUMENT_ROOT]/" ); // Server
define("MODULE_ROUTE", "$_SERVER[DOCUMENT_ROOT]/backend-modules/" ); // Local
define("CONFIGURATIONS_ROUTE", MODULE_ROUTE . "Configurations/" );

require_once( CONFIGURATIONS_ROUTE . "debugging.php" );
require_once( MODULE_ROUTE . "Loaders/LOADMODULE_Functionals.php" );
require_once( MODULE_ROUTE . "Loaders/LOADMODULE_SQL.php" );
require_once( MODULE_ROUTE . "Loaders/LOADMODULE_Accounts.php" );
require_once( MODULE_ROUTE . "Loaders/LOADMODULE_Email.php" );

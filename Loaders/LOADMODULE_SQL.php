<?php
define("MODULE_ROUTE_SQL", MODULE_ROUTE . "Module-SQL/" );

require_once( MODULE_ROUTE_SQL . "SQLConnector.php");
require_once( MODULE_ROUTE_SQL . "SQLBasicTableManager.php");
require_once( MODULE_ROUTE_SQL . "SQLBasicSelector.php");
require_once( MODULE_ROUTE_SQL . "SQLWhereObject.php");

require_once( MODULE_ROUTE_SQL . "SQLSummarySelector.php");
require_once( MODULE_ROUTE_SQL . "SQLInsert.php");
require_once( MODULE_ROUTE_SQL . "SQLUpdate.php");
require_once( MODULE_ROUTE_SQL . "SQLDelete.php");

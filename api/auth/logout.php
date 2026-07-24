<?php

require_once __DIR__ . "/../../auth/session.php";

logout();

header("Location: /cinema-booking/index.php");
exit;
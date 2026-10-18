<?php
require_once __DIR__ . '/../includes/auth.php';
// Public admin registration is disabled for security.
// The single admin account is provisioned via schema.sql.
header("Location: login.php?msg=" . urlencode("Public admin registration is disabled."));
exit;
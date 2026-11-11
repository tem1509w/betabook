<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../config/init.php';

$db = db();
$connection = $db->getConnection();
$eligible = "((order_status = 'completed' AND payment_status = 'paid')
    OR payment_status = 'refunded'
    OR order_status = 'cancelled')";
$batchSize = 500;
$deleted = 0;

try {
    $connection->beginTransaction();
    $db->run(
        "UPDATE orders
         SET retention_started_at = updated_at
         WHERE retention_started_at IS NULL AND $eligible"
    );
    $db->run(
        "UPDATE orders
         SET retention_started_at = NULL
         WHERE retention_started_at IS NOT NULL AND NOT $eligible"
    );
    $connection->commit();

    $statement = $connection->prepare(
        "DELETE FROM orders
         WHERE retention_started_at <= DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 30 DAY)
           AND $eligible
         LIMIT $batchSize"
    );

    do {
        $connection->beginTransaction();
        $statement->execute();
        $batchDeleted = $statement->rowCount();
        $connection->commit();
        $deleted += $batchDeleted;
    } while ($batchDeleted === $batchSize);

    fwrite(STDOUT, sprintf(
        "[%s] Deleted %d eligible order(s).\n",
        date('Y-m-d H:i:s'),
        $deleted
    ));
} catch (Throwable $error) {
    if ($connection->inTransaction()) {
        $connection->rollBack();
    }
    fwrite(STDERR, 'Order cleanup failed: ' . $error->getMessage() . PHP_EOL);
    exit(1);
}

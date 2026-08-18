<?php

if (!function_exists('getSavingCurrentAmount')) {
    function getSavingCurrentAmount(PDO $pdo, int $savingId, int $userId): ?float
    {
        $stmt = $pdo->prepare("
            SELECT COALESCE(SUM(CASE WHEN st.type = 'deposit' THEN st.amount ELSE -st.amount END), 0) AS current_amount
            FROM savings s
            LEFT JOIN saving_transactions st ON st.saving_id = s.id
            WHERE s.id = :saving_id AND s.user_id = :user_id
            GROUP BY s.id
        ");
        $stmt->execute([
            ':saving_id' => $savingId,
            ':user_id' => $userId,
        ]);
        $amount = $stmt->fetchColumn();
        if ($amount === false) {
            return null;
        }
        return (float) $amount;
    }
}

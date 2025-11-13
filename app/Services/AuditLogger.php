<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Audit Logger Service
 *
 * Provides centralized audit logging for sensitive operations including:
 * - User authentication events
 * - Data modifications
 * - Permission changes
 * - Security-related actions
 * - File access
 *
 * @package App\Services
 */
class AuditLogger
{
    /**
     * Log an authentication event
     *
     * @param string $event Event type (login, logout, failed_login, etc.)
     * @param int|null $userId User ID if available
     * @param array<string, mixed> $metadata Additional metadata
     * @return AuditLog
     */
    public function logAuth(string $event, ?int $userId = null, array $metadata = []): AuditLog
    {
        return $this->log(
            action: $event,
            auditable_type: 'App\\Models\\User',
            auditable_id: $userId,
            metadata: array_merge($metadata, [
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]),
            severity: $this->getSeverityForAuthEvent($event)
        );
    }

    /**
     * Log a data modification event
     *
     * @param string $action Action performed (created, updated, deleted)
     * @param string $auditableType Model class name
     * @param int $auditableId Model ID
     * @param array<string, mixed> $oldValues Old values before change
     * @param array<string, mixed> $newValues New values after change
     * @return AuditLog
     */
    public function logDataChange(
        string $action,
        string $auditableType,
        int $auditableId,
        array $oldValues = [],
        array $newValues = []
    ): AuditLog {
        return $this->log(
            action: $action,
            auditable_type: $auditableType,
            auditable_id: $auditableId,
            metadata: [
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'changes' => $this->getChanges($oldValues, $newValues),
            ],
            severity: 'info'
        );
    }

    /**
     * Log a security event
     *
     * @param string $event Security event type
     * @param string $severity Severity level (critical, high, medium, low, info)
     * @param array<string, mixed> $metadata Additional context
     * @return AuditLog
     */
    public function logSecurity(string $event, string $severity = 'high', array $metadata = []): AuditLog
    {
        return $this->log(
            action: $event,
            auditable_type: 'Security',
            auditable_id: null,
            metadata: array_merge($metadata, [
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'timestamp' => now()->toIso8601String(),
            ]),
            severity: $severity
        );
    }

    /**
     * Log a file access event
     *
     * @param string $action Action performed (download, upload, delete, view)
     * @param int $documentId Document ID
     * @param string $fileName File name
     * @param array<string, mixed> $metadata Additional metadata
     * @return AuditLog
     */
    public function logFileAccess(
        string $action,
        int $documentId,
        string $fileName,
        array $metadata = []
    ): AuditLog {
        return $this->log(
            action: "file_{$action}",
            auditable_type: 'App\\Models\\Document',
            auditable_id: $documentId,
            metadata: array_merge($metadata, [
                'file_name' => $fileName,
                'ip_address' => Request::ip(),
            ]),
            severity: $action === 'delete' ? 'medium' : 'info'
        );
    }

    /**
     * Log a permission change event
     *
     * @param string $action Action performed
     * @param int $userId User ID affected
     * @param array<string, mixed> $metadata Permission details
     * @return AuditLog
     */
    public function logPermissionChange(string $action, int $userId, array $metadata = []): AuditLog
    {
        return $this->log(
            action: "permission_{$action}",
            auditable_type: 'App\\Models\\User',
            auditable_id: $userId,
            metadata: $metadata,
            severity: 'high'
        );
    }

    /**
     * Log a payment transaction
     *
     * @param string $action Transaction action
     * @param int $paymentId Payment ID
     * @param float $amount Transaction amount
     * @param array<string, mixed> $metadata Additional details
     * @return AuditLog
     */
    public function logPayment(
        string $action,
        int $paymentId,
        float $amount,
        array $metadata = []
    ): AuditLog {
        return $this->log(
            action: "payment_{$action}",
            auditable_type: 'App\\Models\\Payment',
            auditable_id: $paymentId,
            metadata: array_merge($metadata, [
                'amount' => $amount,
                'currency' => 'USD',
            ]),
            severity: $action === 'refunded' ? 'high' : 'medium'
        );
    }

    /**
     * Core logging method
     *
     * @param string $action Action performed
     * @param string|null $auditableType Model class being audited
     * @param int|null $auditableId Model ID being audited
     * @param array<string, mixed> $metadata Additional context data
     * @param string $severity Severity level
     * @return AuditLog
     */
    protected function log(
        string $action,
        ?string $auditableType = null,
        ?int $auditableId = null,
        array $metadata = [],
        string $severity = 'info'
    ): AuditLog {
        return AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'auditable_type' => $auditableType,
            'auditable_id' => $auditableId,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'metadata' => $metadata,
            'severity' => $severity,
        ]);
    }

    /**
     * Get severity level for authentication events
     *
     * @param string $event Event type
     * @return string Severity level
     */
    protected function getSeverityForAuthEvent(string $event): string
    {
        return match ($event) {
            'failed_login', 'account_locked', 'suspicious_activity' => 'high',
            'password_changed', 'email_changed', '2fa_enabled', '2fa_disabled' => 'medium',
            'login', 'logout' => 'info',
            default => 'info',
        };
    }

    /**
     * Get changes between old and new values
     *
     * @param array<string, mixed> $oldValues
     * @param array<string, mixed> $newValues
     * @return array<string, array<string, mixed>>
     */
    protected function getChanges(array $oldValues, array $newValues): array
    {
        $changes = [];

        foreach ($newValues as $key => $newValue) {
            $oldValue = $oldValues[$key] ?? null;

            if ($oldValue !== $newValue) {
                $changes[$key] = [
                    'from' => $oldValue,
                    'to' => $newValue,
                ];
            }
        }

        return $changes;
    }
}

<?php

namespace Src\Adapter\Gateways\Database;

use Src\Domain\Entity\Settings;
use Src\Domain\Interface\SettingsInterface;
use PDO;
use Exception;

class SettingsRepository implements SettingsInterface
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getSettings(): ?Settings
    {
        $stmt = $this->db->prepare("SELECT * FROM settings WHERE id = 1");
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            return new Settings(
                kebeleName: $data['kebele_name'],
                adminEmail: $data['admin_email'],
                timezone: $data['timezone'],
                dateFormat: $data['date_format'],
                emailNotifications: (bool)$data['email_notifications'],
                auditLogging: (bool)$data['audit_logging'],
                showRecentActivity: (bool)$data['show_recent_activity'],
                requireStrongPasswords: (bool)$data['require_strong_passwords'],
                passwordExpiration: (bool)$data['password_expiration'],
                require2fa: (bool)$data['require_2fa'],
                sessionTimeout: (int)$data['session_timeout'],
                maxLoginAttempts: (int)$data['max_login_attempts'],
                createdAt: $data['created_at'],
                updatedAt: $data['updated_at']
            );
        }
        return null;
    }

    public function updateSettings(Settings $settings): ?Settings
    {
        $stmt = $this->db->prepare("
            UPDATE settings SET
                kebele_name = :kebele_name,
                admin_email = :admin_email,
                timezone = :timezone,
                date_format = :date_format,
                email_notifications = :email_notifications,
                audit_logging = :audit_logging,
                show_recent_activity = :show_recent_activity,
                require_strong_passwords = :require_strong_passwords,
                password_expiration = :password_expiration,
                require_2fa = :require_2fa,
                session_timeout = :session_timeout,
                max_login_attempts = :max_login_attempts,
                updated_at = NOW()
            WHERE id = 1
        ");

        $stmt->bindParam(':kebele_name', $settings->kebeleName);
        $stmt->bindParam(':admin_email', $settings->adminEmail);
        $stmt->bindParam(':timezone', $settings->timezone);
        $stmt->bindParam(':date_format', $settings->dateFormat);
        $stmt->bindValue(':email_notifications', $settings->emailNotifications ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':audit_logging', $settings->auditLogging ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':show_recent_activity', $settings->showRecentActivity ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':require_strong_passwords', $settings->requireStrongPasswords ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':password_expiration', $settings->passwordExpiration ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':require_2fa', $settings->require2fa ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindParam(':session_timeout', $settings->sessionTimeout, PDO::PARAM_INT);
        $stmt->bindParam(':max_login_attempts', $settings->maxLoginAttempts, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $this->getSettings();
        }
        return null;
    }
}
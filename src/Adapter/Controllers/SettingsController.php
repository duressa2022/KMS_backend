<?php

namespace Src\Adapter\Controllers;

use Src\Adapter\Presenters\JsonPresenter;
use Src\Usecase\SettingsUsecase;
use Src\Domain\Entity\Settings;

class SettingsController
{
    private SettingsUsecase $settingsUsecase;
    private JsonPresenter $jsonPresenter;

    public function __construct(SettingsUsecase $settingsUsecase, JsonPresenter $jsonPresenter)
    {
        $this->settingsUsecase = $settingsUsecase;
        $this->jsonPresenter = $jsonPresenter;
    }

    public function getSettings(): void
    {
        $settings = $this->settingsUsecase->getSettings();
        if ($settings) {
            $this->jsonPresenter->respond_without(200, ['data' => $settings]);
        } else {
            $this->jsonPresenter->respond_without(404, ['message' => 'Settings not found']);
        }
    }

    public function updateSettings(array $request): void
    {
        // Validate input
        if (!filter_var($request['admin_email'] ?? '', FILTER_VALIDATE_EMAIL)) {
            $this->jsonPresenter->respond_without(400, ['message' => 'Invalid admin email']);
            return;
        }
        if (!in_array($request['timezone'] ?? '', ['Africa/Addis_Ababa', 'Africa/Nairobi', 'Africa/Cairo'])) {
            $this->jsonPresenter->respond_without(400, ['message' => 'Invalid timezone']);
            return;
        }
        if (!in_array($request['date_format'] ?? '', ['dd/mm/yyyy', 'mm/dd/yyyy', 'yyyy-mm-dd'])) {
            $this->jsonPresenter->respond_without(400, ['message' => 'Invalid date format']);
            return;
        }
        if (!in_array($request['session_timeout'] ?? 0, [15, 30, 60, 120, 0])) {
            $this->jsonPresenter->respond_without(400, ['message' => 'Invalid session timeout']);
            return;
        }
        if (!in_array($request['max_login_attempts'] ?? 0, [3, 5, 10, 0])) {
            $this->jsonPresenter->respond_without(400, ['message' => 'Invalid max login attempts']);
            return;
        }

        $settings = new Settings(
            kebeleName: $request['kebele_name'] ?? 'Ginjo Guduru Kebele Administration',
            adminEmail: $request['admin_email'],
            timezone: $request['timezone'],
            dateFormat: $request['date_format'],
            emailNotifications: (bool)($request['email_notifications'] ?? false),
            auditLogging: (bool)($request['audit_logging'] ?? false),
            showRecentActivity: (bool)($request['show_recent_activity'] ?? false),
            requireStrongPasswords: (bool)($request['require_strong_passwords'] ?? false),
            passwordExpiration: (bool)($request['password_expiration'] ?? false),
            require2fa: (bool)($request['require_2fa'] ?? false),
            sessionTimeout: (int)$request['session_timeout'],
            maxLoginAttempts: (int)$request['max_login_attempts']
        );

        $updatedSettings = $this->settingsUsecase->updateSettings($settings);
        if ($updatedSettings) {
            $this->jsonPresenter->respond_without(200, ['message' => 'Settings updated successfully', 'data' => $updatedSettings]);
        } else {
            $this->jsonPresenter->respond_without(400, ['message' => 'Failed to update settings']);
        }
    }
}
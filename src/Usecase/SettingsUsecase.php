<?php

namespace Src\Usecase;

use Src\Domain\Entity\Settings;
use Src\Domain\Interface\SettingsInterface;

class SettingsUsecase
{
    private SettingsInterface $settingsRepository;

    public function __construct(SettingsInterface $settingsRepository)
    {
        $this->settingsRepository = $settingsRepository;
    }

    public function getSettings(): ?Settings
    {
        return $this->settingsRepository->getSettings();
    }

    public function updateSettings(Settings $settings): ?Settings
    {
        return $this->settingsRepository->updateSettings($settings);
    }
}
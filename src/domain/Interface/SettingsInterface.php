<?php

namespace Src\Domain\Interface;

use Src\Domain\Entity\Settings;

interface SettingsInterface {
    public function getSettings(): ?Settings;
    public function updateSettings(Settings $settings): ?Settings;
}
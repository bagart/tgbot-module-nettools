<?php

declare(strict_types=1);

namespace BAGArt\TelegramBotNettools\Web;

use BAGArt\TelegramBotMenu\Contracts\TgSettingsFormContract;
use BAGArt\TelegramBotMenu\Contracts\TgWebUiContract;
use BAGArt\TelegramBotMenu\Manifest\TgWebUiManifest;
use BAGArt\TelegramBotMenu\Manifest\UiAudience;
use BAGArt\TelegramBotMenu\Manifest\UiEntry;
use BAGArt\TelegramBotMenu\Manifest\UiField;
use BAGArt\TelegramBotMenu\Manifest\UiGroup;
use BAGArt\TelegramBotMenu\Manifest\UiKind;
use BAGArt\TelegramBotNettools\NettoolsModule;
use InvalidArgumentException;

/**
 * Menu-hub surface for nettools (menu_integration.md M-4): the per-chat UI
 * overlay keys — exactly the raw keys {@see \BAGArt\TelegramBotNettools\Support\ChatSettings}
 * persists — exposed as a §8.3 schema form. Engine toggles (recon/portscan/…)
 * stay in config('tg-nettools') until the enablement-settings seam lands
 * (G2/task 25 territory), so this form is deliberately overlay-only.
 */
final class NettoolsWebUi implements TgSettingsFormContract, TgWebUiContract
{
    public const array DETAIL_MODE_OPTIONS = ['compact', 'full'];

    public static function manifest(): TgWebUiManifest
    {
        return new TgWebUiManifest(
            moduleId: NettoolsModule::ID,
            title: 't:nettools.title',
            icon: '🛰',
            kind: UiKind::Tool,
            minAudience: UiAudience::User,
            description: 't:nettools.description',
            entry: UiEntry::schema([
                UiGroup::of('chat_ui', 't:nettools.group.chat_ui', [
                    UiField::enum('detail_mode', 't:nettools.field.detail_mode', options: [
                        ['value' => 'compact', 'label' => 't:nettools.option.compact'],
                        ['value' => 'full', 'label' => 't:nettools.option.full'],
                    ], default: 'compact'),
                    UiField::bool('heavy_confirm', 't:nettools.field.heavy_confirm', default: true),
                    UiField::bool('auto_capture', 't:nettools.field.auto_capture', default: true),
                ]),
            ]),
            sortKey: 'nettools',
            memberReadVisible: true,
        );
    }

    /** @return array<string, array<string, string>> */
    public static function translations(): array
    {
        return [
            'en' => [
                'title' => 'Nettools',
                'description' => 'Network probe toolkit for the chat',
                'group.chat_ui' => 'Chat output',
                'field.detail_mode' => 'Probe report detail',
                'option.compact' => 'Compact',
                'option.full' => 'Full',
                'field.heavy_confirm' => 'Confirm heavy probes',
                'field.auto_capture' => 'Auto-capture results to target memory',
            ],
            'ru' => [
                'title' => 'Неттулзы',
                'description' => 'Сетевой набор инструментов для чата',
                'group.chat_ui' => 'Вывод в чате',
                'field.detail_mode' => 'Детальность отчётов',
                'option.compact' => 'Компактный',
                'option.full' => 'Полный',
                'field.heavy_confirm' => 'Подтверждение тяжёлых проверок',
                'field.auto_capture' => 'Автосохранение результатов в память целей',
            ],
            'fr' => [
                'title' => 'Outils réseau',
                'description' => "Boîte à outils de sonde réseau pour le chat",
                'group.chat_ui' => 'Sortie dans le chat',
                'field.detail_mode' => 'Détail des rapports de sonde',
                'option.compact' => 'Compact',
                'option.full' => 'Complet',
                'field.heavy_confirm' => 'Confirmer les sondes lourdes',
                'field.auto_capture' => 'Capture automatique des résultats en mémoire cible',
            ],
            'es' => [
                'title' => 'Herramientas de red',
                'description' => 'Kit de herramientas de sonda de red para el chat',
                'group.chat_ui' => 'Salida en el chat',
                'field.detail_mode' => 'Detalle del informe de sonda',
                'option.compact' => 'Compacto',
                'option.full' => 'Completo',
                'field.heavy_confirm' => 'Confirmar sondas pesadas',
                'field.auto_capture' => 'Captura automática de resultados en memoria de destino',
            ],
            'zh' => [
                'title' => '网络工具',
                'description' => '聊天网络探测工具包',
                'group.chat_ui' => '聊天输出',
                'field.detail_mode' => '探测报告详细程度',
                'option.compact' => '紧凑',
                'option.full' => '完整',
                'field.heavy_confirm' => '确认重型探测',
                'field.auto_capture' => '自动保存结果到目标记忆',
            ],
        ];
    }

    /**
     * Normalizes onto the ChatSettings overlay raw keys; anything else
     * (engine toggles, quotas) is rejected rather than silently mirrored.
     *
     * @param array<string, mixed> $raw
     * @return array<string, mixed>
     */
    public function validate(array $raw): array
    {
        $patch = [];

        if (array_key_exists('detail_mode', $raw)) {
            $mode = $raw['detail_mode'];
            if (! is_string($mode) || ! in_array($mode, self::DETAIL_MODE_OPTIONS, true)) {
                throw new InvalidArgumentException('detail_mode must be one of: '.implode(', ', self::DETAIL_MODE_OPTIONS));
            }
            $patch['detail_mode'] = $mode;
        }

        foreach (['heavy_confirm', 'auto_capture'] as $key) {
            if (array_key_exists($key, $raw)) {
                $value = $raw[$key];
                if (! is_bool($value)) {
                    if ($value === 'true' || $value === '1') {
                        $value = true;
                    } elseif ($value === 'false' || $value === '0') {
                        $value = false;
                    } else {
                        throw new InvalidArgumentException($key.' must be a boolean');
                    }
                }
                $patch[$key] = $value;
            }
        }

        return $patch;
    }

    public function isConfigured(array $settings): bool
    {
        return true;
    }
}

<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Generator;

use OnixSystemsPHP\HyperfSupport\Contract\SourceConfiguratorInterface;
use OnixSystemsPHP\HyperfSupport\Contract\TicketDescriptionGeneratorContract;
use OnixSystemsPHP\HyperfSupport\Model\Ticket;

class DefaultTicketDescriptionGenerator implements TicketDescriptionGeneratorContract
{
    public function __construct(private readonly SourceConfiguratorInterface $sourceConfigurator)
    {
    }

    /**
     * @inheritDoc
     */
    public function color(Ticket $ticket): string
    {
        return match ($ticket->custom_fields['type']) {
            'Feature Request' => '#6cc3e0',
            'Tweak' => '#579dff',
            'Bug' => match ((int)$ticket->custom_fields['level']) {
                1 => '#61bd4f',
                2 => '#ff9f1a',
                3 => '#eb5a46',
                4 => '#c377e0',
                default => '#ffffff',
            },
            default => '#ffffff',
        };
    }

    /**
     * @inheritDoc
     */
    public function cover(Ticket $ticket): string
    {
        return match ($ticket->custom_fields['type']) {
            'Feature Request' => 'sky',
            'Tweak' => 'blue',
            'Bug' => match ((int)$ticket->custom_fields['level']) {
                1 => 'lime',
                2 => 'purple',
                3 => 'orange',
                4 => 'red',
                default => 'black',
            },
            default => 'yellow',
        };
    }

    /**
     * @inheritDoc
     */
    public function description(Ticket $ticket): string
    {
        return match ($ticket->custom_fields['type']) {
            'Feature Request' => 'This category includes feature requests and suggestions for improvements.',
            'Tweak' => 'This category includes minor changes and improvements.',
            'Bug' => match ((int)$ticket->custom_fields['level']) {
                1 => 'This category includes bugs that have little impact on using the system.',
                2 => 'This category includes bugs which have a moderate impact on using the system but the system can still be used.',
                3 => 'This category includes bugs which have a large impact on users which are critical to every day functioning.',
                4 => 'Complete system nonavailability.',
            },
            default => '',
        };
    }

    /**
     * @inheritDoc
     */
    public function label(Ticket $ticket): string
    {
        $type = $ticket->custom_fields['type'] ?? '';
        $level = $ticket->custom_fields['level'];

        return match ($type) {
            'Bug' => sprintf("Bug Level %d: %s", $level, $this->human($level)),
            default => $type,
        };
    }

    /**
     * @inheritDoc
     */
    public function getMentionsByIntegration(string $integration, Ticket $ticket): array
    {
        $members = $this->sourceConfigurator->getApiConfig(
            $ticket->source,
            'integrations',
            strtolower($integration),
            'members'
        ) ?? [];

        if (strtolower($integration) === 'trello') {
            return $members[$ticket->custom_fields['status']] ?? $members['default'] ?? [];
        }

        return match ($ticket->custom_fields['type']) {
            'Tweak', 'Feature Request' => $members[$ticket->custom_fields['type']] ?? [],
            'Bug' => $members['Bug'][$ticket->custom_fields['level']] ?? [],
            default => [],
        };
    }

    /**
     * @inheritDoc
     */
    public function getTrelloList(Ticket $ticket): ?string
    {
        $columns = $this->sourceConfigurator->getApiConfig($ticket->source, 'integrations', 'trello', 'lists') ?? [];

        return $columns[$ticket->custom_fields['status']] ?? $columns['default'] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function inTriggerLists(string $source, string $status): bool
    {
        $list = $this->sourceConfigurator->getApiConfig($source, 'integrations', 'trello', 'lists')[$status];

        return in_array(
            $list,
            $this->sourceConfigurator->getApiConfig($source, 'integrations', 'trello', 'trigger_lists')
        );
    }

    /**
     * Get human-readable text depending on bug level.
     *
     * @param int $bugLevel
     * @return string
     */
    private function human(int $bugLevel): string
    {
        return match ($bugLevel) {
            1 => 'Low Impact',
            2 => 'Moderate Impact',
            3 => 'Severe Impact',
            4 => 'Portal Down',
        };
    }
}

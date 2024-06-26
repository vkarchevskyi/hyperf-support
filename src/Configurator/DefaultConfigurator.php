<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Configurator;

use Hyperf\Contract\ConfigInterface;
use OnixSystemsPHP\HyperfSupport\Contract\SourceConfiguratorInterface;

class DefaultConfigurator implements SourceConfiguratorInterface
{
    public function __construct(private readonly ConfigInterface $config)
    {
    }

    /**
     * @inheritDoc
     */
    public function getApiConfig(string $source, string ...$keys): mixed
    {
        if ($source) {
            return $this->config->get('support.' . implode('.', $keys));
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getConfigValueByIntegrationAndKey(string $integration, string $key): ?string
    {
        return match ($integration) {
            'trello' => $this->config->get('support.integrations.trello.keys_to_source.' . $key),
            'slack' => $this->config->get('support.integrations.slack.keys_to_source.' . $key),
            default => null,
        };
    }

    /**
     * @inheritDoc
     */
    public function isValidApiKey(string $integration, string $key): bool
    {
        $source = match ($integration) {
            'trello' => $this->config->get('support.integrations.trello.keys_to_source.' . $key),
            'slack' => $this->config->get('support.integrations.slack.keys_to_source.' . $key),
            default => null,
        };

        return !is_null($source);
    }
}

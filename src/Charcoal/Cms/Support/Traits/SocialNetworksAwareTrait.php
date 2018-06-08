<?php

namespace Charcoal\Cms\Support\Traits;

// From 'charcoal-core'
use Charcoal\Model\ModelInterface;

// From 'charcoal-cms'
use Charcoal\Cms\ConfigInterface;

/**
 *
 */
trait SocialNetworksAwareTrait
{
    /**
     * The websites's social presence.
     *
     * @var array|null
     */
    protected $socialNetworks;

    /**
     * Determine if the website has a social presence.
     *
     * @return integer|boolean
     */
    public function hasSocialNetworks()
    {
        return count($this->socialNetworks());
    }

    /**
     * Retrieve the websites's social presence.
     *
     * @throws \Exception If the given context does not have access to config.
     * @return array
     */
    public function socialNetworks()
    {
        if ($this->socialNetworks) {
            return $this->socialNetworks;
        }

        $socials = json_decode($this->cmsConfig()['social_medias'], true);
        $configMeta = $this->configModel()->p('social_medias')->structureMetadata();

        foreach ($socials as $ident => $account) {
            $prefix = $configMeta->property($ident)['input_prefix'];
            $socials[$ident] = [
                'account' => $account,
                'prefix' => $prefix,
                'fullUrl' => $prefix.$account
            ];
        }

        $this->socialNetworks = $socials;

        return $this->socialNetworks;
    }

    /**
     * @return ConfigInterface
     */
    abstract public function cmsConfig();

    /**
     * @return ModelInterface
     */
    abstract public function configModel();
}

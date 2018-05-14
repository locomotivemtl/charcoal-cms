<?php

namespace Charcoal\Relation\Traits;

use InvalidArgumentException;

// From 'charcoal-config'
use Charcoal\Config\ConfigInterface;

// From 'charcoal-core'
use Charcoal\Model\ModelInterface;

// From 'charcoal-admin'
use Charcoal\Admin\Ui\ObjectContainerInterface;

// From 'charcoal-cms'
use Charcoal\Relation\RelationConfig;

/**
 * Configurable Relation Trait
 *
* An implementation, as Trait, of the {@see \Charcoal\Config\ConfigurableInterface}.
*/
trait ConfigurableRelationTrait
{
    /**
     * The relation configset.
     *
     * @var ConfigInterface
     */
    private $config;

    /**
     * Set the object's configuration container.
     *
     * @param  ConfigInterface|array $config The datas to set.
     * @throws InvalidArgumentException If the parameter is invalid.
     * @return ConfigurableInterface Chainable
     */
    public function setConfig($config)
    {
        if (is_array($config)) {
            $this->config = $this->createConfig($config);
        } elseif ($config instanceof ConfigInterface) {
            $this->config = $config;
        } else {
            throw new InvalidArgumentException(
                'Configuration must be an array or a ConfigInterface object.'
            );
        }
        return $this;
    }

    /**
     * Retrieve the object's configuration container, or one of its entry.
     *
     * If the object has no existing config, create one.
     *
     * If a key is provided, return the configuration key value instead of the full object.
     *
     * @param  string $key Optional. If provided, the config key value will be returned, instead of the full object.
     * @return ConfigInterface
     */
    public function config($key = null)
    {
        if ($this->config === null) {
            $this->config = $this->createConfig();
        }

        if ($key !== null) {
            return $this->config->get($key);
        } else {
            return $this->config;
        }
    }

    /**
     * Retrieve a new RelationConfig instance for the class.
     *
     * @param  array|null $data Optional data to pass to the new configset.
     * @return ConfigInterface
     */
    protected function createConfig($data = null)
    {
        return new RelationConfig($data);
    }

    /**
     * Parse the given data and recursively merge presets from relation config.
     *
     * @param  array $data The widget data.
     * @return array Returns the merged widget data.
     */
    protected function mergePresets(array $data)
    {
        if (isset($data['target_object_types'])) {
            $data['target_object_types'] = $this->mergePresetTargetObjectTypes($data['target_object_types']);
        }

        return $data;
    }

    /**
     * Parse the given data and merge the preset object types.
     *
     * @param  string|array $data The target object type data.
     * @throws InvalidArgumentException If the object type or structure is invalid.
     * @return array Returns the merged target object type data.
     */
    private function mergePresetTargetObjectTypes($data)
    {
        if (is_string($data)) {
            $groupIdent = $data;
            if ($this instanceof ObjectContainerInterface) {
                if ($this->hasObj()) {
                    $groupIdent = $this->obj()->render($groupIdent);
                }
            } elseif ($this instanceof ModelInterface) {
                $groupIdent = $this->render($groupIdent);
            }

            $presetGroups = $this->config('groups');
            if (isset($presetGroups[$groupIdent])) {
                $data = $presetGroups[$groupIdent];
            }
        }

        if (is_array($data)) {
            $presetTypes = $this->config('targetObjectTypes');
            $targetObjectTypes = [];
            foreach ($data as $type => $struct) {
                if (is_string($struct)) {
                    $type = $struct;
                    $struct = [];
                }

                if (!is_string($type)) {
                    throw new InvalidArgumentException(
                        'The object type must be a string'
                    );
                }

                if (!is_array($struct)) {
                    throw new InvalidArgumentException(sprintf(
                        'The object structure for "%s" must be an array',
                        $type
                    ));
                }

                if (isset($presetTypes[$type])) {
                    $struct = array_replace_recursive(
                        $presetTypes[$type],
                        $struct
                    );
                }

                $targetObjectTypes[$type] = $struct;
            }

            $data = $targetObjectTypes;
        }

        return $data;
    }
}

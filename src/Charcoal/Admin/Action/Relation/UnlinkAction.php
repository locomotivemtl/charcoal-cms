<?php

namespace Charcoal\Admin\Action\Relation;

use Exception;

// From 'pimple'
use Pimple\Container;

// From PSR-7 (HTTP Messaging)
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

// From 'charcoal-admin'
use Charcoal\Admin\AdminAction;

// From 'charcoal-core'
use Charcoal\Loader\CollectionLoader;

// From 'charcoal-cms'
use Charcoal\Relation\Pivot;

/**
 * Dissociate two objects from a Pivot.
 *
 * Detaches an object from another by removing
 * a record from the intermediate table.
 */
class UnlinkAction extends AdminAction
{
    /**
     * @param RequestInterface  $request  A PSR-7 compatible Request instance.
     * @param ResponseInterface $response A PSR-7 compatible Response instance.
     * @return ResponseInterface
     */
    public function run(RequestInterface $request, ResponseInterface $response)
    {
        $params = $request->getParams();

        if (!isset($params['pivot_id'])) {
            $this->setSuccess(false);

            return $response;
        }

        $pivotId = $params['pivot_id'];

        $pivotProto = $this->modelFactory()->create(Pivot::class);
        if (!$pivotProto->source()->tableExists()) {
            $pivotProto->source()->createTable();
        }

        $pivotModel = $pivotProto->load($pivotId);

        if ($pivotModel->id()) {
            $pivotModel->delete();
        }

        $this->setSuccess(true);

        return $response;
    }
}

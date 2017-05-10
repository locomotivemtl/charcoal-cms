<?php

namespace Charcoal\Admin\Action\Relation;

use Exception;

// From Pimple
use Pimple\Container;

// From PSR-7 (HTTP Messaging)
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

// From 'charcoal-admin'
use Charcoal\Admin\AdminAction;

// From 'charcoal-core'
use Charcoal\Loader\CollectionLoader;

// From 'charcoal-cms'
use Charcoal\Relation\Interfaces\PivotableInterface;
use Charcoal\Relation\Pivot;

/**
 * Associate two objects using a Pivot.
 *
 * Attaches one object to another by inserting
 * a record in the intermediate table.
 */
class LinkAction extends AdminAction
{
    /**
     * @param RequestInterface  $request  A PSR-7 compatible Request instance.
     * @param ResponseInterface $response A PSR-7 compatible Response instance.
     * @return ResponseInterface
     */
    public function run(RequestInterface $request, ResponseInterface $response)
    {
        $params = $request->getParams();

        if (!isset($params['pivots']) ||
            !isset($params['obj_id']) ||
            !isset($params['obj_type']) ||
            !isset($params['group'])
        ) {
            $this->addFeedback('error', 'Invalid parameters for Pivot.');
            $this->setSuccess(false);

            return $response;
        }

        $pivots        = $params['pivots'];
        $sourceObjId   = $params['obj_id'];
        $sourceObjType = $params['obj_type'];
        $group         = $params['group'];

        // Needs more pivots
        if (!count($pivots)) {
            $this->setSuccess(false);

            return $response;
        }

        // Try loading the object
        try {
            $obj = $this->modelFactory()->create($sourceObjType)->load($sourceObjId);
        } catch (Exception $e) {
            $this->setSuccess(false);
            return $response;
        }

        $pivotProto = $this->modelFactory()->create(Pivot::class);
        if (!$pivotProto->source()->tableExists()) {
            $pivotProto->source()->createTable();
        }

        // Clean all previously created pivots and start anew
        $loader = new CollectionLoader([
            'logger'  => $this->logger,
            'factory' => $this->modelFactory()
        ]);
        $loader
            ->setModel($pivotProto)
            ->addFilter('source_object_type', $sourceObjType)
            ->addFilter('source_object_id', $sourceObjId)
            ->addFilter('group', $group)
            ->addOrder('position', 'asc');

        $existingPivots = $loader->load();

        foreach ($existingPivots as $j) {
            $j->delete();
        }

        $count = count($pivots);
        $i = 0;
        for (; $i < $count; $i++) {
            $targetObjType = $pivots[$i]['target_object_type'];
            $targetObjId = $pivots[$i]['target_object_id'];
            $position = $pivots[$i]['position'];

            $pivotModel = $this->modelFactory()->create(Pivot::class);
            $pivotModel
                ->setSourceObjectType($sourceObjType)
                ->setSourceObjectId($sourceObjId)
                ->setTargetObjectType($targetObjType)
                ->setTargetObjectId($targetObjId)
                ->setGroup($group)
                ->setPosition($position);

            $pivotModel->save();

            $targetObjModel = $this->modelFactory()->create($targetObjType);

            if ($targetObjModel instanceof PivotableInterface) {
                $targetObjModel->load($targetObjId)->postPivotSave();
            }
        }

        $this->setSuccess(true);

        return $response;
    }
}

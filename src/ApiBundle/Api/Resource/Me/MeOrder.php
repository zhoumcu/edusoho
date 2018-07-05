<?php

namespace ApiBundle\Api\Resource\Me;

use ApiBundle\Api\ApiRequest;
use ApiBundle\Api\Resource\AbstractResource;
use ApiBundle\Api\Annotation\ResponseFilter;
use Biz\OrderFacade\Service\OrderFacadeService;

class MeOrder extends AbstractResource
{
    /**
     * @ResponseFilter(class="ApiBundle\Api\Resource\Order\OrderFilter", mode="simple")
     */
    public function search(ApiRequest $request)
    {
        $conditions = array(
            'user_id' => $this->getCurrentUser()->getId(),
        );
        list($offset, $limit) = $this->getOffsetAndLimit($request);
        $orders = $this->getOrderService()->searchOrders(
            $conditions,
            array('created_time' => 'DESC'),
            $offset,
            $limit
        );

        return $orders;
    }

    /**
     * @return OrderService
     */
    protected function getOrderService()
    {
        return $this->getBiz()->service('Order:OrderService');
    }

    /**
     * @return OrderFacadeService
     */
    private function getOrderFacadeService()
    {
        return $this->service('OrderFacade:OrderFacadeService');
    }
}
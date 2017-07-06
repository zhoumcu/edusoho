<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Common\Paginator;
use AppBundle\Common\ArrayToolkit;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Common\ExportHelp;

class InviteController extends BaseController
{
    public function recordAction(Request $request)
    {
        $conditions = $request->query->all();
        $conditions = ArrayToolkit::parts($conditions, array('nickname', 'startDate', 'endDate'));

        $page = $request->query->get('page', 0);

        if (!empty($conditions['nickname']) && empty($page)) {
            $user = $this->getUserService()->getUserByNickname($conditions['nickname']);
            $conditions['inviteUserId'] = empty($user) ? '0' : $user['id'];
            unset($conditions['nickname']);
            $invitedRecord = $this->getInvitedRecordByUserIdAndConditions($user, $conditions);
        }

        $recordCount = $this->getInviteRecordService()->countRecords($conditions);
        $paginator = new Paginator(
            $this->get('request'),
            $recordCount,
            20
        );

        $inviteRecords = $this->getInviteRecordService()->searchRecords(
            $conditions,
            array(),
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        if (!empty($invitedRecord)) {
            $inviteRecords = array_merge($invitedRecord, $inviteRecords);
        }

        foreach ($inviteRecords as &$record) {
            list($coinAmountTotalPrice, $amountTotalPrice, $totalPrice) = $this->getUserOrderDataByUserIdAndTime($record['invitedUserId'], $record['inviteTime']);
            $record['coinAmountTotalPrice'] = $coinAmountTotalPrice;
            $record['amountTotalPrice'] = $amountTotalPrice;
            $record['totalPrice'] = $totalPrice;
        }

        $users = $this->getAllUsersByRecords($inviteRecords);

        return $this->render('admin/invite/records.html.twig', array(
            'records' => $inviteRecords,
            'users' => $users,
            'paginator' => $paginator,
        ));
    }

    public function preExportRecordDataAction(Request $request)
    {
        list($start, $limit, $exportAllowCount) = ExportHelp::getMagicExportSetting($request);

        $conditions = $request->query->all();
        $conditions = ArrayToolkit::parts($conditions, array('nickname', 'startDate', 'endDate'));
        $nickname = $request->query->get('nickname');
        if (!empty($nickname)) {
            $user = $this->getUserService()->getUserByNickname($nickname);
            $conditions['inviteUserId'] = empty($user) ? '0' : $user['id'];
            unset($conditions['nickname']);
        }

        list($records, $recordCount) = $this->getExportRecordContent(
            $start,
            $limit,
            $conditions,
            $exportAllowCount
        );

        $title = '邀请人,注册用户,订单消费总额,订单虚拟币总额,订单现金总额,邀请码,邀请时间';
        $file = '';
        if ($start == 0) {
            $file = ExportHelp::addFileTitle($request, 'invite_record', $title);

            if (!empty($user)) {
                $invitedRecord = $this->getInvitedRecordByUserIdAndConditions($user, $conditions);
                if ($invitedRecord) {
                    $users = $this->getAllUsersByRecords($invitedRecord);
                    $invitedExportContent = $this->exportDataByRecord(reset($invitedRecord), $users);
                    $file = ExportHelp::saveToTempFile($request, $invitedExportContent, $file);
                }
            }
        }

        $content = implode("\r\n", $records);
        $file = ExportHelp::saveToTempFile($request, $content, $file);

        $status = ExportHelp::getNextMethod($start + $limit, $recordCount);

        return $this->createJsonResponse(
            array(
                'status' => $status,
                'fileName' => $file,
                'start' => $start + $limit,
            )
        );
    }

    public function getExportRecordContent($start, $limit, $conditions, $exportAllowCount)
    {
        $recordCount = $this->getInviteRecordService()->countRecords($conditions);

        $recordCount = ($recordCount > $exportAllowCount) ? $exportAllowCount : $recordCount;
        if ($recordCount < ($start + $limit + 1)) {
            $limit = $recordCount - $start;
        }

        $recordData = array();
        $records = $this->getInviteRecordService()->searchRecords(
            $conditions,
            array(),
            $start,
            $limit
        );
        $users = $this->getAllUsersByRecords($records);

        foreach ($records as $record) {
            $content = $this->exportDataByRecord($record, $users);
            $recordData[] = $content;
        }

        return array($recordData, $recordCount);
    }

    protected function getInvitedRecordByUserIdAndConditions($user, $conditions)
    {
        if (empty($user)) {
            return array();
        }
        $invitedRecordConditions = ArrayToolkit::parts($conditions, array('startDate', 'endDate'));
        $invitedRecordConditions['invitedUserId'] = $user['id'];
        $invitedRecord = $this->getInviteRecordService()->searchRecords(
            $invitedRecordConditions,
            array(),
            0,
            1
        );

        return ArrayToolkit::index($invitedRecord, 'id');
    }

    protected function exportDataByRecord($record, $users)
    {
        list($coinAmountTotalPrice, $amountTotalPrice, $totalPrice) = $this->getUserOrderDataByUserIdAndTime($record['invitedUserId'], $record['inviteTime']);
        $content = '';
        $content .= $users[$record['inviteUserId']]['nickname'].',';
        $content .= $users[$record['invitedUserId']]['nickname'].',';
        $content .= $totalPrice.',';
        $content .= $coinAmountTotalPrice.',';
        $content .= $amountTotalPrice.',';
        $content .= $users[$record['inviteUserId']]['inviteCode'].',';
        $content .= date('Y-m-d H:i:s', $record['inviteTime']).',';

        return $content;
    }

    public function userRecordsAction(Request $request)
    {
        $conditions = $request->query->all();
        $conditions = ArrayToolkit::parts($conditions, array('nickname'));

        $paginator = new Paginator(
            $this->get('request'),
            $this->getUserService()->countUsers($conditions),
            20
        );

        $users = $this->getUserService()->searchUsers(
            $conditions,
            array('id' => 'ASC'),
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        $inviteInformations = $this->getInviteInformationsByUsers($users);

        return $this->render('admin/invite/user-record.html.twig', array(
            'paginator' => $paginator,
            'inviteInformations' => $inviteInformations,
        ));
    }

    public function userRecordsPreExportAction(Request $request)
    {
        list($start, $limit, $exportAllowCount) = ExportHelp::getMagicExportSetting($request);

        $conditions = $request->query->all();

        $count = $this->getUserService()->countUsers($conditions);
        $count = ($count > $exportAllowCount) ? $exportAllowCount : $count;
        if ($count < ($start + $limit + 1)) {
            $limit = $count - $start;
        }

        $users = $this->getUserService()->searchUsers(
            $conditions,
            array('id' => 'ASC'),
            $start,
            $limit
        );

        $userRecordData = $this->getInviteInformationsByUsers($users);
        $userRecordData = $this->getUserRecordContent($userRecordData);

        $title = '用户名,邀请人数,付费用户数,订单消费总额,订单虚拟币总额	,订单现金总额';
        $file = '';
        if ($start == 0) {
            $file = ExportHelp::addFileTitle($request, 'user_record', $title);
        }

        $content = implode("\r\n", $userRecordData);
        $file = ExportHelp::saveToTempFile($request, $content, $file);

        $status = ExportHelp::getNextMethod($start + $limit, $count);

        return $this->createJsonResponse(
            array(
                'status' => $status,
                'fileName' => $file,
                'start' => $start + $limit,
            )
        );
    }

    private function getUserRecordContent($userRecordDatas)
    {
        $data = array();
        foreach ($userRecordDatas as $userRecordData) {
            $content = '';
            $content .= $userRecordData['nickname'].',';
            $content .= $userRecordData['count'].',';
            $content .= $userRecordData['payingUserCount'].',';
            $content .= $userRecordData['payingUserTotalPrice'].',';
            $content .= $userRecordData['coinAmountPrice'].',';
            $content .= $userRecordData['amountPrice'].',';
            $data[] = $content;
        }

        return $data;
    }

    private function getInviteInformationsByUsers($users)
    {
        $inviteInformations = array();
        foreach ($users as $key => $user) {
            $invitedRecords = $this->getInviteRecordService()->findRecordsByInviteUserId($user['id']);
            $payingUserCount = 0;
            $totalPrice = 0;
            $totalCoinAmount = 0;
            $totalAmount = 0;

            foreach ($invitedRecords as $keynum => $invitedRecord) {
                list($coinAmountTotalPrice, $amountTotalPrice, $tempPrice) = $this->getUserOrderDataByUserIdAndTime($invitedRecord['invitedUserId'], $invitedRecord['inviteTime']);

                if ($coinAmountTotalPrice || $amountTotalPrice) {
                    $payingUserCount = $payingUserCount + 1;
                }

                $totalCoinAmount = $totalCoinAmount + $coinAmountTotalPrice;
                $totalAmount = $totalAmount + $amountTotalPrice;
                $totalPrice = $totalPrice + $tempPrice;
            }

            $inviteInformations[] = array(
                'id' => $user['id'],
                'nickname' => $user['nickname'],
                'payingUserCount' => $payingUserCount,
                'payingUserTotalPrice' => $totalPrice,
                'coinAmountPrice' => $totalCoinAmount,
                'amountPrice' => $totalAmount,
                'count' => count($invitedRecords),
            );
        }

        return $inviteInformations;
    }

    public function inviteDetailAction(Request $request)
    {
        $inviteUserId = $request->query->get('inviteUserId');

        $details = array();

        $invitedRecords = $this->getInviteRecordService()->findRecordsByInviteUserId($inviteUserId);

        foreach ($invitedRecords as $key => $invitedRecord) {
            list($coinAmountTotalPrice, $amountTotalPrice, $totalPrice) = $this->getUserOrderDataByUserIdAndTime($invitedRecord['invitedUserId'], $invitedRecord['inviteTime']);

            $user = $this->getUserService()->getUser($invitedRecord['invitedUserId']);

            if (!empty($user)) {
                $details[] = array(
                    'userId' => $user['id'],
                    'nickname' => $user['nickname'],
                    'totalPrice' => $totalPrice,
                    'amountTotalPrice' => $amountTotalPrice,
                    'coinAmountTotalPrice' => $coinAmountTotalPrice,
                );
            }
        }

        return $this->render('admin/invite/invite-modal.html.twig', array(
            'details' => $details,
        ));
    }

    // 得到这个用户在注册后消费情况，订单消费总额；订单虚拟币总额；订单现金总额
    protected function getUserOrderDataByUserIdAndTime($userId, $inviteTime)
    {
        $coinAmountTotalPrice = $this->getOrderService()->analysisCoinAmount(array('userId' => $userId, 'coinAmount' => 0, 'status' => 'paid', 'paidStartTime' => $inviteTime));
        $amountTotalPrice = $this->getOrderService()->analysisAmount(array('userId' => $userId, 'amount' => 0, 'status' => 'paid', 'paidStartTime' => $inviteTime));
        $totalPrice = $this->getOrderService()->analysisTotalPrice(array('userId' => $userId, 'status' => 'paid', 'paidStartTime' => $inviteTime));

        return array($coinAmountTotalPrice, $amountTotalPrice, $totalPrice);
    }

    protected function getAllUsersByRecords($records)
    {
        $inviteUserIds = ArrayToolkit::column($records, 'inviteUserId');
        $invitedUserIds = ArrayToolkit::column($records, 'invitedUserId');
        $userIds = array_merge($inviteUserIds, $invitedUserIds);
        $users = $this->getUserService()->findUsersByIds($userIds);

        return $users;
    }

    public function couponAction(Request $request, $filter)
    {
        $fileds = $request->query->all();
        $conditions = array();
        $conditions = $this->_prepareQueryCondition($fileds);

        if ($filter == 'invite') {
            $conditions['inviteUserCardIdNotEqual'] = 0;
        } elseif ($filter == 'invited') {
            $conditions['invitedUserCardIdNotEqual'] = 0;
        }

        list($paginator, $cardInformations) = $this->getCardInformations($request, $conditions);

        if ($filter == 'invite') {
            $cardIds = ArrayToolkit::column($cardInformations, 'inviteUserCardId');
        } elseif ($filter == 'invited') {
            $cardIds = ArrayToolkit::column($cardInformations, 'invitedUserCardId');
        }

        $cards = $this->getCardService()->findCardsByCardIds($cardIds);
        list($coupons, $orders, $users) = $this->getCardsData($cards);

        return $this->render('admin/invite/coupon.html.twig', array(
            'paginator' => $paginator,
            'cardInformations' => $cardInformations,
            'filter' => $filter,
            'users' => $users,
            'coupons' => $coupons,
            'cards' => $cards,
            'orders' => $orders,
        ));
    }

    public function queryInviteCouponAction(Request $request)
    {
        $fileds = $request->query->all();
        $conditions = array();
        $conditions = $this->_prepareQueryCondition($fileds);
        $conditions['cardType'] = 'coupon';
        $cards = $this->getCardService()->searchCards(
            $conditions,
            array('id' => 'ASC'),
            0,
            PHP_INT_MAX
        );
        $cards = ArrayToolkit::index($cards, 'cardId');
        list($coupons, $orders, $users) = $this->getCardsData($cards);
        $conditions = array();
        $conditions['inviteUserCardIds'] = empty($cards) ? array(-1) : ArrayToolkit::column($cards, 'cardId');
        list($paginator, $cardInformations) = $this->getCardInformations($request, $conditions);

        return $this->render('admin/invite/coupon.html.twig', array(
            'paginator' => $paginator,
            'cardInformations' => $cardInformations,
            'filter' => 'invite',
            'users' => $users,
            'coupons' => $coupons,
            'cards' => $cards,
            'orders' => $orders,
        ));
    }

    private function _prepareQueryCondition($fileds)
    {
        $conditions = array();

        if (!empty($fileds['nickname'])) {
            $conditions['nickname'] = $fileds['nickname'];
        }

        if (!empty($fileds['startDateTime'])) {
            $conditions['startDateTime'] = strtotime($fileds['startDateTime']);
        }

        if (!empty($fileds['endDateTime'])) {
            $conditions['endDateTime'] = strtotime($fileds['endDateTime']);
        }

        return $conditions;
    }

    private function getCardsData($cards)
    {
        $coupons = $this->getCouponService()->findCouponsByIds(ArrayToolkit::column($cards, 'cardId'));

        $orders = $this->getOrderService()->findOrdersByIds(ArrayToolkit::column($coupons, 'orderId'));

        $users = $this->getUserService()->findUsersByIds(ArrayToolkit::column($cards, 'userId'));

        return array($coupons, $orders, $users);
    }

    private function getCardInformations($request, $conditions)
    {
        $paginator = new Paginator(
            $request,
            $this->getInviteRecordService()->countRecords($conditions),
            20
        );

        $cardInformations = $this->getInviteRecordService()->searchRecords(
            $conditions,
            array('inviteTime' => 'DESC'),
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        return array($paginator, $cardInformations);
    }

    protected function getInviteRecordService()
    {
        return $this->createService('User:InviteRecordService');
    }

    protected function getOrderService()
    {
        return $this->createService('Order:OrderService');
    }

    protected function getUserService()
    {
        return $this->createService('User:UserService');
    }

    protected function getCardService()
    {
        return $this->createService('Card:CardService');
    }

    protected function getCouponService()
    {
        return $this->createService('Coupon:CouponService');
    }
}

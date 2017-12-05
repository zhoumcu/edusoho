<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Common\Paginator;
use AppBundle\Common\ArrayToolkit;
use Biz\User\Service\AuthService;
use Symfony\Component\HttpFoundation\Request;

class UserLearnStatisticsController extends BaseController
{
    public function showAction(Request $request)
    {
        $conditions = $request->query->all();
        $paginator = new Paginator(
            $request,
            $this->getLearnStatisticsService()->countTotalStatistics($conditions),
            20
        );

        $statistics = $this->getLearnStatisticsService()->searchTotalStatistics(
            $conditions,
            array('id' => 'DESC'),
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );
        $userIds = ArrayToolkit::column($statistics, 'userId');
        $users = $this->getUserService()->findUsersByIds($userIds);
        return $this->render('admin/learn-Statistics/show.html.twig', array(
            'statistics' => $statistics,
            'paginator' => $paginator,
            'users' => $users,
        ));
    }  

    public function syncDailyData()
    {
        
    }

    protected function getLearnStatisticsService()
    {
        return $this->createService('UserLearnStatistics:LearnStatisticsService');
    }

    protected function getSettingService()
    {
        return $this->createService('System:SettingService');
    }

    protected function getUserService()
    {
        return $this->createService('User:UserService');
    }
}
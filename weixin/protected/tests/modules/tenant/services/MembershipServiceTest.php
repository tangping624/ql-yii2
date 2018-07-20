<?php

namespace app\tests;

require_once __DIR__ . '/../../../helpers/TestBase.php';

use app\modules\member\repositories\CustomerChannelRepository;
use app\modules\member\repositories\MembershipRepository;
use app\modules\member\repositories\RoomRepository;
use app\modules\member\services\BizParamService;
use app\modules\member\services\MembershipService;
use app\modules\member\services\PointService;
use \Mockery as m;

class MembershipServiceTest extends \TestBase
{
    /**
     * @var MembershipRepository | \Mockery\MockInterface
     */
    protected $membershipRepo;

    /**
     * @var RoomRepository | \Mockery\MockInterface
     */
    protected $roomRepo;

    /**
     * @var PointService | \Mockery\MockInterface
     */
    protected $pointService;

    /**
     * @var BizParamService | \Mockery\MockInterface
     */
    protected $bizParamService;

    /**
     * @var CustomerChannelRepository | \Mockery\MockInterface
     */
    protected $customerChannelRepository;


    /**
     * @var MembershipService
     */
    protected $service;

    public function setUp()
    {
        \Yii::$app->cache->flush();
        $this->membershipRepo = m::mock('app\modules\member\repositories\MembershipRepository');
        $this->roomRepo = m::mock('app\modules\member\repositories\RoomRepository');
        $this->pointService = m::mock('app\modules\member\services\PointService');
        $this->bizParamService = m::mock('app\modules\member\services\BizParamService');
        $this->customerChannelRepository = m::mock('app\modules\member\repositories\CustomerChannelRepository');

        //只为加载常量
        \app\framework\biz\bizparam\BizParamAPI::instance();
        $this->service = new MembershipService(
            $this->membershipRepo,
            $this->roomRepo,
            $this->customerChannelRepository,
            $this->bizParamService,
            $this->pointService
        );
    }

    public function testSignIn()
    {
        $param = ['mobile' => '18688772914', 'openid' => 'xx123', 'recommender_id' => null];
        $openid = $param['openid'];
        $mobile = $param['mobile'];
        $recommendId = $param['recommender_id'];

        $publicId = 'public_id_1';
        $corpId = 'corp_id_1';

        //1. 粉丝已经存在memberId, 则提示注册失败
        $this->membershipRepo->shouldReceive('isMember')->andReturn(true);
        $result = $this->service->signIn($param, $publicId, $corpId);
        $this->assertTrue($result['result'] == false);

        $this->setUp();
        //2.1 新会员
        $memberIdResult = false;
        // $fan = ['sex'=>'男', 'nick_name' => 'nk'];
        // $paramValue = [['value'=>1]];
        $this->membershipRepo->shouldReceive('isMember')->andReturn(false);
        $this->membershipRepo->shouldReceive('getMemberByEachMobile')->andReturn($memberIdResult)->once();
        $this->service = m::mock(
            'app\modules\member\services\MembershipService[register,login]',
            [
                $this->membershipRepo,
                $this->roomRepo,
                $this->customerChannelRepository,
                $this->bizParamService,
                $this->pointService
            ]
        );

        $this->service->shouldReceive('register')->with(
            $openid,
            $publicId,
            $corpId,
            $mobile,
            $recommendId
        )->andReturn('member_id');

        $result = $this->service->signIn($param, $publicId, $corpId);
        $this->assertTrue($result['result'] == true && $result['require_auth'] == false && $result['member_id'] == 'member_id');

        //2.2 已有会员
        $this->setUp();
        $this->membershipRepo->shouldReceive('isMember')->andReturn(false);
        $memberIdResult = ['memberId' => 'm1', 'hasLoggedBefore' => 0, 'isMaster' => 1, 'corp_id' => 'corp_id1'];
        $this->membershipRepo->shouldReceive('getMemberByEachMobile')->andReturn($memberIdResult)->once();
        $currentMember = [
            'id' => 'm1',
            'name' => 'zs',
            'corp_id' => 'new_corp_id',
            'mobile' => '13654254124',
            'id_code' => '430425158111251366',
            'id_type' => '身份证',
            'zip_code' => '421600',
            'mailing_address' => 'shen zhen',
            'recommender_id' => '',
            'sex' => '男',
            'birthday' => '',
            'level_id' => '13xx'
        ];
        $this->membershipRepo->shouldReceive('getMemberBasicById')->with(
            'm1',
            [
                'id',
                'name',
                'corp_id',
                'mobile',
                'id_code',
                'id_type',
                'zip_code',
                'mailing_address',
                'recommender_id',
                'sex',
                'birthday',
                'level_id'
            ]
        )
            ->andReturn($currentMember);
        $this->service = m::mock(
            'app\modules\member\services\MembershipService[register,login,isNeedAuthenticate]',
            [
                $this->membershipRepo,
                $this->roomRepo,
                $this->customerChannelRepository,
                $this->bizParamService,
                $this->pointService
            ]
        );

        $this->service->shouldReceive('login')->with(
            $openid,
            $publicId,
            $corpId,
            $mobile,
            ['id' => 'm1', 'has_logged_before' => $memberIdResult['hasLoggedBefore']],
            $recommendId
        )
            ->andReturn('member_id');
        $this->service->shouldReceive('isNeedAuthenticate')->with('m1')->andReturn(true)->once();

        $result = $this->service->signIn($param, $publicId, $corpId);

        $this->assertTrue(
            $result['result'] == true && $result['require_auth'] == true,
            $result['member_id'] == 'member_id'
        );


    }

    //matchType = 0
    public function testVerifyIdCode()
    {
        $memberId = '1';
        $corpId = 'oid';
        //$paramValue = [['value'=>10]];
        $input = '123456';
        $mobile = '136';
        $publicId = 'pid';
        $openid = 'openid';
        //$recommenderId = '';

        //$authInfo = ['id_code' => '123456', 'type' => '业主', 'is_authenticated' => 0, 'corp_id'=>'oid'];

        /////////正常匹配通过逻辑//////////
        //新会员
        $destMemberRow = ['id'=>'id1','mobile'=>$mobile, 'has_logged_before'=>0, 'id_type'=>'身份证', 'is_authenticated'=>0];
        $this->membershipRepo->shouldReceive('getMemberByIdCode')->with($input)->andReturn($destMemberRow)->once();
        $this->membershipRepo->shouldReceive('updateMember');


        $member = ['isMaster' => false, 'memberId' => $memberId, 'hasLoggedBefore' => 0, 'is_authenticated' => 0];
        $this->membershipRepo->shouldReceive('getMemberByEachMobile')->andReturn($member);
        $this->membershipRepo->shouldReceive('authOwnerAndSetMobile')->andReturn(true);
        $this->bizParamService->shouldReceive('getBusinessParameters')->andReturn([['value'=>2]]);
        $this->pointService->shouldReceive('sigInThenAddPoint')->with($memberId, 2, MEMBER_POINT_AUTH_TEXT)->andReturn(true)->once();
        $service = m::mock('app\modules\member\services\MembershipService[signIn]', [$this->membershipRepo, $this->roomRepo, $this->customerChannelRepository, $this->bizParamService, $this->pointService]);
        //$signResult = ['result' => true, 'msg' => 'success', 'require_auth' => false, 'member_id' => $memberId];
        $service->verifyIdCode($input, $openid, $corpId, $mobile, $publicId);

        //已有会员
        $this->setUp();
        $destMemberRow['mobile'] = '135';
        $member = ['isMaster' => false, 'memberId' => $memberId, 'hasLoggedBefore' => 0, 'is_authenticated' => 0];
        $this->membershipRepo->shouldReceive('getMemberByEachMobile')->andReturn($member);

        $this->membershipRepo->shouldReceive('getMemberByIdCode')->with($input)->andReturn($destMemberRow)->once();
        $this->membershipRepo->shouldReceive('updateMember')->never();
        $this->bizParamService->shouldReceive('getBusinessParameters')->andReturn([['value'=>2]]);
        $this->pointService->shouldReceive('sigInThenAddPoint')->with($memberId, 2, MEMBER_POINT_AUTH_TEXT)->andReturn(true)->once();
        $this->membershipRepo->shouldReceive('authOwnerAndSetMobile')
            ->with($destMemberRow['id'], $member['memberId'], $mobile, $corpId, $input, $destMemberRow['id_type'])
            ->andReturn(true)->once();
        $service = m::mock('app\modules\member\services\MembershipService[signIn]', [$this->membershipRepo, $this->roomRepo, $this->customerChannelRepository, $this->bizParamService, $this->pointService]);
        //$signResult = ['result' => true, 'msg' => 'success', 'require_auth' => false, 'member_id' => $memberId];
        //$signParams = ['mobile' => $mobile, 'openid' => $openid, 'recommender_id' => $recommenderId];
        $service->verifyIdCode($input, $openid, $corpId, $mobile, $publicId);


    }

    public function testAddPointOfAuthenticate()
    {
        $corpId = 'corpid';
        $memberId = 'mid';

        $this->bizParamService->shouldReceive('getBusinessParameters')->with('AuthPoint', $corpId)->andReturn([['value' => 10]])->once();
        $this->pointService->shouldReceive('sigInThenAddPoint')->with($memberId, 10, MEMBER_POINT_AUTH_TEXT)->andReturn(true)->once();
        $this->service->addPointOfAuthenticate($corpId, $memberId);
    }

    public function testIsNeedAuthenticate()
    {
        $memberId = 'mid';
        $this->membershipRepo->shouldReceive('getMemberOfYezhuBasic')->with($memberId)->andReturn(['is_authenticated'=>1, 'id_code'=>'123'])->once();
        $expect = $this->service->isNeedAuthenticate($memberId);
        $this->assertTrue($expect === false);

        $this->setUp();
        $this->membershipRepo->shouldReceive('getMemberOfYezhuBasic')->with($memberId)->andReturn(['is_authenticated'=>0, 'id_code'=>'123'])->once();
        $expect = $this->service->isNeedAuthenticate($memberId);
        $this->assertTrue($expect === true);

        $this->setUp();
        $this->membershipRepo->shouldReceive('getMemberOfYezhuBasic')->with($memberId)->andReturn(['is_authenticated'=>0, 'id_code'=>''])->once();
        $expect = $this->service->isNeedAuthenticate($memberId);
        $this->assertTrue($expect === false);
    }

    public function testGetMemberAuthInfo()
    {
        $memberId = 'mid';
        $this->membershipRepo->shouldReceive('getMemberAuthInfo')->with($memberId)->once();
        $this->service->getMemberAuthInfo($memberId);
    }

    public function testGetIdCodeAndNameByMemberId()
    {
        $memberId = 'mid';
        $this->membershipRepo->shouldReceive('getIdCodeAndNameByMemberId')->with($memberId)->once();
        $this->service->getIdCodeAndNameByMemberId($memberId);
    }

    public function testIsMember()
    {
        $openId = 'openid';
        $result = true;
        $this->membershipRepo->shouldReceive('isMember')->with($openId)->andReturn($result)->once();
        $expect = $this->service->isMember($openId);
        $this->assertTrue($result === $expect);

    }

    public function testUpdateAuthIdCode()
    {
        $memberId = 'mid';
        $idType = '身份证';
        $idCode = '113';
        $corpId = 'corp_id';

        $this->service = m::mock(
            'app\modules\member\services\MembershipService[addPointOfAuthenticate]',
            [$this->membershipRepo, $this->roomRepo, $this->customerChannelRepository, $this->bizParamService, $this->pointService]
        );
        $this->membershipRepo->shouldReceive('isAuthenticated')->with($memberId)->andReturn(false)->once();
        $this->membershipRepo->shouldReceive('updateIdCode')->with($memberId, $idType, $idCode)->andReturn(true)->once();
        $this->service->shouldReceive('addPointOfAuthenticate')->with($corpId, $memberId)->once();
        $expect = $this->service->updateAuthIdCode($memberId, $idType, $idCode, $corpId);
        $this->assertTrue($expect === true);

        ////case: 证件号不存在
        $this->setUp();
        $this->membershipRepo->shouldReceive('updateIdCode')->with($memberId, $idType, $idCode)->andReturn(true)->once();
        $this->membershipRepo->shouldReceive('isAuthenticated')->with($memberId)->andReturn(true)->once();
        $this->service = m::mock(
            'app\modules\member\services\MembershipService[addPointOfAuthenticate]',
            [$this->membershipRepo, $this->roomRepo, $this->customerChannelRepository, $this->bizParamService, $this->pointService]
        );

        $this->service->shouldReceive('addPointOfAuthenticate')->never();
        $expect = $this->service->updateAuthIdCode($memberId, $idType, $idCode, $corpId);
        $this->assertTrue($expect === true);
    }
}

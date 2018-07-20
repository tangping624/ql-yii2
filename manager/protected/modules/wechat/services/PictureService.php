<?php
/**
 * 图文消息
 * User: fanwq
 * Date: 2015/5/5
 * Time: 16:32
 */
namespace app\modules\wechat\services;

use app\framework\oauth2\ClientHelper;
use app\framework\utils\Security;
use app\modules\wechat\repositories\PictureRepository;
use app\modules\ServiceBase;
use app\framework\biz\bizparam\BizParamAPI;
use app\framework\biz\cache\SiteCacheManager;
use app\framework\utils\WebUtility;

class PictureService extends ServiceBase
{
    private $_pictureRepository;
    const BIZ_CODE_ATTENTION_URL = 'AttentionUrl';

    public function __construct(PictureRepository $pictureRepository)
    {
        $this->_pictureRepository = $pictureRepository;
    }

    public function getPictureDetail($accountId, $aid, $type, $id = null)
    {
        $typeArr = ['reply', 'keyword_set', 'menu', 'welcome'];
        if (!in_array($type, $typeArr)) {
            return [];
        }
        $detail = $this->_pictureRepository->getDetailById($accountId, $type, $id);
        if (empty($detail) || !isset($detail['content'])) {
            return [];
        }

        $detail = json_decode($detail['content'], true);
        preg_match("/(\d{1,2})月(\d{1,2})日/", $detail['modified_on'], $dateArr);
        $detail['modified_on'] = date('Y') . '-' . $dateArr[1] . '-' . $dateArr[2];
        $detail['account_name'] = $this->getNameByAccountId($accountId);
        $detail['attention_url'] =$this->getAttentionUrl($accountId);
        $detail['title'] = $detail['body'] = '';
        foreach ($detail['articles'] as $arr) {
            if ($arr['id'] == $aid) {
                $detail['title'] = $arr['title'];
                $detail['body'] = $this->dealImgSrc($arr['body']);
                $detail['is_cover_showin_body'] = $arr['is_cover_showin_body'];
                $detail['cover_url'] = $arr['cover_url'];
            }
        }
        return $detail;
    }

    private function dealImgSrc($body)
    {
        preg_match_all('/<img.*?src=[\'"]([^>\'"]+)[\'"][^>]*>/i', $body, $match);

        if (!empty($match[1])) {
            $arr = [];
            $commonImageUrl = WebUtility::createBeautifiedUrl('/wechat/image/image') ;
            foreach ($match[1] as $src) {
                if (strpos($src, $commonImageUrl) !== false) {
                    continue;
                }

                $arr[$src] = $commonImageUrl . '?src=' . urlencode($src);
            }

            $srcs = array_keys($arr);
            $dests = array_values($arr);

            $body = str_replace($srcs, $dests, $body);
        }

        return $body;
    }

    public function getArticleDetail($id, $accountId)
    {
        $detail = $this->_pictureRepository->getArticleById($id);
        if (empty($detail)) {
            return [];
        }

        $detail['modified_on'] = date('Y-m-d', strtotime($detail['modified_on']));
        $detail['account_name'] = $this->getNameByAccountId($accountId);
        $detail['attention_url'] = $this->getAttentionUrl($accountId);
        $detail['body'] = $this->dealImgSrc($detail['body']);
        return $detail;
    }

    public function getNameByAccountId($accountId)
    {
        $accountName = $this->_pictureRepository->getNameByAccountId($accountId);
        return $accountName ? $accountName : '';
    }

    public function getAttentionUrl($accountId)
    {
        $attentionUrl = $this->_pictureRepository->getAttentionUrl($accountId);
        return $attentionUrl ? $attentionUrl : '';
//        $data = BizParamAPI::instance()->getBusinessParameters(BIZ_CODE_ATTENTION_URL, $accountId);
//        return isset($data[0]['value']) ? $data[0]['value'] : '';
    }

    public function genOriginalUrl($content, $type, $account_id, $id = null)
    {
        $content = json_decode($content, true);
        if (empty($content) || !isset($content['articles'])) {
            return json_encode($content);
        }
        foreach ($content['articles'] as $key => $arr) {
            $content['articles'][$key]['original_url'] = $this->getLinkUrl($arr['original_url'], $type, $arr['id'], $account_id, $id);
            $content['articles'][$key]['body'] = $this->recoveryImgSrc($arr['body']);
        }

        return json_encode($content);
    }

    public function getLinkUrl($originUrl, $type, $aid, $account_id, $id)
    {
        if ($originUrl) {
            return $originUrl;
        } 
        if ($id) {
            return WebUtility::createBeautifiedUrl('/wechat/picture/show',['aid'=> $aid ,'id'=> $id , 'type'=> $type , 'public_id'=> $account_id]) ;
        }
        return WebUtility::createBeautifiedUrl('/wechat/picture/show',['aid'=> $aid , 'type'=> $type , 'public_id'=> $account_id]) ;
    }

    public function getJssdksign($tenantCode, $accountId, $url)
    {
        $invokeUri = WebUtility::createBeautifiedUrl('api/weixin/jssdksign') ; 
        $restClient = new \app\framework\webService\RestClientHelper();
        \Yii::trace('call api: ' . $invokeUri);
        $signConfig = $restClient->invoke($invokeUri, ['accountId' => $accountId, 'url' => $url], 'GET');
        return $signConfig;
    }

    public function addPoint($articleId, $memberId, $point, $cause)
    {
        if (floatval($point) <= 0) {
            return;
        }
        if (empty($memberId)) {
            throw new \InvalidArgumentException('$memberId不能为空!');
        }

        if (empty($articleId)) {
            throw new \InvalidArgumentException('$articleId不能为空!');
        }

        $checkKey = md5($articleId . ':' . $memberId . ':sharepicture');
        $sharedKey = 'public_account:picture:share:' . $checkKey;
        $flag = mt_rand(1, 2^31);
        $lockSuccess = \Yii::$app->cache->add($sharedKey, $flag, 300);
        if($lockSuccess && \Yii::$app->cache->get($sharedKey) == $flag) {
            $calc = \app\framework\biz\point\PointTradeCalculator::member();
            $pointResult = $calc->share($memberId, $articleId);
            $tReader = \Yii::$container->get('app\framework\biz\tenant\TenantReaderInterface');
            $tenantCode = $tReader->getCurrentTenantCode();
            $memberCenterUrl = \app\framework\biz\cache\SiteCacheManager::getSiteUrl('MemberCenterSite');
            $url = $memberCenterUrl . '/' . $tenantCode . '/api/integral/update';
            $params = [
                'member_id' => $memberId,
                'acquired_time' => time(),
                'point' => $point,
                'cause' => $cause,
                'check_key' => $checkKey,
                'correlate_id' => $articleId,
                'source_id' => '39d08ba1-2108-165f-b954-428935c5c257',
                'credit_id' => $pointResult->creditId,
                'debit_id' => $pointResult->debitId
            ];

            //获取签名
            $sign = Security::getPointSign($params);
            $params["sign"] = $sign;

            $result = ClientHelper::post($url, $params);
            $result = json_decode($result);
            if (isset($result->errcode)) {
                if ($result->errcode == 3002) {
                    \Yii::trace('积分处理已经存在!' . json_encode($params));
                    return;
                }
                throw new \Exception("奖励积分失败，请截图保留以核实。错误：errcode:" . $result->errcode . " errmsg:" . $result->errmsg . ",with:" . json_encode($params));
            }
        }
    }

    private function recoveryImgSrc($body)
    {
        preg_match_all('/<img.*?src=[\'"]([^>\'"]+)[\'"][^>]*>/i', $body, $match);

        if (!empty($match[1])) {
            $arr = [];
            $commonImageUrl = '/wechat/image/image';
            foreach ($match[1] as $src) {
                if (strpos($src, $commonImageUrl) === false) {
                    continue;
                }

                $arr[$src] = urldecode(str_replace($commonImageUrl.'?src=', '', $src));
            }

            $srcs = array_keys($arr);
            $dests = array_values($arr);

            $body = str_replace($srcs, $dests, $body);
        }

        return $body;
    }

}

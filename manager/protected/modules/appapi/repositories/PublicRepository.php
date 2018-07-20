<?php
namespace app\modules\appapi\repositories;
use app\repositories\RepositoryBase;
use yii\db\Query;
use app\entities\merchant\SellerType;
use app\entities\lobby\MNews;
use app\entities\city\City;
use app\framework\db\PageResult;
use app\entities\advert\AAdvert;
class PublicRepository extends RepositoryBase {

    //获取子分类
    public function getType($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }

        return  SellerType::find()->select('id,name,icon,parent_id')->where(['parent_id'=>$id])->all();


    }

    //获取三条新鲜事
    public function getNews($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }

        return  MNews::find()->select('id,title,photo,content')->where(['type_id'=>$id])->orderBy('created_on DESC')->limit(3)->all();

    }

    //新鲜事列表
    public function getNewsList($skip,$limit,$id){
        if ($skip < 0 || $limit < 1) {
            throw new \InvalidArgumentException('$skip, $limit');
        }
        $where='';
        if(!empty($id)){
            $where.=" where type_id=:id";
            $params[':id'] = $id;
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS id,type_id,title,photo,source,member_id,content,created_on FROM m_news ".$where." ORDER BY created_on DESC LIMIT :skip,:limit";
        $params[':skip']=$skip;
        $params[':limit'] = $limit;
        $pageResult = new PageResult();
        $db = MNews::getDb();
        $cmd = $db->createCommand($sql,$params) ;
        $pageResult->items = $cmd->queryAll();
        $sql = "SELECT FOUND_ROWS() AS count;";
        $rows = $db->createCommand($sql)->queryAll();
        $pageResult->total = $rows[0]["count"];
        return $pageResult;
    }



    public function getCity(){
        $res =  City::find()->select("id,name as treeText,code")->where('parent_id is null')->orderBy('name asc')->asArray()->all();
       /* foreach ($res as $k => &$v) {
            $shi =  City::find()->select("id,name as treeText")->where([ 'parent_id' => $v['id']])->asArray()->all();
            foreach ($shi as &$item) {
                $item['pid'] = $v['id'];
                $item['pName'] = $v['treeText'];
            }
            $temp = [
                'id'=>'',
                'treeText'=>'全部',
                'pid' =>$v['id'],
                'pName'=>$v['treeText']
            ];
            array_unshift($shi,$temp);
            $v['childNode'] = $shi;
        }*/

        return $res;
    }

    //获取广告
    public function getAdvert($appcode)
    {
        $param[':appcode']=$appcode;
        $sql="SELECT a.id,a.title,c.original_url,c.thumb_url,c.link_url FROM a_advert a INNER JOIN a_adsense b ON a.adsenseid=b.id INNER JOIN a_images c ON c.fid=a.id  WHERE b.app_code=:appcode  ORDER BY b.grouporder desc";
        return AAdvert::getDb()->createCommand($sql, $param)->queryAll();
    }

}
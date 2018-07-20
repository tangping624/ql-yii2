<?php
 namespace app\modules\pub\repositories;
 use app\repositories\RepositoryBase;
 use app\entities\TBranner;
 
 
class BrannerRepository  extends RepositoryBase{
    
    public function getNavigation($appcode){

          if(empty($appcode)){
            throw new \InvalidArgumentException('$appcode对象不存在');
        }
        $sql = " select a.name,a.value,a.img_url,a.highlight_img_url,a.href,a.font_color,a.highlight_font_color  
            from t_banner a 
            inner join p_account b on b.package_type = a.package_type  and b.is_deleted=0
            where a.package_type=2 and a.is_deleted =0 and a.type=1 and a.appcode=:appcode order by a.sort ";
        return TBranner::getDb()->createCommand($sql,[':appcode'=>$appcode])->queryAll();
    }
}

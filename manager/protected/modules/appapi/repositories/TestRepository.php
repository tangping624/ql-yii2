<?php
 namespace app\modules\appapi\repositories;
 use app\modules\RepositoryBase;
 use app\entities\TUser;
class TestRepository extends RepositoryBase{
    public function getAllUser(){
        return TUser::find()->all();
    }
}

<?php

namespace data\service;


class Course extends BaseService
{
    
    public $Models;

    function __construct() {
        parent::__construct();
        $this->Models = model('Article');
    }


    /**
     * 获取列表
     * @return array
     */
    public function getrData()
    {

        $where = [];


        $data = $this->data;
        if (!empty($data['title'])) {
            $where[] = ['title', 'like', '%' . $data['title'] . '%'];
        }

        if (!empty($data['types'])) {
            $where[] = ['types', '=', $data['types']];
        }



        $where[] = ['type', '=', 1];

        $limit = $this->getLimit($data['limit']);
        $tablelist = $this->Models->getTableList([], '', $limit, [], $where);

        // 遍历获取分类
        for ($i = 0; $i < count($tablelist['data']); $i++) {
            $arr = model_s('Type', [['id', 'in',explode(',', $tablelist['data'][$i]['types'])]]);
            $str = [];
            foreach ($arr as $item) {
                array_push($str,$item['name']);
            }
            $tablelist['data'][$i]['types'] = join($str,'   ');

            // 获取评论数量
            $tablelist['data'][$i]['pl'] = model_s('Pinglun',[['aid','=', $tablelist['data'][$i]['id']]])->count();
        }



        return json(['code' => 0, 'msg' => "", "count" => $tablelist['total'], 'data' => $tablelist['data']]);

    }


    /**
     * 添加
     * @return mixed
     */
    public function Add()
    {
        $data = input();


        if (empty($data['types'])){
            $data['types'] = '';
        }else{
            $data['types'] = join($data['types'], ',');
        }



        $result = $this->Models->allowField(true)->save($data);
        if ($result) {
            return $this->SystemLogs::logwrite($this->session->getUserInfo('real_name') . "(" . $this->session->getUserInfo('uid') . ")" . "添加".NAMECRUD."：" . $data[FIELD] . " 成功", $result);
        }
        return $this->SystemLogs::logwrite($this->session->getUserInfo('real_name') . "(" . $this->session->getUserInfo('uid') . ")" . "添加".NAMECRUD."：" . $data[FIELD] . "失败", $result);
    }

    /**
     * 修改
     * @return mixed
     */
    public function Edit()
    {
        $data = input();



        if (empty($data['types'])){
            $data['types'] = '';
        }else{
            $data['types'] = join($data['types'], ',');
        }


        $result = $this->Models->update($data);
        if ($result) {
            return $this->SystemLogs::logwrite($this->session->getUserInfo('real_name') . "(" . $this->session->getUserInfo('uid') . ")" . "修改".NAMECRUD."：" . $data[FIELD] . " 成功", $result);
        }
        return $this->SystemLogs::logwrite($this->session->getUserInfo('real_name') . "(" . $this->session->getUserInfo('uid') . ")" . "修改".NAMECRUD."：" . $data[FIELD] . "失败", $result);
    }


}
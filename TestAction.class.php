<?php
/**Shein-销售分组
 * Created by PhpStorm.
 * User: hiro
 * Date: 16-5-25
 * Time: 下午3:30
 */

class SaleAction extends PublicAction{

    public $code_tab = '' ; //表格请求数据标识
    public $code_export = '' ; //导出请求标识
    public $count = 0;
    private $unselected = array('APP','app','PC','WEB','M','www','m','fr','es',
        'de','ru','us','ios','it','ar','android','rw','rwes','rwfr','rwm','rwios',
        'rwandroid','em','mmcm','mde','mfr','mes','mru','mit', 'mar','au','ec','eces',
        'ecfr','ecde','ecm','ecmes','ecmfr','ecmde','mus','rwde','rwmde','uk','mau');

    /**
     *销售额统计
     * @modify by jiang 2017-10-23 09:18 增加客单价
     * @modify by chenmin 2017-10-25 09:18 增加实际销售额
     */
    public function statistic_sale(){
        $tab_name = "dm_ord_sale_goods_stat";
        $db_type = "Mysql_WH";
        //销售额，成本，运费
        if(I('post.index')=='sale'){
            $column_name = "round(sum(pay_amt),2) as pay_amt";
            $column_name_trmnl_tp = "round(sum(a.pay_amt),2) as pay_amt";
            $title='订单销售额($)(105)';
            $field_arr='pay_amt';
        }elseif(I('post.index')=='goods_amt'){
            $column_name = "round(sum(goods_amt),2) as goods_amt";
            $column_name_trmnl_tp = "round(sum(a.goods_amt),2) as goods_amt";
            $title='产品销售额($)';
            $field_arr='goods_amt';
        }elseif(I('post.index')=='cost'){
            $column_name = "round(sum(cost_amt),2) as cost_amt";
            $column_name_trmnl_tp = "round(sum(a.cost_amt),2) as cost_amt";
            $title='成本(￥)';
            $field_arr='cost_amt';
        }elseif(I('post.index')=='shpp_cost'){
            $column_name = "round(sum(shpp_amt),2) as shpp_amt";
            $column_name_trmnl_tp = "round(sum(a.shpp_amt),2) as shpp_amt";
            $title='用户支付运费$';
            $field_arr='shpp_amt';
        }elseif(I('post.index')=='shpp_real_amt'){
            $column_name = "round(sum(shpp_real_amt),2) as shpp_real_amt";
            $column_name_trmnl_tp = "round(sum(a.shpp_real_amt),2) as shpp_real_amt";
            $title='实际物流费用¥';
            $field_arr='shpp_real_amt';
        }elseif(I('post.index')=='goods_cnt'){
            $column_name = "round(sum(goods_cnt),2) as goods_cnt";
            $column_name_trmnl_tp = "round(sum(a.goods_cnt),2) as goods_cnt";
            $title='销量';
            $field_arr='goods_cnt';
        }elseif(I('post.index') == 'order_cnt'){
            $column_name = "round(sum(order_cnt),2) as order_cnt";
            $column_name_trmnl_tp = "round(sum(a.order_cnt),2) as order_cnt";
            $title='订单数';
            $field_arr='order_cnt';
        }elseif(I('post.index') == 'customer_piece'){
            $column_name = "case when sum(order_cnt)>0 then round(sum(goods_cnt)/sum(order_cnt),2) else 0 end as customer_piece";
            $column_name_trmnl_tp = "case when sum(order_cnt)>0 then round(sum(goods_cnt)/sum(order_cnt),2) else 0 end as customer_piece";
            $title='客单价';
            $field_arr='customer_piece';
        }elseif(I('post.index') == 'real_pay_amt'){
            $column_name = "round(sum(real_pay_amt),2) as real_pay_amt";
            $column_name_trmnl_tp = "round(sum(a.real_pay_amt),2) as real_pay_amt";
            $title='实际销售额(商城)';
            $field_arr='real_pay_amt';
        }
        //model
        if( I('post.order')=='month' ){
            if(I('post.time_range')=='morning'){
                $tab_nm = $tab_name.'_mor_m';
                $model = M($tab_name."_mor_m",null,$db_type);
                $model_trmnl_tp = M("$tab_name"."_mor_m a,dw_pub_site_td b",null,$db_type);
            }else{
                $tab_nm = $tab_name.'_m';
                $model = M($tab_name."_m",null,$db_type);
                $model_trmnl_tp = M("$tab_name"."_m a,dw_pub_site_td b",null,$db_type);
            }
            $field_date = "date_format(concat(dt,'00'),'%Y-%m') as dt,";
            $field_date_trmnl_tp = "date_format(concat(a.dt,'00'),'%Y-%m') as dt,";
        } else if ( I('post.order')=='week' ) {
            if(I('post.time_range')=='morning'){
                $tab_nm = $tab_name.'_mor_w';
                $model = M($tab_name."_mor_w",null,$db_type);
                $model_trmnl_tp = M("$tab_name"."_mor_w a,dw_pub_site_td b",null,$db_type);
            }else{
                $tab_nm = $tab_name.'_w';
                $model = M($tab_name."_w",null,$db_type);
                $model_trmnl_tp = M("$tab_name"."_w a,dw_pub_site_td b",null,$db_type);
            }
            $field_date = "concat(substr(dt,'1',4),'-',substr(dt,'5',2)) as dt,";
            $field_date_trmnl_tp = "concat(substr(a.dt,'1',4),'-',substr(a.dt,'5',2)) as dt,";
        } elseif(I('post.order') == 'hour'){
            $tab_nm = $tab_name.'_h';
            $model = M($tab_name."_h",null,$db_type);
            $model_trmnl_tp = M("$tab_name"."_h a,dw_pub_site_td b",null,$db_type);
            $field_date = "date_format(concat(dt,'0000'),'%Y-%m-%d %H:%i:%s') as dt,";
            $field_date_trmnl_tp = "date_format(concat(a.dt,'0000'),'%Y-%m-%d %H:%i:%s') as dt,";
        } else{
            if(I('post.time_range') == 'morning'){
                $tab_nm = $tab_name.'_mor_d';
                $model = M($tab_name . "_mor_d", null, $db_type);
                $model_trmnl_tp = M("$tab_name" . "_mor_d a,dw_pub_site_td b", null, $db_type);
            }else {
                $tab_nm = $tab_name.'_d';
                $model = M($tab_name . "_d", null, $db_type);
                $model_trmnl_tp = M("$tab_name" . "_d a,dw_pub_site_td b", null, $db_type);
            }
            $field_date = "date(pay_dt) as dt,";
            $field_date_trmnl_tp = "date(a.pay_dt) as dt,";
        }
        //field

        $field = $field_date."site_id,$column_name";
        $field_trmnl_tp_l1 = $field_date_trmnl_tp."b.site_l1_id AS site_id,$column_name_trmnl_tp";
        $field_trmnl_tp_l2 = $field_date_trmnl_tp."b.site_l2_id AS site_id,$column_name_trmnl_tp";

        //array init
        $list_total = $list_trmnl_tp = $list_trmnl_tp_l2 = $list_site_id =array();
        $query_date=$this->get_query_date();
        if(I('post.order') =='day' && I('post.week_status') !=''){
            $map_tmp['day_of_week'] = I('post.week_status');
            $map_tmp['date_key'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
            $voList_dt_tmp = M('dm_date_td',null,'Mysql_WH')->where($map_tmp)->select();
            foreach ($voList_dt_tmp as $k){
                $map_dt[] = $k['date_key'];
            }
            $map_trmnl_tp_l2['a.pay_dt'] = $map_trmnl_tp_l1['a.pay_dt'] = $map['pay_dt']
                = array('IN',$map_dt);
            $group='pay_dt';
        }else{
            if(I('post.order')=='day'){
                $map_trmnl_tp_l2['a.pay_dt'] = $map_trmnl_tp_l1['a.pay_dt'] = $map['pay_dt']
                    = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
                $group='pay_dt';
            }else{
                $map_trmnl_tp_l2['a.dt'] = $map_trmnl_tp_l1['a.dt'] = $map['dt']
                    = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
                $group='dt';
            }
        }
        $site_tp = I('post.site_tp');
        $map['site_id'] = array('IN',$_SESSION['_SITELIST'][$site_tp]);
        $map_trmnl_tp_l2['a.site_tp'] = $map_trmnl_tp_l1['a.site_tp'] = $map['site_tp'] = I('post.site_tp');

        if(I('post.pay_mthd') != ''){
            $map_trmnl_web['a.pay_mthd'] = $map_trmnl_tp['a.pay_mthd'] = I('post.pay_mthd');
            $map['pay_mthd']=$map_trmnl_tp_l1['a.pay_mthd'] =$map_trmnl_tp_l2['a.pay_mthd']= I('post.pay_mthd');
        }

        //Common site_id query
        $list_site_id = $model->field($field)->where($map)->order('dt desc,site_id asc')->group($group.',site_id')->select();
        //Special site_id-total
        $trmnl_total_intersect = array_intersect(array('TOTAL'),$_SESSION['_SITELIST'][$site_tp]);
        if(!empty($trmnl_total_intersect)){
            if(in_array($tab_nm,$this-> tab_name_list_with_total)){
                unset($map['site_id']);
                $map['site_id'] = array('LIKE','%total%');
                $list_total=$model->field($field_date."'TOTAL' as site_id,$column_name")
                    ->where($map)->order('dt desc,site_id asc')->group($group)->select();
            }else{
                $list_total =$model->table($tab_nm.' a')
                    ->field($field_date_trmnl_tp."'TOTAL' as site_id,$column_name_trmnl_tp")
                    ->where($map)->order('dt desc')->group($group)->select();
            }
        }
        //TOTAL end
        //Special site_id-lv1
        $special_site_tp_l1 = $model
            ->query("select distinct site_l1_id from dw_pub_site_td where site_tp = '$site_tp' and site_l1_id is not null");

        foreach($special_site_tp_l1 as $k) {
            $trmnl_tp[] = $k['site_l1_id'];
        }
        //Computes the intersection of trmnl_tp
        $trmnl_tp_intersect_l1 = array_intersect($trmnl_tp,$_SESSION['_SITELIST'][$site_tp]);
        if(!empty($trmnl_tp_intersect_l1)){
            $map_trmnl_tp_l1['b.site_l1_id'] = array('IN',$trmnl_tp_intersect_l1);
            $list_trmnl_tp = $model_trmnl_tp->field($field_trmnl_tp_l1)->where($map_trmnl_tp_l1)
                ->where('a.site_id = b.site_id')->group('a.'.$group.',b.site_l1_id')
                ->order('dt desc,site_id asc')->select();
        }
        //lv1 end
        //Special site_id-lv2
        $special_site_tp_l2 = $model
            ->query("select distinct site_l2_id from dw_pub_site_td where site_tp = '$site_tp' and site_l2_id is not null");
        foreach($special_site_tp_l2 as $k) {
            $trmnl_tp_l2[] = $k['site_l2_id'];
        }
        //Computes the intersection of trmnl_tp
        $trmnl_tp_intersect_l2 = array_intersect($trmnl_tp_l2,$_SESSION['_SITELIST'][$site_tp]);
        if(!empty($trmnl_tp_intersect_l2)){
            $map_trmnl_tp_l2['b.site_l2_id'] = array('IN',$trmnl_tp_intersect_l2);
            $list_trmnl_tp_l2 = $model_trmnl_tp->field($field_trmnl_tp_l2)->where($map_trmnl_tp_l2)
                ->where('a.site_id = b.site_id')->group('a.'.$group.',b.site_l2_id')
                ->order('dt desc,site_id asc')->select();
        }
        //lv2 end
        $list =array_merge($list_site_id,$list_total,$list_trmnl_tp,$list_trmnl_tp_l2);
        
        $arr = array('title' => $title , 'dim1' => 'dt','dim2' => 'site_id','field' => $field_arr  ,'table' => 'ssa_site_core');
        $this->statistic_line($list,$arr,$this->unselected);
    }

    /*
     * 后台销售统计
     * modified by chenmin 20161208 14:12 换表 库存数指实时库存，最新的库存数
     */
    public function display_sku_sale(){
        C('DB_CASE_LOWER',true);
        $model= M('dm_ssa_sku_sale_goods_new_d',null,'Oracle_WH');
        $pay_start_date = ('' == I('post.pay_start_date') ) ? date("Ymd",mktime(0, 0, 0, date("m"), date("d")-7, date("Y"))) : str_replace('-','',I('post.pay_start_date'));
        $pay_end_date = ('' == I('post.pay_end_date') ) ? date("Ymd",time()) :  str_replace('-','',I('post.pay_end_date'));

        $map['dt'] = array(array('EGT',$pay_start_date),array('ELT',$pay_end_date),'and');

        $add_start_date = ('' == I('post.add_start_date') ) ? '2012-01-01' : I('post.add_start_date');
        $add_end_date = ('' == I('post.add_end_date') ) ? date("Y-m-d",time()) : I('post.add_end_date');
        $map['add_dt'] = array(array('EGT',$add_start_date),array('ELT',$add_end_date),'and');
        $map['site_tp']=I('post.site_tp');
        if (I('post.country') != '') {
            $map['lower(country_nm)'] = strtolower(I('post.country'));
        }

        if (I('post.goods_sn') != '') {
            $map['goods_sn'] = I('post.goods_sn');
        }
        //供应商模糊搜索
        if(I('post.supplier_nm')!=''){
            $map['supplier_nm']=array('like','%'.I('post.supplier_nm').'%');
        }
        $map['site_id'] = $this->get_query_site('dm_ssa_sku_sale_goods_new_d');
        if (I('post.type') != '') {
            $cat_model = M('pub_sku_category_td', 'dw_', 'Mysql_WH');
            $flag['sku_cate_id'] = I('post.type');
            $return_flag = $cat_model->field('distinct cate_lvl')->where($flag)->select();
            $tmp_str =I('post.type');
            if('1' == $return_flag[0]['cate_lvl']){
                $a_flag['sku_cate_1_id'] = I('post.type');
                $arr_flag = $cat_model->field('distinct sku_cate_2_id')->where($a_flag)->select();
                foreach($arr_flag as $key){
                    if($key['sku_cate_2_id'] != ''){
                        $tmp_str .= ','.$key['sku_cate_2_id'];
                    }
                }
            }
            $map['sku_cate_id'] = array('in',$tmp_str);
        }


        // $field = "max(sku_cate_nm) as item_category,goods_id,goods_sn,max(img_small_url) as goods_thumb,max(supplier_nm) as supplier_name,max(cost_amt) as cost,case max(special_price)  when 0 then max(price) else max(special_price) end as shop_price,sum(sale_cnt) as sale_num,sum(pv) as click_count,sum(ip_uv) as ip_uv, case when sum(ip_uv)>0 then round((sum(pay_user_cnt))*100/sum(ip_uv),2) else '0' end as conversion,case max(sale_flag) when 1 then '是' when 0 then '否' end as on_sale";
        $field = "sku_cate_nm as item_category,goods_id,goods_sn,
        max(img_small_url) as goods_thumb,max(supplier_nm) as supplier_name,
        max(cost_amt) as cost,
        case max(sepcial_price) when 0 then max(price) else max(sepcial_price) end as shop_price,
        sum(sale_cnt) as sale_num,sum(uv) as ip_uv,
        case max(sale_flag) when 1 then '是' when 0 then '否' end as on_sale";
        $field_all="a.item_category,a.goods_id,a.goods_sn,a.goods_thumb,a.supplier_name,a.cost,a.shop_price,a.sale_num,a.ip_uv,a.on_sale,b.storage_inventory";
        $order = 'sale_num';
        $group = 'sku_cate_nm,goods_id,goods_sn';
        $form = 'form_sku_sale';
        // $having = 'sale_num > 0' ;
        $having = 'sum(sale_cnt) > 0' ;
        //字段排序 默认主键
        if (isset($_REQUEST ['_order'])) {
            $order = $_REQUEST ['_order'];
        } else {
            $order = !empty($sortBy) ? $sortBy : $model->getPk();
        }
        //排序默认倒序排列
        //sort 0：倒序 非0：正序
        if (isset($_REQUEST ['_sort'])) {
            $sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
        } else {
            $sort = $asc ? 'asc' : 'desc';
        }

        //取得记录总数
        if ($group != '') {
            $subQuery = $model->field($field)->where($map)->group($group)->having($having)->select(false);
            $count = $model->table($subQuery.' a')->count('1');
        } else {
            $count = $model->where($map)->count('1');
        }

        if ($count>0) {
            import('@.ORG.Util.Page');
            //创建分页对象
            if ($_REQUEST ['listRows'] == 'undefined' || $_REQUEST ['listRows'] =='') {
                $listRows = '100';
            } else {
                $listRows = $_REQUEST ['listRows'];
            }
            $p = new Page($count, $listRows);

            $voList_tmp = $model->field($field)->where($map)->order('sum(sale_cnt) desc')->group($group)->having($having)->limit($p->firstRow.','.$p->listRows)->select(false);
            $model_real=M('dm_stk_goods_inventory_d',null,'Oracle_WH');
            $field_real="goods_sn,sum(gz_real_stk_cnt+am_east_real_stk_cnt+mmc_real_stk_cnt+eu_real_stk_cnt) as storage_inventory";
            $map_real['dt']=date('Ymd',strtotime('-1 day'));
            $list_real=$model_real->field($field_real)->where($map_real)->group('goods_sn')->select(false);
            //连表操作取实时库存
            $voList = $model->table($voList_tmp.' a')->join('left join'.$list_real." b on a.goods_sn = b.goods_sn")->field($field_all)->order('sale_num desc')->select();
            foreach ($map as $key => $val) {
                if (!is_array($val)) {
                    $p->parameter.="$key=".urldecode($val)."&";
                }
            }

            //分页显示
            $page = $p->show();
            //列表排序显示
            $sortImg = $sort; //排序图标
            $sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列' ;//排序提示
            $sort = $sort == 'desc' ? 1 : 0 ;//排序方式

            //模板赋值
            $this->assign('volist',$voList);
            $this->assign('sort',$sort);
            $this->assign('order',$order);
            $this->assign('sortImg',$sortImg);
            $this->assign('sortType',$sortAlt);
            $this->assign('page',$page);
            $this->assign('flag',$flag);
            $this->assign('site_tp',I('post.site_tp'));
            $this->assign('date_type','day');

            $data = $this->fetch('Echarts:'.$form);
            echo $data;
        }else{
            echo '当前天暂无数据';
        }
        return;
    }

    /**
     * 分类销售统计
     * @modify zjh 2016-09-30 下午5:00 从statistic修改并迁移过来
     */
    public function statistic_category_sales() {
        C('DB_CASE_LOWER',true);
        if( I('post.order')=='month' ){
            $dt = "to_char(to_date(dt,'yyyy-mm'),'yyyy-mm') as dt";
            $model = M('dm_ord_category_sales_m',null,'Oracle_WH');
        } else if ( I('post.order')=='week' ) {
            $dt = "substr(dt,1,4)||'-'||substr(dt,5,2) as dt";
            $model = M('dm_ord_category_sales_w',null,'Oracle_WH');
        } else if ( I('post.order')=='day' ){
            $dt = "to_char(to_date(dt,'yyyy-mm-dd'),'yyyy-mm-dd') as dt";
            $model = M('dm_ord_category_sales_d',null,'Oracle_WH');
        }
        $cate_sale_st = M('dm_ord_category_sales_d a,dw_pub_site_td b',null,'Oracle_WH');
        $query_date=$this->get_query_date();
        $map['dt'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
        $map['site_id'] = $this->get_query_site('dm_ord_category_sales_d');
        $map['sku_cate_nm']=array('exp',' is not null');
        $field = I('post.type');
        if ($field == 'sales_amt') {
            $title = '分类销量统计-'.I('post.site').'-交易额($)';
        } else {
            $title = '分类销量统计-'.I('post.site').'-购买数';
        }
        $field_sum = $dt.', sum(sales_cnt) as sales_cnt,sum(sales_amt) as sales_amt,sku_cate_nm';
        $list = $model->field($field_sum)
            ->where($map)->group('dt,sku_cate_nm')
            ->order('dt asc')->select();
        $arr = array(
            'title'=>$title,
            'dim1' => 'dt',
            'dim2' => 'sku_cate_nm',
            'field'=>$field,
            'echarts_type'=>'line', //图表类型 (可选｜默认line)
            'others'=>array( //其他参数设置 （可选）
                'grid'=>array(
                    'x'=>80,
                    'y'=>200,
                    'height'=>300
                )
            ),
            'domHeight'=>550 //调整dom参数 (可选)
        );
        $unselected = array('Scarves','Swimwear','Bodysuits','Underwear','Brooches','Belts','Pumps','Leggings','Sandals','Earrings','Makeup Brushes','Necklaces','Rings','Two-piece outfits','Sunglasses','Pendants','Flats','Hair Accessories','Plus Size Tops','Plus Size Dresses','Hats','Plus Size Blouses','Plus Size Pants','Socks&Tights');
        echo A('Public',null,true)->echarts3($list,$arr,$unselected);
    }
    /*
     * 分类销售统计 --export
     * author@hiro 2017-0703 09:28:32
     */
    public function export_category_sales(){
        C('DB_CASE_LOWER',true);
        if( I('post.order')=='month' ){
            $dt = "to_char(to_date(dt,'yyyy-mm'),'yyyy-mm') as dt";
            $model = M('dm_ord_category_sales_m',null,'Oracle_WH');
        } else if ( I('post.order')=='week' ) {
            $dt = "substr(dt,1,4)||'-'||substr(dt,5,2) as dt";
            $model = M('dm_ord_category_sales_w',null,'Oracle_WH');
        } else if ( I('post.order')=='day' ){
            $dt = "to_char(to_date(dt,'yyyy-mm-dd'),'yyyy-mm-dd') as dt";
            $model = M('dm_ord_category_sales_d',null,'Oracle_WH');
        }
        $query_date=$this->get_query_date();
        $map['dt'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
        $map['site_id'] = $this->get_query_site('dm_ord_category_sales_d');
        $map['sku_cate_nm']=array('exp',' is not null');
        $field = I('post.type');
        if ($field == 'sales_amt') {
            $title = '分类销量统计-'.I('post.site').'-交易额($)';
        } else {
            $title = '分类销量统计-'.I('post.site').'-购买数';
        }
        $field_sum = $dt.', sum(sales_cnt) as sales_cnt,sum(sales_amt) as sales_amt,sku_cate_nm';
        $list = $model->field($field_sum)
            ->where($map)->group('dt,sku_cate_nm')
            ->order('dt asc')->select();
        $list_sum = $model->field($dt.",sum(sales_cnt) as sales_cnt,sum(sales_amt) as sales_amt")
            ->where($map)->group('dt')
            ->order('dt asc')->select();
        foreach ($list_sum as $k){
            $sum_arr[$k['dt']] =$k[$field];
        }
        $date = $this->get_date();
        foreach ($list as $k){
            $cate_list_arr [$k['sku_cate_nm']]['cate'] = $k['sku_cate_nm'];
            foreach ($date as $k_dt=>$v_dt){
                $cate_list_arr [$k['sku_cate_nm']][$v_dt.'cnt'] = '0';
                $cate_list_arr [$k['sku_cate_nm']][$v_dt.'pnt'] = '0%';
            }
        }
        foreach ($list as $k){
            $cate_list_arr [$k['sku_cate_nm']]['cate'] = $k['sku_cate_nm'];
            $cate_list_arr [$k['sku_cate_nm']][$k['dt'].'cnt'] = $k[$field];
            $cate_list_arr [$k['sku_cate_nm']][$k['dt'].'pnt'] = (round($k[$field]*100 / $sum_arr[$k['dt']],2)).'%';
        }
        $article = array(' ');
        foreach ($date as $k =>$v){
            $article[] = $v;
            $article[] = '占比%';
        }
        $this->excel_export($cate_list_arr,$article,$title);
    }

    /*
     * 分类国家销售统计
     * modified @hiro 2016-09-28 14:19:54 首字母排序,other置后
     * modify @chenmin 20170509 分类下拉菜单优化
     * @modify zjh 2017-07-04 下午7:14 增加国家分类查询
     * @modify zjh 2017-07-10 下午4:23 增加导出
     */
    public function statistic_country_sales(){
        $showType = I('post.showType');
        C("DB_CASE_LOWER",true);
        $site_tp = $map['site_tp'] = I('post.site_tp');
        $map['site_id'] = $this->get_query_site('dm_ord_category_sales_d');
        $type = I('post.type');
        if( I('post.order')=='month' ){
            $dt = "to_char(to_date(dt,'yyyy-mm'),'yyyy-mm') as dt";
            $model = M('dm_ord_category_sales_m',null,'Oracle_WH');
        }elseif( I('post.order')=='week' ) {
            $dt = "substr(dt,1,4)||'-'||substr(dt,5,2) as dt";
            $model = M('dm_ord_category_sales_w',null,'Oracle_WH');
        }elseif( I('post.order')=='day' ){
            $dt = "to_char(to_date(dt,'yyyy-mm-dd'),'yyyy-mm-dd') as dt";
            $model = M('dm_ord_category_sales_d',null,'Oracle_WH');
        }
        $query_date=$this->get_query_date();
        $map['dt'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
        if(I('post.is_export') == '1'){
            ini_set('memory_limit','400M');
            set_time_limit(600);
            $where_site_id = $map['site_id'];
            unset($map['site_id']);
            $map['a.site_id'] = $where_site_id;

            $field = "dt,site_tp,a.site_id,
  sku_cate_nm as cate,
  case  shpp_country_nm when 'United States' then 'United States'
              when 'France' then 'France' when 'Germany' then 'Germany'
              when 'Saudi Arabia' then 'Saudi Arabia' when 'Spain' then 'Spain'
              when 'United Kingdom' then 'United Kingdom' when 'Australia' then 'Australia'
              when 'Italy' then 'Italy' when 'United Arab Emirates' then 'United Arab Emirates'
              when 'Kuwait' then 'Kuwait' when 'Canada' then 'Canada' when 'Russian Federation' then 'Russian Federation'
              when 'India' then 'India' else 'other' end as country,
  sum(sales_cnt) as sales_cnt,sum(sales_amt) as sales_amt
";
            $orderBy = "dt desc,site_tp,a.site_id";
            $group = "dt,site_tp,a.site_id,sku_cate_nm,case  shpp_country_nm when 'United States' then 'United States'
                              when 'France' then 'France' when 'Germany' then 'Germany'
                              when 'Saudi Arabia' then 'Saudi Arabia' when 'Spain' then 'Spain'
                              when 'United Kingdom' then 'United Kingdom' when 'Australia' then 'Australia'
                              when 'Italy' then 'Italy' when 'United Arab Emirates' then 'United Arab Emirates'
                              when 'Kuwait' then 'Kuwait' when 'Canada' then 'Canada' when 'Russian Federation' then 'Russian Federation'
                              when 'India' then 'India' else 'other' end";
            $data = $model->join("a left join dw_pub_site_td b on a.site_id = b.site_id")
                ->field($field)->where($map)->group($group)->order($orderBy)->select();
            foreach($data as &$v){
                unset($v['numrow']);
            }
            unset($v);
            $title = ['时间','主站点','分站点','分类','国家','购买数','销售额'];
            $this->excel_export($data,$title,'分类国家销售统计-明细导出');
            die;
        }



        if(!isset($_POST['cate'])){
            $sku_cate='total';
        }else{
            $sku_cate_tmp=str_replace('amp;','',I('post.cate'));
            $sku_cate=trim($sku_cate_tmp);
        }
        if($showType == 'cate'){
            if($sku_cate != "total"){
                //获取一级分类数据
                $sku_cate1 = M('dw_pub_sku_category_td',null,'Mysql_WH')->field('distinct sku_cate_1_nm')->where("site_tp = '$site_tp'")->select();
                $sku_cate1_array = array();
                foreach($sku_cate1 as $k =>$v){
                    if($v['sku_cate_1_nm'] != null || $v['sku_cate_1_nm'] != 'null'){
                        $sku_cate1_array[] = $v['sku_cate_1_nm'];
                    }
                }
                //判断
                if(in_array($sku_cate,$sku_cate1_array)){
                    $map['sku_cate_1_nm']  = $sku_cate;
                }else{
                    $map['sku_cate_2_nm']  = $sku_cate;
                }
            }
        }else{
            if(I('post.country') != '' ){
                $map['shpp_country_nm'] = I('post.country');
                $map['sku_cate_nm'] = array(
                    'neq',"''"
                );
            }
        }

        if($showType == 'cate'){
            $field = "$dt,case  shpp_country_nm when 'United States' then 'United States'
                when 'France' then 'France' when 'Germany' then 'Germany'
                when 'Saudi Arabia' then 'Saudi Arabia' when 'Spain' then 'Spain'
                when 'United Kingdom' then 'United Kingdom' when 'Australia' then 'Australia'
                when 'Italy' then 'Italy' when 'United Arab Emirates' then 'United Arab Emirates'
                when 'Kuwait' then 'Kuwait' when 'Canada' then 'Canada' when 'Russian Federation' then 'Russian Federation'
                when 'India' then 'India' else 'other' end as country,sum($type) as sales_cnt ";
            $list = $model->field($field)->where($map)->group("dt,case  shpp_country_nm when 'United States' then 'United States'
                when 'France' then 'France' when 'Germany' then 'Germany'
                when 'Saudi Arabia' then 'Saudi Arabia' when 'Spain' then 'Spain'
                when 'United Kingdom' then 'United Kingdom' when 'Australia' then 'Australia'
                when 'Italy' then 'Italy' when 'United Arab Emirates' then 'United Arab Emirates'
                when 'Kuwait' then 'Kuwait' when 'Canada' then 'Canada' when 'Russian Federation' then 'Russian Federation'
                when 'India' then 'India' else 'other' end")->order("dt asc,case  shpp_country_nm when 'United States' then 'United States'
                when 'France' then 'France' when 'Germany' then 'Germany'
                when 'Saudi Arabia' then 'Saudi Arabia' when 'Spain' then 'Spain'
                when 'United Kingdom' then 'United Kingdom' when 'Australia' then 'Australia'
                when 'Italy' then 'Italy' when 'United Arab Emirates' then 'United Arab Emirates'
                when 'Kuwait' then 'Kuwait' when 'Canada' then 'Canada' when 'Russian Federation' then 'Russian Federation'
                when 'India' then 'India' else 'other' end asc")->select();
            $list_sum=$model->field("$dt,sum($type) as sales_cnt")->where($map)->group('dt')->select();
        }else{
            // country
            $field = "$dt,sku_cate_nm as country,sum($type) as sales_cnt";
            $list = $model->field($field)->where($map)->group("dt,sku_cate_nm")->order("dt asc,country asc")->select();
            $list_sum=$model->field("$dt,sum($type) as sales_cnt")->where($map)->group('dt')->select();
        }
        $tooltip=array();
        foreach($list as $k=>$v){
            foreach($list_sum as $a=>$b){
                if($v['dt']==$b['dt']){
                    $tooltip[$v['dt']][$v['country']]=($b['sales_cnt']>0)?(round($v['sales_cnt']/$b['sales_cnt']*100,2).'%'):0;
                }
            }
        }
        $showTable = array(
            'cate'=>['div'=>450,'Y'=>20],
            'country'=>['div'=>600,'Y'=>30],
        );
        $this->assign('showType',$showType);
        $this->assign('divHeight',$showTable[$showType]['div']);
        $this->assign('chartY',$showTable[$showType]['Y']);
        $this->assign('tooltipPnt',JSON_encode($tooltip));
        foreach($list as $k){
            $legend_list[$k['country']]= $k['country'];
        }
        //other置后
        if($showType == 'cate'){
            unset($legend_list['other']);
            $legend_list['other'] = 'other';
        }
        $arr = array('title'=>'销售国家统计','dim1'=>'dt','dim2'=>'country','field'=>'sales_cnt','legend_list'=>$legend_list);
        $legend = array();
        $xAxis = array();
        $series = array();
        $member = array();
        $date = array();

        $date=$this->get_dates();
        foreach ($list as $k => $v) {
            array_push($legend, $v[$arr['dim2']]);
            $result[$v[$arr['dim2']]][$v[$arr['dim1']]] = $v[$arr['field']];
        }
        if (I('post.order')=='week') {
            $xAxis = $date[1];
            $date = $date[2];
            $this-> xAxis=$xAxis;
        } else {
            $this-> xAxis=$date;
        }

        foreach ($result as $k => $v) {
            foreach ($date as $d) {
                if (!empty($v[$d])) {
                    $member = $v[$d];
                }else{
                    $member = 0;
                }
                $series[$k][$d] = $member;
            }
        }
        //特殊处理-分类国家销售统计-首字母排序,other置后
        if($arr['legend_list'] !=''){
            $this-> legend=array_unique($arr['legend_list']);
        }else{
            $this-> legend=array_unique($legend);
        }
        unset($series['']);
        $this-> series=$series;
        $this-> xAxis=$date;
        $this-> title=$arr['title'];
        if ($arr['flag'] == 'line_bar') {
            $this-> line_field='转化率';
        }
        $data=$this->fetch('Echarts:echarts_bar_country_sales');

        $this->ajaxReturn(array('data'=>$data));
    }

    /*
     * 国家销售统计
     */
    public function display_sku_country(){
        C("DB_CASE_LOWER",true);
        $model = M('dm_pro_ctr_d',null,'Oracle_Amazon');
        $order = 'goods_cn';
        $form = 'form_sku_country';
        $field = "to_char(to_date(dt,'yyyy-mm-dd'),'yyyy-mm-dd') as dt,goods_sn,goods_id,country,goods_cn";
        if (I('post.goods_sn') !='') {
            $map['goods_sn'] = I('post.goods_sn');
        }
        $query_date=$this->get_query_date();
        $map['dt'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
        $map['site_tp'] = I('post.site_tp');
        $this->_list($model,$field,$map,$order,$form);
    }

    /**
     * 供应商销售统计 供应商分类获取
     */
    public function pub_supplier_get(){
        $model= M('dw_pty_supplier_td',null,'Mysql_WH');
        $sql = "select distinct cate_1_nm,cate_2_nm from dw_pty_supplier_td
              where cate_2_nm is not null and cate_1_nm is not null order by cate_1_nm,cate_2_nm";
        $data = $model->query($sql);
        foreach($data as $line){
            $list[$line['cate_1_nm']][] = $line['cate_2_nm'];
        }
        $this->ajaxReturn($list);
    }

    /**
     * 供应商销售统计-查询
     * 由新框架迁来的
     * @modify zjh 2016-09-29 上午11:21 优化逻辑 增加导出功能
     */
    public function statistic_sup_sale(){
        $site_tp = I('post.site_tp');           //total/detail/..
        $cate_1_nm = I('post.cate_1_nm');       //..
        $cate_2_nm = I('post.cate_2_nm');       //..

        if (I('post.order') == 'month') {
            $model = M('dm_supplier_sale_core_m', null, 'Mysql_WH');
            $field_dt = "date_format(concat(dt,'00'),'%Y-%m')";
        } else {
            if (I('post.order') == 'week') {
                $model = M('dm_supplier_sale_core_w', null, 'Mysql_WH');
                $field_dt = "concat(substr(dt,'1',4),'-',substr(dt,'5',2))";
            } else {
                $model = M('dm_supplier_sale_core_d', null, 'Mysql_WH');
                $field_dt = "date(dt)";
            }
        }
        $query_date = $this->get_query_date();
        //拼装查询条件
        $where = ' 1=1';
        $where .= " and dt >='" . $query_date['start_date'] . "'";
        $where .= " and dt <='" . $query_date['end_date'] . "'";
        $group = $field_dt;

        if($site_tp != 'total'){
            $where .= " and site_tp ='$site_tp'";
        }

        if($cate_1_nm == 'detail'){
            $field_cate_1 = "cate_1_nm,";
            $group .= ",cate_1_nm";
            if(in_array('detail',explode(',',$cate_2_nm))){
                $field_cate_2 = "cate_2_nm,";
                $group .= ",cate_2_nm";
                $where_sum = $where;
            }elseif($cate_2_nm == ''){
                $field_cate_2 = "'汇总' as cate_2_nm,";
                $where_sum = $where;
            }else{
                $field_cate_2 = "cate_2_nm,";
                $group .= ",cate_2_nm";
                $cate_2_nm = "'".implode("','",explode(',',$cate_2_nm))."'";
                $where_sum = $where;
                $where .= " and cate_2_nm in ($cate_2_nm)";
            }
        }elseif($cate_1_nm !='total'){
            $field_cate_1 = "cate_1_nm,";
            $group .= ",cate_1_nm";
            $where .= " and cate_1_nm = '$cate_1_nm'";
            if(in_array('detail',explode(',',$cate_2_nm))){
                $field_cate_2 = "cate_2_nm,";
                $group .= ",cate_2_nm";
                $where_sum = $where;
//                $where .= " and cate_1_nm = '$cate_1_nm'";
            }elseif($cate_2_nm == ''){
                $field_cate_2 = "'汇总' as cate_2_nm,";
                $where_sum = $where;
//                $where .= " and cate_1_nm = '$cate_1_nm'";
            }else{
                $field_cate_2 = "cate_2_nm,";
                $group .= ",cate_2_nm";
                $cate_2_nm = "'".implode("','",explode(',',$cate_2_nm))."'";
                $where_sum = $where;
//                $where .= " and cate_1_nm = '$cate_1_nm' and cate_2_nm in ($cate_2_nm)";
                $where .= " and cate_2_nm in ($cate_2_nm)";
            }
        }else{
            $field_cate_1 = "'汇总' as cate_1_nm,";
            $field_cate_2 = "'汇总' as cate_2_nm,";
            $where_sum = $where;
        }


        $field_sum = "$field_dt as dt,
                    sum(sku_cnt) as sku_cnt,
                    sum(new_sku_cnt) as new_sku_cnt,
                    sum(sale_cnt) as sale_cnt,
                    round(sum(pay_amt),2) as pay_amt,
                    round(sum(gmv_amt),2) as gmv_amt,
                    sum(uv) as uv";
        $field_d = "$field_dt as dt,
                    $field_cate_1 $field_cate_2
                    sum(sku_cnt) as sku_cnt,
                    sum(new_sku_cnt) as new_sku_cnt,
                    case when sum(pay_amt)>0 then round(sum(gmv_amt)/sum(pay_amt)*100,2)else 0 end as gmv_rate,
                    sum(sale_cnt) as sale_cnt,
                    round(sum(pay_amt),2) as pay_amt,
                    round(sum(gmv_amt),2) as gmv_amt,
                    case when sum(sale_cnt)>0 then round(sum(pay_amt)/sum(sale_cnt),2) else 0 end as avg_price,
                    if(sum(uv)>0,round(sum(pay_amt)/sum(uv),2),0) as uv_output";

        $order = 'dt desc,cate_1_nm desc,cate_2_nm';

        // 获取数据
        $dt_sum = $model->field($field_sum)->where($where_sum)->group($field_dt)->select();//按时间数据汇总
        $all_sum = $model->field($field_sum)->where($where)->select();//所有数据汇总


        $tmp_d = $model->field($field_d)->where($where)->group($group)->order($order)->select();//详细数据

//        echo $dt_sum,"<br/>",$all_sum,'<br/>',$tmp_d;
//die;
        if ($tmp_d == '') {
            echo '当前天暂无数据';
            return;
        }
        //数据合并及处理
        foreach ($dt_sum as $k) {
            $arr_sum[$k['dt']] = $k;
        }
        foreach ($tmp_d as $k) {
            $k['sale_rate'] = round($k['sale_cnt'] / $arr_sum[$k['dt']]['sale_cnt'] * 100, 2);
            $k['pay_rate'] = round($k['pay_amt'] / $arr_sum[$k['dt']]['pay_amt'] * 100, 2);
            $k['gmv_amt_rate'] = round($k['gmv_amt'] / $arr_sum[$k['dt']]['gmv_amt'] * 100, 2);
            $arr_d[] = $k;
        }
        if(I('post.order') == 'week' && I('post.is_export') != 1){
            foreach ($arr_d as $key=> $v) {
                $a = date('m/d', strtotime(substr($v['dt'], 0, -2) . 'W' . substr($v['dt'], -2)));
                $b = date("m/d", strtotime("$a +6 day"));
                $arr_d[$key]['dt'] = $v['dt'] . '周 <br>(' . $a . '-' . $b . ')';
            }
        }
        //加入合计信息
        $totalInfo = array(
            'dt'=>'汇总:',
            'cate_1_nm'=>'-',
            'cate_2_nm'=>'-',
            'sku_cnt'=>$all_sum[0]['sku_cnt'],
            'new_sku_cnt'=>$all_sum[0]['new_sku_cnt'],
            'sale_cnt'=>$all_sum[0]['sale_cnt'],//销量
            'sale_rate'=>'-',
            'pay_amt'=>$all_sum[0]['pay_amt'],//销售额
            'pay_rate'=>'-',
            'gmv_amt'=>$all_sum[0]['gmv_amt'],//毛利
            'gmv_rate'=>'-',
            'gmv_amt_rate'=>'-',
            'avg_price'=>$all_sum[0]['sale_cnt'] == 0?0:round($all_sum[0]['pay_amt']/$all_sum[0]['sale_cnt'],2),
            'uv_output'=>$all_sum[0]['uv'] ==0?0:round($all_sum[0]['pay_amt']/$all_sum[0]['uv'],2)
        );
        foreach($totalInfo as $key =>$v){
            if($v != '-'){
                $total[$key] = "<font color=\"red\">{$v}</font>";
            }else{
                $total[$key] = $v;
            }
        }
        array_unshift($arr_d,$total);
        //加入合计信息 end
        if(I('post.is_export') == '1'){
            //导出
            unset($arr_d[0]);
            array_unshift($arr_d,$totalInfo);
            $list = array();
            foreach($arr_d as $k=>$v){
                $list[$k][] = $v['dt'];
                $list[$k][] = $v['cate_1_nm'];
                $list[$k][] = $v['cate_2_nm'];
                $list[$k][] = $v['sku_cnt'];
                $list[$k][] = $v['new_sku_cnt'];
                $list[$k][] = $v['sale_cnt'];
                $list[$k][] = $v['sale_rate'];
                $list[$k][] = $v['pay_amt'];
                $list[$k][] = $v['pay_rate'];
                $list[$k][] = $v['gmv_amt'];
                $list[$k][] = $v['gmv_rate'];
                $list[$k][] = $v['gmv_amt_rate'];
                $list[$k][] = $v['avg_price'];
                $list[$k][] = $v['uv_output'];
            }
            unset($arr_d);
            $title = ['日期'.I('post.order'),'供应商一级分类','供应商二级','销售SKU数','上新SKU数','销量','销量占比','销售额','销售额占比','毛利','毛利占比','毛利率','平均售价','UV产出'];
            $this->excel_export($list,$title,'供应商销售统计');
            exit;
        }
        //ajax返回
        $this->assign('list', $arr_d);
        $this->assign('order', I('post.order'));
        $data = $this->fetch('Echarts:form_sup_sale');
        echo $data;
    }

    /*
     * 国家销售占比
     * modified @hiro 2016-09-28 10:52:16
     * 新增站点筛选&时间调整
     * modified by jianglang 20170607 15:43 增加分站点维度
     */
    public function display_country_sale_rate() {
        $map = "1=1 ";
        $model = M('dm_national_sales_account_m',null,'Mysql_WH');
        $order='dt desc,total_order desc';
        $field="date_format(concat(dt,'00'),'%Y-%m') as dt,
                country_nm,
                round(sum(total_sales),0) as total_sales,
                0 as sales_rate,
                round(sum(total_order),2) as total_order,
                0 as order_rate,
                sum(total_sku) as total_sku,
                0 as sku_rate";
        $field_total = "
                    date_format(concat(dt,'00'),'%Y-%m') as dt,
                    sum(total_sales) as total_sales,
                    sum(total_order) as total_order,
                    sum(total_sku) as total_sku
                    ";
        $site_id=$this->get_query_site('dm_national_sales_account_m');
        $site_str = implode('\',\'',$site_id[1]);
        $map .= " and site_id in('".$site_str."') ";
        $start_date = ('' == I('post.start_date') ) ? date("Y-m-d",mktime(0, 0, 0, date("m")-1, 1, date("Y"))) : I('post.start_date');
        $end_date = ('' == I('post.end_date') ) ? date("Y-m-d",mktime(0, 0, 0, date("m")-1, 1, date("Y"))) : I('post.end_date');
        $start_date = date('Ym', strtotime($start_date));
        $end_date = date('Ym', strtotime($end_date));
        $map .= "and dt>='".$start_date."' and dt<='".$end_date."'";
        $form = 'form_country_sale';
        $total_result = $model->field($field_total)->where($map)->group('dt')->select();
        foreach ($total_result as $v)
        {
            $total_arr[$v['dt']]['total_sales'] = $v['total_sales'];
            $total_arr[$v['dt']]['total_order'] = $v['total_order'];
            $total_arr[$v['dt']]['total_sku'] = $v['total_sku'];
        }
        $group = 'dt,country_nm';
        //取得记录总数
        if ($group != '') {
            $subQuery = $model->field($field)->where($map)->group($group)->select(false);
            $count = $model->table($subQuery.' a')->count('1');
        } else {
            $count = $model->where($map)->count('1');
        }
        if(I('post.is_export')!=1){
            if ($count>0) {
                import('@.ORG.Util.Page');
                //创建分页对象
                if ($_REQUEST ['listRows'] == 'undefined' || $_REQUEST ['listRows'] =='') {
                    $listRows = '100';
                } else {
                    $listRows = $_REQUEST ['listRows'];
                }
                $p = new Page($count, $listRows);
                $voList_tmp = $model->field($field)->where($map)->group($group)->order($order." ".$sort)->limit($p->firstRow.','.$p->listRows)->select();
                foreach ($voList_tmp as $k=>$v)
                {
                    $voList[$k]['dt'] = $v['dt'];
                    $voList[$k]['country_nm'] = $v['country_nm'];
                    $voList[$k]['total_sales'] = $v['total_sales'];
                    $voList[$k]['sales_rate'] = round($v['total_sales']/$total_arr[$v['dt']]['total_sales']*100,2);
                    $voList[$k]['total_order'] = $v['total_order'];
                    $voList[$k]['order_rate'] = round($v['total_order']/$total_arr[$v['dt']]['total_order']*100,2);
                    $voList[$k]['total_sku'] = $v['total_sku'];
                    $voList[$k]['sku_rate'] = round($v['total_sku']/$total_arr[$v['dt']]['total_sku']*100,2);
                }
                foreach ($map as $key => $val) {
                    if (!is_array($val)) {
                        $p->parameter.="$key=".urldecode($val)."&";
                    }
                }
                //分页显示
                $page = $p->show();
                //列表排序显示
                $sortImg = $sort; //排序图标
                $sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列' ;//排序提示
                $sort = $sort == 'desc' ? 1 : 0 ;//排序方式

                //模板赋值
                $this->assign('list',$voList);
                $this->assign('sort',$sort);
                $this->assign('order',$order);
                $this->assign('sortImg',$sortImg);
                $this->assign('sortType',$sortAlt);
                $this->assign('page',$page);

                $data = $this->fetch('Echarts:'.$form);
                echo $data;
            }else{
                echo'当前天暂无数据';
            }
            return;
        }else{
            $voList_tmp = $model->field($field)->join($join)->where($map)->group($group)->order($order." ".$sort)->select();
            foreach ($voList_tmp as $k=>$v)
            {
                $voList[$k]['dt'] = $v['dt'];
                $voList[$k]['country_nm'] = $v['country_nm'];
                $voList[$k]['total_sales'] = $v['total_sales'];
                $voList[$k]['sales_rate'] = round($v['total_sales']/$total_arr[$v['dt']]['total_sales']*100,2);
                $voList[$k]['total_order'] = $v['total_order'];
                $voList[$k]['order_rate'] = round($v['total_order']/$total_arr[$v['dt']]['total_order']*100,2);
                $voList[$k]['total_sku'] = $v['total_sku'];
                $voList[$k]['sku_rate'] = round($v['total_sku']/$total_arr[$v['dt']]['total_sku']*100,2);
            }
            //开始导出
            ini_set('memory_limit', '512M');
            header("Content-type:application/download");
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:filename=" . "国家销售占比导出.csv");
            echo "日期,国家,销售额,占总销售(%),订单数,占总订单(%),产品数,占总产品(%)\r\n";
            foreach ($voList as $key => $val) {
                $sku = iconv("UTF-8", "UTF-8", $val['dt']);
                $sku = $sku ? $sku : '-';
                $img_url = iconv("UTF-8", "UTF-8", $val['country_nm']);
                $img_url = $img_url ? $img_url : '-';
                $buyer_nm = iconv("UTF-8", "UTF-8", $val['total_order']);
                $buyer_nm = $buyer_nm ? $buyer_nm : '0';
                $cost_rmb_amt = iconv("UTF-8", "UTF-8", $val['order_rate']);
                $cost_rmb_amt = $cost_rmb_amt ? $cost_rmb_amt .'%': '0';
                $special_price = iconv("UTF-8", "UTF-8", $val['total_sales']);
                $special_price = $special_price ? $special_price : '0';
                $prd_add_time = iconv("UTF-8", "UTF-8", $val['sales_rate']);
                $prd_add_time = $prd_add_time ? $prd_add_time.'%' : '-';
                $prd_status_nm = iconv("UTF-8", "UTF-8", $val['total_sku']);
                $prd_status_nm = $prd_status_nm ? $prd_status_nm : '0';
                $prd_status_time = iconv("UTF-8", "UTF-8", $val['sku_rate']);
                $prd_status_time = $prd_status_time ? $prd_status_time.'%' : '0';
                echo "$sku,$img_url,$buyer_nm,$cost_rmb_amt,$special_price,$prd_add_time,$prd_status_nm,$prd_status_time\r\n";
            }
            exit;
        }
    }

    /*
     * 图片来源销售占比
     * modified by chenmin 20161013 17:00 新品上架时间修改
     */
    public function statistic_pic_sale_rate() {
        C("DB_CASE_LOWER",true);
        $ssa_site_core = M('dm_ssa_site_core_d', null, 'Mysql_WH');
        $field_conversion = "date(dt) as dt,'转化率' as img_src_tp_nm,case when ip_uv > 0 then round(pay_order_user_cnt/ip_uv,4)*100 else 0 end as sale_cnt";
        $ord_goods_pic_sr_d = M('dm_ord_goods_pic_sr_d',null,'Oracle_Amazon');
        $query_date=$this->get_query_date();
        $map['dt'] = $condition['dt']=array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
        $map['site_tp'] = I('post.site_tp');
        $condition['site_id'] = $this->get_query_site('dm_ssa_site_core_d');
        $photo = I('post.photo');
        if(I('post.photo')!=''){
            $map['sy_user_nm'] = array('in',$photo);
        }
        $ps = I('post.ps');
        if(I('post.ps')!=''){
            $map['ps_user_nm'] = array('in',$ps);
        }
        if (I('post.supplier') != '') {
            $map['supplier_name'] = array('like','%'.I('post.supplier').'%');
        }

        $add_start_time=substr(I('post.add_start_date'),0,10);
        if(I('post.add_end_date')==''){
            $add_end_time=substr(I('post.add_end_date'),0,10);
        }else{
            $add_end_time=substr(date("Y-m-d",strtotime(I('post.add_end_date'))+86400),0,10);
        }
        if($add_start_time!='' and $add_end_time!=''){
            $map['frst_sale_time']=array(array('EGT',$add_start_time),array('ELT',$add_end_time),'and');
        }elseif($add_start_time!='' and $add_end_time==''){
            $map['frst_sale_time']=array("EGT",$add_start_time);
        }elseif($add_start_time=='' and $add_end_time!=''){
            $map['frst_sale_time']=array("ELT",$add_end_time);
        }
        $list = $ord_goods_pic_sr_d->field("to_char(to_date(dt,'yyyy-mm-dd'),'yyyy-mm-dd') as dt,img_src_tp_nm,sum(sale_cnt) as sale_cnt")->where($map)->group('dt,img_src_tp_nm')->select();
        $list_conversion = $ssa_site_core->field($field_conversion)->where($condition)->order('dt desc')->select();
        foreach ($list_conversion as $k => $v) {
            array_push($list, $v);
        }
        $arr = array('title'=>'图片来源销售占比','dim1' => 'dt','dim2' => 'img_src_tp_nm', 'field'=>'sale_cnt', 'flag' => 'line_bar');
        $this->statistic_bar($list,$arr);
    }

    /*
     * 图片来源在售占比 -SKU
     * modified by chenmin 20161013 17:00 上架时间修改
     */
    public function statistic_pic_sku_sale_rate() {
        $ssa_site_core = M('dm_ssa_site_core_d', null, 'Mysql_WH');
        $field_conversion = "date(dt) as dt,'转化率' as img_src_tp_nm,case when ip_uv > 0 then round(pay_order_user_cnt/ip_uv,4)*100 else 0 end as onsale_cnt";
        $pub_goods_sale_pic_d = M('dm_pub_goods_sale_pic_d',null,'Mysql_WH');
        $query_date=$this->get_query_date();
        $map['dt']= $condition['dt']= array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
        $map['site_tp'] = I('post.site_tp');
        $condition['site_id'] = $this->get_query_site('dm_ssa_site_core_d');
        $photo = I('post.photo');
        if(I('post.photo') !=''){
            $map['sy_user_nm'] = array('in',$photo);
        }
        $ps = I('post.ps');
        if(I('post.ps')!=''){
            $map['ps_user_nm'] = array('in',$ps);
        }
        if (I('post.supplier') != '') {
            $map['supplier_name'] = array('like','%'.I('post.supplier').'%');
        }
        $add_start_time=substr(I('post.add_start_date'),0,10);
        if(I('post.add_end_date')==''){
            $add_end_time=substr(I('post.add_end_date'),0,10);
        }else{
            $add_end_time=substr(date("Y-m-d",strtotime(I('post.add_end_date'))+86400),0,10);
        }
        if($add_start_time!='' and $add_end_time!=''){
            $map['frst_sale_time']=array(array('EGT',$add_start_time),array('ELT',$add_end_time),'and');
        }elseif($add_start_time!='' and $add_end_time==''){
            $map['frst_sale_time']=array("EGT",$add_start_time);
        }elseif($add_start_time=='' and $add_end_time!=''){
            $map['frst_sale_time']=array("ELT",$add_end_time);
        }
        $list = $pub_goods_sale_pic_d->field('date(dt) as dt,img_src_tp_nm,sum(onsale_cnt) as onsale_cnt')->where($map)->group('dt,img_src_tp_nm')->select();
        $list_conversion = $ssa_site_core->field($field_conversion)->where($condition)->order('dt desc')->select();
        foreach ($list_conversion as $k => $v) {
            array_push($list, $v);
        }
        $arr = array('title'=>'图片来源在售占比','dim1' => 'dt','dim2' => 'img_src_tp_nm', 'field'=>'onsale_cnt', 'flag' => 'line_bar');
        $this->statistic_bar($list,$arr);
    }

    /*
     * 客单价-未拆单
     * modify @hiro 2016-07-19 14:48:04
     */
    public function statistic_presale_per_order() {
        $tab_name = "dm_ssa_site_core";
        $db_type = "Mysql_WH";
        $column_name = "case when unsplit_order_cnt >0 then round(pay_amt/unsplit_order_cnt,4) else 0 end as presale_conversion";
        $column_name_trmnl_tp = "case when sum(a.unsplit_order_cnt) >0 then round(sum(a.pay_amt)/sum(a.unsplit_order_cnt),4) else 0 end as presale_conversion";
        $list = $this->statistic_query_output($tab_name,$db_type,$column_name,$column_name_trmnl_tp);
        $arr = array('title' =>'客单价-未拆单' , 'dim1' => 'dt','dim2' => 'site_id','field' => 'presale_conversion'  ,'table' => 'dm_ssa_site_core');
        $this->statistic_line($list,$arr, $this->unselected);
    }

    /*
     * 客单价
     * 加按周和按月的0-7点数据
     * modified by chenmin
     * 20160826 16:30
     */
    public function statistic_sale_per_order(){
        $tab_name = "dm_ssa_site_core";
        $db_type = "Mysql_WH";
        $column_name = "case when pay_order_cnt>0 then round(pay_amt/pay_order_cnt,2) else 0 end as chck_pnt";
        $column_name_trmnl_tp = "case when sum(a.pay_order_cnt)>0 then round(sum(a.pay_amt)/sum(a.pay_order_cnt),2) else 0 end as chck_pnt";
        $list = $this->statistic_query_output($tab_name,$db_type,$column_name,$column_name_trmnl_tp);
        $arr = array('title' =>'客单价' , 'dim1' => 'dt','dim2' => 'site_id','field' => 'chck_pnt'  ,'table' => 'dm_ssa_site_core');
        $this->statistic_line($list,$arr, $this->unselected);
    }

    /*
    * 平均访问金额统计
    */
    public function statistic_sale_per_ip(){
        $tab_name = "dm_ssa_site_core";
        $db_type = "Mysql_WH";
        $column_name = "case when ip_uv>0 then round(pay_amt/ip_uv,2) else 0 end as chck_pnt";
        $column_name_trmnl_tp = "case when sum(a.ip_uv)>0 then round(sum(a.pay_amt)/sum(a.ip_uv),2) else 0 end as chck_pnt";
        $list = $this->statistic_query_output($tab_name,$db_type,$column_name,$column_name_trmnl_tp);
        $arr = array('title' =>'平均访问金额统计' , 'dim1' => 'dt','dim2' => 'site_id','field' => 'chck_pnt'  ,'table' => 'dm_ssa_site_core');
        $this->statistic_line($list,$arr, $this->unselected);
    }


    /*
     * 订单金额分析
     */
    public function statistic_order_sale(){
        $tab_name = "dm_ord_stat_amt";
        $db_type = "Mysql_WH";
        $column_name = "sum(ord_cnt) as ord_cnt";
        $column_name_trmnl_tp = "sum(a.ord_cnt) as ord_cnt";

        //model
        if (I('post.order') == 'month') {
            $model = M($tab_name . "_m", null, $db_type);
            $model_trmnl_tp = M("$tab_name" . "_m a,dw_pub_site_td b", null, $db_type);
            $field_date = "date_format(concat(dt,'00'),'%Y-%m') as dt,";
            $field_date_trmnl_tp = "date_format(concat(a.dt,'00'),'%Y-%m') as dt,";
        } else if (I('post.order') == 'week') {
            $model = M($tab_name . "_w", null, $db_type);
            $model_trmnl_tp = M("$tab_name" . "_w a,dw_pub_site_td b", null, $db_type);
            $field_date = "concat(substr(dt,'1',4),'-',substr(dt,'5',2)) as dt,";
            $field_date_trmnl_tp = "concat(substr(a.dt,'1',4),'-',substr(a.dt,'5',2)) as dt,";
        } elseif (I('post.order') == 'hour') {
            $model = M($tab_name . "_h", null, $db_type);
            $model_trmnl_tp = M("$tab_name" . "_h a,dw_pub_site_td b", null, $db_type);
            $field_date = "date_format(concat(dt,'0000'),'%Y-%m-%d %H:%i:%s') as dt,";
            $field_date_trmnl_tp = "date_format(concat(a.dt,'0000'),'%Y-%m-%d %H:%i:%s') as dt,";
        } else {
            if (I('post.time_range') == 'morning') {
                $model = M($tab_name . "_mor_d", null, $db_type);
                $model_trmnl_tp = M("$tab_name" . "_mor_d a,dw_pub_site_td b", null, $db_type);
            } else {
                $model = M($tab_name . "_d", null, $db_type);
                $model_trmnl_tp = M("$tab_name" . "_d a,dw_pub_site_td b", null, $db_type);
            }
            $field_date = "date(dt) as dt,";
            $field_date_trmnl_tp = "date(a.dt) as dt,";
        }
        //field
        $field = $field_date . "site_id,$column_name";
        $field_trmnl_tp = $field_date_trmnl_tp . "b.trmnl_tp AS site_id,$column_name_trmnl_tp";
        $field_trmnl_web = $field_date_trmnl_tp . "'WEB' AS site_id,$column_name_trmnl_tp";


        //array init
        $list_trmnl_web = $list_trmnl_tp = $list_site_id = array();
        $query_date = $this->get_query_date();


        $site_tp = I('post.site_tp');
        $map['pay_amt_tp'] = I('post.sale_num');
        $map['site_id'] = array('IN', $_SESSION['_SITELIST'][$site_tp]);
        $map_trmnl_web['a.site_tp'] = $map_trmnl_tp['a.site_tp'] = $map['site_tp'] = I('post.site_tp');
        $map_trmnl_web['a.dt'] = $map_trmnl_tp['a.dt'] = $map['dt']
            = array(array('EGT', $query_date['start_date']), array('ELT', $query_date['end_date']), 'and');

        //Common site_id query
        $list_site_id = $model->field($field)->where($map)->group('dt,site_id')->order('dt desc,site_id asc')->select();
        //Special site_id like PC,APP&M
        $trmnl_tp = array('PC', 'APP', 'M');
        //Computes the intersection of trmnl_tp
        $trmnl_tp_intersect = array_intersect($trmnl_tp, $_SESSION['_SITELIST'][$site_tp]);
        if (!empty($trmnl_tp_intersect)) {
            $map_trmnl_tp['b.trmnl_tp'] = array('IN', $trmnl_tp_intersect);
            $list_trmnl_tp = $model_trmnl_tp->field($field_trmnl_tp)->where($map_trmnl_tp)
                ->where('a.site_id = b.site_id')->group('a.dt,b.trmnl_tp')
                ->order('dt desc,site_id asc')->select();
        }
        //Single one WEB
        if (in_array('WEB', $_SESSION['_SITELIST'][$site_tp])) {
            $trmnl_web = array('PC', 'M');
            $map_trmnl_web['b.trmnl_tp'] = array('IN', $trmnl_web);
            $list_trmnl_web = $model_trmnl_tp->field($field_trmnl_web)->where($map_trmnl_web)
                ->where('a.site_id = b.site_id')->group('a.dt')
                ->order('dt desc,site_id asc')->select();
        }
        $list = array_merge($list_site_id, $list_trmnl_tp, $list_trmnl_web);
        $arr = array('title' => '订单金额分析', 'dim1' => 'dt', 'dim2' => 'site_id', 'field' => 'ord_cnt', 'table' => 'dm_ssa_site_core');
        $this->statistic_line($list, $arr, $this->unselected);
    }


    /**
     * 首单售罄率
     * modify @chenmin 20170428 10:20 加了一个上架时间为空的汇总统计
     * modify @chenmin  20171201 14:15 优化
     */
    public function sale_out_rate(){
        $cat_model = M('dw_pub_sku_category_td', null, 'Mysql_WH');
        if(I('post.type') != '') {
            $flag['sku_cate_id'] = I('post.type');
            $return_flag = $cat_model->field('distinct cate_lvl')->where($flag)->select();
            $tmp_str =I('post.type');
            if('1' == $return_flag[0]['cate_lvl']){
                $a_flag['sku_cate_1_id'] = I('post.type');
                $arr_flag = $cat_model->field('distinct sku_cate_2_id')->where($a_flag)->select();
                foreach($arr_flag as $key){
                    if($key['sku_cate_2_id'] != ''){
                        $tmp_str .= ','.$key['sku_cate_2_id'];
                    }
                }
            }
            $map['sku_cate_id'] = array('in',$tmp_str);
        }
        //处理生成条件
        if(I('prd_add_time_egt') !=''){
            $map['prd_add_time'][] =array('EGT',I('prd_add_time_egt'));
        }
        if(I('prd_add_time_elt') !='') {
            $map['prd_add_time'][] =array('ELT',I('prd_add_time_elt'));
        }
        if(I('goods_sns') !='') {
            $map['goods_sn'] =I('goods_sns');
        }
        if(I('sale_flags') == '0'){
            $map['sale_flag']=0;
        }
        if(I('sale_flags') == '1'){
            $map['sale_flag']=1;
        }
        if(I('recycle_flags') == '0'){
            $map['recycle_flag']=0;
        }
        if(I('recycle_flags') == '1'){
            $map['recycle_flag']=1;
        }
        if(I('buyer_nm') !='') {
//            $map['buyer_nm'] =I('buyer_nms');
            $map['buyer_nm']=array('in',I('post.buyer_nm'));
        }
        if(I('prd_status_nms') !='0'){
            if (I('prd_status_nms') != '未完成') {
                $map['prd_status_nm'] = I('prd_status_nms');
            } else {
                $map['prd_status_nm'] = [" not in ", ['已完成','退货']];
            }
        }
        if(I('pre_sale_time_elt') !='' and I('pre_sale_time_egt') !='' and I('post.null_sta')=='false'){
            $map['pre_sale_time'][] =array('EGT',I('pre_sale_time_egt'));
            $map['pre_sale_time'][] =array('ELT',I('pre_sale_time_elt'));
        }elseif(I('pre_sale_time_elt') !=''  and I('post.null_sta')=='false'){
            $map['pre_sale_time'][] =array('ELT',I('pre_sale_time_elt'));
        }elseif(I('pre_sale_time_egt') !='' and I('post.null_sta')=='false'){
            $map['pre_sale_time'][] =array('EGT',I('pre_sale_time_egt'));
        }elseif(I('pre_sale_time_elt')!='' or I('pre_sale_time_egt')!='' and I('post.null_sta')=='true'){
            $map['pre_sale_time']='qfef';
        }elseif(I('pre_sale_time_elt')=='' and I('pre_sale_time_egt')=='' and I('post.null_sta')=='true'){
            $map['pre_sale_time']=array('EXP',' is null');
        }
        if(I('pre_sale_day_elt') !='') {
            $map['pre_sale_day'][] =array('ELT',I('pre_sale_day_elt'));
        }
        if(I('pre_sale_day_egt') !='') {
            $map['pre_sale_day'][] =array('EGT',I('pre_sale_day_egt'));
        }
        //处理售罄
        if(I('out_day_type') == 'sale'){
            if(I('out_day_elt') !='') {
                $map['sale_out_day'][] =array('ELT',I('out_day_elt'));
            }
            if(I('out_day_egt') !='') {
                $map['sale_out_day'][] =array('EGT',I('out_day_egt'));
            }
            if(I('out_day_egt') =='' && I('out_day_elt') ==''){
                $where = array();
                $where['sale_out_day'][] =array('EGT',0);
                $where['sale_out_day'][] =array('ELT',0);
                $where['_logic'] = 'or';
                $map['_complex'][] =$where;
            }
        }elseif(I('out_day_type') == 'pre'){
            if(I('out_day_elt') !='') {
                $map['pre_out_day'][] =array('ELT',I('out_day_elt'));
            }
            if(I('out_day_egt') !='') {
                $map['pre_out_day'][] =array('EGT',I('out_day_egt'));
            }
            if(I('out_day_egt') =='' && I('out_day_elt') ==''){
                $where = array();
                $where['pre_out_day'][] =array('EGT',0);
                $where['pre_out_day'][] =array('ELT',0);
                $where['_logic'] = 'or';
                $map['_complex'][] =$where;
            }
        }else{
            if(I('out_day_elt') !='' && I('out_day_egt') =='') {
                $where = array();
                $where['pre_out_day'] =array('ELT',I('out_day_elt'));
                $where['sale_out_day'] =array('ELT',I('out_day_elt'));
                $where['_logic'] = 'or';
                $map['_complex'][] =$where;
            }
            if(I('out_day_elt') =='' && I('out_day_egt') !='') {
                $where = array();
                $where['pre_out_day'] =array('EGT',I('out_day_egt'));
                $where['sale_out_day'] =array('EGT',I('out_day_egt'));
                $where['_logic'] = 'or';
                $map['_complex'][] =$where;
            }
            if(I('out_day_elt') !='' && I('out_day_egt') !='') {
                $where = array();
                $where['pre_out_day'] =array(array('EGT',I('out_day_egt')),array('ELT',I('out_day_elt')));
                $where['sale_out_day'] =array(array('EGT',I('out_day_egt')),array('ELT',I('out_day_elt')));
                $where['_logic'] = 'or';
                $map['_complex'][] =$where;
            }
        }
        $map['site_tp']=I('post.site_tp');
        if(I('post.supplier_nm')!=''){
            $map['supplier_nm']=array('like',I('post.supplier_nm').'%');
        }
        if(I('post.cate_1_nm')!=''){
            $map['cate_1_nm'] = I('post.cate_1_nm');
        }
        $model = M('dm_pub_sku_frst_ord_out_d',null,'Mysql_WH');
        $field = "sku_cate_nm,goods_sn,case when img_url LIKE 'images%'
        then 'http://img.'||if(site_tp='emmastyle','makemechic',site_tp)||'.com/'||img_url
        else img_url end as img_url,
        case when sale_flag='1' then '√' else '×' end as sale_flag,
        case when recycle_flag='1' then '√' else '×' end as recycle_flag,
        buyer_nm,cost_rmb_amt,price,special_price,prd_add_time,prd_status_nm,
        prd_status_time,to_days(prd_status_time)-to_days(prd_add_time) as days,
        prd_user_name,cate_1_nm,supplier_cd,stored_cnt,pre_sale_time,pre_sale_day,sale_out_day,pre_out_day,sale_cnt7,total_cnt,stk_cnt,
        case when sum(uv)>0 then concat(round(site_total_cnt/uv*100,2),'%') else 0 end as cvs_rate,sum(pcs_cnt) as pcs_cnt,
        sum(tobe_onshelf_cnt) as tobe_onshelf_cnt,sum(shpp_stk_cnt) as shpp_stk_cnt,max(produce_team) as produce_team";
        $form = "form_sale_out_rate";
        $group ="goods_sn";
        //字段排序 默认主键
        if (isset($_REQUEST ['sortBy'])) {
            $order = $_REQUEST ['sortBy'];
        } else {
            $order = !empty($sortBy) ? $sortBy : $model->getPk();
        }
        //排序默认倒序排列
        //sort 0：倒序 非0：正序
        if (isset($_REQUEST ['_sort'])) {
            $sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
        } else {
            $sort = $asc ? 'asc' : 'desc';
        }
        //取得记录总数
        if ($group != '') {
            $subQuery = $model->field($field)->where($map)->group($group)->select(false);
            $count = $model->table($subQuery.' a')->count('1');
        } else {
            $count = $model->where($map)->count('1');
        }
        if(I('post.is_export')=='1'){
            //导出开始
            $epage='800';
            header("Content-type:application/octet-stream");
            header("Accept-Ranges:bytes");
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename=" . "首单售罄率导出".".xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            $title=['分类','SKU','图片','上架状态','回收站状态','买手','成本','原价','特价','首单下单时间','首单跟单状态','跟单状态最新时间','生产时效','加工厂','一级渠道','供应商编号','入库数量(总)','上架时间(分)','上架天数(分)','首单售罄天数(总)','预测售罄天数(总)','近7天销量(分)','总销量(总)','库存','转化率(分)','待采购数量','待上架数量','在途数量','生产组别'];
            if(!empty($title)){
                foreach($title as $k=>$v){
                    $title[$k]=iconv('UTF-8','GB2312',$v);
                }
                $title=implode("\t",$title);
                echo "$title\n";
            }
            //获取分页数
            $lim=floor($count/$epage);
            if ($lim == 0) {
                $lim = 1;
                $epage = $count;
            }
            for($time = 0;$time <= $lim; $time++) {
                //设置limit限制
                $now = $time*$epage;
                if ($lim - $time < 1) {
                    $epage = $count - $now;
                }
                $limit = "$now,$epage";
                //目标数组
                $data = $model->field($field)->where($map)->group($group)->order('goods_sn desc')->limit($limit)->select();
                if(!empty($data)){
                    foreach($data as $key=>$val){
                        unset($val['numrow']);
                        foreach ($val as $ck => $cv) {
                            $data[$key][$ck]=iconv("UTF-8", "GB2312", $cv);
                        }
                        $data[$key]=implode("\t", $data[$key]);
                    }
                    echo implode("\n",$data);
                    echo ("\n");
                }
            }
            exit;
        }else{
            if ($count>0) {
                import('@.ORG.Util.Page_o');
                //创建分页对象
                if ($_REQUEST ['listRows'] == 'undefined' || $_REQUEST ['listRows'] =='') {
                    $listRows = '100';
                } else {
                    $listRows = $_REQUEST ['listRows'];
                }
                $p = new Page($count, $listRows);
                $voList = $model->field($field)->where($map)->group($group)->order($order." ".$sort)->limit($p->firstRow.','.$p->listRows)->select();
                foreach ($map as $key => $val) {
                    if (!is_array($val)) {
                        $p->parameter.="$key=".urldecode($val)."&";
                    }
                }
                //分页显示
                $page = $p->show();
                //列表排序显示
                $sortImg = $sort; //排序图标
                $sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列' ;//排序提示
                $sort = $sort == 'desc' ? 1 : 0 ;//排序方式

                //模板赋值
                $this->assign('list',$voList);
                $this->assign('sort',$sort);
                $this->assign('order',$order);
                $this->assign('sortImg',$sortImg);
                $this->assign('sortType',$sortAlt);
                $this->assign('page',$page);

                $data = $this->fetch('Echarts:'.$form);
                echo $data;
            }else{
                echo'当前天暂无数据';
            }
            return;
        }
    }
    /**
     * 首单售罄率 Total
     */
    public function total_statistic(){
        //处理生成条件
        if(I('prd_add_time_egt') !=''){
            $map['prd_add_time'][] =array('EGT',I('prd_add_time_egt'));
        }
        if(I('prd_add_time_elt') !='') {
            $map['prd_add_time'][] =array('ELT',I('prd_add_time_elt'));
        }
        if(I('goods_sns') !='') {
            $map['goods_sn'] =I('goods_sns');
        }
        if(I('sale_flags') == '0'){
            $map['sale_flag']=0;
        }
        if(I('sale_flags') == '1'){
            $map['sale_flag']=1;
        }
        if(I('recycle_flags') == '0'){
            $map['recycle_flag']=0;
        }
        if(I('recycle_flags') == '1'){
            $map['recycle_flag']=1;
        }

        if(I('post.buyer_nm') !='') {
            $map['buyer_nm'] =array('in',I('post.buyer_nm'));
        }
        if(I('prd_status_nms') !='0'){
            $map['prd_status_nm'] =I('prd_status_nms');
        }

        if(I('pre_sale_time_elt') !='' and I('pre_sale_time_egt') !='' and I('post.null_sta')=='false'){
            $map['pre_sale_time'][] =array('EGT',I('pre_sale_time_egt'));
            $map['pre_sale_time'][] =array('ELT',I('pre_sale_time_elt'));
        }elseif(I('pre_sale_time_elt') !=''  and I('post.null_sta')=='false'){
            $map['pre_sale_time'][] =array('ELT',I('pre_sale_time_elt'));
        }elseif(I('pre_sale_time_egt') !='' and I('post.null_sta')=='false'){
            $map['pre_sale_time'][] =array('EGT',I('pre_sale_time_egt'));
        }elseif(I('pre_sale_time_elt')!='' or I('pre_sale_time_egt')!='' and I('post.null_sta')=='true'){
            $map['pre_sale_time']='qfef';
        }elseif(I('pre_sale_time_elt')=='' and I('pre_sale_time_egt')=='' and I('post.null_sta')=='true'){
            $map['pre_sale_time']=array('EXP',' is null');
        }
        if(I('pre_sale_day_elt') !='') {
            $map['pre_sale_day'][] =array('ELT',I('pre_sale_day_elt'));
        }
        if(I('pre_sale_day_egt') !='') {
            $map['pre_sale_day'][] =array('EGT',I('pre_sale_day_egt'));
        }
        //处理售罄
        if(I('out_day_type') == 'sale'){
            if(I('out_day_elt') !='') {
                $map['sale_out_day'][] =array('ELT',I('out_day_elt'));
            }
            if(I('out_day_egt') !='') {
                $map['sale_out_day'][] =array('EGT',I('out_day_egt'));
            }
            if(I('out_day_egt') =='' && I('out_day_elt') ==''){
                $where = array();
                $where['sale_out_day'][] =array('EGT',0);
                $where['sale_out_day'][] =array('ELT',0);
                $where['_logic'] = 'or';
                $map['_complex'][] =$where;
            }
        }elseif(I('out_day_type') == 'pre'){
            if(I('out_day_elt') !='') {
                $map['pre_out_day'][] =array('ELT',I('out_day_elt'));
            }
            if(I('out_day_egt') !='') {
                $map['pre_out_day'][] =array('EGT',I('out_day_egt'));
            }
            if(I('out_day_egt') =='' && I('out_day_elt') ==''){
                $where = array();
                $where['pre_out_day'][] =array('EGT',0);
                $where['pre_out_day'][] =array('ELT',0);
                $where['_logic'] = 'or';
                $map['_complex'][] =$where;
            }
        }else{
            if(I('out_day_elt') !='' && I('out_day_egt') =='') {
                $where = array();
                $where['pre_out_day'] =array('ELT',I('out_day_elt'));
                $where['sale_out_day'] =array('ELT',I('out_day_elt'));
                $where['_logic'] = 'or';
                $map['_complex'][] =$where;
            }
            if(I('out_day_elt') =='' && I('out_day_egt') !='') {
                $where = array();
                $where['pre_out_day'] =array('EGT',I('out_day_egt'));
                $where['sale_out_day'] =array('EGT',I('out_day_egt'));
                $where['_logic'] = 'or';
                $map['_complex'][] =$where;
            }
            if(I('out_day_elt') !='' && I('out_day_egt') !='') {
                $where = array();
                $where['pre_out_day'] =array(array('EGT',I('out_day_egt')),array('ELT',I('out_day_elt')));
                $where['sale_out_day'] =array(array('EGT',I('out_day_egt')),array('ELT',I('out_day_elt')));
                $where['_logic'] = 'or';
                $map['_complex'][] =$where;
            }
        }
        $map['site_tp']=I('post.site_tp');
        if(I('post.supplier_nm')!=''){
            $map['supplier_nm']=array('like',I('post.supplier_nm').'%');
        }
        //获取统计数据
        $model = M('dm_pub_sku_frst_ord_out_d',null,'Mysql_WH');
        $sku_count = $model->where($map)->count('goods_sn');
        $sku_sale_count = $model->where($map)->where("sale_flag =1")->count('goods_sn');
        $sku_recycle_count = $model->where($map)->where("recycle_flag =1")->count('goods_sn');
        //跟单状态

        $option = array(
            'field'=>'prd_status_nm'
        );
        $res = $model->distinct('prd_status_nm')->select($option);
        foreach($res as $k=> $v){
            //$prd_status_nm[]=$v['prd_status_nm'];
            $prd_status_nm_count[$v['prd_status_nm']] = $model->where($map)->where("prd_status_nm ='".$v['prd_status_nm']."'")->count('prd_status_nm');
        }

        //售罄时间段内（总销量统计）
        $sale_out_day_count20 = $model->where($map)->where("sale_out_day <= '20' and total_cnt >= '100'")->count();
        $pre_out_day_count20 = $model->where($map)->where("pre_out_day <= '20'")->count();
        $sale_out_day_count20_30 = $model->where($map)->where("sale_out_day > '20' and sale_out_day <='30' and total_cnt>= '100'")->count();
        $pre_out_day_count20_30 = $model->where($map)->where("pre_out_day > '20' and pre_out_day <='30'")->count();
        $sale_out_day_count30_60 = $model->where($map)->where("sale_out_day > '30' and sale_out_day <='60' and total_cnt>= '100'")->count();
        $pre_out_day_count30_60 = $model->where($map)->where("pre_out_day > '30' and pre_out_day <='60'")->count();
        $sale_out_day_count60_90 = $model->where($map)->where("sale_out_day > '60' and sale_out_day <='90' and total_cnt>= '100'")->count();
        $pre_out_day_count60_90 = $model->where($map)->where("pre_out_day > '60' and pre_out_day <='90'")->count();
        $sale_out_day_count90 = $model->where($map)->where("sale_out_day > '90' and total_cnt>='100'")->count();
        $pre_out_day_count90 = $model->where($map)->where("pre_out_day > '90'")->count();

        $sale_cnt7_count = $model->where($map)->sum('sale_cnt7');
        $total_cnt_count = $model->where($map)->sum('total_cnt');
        $stk_cnt_count = $model->where($map)->sum('stk_cnt');

        $clk_cnt_count = $model->where($map)->sum('clk_cnt');

        $evg_cvs_rate = round(($total_cnt_count/$clk_cnt_count)*100,2).'%';

        //拼接统计数据 返回前台展示


        $backspace = "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
        $total_statistc_string = "";

        $total_statistc_string .= "<b>SKU总数：</b>$sku_count<br/>";
        $total_statistc_string .= "<b>上架状态为√SKU总数：</b>$sku_sale_count<br/>";
        $total_statistc_string .= "<b>回收站状态为√SKU总数：</b>$sku_recycle_count<br/>";
        $total_statistc_string .= "<b>跟单状态:</b> <br/>";
        foreach($prd_status_nm_count as $k=>$v){
            if($k != ''){
                $total_statistc_string .= "$backspace $k:$v<br/>";
            }
        }
        $total_statistc_string .= "<b>售罄时间段内（SKU数量统计）</b> <br/>";
        $total_statistc_string .= "$backspace 售罄天数：（ 实际售罄（SKU数） ： 预测售罄 ）<br/>";
        $total_statistc_string .= "$backspace 小于等于20天的：（$sale_out_day_count20 : $pre_out_day_count20 ）<br/>";
        $total_statistc_string .= "$backspace 大于20天，小于等于30天的：($sale_out_day_count20_30 ：$pre_out_day_count20_30 )<br/>";
        $total_statistc_string .= "$backspace 大于30天，小于等于60天的：($sale_out_day_count30_60 ： $pre_out_day_count30_60 )<br/>";
        $total_statistc_string .= "$backspace 大于60天，小于等于90天的：($sale_out_day_count60_90 ： $pre_out_day_count60_90 )<br/>";
        $total_statistc_string .= "$backspace 大于90天的：($sale_out_day_count90 ：$pre_out_day_count90 )<br/>";
        $total_statistc_string .= "<b>近7天销售量总和：</b>$sale_cnt7_count<br/>";
        $total_statistc_string .= "<b>总销量：</b>$total_cnt_count<br/>";
        $total_statistc_string .= "<b>总库存：</b>$stk_cnt_count<br/>";
        $total_statistc_string .= "<b>平均转化率：</b>$evg_cvs_rate<br/>";

        echo $total_statistc_string;
        exit;
    }

    /*
     * 订单运费对比
     */
    public function statistic_ship_lvl(){
        C('DB_CASE_LOWER',true);
        $ord_order_shpp = M('dm_ord_order_shpp_lvl_d',null,'Oracle_WH');
        $query_date=$this->get_query_date_ora();
        $condition['dt'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
        $condition['date_type']=I('post.order');
        $condition['site_tp']=I('post.site_tp');
        $condition['site_id'] = $this->get_query_site('dm_ord_order_shpp_lvl_d');
        $list_temp=$ord_order_shpp->field('dt,sum(shpp_free_cnt) as free,sum(shpp_7_cnt) as "$7",sum(shpp_15_cnt) as "$15" ')->where($condition)->group('dt')->order('dt desc')->select();

        $i=0;
        foreach ($list_temp as $k ){
            unset($k['numrow']);
            foreach($k as $v =>$b){
                if($v != 'dt'){
                    $list[$i]['dt'] = $k['dt'];
                    $list[$i]['val'] = $b;
                    $list[$i]['site'] = $v;
                    $i++;
                }
            }
        }
        $arr = array('title'=>'订单运费对比','dim1'=>'dt','dim2' => 'site','field' => 'val');
        $this->statistic_line($list,$arr,null);

    }

    /*
     * 货币支付对比
     */
    public function statistic_sale_currency(){
        $where = ' 1=1';
        C('DB_CASE_LOWER',true);
        $ord_sale_currency_d = M('dm_ord_sale_currency_d',null,'Oracle_WH');
        $query_date=$this->get_query_date_ora();
        $where .= " and dt >= '".$query_date['start_date']."'";
        $where .= " and dt <= '".$query_date['end_date']."'";
        $where .= " and site= '".I('post.site_tp')."'";
        $site_id_list = $this->get_query_site('dm_ord_sale_currency_d');
        foreach($site_id_list as $k=>$v){
            $site_id=implode("','",$v);
        }
        $where .=" and site_from in ('".$site_id."')";
        $currency_list="('USD','EUR','GBP','AUD','RUB','SAR','CAD','NOK','SEK','MXN')";
        $where .= " and currency in".$currency_list;
        $where .= " and date_type = '".I('post.order')."'";
        $field="currency,dt,sum(total_order_num) as total_order_num";
        $list=$ord_sale_currency_d->field($field)->where($where)->group('dt,currency')->order('dt desc')->select();
        $arr = array('title'=>'货币支付对比','dim1'=>'dt','dim2' => 'currency','field' => 'total_order_num');
        $this->statistic_line($list,$arr,null);
    }


    /*
     * 订单产品分析
     */
    public function statistic_order_goods(){
        $tab_name = "dm_ord_stat_goods";
        $db_type = "Mysql_WH";
        $column_name = "sum(ord_cnt) as ord_cnt";
        $column_name_trmnl_tp = "sum(a.ord_cnt) as ord_cnt";

        //model
        if (I('post.order') == 'month') {
            $tab_nm = $tab_name.'_m';
            $model = M($tab_name . "_m", null, $db_type);
            $model_trmnl_tp = M("$tab_name" . "_m a,dw_pub_site_td b", null, $db_type);
            $field_date = "date_format(concat(dt,'00'),'%Y-%m') as dt,";
            $field_date_trmnl_tp = "date_format(concat(a.dt,'00'),'%Y-%m') as dt,";
        } else if (I('post.order') == 'week') {
            $tab_nm = $tab_name.'_w';
            $model = M($tab_name . "_w", null, $db_type);
            $model_trmnl_tp = M("$tab_name" . "_w a,dw_pub_site_td b", null, $db_type);
            $field_date = "concat(substr(dt,'1',4),'-',substr(dt,'5',2)) as dt,";
            $field_date_trmnl_tp = "concat(substr(a.dt,'1',4),'-',substr(a.dt,'5',2)) as dt,";
        } elseif (I('post.order') == 'hour') {
            $tab_nm = $tab_name.'_h';
            $model = M($tab_name . "_h", null, $db_type);
            $model_trmnl_tp = M("$tab_name" . "_h a,dw_pub_site_td b", null, $db_type);
            $field_date = "date_format(concat(dt,'0000'),'%Y-%m-%d %H:%i:%s') as dt,";
            $field_date_trmnl_tp = "date_format(concat(a.dt,'0000'),'%Y-%m-%d %H:%i:%s') as dt,";
        } else {
            if (I('post.time_range') == 'morning') {
                $tab_nm = $tab_name.'_mor_d';
                $model = M($tab_name . "_mor_d", null, $db_type);
                $model_trmnl_tp = M("$tab_name" . "_mor_d a,dw_pub_site_td b", null, $db_type);
            } else {
                $tab_nm = $tab_name.'_d';
                $model = M($tab_name . "_d", null, $db_type);
                $model_trmnl_tp = M("$tab_name" . "_d a,dw_pub_site_td b", null, $db_type);
            }
            $field_date = "date(dt) as dt,";
            $field_date_trmnl_tp = "date(a.dt) as dt,";
        }
        //field
        $field = $field_date . "site_id,$column_name";
        $field_trmnl_tp = $field_date_trmnl_tp . "b.trmnl_tp AS site_id,$column_name_trmnl_tp";
        $field_trmnl_web = $field_date_trmnl_tp . "'WEB' AS site_id,$column_name_trmnl_tp";


        //array init
        $list_trmnl_web = $list_trmnl_tp = $list_site_id = array();
        $query_date = $this->get_query_date();


        $site_tp = I('post.site_tp');
        $map_trmnl_web['a.sale_cnt_tp'] = $map_trmnl_tp['a.sale_cnt_tp'] =$map['sale_cnt_tp'] = I('post.goods_num');
        $map['site_id'] = array('IN', $_SESSION['_SITELIST'][$site_tp]);
        $map_trmnl_web['a.site_tp'] = $map_trmnl_tp['a.site_tp'] = $map['site_tp'] = I('post.site_tp');
        $map_trmnl_web['a.dt'] = $map_trmnl_tp['a.dt'] = $map['dt']
            = array(array('EGT', $query_date['start_date']), array('ELT', $query_date['end_date']), 'and');

        //Common site_id query
        $list_site_id = $model->field($field)->where($map)->group('dt,site_id')->order('dt desc,site_id asc')->select();

        //Special site_id-total
        $trmnl_total_intersect = array_intersect(array('TOTAL'),$_SESSION['_SITELIST'][$site_tp]);
        if(!empty($trmnl_total_intersect)){
            if(in_array($tab_nm,$this-> tab_name_list_with_total)){
                unset($map['site_id']);
                $map['site_id'] = array('LIKE','%total%');
                $list_total=$model->field($field_date."'TOTAL' as site_id,$column_name")
                    ->where($map)->order('dt desc,site_id asc')->select();
            }else{
                $list_total =$model->table($tab_nm.' a')
                    ->field($field_date_trmnl_tp."'TOTAL' as site_id,$column_name_trmnl_tp")
                    ->where($map)->order('dt desc')->group('dt')->select();
            }
        }

        //Special site_id like PC,APP&M
        $trmnl_tp = array('PC', 'APP', 'M');
        //Computes the intersection of trmnl_tp
        $trmnl_tp_intersect = array_intersect($trmnl_tp, $_SESSION['_SITELIST'][$site_tp]);
        if (!empty($trmnl_tp_intersect)) {
            $map_trmnl_tp['b.trmnl_tp'] = array('IN', $trmnl_tp_intersect);
            $list_trmnl_tp = $model_trmnl_tp->field($field_trmnl_tp)->where($map_trmnl_tp)
                ->where('a.site_id = b.site_id')->group('a.dt,b.trmnl_tp')
                ->order('dt desc,site_id asc')->select();
        }
        //Single one WEB
        if (in_array('WEB', $_SESSION['_SITELIST'][$site_tp])) {
            $trmnl_web = array('PC', 'M');
            $map_trmnl_web['b.trmnl_tp'] = array('IN', $trmnl_web);
            $list_trmnl_web = $model_trmnl_tp->field($field_trmnl_web)->where($map_trmnl_web)
                ->where('a.site_id = b.site_id')->group('a.dt')
                ->order('dt desc,site_id asc')->select();
        }
        $list = array_merge($list_site_id, $list_trmnl_tp, $list_trmnl_web,$list_total);
        $arr = array('title' => '订单产品分析', 'dim1' => 'dt', 'dim2' => 'site_id', 'field' => 'ord_cnt', 'table' => 'dm_ssa_site_core');
        $this->statistic_line($list, $arr, $this->unselected);
    }

    /**
     * 滞销产品统计
     * modified @hiro 2017-05-10 11:39:39 add 商品层次
     * modify @chenmin 20170704 10:30 库存总数改为各分仓库库存数
     */
    public function display_outstock_ana(){
        C("DB_CASE_LOWER",true);
        $warehouse=I('post.warehouse');
        $model= M('dm_stk_unmarketable_sku_d',null,'Oracle_WH');
        $pay_start_date = ('' == I('post.cost_amt_l') ) ? '0': I('post.cost_amt_l');
        $pay_end_date = ('' == I('post.cost_amt_m') ) ? '999999': I('post.cost_amt_m');

        $map['cost_rmb_amt'] = array(array('EGT',$pay_start_date),array('ELT',$pay_end_date),'and');
        $map['site_tp']=I('post.site_tp');
        $map['warehouse']=$warehouse;
        if (I('post.type') != '') {
            $map['sale_flag'] = I('post.type');
        }
        if (I('post.goods_sn') != '') {
            $map['goods_sn'] = array('like','%'.I('post.goods_sn').'%');
        }
        if (I('post.supplier_nm') != '') {
            $map['supplier_nm'] = array('like','%'.I('post.supplier_nm').'%');
        }
        if(I('post.buyer_nm')!='total'){
            if(I('post.buyer_nm')!='null'){
                $map['buyer_nm']=I('post.buyer_nm');
            }else{
                $map['buyer_nm']=array('EXP','is null');
            }
        }
        if(I('post.group_nm')!='total'){
            if(I('post.group_nm')!= 'null'){
                $map['group_nm']=I('post.group_nm');
            }else {
                $map['group_nm']=array('EXP',' is null');
            }
        }

        $field = "goods_sn,img_url,supplier_nm,buyer_nm,group_nm,layer_nm,sum(inv_cnt) as inv_cnt,price,sepcial_price,cost_rmb_amt,delay_day,sale_flag,recycle_flag";
        $group = 'goods_sn,img_url,supplier_nm,buyer_nm,group_nm,layer_nm,price,sepcial_price,cost_rmb_amt,delay_day,sale_flag,recycle_flag';
        $form = 'form_outstock_ana';

        //字段排序 默认主键
        if (isset($_REQUEST ['order'])) {
            $order = $_REQUEST ['order'];
        } else {
            $order = !empty($sortBy) ? $sortBy : $model->getPk();
        }
        //排序默认倒序排列
        //sort 0：倒序 非0：正序
        if (isset($_REQUEST ['_sort'])) {
            $sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
        } else {
            $sort = $asc ? 'asc' : 'desc';
        }

        //取得记录总数
        if ($group != '') {
            $subQuery = $model->field($field)->where($map)->group($group)->select(false);
            $count = $model->table($subQuery.' a')->count('1');
        } else {
            $count = $model->where($map)->count('1');
        }
        if ($count>0) {
            import('@.ORG.Util.Page');
            //创建分页对象
            if ($_REQUEST ['listRows'] == 'undefined' || $_REQUEST ['listRows'] =='') {
                $listRows = '100';
            } else {
                $listRows = $_REQUEST ['listRows'];
            }
            $p = new Page($count, $listRows);

            if($order=='inv_cnt'){
                //库存总数
                $temp_voList_ = $model->field($field)->where($map)->group($group)->select(false);
                unset($map['warehouse']);
                $size_all_tmp=$model->field('goods_sn,sum(inv_cnt) as inv_cnt')->where($map)->group('goods_sn')->order($order." ".$sort)->select(false);
                $temp_voList = $model->table($temp_voList_)->join(' t left join '.$size_all_tmp." p on t.goods_sn=p.goods_sn")
                    ->field(" t.goods_sn,t.img_url,supplier_nm,buyer_nm,group_nm,layer_nm,sum(t.inv_cnt) AS inv_cnt,sum(p.inv_cnt) AS size_cnt,price,sepcial_price,
               cost_rmb_amt,delay_day,sale_flag,
               recycle_flag")->group("t.goods_sn,t.img_url,t.supplier_nm,t.buyer_nm,t.group_nm,t.layer_nm,t.price,t.sepcial_price,t.cost_rmb_amt,t.delay_day,t.sale_flag,
               t.recycle_flag")->order("sum(p.".$order.") ".$sort)->limit($p->firstRow.','.$p->listRows)->select();
            }elseif($order=='inv_cnt_b' or $order=='inv_cnt_ce' or $order=='inv_cnt_cw' or $order=='inv_cnt_d'){
                $temp_voList_ = $model->field($field)->where($map)->group($group)->select(false);
                unset($map['warehouse']);
                if($order=='inv_cnt_b'){
                    $map['warehouse']='B';
                }elseif($order=='inv_cnt_ce'){
                    $map['warehouse']='CE';
                }elseif($order=='inv_cnt_cw'){
                    $map['warehouse']='CW';
                }elseif($order=='inv_cnt_d'){
                    $map['warehouse']='D';
                }
                $size_all_tmp_=$model->field('goods_sn,warehouse,sum(inv_cnt) as inv_cnt')->where($map)->group('goods_sn,warehouse')->select(false);
                $temp_voList = $model->table($temp_voList_)->join(' t left join '.$size_all_tmp_." p on t.goods_sn=p.goods_sn")
                    ->field("t.goods_sn,t.img_url,supplier_nm,buyer_nm,group_nm,layer_nm,sum(t.inv_cnt) AS inv_cnt,nvl(sum(p.inv_cnt),0) AS size_cnt,price,sepcial_price,
               cost_rmb_amt,delay_day,sale_flag,
               recycle_flag")->group("t.goods_sn,t.img_url,t.supplier_nm,t.buyer_nm,t.group_nm,t.layer_nm,t.price,t.sepcial_price,t.cost_rmb_amt,t.delay_day,t.sale_flag,
               t.recycle_flag")->order("size_cnt ".$sort)->limit($p->firstRow.','.$p->listRows)->select();
            }else{
                $temp_voList = $model->field($field)->where($map)->group($group)->order($order." ".$sort)->limit($p->firstRow.','.$p->listRows)->select();
            }
            foreach ($temp_voList as $k){
                $goods_sn_list[] =  $k['goods_sn'];
            }
            $where_goods_sn['goods_sn'] = array('IN',$goods_sn_list);
            unset($map['warehouse']);
            //每个仓库每个尺码
            $size_tmp=$model->field('goods_sn,goods_size,warehouse,inv_cnt as size_cnt')->where($map)->where($where_goods_sn)->select();
            $size=[];
            foreach($size_tmp as $k=>$v){
                $size[$v['goods_sn']][$v['warehouse']][$v['goods_size']]=$v['size_cnt'];
            }
            //不涉及仓库的每个尺码
            $size_tmp_=$model->field('goods_sn,goods_size,sum(inv_cnt) as size_cnt')->where($map)->where($where_goods_sn)->group('goods_sn,goods_size')->select();
            $tmp_siz_list=[];
            foreach ($size_tmp_ as $key => $v) {
                $tmp_siz_list[$v['goods_sn']][$v['goods_size']] = $v['size_cnt'];
            }
            //每个仓库的总数
            $size_all_tmp=$model->field('goods_sn,warehouse,sum(inv_cnt) as size_cnt')->where($map)->where($where_goods_sn)->group('goods_sn,warehouse')->select();
            $size_all=[];
            foreach($size_all_tmp as $k=>$v){
                $size_all[$v['goods_sn']][$v['warehouse']]=$v['size_cnt'];
            }
            //每个goods_sn的总数
            $size_sum_tmp=$model->field('goods_sn,sum(inv_cnt) as size_cnt')->where($map)->where($where_goods_sn)->group('goods_sn')->select();
            $size_sum=[];
            foreach($size_sum_tmp as $k=>$v){
                $size_sum[$v['goods_sn']]=$v['size_cnt'];
            }
            foreach($temp_voList as $key => $v){
                $tmp_i = 0;
                $tmp_i_b = 0;
                $tmp_i_ce = 0;
                $tmp_i_cw = 0;
                $tmp_i_d = 0;
                foreach ($size[$v['goods_sn']]['B'] as $i =>$val){
                    $tmp_b[$tmp_i_b]['stock']=$val;
                    $tmp_b[$tmp_i_b]['size']=$i;
                    $tmp_i_b++;
                };
                foreach ($size[$v['goods_sn']]['CE'] as $i =>$val){
                    $tmp_ce[$tmp_i_ce]['stock']=$val;
                    $tmp_ce[$tmp_i_ce]['size']=$i;
                    $tmp_i_ce++;
                };

                foreach ($size[$v['goods_sn']]['CW'] as $i =>$val){
                    $tmp_cw[$tmp_i_cw]['stock']=$val;
                    $tmp_cw[$tmp_i_cw]['size']=$i;
                    $tmp_i_cw++;
                };
                foreach ($size[$v['goods_sn']]['D'] as $i =>$val){
                    $tmp_d[$tmp_i_d]['stock']=$val;
                    $tmp_d[$tmp_i_d]['size']=$i;
                    $tmp_i_d++;
                };
                foreach($tmp_siz_list[$v['goods_sn']] as $i =>$val) {
                    $tmp_s[$tmp_i]['stock']=$val;
                    $tmp_s[$tmp_i]['size']=$i;
                    $tmp_i++;
                };
                $v['goods_size']=$tmp_s['goods_size'];
                $v['warehouse_b']=$tmp_b;
                $v['warehouse_ce']=$tmp_ce;
                $v['warehouse_cw']=$tmp_cw;
                $v['warehouse_d']=$tmp_d;
                $v['no_warehouse']=$tmp_s;
                $v['warehouse_b_all']=$size_all[$v['goods_sn']]['B'];
                $v['warehouse_ce_all']=$size_all[$v['goods_sn']]['CE'];
                $v['warehouse_cw_all']=$size_all[$v['goods_sn']]['CW'];
                $v['warehouse_d_all']=$size_all[$v['goods_sn']]['D'];
                $v['warehouse_sum']=$size_sum[$v['goods_sn']];
                $tmp_b = [];
                $tmp_ce = [];
                $tmp_cw = [];
                $tmp_d = [];
                $tmp_s = [];
                $voList[] = $v;
            }
            foreach ($map as $key => $val) {
                if (!is_array($val)) {
                    $p->parameter.="$key=".urldecode($val)."&";
                }
            }
            //分页显示
            $page = $p->show();
            //列表排序显示
            $sortImg = $sort; //排序图标
            $sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列' ;//排序提示
            $sort = $sort == 'desc' ? 1 : 0 ;//排序方式
            //模板赋值
            $this->assign('size_all',$size_all);
            $this->assign('volist',$voList);
            $this->assign('sort',$sort);
            $this->assign('order',$order);
            $this->assign('sortImg',$sortImg);
            $this->assign('sortType',$sortAlt);
            $this->assign('page',$page);
            $this->assign('date_type','day');
            $data = $this->fetch('Echarts:'.$form);

            echo $data;
        }else{
            echo '当前天暂无数据';
        }
        return;
    }

    /**
     * 滞销品统计
     * 设计小组参数获取
     */
    public function group_nm_param_get(){
        C('DB_CASE_LOWER',true);
        $tab_nm = I('post.tab_nm');     //表名 带前缀
        $c_nm = I('post.c_nm');         //字段名
        $site_tp = I('post.site_tp');   //不需要填 null
        $db_type = I('post.db_type');   // Oracle_WH Mysql_WH
        $model= M($tab_nm,null,$db_type);
        if(I('post.site_tp') != null){
            $sql = "select distinct $c_nm from $tab_nm where site_tp = '$site_tp' order by $c_nm";
        }else{
            $sql = "select distinct $c_nm from $tab_nm  order by $c_nm";
        }
        $tmp = $model->query($sql);
        foreach($tmp as $k){
            $list[] = $k[$c_nm];
        }
        $this->ajaxReturn( array('list'=>$list));
    }

    /**
     * 滞销产品导出
     * modify @chenmin 20170717 09:16  库存总数修改为“库存总数和各仓库的库存数”
     */
    public function exp_outstock_ana(){
        C("DB_CASE_LOWER",true);
        $warehouse=I('post.warehouse');
        $model= M('dm_stk_unmarketable_sku_d',null,'Oracle_WH');
        $pay_start_date = ('' == I('post.cost_amt_l') ) ? '0': I('post.cost_amt_l');
        $pay_end_date = ('' == I('post.cost_amt_m') ) ? '999999': I('post.cost_amt_m');
        $map['cost_rmb_amt'] = array(array('EGT',$pay_start_date),array('ELT',$pay_end_date),'and');
        $map['site_tp']=I('post.site_tp');
        $map['warehouse']=$warehouse;
        if (I('post.type') != '') {
            $map['sale_flag'] = I('post.type');
        }
        if (I('post.goods_sn') != '') {
            $map['goods_sn'] = array('like','%'.I('post.goods_sn').'%');
        }else{
            if(I('post.exp_checkbox') != ''){
                $map['goods_sn'] = array('IN',I('post.exp_checkbox'));
            }
        }
        if (I('post.supplier_nm') != '') {
            $map['supplier_nm'] = array('like','%'.I('post.supplier_nm').'%');
        }
        if(I('post.buyer_nm')!='total'){
            if(I('post.buyer_nm')!='null'){
                $map['buyer_nm']=I('post.buyer_nm');
            }else{
                $map['buyer_nm']=array('EXP','is null');
            }
        }
        if(I('post.group_nm')!='total'){
            if(I('post.group_nm')!= 'null'){
                $map['group_nm']=I('post.group_nm');
            }else {
                $map['group_nm']=array('EXP',' is null');
            }
        }
        $field = "goods_sn,img_url,supplier_nm,buyer_nm,group_nm,layer_nm,sum(inv_cnt) as inv_cnt,price,sepcial_price,cost_rmb_amt,delay_day,sale_flag,recycle_flag";
        $group = 'goods_sn,img_url,supplier_nm,buyer_nm,group_nm,layer_nm,price,sepcial_price,cost_rmb_amt,delay_day,sale_flag,recycle_flag';
        $temp_voList = $model->field($field)->where($map)->group($group)->order('goods_sn desc')->select();
        unset($map['warehouse']);
        //每个仓库的总数
        $size_all_tmp = $model->field('goods_sn,warehouse,sum(inv_cnt) as size_cnt')->where($map)->group('goods_sn,warehouse')->select();
        $size_all = [];
        foreach ($size_all_tmp as $k => $v) {
            $size_all[$v['goods_sn']][$v['warehouse']] = $v['size_cnt'];
        }
        //每个goods_sn的总数
        $size_sum_tmp = $model->field('goods_sn,sum(inv_cnt) as size_cnt')->where($map)->group('goods_sn')->select();
        $size_sum = [];
        foreach ($size_sum_tmp as $k => $v) {
            $size_sum[$v['goods_sn']] = $v['size_cnt'];
        }
        $voList=[];
        foreach ($temp_voList as $k => $v) {
            $voList[$k]['goods_sn']=$v['goods_sn'];
            $voList[$k]['img_url']=$v['img_url'];
            $voList[$k]['supplier_nm']=$v['supplier_nm'];
            $voList[$k]['buyer_nm']=$v['buyer_nm'];
            $voList[$k]['group_nm']=$v['group_nm'];
            $voList[$k]['layer_nm']=$v['layer_nm'];
            $voList[$k]['layer_nm']=$v['layer_nm'];
            $voList[$k]['warehouse_sum'] = $size_sum[$v['goods_sn']];
            $voList[$k]['warehouse_b_all'] = $size_all[$v['goods_sn']]['B'];
            $voList[$k]['warehouse_ce_all'] = $size_all[$v['goods_sn']]['CE'];
            $voList[$k]['warehouse_cw_all'] = $size_all[$v['goods_sn']]['CW'];
            $voList[$k]['warehouse_d_all'] = $size_all[$v['goods_sn']]['D'];
            $voList[$k]['price']=$v['price'];
            $voList[$k]['sepcial_price']=$v['sepcial_price'];
            $voList[$k]['cost_rmb_amt']=$v['cost_rmb_amt'];
            $voList[$k]['delay_day']=$v['delay_day'];
            $voList[$k]['sale_flag']=$v['sale_flag']==1?'上架':'下架';
            $voList[$k]['recycle_flag']=$v['recycle_flag']==1?'是':'否';
        }
        $article = array('sku','商品图片','供应商','买手','设计小组','商品层次','库存总数','B区库存','C区东部库存','C区西部库存','D区库存','原价','特价','成本','滞销天数','是否上架 (1=上架)','是否侵权 (1=侵权)');
        $this->excel_export($voList,$article,'滞销品统计报表');die;
    }
    /*
     * 预售产品数统计
     */
    public function statistic_presale_product() {
        if (I('post.order')=='hour') {
            $model = M('dm_ssa_site_core_h',null,'Mysql_WH');
            $field = "DATE_FORMAT(CONCAT(dt,'0000'),'%Y-%m-%d %H:%i:%s') AS dt,
            sum(goods_presale_cnt) as  正在预售,
            sum(goods_out_presale_cnt) as 预售结束";
        } else if( I('post.order')=='month' ){
            $model = M('dm_ssa_site_core_m',null,'Mysql_WH');
            $field = "date_format(concat(dt,'00'),'%Y-%m') as dt,
            sum(goods_presale_cnt) as  正在预售,
            sum(goods_out_presale_cnt) as 预售结束";
        } else if ( I('post.order')=='week' ) {
            $model = M('dm_ssa_site_core_w',null,'Mysql_WH');
            $field = "concat(substr(dt,'1',4),'-',substr(dt,'5',2)) as dt,
            sum(goods_presale_cnt) as  正在预售,
            sum(goods_out_presale_cnt) as 预售结束";
        } else {
            if(I('post.time_range') == 'morning'){
                $model = M('dm_ssa_site_core_mor_d',null,'Mysql_WH');
            }else {
                $model = M('dm_ssa_site_core_d',null,'Mysql_WH');
            }
            $field = "date(dt) as dt,
            sum(goods_presale_cnt) as  正在预售,
            sum(goods_out_presale_cnt) as 预售结束";
        }
        $query_date=$this->get_query_date();
        $map['dt'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
        $map['site_tp'] = I('post.site_tp');
        $map['site_id'] = $this->get_query_site('dm_ssa_site_core_d');
        $list_temp = $model->field($field)->where($map)->order('dt desc,site_id asc')->group('dt')->select();
        $list = array();
        $i=0;
        foreach ($list_temp as $k ){
            foreach($k as $v =>$b){
                if($v != 'dt'){
                    $list[$i]['dt'] = $k['dt'];
                    $list[$i]['val'] = $b;
                    $list[$i]['site'] = $v;
                    $i++;
                }
            }
        }
        $arr = array('title' =>'预售产品数统计' , 'dim1' => 'dt','dim2' => 'site','field' => 'val'  ,'table' => 'ssa_site_core');
        $this->statistic_line($list,$arr,null);
    }

    /*
     * 订单产品数统计
     * modified by chenmin 20170419 10:40  按天的都取pay_dt,其它都取dt
     */
    public function statistic_order_product() {
        $tab_name = "dm_ord_sale_goods_stat";
        $db_type = "Mysql_WH";
        $column_name = "sum(goods_cnt) as goods_cnt";
        $column_name_trmnl_tp = "sum(a.goods_cnt) as goods_cnt";

        //model
        if (I('post.order') == 'month') {
            $model = M($tab_name . "_m", null, $db_type);
            $model_trmnl_tp = M("$tab_name" . "_m a,dw_pub_site_td b", null, $db_type);
            $field_date = "date_format(concat(dt,'00'),'%Y-%m') as dt,";
            $field_date_trmnl_tp = "date_format(concat(a.dt,'00'),'%Y-%m') as dt,";
        } else if (I('post.order') == 'week') {
            $model = M($tab_name . "_w", null, $db_type);
            $model_trmnl_tp = M("$tab_name" . "_w a,dw_pub_site_td b", null, $db_type);
            $field_date = "concat(substr(dt,'1',4),'-',substr(dt,'5',2)) as dt,";
            $field_date_trmnl_tp = "concat(substr(a.dt,'1',4),'-',substr(a.dt,'5',2)) as dt,";
        } elseif (I('post.order') == 'hour') {
            $model = M($tab_name . "_h", null, $db_type);
            $model_trmnl_tp = M("$tab_name" . "_h a,dw_pub_site_td b", null, $db_type);
            $field_date = "date_format(concat(dt,'0000'),'%Y-%m-%d %H:%i:%s') as dt,";
            $field_date_trmnl_tp = "date_format(concat(a.dt,'0000'),'%Y-%m-%d %H:%i:%s') as dt,";
        } else {
            if (I('post.time_range') == 'morning') {
                $model = M($tab_name . "_mor_d", null, $db_type);
                $model_trmnl_tp = M("$tab_name" . "_mor_d a,dw_pub_site_td b", null, $db_type);
            } else {
                $model = M($tab_name . "_d", null, $db_type);
                $model_trmnl_tp = M("$tab_name" . "_d a,dw_pub_site_td b", null, $db_type);
            }
            $field_date = "date(pay_dt) as dt,";
            $field_date_trmnl_tp = "date(a.pay_dt) as dt,";
        }
        //field
        $field = $field_date . "site_id,$column_name";
        $field_trmnl_tp = $field_date_trmnl_tp . "b.trmnl_tp AS site_id,$column_name_trmnl_tp";
        $field_trmnl_web = $field_date_trmnl_tp . "'WEB' AS site_id,$column_name_trmnl_tp";


        //array init
        $list_trmnl_web = $list_trmnl_tp = $list_site_id = array();
        $query_date = $this->get_query_date();


        $site_tp = I('post.site_tp');

        if(I('post.pay_mthd') != ''){
            $map_trmnl_web['a.pay_mthd'] =  $map_trmnl_tp['a.pay_mthd'] = $map['pay_mthd'] = I('post.pay_mthd');
        }
        $map['site_id'] = array('IN', $_SESSION['_SITELIST'][$site_tp]);
        $map_trmnl_web['a.site_tp'] = $map_trmnl_tp['a.site_tp'] = $map['site_tp'] = I('post.site_tp');
        if(I('post.order')=='day'){
            $map_trmnl_web['a.pay_dt'] = $map_trmnl_tp['a.pay_dt'] = $map['pay_dt']
                = array(array('EGT', $query_date['start_date']), array('ELT', $query_date['end_date']), 'and');
            $group='pay_dt';
        }else{
            $map_trmnl_web['a.dt'] = $map_trmnl_tp['a.dt'] = $map['dt']
                = array(array('EGT', $query_date['start_date']), array('ELT', $query_date['end_date']), 'and');
            $group='dt';
        }


        //Common site_id query
        $list_site_id = $model->field($field)->where($map)->group($group.',site_id')->order('dt desc,site_id asc')->select();

        //Special site_id like PC,APP&M
        $trmnl_tp = array('PC', 'APP', 'M');
        //Computes the intersection of trmnl_tp
        $trmnl_tp_intersect = array_intersect($trmnl_tp, $_SESSION['_SITELIST'][$site_tp]);
        if (!empty($trmnl_tp_intersect)) {
            $map_trmnl_tp['b.trmnl_tp'] = array('IN', $trmnl_tp_intersect);
            $list_trmnl_tp = $model_trmnl_tp->field($field_trmnl_tp)->where($map_trmnl_tp)
                ->where('a.site_id = b.site_id')->group('a.'.$group.',b.trmnl_tp')
                ->order('dt desc,site_id asc')->select();
        }
        //Single one WEB
        if (in_array('WEB', $_SESSION['_SITELIST'][$site_tp])) {
            $trmnl_web = array('PC', 'M');
            $map_trmnl_web['b.trmnl_tp'] = array('IN', $trmnl_web);
            $list_trmnl_web = $model_trmnl_tp->field($field_trmnl_web)->where($map_trmnl_web)
                ->where('a.site_id = b.site_id')->group('a.'.$group)
                ->order('dt desc,site_id asc')->select();
        }
        $list = array_merge($list_site_id, $list_trmnl_tp, $list_trmnl_web);
        $arr = array('title' => '订单产品数统计', 'dim1' => 'dt', 'dim2' => 'site_id', 'field' => 'goods_cnt', 'table' => 'dm_ssa_site_core');
        $this->statistic_line($list, $arr, $this->unselected);
    }

    /*
     *获取买手下拉单对应参数
     * modified @hiro 2016-09-09 16:11:51
     * 更换下拉菜单数据元
     */
    public function get_select_param(){
        $map['site_tp']= I('post.site_tp') ;
        //站点
        $site_id = M('dw_pub_site_td',null,'Mysql_WH')->field('distinct site_id')->where($map)->select();
        $site_id[]=array('site_id'=>'ALL');
        //图片来源
        $img_src_tp_nm = M('dw_pub_gds_img_src_td',null,'Mysql_WH')->field('distinct img_src_tp_nm')->select();
        //买手
        $buyer_nm= M('dw_pub_gds_buyer_td',null,'Mysql_WH')->field('distinct buyer_nm')->select();
        //维护买手
        $maintain_buyer= M('dw_pub_gds_buyer_td',null,'Mysql_WH')->field('distinct buyer_nm as maintain_buyer')->select();

        $return_list['site_id'] = $site_id;
        $return_list['img_src_tp_nm'] = $img_src_tp_nm;
        $return_list['buyer_nm'] = $buyer_nm;
        $return_list['maintain_buyer'] = $maintain_buyer;
        $this->ajaxReturn( array('list_all'=>$return_list));
    }


    /*
     * 买手商品列表
     */
    public function buyer_list_display(){
        C("DB_CASE_LOWER",true);
        $site_tp = I('post.site_tp');
        $model = M('dm_pub_buyer_sku_d',null,'Oracle_WH');
        $where = ' 1=1 ';
        $where .= " and site_tp = '$site_tp'";
        //上下架状态
        if (I('post.stat') != '') {
            if(I('post.stat') =='1'){
                $having = 'sale_flag > 0 ';
            }else{
                $having = 'sale_flag = 0';
            }
        }
        //回收站状态
        if (I('post.recycle') != '') {
            $where .= ' and recycle_flag ='.I('post.recycle');
        }
        //图片来源
        if (I('post.img') != '') {
            $img_list='';
            foreach(I('post.img') as $k => $v){
                $img_list .= "'".$v."',";
            }
            $img_id = rtrim($img_list,',');
            $where .= ' and img_src_tp_nm in ('.$img_id.')';
        }
        //选择买手
        if (I('post.buyer') != '') {
            $buyer_list = '';
            foreach (I('post.buyer') as $k => $v) {
                $buyer_list .= "'" . $v . "',";
            }
            $buyer_id = rtrim($buyer_list, ',');
            $where .= ' and buyer_nm in (' . $buyer_id . ')';

        }
        //选择维护买手
        if (I('post.maintain_buyer') != '') {
            $maintain_list='';
            foreach(I('post.maintain_buyer') as $k => $v){
                $maintain_list .= "'".$v."',";
            }
            $maintain_id = rtrim($maintain_list,',');
            $where .= ' and maintain_buyer in ('.$maintain_id.')';

        }

        //商品添加时间
        $start_date = ('' == I('post.start_date') ) ? date("Y-m-d",mktime(0, 0, 0, date("m"), date("d")-30, date("Y"))) :I('post.start_date');
        $end_date = ('' == I('post.end_date') ) ? date("Y-m-d",time()):I('post.end_date');

        $where .= " and onsale_time >='" .$start_date."' and onsale_time <='".$end_date."'";
        //SKU-模糊查询
        if (I('post.goods_sn') != '') {
            $where .= " and goods_sn like '%" .I('post.goods_sn')."%'";
        }
        //供应商-模糊查询
        if (I('post.supplier_nm') != '') {
            $where .= " and supplier_nm like '%" .I('post.supplier_nm')."%'";
        }
        //供应商编号
        if (I('post.supplier_cd') != '') {
            $where .= ' and supplier_cd =' ."'" .I('post.supplier_cd') ."'";
        }
        //摄影人
        if (I('post.sy_user_nm') != '') {
            $where .= ' and sy_user_nm =' ."'" .I('post.sy_user_nm') ."'";
        }
        //修图人
        if (I('post.ps_user_nm') != '') {
            $where .= ' and ps_user_nm ='  ."'".I('post.ps_user_nm') ."'";
        }
        //PIC编号
        if (I('post.model_pic_sn') != '') {
            $where .= ' and model_pic_sn =' ."'".I('post.model_pic_sn') ."'";
        }

        $where = trim($where,'A');
        //两次查询 第一次求和,第二次取站点
        $field = "max(goods_id) as goods_id,
            max(img_url) as img_url,
            max(goods_name_en) as goods_name_en,
            max(goods_sn) as goods_sn,
            max(cost_rmb_amt) as cost_rmb_amt,
            max(buyer_nm) as buyer_nm,max(maintain_buyer) as maintain_buyer,
            count(site_id) as site_id_count,
            sum(nvl(sale_flag,0)) as sale_flag_count,
            max(recycle_flag) as recycle_flag,
            max(supplier_nm) as supplier_nm,
            max(supplier_cd) as supplier_cd,
            nvl(stk_cnt,0) as stk_cnt,
            sum(nvl(clk_cnt,0)) as clk_cnt,
            sum(nvl(cnt_all,0)) as cnt_all,
            case when sum(nvl(clk_cnt,0))>0 then to_char(sum(nvl(cnt_all,0))/sum(nvl(clk_cnt,0))*100,'fm999990.90') ELSE '0' END as cvs_rate,
            sum(nvl(cnt_monthc,0)) as cnt_monthc,
            sum(nvl(cnt_monthl,0)) as cnt_monthl,
            max(onsale_time) as onsale_time,
            max(last_unsale_time) as last_unsale_time,
            max(free_flag) as free_flag,
            max(img_src_tp_nm) as img_src_tp_nm,
            max(sy_user_nm) as sy_user_nm,
            max(ps_user_nm) as ps_user_nm,
            max(model_pic_sn) as model_pic_sn";
        $group = 'goods_id,
            img_url,
            goods_name_en,
            goods_sn,
            buyer_nm,maintain_buyer,
            recycle_flag,
            supplier_nm,
            supplier_cd,
            stk_cnt,
            onsale_time,
            last_unsale_time,
            free_flag,
            img_src_tp_nm,
            sy_user_nm,
            ps_user_nm,
            sale_flag,
            model_pic_sn';
        $subQuery = $model->field($field)->where($where)->group($group)->select(false);
        $count = $model->table($subQuery.' a')->count('1');

        $asc=false;
        //字段排序 默认主键
        if (isset($_REQUEST ['order'])) {
            $order = $_REQUEST ['order'];
        } else {
            $order = !empty($sortBy) ? $sortBy : $model->getPk();
        }
        //排序默认倒序排列
        //sort 0：倒序 非0：正序
        if (isset($_REQUEST ['_sort'])) {
            $sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
        } else {
            $sort = $asc ? 'asc' : 'desc';
        }
        if($count>0){
            import('@.ORG.Util.Page_o');
            //创建分页对象
            if ($_REQUEST ['listRows'] == 'undefined' || $_REQUEST ['listRows'] =='') {
                $listRows = '100';
            } else {
                $listRows = $_REQUEST ['listRows'];
            }
            $p = new Page($count, $listRows);
            $list_tmp = $model->field($field)->where($where)->group($group)->having($having)->order($order." ".$sort)->limit($p->firstRow.','.$p->listRows)->select();
            $page = $p->show();

            //列表排序显示
            $sortImg = $sort; //排序图标
            $sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列' ;//排序提示
            $sort = $sort == 'desc' ? 1 : 0 ;//排序方式
            //模板赋值
            $this->assign('sort',$sort);
            $this->assign('order',$order);
            $this->assign('sortImg',$sortImg);
            $this->assign('sortType',$sortAlt);


            $form = 'form_buyer_list';
            $this->assign('page', $page);
            $this->assign('list', $list_tmp);

            $data = $this->fetch('Echarts:' . $form);
            echo $data;exit;
        }else{
            echo '当前天暂无数据';
        }
        return;

    }

    /*
     * 买手商品列表 导出
     */
    public function buyer_list_exp(){
        C("DB_CASE_LOWER",true);
        $site_tp = I('post.site_tp');
        ini_set('memory_limit','512M');
        $model = M('dm_pub_buyer_sku_d',null,'Oracle_Amazon');
        $where = ' 1=1 ';
        $where .= " and site_tp = '$site_tp'";
        //上下架状态
        if (I('post.stat') != '') {
            if(I('post.stat') =='1'){
                $having = 'sale_flag > 0 ';
            }else{
                $having = 'sale_flag = 0';
            }
        }
        //回收站状态
        if (I('post.recycle2') != '') {
            $where .= ' and recycle_flag ='.I('post.recycle2');
        }
        //图片来源
        if (I('post.img2') != '') {
            $where .= " and img_src_tp_nm in ('".str_replace(',',"','",I('post.img2'))."')";
        }
        //选择买手
        if (I('post.buyer2') != '') {
            $where .= " and buyer_nm in ('".str_replace(',',"','",I('post.buyer2'))."')";
        }
        //选择维护买手
        if (I('post.maintain_buyer2') != '') {

            $where .= " and maintain_buyer in ('".str_replace(',',"','",I('post.maintain_buyer2'))."')";
        }

        //商品添加时间
        $start_date = ('' == I('post.start_date2') ) ? date("Y-m-d",mktime(0, 0, 0, date("m"), date("d")-30, date("Y"))) :I('post.start_date2');
        $end_date = ('' == I('post.end_date2') ) ? date("Y-m-d",time()):I('post.end_date2');

        $where .= " and onsale_time >='" .$start_date."' and onsale_time <='".$end_date."'";
        //SKU-模糊查询
        if (I('post.goods_sn2') != '') {
            $where .= " and goods_sn like '%" .I('post.goods_sn2')."%'";
        }
        //供应商-模糊查询
        if (I('post.supplier_nm2') != '') {
            $where .= " and supplier_nm like '%" .I('post.supplier_nm2')."%'";
        }
        //供应商编号
        if (I('post.supplier_cd2') != '') {
            $where .= ' and supplier_cd =' ."'" .I('post.supplier_cd2') ."'";
        }
        //摄影人
        if (I('post.sy_user_nm2') != '') {
            $where .= ' and sy_user_nm =' ."'" .I('post.sy_user_nm2') ."'";
        }
        //修图人
        if (I('post.ps_user_nm2') != '') {
            $where .= ' and ps_user_nm ='  ."'".I('post.ps_user_nm2') ."'";
        }
        //PIC编号
        if (I('post.model_pic_sn2') != '') {
            $where .= ' and model_pic_sn =' ."'".I('post.model_pic_sn2') ."'";
        }

        $where = trim($where,'A');
        //两次查询 第一次求和,第二次取站点
        $field = "max(goods_id) as goods_id,
            max(img_url) as img_url,
            max(goods_name_en) as goods_name_en,
            max(goods_sn) as goods_sn,
            max(cost_rmb_amt) as cost_rmb_amt,
            max(buyer_nm) as buyer_nm,max(maintain_buyer) as maintain_buyer,
            count(site_id) as site_id_count,
            sum(nvl(sale_flag,0)) as sale_flag_count,
            max(recycle_flag) as recycle_flag,
            max(supplier_nm) as supplier_nm,
            max(supplier_cd) as supplier_cd,
            nvl(stk_cnt,0) as stk_cnt,
            sum(nvl(clk_cnt,0)) as clk_cnt,
            sum(nvl(cnt_all,0)) as cnt_all,
            case when sum(nvl(clk_cnt,0))>0 then to_char(sum(nvl(cnt_all,0))/sum(nvl(clk_cnt,0))*100,'fm999990.90') ELSE '0' END as cvs_rate,
            sum(nvl(cnt_monthc,0)) as cnt_monthc,
            sum(nvl(cnt_monthl,0)) as cnt_monthl,
            max(onsale_time) as onsale_time,
            max(last_unsale_time) as last_unsale_time,
            max(free_flag) as free_flag,
            max(img_src_tp_nm) as img_src_tp_nm,
            max(sy_user_nm) as sy_user_nm,
            max(ps_user_nm) as ps_user_nm,
            max(model_pic_sn) as model_pic_sn";
        $group = 'goods_id,
            img_url,
            goods_name_en,
            goods_sn,
            buyer_nm,maintain_buyer,
            recycle_flag,
            supplier_nm,
            supplier_cd,
            stk_cnt,
            onsale_time,
            last_unsale_time,
            free_flag,
            img_src_tp_nm,
            sy_user_nm,
            ps_user_nm,
            sale_flag,
            model_pic_sn';
        $subQuery = $model->field($field)->where($where)->group($group)->select(false);
        $count = $model->table($subQuery.' a')->count('1');
        if($count>0){
            import('@.ORG.Util.Page_o');
            //创建分页对象
            if ($_REQUEST ['listRows'] == 'undefined' || $_REQUEST ['listRows'] =='') {
                $listRows = '100';
            } else {
                $listRows = $_REQUEST ['listRows'];
            }
            $p = new Page($count, $listRows);
            $list_tmp = $model->field($field)->where($where)->group($group)->having($having)->select();
            foreach ($list_tmp as $k=> $v){

                $tmp['goods_id'] = $v['goods_id'];
                $tmp['img_url'] = $v['img_url'];
                $tmp['goods_name_en'] = $v['goods_name_en'];
                $tmp['goods_sn'] = $v['goods_sn'];
                $tmp['cost_rmb_amt'] = $v['cost_rmb_amt'];
                $tmp['buyer_nm'] = $v['buyer_nm'];
                $tmp['maintain_buyer'] = $v['maintain_buyer'];
                $tmp['site_flag'] = $v['sale_flag_count']>0 ? '√':'×';
                $tmp['recycle_flag'] = $v['recycle_flag'];
                $tmp['supplier_nm'] = $v['supplier_nm'];
                $tmp['supplier_cd'] = $v['supplier_cd'];
                $tmp['stk_cnt'] = $v['stk_cnt'];
                $tmp['clk_cnt'] = $v['clk_cnt'];
                $tmp['cnt_all'] = $v['cnt_all'];
                $tmp['cvs_rate'] = $v['cvs_rate'];
                $tmp['cnt_monthc'] = $v['cnt_monthc'];
                $tmp['cnt_monthl'] = $v['cnt_monthl'];
                $tmp['onsale_time'] = $v['onsale_time'];
                $tmp['last_unsale_time'] = $v['last_unsale_time'];
                $tmp['free_flag'] = $v['free_flag'];
                $tmp['img_src_tp_nm'] = $v['img_src_tp_nm'];
                $tmp['sy_user_nm'] = $v['sy_user_nm'];
                $tmp['ps_user_nm'] = $v['ps_user_nm'];
                $tmp['model_pic_sn'] = $v['model_pic_sn'];
                $list[] = $tmp;

            }
            $article = array('goods_id','商品图片','商品名称','sku','成本','买手','维护买手','上架','回收站','供应商','供应商编号','实际库存','点击','购买','转化率','当月销量','上月销量','上架时间','下架时间','免费版','图片来源','摄影','修图','PIC编号');
            $this->excel_export($list,$article,'买手商品列表');


            exit;
        }else{
            echo '当前天暂无数据';
        }
        return;


    }


    /*
     * 价格区间的产品款数 old
     */
    public function statistic_goods_price_lvl_ord(){
        C('DB_CASE_LOWER',true);
        $model = M('dm_ord_goods_price_lvl_d',null,'Oracle_WH');
        $query_date=$this->get_query_date_ora();
        $map['dt'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
        $map['site_tp']=I('post.site_tp');
        $map['date_type']=I('post.order');
        //$condition['date_type']='day';
        $map['site_id'] = $this->get_query_site('dm_ord_goods_price_lvl_d');
        $list=$model->where($map)->field('price_lvl_id,sum(goods_cnt) as goods_cnt ')->order('price_lvl_id asc')->group('price_lvl_id')->select();
        $arr = array('title'=>'价格区间的产品款数','dim1'=>'dt','dim2'=>'price_lvl_id','field'=>'goods_cnt','flag'=>'xAxisNum','legend'=>'订单价格区间');

        $this->statistic($list,$arr);


    }

    /*
		统计渲染图表信息line
		用于按site_from系列
		 */
    function statistic($list,$arr){
        $legend = array();
        $xAxis = array();
        $series = array();
        $member = array();
        $date = array();

        $date=$this->get_date();
        if($arr['flag']=='xAxisNum') {
            //图表横坐标是区间
            array_push($legend, $arr['legend']);
            $this-> legend=array_unique($legend);
            foreach($list as $k => $v){
                $result[$arr['legend']][$v['price_lvl_id']]= $v[$arr['field']];
                array_push($xAxis, $v['price_lvl_id']);
            }
            foreach($result as $k => $v){
                foreach($v as $kk => $vv)
                    $series[$arr['legend']][$kk] = $vv;
            }
            $this-> xAxis=$xAxis;
        }  else{
            //图表横坐标是日期
            if (I('post.order')=='month') {
                foreach ($list as $k => $v) {
                    array_push($legend, $v[$arr['dim2']]);
                    $result[$v[$arr['dim2']]][$v[$arr['dim1']]] = $v[$arr['field']];
                }
            } else if (I('post.order')=='week') {
                foreach ($list as $k => $v) {
                    array_push($legend, $v[$arr['dim2']]);
                    $result[$v[$arr['dim2']]][$v[$arr['dim1']]] = $v[$arr['field']];
                }
            } else if (I('post.order')=='hour') {
                foreach ($list as $k => $v) {
                    array_push($legend, $v[$arr['dim2']]);
                    $result[$v[$arr['dim2']]][$v[$arr['dim1']]] = $v[$arr['field']];
                }
            } else {
                foreach ($list as $k => $v) {

                    array_push($legend, $v[$arr['dim2']]);
                    $result[$v[$arr['dim2']]][$v[$arr['dim1']]] = $v[$arr['field']];

                }
            }
            if (I('post.order')=='week') {
                $xAxis = $date[1];
                $date = $date[2];
                $this-> xAxis=$xAxis;
            } else {
                $this-> xAxis=$date;
            }
            foreach ($result as $k => $v) {
                foreach ($date as $d) {
                    if (!empty($v[$d])) {
                        $member = $v[$d];
                    }else{
                        $member = 0;
                    }
                    $series[$k][$d] = $member;
                }
            }
        }
        $this-> legend=array_unique($legend);
        $this-> series=$series;
        $this-> title=$arr['title'];

        $str_len_px=$this->string_length_px($series);
        $this->assign('str_len_px',json_encode($str_len_px));

        if( $arr['table'] == 'hour_kpi_count' || $arr['table'] == 'server_response_time' ){
            $this -> unselected = array('www', 'de', 'es', 'fr', 'ios', 'm', 'ru', 'us', 'it', 'ar', 'android','download','romwe_total','www_rw','es_rw','fr_rw','m_rw','mde');
        }

        if ($arr['table'] == 'quality_control') {
            $this-> id_name=$arr['flag'];
            $data=$this->fetch('Echarts:echarts_line_quality_control');
        } else {
            if (I('post.order')=='hour') {
                $data=$this->fetch('Echarts:echarts_line_hour');
            } else {
                $data=$this->fetch('Echarts:echarts_line');
            }
        }
        echo $data;die;
    }

    /**
     * 优化价格区间（售价、成本）的产品款数
     * @author zjh 2016-09-28 上午10:18
     */
    public function statistic_goods_price_lvl(){
        $sale_flag = I('post.sale_flag');
        $site_tp = I('post.site_tp');
        $price_type = I('post.price_type');
        if($sale_flag == 'onsale'){
            $map['sale_flag'] = 1;
        }
        if($site_tp != ''){
            $map['site_tp'] = $site_tp;
        }
        if($price_type == 'price'){
            //售价price
            //两张表数据结构完全一样
            $model = M('dm_pub_goods_price_lvl_d',null,'Mysql_WH');
            $title = '售价价格区间 产品款数';
        }else{
            //成本cost
            $model = M('dm_pub_goods_cost_lvl_d',null,'Mysql_WH');
            $title = '成本价格区间 产品款数';
        }
        $field = 'lvl_nm,sku_cate_1_nm,sum(goods_cnt) as total';
        $group = 'lvl_nm,sku_cate_1_nm';
        $order = 'lvl_nm';
        $res = $model -> field($field)->where($map)->group($group)->order($order)->select();
        $title = array(
            'title'=>$title,
            'dim1' => 'lvl_nm',
            'dim2' => 'sku_cate_1_nm',
            'field' => 'total',
            'echarts_type' => 'bar',    //柱状图
            'bar_stack' => true         //柱状堆积
        );
        echo $this->statistic_chart($res,$title,'');
    }


    /*
     * 复购Cohort统计
     * author @hiro 2016-07-22 13:48:56
     * modified by chenmin 20161212 10:40 加展示类型
     */
    public function statistic_multi_buy(){
        $model = M('dm_ord_re_purchase_cohort_m',null,'Mysql_WH');
        $type = I('post.select_type');
        $start_date = ('' == I('post.start_date') ) ? date("Y-m-d",mktime(0, 0, 0, date("m")-25, 1, date("Y"))) : I('post.start_date');
        $end_date = ('' == I('post.end_date') ) ? date("Y-m-d",mktime(0, 0, 0, date("m"), 1, date("Y"))) : I('post.end_date');
        $start_date = date('Ym', strtotime($start_date));
        $end_date = date('Ym', strtotime($end_date));

        $query_date = array('start_date' => $start_date, 'end_date' => $end_date);
        $map['month_frst'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
        $map['site_tp'] = I('post.site_tp');
        $trmnl=I('post.site_id');
        if($trmnl != 'TOTAL'){
            $map['site_id'] = I('post.site_id');
        }else{
            $map['site_id'] = 'total';
        }
        if(I('post.country_nm') != ''){
            $map['country_nm'] = I('post.country_nm');
        }
        $field ="month_frst,dt,sum($type) as type";
        $group ='month_frst,dt';
        $order = 'month_frst desc,dt asc';
        //表中的site_id字段中直接取好了M，APP等的值，为了去重。
        //sql语句不区分大小写，所以此处加了binary以区分大小写。
        if($trmnl=='M'){
            unset($map['site_id']);
            $voList = $model->field($field)->where($map)->where(" binary site_id= 'M'")->group($group)->order($order)->select();
        }elseif($trmnl=='m'){
            unset($map['site_id']);
            $voList = $model->field($field)->where($map)->where(" binary site_id='m'")->group($group)->order($order)->select();
        }else{
            $voList = $model->field($field)->where($map)->group($group)->order($order)->select();
        }
        foreach($voList as $k){
            $fin_arr[$k['month_frst']][$k['dt']] = $k['type'];
        }
        foreach($voList as $k){
            $month_frst[$k['dt']]=$k['dt'];
        }
        //加展示类型
        if(I('post.display_type')=='cnt'){
            foreach($fin_arr as $k => $v){
                foreach($month_frst as $v_k =>$v_v){
                    if($v_k != $k){
                        $fin_arr[$k][$v_k] ='';
                    }
                }
                foreach($v as $v_k => $v_v){
                    if($v_k != $k){
                        $fin_arr[$k][$v_k] = $v_v;
                    }
                }
            }
        }elseif (I('post.display_type')=='pnt'){
            foreach($fin_arr as $k => $v){
                foreach($month_frst as $v_k =>$v_v){
                    if($v_k != $k){
                        $fin_arr[$k][$v_k] ='';
                    }
                }
                foreach($v as $v_k => $v_v){
                    if($v_k != $k){
                        $fin_arr[$k][$v_k] = round($v_v/$fin_arr[$k][$k]*100,2).'%';
                    }
                }
            }
        }
        $this->assign('list',$fin_arr);
        $data = $this->fetch('Echarts:'.'form_multibuy_cohort');
        echo $data;
        exit;
    }
    /**
     * 复购Cohort统计(新)
     * author @chenmin 20170307 14:00
     * modify @chenmin 20171113 10:00 展示类型增加：“比例(百分点)”，“比例(百分比)”
     * modify @chenmin 20180111 17:00 展示类型增加：“比例(百分点)”，“比例(百分比)”
     */
    function statistic_multi_buy_new_bak(){
        $model = M('dm_ord_rebuy_cohort_m_bak', null, 'Mysql_WH');
        //用户数，订单数和总金额
        $type = I('post.select_type');
        if ($type == 'mem_cnt') {
            //用户数
            $type_frst = 'frst_usr_cnt';
            //Month1
            $type_rebuy = "frst_rebuy_usr_cnt";
        } elseif ($type == 'ord_cnt') {
            //订单数
            $type_frst = 'frst_order_cnt';
            $type_rebuy = 'frst_rebuy_order_cnt';
        } elseif ($type == 'ord_amt') {
            //总金额
            $type_frst = 'frst_order_amt';
            $type_rebuy = 'frst_rebuy_order_amt';
        }
        $start_date = ('' == I('post.start_date')) ? date("Y-m-d",
            mktime(0, 0, 0, date("m") - 36, 1, date("Y"))) : I('post.start_date');
        $end_date = ('' == I('post.end_date')) ? date("Y-m-d",
            mktime(0, 0, 0, date("m"), 1, date("Y"))) : I('post.end_date');
        $month_num=floor((strtotime($end_date)-strtotime($start_date))/(30*86400))+1;
        $start_date = date('Ym', strtotime($start_date));
        $end_date = date('Ym', strtotime($end_date));

        $query_date = array('start_date' => $start_date, 'end_date' => $end_date);
        $map['month_frst'] = array(array('EGT', $query_date['start_date']), array('ELT', $query_date['end_date']), 'and');
        $map['site_tp'] = I('post.site_tp');
        if (I('post.country_nm') != '') {
            $map['shpp_country_nm'] = I('post.country_nm');
        } else {
            $map['shpp_country_nm'] = 'total';
        }
        $field = "month_frst,dt,round(sum($type),0) as type,trmnl_tp";
        $field_total = "month_frst,dt,round(sum($type),0) as type,'TOTAL' as trmnl_tp";
        $field_total_rebuy = "month_frst,dt,round(sum($type_rebuy),0) as type,'TOTAL' as trmnl_tp";
        $field_total_frst = "month_frst,round(sum($type_frst),0) as type,'TOTAL' as trmnl_tp";
        $field_total_frst_rebuy = "month_frst,round(sum($type_rebuy),0) as type,'TOTAL' as trmnl_tp";
        $field_total_frst_web = "month_frst,round(sum($type_frst),0) as type,'WEB' as trmnl_tp";
        $field_total_frst_web_rebuy = "month_frst,round(sum($type_rebuy),0) as type,'WEB' as trmnl_tp";
        $field_dis_frst = "month_frst,round(sum($type_frst),0) as type, trmnl_tp";
        $field_dis_frst_rebuy = "month_frst,round(sum($type_rebuy),0) as type, trmnl_tp";
        $field_web = "month_frst,dt,round(sum($type),0) as type,'WEB' as trmnl_tp";
        $group = 'month_frst,dt,trmnl_tp';
        //按月和按自然月的区别是：展现方式不一样
        if (I('post.order') == 'month') {
            $order = 'month_frst desc,dt asc,trmnl_tp desc';
        } elseif (I('post.order') == 'month_natural') {
            $order = 'month_frst desc,dt desc,trmnl_tp desc';
        }
        //WEB=PC+M;  TOTAL=WEB+APP
        if (I('post.distinguish') == 'yes') {
            if (I('post.display_type') == 'cnt') {
                $voList_spr = $model->field($field)->where($map)->group($group)->order($order)->select();
                //首单数
                $list_dis_frst = $model->field($field_dis_frst)->where($map)->group("month_frst,trmnl_tp")->order($order)->select();
                $list_frst_tmp_ = $model->field($field_total_frst)->where($map)->where("trmnl_tp in ('PC','M','APP')")->group('month_frst')->select();
                $list_frst_tmp_web = $model->field($field_total_frst_web)->where($map)->where("trmnl_tp in ('PC','M')")->group('month_frst')->select();
                $list_frst_tmp = array_merge($list_dis_frst, $list_frst_tmp_, $list_frst_tmp_web);

                //Month1
                $list_dis_frst_rebuy = $model->field($field_dis_frst_rebuy)->where($map)->where(" dt=month_frst")->group("month_frst,trmnl_tp")->order($order)->select();
                $list_frst_tmp_rebuy = $model->field($field_total_frst_rebuy)->where($map)->where("trmnl_tp in ('PC','M','APP') and dt=month_frst")->group('month_frst')->select();
                $list_frst_tmp_web_rebuy = $model->field($field_total_frst_web_rebuy)->where($map)->where("trmnl_tp in ('PC','M') and dt=month_frst")->group('month_frst')->select();
                $list_frst_tmp_rebuy = array_merge($list_dis_frst_rebuy, $list_frst_tmp_rebuy,
                    $list_frst_tmp_web_rebuy);
                $list_total_rebuy = [];
                foreach ($list_frst_tmp_rebuy as $k => $v) {
                    $list_total_rebuy[$v['month_frst']][$v['trmnl_tp']] = $v['type'];
                }
                $voList_total = $model->field($field_total)->where($map)->where("trmnl_tp in ('PC','M','APP')")->group('month_frst,dt')->order($order)->select();
                $voList_web = $model->field($field_web)->where($map)->where("trmnl_tp in ('PC','M')")->group('month_frst,dt')->order($order)->select();
                $voList = array_merge($voList_total, $voList_spr, $voList_web);
//            } elseif (I('post.display_type') == 'pnt') {
            } else{
                $voList_spr = $model->field($field)->where($map)->group($group)->order($order)->select();
                $voList_total = $model->field($field_total)->where($map)->where("trmnl_tp in ('PC','M','APP')")->group('month_frst,dt')->order($order)->select();
                $voList_web = $model->field($field_web)->where($map)->where("trmnl_tp in ('PC','M')")->group('month_frst,dt')->order($order)->select();
                $voList_pnt = array_merge($voList_spr, $voList_web);
                foreach ($voList_pnt as $k => $v) {
                    foreach ($voList_total as $a => $b) {
                        if ($v['dt'] == $b['dt'] and $v['month_frst'] == $b['month_frst']) {
                            $voList_pnt[$k]['type'] = round($v['type'] / $b['type'] * 100, 2) . '%';
                        }
                    }
                }
                $voList = array_merge($voList_total, $voList_pnt);
                //首单数
                $list_dis_frst = $model->field($field_dis_frst)->where($map)->group("month_frst,trmnl_tp")->order($order)->select();
                $list_frst_tmp_ = $model->field($field_total_frst)->where($map)->where("trmnl_tp in ('PC','M','APP')")->group('month_frst')->select();
                $list_frst_tmp_web = $model->field($field_total_frst_web)->where($map)->where("trmnl_tp in ('PC','M')")->group('month_frst')->select();
                $list_frst_tmp = array_merge($list_dis_frst, $list_frst_tmp_web);
                $list_total_frst = [];
                foreach ($list_frst_tmp as $k => $v) {
                    foreach ($list_frst_tmp_ as $a => $b) {
                        if ($v['month_frst'] == $b['month_frst']) {
                            $list_total_frst[$k]['month_frst'] = $v['month_frst'];
                            $list_total_frst[$k]['type'] = round($v['type'] / $b['type'] * 100, 2) . '%';
                            $list_total_frst[$k]['trmnl_tp'] = $v['trmnl_tp'];
                        }
                    }
                }
                $list_frst_tmp = array_merge($list_total_frst, $list_frst_tmp_);
                $list_total_frst = [];
                foreach ($list_frst_tmp as $k => $v) {
                    $list_total_frst[$v['month_frst']][$v['trmnl_tp']] = $v['type'];
                }
                //Month1
                $list_dis_frst_rebuy = $model->field($field_dis_frst_rebuy)->where($map)->where("dt=month_frst")->group("month_frst,trmnl_tp")->order($order)->select();
                $list_frst_tmp_rebuy_ = $model->field($field_total_frst_rebuy)->where($map)->where("trmnl_tp in ('PC','M','APP') and dt=month_frst ")->group('month_frst')->select();
                $list_frst_tmp_web_rebuy = $model->field($field_total_frst_web_rebuy)->where($map)->where("trmnl_tp in ('PC','M') and dt=month_frst")->group('month_frst')->select();
                $list_frst_tmp_rebuy = array_merge($list_dis_frst_rebuy, $list_frst_tmp_web_rebuy);
                $list_total_frst_rebuy = [];
                foreach ($list_frst_tmp_rebuy as $k => $v) {
                    foreach ($list_frst_tmp_rebuy_ as $a => $b) {
                        if ($v['month_frst'] == $b['month_frst']) {
                            $list_total_frst_rebuy[$k]['month_frst'] = $v['month_frst'];
                            $list_total_frst_rebuy[$k]['type'] = round($v['type'] / $b['type'] * 100, 2) . '%';
                            $list_total_frst_rebuy[$k]['trmnl_tp'] = $v['trmnl_tp'];
                        }
                    }
                }
                $list_frst_tmp_rebuy = array_merge($list_total_frst_rebuy, $list_frst_tmp_rebuy_);
                $list_total_rebuy = [];
                foreach ($list_frst_tmp_rebuy as $k => $v) {
                    if ($v['trmnl_tp'] == 'TOTAL') {
                        $list_total_rebuy[$v['month_frst']][$v['trmnl_tp']] = $list_total_frst[$v['month_frst']][$v['trmnl_tp']] > 0 ? round($v['type'] / $list_total_frst[$v['month_frst']][$v['trmnl_tp']] * 100,
                                2) . '%' : 0;
                    } else {
                        $list_total_rebuy[$v['month_frst']][$v['trmnl_tp']] = $v['type'];
                    }
                }
            }
        } else {
            $voList_total = $model->field($field_total)->where($map)->where("trmnl_tp in ('PC','M','APP')")->group('month_frst,dt')->order($order)->select();
            $list_frst_tmp = $model->field($field_total_frst)->where($map)->where("trmnl_tp in ('PC','M','APP')")->group('month_frst')->select();
            $voList = $voList_total;
            $list_total_frst = [];
            foreach ($list_frst_tmp as $k => $v) {
                $list_total_frst[$v['month_frst']][$v['trmnl_tp']] = $v['type'];
            }
            //Month1数据单独算
            $voList_total_rebuy = $model->field($field_total_rebuy)->where($map)->where("trmnl_tp in ('PC','M','APP') and dt=month_frst")->group('month_frst,dt')->order($order)->select();
            $list_total_rebuy = [];
            foreach ($voList_total_rebuy as $k => $v) {
                if (I('post.display_type') == 'cnt') {
                    $list_total_rebuy[$v['month_frst']][$v['trmnl_tp']] = $v['type'];
                } else {
                    $list_total_rebuy[$v['month_frst']][$v['trmnl_tp']] = $list_total_frst[$v['month_frst']][$v['trmnl_tp']] > 0 ? round($v['type'] / $list_total_frst[$v['month_frst']][$v['trmnl_tp']] * 100,
                            2) . '%' : 0;
                }
            }
        }
        $list_total_frst = [];
        foreach ($list_frst_tmp as $k => $v) {
            $list_total_frst[$v['month_frst']][$v['trmnl_tp']] = $v['type'];
        }
        foreach ($voList as $k) {
            $fin_arr[$k['month_frst']][$k['trmnl_tp']] [$k['dt']] = $k['type'];
        }
        foreach ($voList as $k) {
            $month_frst[$k['dt']] = $k['dt'];
        }
        //把Month1数据塞到$fin_arr中
        foreach ($fin_arr as $k => $v) {
            foreach ($v as $kk => $v) {
                foreach ($list_total_rebuy as $a => $b) {
                    foreach ($b as $aa => $bb) {
                        if ($k == $a) {
                            $fin_arr[$k][$kk][$k] = $list_total_rebuy[$k][$kk];
                        }
                    }
                }
            }
        }
        //加展示类型
        if (I('post.display_type') == 'cnt') {
            foreach ($fin_arr as $k => $v) {
                foreach ($v as $v_k => $v_v) {
                    foreach ($month_frst as $c => $d) {
                        if ($c != $k) {
                            $fin_arr[$k][$v_k][$c] = '';
                        }
                    }
                }
                foreach ($v as $v_k => $v_v) {
                    foreach ($v_v as $a => $b) {
                        if ($a != $k) {
                            $fin_arr[$k][$v_k][$a] = $b;
                        }
                    }
                }
            }
        } elseif (I('post.display_type') == 'pnt') {
            foreach ($fin_arr as $k => $v) {
                foreach ($v as $v_k => $v_v) {
                    foreach ($month_frst as $c => $d) {
                        if ($c != $k) {
                            $fin_arr[$k][$v_k][$c] = '';
                        }
                    }
                }
                foreach ($v as $v_k => $v_v) {
                    foreach ($v_v as $a => $b) {
                        if ($a != $k) {
                            if ($v_k == 'TOTAL') {
                                $fin_arr[$k][$v_k][$a] = round($b / $list_total_frst[$k]['TOTAL'] * 100, 2) . '%';
                            } else {
                                $fin_arr[$k][$v_k][$a] = $b;
                            }
                        }
                    }
                }
            }
        }elseif(I('post.display_type')=='pnt_point'){
            foreach ($fin_arr as $k => $v) {
                foreach ($v as $v_k => $v_v) {
                    foreach ($month_frst as $c => $d) {
                        if ($c != $k) {
                            $fin_arr[$k][$v_k][$c] = '';
                        }
                    }
                }
                foreach ($v as $v_k => $v_v) {
                    foreach ($v_v as $a => $b) {
                        if ($a != $k) {
                            if ($v_k == 'TOTAL') {
                                $fin_arr[$k][$v_k][$a] = round($b/$list_total_frst[$k]['TOTAL'] * 100, 2).'%';
                            } else {
                                $fin_arr[$k][$v_k][$a] = $b;
                            }
                        }
                    }
                }
            }
            foreach($fin_arr as $k=>$v){
                foreach($v as $v_k=>$v_v){
                    foreach($v_v as $a=>$b){
                        $k_twel=date("Ym",mktime(0, 0, 0, date(substr($k,4,2)), 1, date(substr($k,0,4)-1)));
                        $a_twel=date("Ym",mktime(0, 0, 0, date(substr($a,4,2)), 1, date(substr($a,0,4)-1)));
                        if(I('post.distinguish')=='no'){
                            if(I('post.is_export')=='1'){
                                if($v_k == 'TOTAL'){
                                    $fin_arr[$k][$v_k][$a] = $fin_arr[$k][$v_k][$a].(substr($fin_arr[$k_twel]['TOTAL'][$a_twel],0,-1)>0?'('.(substr($fin_arr[$k]['TOTAL'][$a],0,-1)-substr($fin_arr[$k_twel]['TOTAL'][$a_twel],0,-1)).'%)':'');
                                }
                            }else{
                                if($v_k == 'TOTAL'){
                                    $fin_arr[$k][$v_k][$a] = $fin_arr[$k][$v_k][$a].(substr($fin_arr[$k_twel]['TOTAL'][$a_twel],0,-1)>0?'<nobr>'.(substr($fin_arr[$k]['TOTAL'][$a],0,-1)>substr($fin_arr[$k_twel]['TOTAL'][$a_twel],0,-1)?"<span style='color: blue'>":"<span style='color: red'>").'&nbsp;('.round(substr($fin_arr[$k]['TOTAL'][$a],0,-1)-substr($fin_arr[$k_twel]['TOTAL'][$a_twel],0,-1),2).'%)</nobr></span>':'');
                                }
                            }
                        }
                    }
                }
            }
        }elseif(I('post.display_type')=='pnt_percent'){
            foreach ($fin_arr as $k => $v) {
                foreach ($v as $v_k => $v_v) {
                    foreach ($month_frst as $c => $d) {
                        if ($c != $k) {
                            $fin_arr[$k][$v_k][$c] = '';
                        }
                    }
                }
                foreach ($v as $v_k => $v_v) {
                    foreach ($v_v as $a => $b) {
                            if ($a != $k) {
                                if ($v_k == 'TOTAL') {
                                    $fin_arr[$k][$v_k][$a] = round($b/$list_total_frst[$k]['TOTAL'] * 100, 2).'%';
                                } else {
                                    $fin_arr[$k][$v_k][$a] = $b;
                                }
                            }
                    }
                }
            }
            foreach($fin_arr as $k=>$v){
                foreach($v as $v_k=>$v_v){
                    foreach($v_v as $a=>$b){
                        $k_twel=date("Ym",mktime(0, 0, 0, date(substr($k,4,2)), 1, date(substr($k,0,4)-1)));
                        $a_twel=date("Ym",mktime(0, 0, 0, date(substr($a,4,2)), 1, date(substr($a,0,4)-1)));
                        if(I('post.distinguish')=='no'){
                            if(I('post.is_export')=='1'){
                                if($v_k == 'TOTAL'){
                                    $fin_arr[$k][$v_k][$a] = $fin_arr[$k][$v_k][$a].(substr($fin_arr[$k_twel]['TOTAL'][$a_twel],0,-1)>0? '('.(($fin_arr[$k]['TOTAL'][$a]-$fin_arr[$k_twel]['TOTAL'][$a_twel])/$fin_arr[$k_twel]['TOTAL'][$a_twel]*100).'%)':'');
                                }
                            }else{
                                if($v_k == 'TOTAL'){
                                    $fin_arr[$k][$v_k][$a] = $fin_arr[$k][$v_k][$a].(substr($fin_arr[$k_twel]['TOTAL'][$a_twel],0,-1)>0?'<nobr>'.(substr($fin_arr[$k]['TOTAL'][$a],0,-1)>substr($fin_arr[$k_twel]['TOTAL'][$a_twel],0,-1)?"<span style='color: blue'>":"<span style='color: red'>").'&nbsp;('.round(($fin_arr[$k]['TOTAL'][$a]-$fin_arr[$k_twel]['TOTAL'][$a_twel])/$fin_arr[$k_twel]['TOTAL'][$a_twel]*100,2).'%)</nobr></span>':'');
                                }
                            }
                        }
                    }
                }
            }
        }
        if(I('post.is_export')=='1'){
            $list = array();
            foreach ($fin_arr as $k => $v) {
                foreach ($v as $a => $b) {
                    foreach ($b as $c => $d) {
                        $list[$k . $a]['dt'] = $k;
                        $list[$k . $a][$a] = $a;
                        $list[$k . $a]['frst_ord'] = $list_total_frst[$k][$a];
                        $list[$k . $a][$c] = $d;
                    }
                }
            }
            if(I('post.order')=='month'){
//                $article = ['月份','设备','首单数','Month 1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','33','34','35','36'];
                $article = ['月份','设备','首单数','Month 1'];
                for($i=2;$i<$month_num;$i++){
                    array_push($article,$i);
                }
            }else{
                $article=['月份','设备','首单数'];
                foreach($month_frst as $k=>$v){
                    array_push($article,$v);
                }
            }
            $this->excel_export($list, $article, '复购Cohort统计导出');
            exit;
        }else{
            $this->assign('order', I('post.order'));
            $this->assign('month_num', $month_num);
            $this->assign('month', $month_frst);
            $this->assign('list_frst_rebuy', $list_total_rebuy);
            $this->assign('list_total_frst', $list_total_frst);
            $this->assign('list', $fin_arr);
            $data = $this->fetch('Echarts:' . 'form_multibuy_cohort_new');
            echo $data;
            exit;
        }
    }
    /**
     * 复购Cohort统计(新)  大码复购率统计 共用一个方法
     * author @chenmin 20180131 14:00
     */
    function statistic_multi_buy_new(){
        if(I('post.type')=='original'){
            $model = M('dm_ord_rebuy_cohort_m', null, 'Mysql_WH');
        }else{
            $model = M('dm_ord_rebuy_cohort_plus_size_m', null, 'Mysql_WH');
        }
        $distinguish=I('post.distinguish'); //TOTAL,PC,M,ios,andriod
        //用户数，订单数和总金额
        $type = I('post.select_type');
        if ($type == 'mem_cnt') {
            //用户数
            $type_frst = 'frst_usr_cnt';
            //Month1
            $type_rebuy = "frst_rebuy_usr_cnt";
        } elseif ($type == 'ord_cnt') {
            //订单数
            $type_frst = 'frst_order_cnt';
            $type_rebuy = 'frst_rebuy_order_cnt';
        } elseif ($type == 'ord_amt') {
            //总金额
            $type_frst = 'frst_order_amt';
            $type_rebuy = 'frst_rebuy_order_amt';
        }
        if(I('post.type')=='original'){
            $start_date = ('' == I('post.start_date')) ? date("Y-m-d",mktime(0, 0, 0, date("m") - 36, 1, date("Y"))) : I('post.start_date');
        }else{
            $start_date = ('' == I('post.start_date')) ? '20171101' : I('post.start_date');
        }
        $end_date = ('' == I('post.end_date')) ? date("Y-m-d",mktime(0, 0, 0, date("m"), 1, date("Y"))) : I('post.end_date');
        $month_num=floor((strtotime($end_date)-strtotime($start_date))/(30*86400))+1;
        $month_num_=floor((strtotime('-1day')-strtotime($start_date))/(30*86400))+1;
        $start_date = date('Ym', strtotime($start_date));
        $end_date = date('Ym', strtotime($end_date));

        $query_date = array('start_date' => $start_date, 'end_date' => $end_date);
        $map['month_frst'] = array(array('EGT', $query_date['start_date']), array('ELT', $query_date['end_date']), 'and');
        $map['site_tp'] = I('post.site_tp');
        if (I('post.country_nm') != '') {
            $map['shpp_country_nm'] = I('post.country_nm');
        } else {
            $map['shpp_country_nm'] = 'total';
        }
        $where="trmnl_tp = '".$distinguish."'";
        if($distinguish=='total'){
            $distinguish_nm='TOTAL';
        }elseif($distinguish=='ios'){
            $distinguish_nm='IOS';
        }elseif($distinguish=='android'){
            $distinguish_nm='ANDROID';
        }else{
            $distinguish_nm=$distinguish;
        }
        $field_total = "month_frst,dt,round(sum($type),0) as type,'$distinguish_nm' as trmnl_tp";
        $field_total_rebuy = "month_frst,dt,round(sum($type_rebuy),0) as type,'$distinguish_nm' as trmnl_tp";
        $field_total_frst = "month_frst,round(sum($type_frst),0) as type,'$distinguish_nm' as trmnl_tp";
        //按月和按自然月的区别是：展现方式不一样
        if (I('post.order') == 'month') {
            $order = 'month_frst desc,dt asc,trmnl_tp desc';
        } elseif (I('post.order') == 'month_natural') {
            $order = 'month_frst desc,dt desc,trmnl_tp desc';
        }
        $voList_total = $model->field($field_total)->where($map)->where($where)->group('month_frst,dt')->order($order)->select();
        $list_frst_tmp = $model->field($field_total_frst)->where($map)->where($where)->group('month_frst')->select();
        $voList = $voList_total;
        $list_total_frst = [];
        foreach ($list_frst_tmp as $k => $v) {
            $list_total_frst[$v['month_frst']][$v['trmnl_tp']] = $v['type'];
        }
        //Month1数据单独算
        $voList_total_rebuy = $model->field($field_total_rebuy)->where($map)->where("$where and dt=month_frst")->group('month_frst,dt')->order($order)->select();
        $list_total_rebuy = [];
        foreach ($voList_total_rebuy as $k => $v) {
            if (I('post.display_type') == 'cnt') {
                $list_total_rebuy[$v['month_frst']][$v['trmnl_tp']] = $v['type'];
            } else {
                $list_total_rebuy[$v['month_frst']][$v['trmnl_tp']] = $list_total_frst[$v['month_frst']][$v['trmnl_tp']] > 0 ? round($v['type'] / $list_total_frst[$v['month_frst']][$v['trmnl_tp']] * 100,
                        2) . '%' : 0;
            }
        }
        $list_total_frst = [];
        foreach ($list_frst_tmp as $k => $v) {
            $list_total_frst[$v['month_frst']][$v['trmnl_tp']] = $v['type'];
        }
        foreach ($voList as $k) {
            $fin_arr[$k['month_frst']][$k['trmnl_tp']] [$k['dt']] = $k['type'];
        }
        foreach ($voList as $k) {
            $month_frst[$k['dt']] = $k['dt'];
        }
        //把Month1数据塞到$fin_arr中
        foreach ($fin_arr as $k => $v) {
            foreach ($v as $kk => $v) {
                foreach ($list_total_rebuy as $a => $b) {
                    foreach ($b as $aa => $bb) {
                        if ($k == $a) {
                            $fin_arr[$k][$kk][$k] = $list_total_rebuy[$k][$kk];
                        }
                    }
                }
            }
        }
        if(I('post.site_tp')=='shein' and I('post.display_type')=='shein_romwe'){
            // 比例（对比romwe）
            $map['site_tp'] = 'romwe';
            $voList_total = $model->field($field_total)->where($map)->where($where)->group('month_frst,dt')->order($order)->select();
            $list_frst_tmp = $model->field($field_total_frst)->where($map)->where($where)->group('month_frst')->select();
            $voList = $voList_total;
            $list_total_frst_romwe = [];
            foreach ($list_frst_tmp as $k => $v) {
                $list_total_frst_romwe[$v['month_frst']][$v['trmnl_tp']] = $v['type'];
            }
            //Month1数据单独算
            $voList_total_rebuy = $model->field($field_total_rebuy)->where($map)->where("$where and dt=month_frst")->group('month_frst,dt')->order($order)->select();
            $list_total_rebuy = [];
            foreach ($voList_total_rebuy as $k => $v) {
//                if (I('post.display_type') == 'cnt') {
//                    $list_total_rebuy[$v['month_frst']][$v['trmnl_tp']] = $v['type'];
//                } else {
                    $list_total_rebuy[$v['month_frst']][$v['trmnl_tp']] = $list_total_frst_romwe[$v['month_frst']][$v['trmnl_tp']] > 0 ?
                        round($v['type'] / $list_total_frst_romwe[$v['month_frst']][$v['trmnl_tp']] * 100,2).'%':0;
//                }
            }
            $list_total_frst_romwe = [];
            foreach ($list_frst_tmp as $k => $v) {
                $list_total_frst_romwe[$v['month_frst']][$v['trmnl_tp']] = $v['type'];
            }
            foreach ($voList as $k) {
                $fin_arr_romwe[$k['month_frst']][$k['trmnl_tp']] [$k['dt']] = $k['type'];
            }
            foreach ($voList as $k) {
                $month_frst[$k['dt']] = $k['dt'];
            }
            //把Month1数据塞到$fin_arr中
            foreach ($fin_arr_romwe as $k => $v) {
                foreach ($v as $kk => $v) {
                    foreach ($list_total_rebuy as $a => $b) {
                        foreach ($b as $aa => $bb) {
                            if ($k == $a) {
                                $fin_arr_romwe[$k][$kk][$k] = $list_total_rebuy[$k][$kk];
                            }
                        }
                    }
                }
            }
        }

        //加展示类型
        if (I('post.display_type') == 'cnt') {
            foreach ($fin_arr as $k => $v) {
                foreach ($v as $v_k => $v_v) {
                    foreach ($month_frst as $c => $d) {
                        if ($c != $k) {
                            $fin_arr[$k][$v_k][$c] = '';
                        }
                    }
                }
                foreach ($v as $v_k => $v_v) {
                    foreach ($v_v as $a => $b) {
                        if ($a != $k) {
                            $fin_arr[$k][$v_k][$a] = $b;
                        }
                    }
                }
            }
        } elseif (I('post.display_type') == 'pnt') {
            foreach ($fin_arr as $k => $v) {
                foreach ($v as $v_k => $v_v) {
                    foreach ($month_frst as $c => $d) {
                        if ($c != $k) {
                            $fin_arr[$k][$v_k][$c] = '';
                        }
                    }
                }
                foreach ($v as $v_k => $v_v) {
                    foreach ($v_v as $a => $b) {
                        if ($a != $k) {
                            $fin_arr[$k][$v_k][$a] = round($b / $list_total_frst[$k][$v_k] * 100, 2) . '%';
                        }
                    }
                }
            }

        }elseif(I('post.display_type')=='pnt_point'){
            foreach ($fin_arr as $k => $v) {
                foreach ($v as $v_k => $v_v) {
                    foreach ($month_frst as $c => $d) {
                        if ($c != $k) {
                            $fin_arr[$k][$v_k][$c] = '';
                        }
                    }
                }
                foreach ($v as $v_k => $v_v) {
                    foreach ($v_v as $a => $b) {
                        if ($a != $k) {
                            $fin_arr[$k][$v_k][$a] = round($b/$list_total_frst[$k][$v_k] * 100, 2).'%';
                        }
                    }
                }
            }
            foreach($fin_arr as $k=>$v){
                foreach($v as $v_k=>$v_v){
                    foreach($v_v as $a=>$b){
                        $k_twel=date("Ym",mktime(0, 0, 0, date(substr($k,4,2)), 1, date(substr($k,0,4)-1)));
                        $a_twel=date("Ym",mktime(0, 0, 0, date(substr($a,4,2)), 1, date(substr($a,0,4)-1)));
                        if(I('post.is_export')=='1'){
                            $fin_arr[$k][$v_k][$a] = $fin_arr[$k][$v_k][$a].(substr($fin_arr[$k_twel][$v_k][$a_twel],0,-1)>0?'('.(substr($fin_arr[$k][$v_k][$a],0,-1)-substr($fin_arr[$k_twel][$v_k][$a_twel],0,-1)).'%)':'');
                        }else{
                            $fin_arr[$k][$v_k][$a] = $fin_arr[$k][$v_k][$a].(substr($fin_arr[$k_twel][$v_k][$a_twel],0,-1)>0?'<nobr>'.(substr($fin_arr[$k][$v_k][$a],0,-1)>substr($fin_arr[$k_twel][$v_k][$a_twel],0,-1)?"<span style='color: blue'>":"<span style='color: red'>").'&nbsp;('.round(substr($fin_arr[$k][$v_k][$a],0,-1)-substr($fin_arr[$k_twel][$v_k][$a_twel],0,-1),2).'%)</nobr></span>':'');
                        }
                    }
                }
            }
        }elseif(I('post.display_type')=='pnt_percent'){
            foreach ($fin_arr as $k => $v) {
                foreach ($v as $v_k => $v_v) {
                    foreach ($month_frst as $c => $d) {
                        if ($c != $k) {
                            $fin_arr[$k][$v_k][$c] = '';
                        }
                    }
                }
                foreach ($v as $v_k => $v_v) {
                    foreach ($v_v as $a => $b) {
                        if ($a != $k) {
                            $fin_arr[$k][$v_k][$a] = round($b/$list_total_frst[$k][$v_k] * 100, 2).'%';
                        }
                    }
                }
            }
            foreach($fin_arr as $k=>$v){
                foreach($v as $v_k=>$v_v){
                    foreach($v_v as $a=>$b){
                        $k_twel=date("Ym",mktime(0, 0, 0, date(substr($k,4,2)), 1, date(substr($k,0,4)-1)));
                        $a_twel=date("Ym",mktime(0, 0, 0, date(substr($a,4,2)), 1, date(substr($a,0,4)-1)));
                        if(I('post.is_export')=='1'){
                            $fin_arr[$k][$v_k][$a] = $fin_arr[$k][$v_k][$a].(substr($fin_arr[$k_twel][$v_k][$a_twel],0,-1)>0? '('.(($fin_arr[$k][$v_k][$a]-$fin_arr[$k_twel][$v_k][$a_twel])/$fin_arr[$k_twel][$v_k][$a_twel]*100).'%)':'');
                        }else{
                            $fin_arr[$k][$v_k][$a] = $fin_arr[$k][$v_k][$a].(substr($fin_arr[$k_twel][$v_k][$a_twel],0,-1)>0?'<nobr>'.(substr($fin_arr[$k][$v_k][$a],0,-1)>substr($fin_arr[$k_twel][$v_k][$a_twel],0,-1)?"<span style='color: blue'>":"<span style='color: red'>").'&nbsp;('.round(($fin_arr[$k][$v_k][$a]-$fin_arr[$k_twel][$v_k][$a_twel])/$fin_arr[$k_twel][$v_k][$a_twel]*100,2).'%)</nobr></span>':'');
                        }
                    }
                }
            }
        }elseif(I('post.display_type')=='shein_romwe'){
            foreach ($fin_arr as $k => $v) {
                foreach ($v as $v_k => $v_v) {
                    foreach ($month_frst as $c => $d) {
                        if ($c != $k) {
                            $fin_arr[$k][$v_k][$c] = '';
                        }
                    }
                }
                foreach ($v as $v_k => $v_v) {
                    foreach ($v_v as $a => $b) {
                        if(I('post.is_export')=='1'){
                            if ($a != $k) {
                                $fin_arr[$k][$v_k][$a] = round($b / $list_total_frst[$k][$v_k] * 100, 2) . "%(".round($fin_arr_romwe[$k][$v_k][$a] / $list_total_frst_romwe[$k][$v_k] * 100, 2) . '%)';
                            }else{
                                $fin_arr[$k][$v_k][$a] = $b."(".$fin_arr_romwe[$k][$v_k][$a].')';
                            }
                        }else{
                            if ($a != $k) {
                                $fin_arr[$k][$v_k][$a] = round($b/$list_total_frst[$k][$v_k] * 100, 2).'%'.
                                    (round($fin_arr_romwe[$k][$v_k][$a]/$list_total_frst_romwe[$k][$v_k]*100, 2)>0?'<nobr>'.
                                        (round($fin_arr_romwe[$k][$v_k][$a]/$list_total_frst_romwe[$k][$v_k]*100, 2)>round($b/$list_total_frst[$k][$v_k]*100, 2)?
                                            "<span style='color: red'>":"<span style='color: blue'>").
                                        '&nbsp;('.round($fin_arr_romwe[$k][$v_k][$a]/$list_total_frst_romwe[$k][$v_k]*100, 2).'%)</nobr></span>':'');
                            }else{
                                $fin_arr[$k][$v_k][$a] = $b.
                                    (substr($fin_arr_romwe[$k][$v_k][$a],0,-1)>0?'<nobr>'.
                                        (substr($fin_arr_romwe[$k][$v_k][$a],0,-1)>substr($b,0,-1)?
                                            "<span style='color: red'>":"<span style='color: blue'>").'&nbsp;('.$fin_arr_romwe[$k][$v_k][$a].')</nobr></span>':'');

                            }
                        }
                    }
                }
            }
            foreach($list_total_frst as $k=>$v){
                foreach($list_total_frst_romwe as $a => $b){
                    if($k==$a){
                        if(I('post.is_export')=='1'){
                            $list_total_frst[$k][$distinguish_nm] = $v[$distinguish_nm].'('.$b[$distinguish_nm].')';
                        }else{
                            $list_total_frst[$k][$distinguish_nm] = $v[$distinguish_nm].
                                ($b[$distinguish_nm]>0?'<nobr>'.
                                    ($b[$distinguish_nm]>$v[$distinguish_nm]?
                                        "<span style='color: red'>":"<span style='color: blue'>").'&nbsp;('.$b[$distinguish_nm].')</nobr></span>':'');
                        }

                    }
                }
            }
        }

        if(I('post.is_export')=='1'){
            $list = array();
            foreach ($fin_arr as $k => $v) {
                foreach ($v as $a => $b) {
                    foreach ($b as $c => $d) {
                        $list[$k . $a]['dt'] = $k;
                        $list[$k . $a][$a] = $a;
                        $list[$k . $a]['frst_ord'] = $list_total_frst[$k][$a];
                        $list[$k . $a][$c] = $d;
                    }
                }
            }
            if(I('post.order')=='month'){
                $article = ['月份','设备','首单数','Month 1'];
                for($i=2;$i<$month_num_;$i++){
                    array_push($article,$i);
                }
            }else{
                $article=['月份','设备','首单数'];
                foreach($month_frst as $k=>$v){
                    array_push($article,$v);
                }
            }
            $this->excel_export($list, $article, '复购Cohort统计导出');
            exit;
        }else{
            $this->assign('order', I('post.order'));
            $this->assign('month_num', $month_num_);
            $this->assign('month', $month_frst);
            $this->assign('list_frst_rebuy', $list_total_rebuy);
            $this->assign('list_total_frst', $list_total_frst);
            $this->assign('list', $fin_arr);
            if(I('post.site_tp')!='shein' and I('post.display_type')=='shein_romwe'){
                $data='不显示数据';
            }else{
                if(I('post.type')=='original'){
                    $data = $this->fetch('Echarts:form_multibuy_cohort_new');
                }else{
                    $data = $this->fetch('Echarts:form_multibuy_plus');
                }
            }
            echo $data;
            exit;
        }
    }
    /**
     * 复购Cohort统计(新) -明细导出
     * author @chenmin 20180206 11:00
     */
    function exp_multi_buy_new_exp(){
        ini_set('memory_limit','512M');
        $model = M('dm_ord_rebuy_cohort_m', null, 'Mysql_WH');
        $distinguish=I('post.distinguish'); //TOTAL,PC,M,ios,andriod
        //用户数，订单数和总金额
        $type = I('post.select_type');
        if ($type == 'mem_cnt') {
            //用户数
            $type_frst = 'frst_usr_cnt';
            //Month1
            $type_rebuy = "frst_rebuy_usr_cnt";
        } elseif ($type == 'ord_cnt') {
            //订单数
            $type_frst = 'frst_order_cnt';
            $type_rebuy = 'frst_rebuy_order_cnt';
        } elseif ($type == 'ord_amt') {
            //总金额
            $type_frst = 'frst_order_amt';
            $type_rebuy = 'frst_rebuy_order_amt';
        }
        $map['site_tp']=I('post.site_tp');
        $start_date = ('' == I('post.start_date')) ? date("Y-m-d",mktime(0, 0, 0, date("m") - 36, 1, date("Y"))) : I('post.start_date');
        $end_date = ('' == I('post.end_date')) ? date("Y-m-d",mktime(0, 0, 0, date("m")-1, 1, date("Y"))) : I('post.end_date');
        $month_num_=floor((strtotime('-1day')-strtotime($start_date))/(30*86400))+1;
        $start_date = date('Ym', strtotime($start_date));
        $end_date = date('Ym', strtotime($end_date));

        $map['month_frst'] = array(array('EGT', $start_date), array('ELT', $end_date), 'and');
        $where="trmnl_tp = '".$distinguish."'";
        $where .= " and shpp_country_nm is not null";
        $where .= " and site_tp is not null and site_tp != 'platform'";
        if($distinguish=='total'){
            $distinguish_nm='TOTAL';
        }elseif($distinguish=='ios'){
            $distinguish_nm='IOS';
        }elseif($distinguish=='android'){
            $distinguish_nm='ANDROID';
        }else{
            $distinguish_nm=$distinguish;
        }
        $field_total = "month_frst,dt,site_tp,shpp_country_nm,round(sum($type),0) as type,'$distinguish_nm' as trmnl_tp";
        $field_total_rebuy = "month_frst,dt,site_tp,shpp_country_nm,round(sum($type_rebuy),0) as type,'$distinguish_nm' as trmnl_tp";
        $field_total_frst = "month_frst,site_tp,shpp_country_nm,round(sum($type_frst),0) as type,'$distinguish_nm' as trmnl_tp";
        //按月和按自然月的区别是：展现方式不一样
        if (I('post.order') == 'month') {
            $order = 'month_frst desc,dt asc,trmnl_tp desc,site_tp desc';
        } elseif (I('post.order') == 'month_natural') {
            $order = 'month_frst desc,dt desc,trmnl_tp desc';
        }
        //建立一个国家数组
        $list_country=$model->field("distinct shpp_country_nm")->where($map)->where($where)->order("shpp_country_nm desc")->select();
        foreach($list_country as $k=>$v){
            $country[]=$v['shpp_country_nm'];
        }
//        $site_tp=['shein','romwe','emmastyle','emmacloth'];
        $site_tp=I('post.site_tp');
        $voList_total = $model->field($field_total)->where($map)->where($where)->group('month_frst,dt,site_tp,shpp_country_nm')->order($order)->select();
        $list_frst_tmp = $model->field($field_total_frst)->where($map)->where($where)->group('month_frst,site_tp,shpp_country_nm')->select();
        $voList = $voList_total;
        $list_total_frst = [];
        foreach ($list_frst_tmp as $k => $v) {
            $list_total_frst[$v['month_frst']][$v['trmnl_tp']][$v['shpp_country_nm']][$v['site_tp']] = $v['type'];
        }
        //Month1数据单独算
        $voList_total_rebuy = $model->field($field_total_rebuy)->where($map)->where("$where and dt=month_frst")->group('month_frst,dt,site_tp,shpp_country_nm')->order($order)->select();
        $list_total_rebuy = [];
        foreach ($voList_total_rebuy as $k => $v) {
            if (I('post.display_type') == 'cnt') {
                $list_total_rebuy[$v['month_frst']][$v['trmnl_tp']][$v['shpp_country_nm']][$v['site_tp']] = $v['type'];
            } else {
                $list_total_rebuy[$v['month_frst']][$v['trmnl_tp']][$v['shpp_country_nm']][$v['site_tp']] = $list_total_frst[$v['month_frst']][$v['trmnl_tp']][$v['shpp_country_nm']][$v['site_tp']] > 0 ? round($v['type'] / $list_total_frst[$v['month_frst']][$v['trmnl_tp']][$v['shpp_country_nm']][$v['site_tp']] * 100,
                        2) . '%' : 0;
            }
        }
        $list_total_frst = [];
        foreach ($list_frst_tmp as $k => $v) {
            $list_total_frst[$v['month_frst']][$v['trmnl_tp']][$v['shpp_country_nm']][$site_tp] = $v['type'];
        }
        foreach ($voList as $k) {
            $fin_arr[$k['month_frst']][$k['trmnl_tp']] [$k['dt']][$k['shpp_country_nm']][$site_tp] = $k['type'];
        }
        foreach ($voList as $k) {
            $month_frst[$k['dt']] = $k['dt'];
        }
        //把Month1数据塞到$fin_arr中
        foreach ($fin_arr as $k => $v) {
            foreach ($v as $kk => $v) {
                foreach ($list_total_rebuy as $a => $b) {
                    foreach ($b as $aa => $bb) {
                        if ($k == $a) {
                            foreach($country as $coun=>$val){
                                $fin_arr[$k][$kk][$k][$val][$site_tp] = $list_total_rebuy[$k][$kk][$val][$site_tp];
                            }
                        }
                    }
                }
            }
        }
        //加展示类型
        if (I('post.display_type') == 'cnt') {
            foreach ($fin_arr as $k => $v) {
                foreach ($v as $v_k => $v_v) {
                    foreach ($month_frst as $c => $d) {
                        if ($c != $k) {
                            foreach($country as $coun=>$val){
                                $fin_arr[$k][$v_k][$c][$val][$site_tp] = '';
                            }
                        }
                    }
                }
                foreach ($v as $v_k => $v_v) {
                    foreach ($v_v as $a => $b) {
                        if ($a != $k) {
                            foreach($country as $coun=>$val){
                                $fin_arr[$k][$v_k][$a][$val][$site_tp] = $b[$val][$site_tp];
                            }
                        }
                    }
                }
            }
        } elseif (I('post.display_type') == 'pnt') {
            foreach ($fin_arr as $k => $v) {
                foreach ($v as $v_k => $v_v) {
                    foreach ($month_frst as $c => $d) {
                        if ($c != $k) {
                            foreach($country as $coun=>$val){
                                $fin_arr[$k][$v_k][$c][$val][$site_tp] = '';
                            }
                        }
                    }
                }
                foreach ($v as $v_k => $v_v) {
                    foreach ($v_v as $a => $b) {
                        if ($a != $k) {
                            foreach($country as $coun=>$val){
                                $fin_arr[$k][$v_k][$a][$val][$site_tp] = round($b[$val][$site_tp] / $list_total_frst[$k][$v_k][$val][$site_tp] * 100, 2) . '%';
                            }
                        }
                    }
                }
            }
        }
        if(I('post.is_export')=='1'){
            $list = array();
            foreach ($fin_arr as $k => $v) {
                foreach ($v as $a => $b) {
                    foreach ($b as $c => $d) {
                        foreach($country as $coun=>$val){
                            $list[$k . $a . $val . $site_tp]['dt'] = $k;
                            $list[$k . $a . $val . $site_tp]['site_tp'] = $site_tp;
                            $list[$k . $a . $val . $site_tp]['shpp_country_nm'] = $val;
                            $list[$k . $a . $val . $site_tp][$a] = $a;
                            $list[$k . $a . $val . $site_tp]['frst_ord'] = $list_total_frst[$k][$a][$val][$site_tp];
                            $list[$k . $a . $val . $site_tp][$c] = $d[$val][$site_tp];
                        }
                    }
                }

            }
            $article = ['月份','主站点','国家','设备','首单数','Month 1'];
            for($i=2;$i<$month_num_;$i++){
                array_push($article,$i);
            }
            $this->excel_export($list, $article, '复购Cohort统计导出');
            exit;
        }
    }


    /**
     * 复购Cohort统计(新) -趋势
     * author @chenmin 20170901 10:00
     */
    function display_multibuy_cohort_trend(){
        if(I('post.type')=='original'){
            $model = M('dm_ord_rebuy_cohort_m', null, 'Mysql_WH');
        }else{
            $model = M('dm_ord_rebuy_cohort_plus_size_m', null, 'Mysql_WH');
        }
        $distinguish=I('post.distinguish'); //TOTAL,PC,M,ios,andriod
        //用户数，订单数和总金额
        $type = I('post.select_type');
        if ($type == 'mem_cnt') {
            //用户数
            $type_frst = 'frst_usr_cnt';
            //Month1
            $type_rebuy = "frst_rebuy_usr_cnt";
        } elseif ($type == 'ord_cnt') {
            //订单数
            $type_frst = 'frst_order_cnt';
            $type_rebuy = 'frst_rebuy_order_cnt';
        } elseif ($type == 'ord_amt') {
            //总金额
            $type_frst = 'frst_order_amt';
            $type_rebuy = 'frst_rebuy_order_amt';
        }
        if(I('post.type')=='original'){
            $start_date = ('' == I('post.start_date')) ? date("Y-m-d",mktime(0, 0, 0, date("m") - 36, 1, date("Y"))) : I('post.start_date');
        }else{
            $start_date = ('' == I('post.start_date')) ? '20171101' : I('post.start_date');
        }
        $end_date = ('' == I('post.end_date')) ? date("Y-m-d",mktime(0, 0, 0, date("m"), 1, date("Y"))) : I('post.end_date');
        $start_date = date('Ym', strtotime($start_date));
        $end_date = date('Ym', strtotime($end_date));
        $query_date = array('start_date' => $start_date, 'end_date' => $end_date);
        $map['month_frst'] = array(array('EGT', $query_date['start_date']), array('ELT', $query_date['end_date']), 'and');
        $map['site_tp'] = I('post.site_tp');
        if (I('post.country_nm') != '') {
            $map['shpp_country_nm'] = I('post.country_nm');
        } else {
            $map['shpp_country_nm'] = 'total';
        }
        $where="trmnl_tp = '".$distinguish."'";
        $field_total = "month_frst,dt,round(sum($type),0) as type,'$distinguish' as trmnl_tp";
        $field_total_rebuy = "month_frst,dt,round(sum($type_rebuy),0) as type,'$distinguish' as trmnl_tp";
        $field_total_frst = "month_frst,round(sum($type_frst),0) as type,'$distinguish' as trmnl_tp";

        $order = 'month_frst desc,dt asc,trmnl_tp desc';

        $voList = $model->field($field_total)->where($map)->where($where)->group('month_frst,dt')->order($order)->select();
        //首单数单独算
        $list_frst_tmp = $model->field($field_total_frst)->where($map)->where($where)->group('month_frst')->select();
        $list_total_frst = [];
        foreach ($list_frst_tmp as $k => $v) {
            $list_total_frst[$v['month_frst']][$v['trmnl_tp']] = $v['type'];
        }
        //Month1数据单独算
        $voList_total_rebuy = $model->field($field_total_rebuy)->where($map)->where("$where and dt=month_frst")->group('month_frst,dt')->order($order)->select();
        $list_total_rebuy = [];
        foreach ($voList_total_rebuy as $k => $v) {
            if (I('post.display_type') == 'cnt') {
                $list_total_rebuy[$v['month_frst']][$v['trmnl_tp']] = $v['type'];
            } else {
                $list_total_rebuy[$v['month_frst']][$v['trmnl_tp']] = $list_total_frst[$v['month_frst']][$v['trmnl_tp']] > 0 ? round($v['type'] / $list_total_frst[$v['month_frst']][$v['trmnl_tp']] * 100,
                    2) : 0;
            }
        }
        //首单数
        $list_total_frst = [];
        foreach ($list_frst_tmp as $k => $v) {
            $list_total_frst[$v['month_frst']][$v['trmnl_tp']] = $v['type'];
        }
        foreach ($voList as $k) {
            $fin_arr[$k['month_frst']][$k['trmnl_tp']] [$k['dt']] = $k['type'];
        }
        foreach ($voList as $k) {
            $month_frst[$k['dt']] = $k['dt'];
        }

        //把Month1数据塞到$fin_arr中
        foreach ($fin_arr as $k => $v) {
            foreach ($v as $kk => $v) {
                foreach ($list_total_rebuy as $a => $b) {
                    foreach ($b as $aa => $bb) {
                        if ($k == $a) {
                            $fin_arr[$k][$kk][$k] = $list_total_rebuy[$k][$kk];
                        }
                    }
                }
            }
        }
        //加展示类型
        if (I('post.display_type') == 'cnt') {
            foreach ($fin_arr as $k => $v) {
                foreach ($v as $v_k => $v_v) {
                    foreach ($month_frst as $c => $d) {
                        if ($c != $k) {
                            $fin_arr[$k][$v_k][$c] = '';
                        }
                    }
                }
                foreach ($v as $v_k => $v_v) {
                    foreach ($v_v as $a => $b) {
                        if ($a != $k) {
                            $fin_arr[$k][$v_k][$a] = $b;
                        }
                    }
                }
            }
        } else {
            foreach ($fin_arr as $k => $v) {
                foreach ($v as $v_k => $v_v) {
                    foreach ($month_frst as $c => $d) {
                        if ($c != $k) {
                            $fin_arr[$k][$v_k][$c] = '';
                        }
                    }
                }
                foreach ($v as $v_k => $v_v) {
                    foreach ($v_v as $a => $b) {
                        if ($a != $k) {
                            $fin_arr[$k][$v_k][$a] = round($b / $list_total_frst[$k][$v_k] * 100, 2);
                        }
                    }
                }
            }
        }
        $axis_arr = ['Month1','2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','33','34','35','36'];
        //把x轴塞到数组中
        $arr = [];
        foreach ($fin_arr as $k => $v) {
            foreach ($v[$distinguish] as $kk => $vv) {
                $arr[$k][$kk] = $vv;
            }
        }
        foreach ($arr as $date => $line) {
            foreach ($axis_arr as $key) {
                $res[$date][$key] = array_shift($line);
            }
        }
        //首单数$list_total_frst  塞到目标数组的最前面
//        foreach($res as $k=>$v){
//            foreach($list_total_frst as $a=>$frst)
//            if($k==$a){
//                array_unshift($res[$k],$frst['TOTAL']);
//            }
//        }
        $legend = array_keys($res);
        $this->xAxis = $axis_arr;
        $this->legend = $legend;
        $this->series = $res;
        if (I('post.display_type') == 'cnt') {
            $display_type = '数量';
        } else {
            $display_type = '比例';
        }
        $this->display_type = $display_type;
        $str_len_px = $this->string_length_px($res);
        $this->assign('str_len_px', json_encode($str_len_px));

        $data = $this->fetch("Echarts:echarts_line_multibuy_cohort_trend");
        echo $data;
        die;
    }

    /*
     * 复购统计
     * author @hiro 2016-07-22 09:14:48
     */
    public function statistic_multi_buy_ama(){
        $ssa_site_core = M('dm_ord_re_purchase_d',null,'Mysql_WH');
        $type = I('post.select_type');
        $field = "date_format(concat(month_id,'00'),'%Y-%m') as dt,country_nm,sum($type) as type";

        $query_date=$this->get_query_date();
        $map['month_id'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
        $map['site_tp'] = I('post.site_tp');
        if(I('post.site_id')=='TOTAL'){
            $map['site_id'] = 'total';
        }else{
            $map['site_id'] = I('post.site_id');
        }
        $list = $ssa_site_core->field($field)->where($map)->order('dt desc,country_nm asc')->group('dt,country_nm')->select();
        $arr = array('title' =>'复购统计' , 'dim1' => 'dt','dim2' => 'country_nm','field' => 'type'  ,'table' => 'ssa_site_core');
        $this->statistic_line($list,$arr,null);
    }
    /*
     * 产品库存结构2.6  产品数目
     */
    public function statistic_stk_daysct(){
        C('DB_CASE_LOWER',true);
        $stk_daysct_grp_d = M('dm_stk_daysct_grp_d',null,'Oracle_Amazon');
        $group=M('dw_pub_gds_grp_td',null,'Oracle_Amazon');
        $query_date=$this->get_query_date();
        $condition['a.inv_add_time'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
        $add_start_time=str_replace('-','',I('post.add_start_date'));
        $add_end_time=str_replace('-','',I('post.add_end_date'));
        if($add_start_time!='' and $add_end_time!=''){
            $condition['a.onsale_time']=array(array('EGT',$add_start_time),array('ELT',$add_end_time),'and');
        }elseif($add_start_time!='' and $add_end_time==''){
            $condition['a.onsale_time']=array("EGT",$add_start_time);
        }elseif($add_start_time=='' and $add_end_time!=''){
            $condition['a.onsale_time']=array("ELT",$add_end_time);
        }
        $warehouse_list=I('post.warehouse_list');
        if (strlen($warehouse_list)>0) {
            $condition['a.inventoty_tp'] = array('in',I('post.warehouse_list'));
        }else{
            $condition['a.inventoty_tp'] = array('exp','is not null');
        }
        $flag="time";
        $condition['b.grp_nm'] = array('exp','is not null');
        $condition['a.day_sct'] = array('neq','未入库');
        $field="to_char(to_date(A.inv_add_time,'yyyymmdd'),'yyyy-mm-dd') AS dt,a.day_sct as day_sct,b.grp_nm as grp_nm,sum(a.inv_cnt) as inv_cnt";
        $list=$stk_daysct_grp_d->join('a left outer join dw_pub_gds_grp_td b on b.grp_id=a.grp_id')->where($condition)->field($field)->order("dt desc")->group('inv_add_time,day_sct,grp_nm')->select();
        $groupList=$group->field('distinct grp_nm')->order("substr(grp_nm,1,2),replace(grp_nm,substr(grp_nm,1,2),'')")->select();
        $arr = array('title'=>'产品数量库存结构','dim1'=>'dt','dim2'=>'day_sct','field'=>'inv_cnt','flag'=>'xAxisNum','area'=>'area','dimGroup'=>$groupList,'type'=>'day_sct','type2'=>'inv_cnt');
        $this->statistic_bar_legend($list,$arr,$flag);
    }


    /*
     * 产品库存结构2.6  产品销量占比
     */
    public function statistic_stk_daysct_rate(){
        C('DB_CASE_LOWER',true);
        $stk_daysct_grp_d = M('stk_daysct_grp_d','dm_','Oracle_Amazon');
        $group=M('pub_gds_grp_td','dw_','Oracle_Amazon');
        $query_date=$this->get_query_date();
        $condition['a.inv_add_time'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
        $add_start_time=str_replace('-','',I('post.add_start_date'));
        $add_end_time=str_replace('-','',I('post.add_end_date'));
        if($add_start_time!='' and $add_end_time!=''){
            $condition['a.onsale_time']=array(array('EGT',$add_start_time),array('ELT',$add_end_time),'and');
        }elseif($add_start_time!='' and $add_end_time==''){
            $condition['a.onsale_time']=array("EGT",$add_start_time);
        }elseif($add_start_time=='' and $add_end_time!=''){
            $condition['a.onsale_time']=array("ELT",$add_end_time);
        }
        $condition['b.grp_nm'] = array('exp','is not null');
        $warehouse_list=I('post.warehouse_list');
        if (strlen($warehouse_list)>0) {
            $condition['a.inventoty_tp'] = array('in',I('post.warehouse_list'));
        }else{
            $condition['a.inventoty_tp'] = array('exp','is not null');
        }
        $flag="time";
        $groupList=$group->field('distinct grp_nm')->order("substr(grp_nm,1,2),replace(grp_nm,substr(grp_nm,1,2),'')")->select();
        $field1="to_char(to_date(A.inv_add_time,'yyyymmdd'),'yyyy-mm-dd') AS dt,a.inventoty_tp as inventoty_tp,a.day_sct as day_sct,b.grp_nm as grp_nm,sum(a.inv_cnt) as inv_cnt";
        $field2 = "f.dt as dt,sum(f.inv_cnt) as sums";
        $list1=$stk_daysct_grp_d->join('a left outer join dw_pub_gds_grp_td b on b.grp_id=a.grp_id')->where($condition)->field($field1)->order("dt desc")->group('inv_add_time,day_sct,b.grp_nm,inventoty_tp')->select();
        $list_co=$stk_daysct_grp_d->join('a left outer join dw_pub_gds_grp_td b on b.grp_id=a.grp_id')->where($condition)->field($field1)->order("dt desc")->group('inv_add_time,day_sct,b.grp_nm,inventoty_tp')->select(false);
        $sum_list=$stk_daysct_grp_d->table($list_co.'f')->field($field2)->order("dt")->group("dt")->select();
        foreach($list1 as $k=>$v){
            foreach($sum_list as $kk=>$vv){
                if($v['dt']==$vv['dt']){
                    $list1[$k]['inv_cnt']=round($list1[$k]['inv_cnt']/$sum_list[$kk]['sums']*100,2);
                }
            }
        }
        $arr = array('title'=>'产品数量占比(%)','dim1'=>'dt','dim2'=>'day_sct','field'=>'inv_cnt','flag'=>'xAxisNum','area'=>'area','dimGroup'=>$groupList,'type'=>'day_sct','type2'=>'inv_cnt');
        $this->statistic_bar_legend($list1,$arr,$flag);
    }

    /*
     * 产品库存结构2.6  产品金额
     */
    public function statistic_stk_daysct_amt(){
        C('DB_CASE_LOWER',true);
        $stk_daysct_grp_d = M('stk_daysct_grp_d','dm_','Oracle_Amazon');
        $group=M('pub_gds_grp_td','dw_','Oracle_Amazon');
        $query_date=$this->get_query_date();
        $condition['a.inv_add_time'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
        $add_start_time=str_replace('-','',I('post.add_start_date'));
        $add_end_time=str_replace('-','',I('post.add_end_date'));
        if($add_start_time!='' and $add_end_time!=''){
            $condition['a.onsale_time']=array(array('EGT',$add_start_time),array('ELT',$add_end_time),'and');
        }elseif($add_start_time!='' and $add_end_time==''){
            $condition['a.onsale_time']=array("EGT",$add_start_time);
        }elseif($add_start_time=='' and $add_end_time!=''){
            $condition['a.onsale_time']=array("ELT",$add_end_time);
        }
        $warehouse_list=I('post.warehouse_list');
        if (strlen($warehouse_list)>0) {
            $condition['a.inventoty_tp'] = array('in',I('post.warehouse_list'));
        }else{
            $condition['a.inventoty_tp'] = array('exp','is not null');
        }
        $flag="time";
        $condition['b.grp_nm'] = array('exp','is not null');
        $condition['a.day_sct'] = array('neq','未入库');
        $field="to_char(to_date(A.inv_add_time,'yyyymmdd'),'yyyy-mm-dd') AS dt,a.day_sct as day_sct,b.grp_nm as grp_nm,to_char(sum(a.inv_amnt),'fm9999999.00') as inv_amnt";
        $list=$stk_daysct_grp_d->join('a left outer join dw_pub_gds_grp_td b on b.grp_id=a.grp_id')->where($condition)->field($field)->order("dt desc")->group('inv_add_time,day_sct,grp_nm')->select();
        $groupList=$group->field('distinct grp_nm')->order("substr(grp_nm,1,2),replace(grp_nm,substr(grp_nm,1,2),'')")->select();
        $arr = array('title'=>'产品数量库存结构','dim1'=>'dt','dim2'=>'day_sct','field'=>'inv_amnt','flag'=>'xAxisNum','area'=>'area','dimGroup'=>$groupList,'type'=>'day_sct','type2'=>'inv_amnt');
        $this->statistic_bar_legend($list,$arr,$flag);
    }

    /*
     * 产品库存结构2.6  产品金额总库存
     */
    public function statistic_stk_daysct_amt_all(){
        C('DB_CASE_LOWER',true);
        $stk_daysct_grp_d = M('stk_daysct_grp_d','dm_','Oracle_Amazon');
        $query_date=$this->get_query_date();
        $condition['a.inv_add_time'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
        $add_start_time=str_replace('-','',I('post.add_start_date'));
        $add_end_time=str_replace('-','',I('post.add_end_date'));
        if($add_start_time!='' and $add_end_time!=''){
            $condition['a.onsale_time']=array(array('EGT',$add_start_time),array('ELT',$add_end_time),'and');
        }elseif($add_start_time!='' and $add_end_time==''){
            $condition['a.onsale_time']=array("EGT",$add_start_time);
        }elseif($add_start_time=='' and $add_end_time!=''){
            $condition['a.onsale_time']=array("ELT",$add_end_time);
        }
        $warehouse_list=I('post.warehouse_list');
        if (strlen($warehouse_list)>0) {
            $condition['a.inventoty_tp'] = array('in',I('post.warehouse_list'));
        }else{
            $condition['a.inventoty_tp'] = array('exp','is not null');
        }
        $flag="time";
        $condition['a.day_sct'] = array('neq','未入库');
        $condition['b.grp_nm'] = array('exp','is not null');
        $field="to_char(to_date(A.inv_add_time,'yyyymmdd'),'yyyy-mm-dd') AS dt,a.day_sct as day_sct,sum(a.inv_amnt) as cost_rmb_amt";
        $list=$stk_daysct_grp_d->join('a left outer join dw_pub_gds_grp_td b on b.grp_id=a.grp_id')->where($condition)->field($field)->order("dt desc")->group('inv_add_time,day_sct')->select();
        $arr = array('title'=>'产品金额总库存','dim1'=>'dt','dim2'=>'day_sct','field'=>'cost_rmb_amt','area'=>'area','all'=>'all');
        $this->statistic_bar_legend($list,$arr,$flag);
    }

    /*
     * 产品库存结构2.6  产品金额占比
     */
    function statistic_stk_daysct_amt_rate(){
        C('DB_CASE_LOWER',true);
        $stk_daysct_grp_d = M('stk_daysct_grp_d','dm_','Oracle_Amazon');
        $group=M('pub_gds_grp_td','dw_','Oracle_Amazon');
        $query_date=$this->get_query_date();
        $condition['a.inv_add_time'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
        $add_start_time=str_replace('-','',I('post.add_start_date'));
        $add_end_time=str_replace('-','',I('post.add_end_date'));
        if($add_start_time!='' and $add_end_time!=''){
            $condition['a.onsale_time']=array(array('EGT',$add_start_time),array('ELT',$add_end_time),'and');
        }elseif($add_start_time!='' and $add_end_time==''){
            $condition['a.onsale_time']=array("EGT",$add_start_time);
        }elseif($add_start_time=='' and $add_end_time!=''){
            $condition['a.onsale_time']=array("ELT",$add_end_time);
        }
        $condition['b.grp_nm'] = array('exp','is not null');
        $warehouse_list=I('post.warehouse_list');
        if (strlen($warehouse_list)>0) {
            $condition['a.inventoty_tp'] = array('in',I('post.warehouse_list'));
        }else{
            $condition['a.inventoty_tp'] = array('exp','is not null');
        }
        $flag='time';
        $groupList=$group->field('distinct grp_nm')->order("substr(grp_nm,1,2),replace(grp_nm,substr(grp_nm,1,2),'')")->select();
        $field1="to_char(to_date(A.inv_add_time,'yyyymmdd'),'yyyy-mm-dd') AS dt,a.inventoty_tp as inventoty_tp,a.day_sct as day_sct,b.grp_nm as grp_nm,sum(a.inv_amnt) as inv_amnt";
        $field2 = "f.dt as dt,sum(f.inv_cnt) as sums";
        $list1=$stk_daysct_grp_d->join('a left outer join dw_pub_gds_grp_td b on b.grp_id=a.grp_id')->where($condition)->field($field1)->order("dt desc")->group('inv_add_time,day_sct,b.grp_nm,inventoty_tp')->select();
        $list_co=$stk_daysct_grp_d->join('a left outer join dw_pub_gds_grp_td b on b.grp_id=a.grp_id')->where($condition)->field($field1)->order("dt desc")->group('inv_add_time,day_sct,b.grp_nm,inventoty_tp')->select(false);
        $sum_list=$stk_daysct_grp_d->table($list_co.'f')->field($field2)->order("dt")->group("dt")->select();
        foreach($list1 as $k=>$v){
            foreach($sum_list as $kk=>$vv){
                if($v['dt']==$vv['dt']){
                    $list1[$k]['inv_amnt']=round($list1[$k]['inv_amnt']/$sum_list[$kk]['sums']*100,2);
                }
            }
        }
        $arr = array('title'=>'产品金额占比(%)','dim1'=>'dt','dim2'=>'day_sct','field'=>'inv_amnt','flag'=>'xAxisNum','area'=>'area','dimGroup'=>$groupList,'type2'=>'inv_amnt','type'=>'day_sct');
        $this->statistic_bar_legend($list1,$arr,$flag);
    }

    /*
     * 产品库存结构2.6  产品数目总库存
     */
    public function statistic_stk_daysct_all(){
        C('DB_CASE_LOWER',true);
        $stk_daysct_grp_d = M('stk_daysct_grp_d','dm_','Oracle_Amazon');
        $query_date=$this->get_query_date();
        $condition['a.inv_add_time'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
        $add_start_time=str_replace('-','',I('post.add_start_date'));
        $add_end_time=str_replace('-','',I('post.add_end_date'));
        if($add_start_time!='' and $add_end_time!=''){
            $condition['a.onsale_time']=array(array('EGT',$add_start_time),array('ELT',$add_end_time),'and');
        }elseif($add_start_time!='' and $add_end_time==''){
            $condition['a.onsale_time']=array("EGT",$add_start_time);
        }elseif($add_start_time=='' and $add_end_time!=''){
            $condition['a.onsale_time']=array("ELT",$add_end_time);
        }
        $warehouse_list=I('post.warehouse_list');
        if (strlen($warehouse_list)>0) {
            $condition['a.inventoty_tp'] = array('in',I('post.warehouse_list'));
        }else{
            $condition['a.inventoty_tp'] = array('exp','is not null');
        }
        $condition['a.day_sct'] = array('neq','未入库');
        $condition['b.grp_nm'] = array('exp','is not null');
        $field="to_char(to_date(A.inv_add_time,'yyyymmdd'),'yyyy-mm-dd') AS dt,a.day_sct as day_sct,sum(a.inv_cnt) as inv_cnt";
        $list=$stk_daysct_grp_d->join('a left outer join dw_pub_gds_grp_td b on b.grp_id=a.grp_id')->where($condition)->field($field)->order("dt desc")->group('inv_add_time,day_sct')->select();
        $arr = array('title'=>'产品数量总库存','dim1'=>'dt','dim2'=>'day_sct','field'=>'inv_cnt','area'=>'area','all'=>'all');
        $this->statistic_bar_legend($list,$arr,$flag);
    }
    /*
    * 销售品类占比
    * modified by chenmin 20170118 优化
    */
    function group_cate_rate(){
        C('DB_CASE_LOWER',true);
        if( I('post.order')=='month' ){
            if(I('post.type')!='sale_sku'){
                $ord_grp = M('dm_ord_grp_m',null,'Oracle_WH');
            }else{
                $ord_grp = M('dm_pub_grp_m',null,'Oracle_WH');
            }
            $field = "to_char(to_date(dt,'yyyy-mm'),'yyyy-mm') as dt,";
        } else if ( I('post.order')=='week' ) {
            if(I('post.type')!='sale_sku'){
                $ord_grp = M('dm_ord_grp_w',null,'Oracle_WH');
            }else{
                $ord_grp = M('dm_pub_grp_w',null,'Oracle_WH');
            }
            $field = "substr(dt,'1',4)||'-'||substr(dt,'5',2) as dt,";
        } else {
            if(I('post.type')!='sale_sku'){
                $ord_grp = M('dm_ord_grp_d',null,'Oracle_WH');
            }else{
                $ord_grp = M('dm_pub_grp_d',null,'Oracle_WH');
            }
            $field = "to_char(to_date(dt,'yyyy-mm-dd'),'yyyy-mm-dd') as dt,";
        }
        if(I('post.type')=='sale_cnt'){
            //销售数量
            $field_sum ="$field round(sum(ord_cnt),0) as cnt";
            $field .= "cate_nm,round(sum(ord_cnt),0) as cnt";
            $title='各小组品类销售数量占比';
        }elseif(I('post.type')=='sale_amt'){
            //销售金额
            $field_sum ="$field round(sum(ord_amnt),0) as cnt";
            $field .= "cate_nm,round(sum(ord_amnt),0) as cnt";
            $title='各小组品类销售金额占比';
        }elseif(I('post.type')=='sale_sku'){
            //在售sku占比
            $field_sum ="$field count(distinct goods_sn) as cnt";
            $field .= "cate_nm,count(distinct goods_sn) as cnt";
            $title='各小组品类在售SKU占比';
        }

        if( I('post.order')=='month'){
            $start_date = ('' == I('post.start_date') ) ? date("Y-m-d",mktime(0, 0, 0, date("m")-6, 1, date("Y"))) : I('post.start_date');
            $end_date = ('' == I('post.end_date') ) ? date("Y-m-d",mktime(0, 0, 0, date("m"), 1, date("Y"))) : I('post.end_date');
            $start_date = date('Ym', strtotime($start_date));
            $end_date = date('Ym', strtotime($end_date));
            $timeYes=date('Y-m', strtotime($start_date));
        }else if(I('post.order')=='week'){
            $start_date = ('' == I('post.start_date') ) ? date("Y-m-d",strtotime('-1 Mon',mktime(0, 0, 0, date("m")-1, 1, date("Y")))) : I('post.start_date');
            $end_date = ('' == I('post.end_date') ) ? date("Y-m-d",strtotime("this monday")) : I('post.end_date');
            //开始时间都转化成每周的周一,避免跨年出现问题。比如，2016年1月1号是周五，如果不转化成周一，显示的就是2016年的53周，其实应该是2015年的53周。
            $week = date('w', strtotime($start_date));
            if($week == 1){
                $i = strtotime($start_date);
            }else{
                $i = strtotime($start_date)-($week-1)*86400;
            }
            $start_date=date('Y',$i).date('W',$i);
            $end_date = date('Y',strtotime($end_date)).date('W', strtotime($end_date));
            $timeYes=date('Y',$i).'-'.date('W',$i);
        }else{
            $start_date = ('' == I('post.start_date') ) ? date("Y-m-d",mktime(0, 0, 0, date("m"), date("d")-7, date("Y"))) : I('post.start_date');
            $end_date = ('' == I('post.end_date') ) ? date("Y-m-d",mktime(0, 0, 0, date("m"), date("d")-1, date("Y"))) : I('post.end_date');
            $start_date = date('Ymd', strtotime($start_date));
            $end_date = date('Ymd', strtotime($end_date));
            $timeYes= date('Y-m-d', strtotime($start_date));
        }
        $condition['dt'] = array(array('EGT',$start_date),array('ELT',$end_date),'and');
        $add_start_time=str_replace('-','',I('post.add_start_date'));
        $add_end_time=str_replace('-','',I('post.add_end_date'));
        if($add_start_time!='' and $add_end_time!=''){
            $condition['add_time']=array(array('EGT',$add_start_time),array('ELT',$add_end_time),'and');
        }elseif($add_start_time!='' and $add_end_time==''){
            $condition['add_time']=array("EGT",$add_start_time);
        }elseif($add_start_time=='' and $add_end_time!=''){
            $condition['add_time']=array("ELT",$add_end_time);
        }
        $condition['site_tp'] = I('post.site_tp');
        //小组
        $map['grp_nm']=array('in',I('post.buyer_grp_nm'));
        $model_=M('dw_pub_gds_grp_td',null,'Mysql_WH');
        $group_list=$model_->field('grp_id')->where($map)->select();
        if (I('post.buyer_grp_nm')!='') {
            foreach($group_list as $k=>$v){
                $grp[$k]=$v['grp_id'];
            }
            $condition['grp_id']=array('in',$grp);
        }
        $condition['cate_nm']=array('exp','is not null');
        $list_tmp=$ord_grp->where($condition)->field($field)->order('dt asc,cnt asc')->group('dt,cate_nm')->select();
        $list_sum_tmp=$ord_grp->field($field_sum)->where($condition)->order('dt desc')->group('dt')->select();
        foreach($list_sum_tmp as $k=>$v){
            $list_sum[$v['dt']]=$v;
        }

        $list=array();
        foreach($list_tmp as $k=>$v){
            $list[$v['dt']]['数量'][$v['cate_nm']]=$v['cnt'];
        }
        foreach($list as $k=>$v){
            foreach($v['数量'] as $a=>$b){
                $yAxis[$k][]=$a;
            }
        }
        $yAxis_jump=array();
        foreach($list as $k=>$v){
            $yAxis_jump[$k]=array(
                'type'=>'category',
                'data'=>array_keys($v['数量'])
            );
        }
        foreach($list as $k=>$v){
            foreach($v as $a=>$b){
                $list_jump[$k]= array(
                    'name'=>'数量',
                    'type'=>'bar',
                    'data'=>array_values($b)
                );
            }
        }
        //求占比
        $pnt=array();
        foreach($list as $k=>$v){
            foreach($list_sum as $a=>$b){
                if($k==$a){
                    foreach($v['数量'] as $c=>$d){
                        $pnt[$k][$c]=round($d/$b['cnt']*100,1);
                    }
                }
            }
        }
        $this->assign('yAxis',$yAxis);
        $this->assign('yAxis_jump',Json_encode($yAxis_jump));
        $this->assign('pnt',Json_encode($pnt));
        $this->assign('list_jump',Json_encode($list_jump));
        $arr = array('title'=>$title,'dim1'=>'cate_nm','field'=>'cnt','flag'=>'all','order'=>I('post.order'),'style'=>'cnt');
        $legend = array();
        $this-> yAxis=$yAxis;
        $this-> legend=array_unique($legend);
        $this-> series=$list;
        $this-> title=$arr['title'];
        if(I('post.order')=='month'){
            $start_date = ('' == I('post.start_date') ) ? date("Y-m-d",mktime(0, 0, 0, date("m")-6, 1, date("Y"))) : I('post.start_date');
            $end_date = ('' == I('post.end_date') ) ? date("Y-m-d",mktime(0, 0, 0, date("m"), 1, date("Y"))) : I('post.end_date');

            $timestamp = strtotime($start_date);
            while($timestamp <= strtotime($end_date)){
                $timestamp=mktime(0, 0, 0, date("m",$timestamp)+1, 1, date("Y",$timestamp));
                $date[] = date('Y-m', $timestamp-1);
            }
        }elseif(I('post.order')=='week'){
            $start_date = ('' == I('post.start_date') ) ? date("Y-m-d",strtotime('-1 Mon',mktime(0, 0, 0, date("m")-1, 1, date("Y")))) : I('post.start_date');
            $end_date = ('' == I('post.end_date') ) ? date("Y-m-d",strtotime("last monday")) : I('post.end_date');
            $week=date('w',strtotime($start_date));
            if($week==1){
                $unixTimeStart = strtotime($start_date);
            }else{
                $unixTimeStart = strtotime($start_date)-($week-1)*86400;
            }
            $unixTimeEnd = strtotime($end_date);
            for($i = $unixTimeStart; $i<= $unixTimeEnd; $i+= 604800){
                $date[] = date('Y',$i).'-'.date('W',$i);
            }
        }else{
            for($i=strtotime($start_date) ; $i<= strtotime($end_date); $i+=86400 ){
                $date[] = date('Y-m-d', $i);
            }
        }
        $this->assign('time',json_encode($date));
        $this->timeYes=$timeYes;
        $this-> timeline=$date;
        $data=$this->fetch('Echarts:echarts_bar_horizontal');
        $this->ajaxReturn(array('data'=>$data));
    }

    /*
     * 销售品类在售SKU占比2.4.3
     */
    public function statistic_cate_grp_sku(){
        C("DB_CASE_LOWER",true);
        if( I('post.order')=='month' ){
            $ord_grp = M('pub_grp_m','dm_','Oracle_Amazon');
            $field1 = "to_char(to_date(dt,'yyyy-mm'),'yyyy-mm') as dt,cate_nm,count(distinct goods_sn) as cnt";
        } else if ( I('post.order')=='week' ) {
            $ord_grp = M('pub_grp_w','dm_','Oracle_Amazon');
            $field1 = "substr(dt,'1',4)||'-'||substr(dt,'5',2) as dt,cate_nm,count(distinct goods_sn) as cnt";
        } else {
            $ord_grp = M('pub_grp_d','dm_','Oracle_Amazon');
            $field1 = "to_char(to_date(dt,'yyyy-mm-dd'),'yyyy-mm-dd') as dt,cate_nm,count(distinct goods_sn) as cnt";
        }
        $query_date=$this->get_query_date();
        $condition['dt'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
        $add_start_time=str_replace('-','',I('post.add_start_date'));
        $add_end_time=str_replace('-','',I('post.add_end_date'));
        if($add_start_time!='' and $add_end_time!=''){
            $condition['add_time']=array(array('EGT',$add_start_time),array('ELT',$add_end_time),'and');
        }elseif($add_start_time!='' and $add_end_time==''){
            $condition['add_time']=array("EGT",$add_start_time);
        }elseif($add_start_time=='' and $add_end_time!=''){
            $condition['add_time']=array("ELT",$add_end_time);
        }
        $condition['site_tp'] = I('post.site_tp');
        $group_list=I('post.buyer_grp_nm');
        if (strlen($group_list)>0) {
            if(strlen($group_list)>0 && strlen($group_list)<8){
                $condition['grp_nm'] = array('like','%'.$group_list.'%');
            }
            else{
                $condition['grp_nm'] = array('in',I('post.buyer_grp_nm'));
            }
        }else{
            $condition['grp_nm'] = array('exp','is not null');
        }
        if (I('post.start_date')!="") {
            $flag="time";
        }
        $condition['cate_nm']=array('exp','is not null');
        $list1=$ord_grp->where($condition)->field($field1)->order('dt desc')->group('dt,cate_nm')->select();
        $arr = array('title'=>'各小组品类在售SKU占比','dim1'=>'cate_nm','field'=>'cnt','flag'=>'all','order'=>I('post.order'),'style'=>'cnt');
        $this->statistic_funnel($list1,$arr);
    }
    /*
     * 销售颜色占比
     * modified by chenmin 20170109 12:00 图例颜色改成对应的颜色 ，比如图例展示的红色系，柱形的颜色就是红色的
     */
    public function statistic_color(){
        $where =" 1=1";
        C("DB_CASE_LOWER",true);
        if( I('post.order')=='month' ){
            $ord_grp = M('dm_ord_grp_m',null,'Oracle_Amazon');
            $field = "to_char(to_date(dt,'yyyy-mm'),'yyyy-mm') as dt,";
        } else if ( I('post.order')=='week' ) {
            $ord_grp = M('dm_ord_grp_w',null,'Oracle_Amazon');
            $field = "substr(dt,'1',4)||'-'||substr(dt,'5',2) as dt,";
        } else {
            $ord_grp = M('dm_ord_grp_d',null,'Oracle_Amazon');
            $field = "to_char(to_date(dt,'yyyy-mm-dd'),'yyyy-mm-dd') as dt,";
        }
        $query_date=$this->get_query_date();
        $where .= " and dt >= '".$query_date['start_date']."'";
        $where .= " and dt <= '".$query_date['end_date']."'";

        $add_start_time=str_replace('-','',I('post.add_start_date'));
        $add_end_time=str_replace('-','',I('post.add_end_date'));
        if($add_start_time!='' and $add_end_time!=''){
            $where .= " and add_time >= '".$add_start_time."'";
            $where .= " and add_time <= '".$add_end_time."'";
        }elseif($add_start_time!='' and $add_end_time==''){
            $where .=" and add_time >= '".$add_start_time."'";
        }elseif($add_start_time=='' and $add_end_time!=''){
            $where .= " and add_time <= '".$add_end_time."'";
        }
        $where .=" and site_tp = '". I('post.site_tp')."'";
        $where .= " and color_set_nm is not null";
        //小组
        $map['grp_nm']=array('in',I('post.buyer_grp_nm'));
        $model_=M('dw_pub_gds_grp_td',null,'Mysql_WH');
        $group_list=$model_->field('grp_id')->where($map)->select();
        if (I('post.buyer_grp_nm')!='') {
            foreach($group_list as $k=>$v){
                $grp[$k]=$v['grp_id'];
            }
            $grp=implode("','",$grp);
            $where .= " and grp_id in ('".$grp."')";
        }
        if(I('post.type')=='color_cnt'){
            $field .= "color_set_nm as color_nm,sum(ord_cnt) as ord_cnt";
            $list=$ord_grp->field($field)->where($where)->order('dt,color_set_nm desc')->group('dt,color_set_nm')->select();
            $arr = array('title'=>'各小组颜色数量','dim1'=>'dt','dim2'=>'color_nm','field'=>'ord_cnt');
        }elseif(I('post.type')=='color_cnt_rate'){
            $field1 = $field."color_set_nm as color_nm,sum(ord_cnt) as sum_ord_cnt";
            $field2 = "f.dt as dt,sum(f.sum_ord_cnt) as sums";
            $list1=$ord_grp->field($field1)->where($where)->order('dt,color_set_nm desc')->group('dt,color_set_nm')->select();
            $list_co=$ord_grp->field($field1)->where($where)->order('dt desc')->group('dt,color_set_nm')->select(false);
            $sum_list=$ord_grp->table($list_co.'f')->field($field2)->order("dt")->group("dt")->select();
            foreach($list1 as $k=>$v){
                foreach($sum_list as $kk=>$vv){
                    if($v['dt']==$vv['dt']){
                        $list1[$k]['sum_ord_cnt']=round($list1[$k]['sum_ord_cnt']/$sum_list[$kk]['sums']*100,2);
                    }
                }
            }
            $arr = array('title'=>'各小组颜色数量占比','dim1'=>'dt','dim2'=>'color_nm','field'=>'sum_ord_cnt');
            $list=$list1;
        }elseif(I('post.type')=='color_amt'){
            $field .="color_set_nm as color_nm,to_char(sum(ord_amnt),'fm999990.90') as ord_amnt";
            $list=$ord_grp->field($field)->where($where)->order('dt,color_set_nm desc')->group('dt,color_set_nm')->select();
            $arr = array('title'=>'各小组颜色金额','dim1'=>'dt','dim2'=>'color_nm','field'=>'ord_amnt');
        }elseif(I('post.type')=='color_amt_rate'){
            $field1=$field."color_set_nm as color_nm,sum(ord_amnt) as sum_ord_amnt";
            $field2 = "f.dt as dt,sum(f.sum_ord_amnt) as sums";
            $list1=$ord_grp->field($field1)->where($where)->order('dt,color_set_nm desc')->group('dt,color_set_nm')->select();
            $list_co=$ord_grp->field($field1)->where($where)->order('dt desc')->group('dt,color_set_nm')->select(false);
            $sum_list=$ord_grp->table($list_co.'f')->field($field2)->order("dt")->group("dt")->select();
            foreach($list1 as $k=>$v){
                foreach($sum_list as $kk=>$vv){
                    if($v['dt']==$vv['dt']){
                        $list1[$k]['sum_ord_amnt']=round($list1[$k]['sum_ord_amnt']/$sum_list[$kk]['sums']*100,2);
                    }
                }
            }
            $arr = array('title'=>'各小组颜色数量占比','dim1'=>'dt','dim2'=>'color_nm','field'=>'sum_ord_amnt');
            $list=$list1;
        }
        $legend = array();
        $xAxis = array();
        $series = array();
        $member = array();
        $date = array();

        $date=$this->get_dates($start_date,$end_date,$date);
        foreach ($list as $k => $v) {
            array_push($legend, $v[$arr['dim2']]);
            $result[$v[$arr['dim2']]][$v[$arr['dim1']]] = $v[$arr['field']];
        }
        if (I('post.order')=='week') {
            $xAxis = $date[1];
            $date = $date[2];
            $this-> xAxis=$xAxis;
        } else {
            $this-> xAxis=$date;
        }

        foreach ($result as $k => $v) {
            foreach ($date as $d) {
                if (!empty($v[$d])) {
                    $member = $v[$d];
                }else{
                    $member = 0;
                }
                $series[$k][$d] = $member;
            }
        }
        //特殊处理-分类国家销售统计-首字母排序,other置后
        if($arr['legend_list'] !=''){
            $this-> legend=array_unique($arr['legend_list']);
        }else{
            $this-> legend=array_unique($legend);
        }
        $this-> series=$series;
        $this-> title=$arr['title'];
        if ($arr['flag'] == 'line_bar') {
            $this-> line_field='转化率';
        }
        $data=$this->fetch('Echarts:echarts_bar_inside_color');

        $this->ajaxReturn(array('data'=>$data));
    }

    /*
     * 小组产品售罄率
     */
    public function statistic_stk_sold_out(){

        if( I('post.order')=='month' ){
            $dt ="date_format(concat(c.dt,'00'),'%Y-%m') as dt";
            $model = M('dm_stk_sold_out_m',null,'Mysql_WH');
            $field_dt = "b.static_month as dt,sum(a.put_cnt) as put_cnt,sum(a.out_cnt) as out_cnt,a.buyer_grp_nm";
            $group_dt ="static_month,buyer_grp_nm";
        } else if ( I('post.order')=='week' ) {
            $dt ="concat(substr(c.dt,'1',4),'-',substr(c.dt,'5',2)) as dt";
            $model = M('dm_stk_sold_out_w',null,'Mysql_WH');
            $field_dt = "b.static_week as dt,sum(a.put_cnt) as put_cnt,sum(a.out_cnt) as out_cnt,a.buyer_grp_nm";
            $group_dt ="static_week,buyer_grp_nm";
        } else {
            $dt ="date(c.dt) AS dt";
            $model = M('dm_stk_sold_out_d',null,'Mysql_WH');
            $field_dt = "b.date_key as dt,sum(a.put_cnt) as put_cnt,sum(a.out_cnt) as out_cnt,a.buyer_grp_nm";
            $group_dt ="date_key,buyer_grp_nm";
        }
        if(I('post.buyer_grp_nm') != ''){
            $map_sum['d.grp_nm'] = array('IN',I('post.buyer_grp_nm'));
            $type_b = "$dt,case when sum(c.put_cnt) >0 then round(sum(c.out_cnt)/sum(c.put_cnt) *100,2) else 0 end as rate ,d.grp_nm as type";
            $group_sum = 'dt,grp_nm';
        }else{
            if(I('post.grp_tp_list') != ''){
                $map_sum['d.grp_tp_nm'] = array('IN',I('post.grp_tp_list'));
            }
            $type_b = "$dt,case when sum(c.put_cnt) >0 then round(sum(c.out_cnt)/sum(c.put_cnt) *100,2) else 0 end as rate ,d.grp_tp_nm as type";
            $group_sum = 'dt,grp_tp_nm';
        }
        $field = "put_dt,sum(put_cnt) as put_cnt,sum(out_cnt) as out_cnt,buyer_grp_nm";
        $group = "put_dt,buyer_grp_nm";
        $add_start_time=('' == I('post.start_date') ) ? date("Ymd",mktime(0, 0, 0, date("m"), date("d")-30, date("Y"))) :str_replace('-','',I('post.start_date'));
        $add_end_time=('' == I('post.end_date') ) ? date("Ymd",mktime(0, 0, 0, date("m"), date("d"), date("Y"))) : str_replace('-','',I('post.end_date'));
        $map['put_dt']  = array(array('EGT',$add_start_time),array('ELT',$add_end_time),'and');
        if(I('post.warehouse') !='') {
            $map['inventory_area'] = array("IN", I('post.warehouse'));
        }
        if(I('post.inventoy') !='') {
            $tmp =I('post.inventoy');
            $map['inventory_cycle'] = $tmp[0];
            unset($tmp);
        }
        $subQuery_t1 = $model->field($field)->where($map)->where('buyer_grp_nm is not null')->group($group)->select(false);

        $subQuery_t2 = $model->table($subQuery_t1.' a')->join("dm_date_td b on a.put_dt = b.date_key")->field($field_dt)->group($group_dt)->select(false);
        $list = $model->table($subQuery_t2.' c')->join("dw_pub_gds_grp_td d on c.buyer_grp_nm = d.grp_id")->field($type_b)->where($map_sum)->group($group_sum)->select();
        $arr = array('title' =>'各小组售罄率' , 'dim1' => 'dt','dim2' => 'type','field' => 'rate'  ,'table' => 'ssa_site_core');
        $this->statistic_line($list,$arr,null);
    }


    /*
     * 小组产品的价格区间销售产品 2.5.1
     */
    public function statistic_ord_sct_grp(){
        C('DB_CASE_LOWER',true);
        if( I('post.order')=='month' ){
            $ord_grp = M('ord_grp_m','dm_','Oracle_WH');
            $field1 = "substr(a.dt,'1',4)||'-'||substr(a.dt,'5',2) as dt,a.sct_prc as sct,sum(a.ord_cnt) as sct_cnt";
        } else if ( I('post.order')=='week' ) {
            $ord_grp = M('ord_grp_w','dm_','Oracle_WH');
            $field1 = "substr(a.dt,'1',4)||'-'||substr(a.dt,'5',2) as dt,a.sct_prc as sct,sum(a.ord_cnt) as sct_cnt";
        } else {
            $ord_grp = M('ord_grp_d','dm_','Oracle_WH');
            $field1 = "to_char(to_date(a.dt,'yyyymmdd'),'yyyy-mm-dd') as dt,a.sct_prc as sct,sum(a.ord_cnt) as sct_cnt";
        }
        $query_date=$this->get_query_date();
        $condition['a.dt'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
        $add_start_time=str_replace('-','',I('post.add_start_date'));
        $add_end_time=str_replace('-','',I('post.add_end_date'));
        if($add_start_time!='' and $add_end_time!=''){
            $condition['a.add_time']=array(array('EGT',$add_start_time),array('ELT',$add_end_time),'and');
        }elseif($add_start_time!='' and $add_end_time==''){
            $condition['a.add_time']=array("EGT",$add_start_time);
        }elseif($add_start_time=='' and $add_end_time!=''){
            $condition['a.add_time']=array("ELT",$add_end_time);
        }
        $condition['site_tp'] = I('post.site_tp');
        $group_list=I('post.buyer_grp_nm');
        if (strlen($group_list)>0) {
            if(strlen($group_list)>0 && strlen($group_list)<8){
                $condition['b.grp_nm'] = array('like','%'.$group_list.'%');
            }
            else{
                $condition['b.grp_nm'] = array('in',I('post.buyer_grp_nm'));
            }
        }else{
            $condition['b.grp_nm'] = array('exp','is not null');
        }
        if (I('post.start_date')!="") {
            $flag="time";
        }
        $condition['a.sct_prc']=array('neq','0');
        $list=$ord_grp->join('a left outer join dw_pub_gds_grp_td b on b.grp_id=a.grp_id')
            ->where($condition)->field($field1)->order("dt desc,sct asc")->group('a.dt,a.sct_prc')->select();
        $arr = array('title'=>'销售产品的价格区间','dim1'=>'dt','dim2'=>'sct','field'=>'sct_cnt','legendArea'=>'area');
        $this->statistic_bar_legend($list,$arr,$flag);
    }

    /*
     * 小组产品的价格区间在售产品 2.5.2
     */
    public function statistic_pub_sct_grp(){
        C('DB_CASE_LOWER',true);
        if( I('post.order')=='month' ){
            $ord_grp = M('pub_grp_m','dm_','Oracle_Amazon');
            $field = "substr(a.dt,'1',4)||'-'||substr(a.dt,'5',2) as dt,a.sct_onsale as sct,count(distinct goods_sn) as sct_cnt";
        } else if ( I('post.order')=='week' ) {
            $ord_grp = M('pub_grp_w','dm_','Oracle_Amazon');
            $field = "substr(a.dt,'1',4)||'-'||substr(a.dt,'5',2) as dt,a.sct_onsale as sct,count(distinct goods_sn) as sct_cnt";
        } else {
            $ord_grp = M('pub_grp_d','dm_','Oracle_Amazon');
            $field = "to_char(to_date(a.dt,'yyyymmdd'),'yyyy-mm-dd') as dt,a.sct_onsale as sct,count(distinct goods_sn) as sct_cnt";
        }
        $query_date=$this->get_query_date();
        $condition['a.dt'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
        $add_start_time=str_replace('-','',I('post.add_start_date'));
        $add_end_time=str_replace('-','',I('post.add_end_date'));
        if($add_start_time!='' and $add_end_time!=''){
            $condition['a.add_time']=array(array('EGT',$add_start_time),array('ELT',$add_end_time),'and');
        }elseif($add_start_time!='' and $add_end_time==''){
            $condition['a.add_time']=array("EGT",$add_start_time);
        }elseif($add_start_time=='' and $add_end_time!=''){
            $condition['a.add_time']=array("ELT",$add_end_time);
        }
        $condition['site_tp'] = I('post.site_tp');
        $group_list=I('post.buyer_grp_nm');
        if (strlen($group_list)>0) {
            if(strlen($group_list)>0 && strlen($group_list)<8){
                $condition['b.grp_nm'] = array('like','%'.$group_list.'%');
            }
            else{
                $condition['b.grp_nm'] = array('in',I('post.buyer_grp_nm'));
            }
        }else{
            $condition['b.grp_nm'] = array('exp','is not null');
        }
        if (I('post.start_date')!="") {
            $flag="time";
        }
        $condition['a.sct_onsale']=array('neq','0');
        $list=$ord_grp->join('a left outer join dw_pub_gds_grp_td b on b.grp_id=a.grp_id')->where($condition)->field($field)->order("dt desc")->group('a.dt,a.sct_onsale')->select();
//        tp($ord_grp->getLastSql());die;
        $arr = array('title'=>'在售产品的价格区间','dim1'=>'dt','dim2'=>'sct','field'=>'sct_cnt','legendArea'=>'area');
        $this->statistic_bar_legend($list,$arr,$flag);
    }

    /*
     * 小组销售量2.1.1
     */
    public function statistic_sale_cnt(){
        C("DB_CASE_LOWER",true);
        if(I('post.buyer_grp_nm') != ''){
            $map_sum['b.grp_nm'] = array('IN',I('post.buyer_grp_nm'));
            $type_b = 'a.dt,sum(a.ord_cnt) as ord_cnt,b.grp_nm as type';
            $group_sum = 'a.dt,b.grp_nm';
        }else{
            if(I('post.grp_tp_list') != ''){
                $map_sum['b.grp_tp_nm'] = array('IN',I('post.grp_tp_list'));
            }
            $type_b = 'a.dt,sum(a.ord_cnt) as ord_cnt,b.grp_tp_nm as type';
            $group_sum = 'a.dt,b.grp_tp_nm';
        }
        if( I('post.order')=='month' ){
            $model = M('dm_ord_grp_m',null,'Oracle_Amazon');
            $field = "to_char(to_date(dt,'yyyy-mm'),'yyyy-mm') as dt,sum(ord_cnt) as ord_cnt,grp_id";
        } else if ( I('post.order')=='week' ) {
            $model = M('dm_ord_grp_w',null,'Oracle_Amazon');
            $field = "substr(dt,'1',4)||'-'||substr(dt,'5',2) as dt,sum(ord_cnt) as ord_cnt,grp_id";
        } else {
            $model = M('dm_ord_grp_d',null,'Oracle_Amazon');
            $field = "to_char(to_date(dt,'yyyy-mm-dd'),'yyyy-mm-dd') AS dt,sum(ord_cnt) as ord_cnt,grp_id";
        }
        $query_date=$this->get_query_date();
        $map['dt'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
        $add_start_time=str_replace('-','',I('post.add_start_date'));
        $add_end_time=str_replace('-','',I('post.add_end_date'));
        if($add_start_time!=''){
            $map['add_time']=array('EGT',$add_start_time);
        }
        if($add_end_time !='') {
            $map['add_time'] = array("ELT", $add_end_time);
        }
        $map['site_tp'] = I('post.site_tp');
        $subQuery_t1 = $model->field($field)->where($map)->where('grp_id is not null')->group('dt,grp_id')->select(false);
        $list = $model->table($subQuery_t1.' a')->join("dw_pub_gds_grp_td b on a.grp_id = b.grp_id")->field($type_b)->where($map_sum)->group($group_sum)->order('dt desc,type asc')->select();
        $arr = array('title' =>'各小组销量' , 'dim1' => 'dt','dim2' => 'type','field' => 'ord_cnt'  ,'table' => 'ssa_site_core');
        $this->statistic_bar($list,$arr);
    }

    /*
     * 小组销售量占比2.1.2
     */
    public function  statistic_sale_cnt_rate(){
        C("DB_CASE_LOWER",true);
        if(I('post.buyer_grp_nm') != ''){
            $map_sum['b.grp_nm'] = array('IN',I('post.buyer_grp_nm'));
            $type_b = 'a.dt,sum(a.ord_cnt) as ord_cnt,b.grp_nm as type';
            $group_sum = 'a.dt,b.grp_nm';
        }else{
            if(I('post.grp_tp_list') != ''){
                $map_sum['b.grp_tp_nm'] = array('IN',I('post.grp_tp_list'));
            }
            $type_b = 'a.dt,sum(a.ord_cnt) as ord_cnt,b.grp_tp_nm as type';
            $group_sum = 'a.dt,b.grp_tp_nm';
        }
        if( I('post.order')=='month' ){
            $model = M('dm_ord_grp_m',null,'Oracle_Amazon');
            $field = "to_char(to_date(dt,'yyyy-mm'),'yyyy-mm') as dt,sum(ord_cnt) as ord_cnt,grp_id";
        } else if ( I('post.order')=='week' ) {
            $model = M('dm_ord_grp_w',null,'Oracle_Amazon');
            $field = "substr(dt,'1',4)||'-'||substr(dt,'5',2) as dt,sum(ord_cnt) as ord_cnt,grp_id";
        } else {
            $model = M('dm_ord_grp_d',null,'Oracle_Amazon');
            $field = "to_char(to_date(dt,'yyyy-mm-dd'),'yyyy-mm-dd') AS dt,sum(ord_cnt) as ord_cnt,grp_id";
        }
        $query_date=$this->get_query_date();
        $map['dt'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
        $add_start_time=str_replace('-','',I('post.add_start_date'));
        $add_end_time=str_replace('-','',I('post.add_end_date'));
        if($add_start_time!=''){
            $map['add_time']=array('EGT',$add_start_time);
        }
        if($add_end_time !='') {
            $map['add_time'] = array("ELT", $add_end_time);
        }
        $map['site_tp'] = I('post.site_tp');
        $subQuery_t1 = $model->field($field)->where($map)->where('grp_id is not null')->group('dt,grp_id')->select(false);
        $sum_list = $model->table($subQuery_t1.' a')->join("dw_pub_gds_grp_td b on a.grp_id = b.grp_id")->field($type_b)->where($map_sum)->group('a.dt,b.grp_tp_nm')->order('dt desc,type asc')->select();
        foreach ($sum_list as $k) {
            $sum_arr[$k['dt']] = $k['ord_cnt'];

        }

        $list = $model->table($subQuery_t1.' a')->join("dw_pub_gds_grp_td b on a.grp_id = b.grp_id")->field($type_b)->where($map_sum)->group($group_sum)->order('dt desc,type asc')->select();

        foreach ($list as $k) {
            $k['ord_cnt']=$k['ord_cnt'] ? round($k['ord_cnt']/$sum_arr[$k['dt']]*100,2) : 0 ;
            $voList[]=$k;

        }
        $arr = array('title' =>'各小组销量' , 'dim1' => 'dt','dim2' => 'type','field' => 'ord_cnt'  ,'table' => 'ssa_site_core');
        $this->statistic_bar($voList,$arr,null);
    }
    /*
     * 销售金额 2.1
     */
    public function statistic_sale_amt(){
        C("DB_CASE_LOWER",true);
        if(I('post.buyer_grp_nm') != ''){
            $map_sum['b.grp_nm'] = array('IN',I('post.buyer_grp_nm'));
            $type_b = 'a.dt,round(sum(a.ord_amnt),2) as ord_amnt,b.grp_nm as type';
            $group_sum = 'a.dt,b.grp_nm';
        }else{
            if(I('post.grp_tp_list') != ''){
                $map_sum['b.grp_tp_nm'] = array('IN',I('post.grp_tp_list'));
            }
            $type_b = 'a.dt,round(sum(a.ord_amnt),2) as ord_amnt,b.grp_tp_nm as type';
            $group_sum = 'a.dt,b.grp_tp_nm';
        }
        if( I('post.order')=='month' ){
            $model = M('dm_ord_grp_m',null,'Oracle_Amazon');
            $field = "to_char(to_date(dt,'yyyy-mm'),'yyyy-mm') as dt,sum(ord_amnt) as ord_amnt,grp_id";
        } else if ( I('post.order')=='week' ) {
            $model = M('dm_ord_grp_w',null,'Oracle_Amazon');
            $field = "substr(dt,'1',4)||'-'||substr(dt,'5',2) as dt,sum(ord_amnt) as ord_amnt,grp_id";
        } else {
            $model = M('dm_ord_grp_d',null,'Oracle_Amazon');
            $field = "to_char(to_date(dt,'yyyy-mm-dd'),'yyyy-mm-dd') AS dt,sum(ord_amnt) as ord_amnt,grp_id";
        }
        $query_date=$this->get_query_date();
        $map['dt'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
        $add_start_time=str_replace('-','',I('post.add_start_date'));
        $add_end_time=str_replace('-','',I('post.add_end_date'));
        if($add_start_time!=''){
            $map['add_time']=array('EGT',$add_start_time);
        }
        if($add_end_time !='') {
            $map['add_time'] = array("ELT", $add_end_time);
        }
        $map['site_tp'] = I('post.site_tp');
        $subQuery_t1 = $model->field($field)->where($map)->where('grp_id is not null')->group('dt,grp_id')->select(false);
        $list = $model->table($subQuery_t1.' a')->join("dw_pub_gds_grp_td b on a.grp_id = b.grp_id")->field($type_b)->where($map_sum)->group($group_sum)->order('dt desc,type asc')->select();
        $arr = array('title' =>'各小组销售额' , 'dim1' => 'dt','dim2' => 'type','field' => 'ord_amnt'  ,'table' => 'ssa_site_core');
        $this->statistic_bar($list,$arr,null);
    }

    /*
     * 小组销售金额占比2.1.4
     */
    public function statistic_sale_amt_rate(){
        C("DB_CASE_LOWER",true);
        if(I('post.buyer_grp_nm') != ''){
            $map_sum['b.grp_nm'] = array('IN',I('post.buyer_grp_nm'));
            $type_b = 'a.dt,sum(a.ord_amnt) as ord_amnt,max(b.grp_nm) as type';
            $group_sum = 'a.dt,b.grp_nm';
        }else{
            if(I('post.grp_tp_list') != ''){
                $map_sum['b.grp_tp_nm'] = array('IN',I('post.grp_tp_list'));
            }
            $type_b = 'a.dt,sum(a.ord_amnt) as ord_amnt,max(b.grp_tp_nm) as type';
            $group_sum = 'a.dt,b.grp_tp_nm';
        }
        if( I('post.order')=='month' ){
            $model = M('dm_ord_grp_m',null,'Oracle_Amazon');
            $field = "to_char(to_date(dt,'yyyy-mm'),'yyyy-mm') as dt,sum(ord_amnt) as ord_amnt,grp_id";
        } else if ( I('post.order')=='week' ) {
            $model = M('dm_ord_grp_w',null,'Oracle_Amazon');
            $field = "substr(dt,'1',4)||'-'||substr(dt,'5',2) as dt,sum(ord_amnt) as ord_amnt,grp_id";
        } else {
            $model = M('dm_ord_grp_d',null,'Oracle_Amazon');
            $field = "to_char(to_date(dt,'yyyy-mm-dd'),'yyyy-mm-dd') AS dt,sum(ord_amnt) as ord_amnt,grp_id";
        }
        $query_date=$this->get_query_date();
        $map['dt'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
        $add_start_time=str_replace('-','',I('post.add_start_date'));
        $add_end_time=str_replace('-','',I('post.add_end_date'));
        if($add_start_time!=''){
            $map['add_time']=array('EGT',$add_start_time);
        }
        if($add_end_time !='') {
            $map['add_time'] = array("ELT", $add_end_time);
        }
        $map['site_tp'] = I('post.site_tp');
        $subQuery_t1 = $model->field($field)->where($map)->where('grp_id is not null')->group('dt,grp_id')->select(false);
        $sum_list = $model->table($subQuery_t1.' a')->join("dw_pub_gds_grp_td b on a.grp_id = b.grp_id")->field($type_b)->where($map_sum)->group('a.dt')->order('dt desc,type asc')->select();
        foreach ($sum_list as $k) {
            $sum_arr[$k['dt']] = $k['ord_amnt'];
        }
        $list = $model->table($subQuery_t1.' a')->join("dw_pub_gds_grp_td b on a.grp_id = b.grp_id")->field($type_b)->where($map_sum)->group($group_sum)->order('dt desc,type asc')->select();
        foreach ($list as $k) {
            $k['ord_amnt']=$k['ord_amnt'] ? round($k['ord_amnt']/$sum_arr[$k['dt']]*100,2) : 0 ;
            $voList[]=$k;

        }
        $arr = array('title' =>'各小组销量' , 'dim1' => 'dt','dim2' => 'type','field' => 'ord_amnt'  ,'table' => 'ssa_site_core');
        $this->statistic_bar($voList,$arr,null);
    }

    /*
     * 获取小组分类对应小组
     */
    public function pub_group_get(){
        if(I('post.grp') !=''){
            $map['grp_tp_nm'] = array('IN',array_filter(I('post.grp')));
            $model = M('dw_pub_gds_grp_td',null,'Mysql_WH');
            $voList = $model->field('distinct grp_nm')->where($map)->order('grp_nm asc')->select();
            foreach ($voList as $key => $value) {
                $list[] = $value['grp_nm'];
            }
            $this->ajaxReturn( array('grp_id'=>$list));
            exit;
        }
    }


    /**
     * 审核、有货统计
     * @modify jiang 2017-04-06 09：28 增加 审核SKU数、无货SKU数、无货SKU比例
     * @modify zjh 2017-06-13 下午3:10 增加site_id
     */
    public function statistic_auto_occu_sta(){
        $check_type = I('post.check_type');
        if(in_array($check_type,['item','order'])){
            // 审核时间,订单最早审核时间
            $map['check_type'] = $check_type;
        }

        if(I('post.monitor_interval') == '72'){
            $model = M('dm_ord_stk_auto_core_72h_d',null,'Mysql_WH');
        }else{
            $model = M('dm_ord_stk_auto_core_d',null,'Mysql_WH');
        }
        $field = "date(seq_time) as dt,
        sum(order_cnt) as order_cnt,
        sum(pre_shpp_goods_cnt) as pre_shpp_goods_cnt,
        case when sum(order_cnt)>0 then concat(round(sum(pre_shpp_goods_cnt)/sum(order_cnt)*100,2),'%') else 0 end as ord_rate,
        sum(goods_cnt) as goods_cnt,
        sum(auto_task_goods_cnt) as auto_task_goods_cnt,
        case when sum(goods_cnt)>0 then concat(round(sum(auto_task_goods_cnt)/sum(goods_cnt)*100,2),'%') else 0 end as task_rate,
        sum(sku_cnt) as sku_cnt,
        sum(nostock_sku_cnt) as nostock_sku_cnt,
        case when sum(sku_cnt)>0 then concat(round(sum(nostock_sku_cnt)/sum(sku_cnt)*100,2),'%') else 0 end as nostock_sku_rate
        ";
        $site_tp = I('post.site_tp');
        $site_id = I('post.site_id');
        $platform = I('post.select_platform');
        if($site_tp != 'TOTAL'){
            $map['site_tp'] = $site_tp;
            if($site_tp  == 'platform'){
                if(!empty($platform)){
                    if($site_id == 'TOTAL'){
                        //解析站点
                        $tmp_platform = M('dm_pub_site_platform_td',null,'Mysql_WH')->field('site_id')->where(['platform'=>['in',$platform]])->select();
                        $map['site_id'] = array('in',array_map(function($a){return $a['site_id'];},$tmp_platform));
                    } else {
                        $map['site_id'] = $site_id;
                    }
                }
            }else{
                if($site_id != 'TOTAL'){
                    $map['site_id'] = $this->get_query_site('dm_ord_stk_auto_core_d');
                }else{
                    $map['_string'] = "site_id = '{$site_tp}_total' or site_id is null";
                }
            }
        }else{
            $map['_string'] = "site_id like '%_total' or site_id is null";
        }
        $query_date=$this->get_query_date();
        $map['seq_time'] = array(array('egt',$query_date['start_date']),array('elt',$query_date['end_date']));
        $group = 'seq_time';
        $_REQUEST ['_order'] = 'seq_time';
        //取得记录总数
        if ($group != '') {
            $subQuery = $model->field($field)->where($map)->group($group)->select(false);
            $count = $model->table($subQuery.' a')->count('1');
        } else {
            $count = $model->where($map)->count('1');
        }

        if ($count>0) {
            import('@.ORG.Util.Page');
            //创建分页对象
            if ($_REQUEST ['listRows'] == 'undefined' || $_REQUEST ['listRows'] =='') {
                $listRows = '100';
            } else {
                $listRows = $_REQUEST ['listRows'];
            }
            $p = new Page($count, $listRows);

            $voList = $model->field($field)->where($map)->group($group)->order('seq_time desc')->limit($p->firstRow.','.$p->listRows)->select();
            foreach ($map as $key => $val) {
                if (!is_array($val)) {
                    $p->parameter.="$key=".urldecode($val)."&";
                }
            }
            //分页显示
            $page = $p->show();
            //列表排序显示
            $sortImg = $sort; //排序图标
            $sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列' ;//排序提示
            $sort = $sort == 'desc' ? 1 : 0 ;//排序方式

            //模板赋值
            $header = ['日期','审核订单数','审核即等待出仓订单数','所占比例','审核的产品数','备货中产品数','所占比例','审核SKU数','无货SKU数','无货SKU比例'];

            $this->assign('header',$header);
            $this->assign('solidLeftHeaderCol',1);
            $this->assign('list',$voList);
            $this->assign('sort',$sort);
            $this->assign('order',$order);
            $this->assign('sortImg',$sortImg);
            $this->assign('sortType',$sortAlt);
            $this->assign('page',$page);

            $this->assign('solidLeftHeaderCol',1);
            $data = $this->fetch('Echarts:form_common_list_with_solid_head');
            echo $data;
        }else{
            echo'当前天暂无数据';
        }
        return;

    }

    /*
     * 分类统计
     */
    public function statistic_occu_cate_sta(){
        $site_tp = I('post.site_tp');
        $site_id = I('post.site_id');

        $map =' 1=1';
        if(I('post.monitor_interval') == '72'){
            $model = M('dm_ord_stk_auto_take_72h_d',null,'Mysql_WH');
        }else{
            $model = M('dm_ord_stk_auto_take_d',null,'Mysql_WH');
        }
        if ($site_tp != 'TOTAL') {
            if($site_id == 'TOTAL'){
                $map .= " and site_tp = '$site_tp'";
            }else{
                $map .= " and site_tp = '$site_tp'";
                $map_site_id = $this->get_query_site('dm_ord_stk_auto_take_d')[1];
                $map .= " and site_id in ('".implode("','",$map_site_id)."')";
            }
        }
        $field = "date(seq_time) as dt,
             cate_id as cate_id,
             cate_nm as cate_nm,
             sum(order_cnt) as order_cnt,
             sum(presale_order_cnt) as presale_order_cnt,
             sum(pre_shpp_goods_cnt) as pre_shpp_goods_cnt,
             case when order_cnt>0 then round(sum(pre_shpp_goods_cnt)/(order_cnt)*100,2) else 0 end as ord_rate,
             sum(goods_cnt) as goods_cnt,
             sum(auto_task_goods_cnt) as auto_task_goods_cnt,
             case when goods_cnt>0 then round(sum(auto_task_goods_cnt)/sum(goods_cnt)*100,2) else 0 end as task_rate
            ";
        $query_date=$this->get_query_date();
        $map .=" and seq_time >='".$query_date['start_date']."'";
        $map .=" and seq_time <='".$query_date['end_date']."'";
        $map .="and cate_nm in ('生产部','沙河','十三行','常熟','阿里巴巴','外协')";
        //$map['dt'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
//        $map['cate_nm'] = array('IN',array('生产部','沙河','十三行','常熟','青岛饰品','阿里巴巴'));
        $group = 'seq_time , cate_nm';
        $form='form_auto_occu_cat';
        //字段排序 默认主键
        if (isset($_REQUEST ['_order'])) {
            $order = $_REQUEST ['_order'];
        } else {
            $order = !empty($sortBy) ? $sortBy : $model->getPk();
        }

        //排序默认倒序排列
        //sort 0：倒序 非0：正序
        if (isset($_REQUEST ['_sort'])) {
            $sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
        } else {
            $sort = $asc ? 'asc' : 'desc';
        }


        $subQuery = $model->field($field)->where($map)->group($group)->select(false);
        $count = $model->table($subQuery.' a')->count('1');
        if($count>0){
            import('@.ORG.Util.Page');
            //创建分页对象
            if (!empty($_REQUEST ['listRows'])) {
                $listRows = $_REQUEST ['listRows'];
            } else {
                $listRows = 6*100;
            }
            $p = new Page($count, $listRows);
            $voList = $model->field($field)->where($map)->order('seq_time desc,cate_id asc')->limit($p->firstRow.','.$p->listRows)->group($group)->select();
            foreach ($voList as $k) {
                $arr[$k['dt']]['dt']=$k['dt'];
                $arr[$k['dt']][$k['cate_nm']]=$k;
                //$arr[$k['dt']][$k['cate_id']]['ord_rate']=$k['ord_rate'].'%';
                $arr[$k['dt']][$k['cate_nm']]['task_rate']=$k['task_rate'].'%';
            }


            foreach ($map as $key => $val) {
                if (!is_array($val)) {
                    $p->parameter.="$key=".urldecode($val)."&";
                }
            }

            //分页显示
            $page = $p->show();
            //列表排序显示
            $sortImg = $sort; //排序图标
            $sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列' ;//排序提示
            $sort = $sort == 'desc' ? 1 : 0 ;//排序方式
            //模板赋值
            $this->assign('list',$arr);
            $this->assign('sort',$sort);
            $this->assign('order',$order);
            $this->assign('sortImg',$sortImg);
            $this->assign('sortType',$sortAlt);
            $totalCount = explode(' 条记录',$page)[0]/6;
            $this->assign('page',str_replace(explode(' 条记录',$page)[0]." 条记录","$totalCount 条记录",$page));
            $this->assign('offset',$p->firstRow);
            $data = $this->fetch('Echarts:'.$form);
            echo $data;
        }else{
            echo '当前天暂无数据';
        }
        return;
    }

    /*
     * modified @hiro 2016-08-12 17:14:28
     * 修正导出逻辑,选择Total站点时附带导出所有单独站点数据,选择子站点时只出Total及本站点数据
     * @modify zjh 2016-11-22 下午4:12 增加维护人
     */
    public function sale_opr_gz(){
        C('DB_CASE_LOWER',true);
        //查询条件开始
        $having_a = ' 1 = 1';
        $having_b = ' 1 = 1';
        $where_a = ' 1 = 1';
        if (I('post.supplier_nm') != '') {
            $where_a .= " and supplier_nm like '%".I('post.supplier_nm')."%'";
        }
        if (I('post.goods_sn') != '') {
            $where_b['goods_sn']  = I('post.goods_sn');
        }else{
            $where_b['goods_sn']  =  array('EXP','IS not NULL');
        }
        $start_date = ('' == I('post.start_date') ) ? date("Ymd",mktime(0, 0, 0, date("m"), date("d")-7, date("Y"))) :str_replace('-','', I('post.start_date'));
        $end_date = ('' == I('post.end_date') ) ? date("Ymd",mktime(0, 0, 0, date("m"), date("d")-1, date("Y"))): str_replace('-','',I('post.end_date'));
        $where_b['dt'] = array(array('EGT',$start_date),array('ELT',$end_date),'and');

        //处理优化编号
        if (I('post.optimize_code') == '1') {
            $where_a .= ' and optmz_sn is not NULL';
        }
        if (I('post.optimize_code') == '0') {
            $where_a .= ' and optmz_sn is NULL';
        }
        //处理优化状态
        if (I('post.optimize_status') != '' and I('post.optimize_status') != '5') {
            $where_a .= ' and optmz_status ='.I('post.optimize_status');
        }
        if(I('post.optimize_status') == '5'){
            $where_a .= ' and optmz_status is NULL';
        }
        //处理上架
        if (I('post.sale_flag') != '') {
            $where_a .= ' and sale_flag ='.I('post.sale_flag');
        }
        //处理上架天数
        $start_onsale_day = I('post.start_onsale_day');
        $end_onsale_day = I('post.end_onsale_day');
        if ($start_onsale_day != '' && $end_onsale_day != '') {
            $where_a .= ' and onsale_day >='.$start_onsale_day.' and onsale_day =<'.$end_onsale_day;
        }
        if ($start_onsale_day != '' && $end_onsale_day == '') {
            $where_a .= ' and onsale_day >='.$start_onsale_day;
        }
        if ($where_a == '' && $end_onsale_day != '') {
            $where_a .= ' and onsale_day =<'.$end_onsale_day;
        }
        //真实库存
        if (I('post.real_stk') != '') {
            $where_a .= ' and real_stk ='.I('post.real_stk');
        }
        //站点
        if(I('post.site_from')!=''){
            $where_b['site_tp']=I('post.site_from');
        }
        //毛利率
        if (I('grtr_gross') != '') {
            $where_a .= ' and ((price-cost_rmb_amt/6.5)/price)*100 >'.I('grtr_gross');
        }
        if (I('less_gross') != '') {
            $where_a .= ' and ((price-cost_rmb_amt/6.5)/price)*100 <'.I('less_gross');
        }
        //库存区
        $where_a .= ' and inv_type ='. 1;
        $where_a .= ' and goods_sn is not NULL';
        //销量
        if(I('post.grtr_ord') != '' ){
            $having_b .=' and ord_cnt >='.I('post.grtr_ord');

        }
        if(I('post.less_ord') !=''){
            $having_b .=' and ord_cnt <='.I('post.less_ord');
        }
        //存销比
        if(I('post.grtr_stksl') != '' ){
            $having_a .=' and stk_sale_rat >='.I('post.grtr_stksl');

        }
        if(I('post.less_stksl') !=''){
            $having_a .=' and stk_sale_rat <='.I('post.less_stksl');
        }
        //查询条件结束
        $model_a = M('pub_sku_stk_prd_d','dm.dm_','Oracle_WH');
        $model_b = M('ord_sku_sup_d','dm.dm_','Oracle_WH');
        $field_a = 'goods_sn,img_url,supplier_nm,optmz_sn,
            buyer_nm,cost_rmb_amt,price,optmz_status,sale_flag,onsale_day,
            sum(onroad_cnt) as onroad_cnt,delivery_day,min_ord_cnt,stock_day,sum(alert_stk) as alert_stk,
            round(stk_sale_rat,2) as stk_sale_rat,sum(stk_cnt) as stk_cnt,sum(pcs_cnt) as pcs_cnt,sum(stock_cnt) as stock_cnt,real_stk,
            round(((price-cost_rmb_amt/6.5)/price)*100,2) as gross_margin,
            max(mntn_manager_nm) as mntn_manager_nm
         ';
        $field_b = 'goods_sn,sum(ord_cnt) as ord_cnt';
        $group_a = 'goods_sn,
                 img_url,
                 supplier_nm,
                 optmz_sn,
                 buyer_nm,
                 cost_rmb_amt,
                 price,
                 optmz_status,
                 sale_flag,
                 onsale_day,
                 delivery_day,
                 min_ord_cnt,
                 stock_day,
                 stk_sale_rat,
                 real_stk';
        $group_b = 'goods_sn';


        $subQuery_a = $model_a->field($field_a)->where($where_a)->group($group_a)->having($having_a)->select(false);
        $subQuery_b = $model_b->field($field_b)->where($where_b)->group($group_b)->having($having_b)->select(false);
        $field_all = 'a.*,b.ord_cnt';
        $model = M();

        $voList_tmp = $model->table($subQuery_a.' a')
            ->join('inner join'.$subQuery_b." b on a.goods_sn = b.goods_sn")
            ->field('a.goods_sn as goods_sn')->order('ord_cnt desc')->select(false);
        $temp_count = $model->table($voList_tmp.' tmp')->field('distinct goods_sn')->select();
        $count = count($temp_count);
        if($count <= 0)
        {
            echo "暂无数据";return;
        }
        import('@.ORG.Util.Page_o');
        //创建分页对象
        if ($_REQUEST ['listRows'] == 'undefined' || $_REQUEST ['listRows'] == '') {
            $listRows = '100';
        } else {
            $listRows = $_REQUEST ['listRows'];
        }
        $p = new Page($count, $listRows);
        //目标数组
        $voList_tmp = $model->table($subQuery_a.' a')
            ->join('inner join'.$subQuery_b." b on a.goods_sn = b.goods_sn")
            ->field('a.goods_sn as goods_sn')->order('ord_cnt desc')->select(false);
        $voList = $model->table($voList_tmp.' tmp')->field('distinct goods_sn')
            ->limit($p->firstRow . ',' . $p->listRows)->select();
        //逻辑变更-主要针对指定site_tp下销量范围查询
        //新逻辑:先取出该site_tp下销量范围内每页sku 再取出该列表sku总销量
        //取此100sku
        foreach ($voList as $k){
            $goods_sn_list[] =  $k['goods_sn'];
        }
        $where_goods_sn['goods_sn'] = array('IN',$goods_sn_list);
        //unset掉sku表中site_tp限制,销毁volist数组,换成sku限制,取总销量
        unset($where_b['site_tp']);
        unset($voList);
        $subQuery_b = $model_b->field($field_b)->where($where_b)->where($where_goods_sn)
            ->group($group_b)->having($having_b)->select(false);
        $sql="SELECT 
            a . *, b.ord_cnt
        FROM(SELECT 
            goods_sn,
                img_url,
                supplier_nm,
                optmz_sn,
                buyer_nm,
                cost_rmb_amt,
                price,
                optmz_status,
                sale_flag,
                onsale_day,
                sum(onroad_cnt) as onroad_cnt,
                delivery_day,
                min_ord_cnt,
                stock_day,
                sum(alert_stk) as alert_stk,
                round(stk_sale_rat, 2) as stk_sale_rat,
                sum(stk_cnt) as stk_cnt,
                sum(pcs_cnt) as pcs_cnt,
                sum(stock_cnt) as stock_cnt,
                real_stk,
                case when price >0 then round(((price - cost_rmb_amt / 6.5) / price) * 100, 2) else 0 end as gross_margin,
                max(mntn_manager_nm) as mntn_manager_nm
        FROM
            dm.dm_pub_sku_stk_prd_d
        WHERE $where_a
        GROUP BY goods_sn , img_url , supplier_nm , optmz_sn , buyer_nm , cost_rmb_amt , price , optmz_status , sale_flag , onsale_day , delivery_day , min_ord_cnt , stock_day , stk_sale_rat , real_stk
        HAVING $having_a)  a
        inner join (
        $subQuery_b
        ) b ON a.goods_sn = b.goods_sn
        ORDER BY ord_cnt desc";
        $voList = $model->query($sql);
        //分仓
        $arr_tab_stk = $model_a->field('goods_sn,inv_type,sku_size,stk_cnt')->where($where_goods_sn)->select();
        foreach ($arr_tab_stk as $k){
            $inv_stk[$k['goods_sn']][$k['inv_type']][$k['sku_size']] = $k['stk_cnt'];
        }
        foreach($inv_stk as $k=>$v){
            foreach($v as $k1 => $v1){
                $inv_stk_sum[$k][$k1]['size']=$v;
                $inv_stk_sum[$k][$k1]['sum']=array_sum($v1);
            }
        }
        //分仓结束 数组$inv_stk_sum
        //取分尺码数据
        $arr_tab_a = $model_a->field('goods_sn,sku_size,onroad_cnt,alert_stk,alert_stk_flag,stk_sale_rat,pcs_cnt,stock_cnt')->where($where_a)->where($where_goods_sn)->select();
        foreach ($arr_tab_a as $k) {
            $arr_onroad_a[$k['goods_sn']][$k['sku_size']] = $k['onroad_cnt'];//在途分尺码
            $arr_alert_a[$k['goods_sn']][$k['sku_size']]['alert_stk'] = $k['alert_stk'];//警戒分尺码
            $arr_alert_a[$k['goods_sn']][$k['sku_size']]['alert_stk_flag'] = $k['alert_stk_flag'];//警戒分尺码
            $arr_stk_rat_a[$k['goods_sn']][$k['sku_size']] = $k['stk_sale_rat'];//存销比分尺码
            $arr_pcs_cnt_a[$k['goods_sn']][$k['sku_size']] = $k['pcs_cnt'];//待采购分尺码
            $arr_stock_cnt_a[$k['goods_sn']][$k['sku_size']] = $k['stock_cnt'];//提前备货分尺码
        }
        //重组数组start
        foreach($arr_onroad_a as $k=>$v){
            $arr_stat_sum[$k]['onroad_stat']['size']=$v;
            $arr_stat_sum[$k]['onroad_stat']['sum']=array_sum($v);

        }
        foreach($arr_alert_a as $k=>$v){
            $arr_stat_sum[$k]['alert_stat']['size']=$v;
            $arr_stat_sum[$k]['alert_stat']['sum']=array_sum($v);

        }
        foreach($arr_stk_rat_a as $k=>$v){
            $arr_stat_sum[$k]['stk_rat_stat']['size']=$v;
            $arr_stat_sum[$k]['stk_rat_stat']['sum']=array_sum($v);

        }
        foreach($arr_pcs_cnt_a as $k=>$v){
            $arr_stat_sum[$k]['pcs_stat']['size']=$v;
            $arr_stat_sum[$k]['pcs_stat']['sum']=array_sum($v);

        }
        foreach($arr_stock_cnt_a as $k=>$v){
            $arr_stat_sum[$k]['stock_stat']['size']=$v;
            $arr_stat_sum[$k]['stock_stat']['sum']=array_sum($v);

        }
        //分尺码结束 数组$arr_stat_sum
        //取站点销量开始
        $arr_tab_b = $model_b->field('goods_sn,site_tp,sku_size,sum(ord_cnt) as ord_cnt')->where($where_b)->where($where_goods_sn)
            ->group('goods_sn,site_tp,sku_size')
            ->select();
        foreach ($arr_tab_b as $k) {
            $arr_list_b[$k['goods_sn']][$k['site_tp']]['size'][$k['sku_size']] = $k['ord_cnt'];
        }
        foreach ($arr_list_b as $k =>$v) {
            $arr_list_b[$k]['shein']['sum']= array_sum($arr_list_b[$k]['shein']['size']);
            $arr_list_b[$k]['romwe']['sum']= array_sum($arr_list_b[$k]['romwe']['size']);
            $arr_list_b[$k]['emmastyle']['sum']= array_sum($arr_list_b[$k]['emmastyle']['size']);
            $arr_list_b[$k]['platform']['sum']= array_sum($arr_list_b[$k]['platform']['size']);
        }
        //分站点销量结束
        $arr_sum_b = $model_b->field('goods_sn,sku_size,sum(ord_cnt) as ord_cnt')->where($where_b)->where($where_goods_sn)
            ->group('goods_sn,sku_size')
            ->select();
        foreach($arr_sum_b as $k){
            $sum_b[$k['goods_sn']][$k['sku_size']] = $k['ord_cnt'];
        }

        $final_list = array();
        foreach($voList as $k){
            $final_list[$k['goods_sn']] = $k;
            $final_list[$k['goods_sn']]['inv_list'] = $inv_stk_sum[$k['goods_sn']];
            $final_list[$k['goods_sn']]['sale_list'] = $arr_list_b[$k['goods_sn']];
            $final_list[$k['goods_sn']]['size_list'] = $arr_stat_sum[$k['goods_sn']];
            $final_list[$k['goods_sn']]['sum_sale'] = $sum_b[$k['goods_sn']];
        }
        $final_list = array_filter($final_list);



        $_POST['_sort']='1';
        $page = $p->show();
        $this->assign('list', $final_list);
        $this->assign('page', $page);
        $this->assign('site_tp',I('post.site_from'));
        $this->assign('warning_red',I('post.warning_red'));
        $form='form_sale_sta_gz';
        $data = $this->fetch("Echarts:$form");
        echo $data;return;
    }
    /*
     * 第一个表格,根据勾选的编号导出
     * ↑不是我写的
     * modified @hiro 2016-08-12 17:14:28
     * 修正导出逻辑,选择Total站点时附带导出所有单独站点数据,选择子站点时只出Total及本站点数据
     * modified @jiang 2016-09-6 11:04:28
     * @modify zjh 2016-11-22 下午4:12 增加维护人
     */
    public function exp_guangzhou(){
        C('DB_CASE_LOWER',true);
        ini_set('memory_limit','512M');
        $_POST['page'] =1;
        $having_a = ' 1 = 1';
        $having_b = ' 1 = 1';
        $where_a = ' 1 = 1';
        if (I('post.supplier_nm') != '') {
            $where_a .= " and supplier_nm like '%".I('post.supplier_nm')."%'";
        }
        if(I('post.checkbox') == '') {
            if (I('post.goods_sn') != '') {
                $where_b['goods_sn'] = I('post.goods_sn');
            } else {
                $where_b['goods_sn'] = array('EXP', 'IS not NULL');
            }
        }else{
            $where_b['goods_sn'] = array('IN',trim(I('post.checkbox'),','));
        }

        $start_date = ('' == I('post.start_date') ) ? date("Ymd",mktime(0, 0, 0, date("m"), date("d")-7, date("Y"))) :str_replace('-','', I('post.start_date'));
        $end_date = ('' == I('post.end_date') ) ? date("Ymd",mktime(0, 0, 0, date("m"), date("d")-1, date("Y"))): str_replace('-','',I('post.end_date'));
        $where_b['dt'] = array(array('EGT',$start_date),array('ELT',$end_date),'and');

        //处理优化编号
        if (I('post.optimize_code') == '1') {
            $where_a .= ' and optmz_sn is not NULL';
        }
        if (I('post.optimize_code') == '0') {
            $where_a .= ' and optmz_sn is NULL';
        }
        //处理优化状态
        if (I('post.optimize_status') != '' and I('post.optimize_status') != '5') {
            $where_a .= ' and optmz_status ='.I('post.optimize_status');
        }
        if(I('post.optimize_status') == '5'){
            $where_a .= ' and optmz_status is NULL';
        }
        //处理上架
        if (I('post.sale_flag') != '') {
            $where_a .= ' and sale_flag ='.I('post.sale_flag');
        }
        //处理上架天数
        $start_onsale_day = I('post.start_onsale_day');
        $end_onsale_day = I('post.end_onsale_day');
        if ($start_onsale_day != '' && $end_onsale_day != '') {
            $where_a .= ' and onsale_day >='.$start_onsale_day.' and onsale_day =<'.$end_onsale_day;
        }
        if ($start_onsale_day != '' && $end_onsale_day == '') {
            $where_a .= ' and onsale_day >='.$start_onsale_day;
        }
        if ($where_a == '' && $end_onsale_day != '') {
            $where_a .= ' and onsale_day =<'.$end_onsale_day;
        }
        //真实库存
        if (I('post.real_stk') != '') {
            $where_a .= ' and real_stk ='.I('post.real_stk');
        }
        //站点
        if(I('post.site_from')!=''){
            $where_b['site_tp']=I('post.site_from');
        }else{
            $where_b['site_tp']=array('EXP','IS not NULL');
        }
        //毛利率
        if (I('grtr_gross') != '') {
            $where_a .= ' and ((price-cost_rmb_amt/6.5)/price)*100 >'.I('grtr_gross');
        }
        if (I('less_gross') != '') {
            $where_a .= ' and ((price-cost_rmb_amt/6.5)/price)*100 <'.I('less_gross');
        }
        //库存区
        $where_a .= ' and inv_type ='. 1;
        $where_a .= ' and goods_sn is not NULL';
        //销量
        if(I('post.grtr_ord') != '' ){
            $having_b .=' and ord_cnt >='.I('post.grtr_ord');

        }
        if(I('post.less_ord') !=''){
            $having_b .=' and ord_cnt <='.I('post.less_ord');
        }
        //存销比
        if(I('post.grtr_stksl') != '' ){
            $having_a .=' and stk_sale_rat >='.I('post.grtr_stksl');

        }
        if(I('post.less_stksl') !=''){
            $having_a .=' and stk_sale_rat <='.I('post.less_stksl');
        }
        //查询条件结束
        $model_a = M('pub_sku_stk_prd_d','dm.dm_','Oracle_WH');
        $model_b = M('ord_sku_sup_d','dm.dm_','Oracle_WH');
        $field_a = 'goods_sn,img_url,supplier_nm,optmz_sn,
            buyer_nm,cost_rmb_amt,price,optmz_status,sale_flag,onsale_day,
            sum(onroad_cnt) as onroad_cnt,delivery_day,min_ord_cnt,stock_day,sum(alert_stk) as alert_stk,
            round(stk_sale_rat,2) as stk_sale_rat,sum(stk_cnt) as stk_cnt,sum(pcs_cnt) as pcs_cnt,sum(stock_cnt) as stock_cnt,real_stk,
            round(((price-cost_rmb_amt/6.5)/price)*100,2) as gross_margin
         ';//,max(mntn_manager_nm) as mntn_manager_nm
        $field_b = 'goods_sn,sum(ord_cnt) as ord_cnt';
        $group_a = 'goods_sn,
                 img_url,
                 supplier_nm,
                 optmz_sn,
                 buyer_nm,
                 cost_rmb_amt,
                 price,
                 optmz_status,
                 sale_flag,
                 onsale_day,
                 delivery_day,
                 min_ord_cnt,
                 stock_day,
                 stk_sale_rat,
                 real_stk';
        $group_b = 'goods_sn';

        $subQuery_a = $model_a->field($field_a)->where($where_a)->group($group_a)->having($having_a)->select(false);
        $subQuery_b_t = $model_b->field($field_b)->where($where_b)->group($group_b)->having($having_b)->select(false);
        $field_all = 'a.*,b.ord_cnt';
        $model = M();
        $voList_tmp = $model->table($subQuery_a.' a')
            ->join('inner join'.$subQuery_b_t." b on a.goods_sn = b.goods_sn")
            ->field('a.goods_sn as goods_sn')->order('ord_cnt desc')->select(false);
        $temp_count = $model->table($voList_tmp.' tmp')->field('distinct goods_sn')->select();
        $count = count($temp_count);
        if($count <= 0){
            echo "暂无数据";return;
        }

//        for($i=0;$i<$page;$i++){
//            //目标数组
//            $voList = $model->table($voList_tmp.' tmp')->field('distinct goods_sn')
//                ->limit($i*500 . ',' .($i+1)*500)->select();
        //逻辑变更-主要针对指定site_tp下销量范围查询
        //新逻辑:先取出该site_tp下销量范围内每页sku 再取出该列表sku总销量
        unset($where_goods_sn);
//            foreach ($voList as $k){
//                $goods_sn_list[] =  $k['goods_sn'];
//            }
//            $where_goods_sn['goods_sn'] = array('IN',$goods_sn_list);
        //unset掉sku表中site_tp限制,销毁volist数组,换成sku限制,取总销量
        unset($where_b['site_tp']);
        unset($voList);
        $subQuery_b = $model_b->field($field_b)->where($where_b)
//                ->where($where_goods_sn)
            ->group($group_b)->having($having_b)->select(false);
        $sql="SELECT 
                        a . *, b.ord_cnt
                    FROM(SELECT 
                        goods_sn,
                            img_url,
                            supplier_nm,
                            optmz_sn,
                            buyer_nm,
                            cost_rmb_amt,
                            price,
                            optmz_status,
                            sale_flag,
                            onsale_day,
                            sum(onroad_cnt) as onroad_cnt,
                            delivery_day,
                            min_ord_cnt,
                            stock_day,
                            sum(alert_stk) as alert_stk,
                            round(stk_sale_rat, 2) as stk_sale_rat,
                            sum(stk_cnt) as stk_cnt,
                            sum(pcs_cnt) as pcs_cnt,
                            sum(stock_cnt) as stock_cnt,
                            real_stk,
                            case when price >0 then round(((price - cost_rmb_amt / 6.5) / price) * 100, 2) else 0 end as gross_margin,
                            max(mntn_manager_nm) as mntn_manager_nm
                    FROM
                        dm.dm_pub_sku_stk_prd_d
                    WHERE $where_a
                    GROUP BY goods_sn , img_url , supplier_nm , optmz_sn , buyer_nm , cost_rmb_amt , price , optmz_status , sale_flag , onsale_day , delivery_day , min_ord_cnt , stock_day , stk_sale_rat , real_stk
                    HAVING $having_a)  a
                    inner join (
                    $subQuery_b
                    ) b ON a.goods_sn = b.goods_sn
                    ORDER BY ord_cnt desc";
        $voList = $model->query($sql);
        $arr_tab_b = $model_b->field('goods_sn,site_tp,sum(ord_cnt) as ord_cnt')->where($where_b)
//                ->where($where_goods_sn)
            ->group('goods_sn,site_tp')
            ->select();

        $arr_sum_b = $model_b->field('goods_sn,sku_size,sum(ord_cnt) as ord_cnt')->where($where_b)
//            ->where($where_goods_sn)
            ->group('goods_sn,sku_size')
            ->select();
        $sum_b = array();
        foreach($arr_sum_b as $k){
            $sum_b[$k['goods_sn']] .= $k['sku_size'].':' . $k['ord_cnt'].'|';
        }
        //取各站点下的各尺码销量
        $arr_sum_b_site = $model_b->field('site_tp,goods_sn,sku_size,sum(ord_cnt) as ord_cnt')->where($where_b)
//            ->where($where_goods_sn)
            ->group('site_tp,goods_sn,sku_size')
            ->select();
        //处理各站点下的各尺码销量
        $sum_b_site=array();
        foreach($arr_sum_b_site as $k){
            $sum_b_site[$k['site_tp']][$k['goods_sn']] .= $k['sku_size'].':' . $k['ord_cnt'].'|';
        }
        foreach ($arr_tab_b as $k) {
            $arr_list_b[$k['goods_sn']][$k['site_tp']] = $k['ord_cnt'];
        }
        unset($arr_tab_b);
        if(I('post.site_from') ==''){
            foreach($voList as $k => $v){
                $voList[$k]['ord_cnt_size'] = rtrim($sum_b[$v['goods_sn']],'|');
                $voList[$k]['shein_cnt'] = $arr_list_b[$v['goods_sn']]['shein'];
                $voList[$k]['shein_cnt_size'] = rtrim($sum_b_site['shein'][$v['goods_sn']],'|');
                $voList[$k]['romwe_cnt'] = $arr_list_b[$v['goods_sn']]['romwe'];
                $voList[$k]['romwe_cnt_size'] = rtrim($sum_b_site['romwe'][$v['goods_sn']],'|');
                $voList[$k]['em_cnt'] = $arr_list_b[$v['goods_sn']]['emmastyle'];
                $voList[$k]['emmastyle_cnt_size'] = rtrim($sum_b_site['emmastyle'][$v['goods_sn']],'|');
                $voList[$k]['platform_cnt'] = $arr_list_b[$v['goods_sn']]['platform'];
                $voList[$k]['platform_cnt_size'] = rtrim($sum_b_site['platform'][$v['goods_sn']],'|');
            }
            $article = array('sku','商品图片','供应商','优化编号','买手','成本','单价','优化状态','上架','上架天数','在途数量','货期','最小起订量','备货天数','警戒库存','存销比','库存','待采购数量','备货数量','真实库存','毛利率(%)','维护人','销量','销量-分尺码','Shein销量','Shein销量-分尺码','Romwe销量','Romwe销量-分尺码','EM销量','EM销量-分尺码','平台销量','平台销量-分尺码');        }else{
            foreach($voList as $k => $v){
                $voList[$k]['ord_cnt_size'] =  rtrim($sum_b[$v['goods_sn']],'|');
                $voList[$k]['site_cnt'] = $arr_list_b[$v['goods_sn']][I('post.site_from')];
                $voList[$k]['site_cnt_size'] = rtrim($sum_b_site[I('post.site_from')][$v['goods_sn']],'|');
            }
            $article = array('sku','商品图片','供应商','优化编号','买手','成本','单价','优化状态','上架','上架天数','在途数量','货期','最小起订量','备货天数','警戒库存','存销比','库存','待采购数量','备货数量','真实库存','毛利率(%)','维护人','销量','销量-分尺码',I('post.site_from').'销量',I('post.site_from').'销量-分尺码');        }
        unset($arr_list_b);
        $this->excel_export($voList,$article,'销售统计-运营');
        unset($voList);

//        }
    }


    public function sale_opr_eu(){
        C("DB_CASE_LOWER",true);
        //查询条件开始
        $having_a = ' 1 = 1';
        $having_b = ' 1 = 1';
        if (I('post.supplier_nm') != '') {
            $where_a['supplier_nm'] = I('post.supplier_nm');
        }
        if (I('post.goods_sn') != '') {
            $where_b['goods_sn']  = I('post.goods_sn');
        }else{
            $where_b['goods_sn']  =  array('EXP','IS not NULL');
        }
        $start_date = ('' == I('post.start_date') ) ? date("Ymd",mktime(0, 0, 0, date("m"), date("d")-7, date("Y"))) :str_replace('-','', I('post.start_date'));
        $end_date = ('' == I('post.end_date') ) ? date("Ymd",mktime(0, 0, 0, date("m"), date("d")-1, date("Y"))): str_replace('-','',I('post.end_date'));
        $where_b['dt'] = array(array('EGT',$start_date),array('ELT',$end_date),'and');

        //处理优化编号
        if (I('post.optimize_code') == '1') {
            $where_a['optmz_sn'] = array('EXP','IS NOT NULL');
        }
        if (I('post.optimize_code') == '0') {
            $where_a['optmz_sn'] = array('EXP','IS NULL');
        }
        //处理优化状态
        if (I('post.optimize_status') != '' and I('post.optimize_status') != '5') {
            $where_a['optmz_status'] = I('post.optimize_status');
        }
        if(I('post.optimize_status') == '5'){
            $where_a['optmz_status'] = array('EXP','IS NULL');
        }
        //处理上架
        if (I('post.sale_flag') != '') {
            $where_a['sale_flag'] = I('post.sale_flag');
        }
        //处理上架天数
        $start_onsale_day = I('post.start_onsale_day');
        $end_onsale_day = I('post.end_onsale_day');
        if ($start_onsale_day != '' && $end_onsale_day != '') {
            $where_a['onsale_day'] = array(array('EGT', $start_onsale_day), array('ELT', $end_onsale_day), 'and');
        }
        if ($start_onsale_day != '' && $end_onsale_day == '') {
            $where_a['onsale_day'] = array('EGT', $start_onsale_day);
        }
        if ($where_a == '' && $end_onsale_day != '') {
            $where_a['onsale_day'] = array('ELT', $end_onsale_day);
        }
        //真实库存
        if (I('post.real_stk') != '') {
            $where_a['real_stk'] = I('post.real_stk');
        }
//        var_dump($where_a);exit;
        //站点
        if(I('post.site_from')!=''){
            $where_b['site_tp']=I('post.site_from');
        }else{
            $where_b['site_tp']=array('EXP','IS not NULL');
        }
        //库存区
        $where_a['inv_type'] = '3';
        $where_a['goods_sn'] = array('EXP','IS not NULL');
        //销量
        if(I('post.grtr_ord') != '' ){
            $having_b .=' and ord_cnt >='.I('post.grtr_ord');

        }
        if(I('post.less_ord') !=''){
            $having_b .=' and ord_cnt <='.I('post.less_ord');
        }
        //存销比
        if(I('post.grtr_stksl') != '' ){
            $having_a .=' and stk_sale_rat >='.I('post.grtr_stksl');

        }
        if(I('post.less_stksl') !=''){
            $having_a .=' and stk_sale_rat <='.I('post.less_stksl');
        }
        $where_b_sum = $where_b;
        $where_b['shpp_country_id'] = array('IN',array('14','21','33','54','56','57','58','68','73','74','82','85','98','104','106','116','122','123','131','150','172','173','177','192','193','198','206','225'));
        //查询条件结束
        $model_a = M('pub_sku_stk_prd_d','dm_','Oracle_Amazon');
        $model_b = M('ord_sku_sup_d','dm_','Oracle_Amazon');
        $field_a = 'goods_sn,img_url,supplier_nm,optmz_sn,
            buyer_nm,cost_rmb_amt,price,optmz_status,sale_flag,onsale_day,
            sum(onroad_cnt) as onroad_cnt,delivery_day,min_ord_cnt,stock_day,sum(alert_stk) as alert_stk,
            round(stk_sale_rat,2) as stk_sale_rat,sum(stk_cnt) as stk_cnt,sum(pcs_cnt) as pcs_cnt,sum(stock_cnt) as stock_cnt,real_stk
         ';
        $field_b = 'goods_sn,sum(ord_cnt) as ord_cnt';
        $group_a = 'goods_sn,img_url,supplier_nm,optmz_sn,
            buyer_nm,cost_rmb_amt,price,optmz_status,sale_flag,onsale_day,delivery_day,min_ord_cnt,stock_day,real_stk,stk_sale_rat';
        $group_b = 'goods_sn';


        $subQuery_a = $model_a->field($field_a)->where($where_a)->group($group_a)->having($having_a)->select(false);
        $subQuery_b = $model_b->field($field_b)->where($where_b)->group($group_b)->having($having_b)->select(false);
        $subQuery_a = str_replace('numrow','numrowa',$subQuery_a);
        $subQuery_b = str_replace('numrow','numrowb',$subQuery_b);
        $field_all = 'a.*,b.ord_cnt';
        $model = M();
        $count = $model->table($subQuery_a.' a')->join('inner join'.$subQuery_b." b on a.goods_sn = b.goods_sn")->field($field_all)->order('ord_cnt desc')->count('1');
//        var_dump($count);exit;
        import('@.ORG.Util.Page_o');
        //创建分页对象
        if ($_REQUEST ['listRows'] == 'undefined' || $_REQUEST ['listRows'] == '') {
            $listRows = '100';
        } else {
            $listRows = $_REQUEST ['listRows'];
        }
        $p = new Page($count, $listRows);
        //目标数组
        $voList = $model->table($subQuery_a.' a')->join('inner join'.$subQuery_b." b on a.goods_sn = b.goods_sn")->field($field_all)->order('ord_cnt desc')->limit($p->firstRow . ',' . $p->listRows)->select();
//        var_dump( $vo   qList);exit;
        //取此100sku
        foreach ($voList as $k){
            $goods_sn_list[] =  $k['goods_sn'];
        }
        $where_goods_sn['goods_sn'] = array('IN',$goods_sn_list);
        //取总销量
        $arr_sale_sum_b = $model_b->field($field_b)->where($where_b_sum)->where($where_goods_sn)->group($group_b)->select();
        foreach ($arr_sale_sum_b as $k){
            $arr_sale_sum_b_list[$k['goods_sn']] = $k['ord_cnt'];
        }
        //分仓
        $arr_tab_stk = $model_a->field('goods_sn,inv_type,sku_size,stk_cnt')->where($where_goods_sn)->select();
        foreach ($arr_tab_stk as $k){
            $k = array_filter($k);
            $inv_stk[$k['goods_sn']][$k['inv_type']][$k['sku_size']] = $k['stk_cnt'];
        }
        foreach($inv_stk as $k=>$v){
            $v = array_filter($v);
            foreach($v as $k1 => $v1){
                $v1 =array_filter($v1);
                $inv_stk_sum[$k][$k1]['size']=$v;
                $inv_stk_sum[$k][$k1]['sum']=array_sum($v1);
            }
        }
        //分仓结束 数组$inv_stk_sum
        //取分尺码数据
        $arr_tab_a = $model_a->field('goods_sn,sku_size,onroad_cnt,alert_stk,alert_stk_flag,stk_sale_rat,pcs_cnt,stock_cnt')->where($where_a)->where($where_goods_sn)->select();
        foreach ($arr_tab_a as $k) {
            $k = array_filter($k);
            $arr_onroad_a[$k['goods_sn']][$k['sku_size']] = $k['onroad_cnt'];//在途分尺码
            $arr_alert_a[$k['goods_sn']][$k['sku_size']]['alert_stk'] = $k['alert_stk'];//警戒分尺码
            $arr_alert_a[$k['goods_sn']][$k['sku_size']]['alert_stk_flag'] = $k['alert_stk_flag'];//警戒分尺码
            $arr_stk_rat_a[$k['goods_sn']][$k['sku_size']] = $k['stk_sale_rat'];//存销比分尺码
            $arr_pcs_cnt_a[$k['goods_sn']][$k['sku_size']] = $k['pcs_cnt'];//待采购分尺码
            $arr_stock_cnt_a[$k['goods_sn']][$k['sku_size']] = $k['stock_cnt'];//提前备货分尺码
        }
        //重组数组start
        foreach($arr_onroad_a as $k=>$v){
            $v = array_filter($v);
            $arr_stat_sum[$k]['onroad_stat']['size']=$v;
            $arr_stat_sum[$k]['onroad_stat']['sum']=array_sum($v);

        }
        foreach($arr_alert_a as $k=>$v){
            $v = array_filter($v);
            $arr_stat_sum[$k]['alert_stat']['size']=$v;
            $arr_stat_sum[$k]['alert_stat']['sum']=array_sum($v);

        }
        foreach($arr_stk_rat_a as $k=>$v){
            $v = array_filter($v);
            $arr_stat_sum[$k]['stk_rat_stat']['size']=$v;
            $arr_stat_sum[$k]['stk_rat_stat']['sum']=array_sum($v);

        }
        foreach($arr_pcs_cnt_a as $k=>$v){
            $v = array_filter($v);
            $arr_stat_sum[$k]['pcs_stat']['size']=$v;
            $arr_stat_sum[$k]['pcs_stat']['sum']=array_sum($v);

        }
        foreach($arr_stock_cnt_a as $k=>$v){
            $v = array_filter($v);
            $arr_stat_sum[$k]['stock_stat']['size']=$v;
            $arr_stat_sum[$k]['stock_stat']['sum']=array_sum($v);

        }
        //分尺码结束 数组$arr_stat_sum
        //取站点销量开始
        $arr_tab_b = $model_b->field('goods_sn,site_tp,sku_size,sum(ord_cnt) as ord_cnt')->where($where_b)->where($where_goods_sn)
            ->group('goods_sn,site_tp,sku_size')
            ->select();
        foreach ($arr_tab_b as $k) {
            $k = array_filter($k);
            $arr_list_b[$k['goods_sn']][$k['site_tp']]['size'][$k['sku_size']] = $k['ord_cnt'];
        }
        foreach ($arr_list_b as $k =>$v) {
            $v = array_filter($v);
            $arr_list_b[$k]['shein']['sum']= array_sum($arr_list_b[$k]['shein']['size']);
            $arr_list_b[$k]['romwe']['sum']= array_sum($arr_list_b[$k]['romwe']['size']);
            $arr_list_b[$k]['emmastyle']['sum']= array_sum($arr_list_b[$k]['emmastyle']['size']);
            $arr_list_b[$k]['platform']['sum']= array_sum($arr_list_b[$k]['platform']['size']);
        }
        //分站点销量结束
        $arr_sum_b = $model_b->field('goods_sn,sku_size,sum(ord_cnt) as ord_cnt')->where($where_b)->where($where_goods_sn)
            ->group('goods_sn,sku_size')
            ->select();
        foreach($arr_sum_b as $k){
            $k = array_filter($k);
            $sum_b[$k['goods_sn']][$k['sku_size']] = $k['ord_cnt'];
        }
        $arr_sale_detail_b = $model_b->field('goods_sn,sku_size,sum(ord_cnt) as ord_cnt')
            ->where($where_b_sum)->where($where_goods_sn)->group('goods_sn,sku_size')->select();

        foreach($arr_sale_detail_b as $k){
            $sale_detail_b[$k['goods_sn']][$k['sku_size']] = $k['ord_cnt'];
        }

        $final_list = array();
        foreach($voList as $k){
            $k = array_filter($k);
            $final_list[$k['goods_sn']] = $k;
            $final_list[$k['goods_sn']]['inv_list'] = $inv_stk_sum[$k['goods_sn']];
            $final_list[$k['goods_sn']]['sale_list'] = $arr_list_b[$k['goods_sn']];
            $final_list[$k['goods_sn']]['size_list'] = $arr_stat_sum[$k['goods_sn']];
            $final_list[$k['goods_sn']]['sum_sale'] = $sum_b[$k['goods_sn']];
            $final_list[$k['goods_sn']]['sum_sale_all'] = $arr_sale_sum_b_list[$k['goods_sn']];
            $final_list[$k['goods_sn']]['sum_sale_detail'] = $sale_detail_b[$k['goods_sn']];
        }
        $final_list = array_filter($final_list);
        $_POST['_sort']='1';
        $page = $p->show();
        $this->assign('list', $final_list);
        $this->assign('page', $page);
        $this->assign('site_tp',I('post.site_from'));
        $this->assign('warning_red',I('post.warning_red'));
        $form='form_sale_sta_eu';
        $data = $this->fetch("Echarts:$form");
        echo $data;return;
    }
    /*
     * 第一个表格,根据勾选的编号导出
     */
    public function exp_eu(){
        C("DB_CASE_LOWER",true);
        $_POST['page'] =1;
        $having_a = ' 1 = 1';
        $having_b = ' 1 = 1';
        if (I('post.supplier_nm') != '') {
            $where_a['supplier_nm'] = I('post.supplier_nm');
        }
        if(I('post.checkbox') == '') {
            if (I('post.goods_sn') != '') {
                $where_b['goods_sn'] = I('post.goods_sn');
            } else {
                $where_b['goods_sn'] = array('EXP', 'IS not NULL');
            }
        }else{
            $where_b['goods_sn'] = array('IN',trim(I('post.checkbox'),','));
        }

        $start_date = ('' == I('post.start_date') ) ? date("Ymd",mktime(0, 0, 0, date("m"), date("d")-7, date("Y"))) :str_replace('-','', I('post.start_date'));
        $end_date = ('' == I('post.end_date') ) ? date("Ymd",mktime(0, 0, 0, date("m"), date("d")-1, date("Y"))): str_replace('-','',I('post.end_date'));
        $where_b['dt'] = array(array('EGT',$start_date),array('ELT',$end_date),'and');

        //处理优化编号
        if (I('post.optimize_code') == '1') {
            $where_a['optmz_sn'] = array('EXP','IS NOT NULL');
        }
        if (I('post.optimize_code') == '0') {
            $where_a['optmz_sn'] = array('EXP','IS NULL');
        }
        //处理优化状态
        if (I('post.optimize_status') != '' and I('post.optimize_status') != '5') {
            $where_a['optmz_status'] = I('post.optimize_status');
        }
        if(I('post.optimize_status') == '5'){
            $where_a['optmz_status'] = array('EXP','IS NULL');
        }
        //处理上架
        if (I('post.sale_flag') != '') {
            $where_a['sale_flag'] = I('post.sale_flag');
        }
        //处理上架天数
        $start_onsale_day = I('post.start_onsale_day');
        $end_onsale_day = I('post.end_onsale_day');
        if ($start_onsale_day != '' && $end_onsale_day != '') {
            $where_a['onsale_day'] = array(array('EGT', $start_onsale_day), array('ELT', $end_onsale_day), 'and');
        }
        if ($start_onsale_day != '' && $end_onsale_day == '') {
            $where_a['onsale_day'] = array('EGT', $start_onsale_day);
        }
        if ($where_a == '' && $end_onsale_day != '') {
            $where_a['onsale_day'] = array('ELT', $end_onsale_day);
        }
        //真实库存
        if (I('post.real_stk') != '') {
            $where_a['real_stk'] = I('post.real_stk');
        }
//        var_dump($where_a);exit;
        //站点
        if(I('post.site_from')!=''){
            $where_b['site_tp']=I('post.site_from');
        }else{
            $where_b['site_tp']=array('EXP','IS not NULL');
        }
        //库存区
        $where_a['inv_type'] = '1';
        $where_a['goods_sn'] = array('EXP','IS not NULL');
        //销量
        if(I('post.grtr_ord') != '' ){
            $having_b .=' and ord_cnt >='.I('post.grtr_ord');

        }
        if(I('post.less_ord') !=''){
            $having_b .=' and ord_cnt <='.I('post.less_ord');
        }
        //存销比
        if(I('post.grtr_stksl') != '' ){
            $having_a .=' and stk_sale_rat >='.I('post.grtr_stksl');

        }
        if(I('post.less_stksl') !=''){
            $having_a .=' and stk_sale_rat <='.I('post.less_stksl');
        }
        $where_b['shpp_country_id'] = array('IN',array('14','21','33','54','56','57','58','68','73','74','82','85','98','104','106','116','122','123','131','150','172','173','177','192','193','198','206','225'));
        //查询条件结束
        $model_a = M('pub_sku_stk_prd_d','dm_','Oracle_Amazon');
        $model_b = M('ord_sku_sup_d','dm_','Oracle_Amazon');
        $field_a = 'goods_sn,img_url,supplier_nm,optmz_sn,
            buyer_nm,cost_rmb_amt,price,optmz_status,sale_flag,onsale_day,
            sum(onroad_cnt) as onroad_cnt,delivery_day,min_ord_cnt,stock_day,sum(alert_stk) as alert_stk,
            round(stk_sale_rat,2) as stk_sale_rat,sum(stk_cnt) as stk_cnt,sum(pcs_cnt) as pcs_cnt,sum(stock_cnt) as stock_cnt,real_stk
         ';
        $field_b = 'goods_sn,sum(ord_cnt) as ord_cnt';
        $group_a = 'goods_sn,img_url,supplier_nm,optmz_sn,
            buyer_nm,cost_rmb_amt,price,optmz_status,sale_flag,onsale_day,delivery_day,min_ord_cnt,stock_day,real_stk,stk_sale_rat';
        $group_b = 'goods_sn';


        $subQuery_a = $model_a->field($field_a)->where($where_a)->group($group_a)->having($having_a)->select(false);
        $subQuery_b = $model_b->field($field_b)->where($where_b)->group($group_b)->having($having_b)->select(false);
        $subQuery_a = str_replace('numrow', 'numrowa', $subQuery_a);
        $subQuery_b = str_replace('numrow', 'numrowb', $subQuery_b);
        $field_all = 'a.*,b.ord_cnt';
        $model = M();
//        $count = $model->table($subQuery_a.' a')->join('inner join'.$subQuery_b." b on a.goods_sn = b.goods_sn")->field($field_all)->order('ord_cnt desc')->count('1');
        $temo_count = $model->table($subQuery_a . ' a')->join('inner join' . $subQuery_b . " b on a.goods_sn = b.goods_sn")->field('distinct a.goods_sn as goods_sn,b.ord_cnt as ord_cnt')->order('ord_cnt desc')->select();
        $count = count($temo_count);
        /*
         * 导出逻辑开始
         */
        $title = array('sku','商品图片','供应商','优化编号','买手','成本','单价','优化状态','上架','上架天数','在途数量','货期','最小起订量','备货天数','警戒库存','存销比','库存','待采购数量','备货数量','真实库存','销量');
        $epage = 800;//每次取出数量
        header("Content-type:application/octet-stream");
        header("Accept-Ranges:bytes");
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=" . "销售统计-欧洲" . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        $fp = fopen('php://output', 'a');
        if (!empty($title)) {
            foreach ($title as $k => $v) {
                $title[$k] = iconv("UTF-8", "UTF-8", $v);
            }
            fputcsv($fp, $title);
        }
        //获取分页数
        $lim = floor($count / $epage);
        if ($lim == 0) {
            $lim = 1;
            $epage = $count;
        }
        for ($time = 0; $time <= $lim; $time++) {
            //设置limit限制
            $now = $time * $epage;
            if ($lim - $time < 1) {
                $epage = $count - $now;
            }
            $limit = "$now,$epage";
            //目标数组
            $voList = $model->table($subQuery_a . ' a')->join('inner join' . $subQuery_b . " b on a.goods_sn = b.goods_sn")->field('distinct a.goods_sn as goods_sn,b.ord_cnt as ord_cnt')
                ->order('ord_cnt desc')->limit($limit)->select();
            //取此100sku
            $goods_sn_list = array();
            foreach ($voList as $k) {
                $goods_sn_list[] = $k['goods_sn'];
            }
            $where_goods_sn['goods_sn'] = array('IN', $goods_sn_list);
            //目标数组
            $subQuery = $model_b->field($field_b)->where($where_b)->where($where_goods_sn)->group($group_b)->having($having_b)->select(false);
            $List = $model->table($subQuery_a . ' a')->join('inner join' . $subQuery . " b on a.goods_sn = b.goods_sn")->field($field_all)->order('ord_cnt desc')->select();
            //导出xls 开始
            if (!empty($List)) {
                $data = array();
                foreach ($List as $key => $val) {
                    unset($val['numrowa']);
                    unset($val['numrow']);
                    fputcsv($fp, $val);
                }
            }
            ob_flush();
            flush();
        }
    }


    public function sale_opr_us(){
        C('DB_CASE_LOWER',true);
        //查询条件开始
        $having_a = ' 1 = 1';
        $having_b = ' 1 = 1';
        if (I('post.supplier_nm') != '') {
            $where_a['supplier_nm'] = I('post.supplier_nm');
        }
        if (I('post.goods_sn') != '') {
            $where_b['goods_sn']  = I('post.goods_sn');
        }else{
            $where_b['goods_sn']  =  array('EXP','IS not NULL');
        }
        $start_date = ('' == I('post.start_date') ) ? date("Ymd",mktime(0, 0, 0, date("m"), date("d")-7, date("Y"))) :str_replace('-','', I('post.start_date'));
        $end_date = ('' == I('post.end_date') ) ? date("Ymd",mktime(0, 0, 0, date("m"), date("d")-1, date("Y"))): str_replace('-','',I('post.end_date'));
        $where_b['dt'] = array(array('EGT',$start_date),array('ELT',$end_date),'and');

        //处理优化编号
        if (I('post.optimize_code') == '1') {
            $where_a['optmz_sn'] = array('EXP','IS NOT NULL');
        }
        if (I('post.optimize_code') == '0') {
            $where_a['optmz_sn'] = array('EXP','IS NULL');
        }
        //处理优化状态
        if (I('post.optimize_status') != '' and I('post.optimize_status') != '5') {
            $where_a['optmz_status'] = I('post.optimize_status');
        }
        if(I('post.optimize_status') == '5'){
            $where_a['optmz_status'] = array('EXP','IS NULL');
        }
        //处理上架
        if (I('post.sale_flag') != '') {
            $where_a['sale_flag'] = I('post.sale_flag');
        }
        //处理上架天数
        $start_onsale_day = I('post.start_onsale_day');
        $end_onsale_day = I('post.end_onsale_day');
        if ($start_onsale_day != '' && $end_onsale_day != '') {
            $where_a['onsale_day'] = array(array('EGT', $start_onsale_day), array('ELT', $end_onsale_day), 'and');
        }
        if ($start_onsale_day != '' && $end_onsale_day == '') {
            $where_a['onsale_day'] = array('EGT', $start_onsale_day);
        }
        if ($where_a == '' && $end_onsale_day != '') {
            $where_a['onsale_day'] = array('ELT', $end_onsale_day);
        }
        //真实库存
        if (I('post.real_stk') != '') {
            $where_a['real_stk'] = I('post.real_stk');
        }
//        var_dump($where_a);exit;
        //站点
        if(I('post.site_from')!=''){
            $where_b['site_tp']=I('post.site_from');
        }else{
            $where_b['site_tp']=array('EXP','IS not NULL');
        }
        //库存区
        $where_a['inv_type'] = '2';
        $where_a['goods_sn'] = array('EXP','IS not NULL');
        //销量
        if(I('post.grtr_ord') != '' ){
            $having_b .=' and ord_cnt >='.I('post.grtr_ord');

        }
        if(I('post.less_ord') !=''){
            $having_b .=' and ord_cnt <='.I('post.less_ord');
        }
        //存销比
        if(I('post.grtr_stksl') != '' ){
            $having_a .=' and stk_sale_rat >='.I('post.grtr_stksl');

        }
        if(I('post.less_stksl') !=''){
            $having_a .=' and stk_sale_rat <='.I('post.less_stksl');
        }
        $where_b_sum = $where_b;
        $where_b['shpp_country_id'] = '226';
        //查询条件结束
        $model_a = M('pub_sku_stk_prd_d','dm_','Oracle_Amazon');
        $model_b = M('ord_sku_sup_d','dm_','Oracle_Amazon');
        $field_a = 'goods_sn,img_url,supplier_nm,optmz_sn,
            buyer_nm,cost_rmb_amt,price,optmz_status,sale_flag,onsale_day,
            sum(onroad_cnt) as onroad_cnt,delivery_day,min_ord_cnt,stock_day,sum(alert_stk) as alert_stk,
            round(stk_sale_rat,2) as stk_sale_rat,sum(stk_cnt) as stk_cnt,sum(pcs_cnt) as pcs_cnt,sum(stock_cnt) as stock_cnt,real_stk
         ';
        $field_b = 'goods_sn,sum(ord_cnt) as ord_cnt';
        $group_a = 'goods_sn,img_url,supplier_nm,optmz_sn,
            buyer_nm,cost_rmb_amt,price,optmz_status,sale_flag,onsale_day,delivery_day,min_ord_cnt,stock_day,real_stk,stk_sale_rat';
        $group_b = 'goods_sn';


        $subQuery_a = $model_a->field($field_a)->where($where_a)->group($group_a)->having($having_a)->select(false);
        $subQuery_b = $model_b->field($field_b)->where($where_b)->group($group_b)->having($having_b)->select(false);
        $subQuery_a = str_replace('numrow','numrowa',$subQuery_a);
        $subQuery_b = str_replace('numrow','numrowb',$subQuery_b);
        $field_all = 'a.*,b.ord_cnt';
        $model = M();
        $count = $model->table($subQuery_a.' a')->join('inner join'.$subQuery_b." b on a.goods_sn = b.goods_sn")->field($field_all)->order('ord_cnt desc')->count('1');
        import('@.ORG.Util.Page_o');
        //创建分页对象
        if ($_REQUEST ['listRows'] == 'undefined' || $_REQUEST ['listRows'] == '') {
            $listRows = '100';
        } else {
            $listRows = $_REQUEST ['listRows'];
        }
        $p = new Page($count, $listRows);
        //目标数组
        $voList = $model->table($subQuery_a.' a')->join('inner join'.$subQuery_b." b on a.goods_sn = b.goods_sn")->field($field_all)->order('ord_cnt desc')->limit($p->firstRow . ',' . $p->listRows)->select();
        //取此100sku
        foreach ($voList as $k){
            $goods_sn_list[] =  $k['goods_sn'];
        }
        $where_goods_sn['goods_sn'] = array('IN',$goods_sn_list);
        //取总销量
        $arr_sale_sum_b = $model_b->field($field_b)->where($where_b_sum)->where($where_goods_sn)->group($group_b)->select();
        foreach ($arr_sale_sum_b as $k){
            $arr_sale_sum_b_list[$k['goods_sn']] = $k['ord_cnt'];
        }
        //分仓
        $arr_tab_stk = $model_a->field('goods_sn,inv_type,sku_size,stk_cnt')->where($where_goods_sn)->select();
        foreach ($arr_tab_stk as $k){
            $k = array_filter($k);
            $inv_stk[$k['goods_sn']][$k['inv_type']][$k['sku_size']] = $k['stk_cnt'];
        }
        foreach($inv_stk as $k=>$v){
            $v = array_filter($v);
            foreach($v as $k1 => $v1){
                $v1 =array_filter($v1);
                $inv_stk_sum[$k][$k1]['size']=$v;
                $inv_stk_sum[$k][$k1]['sum']=array_sum($v1);
            }
        }
        //分仓结束 数组$inv_stk_sum
        //取分尺码数据
        $arr_tab_a = $model_a->field('goods_sn,sku_size,onroad_cnt,alert_stk,alert_stk_flag,stk_sale_rat,pcs_cnt,stock_cnt')->where($where_a)->where($where_goods_sn)->select();
        foreach ($arr_tab_a as $k) {
            $k = array_filter($k);
            $arr_onroad_a[$k['goods_sn']][$k['sku_size']] = $k['onroad_cnt'];//在途分尺码
            $arr_alert_a[$k['goods_sn']][$k['sku_size']]['alert_stk'] = $k['alert_stk'];//警戒分尺码
            $arr_alert_a[$k['goods_sn']][$k['sku_size']]['alert_stk_flag'] = $k['alert_stk_flag'];//警戒分尺码
            $arr_stk_rat_a[$k['goods_sn']][$k['sku_size']] = $k['stk_sale_rat'];//存销比分尺码
            $arr_pcs_cnt_a[$k['goods_sn']][$k['sku_size']] = $k['pcs_cnt'];//待采购分尺码
            $arr_stock_cnt_a[$k['goods_sn']][$k['sku_size']] = $k['stock_cnt'];//提前备货分尺码
        }
        //重组数组start
        foreach($arr_onroad_a as $k=>$v){
            $v = array_filter($v);
            $arr_stat_sum[$k]['onroad_stat']['size']=$v;
            $arr_stat_sum[$k]['onroad_stat']['sum']=array_sum($v);

        }
        foreach($arr_alert_a as $k=>$v){
            $v = array_filter($v);
            $arr_stat_sum[$k]['alert_stat']['size']=$v;
            $arr_stat_sum[$k]['alert_stat']['sum']=array_sum($v);

        }
        foreach($arr_stk_rat_a as $k=>$v){
            $v = array_filter($v);
            $arr_stat_sum[$k]['stk_rat_stat']['size']=$v;
            $arr_stat_sum[$k]['stk_rat_stat']['sum']=array_sum($v);

        }
        foreach($arr_pcs_cnt_a as $k=>$v){
            $v = array_filter($v);
            $arr_stat_sum[$k]['pcs_stat']['size']=$v;
            $arr_stat_sum[$k]['pcs_stat']['sum']=array_sum($v);

        }
        foreach($arr_stock_cnt_a as $k=>$v){
            $v = array_filter($v);
            $arr_stat_sum[$k]['stock_stat']['size']=$v;
            $arr_stat_sum[$k]['stock_stat']['sum']=array_sum($v);

        }
        //分尺码结束 数组$arr_stat_sum
        //取站点销量开始
        $arr_tab_b = $model_b->field('goods_sn,site_tp,sku_size,sum(ord_cnt) as ord_cnt')->where($where_b)->where($where_goods_sn)
            ->group('goods_sn,site_tp,sku_size')
            ->select();
        foreach ($arr_tab_b as $k) {
            $k = array_filter($k);
            $arr_list_b[$k['goods_sn']][$k['site_tp']]['size'][$k['sku_size']] = $k['ord_cnt'];
        }
        foreach ($arr_list_b as $k =>$v) {
            $v = array_filter($v);
            $arr_list_b[$k]['shein']['sum']= array_sum($arr_list_b[$k]['shein']['size']);
            $arr_list_b[$k]['romwe']['sum']= array_sum($arr_list_b[$k]['romwe']['size']);
            $arr_list_b[$k]['emmastyle']['sum']= array_sum($arr_list_b[$k]['emmastyle']['size']);
            $arr_list_b[$k]['platform']['sum']= array_sum($arr_list_b[$k]['platform']['size']);
        }
        //分站点销量结束
        $arr_sum_b = $model_b->field('goods_sn,sku_size,sum(ord_cnt) as ord_cnt')->where($where_b)->where($where_goods_sn)
            ->group('goods_sn,sku_size')
            ->select();
        foreach($arr_sum_b as $k){
            $k = array_filter($k);
            $sum_b[$k['goods_sn']][$k['sku_size']] = $k['ord_cnt'];
        }
        $arr_sale_detail_b = $model_b->field('goods_sn,sku_size,sum(ord_cnt) as ord_cnt')
            ->where($where_b_sum)->where($where_goods_sn)->group('goods_sn,sku_size')->select();

        foreach($arr_sale_detail_b as $k){
            $sale_detail_b[$k['goods_sn']][$k['sku_size']] = $k['ord_cnt'];
        }

        $final_list = array();
        foreach($voList as $k){
            $k = array_filter($k);
            $final_list[$k['goods_sn']] = $k;
            $final_list[$k['goods_sn']]['inv_list'] = $inv_stk_sum[$k['goods_sn']];
            $final_list[$k['goods_sn']]['sale_list'] = $arr_list_b[$k['goods_sn']];
            $final_list[$k['goods_sn']]['size_list'] = $arr_stat_sum[$k['goods_sn']];
            $final_list[$k['goods_sn']]['sum_sale'] = $sum_b[$k['goods_sn']];
            $final_list[$k['goods_sn']]['sum_sale_all'] = $arr_sale_sum_b_list[$k['goods_sn']];
            $final_list[$k['goods_sn']]['sum_sale_detail'] = $sale_detail_b[$k['goods_sn']];
        }
        $final_list = array_filter($final_list);


        $_POST['_sort']='1';
        $page = $p->show();
        $this->assign('list', $final_list);
        $this->assign('page', $page);
        $this->assign('site_tp',I('post.site_from'));
        $this->assign('warning_red',I('post.warning_red'));
        $form='form_sale_sta_us';
        $data = $this->fetch("Echarts:$form");
        echo $data;return;
    }
    /*
     * 第一个表格,根据勾选的编号导出
     */
    public function exp_us(){
        C('DB_CASE_LOWER', true);
        $_POST['page'] =1;
        $having_a = ' 1 = 1';
        $having_b = ' 1 = 1';
        if (I('post.supplier_nm') != '') {
            $where_a['supplier_nm'] = I('post.supplier_nm');
        }
        if(I('post.checkbox') == '') {
            if (I('post.goods_sn') != '') {
                $where_b['goods_sn'] = I('post.goods_sn');
            } else {
                $where_b['goods_sn'] = array('EXP', 'IS not NULL');
            }
        }else{
            $where_b['goods_sn'] = array('IN',trim(I('post.checkbox'),','));
        }

        $start_date = ('' == I('post.start_date') ) ? date("Ymd",mktime(0, 0, 0, date("m"), date("d")-7, date("Y"))) :str_replace('-','', I('post.start_date'));
        $end_date = ('' == I('post.end_date') ) ? date("Ymd",mktime(0, 0, 0, date("m"), date("d")-1, date("Y"))): str_replace('-','',I('post.end_date'));
        $where_b['dt'] = array(array('EGT',$start_date),array('ELT',$end_date),'and');

        //处理优化编号
        if (I('post.optimize_code') == '1') {
            $where_a['optmz_sn'] = array('EXP','IS NOT NULL');
        }
        if (I('post.optimize_code') == '0') {
            $where_a['optmz_sn'] = array('EXP','IS NULL');
        }
        //处理优化状态
        if (I('post.optimize_status') != '' and I('post.optimize_status') != '5') {
            $where_a['optmz_status'] = I('post.optimize_status');
        }
        if(I('post.optimize_status') == '5'){
            $where_a['optmz_status'] = array('EXP','IS NULL');
        }
        //处理上架
        if (I('post.sale_flag') != '') {
            $where_a['sale_flag'] = I('post.sale_flag');
        }
        //处理上架天数
        $start_onsale_day = I('post.start_onsale_day');
        $end_onsale_day = I('post.end_onsale_day');
        if ($start_onsale_day != '' && $end_onsale_day != '') {
            $where_a['onsale_day'] = array(array('EGT', $start_onsale_day), array('ELT', $end_onsale_day), 'and');
        }
        if ($start_onsale_day != '' && $end_onsale_day == '') {
            $where_a['onsale_day'] = array('EGT', $start_onsale_day);
        }
        if ($where_a == '' && $end_onsale_day != '') {
            $where_a['onsale_day'] = array('ELT', $end_onsale_day);
        }
        //真实库存
        if (I('post.real_stk') != '') {
            $where_a['real_stk'] = I('post.real_stk');
        }
        //站点
        if(I('post.site_from')!=''){
            $where_b['site_tp']=I('post.site_from');
        }else{
            $where_b['site_tp']=array('EXP','IS not NULL');
        }
        //库存区
        $where_a['inv_type'] = '1';
        $where_a['goods_sn'] = array('EXP','IS not NULL');
        //销量
        if(I('post.grtr_ord') != '' ){
            $having_b .=' and ord_cnt >='.I('post.grtr_ord');

        }
        if(I('post.less_ord') !=''){
            $having_b .=' and ord_cnt <='.I('post.less_ord');
        }
        //存销比
        if(I('post.grtr_stksl') != '' ){
            $having_a .=' and stk_sale_rat >='.I('post.grtr_stksl');

        }
        if(I('post.less_stksl') !=''){
            $having_a .=' and stk_sale_rat <='.I('post.less_stksl');
        }
        $where_b['shpp_country_id']  = '226';
        //查询条件结束
        $model_a = M('pub_sku_stk_prd_d','dm_','Oracle_Amazon');
        $model_b = M('ord_sku_sup_d','dm_','Oracle_Amazon');
        $field_a = 'goods_sn,img_url,supplier_nm,optmz_sn,
            buyer_nm,cost_rmb_amt,price,optmz_status,sale_flag,onsale_day,
            sum(onroad_cnt) as onroad_cnt,delivery_day,min_ord_cnt,stock_day,sum(alert_stk) as alert_stk,
            round(stk_sale_rat,2) as stk_sale_rat,sum(stk_cnt) as stk_cnt,sum(pcs_cnt) as pcs_cnt,sum(stock_cnt) as stock_cnt,real_stk
         ';
        $field_b = 'goods_sn,sum(ord_cnt) as ord_cnt';
        $group_a = 'goods_sn,img_url,supplier_nm,optmz_sn,
            buyer_nm,cost_rmb_amt,price,optmz_status,sale_flag,onsale_day,delivery_day,min_ord_cnt,stock_day,real_stk,stk_sale_rat';
        $group_b = 'goods_sn';


        $subQuery_a = $model_a->field($field_a)->where($where_a)->group($group_a)->having($having_a)->select(false);
        $subQuery_b = $model_b->field($field_b)->where($where_b)->group($group_b)->having($having_b)->select(false);
        $subQuery_a = str_replace('numrow', 'numrowa', $subQuery_a);
        $subQuery_b = str_replace('numrow', 'numrowb', $subQuery_b);
        $field_all = 'a.*,b.ord_cnt';
        $model = M();
        $temo_count = $model->table($subQuery_a . ' a')->join('inner join' . $subQuery_b . " b on a.goods_sn = b.goods_sn")->field('distinct a.goods_sn as goods_sn,b.ord_cnt as ord_cnt')->order('ord_cnt desc')->select();
        $count = count($temo_count);
        /*
         * 导出逻辑开始
         */
        $title = array(
            'sku',
            '商品图片',
            '供应商',
            '优化编号',
            '买手',
            '成本',
            '单价',
            '优化状态',
            '上架',
            '上架天数',
            '在途数量',
            '货期',
            '最小起订量',
            '备货天数',
            '警戒库存',
            '存销比',
            '库存',
            '待采购数量',
            '备货数量',
            '真实库存',
            '销量'
        );
        $epage = 800;//每次取出数量
        header("Content-type:application/octet-stream");
        header("Accept-Ranges:bytes");
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=" . "销售统计-美国" . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        $fp = fopen('php://output', 'a');
        if (!empty($title)) {
            foreach ($title as $k => $v) {
                $title[$k] = iconv("UTF-8", "UTF-8", $v);
            }
            fputcsv($fp, $title);
        }
        //获取分页数
        $lim = floor($count / $epage);
        if ($lim == 0) {
            $lim = 1;
            $epage = $count;
        }
        for ($time = 0; $time <= $lim; $time++) {
            //设置limit限制
            $now = $time * $epage;
            if ($lim - $time < 1) {
                $epage = $count - $now;
            }
            $limit = "$now,$epage";
            //目标数组
            $voList = $model->table($subQuery_a . ' a')->join('inner join' . $subQuery_b . " b on a.goods_sn = b.goods_sn")->field('distinct a.goods_sn as goods_sn,b.ord_cnt as ord_cnt')
                ->order('ord_cnt desc')->limit($limit)->select();
            //取此100sku
            $goods_sn_list = array();
            foreach ($voList as $k) {
                $goods_sn_list[] = $k['goods_sn'];
            }
            $where_goods_sn['goods_sn'] = array('IN', $goods_sn_list);
            //目标数组
            $subQuery = $model_b->field($field_b)->where($where_b)->where($where_goods_sn)->group($group_b)->having($having_b)->select(false);
            $List = $model->table($subQuery_a . ' a')->join('inner join' . $subQuery . " b on a.goods_sn = b.goods_sn")->field($field_all)->order('ord_cnt desc')->select();
            //导出xls 开始
            if (!empty($List)) {
                $data = array();
                foreach ($List as $key => $val) {
                    unset($val['numrowa']);
                    unset($val['numrow']);
                    fputcsv($fp, $val);
                }
            }
            ob_flush();
            flush();

        }
    }


    //销售产品数
    function goods_form() {
        C("DB_CASE_LOWER",true);
        //根据按周按月按天选择不同的数据库表
        if( I('post.order')=='month' ){
            $model = M('','','Oracle_Amazon');
            $table = "dm_ord_sku_sup_m";
            $field1 = "to_char(to_date(dt,'yyyy-mm'),'yyyy-mm') as dt,sku_size as type,sum(ord_cnt) as ord_cnts";
            $field2 = "to_char(to_date(dt,'yyyy-mm'),'yyyy-mm') as dt,'全部' as type,sum(ord_cnt) as ord_cnts";
            $field3 = "to_char(to_date(dt,'yyyy-mm'),'yyyy-mm') as dt,price,'售价' as type";
        } else if ( I('post.order')=='week' ) {
            $model = M('','','Oracle_Amazon');
            $table = "dm_ord_sku_sup_w";
            $field1 = "substr(dt,'1',4)||'-'||substr(dt,'5',2) as dt,sku_size as type,sum(ord_cnt) as ord_cnts";
            $field2 = "substr(dt,'1',4)||'-'||substr(dt,'5',2) as dt,'全部' as type,sum(ord_cnt) as ord_cnts";
            $field3 = "substr(dt,'1',4)||'-'||substr(dt,'5',2) as dt,price,'售价' as type";
        } else {
            $model = M('','','Oracle_Amazon');
            $table = "dm_ord_sku_sup_d";
            $field1 = "to_char(to_date(dt,'yyyy-mm-dd'),'yyyy-mm-dd') as dt,sku_size as type,sum(ord_cnt) as ord_cnts";
            $field2 = "to_char(to_date(dt,'yyyy-mm-dd'),'yyyy-mm-dd') as dt,'全部' as type,sum(ord_cnt) as ord_cnts";
            $field3 = "to_char(to_date(dt,'yyyy-mm-dd'),'yyyy-mm-dd') as dt,price,'售价' as type";
        }

        $query_date = $this->get_query_date();
        $dt = " dt>= ".$query_date['start_date']." and dt <= ".$query_date['end_date'];

        if (I('post.goods_sn') != '') {
            $goods_sn = " and a.goods_sn = '".I('post.goods_sn')."' ";
        }
        if(I('post.site_tp') !=''){
            $array = explode(',',I('post.site_tp'));
            $site_tp = "(";
            foreach($array as $value){
                $site_tp =  $site_tp."'".$value."',";
            }
            $site_tp = " and site_tp in ".$site_tp."'')";
        }
        $sku_size = " and sku_size in ('S','XS','M','L')";

        $sql_size = "select ".$field1."
                from ".$table." a
                where ".$dt.$site_tp.$sku_size.$goods_sn."
                group by dt,sku_size
                order by dt desc";

        $sql_total = "select ".$field2."
                from ".$table." a
                where ".$dt.$site_tp.$goods_sn."
                group by dt
                order by dt desc";

        $sql_price = "select ".$field3."
                from ".$table." a
                inner join dm_pub_sku_stk_prd_d b on a.goods_sn=b.goods_sn
                where ".$dt.$site_tp.$goods_sn."
                order by dt desc";

        $list_size =  $model->query($sql_size);
        $list_total =  $model->query($sql_total);
        $list = array_merge($list_size,$list_total);
        $list_price =  $model->query($sql_price);
        //echo $model->getLastSql();die;
        //print_r($list);die;

        if(empty($list)&&empty($list_price)){
            $this->ajaxReturn( array('data'=> '当前天暂无数据') );
        }

        $arr =array('title'=>'销售产品数','dim1'=>'dt','dim2'=>'type','field1'=>'ord_cnts','field2'=>'price');
        $this->statistic_goods($list,$list_price,$arr);
    }

    function statistic_goods($list1,$list2,$arr,$unselected){
        $legend = array();
        $xAxis = array();
        $series = array();
        $member = array();

        $date=$this->get_date();
        foreach ($list1 as $k => $v) {
            array_push($legend, $v[$arr['dim2']]);
            $result[$v[$arr['dim2']]][$v[$arr['dim1']]] = $v[$arr['field1']];
        }

        foreach ($list2 as $k => $v) {
            array_push($legend, $v[$arr['dim2']]);
            $result[$v[$arr['dim2']]][$v[$arr['dim1']]] = $v[$arr['field2']];
        }

        if (I('post.order')=='week') {
            $xAxis = $date[1];
            $date = $date[2];
            $this-> xAxis=$xAxis;
        } else {
            $this-> xAxis=$date;
        }

        foreach ($result as $k => $v) {
            foreach ($date as $d) {
                if (!empty($v[$d])) {
                    $member = $v[$d];
                }else{
                    $member = 0;
                }
                $series[$k][$d] = $member;
            }
        }


        $this-> legend=array_unique($legend);
        $this-> xAxis=$date;
        $this-> series=$series;
        $this-> title=$arr['title'];

        //默认展示隐藏部分
        $this -> unselected = $unselected;

        $data=$this->fetch('Echarts:echarts_line_goods');
//        print_r($data);die;
        $this->ajaxReturn(array('data'=>$data));
    }

    //销售用户数
    function member_form() {
        //根据按周按月按天选择不同的数据库表
        if( I('post.order')=='month' ){
            $model = M('','','Oracle_Amazon');
            $table = "dm_ord_sku_sup_m";
            $field1 = "to_char(to_date(dt,'yyyy-mm'),'yyyy-mm') as dt,sku_size as type,count(distinct member_id) as members";
            $field2 = "to_char(to_date(dt,'yyyy-mm'),'yyyy-mm') as dt,'全部' as type,count(distinct member_id) as members";
            $field3 = "to_char(to_date(dt,'yyyy-mm'),'yyyy-mm') as dt,price,'售价' as type";
        } else if ( I('post.order')=='week' ) {
            $model = M('','','Oracle_Amazon');
            $table = "dm_ord_sku_sup_w";
            $field1 = "substr(dt,'1',4)||'-'||substr(dt,'5',2) as dt,sku_size as type,count(distinct member_id) as members";
            $field2 = "substr(dt,'1',4)||'-'||substr(dt,'5',2) as dt,'全部' as type,count(distinct member_id) as members";
            $field3 = "substr(dt,'1',4)||'-'||substr(dt,'5',2) as dt,price,'售价' as type";
        } else {
            $model = M('','','Oracle_Amazon');
            $table = "dm_ord_sku_sup_d";
            $field1 = "to_char(to_date(dt,'yyyy-mm-dd'),'yyyy-mm-dd') as dt,sku_size as type,count(distinct member_id) as members";
            $field2 = "to_char(to_date(dt,'yyyy-mm-dd'),'yyyy-mm-dd') as dt,'全部' as type,count(distinct member_id) as members";
            $field3 = "to_char(to_date(dt,'yyyy-mm-dd'),'yyyy-mm-dd') as dt,price,'售价' as type";
        }

        $query_date = $this->get_query_date();
        $dt = " dt>= ".$query_date['start_date']." and dt <= ".$query_date['end_date'];

        if (I('post.goods_sn') != '') {
            $goods_sn = " and a.goods_sn = '".I('post.goods_sn')."' ";
        }
        if(I('post.site_tp') !=''){
            $array = explode(',',I('post.site_tp'));
            $site_tp = "(";
            foreach($array as $value){
                $site_tp =  $site_tp."'".$value."',";
            }
            $site_tp = " and site_tp in ".$site_tp."'')";
        }
        $sku_size = " and sku_size in ('S','XS','M','L')";

        $sql_size = "select ".$field1."
                from ".$table." a
                where ".$dt.$site_tp.$sku_size.$goods_sn."
                group by dt,sku_size
                order by dt desc";

        $sql_total = "select ".$field2."
                from ".$table." a
                where ".$dt.$site_tp.$goods_sn."
                group by dt
                order by dt desc";

        $sql_price = "select ".$field3."
                from ".$table." a
                inner join dm_pub_sku_stk_prd_d b on a.goods_sn=b.goods_sn
                where ".$dt.$site_tp.$goods_sn."
                order by dt desc";

        $list_size =  $model->query($sql_size);
        $list_total =  $model->query($sql_total);
        $list = array_merge($list_size,$list_total);
        $list_price =  $model->query($sql_price);
        //echo $model->getLastSql();die;
        //print_r($list);die;

        if(empty($list)&&empty($list_price)){
            $this->ajaxReturn( array('data'=> '当前天暂无数据') );
        }

        $arr =array('title'=>'销售用户数','dim1'=>'dt','dim2'=>'type','field1'=>'members','field2'=>'price');
        $this->statistic_member($list,$list_price,$arr);
    }

    function statistic_member($list1,$list2,$arr,$unselected){
        $legend = array();
        $xAxis = array();
        $series = array();
        $member = array();

        $date=$this->get_date();
        foreach ($list1 as $k => $v) {
            array_push($legend, $v[$arr['dim2']]);
            $result[$v[$arr['dim2']]][$v[$arr['dim1']]] = $v[$arr['field1']];
        }

        foreach ($list2 as $k => $v) {
            array_push($legend, $v[$arr['dim2']]);
            $result[$v[$arr['dim2']]][$v[$arr['dim1']]] = $v[$arr['field2']];
        }

        if (I('post.order')=='week') {
            $xAxis = $date[1];
            $date = $date[2];
            $this-> xAxis=$xAxis;
        } else {
            $this-> xAxis=$date;
        }

        foreach ($result as $k => $v) {
            foreach ($date as $d) {
                if (!empty($v[$d])) {
                    $member = $v[$d];
                }else{
                    $member = 0;
                }
                $series[$k][$d] = $member;
            }
        }


        $this-> legend=array_unique($legend);
        $this-> xAxis=$date;
        $this-> series=$series;
        $this-> title=$arr['title'];

        /*print_r($legend);
        print_r($series);
        print_r($date);die;*/


        //默认展示隐藏部分
        $this -> unselected = $unselected;

        $data=$this->fetch('Echarts:echarts_line_member');
//        print_r($data);die;
        $this->ajaxReturn(array('data'=>$data));
    }

    function get_full_data($model,$field,$where,$join,$group,$order){
        //取得记录总数
        if ($group != '') {
            $subQuery = $model->field($field)->join($join)->where($where)->group($group)->select(false);
            $count = $model->table($subQuery.' a')->count('1');
        } else {
            $count = $model->where($where)->count('1');
        }
        if($count>0){
            $voList = $model->field($field)->join($join)->where($where)->group($group)->order($order)->select();

            /*$voList = $model->field($field)->join($join)->where($where)->group($group)->order($order)->select(false);
            print_r($voList);die;*/
            return $voList;

        }else{
            $this->ajaxReturn( array('data'=> '当前天暂无数据') );
        }
    }

    /*
        统计渲染图表信息legend
        用于按site_from系列
         */
    function statistic_bar_legend($list,$arr,$flag){
        $legend = array();
        $xAxis = array();
        $series = array();
        $member = array();
        $date = array();
        if($arr['flag']=='xAxisNum') {
            $date=$this->get_date();
            if($arr['area']=='area'){
                array_push($legend, '0-7天');
                array_push($legend, '8-15天');
                array_push($legend, '16-30天');
                array_push($legend, '31-60天');
                array_push($legend, '61-90天');
                array_push($legend, '91-180天');
                array_push($legend, '181+天');
                $groupList=$arr['dimGroup'];
                foreach ($groupList as $k => $v) {
                    foreach($v as $k1=>$v2){
                        array_push($xAxis, $v['grp_nm']);
                    }

                }
                $xAxisTemp=array_unique($xAxis);
                $i=0;
                foreach($legend as $k1=>$v1){
                    foreach ($xAxisTemp as $k2 => $v2) {
                        foreach ($list as $key => $value) {
                            $i=0;
                            if($value[$arr['type']]==$v1&&$value['grp_nm']==$v2){
                                $i=1;
                                $series[$v1][$v2]= $value[$arr['type2']];
                                break;
                            }

                        }
                        if($i==0){
                            $series[$v1][$v2]=0;

                        }
                    }

                }
                $xAxisTemp=array_unique($xAxis);

            }else{
                foreach($list as $k => $v){
                    array_push($legend, $v[$arr['dim2']]);
                    array_push($xAxis, $v['grp_nm']);
                    $series[$v[$arr['dim2']]][$v['grp_nm']]= $v[$arr['field']];
                }
            }
            //图表横坐标是区间
            $xAxis=array_unique($xAxis);
            $this-> xs=$xAxis;
        }else{
            $date=$this->get_date();
            //图表横坐标是日期
            if (I('post.order')=='month') {
                foreach ($list as $k => $v) {
                    $result[$v[$arr['dim2']]][$v[$arr['dim1']]] = $v[$arr['field']];
                }
            } else if (I('post.order')=='week') {
                foreach ($list as $k => $v) {
                    $result[$v[$arr['dim2']]][$v[$arr['dim1']]] = $v[$arr['field']];
                }
            } else if (I('post.order')=='hour') {
                foreach ($list as $k => $v) {
                    $result[$v[$arr['dim2']]][$v[$arr['dim1']]] = $v[$arr['field']];
                }
            } else {
                foreach ($list as $k => $v) {
                    $result[$v[$arr['dim2']]][$v[$arr['dim1']]] = $v[$arr['field']];
                }
            }

            if (I('post.order')=='week') {
                $xAxis = $date[1];
                $date = $date[2];
                $this-> xs=$xAxis;
            } else {
                $this-> xs=$date;
            }
            foreach ($result as $k => $v) {
                foreach ($date as $d) {
                    if (!empty($v[$d])) {
                        $member = $v[$d];
                    }else{
                        $member = 0;
                    }
                    $series[$k][$d] = $member;
                }
            }
        }
        if($arr['legendArea']='area'&&$arr['legend'] ==''&&$arr['area']!='area'){
            array_push($legend, '0-10');
            array_push($legend, '10-15');
            array_push($legend, '15-20');
            array_push($legend, '20-30');
            array_push($legend, '30-40');
            array_push($legend, '40-50');
            array_push($legend, '50-60');
            array_push($legend, '60-70');
            array_push($legend, '70-80');
            array_push($legend, '80-90');
            array_push($legend, '90-100');
            array_push($legend, '100+');
        }
        if($arr['area']='area'&&$arr['all']='all'&&$arr['legendArea']!='area'&&$arr['legend'] ==''){
            array_push($legend, '0-7天');
            array_push($legend, '8-15天');
            array_push($legend, '16-30天');
            array_push($legend, '31-60天');
            array_push($legend, '61-90天');
            array_push($legend, '91-180天');
            array_push($legend, '181+天');
        }
        if($arr['legend'] !=''){
            $listBuyer=$arr['legend'];
            foreach ($listBuyer as $k => $v) {
                array_push($legend, $v['grp_nm']);
            }
        }
        $legend=array_unique($legend);
        $this-> legend=$legend;
        $this-> series=$series;
        $this-> title=$arr['title'];
        $this-> flag=$flag;

        if (I('post.order')=='hour') {
            $data=$this->fetch('Echarts:echarts_line_hour');
        } else {
            $data=$this->fetch('Echarts:echarts_line_bar_draggable');
        }
        $this->ajaxReturn(array('data'=>$data));

    }

    /*
    统计渲染图表信息line
    用于按site_from系列
     */
    function statistic_bar($list,$arr){
        $legend = array();
        $xAxis = array();
        $series = array();
        $member = array();
        $date = array();

        $date=$this->get_dates($start_date,$end_date,$date);


        foreach ($list as $k => $v) {
            array_push($legend, $v[$arr['dim2']]);
            $result[$v[$arr['dim2']]][$v[$arr['dim1']]] = $v[$arr['field']];
        }
        if (I('post.order')=='week') {
            $xAxis = $date[1];
            $date = $date[2];
            $this-> xAxis=$xAxis;
        } else {
            $this-> xAxis=$date;
        }

        foreach ($result as $k => $v) {
            foreach ($date as $d) {
                if (!empty($v[$d])) {
                    $member = $v[$d];
                }else{
                    $member = 0;
                }
                $series[$k][$d] = $member;
            }
        }
        //特殊处理-分类国家销售统计-首字母排序,other置后
        if($arr['legend_list'] !=''){
            $this-> legend=array_unique($arr['legend_list']);
        }else{
            $this-> legend=array_unique($legend);
        }
        $this-> series=$series;
        $this-> title=$arr['title'];
        if ($arr['flag'] == 'line_bar') {
            $this-> line_field='转化率';
        }
        $data=$this->fetch('Echarts:echarts_bar_inside');

        $this->ajaxReturn(array('data'=>$data));
    }

    /*
    获取显示时间区间
     */
    private function get_dates($start_date, $end_date, $date){
        if( I('post.order')=='month'){
            $start_date = ('' == I('post.start_date') ) ? date("Y-m-d",mktime(0, 0, 0, date("m")-13, 1, date("Y"))) : I('post.start_date');
            $end_date = ('' == I('post.end_date') ) ? date("Y-m-d",mktime(0, 0, 0, date("m"), 1, date("Y"))) : I('post.end_date');

            $timestamp = strtotime($start_date);
            while($timestamp <= strtotime($end_date)){
                $timestamp=mktime(0, 0, 0, date("m",$timestamp)+1, 1, date("Y",$timestamp));
                $date[] = date('Y-m', $timestamp-1);
            }
            return $date;
        }else if(I('post.order')=='week'){
            $start_date = ('' == I('post.start_date') ) ? date("Y-m-d",strtotime('-1 Mon',mktime(0, 0, 0, date("m")-3, 1, date("Y")))) : I('post.start_date');
            $end_date = ('' == I('post.end_date') ) ? date("Y-m-d",strtotime("this monday")) : I('post.end_date');

            $start_weeks = date('W', strtotime($start_date));
            $week = date('w', strtotime($start_date));
            if($week == 0){
                $i = strtotime('+1 day', strtotime($start_date) );
            }else if($week == 1){
                $i = strtotime($start_date);
            }else{
                $i = strtotime('+'.(8-$week).' days', strtotime($start_date) );
            }

            for($i ; $i<= strtotime($end_date); $i+=86400*7 ){
                $date_list[] = '第'.date('W', $i).'周';
                $date_sort[]=date('Y',$i).'-'.date('W', $i);
            }

            $date[1]=$date_list;
            $date[2]=array_unique($date_sort);
            return $date;
        }else if(I('post.order')=='hour'){
            $start_date = ('' == I('post.start_date') ) ? date("Y-m-d H:i:s",mktime(0, 0, 0, date("m")  , date("d")-1, date("Y"))) : I('post.start_date');
            $end_date = ('' == I('post.end_date') ) ?  date("Y-m-d H:i:s",time()) : I('post.end_date');

            for($i = strtotime($start_date) ; $i<= strtotime($end_date); $i+= 3600 ){
                $date[] = date('Y-m-d H:i:s', $i);
            }
            return $date;

        }else{
            $start_date = ('' == I('post.start_date') ) ? date("Y-m-d",mktime(0, 0, 0, date("m"), date("d")-30, date("Y"))) : I('post.start_date');
            $end_date = ('' == I('post.end_date') ) ? date("Y-m-d",time()) : I('post.end_date');

            for($i=strtotime($start_date) ; $i<= strtotime($end_date); $i+=86400 ){
                $date[] = date('Y-m-d', $i);
            }
            return $date;
        }
    }

    function _list($model, $field, $map, $sortBy, $form, $asc = false){
        //字段排序 默认主键
        if (isset($_REQUEST ['_order'])) {
            $order = $_REQUEST ['_order'];
        } else {
            $order = !empty($sortBy) ? $sortBy : $model->getPk();
        }

        //排序默认倒序排列
        //sort 0：倒序 非0：正序
        if (isset($_REQUEST ['_sort'])) {
            $sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
        } else {
            $sort = $asc ? 'asc' : 'desc';
        }

        if(empty($map['goods_sn'])){
            $count = $model->where($map)->count('1');
        } else {
            $subQuery = $model->field('goods_sn')->where($map)->group('goods_sn,country,goods_id')->select(false);
            $count = $model->table($subQuery.' a')->count('1');
        }

        if($count>=0){
            import('@.ORG.Util.Page');
            //创建分页对象
            if (!empty($_REQUEST ['listRows'])) {
                $listRows = $_REQUEST ['listRows'];
            } else {
                $listRows = '31';
            }
            $p = new Page($count, $listRows);

            if(empty($map['goods_sn'])){
                $voList = $model->field($field)->where($map)->order(($order." ".$sort))->limit($p->firstRow.','.$p->listRows)->select();
            } else {
                $voList = $model->field("goods_sn,country,goods_id,sum(goods_cn) as goods_cnt")->where($map)->group("goods_sn,country,goods_id")->order("goods_cnt desc")->limit($p->firstRow.','.$p->listRows)->select();
                $form = 'form_sku_country_p';
            }

            foreach ($map as $key => $val) {
                if (!is_array($val)) {
                    $p->parameter.="$key=".urldecode($val)."&";
                }
            }

            //分页显示
            $page = $p->show();
            //列表排序显示
            $sortImg = $sort; //排序图标
            $sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列' ;//排序提示
            $sort = $sort == 'desc' ? 1 : 0 ;//排序方式

            //模板赋值
            $this->assign('list',$voList);
            $this->assign('sort',$sort);
            $this->assign('order',$order);
            $this->assign('sortImg',$sortImg);
            $this->assign('sortType',$sortAlt);
            $this->assign('page',$page);

            $data = $this->fetch('Echarts:'.$form);
            echo $data;
        }
        return;
    }

    /*
     获取显示时间区间
  */
    public function get_date(){
        $date = array();
        if( I('post.order')=='month'){
            $start_date = ('' == I('post.start_date') ) ? '2016-06-01' : I('post.start_date');
            $end_date = ('' == I('post.end_date') ) ? date("Y-m-d",mktime(0, 0, 0, date("m")-1, 1, date("Y"))) : I('post.end_date');
            $timestamp = strtotime($start_date);
            while($timestamp <= strtotime($end_date)){
                $timestamp=mktime(0, 0, 0, date("m",$timestamp)+1, 1, date("Y",$timestamp));
                $date[] = date('Y-m', $timestamp-1);
            }
            return $date;
        }else if(I('post.order')=='week'){
            $start_date = ('' == I('post.start_date') ) ? date("Y-m-d",strtotime('-1 Mon',mktime(0, 0, 0, date("m")-3, 1, date("Y")))) : I('post.start_date');
            $end_date = ('' == I('post.end_date') ) ? date("Y-m-d",strtotime("this monday")) : I('post.end_date');
            $week=date('w',strtotime($start_date));
            //当时间是周日的时候，$week为0，这时候需要向前推6天，以确保都放在周一计算
            if($week==1){
                $unixTimeStart = strtotime($start_date);
            }elseif($week==0){
                $unixTimeStart = strtotime($start_date)-6*86400;
            }else{
                $unixTimeStart = strtotime($start_date)-($week-1)*86400;
            }
            $unixTimeEnd = strtotime($end_date);
            for($i = $unixTimeStart; $i<= $unixTimeEnd; $i+= 604800){
                $date[1][] = '第'. date('W',$i) .'周';
                $date[2][] = date('Y',$i).'-'.date('W',$i);
            }
            return $date;

        }else if(I('post.order')=='hour'){
            $start_date = ('' == I('post.start_date') ) ? date("Y-m-d H:i:s",mktime(0, 0, 0, date("m")  , date("d")-1, date("Y"))) : I('post.start_date');
            $end_date = ('' == I('post.end_date') ) ?  date("Y-m-d H:i:s",time()) : I('post.end_date');

            for($i = strtotime($start_date) ; $i<= strtotime($end_date); $i+= 3600 ){
                $date[] = date('Y-m-d H:i:s', $i);
            }
            return $date;
        }else{
            if(I('post.week_status')!='') {
                $query_date = $this->get_query_date();
                $map_tmp['day_of_week'] = I('post.week_status');
                $map_tmp['date_key'] = array(array('EGT', $query_date['start_date']), array('ELT', $query_date['end_date']), 'and');
                $voList_dt_tmp = M('dm_date_td', null, 'Mysql_WH')->field('date(date_key) as dt')->where($map_tmp)->order('dt asc')->select();
                foreach ($voList_dt_tmp as $k) {
                    $date[] = $k['dt'];
                }
            }else {
                $start_date = ('' == I('post.start_date')) ? date("Y-m-d",
                    mktime(0, 0, 0, date("m"), date("d") - 30, date("Y"))) : I('post.start_date');
                $end_date = ('' == I('post.end_date')) ? date("Y-m-d",
                    mktime(0, 0, 0, date("m"), date("d"), date("Y"))) : I('post.end_date');
                for ($i = strtotime($start_date); $i <= strtotime($end_date); $i += 86400) {
                    $date[] = date('Y-m-d', $i);
                }
            }
            return $date;
        }
    }
    /**
     * 头单售罄率
     * @author jiang
     * @time 2016-10-17
     */
    function ord_frst_sold_out()
    {
        $order = I('post.order');
        //生成选择条件
        if($order == 'day'){
            $model = M('dm_ord_frst_sold_out_d',null,'Mysql_Amazon');
            $field = "date(dt) as dt,";
        }elseif($order == 'month'){
            $model = M('dm_ord_frst_sold_out_m',null,'Mysql_Amazon');
            $field = "date_format(concat(dt,'00'),'%Y-%m') as dt,";
        }elseif($order == 'week'){
            $model = M('dm_ord_frst_sold_out_w',null,'Mysql_Amazon');
            $field = "concat(substr(dt,'1',4),'-',substr(dt,'5',2)) as dt,";
        }
        $field .= "goods_status,
                   cate_nm,
                   sum(onsale_sku_cnt) as onsale_sku_cnt,
                   sum(saleout_7_cnt) as saleout_7_cnt,
                   round(sum(saleout_7_cnt)/sum(onsale_sku_cnt)*100,2) as saleout_7_rate,
                   sum(saleout_15_cnt) as saleout_15_cnt,
                   round(sum(saleout_15_cnt)/sum(onsale_sku_cnt)*100,2) as saleout_15_rate,
                   sum(saleout_30_cnt) as saleout_30_cnt,
                   round(sum(saleout_30_cnt)/sum(onsale_sku_cnt)*100,2) as saleout_30_rate,
                   sum(saleout_60_cnt) as saleout_60_cnt,
                   round(sum(saleout_60_cnt)/sum(onsale_sku_cnt)*100,2) as saleout_60_rate,
                   sum(saleout_90_cnt) as saleout_90_cnt,
                   round(sum(saleout_90_cnt)/sum(onsale_sku_cnt)*100,2) as saleout_90_rate
                   ";
        $map = '1=1';
        $query_date = $this ->get_query_date();
        $map .= ' and dt>='.$query_date['start_date'];
        $map .= ' and dt<='.$query_date['end_date'];
        if(I('post.cate_nm') != ''){
            $cate_nm_tmp = implode("','", I('post.cate_nm'));
            $site_tm = "'" . $cate_nm_tmp . "'";
            $map .= ' and cate_nm in(' . $site_tm . ')';
        }
        if(I('post.site_tp') != ''){
            $map .= ' and site_tp="'.I('post.site_tp').'"';
        }
        if(I('post.goods_status') != ''){
            $map .= ' and goods_status='.I('post.goods_status');
        }
        $group = 'dt';
        $order = 'dt DESC';
        $voList = $model -> field($field)->where($map)->group($group)->order($order)->select();
        if(count($voList) == 0){
            $this->ajaxReturn(array('data'=>'暂时没有数据'));
        }
        $temp_arr = array();
        foreach ($voList as $k=>$v)
        {
            $temp_arr['7天售罄率'][$v['dt']] = $v['saleout_7_rate'];
            $temp_arr['15天售罄率'][$v['dt']] = $v['saleout_15_rate'];
            $temp_arr['30天售罄率'][$v['dt']] = $v['saleout_30_rate'];
            $temp_arr['60天售罄率'][$v['dt']] = $v['saleout_60_rate'];
            $temp_arr['90天售罄率'][$v['dt']] = $v['saleout_90_rate'];
        }
        $legend = array('7天售罄率','15天售罄率','30天售罄率','60天售罄率','90天售罄率');
        $xAxis = array();
        $series = array();
        $member = array();
//        print_r($temp_arr);die;
        $date=$this->get_date();
        if (I('post.order')=='week') {
            $xAxis = $date[1];
            $date = $date[2];
            $this-> xAxis=$xAxis;
        } else {
            $this-> xAxis=$date;
        }
        foreach ($temp_arr as $k => $v) {
            foreach ($date as $d) {
                if (!empty($v[$d])) {
                    $member = $v[$d];
                }else{
                    $member = 0;
                }
                $series[$k][$d] = $member;
            }
        }
        $this-> xAxis=$date;
        $this-> series=$series;
//        print_r($series);die;

        //默认展示隐藏部分
        $this -> legend = $legend;
        $data=$this->fetch('Echarts:echarts_line_ord_frst_sold');
        $this->ajaxReturn(array('data'=>$data));
    }
    function statistic_funnel($list,$arr){
        $legend = array();
        $xAxis = array();
        $series = array();
        $member = array();
        $date = array();
        $date = $this->get_date($start_date,$end_date,$date);
        if (I('post.order')=='month') {
            foreach ($list as $k => $v) {
                array_push($legend, $v[$arr['dim1']]);
                $result[$v['dt']][$v[$arr['dim1']]] = $v[$arr['field']];
            }
        } else if (I('post.order')=='week') {
            foreach ($list as $k => $v) {
                array_push($legend, $v[$arr['dim1']]);
                $result[$v['dt']][$v[$arr['dim1']]] = $v[$arr['field']];
            }
        } else {
            foreach ($list as $k => $v) {
                array_push($legend, $v[$arr['dim1']]);
                $result[$v['dt']][$v[$arr['dim1']]] = $v[$arr['field']];
            }
        }
        if ($arr['flag'] == 'single') {
            $this-> xAxis=$arr['xAxis'];
            $d = $arr['xAxis'];
            foreach ($legend as $l) {
                if (!empty($result[$d][$l])) {
                    $member = $result[$d][$l];
                } else {
                    $member = 0;
                }
                $res[$l] = $member;
            }
            $series[$d] = $res;
        } else {
            if (I('post.order')=='week') {
                $xAxis = $date[1];
                $date = $date[2];
                $this-> xAxis=$xAxis;
            } else {
                $this-> xAxis=$date;
            }
            foreach ($date as $d) {
                foreach ($legend as $l) {
                    if (!empty($result[$d][$l])) {
                        $member = $result[$d][$l];
                    } else {
                        $member = 0;
                    }
                    $res[$l] = $member;
                }
                $series[$d] = $res;
            }
        }
        $this-> legend=array_unique($legend);
        $this-> series=$series;
        $this-> title=$arr['title'];
        $this-> order=$arr['order'];
        $this-> style=$arr['style'];


        if ( empty($series)) {
            $this->ajaxReturn(array('data'=>'当前暂无数据'));
        } else {
            if ($arr['flag'] == 'single') {
                $data=$this->fetch('Echarts:echarts_pie_single');

                $this->ajaxReturn(array('data'=>$data));
            } else {
                $data=$this->fetch('Echarts:echarts_funnel');
                $this->ajaxReturn(array('data'=>$data));
            }
        }
    }
    /*
     * 订单概况统计
     */
    function order_summary_table(){
        $model=M('dm_ord_acc_order_d',null,'Mysql_WH');
        //昨日订单，总订单，过期审核
        $map['pay_dt']=date('Ymd',time()-3600*24);
        $field="sum(order_cnt) as order_cnt,sum(acc_order_cnt) as acc_order_cnt,sum(noaudit_order_cnt) as noaudit_order_cnt";
        $list=$model->field($field)->where($map)->group('pay_dt')->select();

        //退款订单
        $start = empty($_POST['start_time'])? (date('Y-m-d',time()-15*3600*24)):($_POST['start_date']);
        $end   = empty($_POST['end_time'])? (date('Y-m-d',time()-3600*24)):($_POST['end_date']);
        $start_date=str_replace('-','',$start);
        $end_date=str_replace('-','',$end);
        $where['pay_dt'] = array(array('EGT',$start_date),array('ELT',$end_date),'and');
        $acc_start=$model->field('acc_order_cnt')->where("pay_dt= '".$start_date."'")->select();
        $acc_end=$model->field('acc_order_cnt')->where("pay_dt= '".$end_date."'")->select();
        $acc=$acc_end[0]['acc_order_cnt']-$acc_start[0]['acc_order_cnt'];

        $refund_order=$model->field('sum(refund_order_cnt) as refund_order_cnt')->where($where)->select();
        $refund_order_cnt=$refund_order[0]['refund_order_cnt'];

        $refund_pnt=round($refund_order_cnt/$acc*100,4);

        //赠送服装gift_order_cnt,red_share_order_cnt
        $gift=$model->field('sum(gift_order_cnt) as gift_order_cnt,sum(red_share_order_cnt) as red_share_order_cnt')->where($where)->select();

        //无货订单
        $nostock=$model->field('sum(nostock_order_cnt) as nostock_order_cnt')->where($where)->select();
        $nostock_order_cnt=$nostock[0]['nostock_order_cnt'];
        $nostock_pnt=round($nostock[0]['nostock_order_cnt']/$acc*100,4);

        //过期发货
        $map_out['pay_dt']=array('ELT',date('Ymd',time()-3600*24*6));
        $outdata = $model->where($map_out)->order('pay_dt desc')->select();
        if(! empty($outdata)){
            //普通商品数量总和
            $ordinaryGoodsSum = $outdata[0]['ordinary_goods_acc_cnt'];
            //预售商品数量总和
            $presaleGoodsSum = $outdata[0]['presale_goods_acc_cnt'];
            //普通订单数量总和
            $ordinaryOrderSum = $outdata[0]['ordinary_order_acc_cnt'];
            //预售订单数量总和
            $presaleOrderSum = $outdata[0]['presale_order_acc_cnt'];

            $prod_sum=$ordinaryGoodsSum+$presaleGoodsSum;
            $ord_sum=$ordinaryOrderSum+$presaleOrderSum;
            //下拉列表记录数组
            $detail = array();
            //循环计算每日商品数量和订单数量
            foreach($outdata as $v) {
                $record = array();
                $record['pay_dt'] = date('Y-m-d', strtotime($v['pay_dt']));//$v['pay_dt'];
                $record['listprodNum'] = $v['ordinary_goods_cnt'] + $v['presale_goods_cnt'];
                $record['listordNum']  = $v['ordinary_order_cnt'] + $v['presale_order_cnt'];
                //过滤0
                if($record['listordNum']!=0 &&$record['listprodNum']!=0){
                    $detail[] = $record;
                }
            }
            $this->assign('prodSum',$prod_sum);
            $this->assign('ordSum',$ord_sum);
            $this->assign('list',$detail);
        }

        $this->assign('order_cnt',$list[0]['order_cnt']);
        $this->assign('acc_order_cnt',$list[0]['acc_order_cnt']);
        $this->assign('noaudit_order_cnt',$list[0]['noaudit_order_cnt']);
        $this->assign('noaudit_order_cnt',$list[0]['noaudit_order_cnt']);
        $this->assign('refund_order_cnt',$refund_order[0]['refund_order_cnt']);
        $this->assign('refund_pnt',$refund_pnt);
        $this->assign('gift_order_cnt',$gift[0]['gift_order_cnt']);
        $this->assign('red_share_order_cnt',$gift[0]['red_share_order_cnt']);
        $this->assign('nostock_order_cnt',$nostock_order_cnt);
        $this->assign('nostock_pnt',$nostock_pnt);

        $data=$this->fetch('Echarts:form_order_summary');
        echo $data;exit;
    }
    /**
     * 维护人有货统计-列表
     * @author jiang
     * @time 2017-03-07 15:22
     */
    function ord_stk_mntn_manager()
    {
        $map =' 1=1';
        $model = M('dm_ord_stk_mntn_manager_d',null,'Mysql_WH');
        $field = "date(seq_time) as dt,
                  mntn_manager_nm,
                  sum(checked_goods) as checked_goods,
                  sum(checked_stk_goods) as checked_stk_goods,
                  case when sum(checked_goods)>0 then round(sum(checked_stk_goods)/sum(checked_goods)*100,2) else 0 end as checked_goods_rate
                ";
        if (I('post.site_tp') !='') {
            $map .=" and site_tp ='". I('post.site_tp')."'";
        }
        if(I('post.check_nm') != '')
        {
            $map .=" and mntn_manager_nm ='". I('post.check_nm')."'";
        }
        $query_date=$this->get_query_date();
        $map .=" and seq_time >='".$query_date['start_date']."'";
        $map .=" and seq_time <='".$query_date['end_date']."'";
        $group = 'seq_time,mntn_manager_nm';
        $_REQUEST ['_order'] = 'seq_time';
        //取得记录总数
        $subQuery = $model->field($field)->where($map)->group($group)->select(false);
        $count = $model->table($subQuery.' a')->count('1');

        if ($count>0) {
            import('@.ORG.Util.Page');
            //创建分页对象
            if (!empty($_REQUEST ['listRows'])) {
                $listRows = $_REQUEST ['listRows'];
            } else {
                $listRows = '100';
            }
            $p = new Page($count, $listRows);

//            $voList = $model->field($field)->where($map)->where("mntn_manager_nm<>''")->group($group)->order('seq_time desc,checked_goods DESC')->limit($p->firstRow.','.$p->listRows)->select();
            $voList = $model->field($field)->where($map)->group($group)->order('seq_time desc,checked_goods DESC')->limit($p->firstRow.','.$p->listRows)->select();
            //分页显示
            $page = $p->show();
            //列表排序显示
            $sortImg = $sort; //排序图标
            $sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列' ;//排序提示
            $sort = $sort == 'desc' ? 1 : 0 ;//排序方式

            //模板赋值
            $this->assign('list',$voList);
            $this->assign('sort',$sort);
            $this->assign('order',$order);
            $this->assign('sortImg',$sortImg);
            $this->assign('sortType',$sortAlt);
            $this->assign('page',$page);

            $data = $this->fetch('Echarts:form_ord_stk_mntn_manager');
            echo $data;
        }else{
            echo'当前天暂无数据';
        }
        return;
    }
    /**
     * 维护人有货统计-导出
     * @author jiang
     * @time 2017-03-08 16:33
     */
    function ord_stk_mntn_manager_exp()
    {
        $map =' 1=1';
        $model = M('dm_ord_stk_mntn_manager_d',null,'Mysql_WH');
        $field = "date(seq_time) as dt,
                  mntn_manager_nm,
                  sum(checked_goods) as checked_goods,
                  sum(checked_stk_goods) as checked_stk_goods,
                  case when sum(checked_goods)>0 then round(sum(checked_stk_goods)/sum(checked_goods)*100,2) else 0 end as checked_goods_rate
                ";
        if (I('post.site_tp') !='') {
            $map .=" and site_tp ='". I('post.site_tp')."'";
        }
        if(I('post.check_nm') != '')
        {
            $map .=" and mntn_manager_nm ='". I('post.check_nm')."'";
        }
        $query_date=$this->get_query_date();
        $map .=" and seq_time >='".$query_date['start_date']."'";
        $map .=" and seq_time <='".$query_date['end_date']."'";
        //导出原始数据
        $res = $model->field($field)->where($map)->group('seq_time,mntn_manager_nm')->order('seq_time desc,checked_goods DESC')->select();
        $arr = array('时间','维护人','审核产品数','有货产品数','占比(%)');
        $this->excel_export($res,$arr,'维护人有货统计导出');
    }
    /**
     * 销售毛利润统计(平台)
     * author @chenmin 20170918 10:00
     * modify @chenmin 20180123 10:00 优化
     */
    function display_sale_profit_stat(){
        C('DB_CASE_LOWER',true);
        $model=M('dm_ord_sale_profit_stat_d',null,'Oracle_WH');
        $query_date=$this->get_query_date();
        if(I('post.inquiry_way')=='sum'){
            if(I('post.order')=='month'){
                $dt="to_char(to_date(b.month_key,'yyyy-mm'),'yyyy-mm') as dt,";
                $join=" a join dm_date_td b on a.pay_dt = b.date_key";
                $group='b.month_key';
                $order="b.month_key desc";
            }elseif(I('post.order')=='week'){
                $dt="substr(b.week_key,'1',4)||'-'||substr(week_key,'5',2) as dt,";
                $join=" a join dm_date_td b on a.pay_dt = b.date_key";
                $group="b.week_key";
                $order="b.week_key desc";
            }elseif(I('post.order')=='day'){
                $dt="to_char(to_date(pay_dt,'yyyy-mm-dd'),'yyyy-mm-dd') as dt,";
                $group="pay_dt";
                $order="pay_dt desc";
            }
            $map[$group]=array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']));
            //目前主站点只有platform
            $map['site_tp']='platform';
            if(I('post.site_id')!=''){
                $site_id="max(site_id) as site_id";
                $map['site_id']=I('post.site_id');
            }else{
                $site_id="'汇总' as site_id";
            }
            $field ="$dt 'platform' as site_tp,$site_id,
            count(distinct order_no) as order_no,
            to_char(sum(pay_amt),'fm999999990.90') as pay_amt,
            to_char(sum(cost_amt),'fm999999990.90') as cost_amt,
            to_char(sum(shpp_real_amt),'fm999999990.90') as shpp_real_amt,
            to_char(sum(pay_amt-cost_amt-shpp_real_amt),'fm999999990.90') as profit,
            case when sum(pay_amt)>0 then concat(to_char(sum(pay_amt-cost_amt-shpp_real_amt)/sum(pay_amt)*100,'fm999990.90'),'%') else '0' end as profit_pnt,
            case when count(order_id_src)>0 then to_char(sum(pay_amt-cost_amt-shpp_real_amt)/count(order_id_src),'fm999990.90')else '0' end as order_profit,
            case when count(order_id_src)>0 then to_char(sum(pay_amt)/count(order_id_src),'fm999990.90')else '0' end as ord_avg";
            $title=['日期','主站点','分站点','订单数','销售额','销售成本','运费','利润','平均利润率','平均每单利润','客单价'];
        }elseif(I('post.inquiry_way'=='detail')){
            $map['pay_dt']=array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']));
            $map['site_tp']='platform';
            if(I('post.site_id')!=''){
                $map['site_id']=I('post.site_id');
            }
            $having =" 1=1 ";
            if(I('post.profit_start')!=''){
                $having .= " and sum(pay_amt-cost_amt-shpp_real_amt-commission) >= '".I('post.profit_start')."'";
            }
            if(I('post.profit_end')!=''){
                $having .= " and sum(pay_amt-cost_amt-shpp_real_amt-commission) <= '".I('post.profit_end')."'";
            }
            if(I('post.order_id')!=''){
                $map['order_no']=I('post.order_id');
            }
            $field="order_id_src,order_no,
            shpp_country_nm,shpp_chnl,
            to_char(sum(pay_amt),'fm999999990.90') as pay_amt,
            to_char(sum(pay_amt)*0.08,'fm999999990.90') as commission,
            to_char(sum(real_pay_amt),'fm999999990.90') as real_pay_amt,
            to_char(sum(cost_amt),'fm999999990.90') as cost_amt,
            to_char(sum(shpp_real_amt),'fm999999990.90') as shpp_real_amt,
            to_char(sum(pay_amt-cost_amt-shpp_real_amt)-sum(pay_amt)*0.08,'fm999999990.90') as profit,
            case when sum(pay_amt)>0 then concat(to_char(sum(pay_amt-cost_amt-shpp_real_amt-commission)/sum(pay_amt)*100,'fm999990.90'),'%') else '0' end as profit_pnt,
            to_char(sum(pkg_weight),'fm999990.90') as pkg_weight
            ";
            $group='order_id_src,order_no,shpp_country_nm,shpp_chnl';
            $order='order_id_src desc,order_no desc';
            $title=['编号','订单号','国家','发货渠道','销售额','佣金','实际销售额','销售成本','运费','利润','利润率','重量'];
        }
        $subQuery = $model->field($field)->where($map)->having($having)->join($join)->group($group)->select(false);
        $count = $model->table($subQuery)->count('1');
        if(I('post.is_export')=='1'){
            //导出开始
            $epage='800';
            header("Content-type:application/octet-stream");
            header("Accept-Ranges:bytes");
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename=" . "单品销量跟踪详情导出".".xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            if(!empty($title)){
                foreach($title as $k=>$v){
                    $title[$k]=iconv('UTF-8','GB2312',$v);
                }
                $title=implode("\t",$title);
                echo "$title\n";
            }
            //获取分页数
            $lim=floor($count/$epage);
            if ($lim == 0) {
                $lim = 1;
                $epage = $count;
            }
            for($time = 0;$time <= $lim; $time++) {
                //设置limit限制
                $now = $time*$epage;
                if ($lim - $time < 1) {
                    $epage = $count - $now;
                }
                $limit = "$now,$epage";
                //目标数组
                $data = $model->field($field)->where($map)->having($having)->group($group)->join($join)->order($order)->limit($limit)->select();
                if(!empty($data)){
                    foreach($data as $key=>$val){
                        unset($val['numrow']);
                        foreach ($val as $ck => $cv) {
                            $data[$key][$ck]=iconv("UTF-8", "GB2312", $cv);
                        }
                        $data[$key]=implode("\t", $data[$key]);
                    }
                    echo implode("\n",$data);
                    echo ("\n");
                }
            } die;
        }else{
            if(I('post.type')=='list'){
                if($count[0]['page_count']>0) {
                    import('@.ORG.Util.Page_o');
                    //创建分页对象
                    $listRows = '100';
                    $_POST['_sort'] = 0;
                    $p = new Page($count, $listRows);
                    $list = $model->field($field)->where($map)->having($having)->group($group)->join($join)->order($order)->limit($p->firstRow . ',' . $p->listRows)->select();
                    foreach($list as $k=>$v){
                        unset($list[$k]['numrow']);
                    }
                    foreach($list as $k=>$v){
                        foreach($v as $kk=>$vv){
                            if($kk!='dt' and $kk!='order_id_src' and $kk!='order_no'and $kk!='shpp_country_nm' and $kk!='shpp_chnl' and $kk!='site_tp' and $kk!='site_id' and substr($kk,'-3')!='pnt'){
                                $list[$k][$kk]=number_format($vv,2);
                            }
                        }
                    }
                    if (I('post.order') == 'week') {
                        foreach ($list as $k) {
                            $a = date('m/d', strtotime(substr($k['dt'], 0, -2) . 'W' . substr($k['dt'], -2)));
                            $b = date("m/d", strtotime("$a +6 day"));
                            $k['dt'] = $k['dt'] . '周 <br>(' . $a . '-' . $b . ')';
                            $arr[] = $k;
                        }
                    } else {
                        $arr = $list;
                    }
                    //分页显示
                    $page = $p->show();
                    $this->assign('page', $page);
                    $this->assign('header', $title);
                    $this->assign('list', $arr);
                    $data = $this->fetch('Echarts:form_common_list');
                    echo $data;die;
                }
            }elseif(I('post.type')=='view'){
                //视图
                $field ="$dt
                    count(distinct order_no) as order_no,
                    to_char(sum(pay_amt),'fm999999990.90') as pay_amt,
                    to_char(sum(cost_amt),'fm999999990.90') as cost_amt,
                    to_char(sum(shpp_real_amt),'fm999999990.90') as shpp_real_amt,
                    to_char(sum(pay_amt-cost_amt-shpp_real_amt),'fm999999990.90') as profit,
                    case when sum(pay_amt)>0 then to_char(sum(pay_amt-cost_amt-shpp_real_amt)/sum(pay_amt)*100,'fm999990.90')else '0' end as profit_pnt,
                    case when count(order_id_src)>0 then to_char(sum(pay_amt-cost_amt-shpp_real_amt)/count(order_id_src),'fm999990.90')else '0' end as order_profit,
                    case when count(order_id_src)>0 then to_char(sum(pay_amt)/count(order_id_src),'fm999990.90')else '0' end as ord_avg";
                $data = $model->field($field)->where($map)->group($group)->join($join)->order($order)->select();
                $viewTable = array(
                    'order_no' => '订单数',
                    'pay_amt' => '销售额',
                    'cost_amt' => '销售成本',
                    'shpp_real_amt' => '运费',
                    'profit' => '利润',
                    'profit_pnt' => '平均利润率',
                    'order_profit' => '平均每单利润',
                    'ord_avg' => '客单价'
                );
                foreach($data as $v){
                    foreach($viewTable as $f_k => $f_v){
                        $data_chart[] = array(
                            'dt' => $v['dt'],
                            'field' => $v[$f_k],
                            'type' => $f_v,
                        );
                    }
                }
                $arr = array(
                    'dim1' => 'dt', //特别说明,不是以时间为x轴的不要用dt
                    'dim2' => 'type',
                    'field'=> 'field',
                    'echarts_type'=>'line', //图表类型 (可选｜默认line)
                    'others'=>array( //其他参数设置 （可选｜无默认）
                        'legend'=> array(
                            'selectedMode'=>'single',
                        ),
                        'toolbox'=>array(
                            'show'=>false,
                        )
                    ),
                    'legend_sort'=>'',
                );
                echo $this->echarts3($data_chart,$arr);
            }
        }
    }
    /**
     * 调用接口
     * @atuhor jiang 2017-11-14 10：45
     *
     */
    function get_product($url,$data,$timeoutMs=60000)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HEADER,0);
//        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS,$timeoutMs);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }
    /**
     * 商品中心导出下拉框取值
     * @author jiang 2017-11-14 14：47
     */
    function get_product_center_sel()
    {
        C('DB_CASE_LOWER',true);
        $tab_nm = I('post.tab_nm');     //表名 带前缀
        $c_nm = I('post.c_nm');         //字段名
        $site_tp = I('post.site_tp');   //不需要填 null
        $db_type = I('post.db_type');   // Oracle_WH Mysql_WH
        $model= M($tab_nm,null,$db_type);
        if(I('post.tab_nm') == 'dw_pi_tag_td'){
            $sql = "select distinct $c_nm from $tab_nm where $c_nm is not null and $c_nm != ' ' and type=0 and is_show=1 order by $c_nm";
        }elseif(I('post.tab_nm') == 'dw_pi_grade_td') {
            $sql = "select distinct $c_nm from $tab_nm where $c_nm is not null and $c_nm != ' ' and is_del=0 order by $c_nm";
        }else{
            $sql = "select distinct $c_nm from $tab_nm where $c_nm is not null and $c_nm != ' ' order by $c_nm";
        }
        $tmp = $model->query($sql);
        foreach($tmp as $k){
            $list[] = $k[$c_nm];
        }
        $this->ajaxReturn( array('list'=>$list));
    }
    /**
     * 商品中心数据导出 task:44547
     * @author jiang 2017-11-14 10:38
     */
    function product_center_exp()
    {
        set_time_limit(0);
        $map = array();
        //接口请求路径
//        $url = "http://119.23.9.169:5000/api/get_ProductData";
        $url = "http://35.161.74.182:5000/api/get_ProductData";

        //站点
        if(I('post.site_tp') != 'total'){
            $map['site_tp'] = I('post.site');
        }
        //sku
        if(I('post.sku') != ''){
            $map['sku'] = trim(I('post.sku'));
        }
        //old_sku
        if(I('post.old_sku') != ''){
            $map['old_sku'] = trim(I('post.old_sku'));
        }
        //开发买手
        if(I('post.buyer') != ''){
            $map['buyer'] = trim(I('post.buyer'));
        }
        //供应商编号
        if(I('post.supplier_id') != ''){
            $map['supplier_id'] = trim(I('post.supplier_id'));
        }
        //供应商名称
        if(I('post.supplier_name') != ''){
            $map['supplier_name'] = trim(I('post.supplier_name'));
        }
        //分类
        if(I('post.category_name') != 'total'){
            $map['category_name'] = I('post.category_name');
        }
        //商品名
        if(I('post.item_mywayec_name') != ''){
            $map['item_mywayec_name'] = trim(I('post.item_mywayec_name'));
        }
        //维护序列号
        if(I('post.sequence') != ''){
            $map['sequence'] = trim(I('post.sequence'));
        }
        //是否完成
        if(I('post.is_done') != 'total'){
            $map['is_done'] = I('post.is_done');
        }
        //标签
        if(I('post.tag_name') != 'total'){
            $map['tag_name'] = I('post.tag_name');
        }
        //小组名称
        if(I('post.group_name') != ''){
            $map['group_name'] = trim(I('post.group_name'));
        }
        //关联标识
        if(I('post.spu') != ''){
            $map['spu'] = trim(I('post.spu'));
        }
        //商品定位
        if(I('post.location_id') != 'total'){
            $map['location_name'] = I('post.location_id');
        }
        //商品层次
        if(I('post.grade_id') != 'total'){
            $map['grade_name'] = I('post.grade_id');
        }
        //发货日期 开始日期 结束日期
        if(I('post.start_date') != ''){
            $map['gte'] = strtotime(I('post.start_date'));
        }elseif(I('post.end_date') != ''){
            $map['gte'] = '0000000000';
        }
        if(I('post.end_date') != ''){
            $map['lte'] = strtotime(I('post.end_date'));
        }elseif(I('post.start_date') != ''){
            $map['lte'] = '9999999999';
        }
        //维护人
        if(I('post.manager') != ''){
            $map['manager'] = trim(I('post.manager'));
        }
        //批量搜索
        if(I('post.skus') != ''){
            $map['batch_sku'] = explode("\r\n",trim(I('post.skus')));
        }
        //是否在售
        if(I('post.onsale_flag') != 'total'){
            $map['onsale_flag'] = I('post.onsale_flag');
        }
        $scroll_id = I('post.scroll_id');
        $tmp_arr = array('product_info'=>$map);

        if($scroll_id == ''){
            $result_tmp = array();
            $tmp_arr['size'] = '500';
            $data = json_encode($tmp_arr);
            $return = $this->get_product($url,$data);
            $result = json_decode($return,true);
            $this->count = $result['total_cnt'];
//            print_r($result['total_cnt']);die;
            $result_tmp = json_decode($result['result_data'],true);
            if($result['scroll_id'] != ''){
                $scroll_arr[0] = $result['scroll_id'];
                $this->code_tab = $result['scroll_id'];
                $this->code_export = $result['scroll_id'];
                $title = array('英文title','SKU','成本￥','成本$','成本(欧元)','成本(英镑)','供应商编号','供应商名称','维护序列号','开发买手','维护买手','版费情况','小组名称','层次','定位','重量','重量计价($)','维护人','主图图片链接','关联标识');
                header("Content-type:application/octet-stream");
                header("Accept-Ranges:bytes");
                header("Content-type:application/vnd.ms-excel");
                header("Content-Disposition:attachment;filename=商品中心数据导出.xls");
                header("Pragma: no-cache");
                header("Expires: 0");
                if (!empty($title)){
                    foreach ($title as $k => $v) {
                        $title[$k]=iconv("UTF-8", "UTF-8",$v);
                    }
                    $title= implode("\t", $title);
                    echo "$title\n";
                }
                while($this->code_export != null){
                    $arr['scroll_id'] = $this->code_export;
                    $data_d = json_encode($arr);
                    $return_d = $this->get_product($url,$data_d);
                    $aa = json_decode($return_d,true);
                    $this->code_export = $aa['scroll_id'];
                    $bb = json_decode($aa['result_data'],true);
                    if(empty($bb)){
                        $result_t = array_merge($result_tmp,array());
                    }else{
                        $result_t = array_merge($result_tmp,json_decode($aa['result_data'],true));
                    }
                    $result_tmp = array();
                    $resultdata = array();
                    foreach ($result_t as $k=>$v){
                        $resultdata[$k]['item_mywayec_name'] = $v['item_mywayec_name'];
                        $resultdata[$k]['SKU'] = $v['SKU'];
                        $resultdata[$k]['cost'] = $v['cost'];
                        $resultdata[$k]['us_cost'] = $v['us_cost'];
                        $resultdata[$k]['eur_cost'] = $v['eur_cost'];
                        $resultdata[$k]['uk_cost'] = $v['uk_cost'];
                        $resultdata[$k]['supplier_id'] = $v['supplier_id'];
                        $resultdata[$k]['supplier_name'] = $v['supplier_name'];
                        $resultdata[$k]['sequence'] = $v['sequence'];
                        $resultdata[$k]['buyer'] = $v['buyer'];
                        $resultdata[$k]['maintain_buyer'] = $v['maintain_buyer'];
                        $resultdata[$k]['is_free'] = $v['is_free']=0?'付费':'免费';
                        $resultdata[$k]['group_name'] = $v['group_name'];
                        $resultdata[$k]['grade_name'] = $v['grade_name'];
                        $resultdata[$k]['location_name'] = $v['location_name'];
                        $resultdata[$k]['weight'] = $v['weight'];
                        $resultdata[$k]['freight'] = $v['freight'];
                        $resultdata[$k]['manager'] = $v['manager'];
                        $resultdata[$k]['image_url'] = $v['image_url'];
                        $resultdata[$k]['spu'] = $v['spu'];
                    }
                    if (!empty($resultdata)){
                        foreach($resultdata as $key=>$val){
                            foreach ($val as $ck => $cv) {
                                $resultdata[$key][$ck]=iconv("UTF-8", "UTF-8", $cv);
                            }
                            $resultdata[$key]=implode("\t", $resultdata[$key]);
                        }
                        echo implode("\n",$resultdata);
                        echo ("\n");
                    }
                }
            }else{
                echo "没有请求到接口数据,请重试！";die;
            }
        }
    }

    /*
     * 复购率月度监控
     * author @hiro 2017-11-17 14:20:22
     */
    public function statistic_multibuy_montor_monthly(){
        $query_date = $this->get_query_date();
        $map['static_month'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
        if(!empty(I('post.site_tp'))){
            $map['site_tp'] = I('post.site_tp');
        }
        if(!empty(I('post.country_nm'))){
            $map['shpp_country_nm'] = I('post.country_nm');
        }else{
            $map['shpp_country_nm'] = "TOTAL";
        }
        $model = M('dm_ord_reorder_by_month',null,'Mysql_WH');
        $field = "date_format(concat(static_month,'00'),'%Y-%m') as dt,sum(user_cnt) as user_cnt,sum(order_cnt) as order_cnt,
        case when sum(user_cnt)>0 then round(sum(user_cnt_2+user_cnt_3+user_cnt_3_plus)/sum(user_cnt)*100,2) else 0 end as user_multibuy_pnt,
        case when sum(order_cnt)>0 then round(sum(order_cnt_2+order_cnt_3+order_cnt_3_plus)/sum(order_cnt)*100,2) else 0 end as order_multibuy_pnt,
        case when sum(user_cnt_new+user_cnt_old)>0 then round(sum(user_cnt_old)/sum(user_cnt_new+user_cnt_old)*100,2) else 0 end as user_old_pnt
        ";
        $voList = $model->field($field)->where($map)->group('static_month')->order('static_month asc')->select();

        $dt = $model->table('dm_date_td')->field("date_format(concat(static_month,'00'),'%Y-%m') as dt")
            ->where(array('static_month' =>array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and')))
            ->group('static_month') ->order('static_month asc')->select();
        foreach ($dt as $k){
            $date_assign[] = $k['dt'];
            $fin_data['用户数'][$k['dt']] = 0;
            $fin_data['订单数'][$k['dt']] = 0;
            $fin_data['用户复购率'][$k['dt']] = 0;
            $fin_data['订单复购率'][$k['dt']] = 0;
            $fin_data['用户回购率'][$k['dt']] = 0;
        }
        foreach ($voList as $k){
            $fin_data['用户数'][$k['dt']] = intval($k['user_cnt']);
            $fin_data['订单数'][$k['dt']] = intval($k['order_cnt']);
            $fin_data['用户复购率'][$k['dt']] = $k['user_multibuy_pnt']+0;
            $fin_data['订单复购率'][$k['dt']] = $k['order_multibuy_pnt']+0;
            $fin_data['用户回购率'][$k['dt']] = $k['user_old_pnt']+0;
        }
        $this-> xaxis = json_encode($date_assign);
        $lengend = array('用户数','订单数','用户复购率','订单复购率','用户回购率');
        $this-> legend = json_encode($lengend);
        $this-> voL = json_encode($fin_data);
        $data=$this->fetch('Echarts:echarts_multibuy_monitor');
        echo $data;
    }

    public function tab_multibuy_monitor_monthly(){
        $query_date = $this->get_query_date();
        $map['static_month'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
        if(!empty(I('post.site_tp'))){
            $map['site_tp'] = I('post.site_tp');
        }
        if(!empty(I('post.country_nm'))){
            $map['shpp_country_nm'] = I('post.country_nm');
        }else{
            $map['shpp_country_nm'] = "TOTAL";
        }
        $model = M('dm_ord_reorder_by_month',null,'Mysql_WH');
        $field = "left(static_month,4) as year,right(static_month,2) as month,
        sum(user_cnt) as user_cnt,
        sum(user_cnt_1) as user_cnt_1,
        sum(user_cnt_2) as user_cnt_2,
        sum(user_cnt_3) as user_cnt_3,
        sum(user_cnt_3_plus) as user_cnt_3_plus,
        case when sum(user_cnt)>0 then 
        concat(round(sum(user_cnt_2+user_cnt_3+user_cnt_3_plus)/sum(user_cnt)*100,2),'%') else '0.00%' end as user_multibuy_pnt,
        sum(user_cnt_new) as user_cnt_new,
        sum(user_cnt_old) as user_cnt_old,
        case when sum(user_cnt_new+user_cnt_old)>0 then
        concat(round(sum(user_cnt_old)/sum(user_cnt_new+user_cnt_old)*100,2),'%') else '0.00%' end as user_old_pnt,
        sum(order_cnt) as order_cnt,
        sum(order_cnt_1) as order_cnt_1,
        sum(order_cnt_2) as order_cnt_2,
        sum(order_cnt_3) as order_cnt_3,
        sum(order_cnt_3_plus) as order_cnt_3_plus,
        case when sum(order_cnt)>0 then 
        concat(round(sum(order_cnt_2+order_cnt_3+order_cnt_3_plus)/sum(order_cnt)*100,2),'%') else '0.00%' end as order_multibuy_pnt
        ";
        $voList = $model->field($field)->where($map)->group('static_month')->order('year desc,month desc')->select();

        $this->assign('list',$voList);
        $data=$this->fetch('Echarts:form_multibuy_monitor');
        echo $data;exit;

    }
    /**
     * 获取查询时间区间
     * @authro jiang 2017-11-23 18:45
     */
    public function get_query_forecast_date(){
        if( I('post.order')=='month'){
            $start_date = ('' == I('post.start_date') ) ? date("Y-m-d",mktime(0, 0, 0, date("m")-13, 1, date("Y"))) : I('post.start_date');
            $end_date = ('' == I('post.end_date') ) ? date("Y-m-d",mktime(0, 0, 0, date("m"), 1, date("Y"))) : I('post.end_date');

            $start_date = date('Ym', strtotime($start_date));
            $end_date = date('Ym', strtotime($end_date));

            return $query_date = array('start_date' => $start_date, 'end_date' => $end_date);
        }else if(I('post.order')=='week'){
            $start_date = ('' == I('post.start_date') ) ? date("Y-m-d",strtotime('-1 Sat',mktime(0, 0, 0, date("m")-3, 1, date("Y")))) : I('post.start_date');
            $end_date = ('' == I('post.end_date') ) ? date("Y-m-d",strtotime("this friday")) : I('post.end_date');
            $week = date('w', strtotime($start_date));
            if($week == 0){
                $i = strtotime($start_date)+2*86400;
            }elseif($week == 5){
                $i=strtotime($start_date)+86400;
            }else{
                $i = strtotime($start_date);
            }
            //结束时间也改成跟开始时间类似。如，当结束时间是2017年1月1号(周日)时，结果显示201752周，实际应该是2016年52周
            $week_end = date('w', strtotime($end_date));
            if($week_end == 6){
                $i_end = strtotime($end_date)+2*86400;
            }elseif($week_end == 0){
                $i_end=strtotime($end_date)+86400;
            }else{
                $i_end = strtotime($end_date);
            }
            $start_date=date('Y',$i).date('W',$i);
            $end_date=date('Y',$i_end).date('W',$i_end);
//            $end_date = date('Y',strtotime($end_date)).date('W', strtotime($end_date));

            return $query_date = array('start_date' => $start_date, 'end_date' => $end_date);
        }else{
            $start_date = ('' == I('post.start_date') ) ? date("Y-m-d",mktime(0, 0, 0, date("m"), date("d")-30, date("Y"))) : I('post.start_date');
            $end_date = ('' == I('post.end_date') ) ? date("Y-m-d",time()) : I('post.end_date');

            $start_date = date('Ymd', strtotime($start_date));
            $end_date = date('Ymd', strtotime($end_date));

            return $query_date = array('start_date' => $start_date, 'end_date' => $end_date);
        }
    }
    /**
     * 销售预估 task：45270
     * @author jiang 2017-11-23 10:33
     */
    function sales_forecast()
    {
        if(I('post.is_export') == '1'){
            $site_arr = explode(',',I('post.site_tp'));
            $market_arr = explode(',',I('post.market'));
            $country_arr = explode(',',I('post.country_nm'));
            $type_arr = explode(',',I('post.type'));
            $trmnl_tmp_arr = explode(',',I('post.trmnl_tp'));
        }else{
            $site_arr = I('post.site_tp');
            $market_arr = I('post.market');
            $country_arr = I('post.country_nm');
            $type_arr = I('post.type');
            $trmnl_tmp_arr = I('post.trmnl_tp');
        }
        foreach ($trmnl_tmp_arr as $v){
            $trmnl_arr[$v] = $v;
        }
        $query_date = $this->get_query_forecast_date();
        //站点
        if($site_arr != ''){
            if(in_array('total',$site_arr)){
                unset($site_arr[0]);
                if(!empty($site_arr)){
                    $map['a.site_tp'] = array('IN',$site_arr);
                }
                $site_tp = "'汇总'";
            }else{
                if(in_array('detail',$site_arr)){
                    unset($site_arr[0]);
                    if(!empty($site_arr)){
                        $map['a.site_tp'] = array('IN',$site_arr);
                    }
                }else{
                    $map['a.site_tp'] = array('IN',$site_arr);
                }
                $site_tp = "a.site_tp";
                $group_arr['a.site_tp'] = "a.site_tp";
            }
        }
        //市场
        if($market_arr != ''){
            if(in_array('total',$market_arr)){
                unset($market_arr[0]);
                if(!empty($market_arr)){
                    $map['a.market'] = array('IN',$market_arr);
                }
                $market = "'汇总'";
                //目标按月
                $market_target_m = "'汇总'";
            }else{
                if(in_array('detail',$market_arr)){
                    unset($market_arr[0]);
                    if(!empty($market_arr)){
                        $map['a.market'] = array('IN',$market_arr);
                    }
                }else{
                    $map['a.market'] = array('IN',$market_arr);
                }
                $market = "a.market";
                //目标按月
                $market_target_m = "c.market";
                $group_arr['a.market'] = "a.market";
                $order_add = ',market asc';
            }
        }
        //国家
        if($country_arr != ''){
            if(in_array('total',$country_arr)){
                unset($country_arr[0]);
                if(!empty($country_arr)){
                    $map['a.short_nm'] = array('IN',$country_arr);
                }
                $country_nm = "'汇总'";
            }else{
                if(in_array('detail',$country_arr)){
                    unset($country_arr[0]);
                    if(!empty($country_arr)){
                        $map['a.short_nm'] = array('IN',$country_arr);
                    }
                }else{
                    $map['a.short_nm'] = array('IN',$country_arr);
                }
                $country_nm = "a.country_nm";
                $group_arr['a.country_nm'] = "a.country_nm";
                $order_add = ',country_nm asc';
            }
        }
        $map_reckon_month = array();
        $map_reckon_month= $map;
        $group_reckon_arr = $group_arr;
        //设备
        if($trmnl_arr != ''){
            if(in_array('total',$trmnl_arr)) {
                unset($trmnl_arr['total']);
                if(!empty($trmnl_arr)){
                    $map['a.trmnl_tp'] = array('IN',$trmnl_arr);
                }
                $trmnl_tp = "'汇总'";
            }else{
                if(in_array('detail',$trmnl_arr)){
                    unset($trmnl_arr['detail']);
                }
                if(!empty($trmnl_arr)){
                    if(in_array('app',$trmnl_arr)){
                        $trmnl_wh_arr_tmp[] = 'ios';
                        $trmnl_wh_arr_tmp[] = 'android';
                        unset($trmnl_arr['app']);
                    }
                    if(in_array('web',$trmnl_arr)){
                        $trmnl_wh_arr_tmp[] = 'M';
                        $trmnl_wh_arr_tmp[] = 'PC';
                        unset($trmnl_arr['web']);
                    }
                    if(!empty($trmnl_arr)){
                        foreach ($trmnl_arr as $v){
                            $trmnl_wh_arr_tmp[] = $v;
                        }
                    }
                    $trmnl_wh_arr = array_unique($trmnl_wh_arr_tmp);
                    $map['a.trmnl_tp'] = array('IN',$trmnl_wh_arr);
                }
                $group_arr['a.trmnl_tp'] = "a.trmnl_tp";
                $trmnl_tp = "a.trmnl_tp";
            }
        }
        if( I('post.order')=='month' ){
            $map['a.static_month'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
            $map_reckon_month['a.static_month'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
            $map_upload_real = $map;
            $map_upload_target = $map;
            $map_upload_target['c.short_nm'] = $map_upload_real['a.short_nm'];
            unset($map_upload_real['a.short_nm']);
            unset($map_upload_real['a.market']);
            unset($map_upload_target['a.short_nm']);
            unset($map_upload_target['a.market']);
            if(!empty($group_arr)){
                $group = implode(',',$group_arr);
                $group = "a.static_month,".$group;
                $group_target_m = str_replace("a.market","c.market",$group);
            }else{
                $group = "a.static_month";
            }
            if(!empty($group_reckon_arr)){
                $group_reckon = implode(',',$group_reckon_arr);
                $group_reckon = "a.static_month,".$group_reckon;
            }else{
                $group_reckon = "a.static_month";
            }
            $model_real = M('pub_sale_analysis_m a', 'dm_', 'Mysql_WH');
            $model_reckon = M('ord_pre_old_ord_m a', 'dm_', 'Mysql_WH');
            $model_target = M('pub_sale_forecast_target_m a', 'dm_', 'Mysql_WH');
            //实际按月
            $field_real="concat(substr(a.static_month,'1',4),'-',substr(a.static_month,'5',2)) as dt,
                    {$site_tp} as site_tp,
                    {$market} as market,
                    {$country_nm} as country_nm,
                    {$trmnl_tp} as trmnl_tp,
                    '实际' as type,
                    round(sum(a.supplier_pay_amt),0) as supplier_pay_amt,
                    round(sum(a.pay_amt),0) as pay_amt,
                    round(sum(a.rfnd_out_pay_amt),0) as rfnd_out_pay_amt,
                    round(sum(a.rfnd_cod_out_pay_amt),0) as rfnd_cod_out_pay_amt,
                    round(sum(a.supplier_order_cnt),0) as supplier_order_cnt,
                    sum(a.supplier_old_user_ord_cnt) as supplier_old_user_ord_cnt,
                    sum(a.supplier_new_user_ord_cnt) as supplier_new_user_ord_cnt,
                    case when sum(a.supplier_order_cnt)>0 then round(sum(a.supplier_old_user_ord_cnt)/sum(a.supplier_order_cnt)*100,2) else 0 end as old_user_rate,
                    case when sum(a.supplier_new_user_ord_cnt)>0 then round(sum(b.market_cost)/sum(a.supplier_new_user_ord_cnt),2) else 0 end as new_user_cost,
                    sum(a.rgst_cnt) as rgst_cnt,
                    case when sum(a.rgst_cnt)>0 then round(sum(a.supplier_new_user_ord_cnt)/sum(a.rgst_cnt)*100,2) else 0 end as rgst_conversion_rate,
                    case when sum(a.rgst_cnt)>0 then round(sum(b.market_cost)/sum(a.rgst_cnt),2) else 0 end as rgst_cost,
                    sum(b.app_downloads) as app_downloads,
                    case when sum(b.app_downloads)>0 then round(sum(a.supplier_new_user_ord_cnt)/sum(b.app_downloads)*100,2) else 0 end as download_conversion,
                    case when sum(b.app_downloads)>0 then round(sum(b.market_cost)/sum(b.app_downloads),2) else 0 end as download_cost,
                    round(sum(b.market_cost),2) as market_cost,
                    case when sum(a.supplier_pay_amt)>0 then round(sum(b.market_cost)/sum(a.supplier_pay_amt)*100,2) else 0 end as market_rate,
                    case when sum(a.supplier_order_cnt)>0 then round(sum(a.supplier_pay_amt)/sum(a.supplier_order_cnt),2) else 0 end as customer_price,
                    avg(b.one_year_purchase) as one_year_purchase,
                    round(avg(b.one_year_purchase_amount),2) as one_year_purchase_amount,
                    case when sum(b.one_year_purchase_amount)>0 then round((case when sum(a.supplier_new_user_ord_cnt)>0 then round(sum(b.market_cost)/sum(a.supplier_new_user_ord_cnt),2) else 0 end)/sum(b.one_year_purchase_amount)*100,2) else 0 end as cpa_rate,
                    '1' as sort";
            //按月实际上传数据取数据
            $file_upload = "substr(dt,'1',6) as static_month,site_tp,country_nm,trmnl_tp,sum(app_downloads) as app_downloads,
                sum(market_cost) as market_cost,avg(one_year_purchase) as one_year_purchase,avg(one_year_purchase_amount) as one_year_purchase_amount";
            //预估按月
            $field_reckon="concat(substr(a.static_month,'1',4),'-',substr(a.static_month,'5',2)) as dt,
                    {$site_tp} as site_tp,
                    {$market} as market,
                    {$country_nm} as country_nm,
                    '汇总' as trmnl_tp,
                    '预估' as type,
                    round((sum(a.pre_ord_cnt)+sum(b.new_user_ord_cnt))*avg(a.guest_unit_price),0) as supplier_pay_amt,                 
                    '-' as pay_amt,
                    '-' as rfnd_out_pay_amt,
                    '-' as rfnd_cod_out_pay_amt,                    
                    round((sum(a.pre_ord_cnt)+sum(b.new_user_ord_cnt)),0) as supplier_order_cnt,                  
                    sum(a.pre_ord_cnt) as supplier_old_user_ord_cnt,
                    sum(b.new_user_ord_cnt) as supplier_new_user_ord_cnt,
                    case when (sum(a.pre_ord_cnt)+sum(b.new_user_ord_cnt))>0 then round(sum(a.pre_ord_cnt)/(sum(a.pre_ord_cnt)+sum(b.new_user_ord_cnt))*100,2) else 0 end as old_user_rate,
                    round(avg(b.new_user_cost),2) as new_user_cost,
                    '-' as rgst_cnt,
                    '-' as rgst_conversion_rate,
                    '-' as rgst_cost,
                    '-' as app_downloads,
                    '-' as download_conversion,
                    '-' as download_cost,
                    round(sum(b.new_user_ord_cnt)*avg(b.new_user_cost),2) as market_cost,
                    round((sum(b.new_user_ord_cnt)*avg(b.new_user_cost))/((sum(a.pre_ord_cnt)+sum(b.new_user_ord_cnt))*avg(a.guest_unit_price))*100,2) as market_rate,
                    round(avg(a.guest_unit_price),2) as customer_price,
                    round(avg(b.one_year_purchase),1) as one_year_purchase,
                    round(avg(b.one_year_purchase_amount),2) as one_year_purchase_amount,
                    case when (sum(b.one_year_purchase_amount)>0 and sum(b.one_year_purchase_amount) is not null) then round(avg(b.new_user_cost)/sum(b.one_year_purchase_amount),2) else 0 end as cpa_rate,
                    '2' as sort 
                ";
            //目标按月
            $field_target="concat(substr(a.static_month,'1',4),'-',substr(a.static_month,'5',2)) as dt,
                    {$site_tp} as site_tp,
                    {$market_target_m} as market,
                    {$country_nm} as country_nm,
                    {$trmnl_tp} as trmnl_tp,
                    '目标' as type,
                    round(sum(a.supplier_pay_amt),0) as supplier_pay_amt,
                    '-' as pay_amt,
                    '-' as rfnd_out_pay_amt,
                    '-' as rfnd_cod_out_pay_amt,
                    round(sum(a.supplier_order_cnt),0) as supplier_order_cnt,
                    sum(a.old_user_ord_cnt) as supplier_old_user_ord_cnt,
                    sum(a.new_user_ord_cnt) as supplier_new_user_ord_cnt,
                    round(sum(a.old_user_rate),2) as old_user_rate,
                    round(sum(a.new_user_cost),2) as new_user_cost,
                    sum(a.rgst_cnt) as rgst_cnt,
                    round(sum(a.rgst_conversion_rate)*100,2) as rgst_conversion_rate,
                    sum(a.rgst_cost) as rgst_cost,
                    sum(a.app_downloads) as app_downloads,
                    round(sum(a.download_conversion)*100,2) as download_conversion,
                    round(sum(a.download_cost),2) as download_cost,
                    round(sum(a.market_cost),2) as market_cost,
                    round(sum(a.market_rate),2) as market_rate,
                    round(sum(a.customer_price),2) as customer_price,
                    avg(b.one_year_purchase) as one_year_purchase,
                    round(avg(b.one_year_purchase_amount),2) as one_year_purchase_amount,
                    round(sum(a.cpa_rate),2) as cpa_rate,
                    '3' as sort";
            $model_upload_real = M('pub_sale_forecast_real_d a', 'dm_', 'Mysql_WH');
            $upload_real_sql = $model_upload_real->field($file_upload)->where($map_upload_real)->group("substr(static_month,'1',6),site_tp,country_nm,trmnl_tp")->select(false);

            $join_t = "left join {$upload_real_sql} b ON a.static_month = b.static_month and lower(a.site_tp)=lower(b.site_tp) and lower(a.short_nm)=lower(b.country_nm) and lower(a.trmnl_tp)=lower(b.trmnl_tp)";
            $join_reckon = "left join dm_pub_sale_forecast_reckon_m b ON a.static_month = b.dt and lower(a.site_tp)=lower(b.site_tp) and lower(a.short_nm)=lower(b.country_nm)";
            $join_target = "left join dm_pub_sale_forecast_reckon_m b ON a.static_month = b.dt and lower(a.site_tp)=lower(b.site_tp) and lower(a.country_nm)=lower(b.country_nm)
                            left join dw_pub_site_mkt_country_td c ON lower(a.country_nm)=lower(c.short_nm)";
            $union_arr = array();
            if(in_array('total',$type_arr) or empty($type_arr)){
                $union_arr[] = $model_real->field($field_real)->where($map)->group($group)->join($join_t)->select(false);
                $union_arr[] = $model_reckon->field($field_reckon)->where($map_reckon_month)->join($join_reckon)->group($group_reckon)->select(false);
                $union_arr[] = $model_target->field($field_target)->where($map_upload_target)->group($group_target_m)->join($join_target)->select(false);
            }else{
                if(in_array('实际',$type_arr)){
                    $real_sql = $model_real->field($field_real)->where($map)->group($group)->join($join_t)->select(false);
                    $union_arr[] = $real_sql;
                }
                if(in_array('预估',$type_arr)) {
                    $reckon_sql = $model_reckon->field($field_reckon)->where($map_reckon_month)->join($join_reckon)->group($group_reckon)->select(false);
                    $union_arr[] = $reckon_sql;
                }
                if(in_array('目标',$type_arr)) {
                    $target_sql = $model_target->field($field_target)->where($map_upload_target)->group($group_target_m)->join($join_target)->select(false);
                    $union_arr[] = $target_sql;
                }
            }

        } else if ( I('post.order')=='week' ) {
            $map['a.static_week'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
            if(!empty($group_arr)){
                $group = implode(',',$group_arr);
                $group = "a.static_week,".$group;
            }else{
                $group = "a.static_week";
            }
            $map_target = $map;
            if($map['a.short_nm'] != ''){
                $map_target['a.country_nm'] = $map['a.short_nm'];
                unset($map_target['a.short_nm']);
            }
            //目标按周字段特殊处理
            if($map_target['a.market'] != ''){
                $map_target['c.market'] = $map_target['a.market'];
                unset($map_target['a.market']);
            }

            $group_target_arr = $group_arr;
            if($market_target_m != "'汇总'"){
                unset($group_target_arr['a.market']);
                $group_target_arr['c.market'] = 'c.market';
            }
            if($country_nm != "'汇总'"){
                $country_nm_target = "d.country_nm";
                unset($group_target_arr['a.country_nm']);
                $group_target_arr['d.country_nm'] = 'd.country_nm';
            }else{
                $country_nm_target = "'汇总'";
            }
            $group_target = implode(',',$group_target_arr);
            $group_target = "a.static_week,".$group_target;
            $model_real = M('pub_sale_analysis_w a', 'dm_', 'Mysql_WH');
            $model_reckon = M('pub_sale_pre_w a', 'dm_', 'Mysql_WH');
            $model_target = M('pub_sale_forecast_target_d a', 'dm_', 'Mysql_WH');
            $field_real="concat(substr(a.static_week,'1',4),'-',substr(a.static_week,'5',2)) as dt,
                    {$site_tp} as site_tp,
                    {$market} as market,
                    {$country_nm} as country_nm,
                    {$trmnl_tp} as trmnl_tp,
                    '实际' as type,
                    round(sum(a.supplier_pay_amt),0) as supplier_pay_amt,
                    round(sum(a.pay_amt),0) as pay_amt,
                    round(sum(a.rfnd_out_pay_amt),0) as rfnd_out_pay_amt,
                    round(sum(a.rfnd_cod_out_pay_amt),0) as rfnd_cod_out_pay_amt,
                    round(sum(a.supplier_order_cnt),0) as supplier_order_cnt,
                    sum(a.supplier_old_user_ord_cnt) as supplier_old_user_ord_cnt,
                    sum(a.supplier_new_user_ord_cnt) as supplier_new_user_ord_cnt,
                    case when sum(a.supplier_order_cnt)>0 then round(sum(a.supplier_old_user_ord_cnt)/sum(a.supplier_order_cnt)*100,2) else 0 end as old_user_rate,
                    case when sum(a.supplier_new_user_ord_cnt)>0 then round(sum(b.market_cost)/sum(a.supplier_new_user_ord_cnt),2) else 0 end as new_user_cost,
                    sum(a.rgst_cnt) as rgst_cnt,
                    case when sum(a.rgst_cnt)>0 then round(sum(a.supplier_new_user_ord_cnt)/sum(a.rgst_cnt)*100,2) else 0 end as rgst_conversion_rate,
                    case when sum(a.rgst_cnt)>0 then round(sum(b.market_cost)/sum(a.rgst_cnt),2) else 0 end as rgst_cost,
                    sum(b.app_downloads) as app_downloads,
                    case when sum(b.app_downloads)>0 then round(sum(a.supplier_new_user_ord_cnt)/sum(b.app_downloads)*100,2) else 0 end as download_conversion,
                    case when sum(b.app_downloads)>0 then round(sum(b.market_cost)/sum(b.app_downloads),2) else 0 end as download_cost,
                    round(sum(b.market_cost),2) as market_cost,
                    case when sum(a.supplier_pay_amt)>0 then round(sum(b.market_cost)/sum(a.supplier_pay_amt)*100,2) else 0 end as market_rate,
                    case when sum(a.supplier_order_cnt)>0 then round(sum(a.supplier_pay_amt)/sum(a.supplier_order_cnt),2) else 0 end as customer_price,
                    avg(b.one_year_purchase) as one_year_purchase,
                    round(avg(b.one_year_purchase_amount),2) as one_year_purchase_amount,
                    case when sum(b.one_year_purchase_amount)>0 then round((case when sum(a.supplier_new_user_ord_cnt)>0 then round(sum(b.market_cost)/sum(a.supplier_new_user_ord_cnt),2) else 0 end)/sum(b.one_year_purchase_amount)*100,2) else 0 end as cpa_rate,
                    '1' as sort";
            $field_reckon="concat(substr(a.static_week,'1',4),'-',substr(a.static_week,'5',2)) as dt,
                    {$site_tp} as site_tp,
                    {$market} as market,
                    {$country_nm} as country_nm,
                    {$trmnl_tp} as trmnl_tp,
                    '预估' as type,                    
                    round(sum(a.pre_supplier_pay_amt),0) as supplier_pay_amt,                    
                    '-' as pay_amt,
                    '-' as rfnd_out_pay_amt,
                    '-' as rfnd_cod_out_pay_amt,                    
                    round(sum(a.pre_supplier_order_cnt),0) as supplier_order_cnt,                    
                    '-' as supplier_old_user_ord_cnt,
                    '-' as supplier_new_user_ord_cnt,
                    '-' as old_user_rate,
                    '-' as new_user_cost,
                    '-' as rgst_cnt,
                    '-' as rgst_conversion_rate,
                    '-' as rgst_cost,
                    '-' as app_downloads,
                    '-' as download_conversion,
                    '-' as download_cost,
                    '-' as market_cost,
                    '-' as market_rate,
                    '-' as customer_price,
                    '-' as one_year_purchase,
                    '-' as one_year_purchase_amount,
                    '-' as cpa_rate,
                    '2' as sort 
                ";
            //目标按周
            $field_target="concat(substr(a.static_week,'1',4),'-',substr(a.static_week,'5',2)) as dt,
                    {$site_tp} as site_tp,
                    {$market_target_m} as market,
                    {$country_nm_target} as country_nm,
                    {$trmnl_tp} as trmnl_tp,
                    '目标' as type,
                    round(sum(a.supplier_pay_amt),0) as supplier_pay_amt,
                    '-' as pay_amt,
                    '-' as rfnd_out_pay_amt,
                    '-' as rfnd_cod_out_pay_amt,
                    round(sum(a.supplier_order_cnt),0) as supplier_order_cnt,
                    sum(a.old_user_ord_cnt) as supplier_old_user_ord_cnt,
                    sum(a.new_user_ord_cnt) as supplier_new_user_ord_cnt,
                    round(sum(a.old_user_rate),2) as old_user_rate,
                    round(sum(a.new_user_cost),2) as new_user_cost,
                    sum(a.rgst_cnt) as rgst_cnt,
                    round(sum(a.rgst_conversion_rate)*100,2) as rgst_conversion_rate,
                    sum(a.rgst_cost) as rgst_cost,
                    sum(a.app_downloads) as app_downloads,
                    round(sum(a.download_conversion)*100,2) as download_conversion,
                    round(sum(a.download_cost),2) as download_cost,
                    round(sum(a.market_cost),2) as market_cost,
                    round(sum(a.market_rate),2) as market_rate,
                    round(sum(a.customer_price),2) as customer_price,
                    avg(b.one_year_purchase) as one_year_purchase,
                    round(avg(b.one_year_purchase_amount),2) as one_year_purchase_amount,
                    round(sum(a.cpa_rate),2) as cpa_rate,
                    '3' as sort";
            $join_t = "left join dm_pub_sale_forecast_real_d b ON a.static_week = b.static_week and lower(a.site_tp)=lower(b.site_tp) and lower(a.short_nm)=lower(b.country_nm) and lower(a.trmnl_tp)=lower(b.trmnl_tp)";
            $join_target = "left join dm_pub_sale_forecast_real_d b ON a.static_week = b.static_week and lower(a.site_tp)=lower(b.site_tp) and lower(a.country_nm)=lower(b.country_nm) and lower(a.trmnl_tp)=lower(b.trmnl_tp)
                    left join dw_pub_site_mkt_country_td c ON lower(a.country_nm)=lower(c.short_nm)
                    left join dw_pub_country_td d ON lower(a.country_nm)=lower(d.short_nm)";
            $union_arr = array();
            if(in_array('total',$type_arr) or empty($type_arr)){
                $union_arr[] = $model_real->field($field_real)->where($map)->group($group)->join($join_t)->select(false);
                $union_arr[] = $model_reckon->field($field_reckon)->where($map)->group($group)->select(false);
//                $union_arr[] = $model_target->field($field_target)->where($map_target)->group($group)->join($join_target)->select(false);
                $union_arr[] = $model_target->field($field_target)->where($map_target)->group($group_target)->join($join_target)->select(false);
            }else{
                if(in_array('实际',$type_arr)){
                    $real_sql = $model_real->field($field_real)->where($map)->group($group)->join($join_t)->select(false);
                    $union_arr[] = $real_sql;
                }
                if(in_array('预估',$type_arr)) {
                    $reckon_sql = $model_reckon->field($field_reckon)->where($map)->group($group)->select(false);
                    $union_arr[] = $reckon_sql;
                }
                if(in_array('目标',$type_arr)) {
                    $target_sql = $model_target->field($field_target)->where($map_target)->group($group_target)->join($join_target)->select(false);
                    $union_arr[] = $target_sql;
                }
            }
        } else {
            $map_reckon = array();
            $map_reckon = $map;
            $map_target = $map;
            $map_target['a.dt'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
            $map_reckon['a.pre_day'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
            $map['a.pay_dt'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');


            if($map['a.short_nm'] != ''){
                $map_target['a.country_nm'] = $map['a.short_nm'];
                unset($map_target['a.short_nm']);
            }
            if(!empty($group_arr)){
                $group = implode(',',$group_arr);
                $group_reckon = "a.pre_day,".$group;
                $group = "a.pay_dt,".$group;
            }else{
                $group = "a.pay_dt";
                $group_reckon = "a.pre_day";
            }
            //目标按天字段特殊处理
            if($map_target['a.market'] != ''){
                $map_target['c.market'] = $map_target['a.market'];
                unset($map_target['a.market']);
            }

            $group_target_arr = $group_arr;
            if($market_target_m != "'汇总'"){
                unset($group_target_arr['a.market']);
                $group_target_arr['c.market'] = 'c.market';
            }
            if($country_nm != "'汇总'"){
                $country_nm_target = "d.country_nm";
                unset($group_target_arr['a.country_nm']);
                $group_target_arr['d.country_nm'] = 'd.country_nm';
            }else{
                $country_nm_target = "'汇总'";
            }
            $group_target = implode(',',$group_target_arr);
            $group_target = "a.dt,".$group_target;
            $model_real = M('pub_sale_analysis_d a', 'dm_', 'Mysql_WH');
            $model_reckon = M('pub_sale_pre_d a', 'dm_', 'Mysql_WH');
            $model_target = M('pub_sale_forecast_target_d a', 'dm_', 'Mysql_WH');
            $field_real="date(a.pay_dt) as dt,
                    {$site_tp} as site_tp,
                    {$market} as market,
                    {$country_nm} as country_nm,
                    {$trmnl_tp} as trmnl_tp,
                    '实际' as type,
                    round(sum(a.supplier_pay_amt),0) as supplier_pay_amt,
                    round(sum(a.pay_amt),0) as pay_amt,
                    round(sum(a.rfnd_out_pay_amt),0) as rfnd_out_pay_amt,
                    round(sum(a.rfnd_cod_out_pay_amt),0) as rfnd_cod_out_pay_amt,
                    round(sum(a.supplier_order_cnt),0) as supplier_order_cnt,
                    sum(a.supplier_old_user_ord_cnt) as supplier_old_user_ord_cnt,
                    sum(a.supplier_new_user_ord_cnt) as supplier_new_user_ord_cnt,
                    case when sum(a.supplier_order_cnt)>0 then round(sum(a.supplier_old_user_ord_cnt)/sum(a.supplier_order_cnt)*100,2) else 0 end as old_user_rate,
                    case when sum(a.supplier_new_user_ord_cnt)>0 then round(sum(b.market_cost)/sum(a.supplier_new_user_ord_cnt),2) else 0 end as new_user_cost,
                    sum(a.rgst_cnt) as rgst_cnt,
                    case when sum(a.rgst_cnt)>0 then round(sum(a.supplier_new_user_ord_cnt)/sum(a.rgst_cnt)*100,2) else 0 end as rgst_conversion_rate,
                    case when sum(a.rgst_cnt)>0 then round(sum(b.market_cost)/sum(a.rgst_cnt),2) else 0 end as rgst_cost,
                    sum(b.app_downloads) as app_downloads,
                    case when sum(b.app_downloads)>0 then round(sum(a.supplier_new_user_ord_cnt)/sum(b.app_downloads)*100,2) else 0 end as download_conversion,
                    case when sum(b.app_downloads)>0 then round(sum(b.market_cost)/sum(b.app_downloads),2) else 0 end as download_cost,
                    round(sum(b.market_cost),2) as market_cost,
                    case when sum(a.supplier_pay_amt)>0 then round(sum(b.market_cost)/sum(a.supplier_pay_amt)*100,2) else 0 end as market_rate,
                    case when sum(a.supplier_order_cnt)>0 then round(sum(a.supplier_pay_amt)/sum(a.supplier_order_cnt),2) else 0 end as customer_price,
                    avg(b.one_year_purchase) as one_year_purchase,
                    round(avg(b.one_year_purchase_amount),2) as one_year_purchase_amount,
                    case when sum(b.one_year_purchase_amount)>0 then round((case when sum(a.supplier_new_user_ord_cnt)>0 then round(sum(b.market_cost)/sum(a.supplier_new_user_ord_cnt),2) else 0 end)/sum(b.one_year_purchase_amount)*100,2) else 0 end as cpa_rate,
                    '1' as sort 
                ";
            $field_reckon="date(a.pre_day) as dt,
                    {$site_tp} as site_tp,
                    {$market} as market,
                    {$country_nm} as country_nm,
                    {$trmnl_tp} as trmnl_tp,
                    '预估' as type,                    
                    round(sum(a.pre_supplier_pay_amt),0) as supplier_pay_amt,                    
                    '-' as pay_amt,
                    '-' as rfnd_out_pay_amt,
                    '-' as rfnd_cod_out_pay_amt,                    
                    round(sum(a.pre_supplier_order_cnt),0) as supplier_order_cnt,                    
                    '-' as supplier_old_user_ord_cnt,
                    '-' as supplier_new_user_ord_cnt,
                    '-' as old_user_rate,
                    '-' as new_user_cost,
                    '-' as rgst_cnt,
                    '-' as rgst_conversion_rate,
                    '-' as rgst_cost,
                    '-' as app_downloads,
                    '-' as download_conversion,
                    '-' as download_cost,
                    '-' as market_cost,
                    '-' as market_rate,
                    '-' as customer_price,
                    '-' as one_year_purchase,
                    '-' as one_year_purchase_amount,
                    '-' as cpa_rate,
                    '2' as sort 
                ";
            //目标按天
            $field_target="date(a.dt) as dt,
                    {$site_tp} as site_tp,
                    {$market_target_m} as market,
                    {$country_nm_target} as country_nm,
                    {$trmnl_tp} as trmnl_tp,
                    '目标' as type,
                    round(sum(a.supplier_pay_amt),0) as supplier_pay_amt,
                    '-' as pay_amt,
                    '-' as rfnd_out_pay_amt,
                    '-' as rfnd_cod_out_pay_amt,
                    round(sum(a.supplier_order_cnt),0) as supplier_order_cnt,
                    sum(a.old_user_ord_cnt) as supplier_old_user_ord_cnt,
                    sum(a.new_user_ord_cnt) as supplier_new_user_ord_cnt,
                    round(sum(a.old_user_rate),2) as old_user_rate,
                    round(sum(a.new_user_cost),2) as new_user_cost,
                    sum(a.rgst_cnt) as rgst_cnt,
                    round(sum(a.rgst_conversion_rate)*100,2) as rgst_conversion_rate,
                    sum(a.rgst_cost) as rgst_cost,
                    sum(a.app_downloads) as app_downloads,
                    round(sum(a.download_conversion)*100,2) as download_conversion,
                    round(sum(a.download_cost),2) as download_cost,
                    round(sum(a.market_cost),2) as market_cost,
                    round(sum(a.market_rate),2) as market_rate,
                    round(sum(a.customer_price),2) as customer_price,
                    avg(b.one_year_purchase) as one_year_purchase,
                    round(avg(b.one_year_purchase_amount),2) as one_year_purchase_amount,
                    round(sum(a.cpa_rate),2) as cpa_rate,
                    '3' as sort";
            $join_t = "left join dm_pub_sale_forecast_real_d b ON a.pay_dt = b.dt and lower(a.site_tp)=lower(b.site_tp) and lower(a.short_nm)=lower(b.country_nm) and lower(a.trmnl_tp)=lower(b.trmnl_tp)";
            $join_target = "left join dm_pub_sale_forecast_real_d b ON a.dt = b.dt and lower(a.site_tp)=lower(b.site_tp) and lower(a.country_nm)=lower(b.country_nm) and lower(a.trmnl_tp)=lower(b.trmnl_tp)
                    left join dw_pub_site_mkt_country_td c ON lower(a.country_nm)=lower(c.short_nm)
                    left join dw_pub_country_td d ON lower(a.country_nm)=lower(d.short_nm)";
            $union_arr = array();
            if(in_array('total',$type_arr) or empty($type_arr)){
                $union_arr[] = $model_real->field($field_real)->where($map)->group($group)->join($join_t)->select(false);
                $union_arr[] = $model_reckon->field($field_reckon)->where($map_reckon)->group($group_reckon)->select(false);
                $union_arr[] = $model_target->field($field_target)->where($map_target)->group($group_target)->join($join_target)->select(false);
            }else{
                if(in_array('实际',$type_arr)){
                    $real_sql = $model_real->field($field_real)->where($map)->group($group)->join($join_t)->select(false);
                    $union_arr[] = $real_sql;
                }
                if(in_array('预估',$type_arr)) {
                    $reckon_sql = $model_reckon->field($field_reckon)->where($map_reckon)->group($group_reckon)->select(false);
                    $union_arr[] = $reckon_sql;
                }
                if(in_array('目标',$type_arr)) {
                    $target_sql = $model_target->field($field_target)->where($map_target)->group($group_target)->join($join_target)->select(false);
                    $union_arr[] = $target_sql;
                }
            }
        }//end if('day')

        $field = "dt,site_tp,market,country_nm,trmnl_tp,type,supplier_pay_amt,pay_amt,rfnd_out_pay_amt,rfnd_cod_out_pay_amt,                    
                    supplier_order_cnt,supplier_old_user_ord_cnt,supplier_new_user_ord_cnt,old_user_rate,new_user_cost,
                    rgst_cnt,rgst_conversion_rate,rgst_cost,app_downloads,download_conversion,download_cost,
                    market_cost,market_rate,customer_price,one_year_purchase,one_year_purchase_amount,
                    cpa_rate,sort";
        foreach ($union_arr as $k=>$v)
        {
            if($k == 0){
                $union_str = $v;
            }else{
                $union_str .= "union ".$v;
            }
        }
        $union_str = "(".$union_str.")";
        //排序
        if (isset($_REQUEST ['sortBy'])) {
            $sortBy = $_REQUEST ['sortBy'];
        } else {
            $sortBy = 'dt';
        }
        //排序默认倒序排列
        //sort 0：倒序 非0：正序
        if (isset($_REQUEST ['_sort'])) {
            $sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
        } else {
            $sort = 'desc';
        }
        if(I('post.is_export')=='1'){
            $voList = $model_real->field($field)->table($union_str.' aa')->group('dt,site_tp,market,country_nm,trmnl_tp,type')->order($sortBy.' '.$sort.$order_add.',sort asc')->select();
            $article = '销售预估-导出';
            $title = array('时间','主站点','市场','国家','设备','数据类型','商城销售额','105销售额','扣除退款销售额','实际收款销售额',
                    '总订单数','老用户订单数','新用户订单数','老用户占比(%)','新用户成本','注册数','注册转化率','注册成本','APP下载数','下载转化率',
                    '下载成本','营销费用','营销占比(%)','客单价','一年购买次数','一年购买金额','CPA占比(%)');
            $this->excel_export($voList,$title,$article);
        }else{
            $count = $model_real->field($field)->table($union_str.' aa')->count('1');
            if($count > 0){
                import('@.ORG.Util.Page_o');
                //创建分页对象
                $listRows = '100';
                $_POST['_sort'] = 0;
                $p = new Page($count, $listRows);
                $list = $model_real->field($field)->table($union_str.' aa')->group('dt,site_tp,market,country_nm,trmnl_tp,type')->order($sortBy.' '.$sort.$order_add.',sort asc')->limit($p->firstRow.','.$p->listRows)->select();
                foreach ($list as &$v){
                    $v['old_user_rate'] = ($v['old_user_rate']=='' or $v['old_user_rate']=='-') ? $v['old_user_rate'] : $v['old_user_rate'].'%';
                    $v['rgst_conversion_rate'] = ($v['rgst_conversion_rate']=='' or $v['rgst_conversion_rate']=='-') ? $v['rgst_conversion_rate'] : $v['rgst_conversion_rate'].'%';
                    $v['download_conversion'] = ($v['download_conversion']=='' or $v['download_conversion']=='-') ? $v['download_conversion'] : $v['download_conversion'].'%';
                    $v['market_rate'] = ($v['market_rate']=='' or $v['market_rate']=='-') ? $v['market_rate'] : $v['market_rate'].'%';
                    $v['cpa_rate'] = ($v['cpa_rate']=='' or $v['cpa_rate']=='-') ? $v['cpa_rate'] : $v['cpa_rate'].'%';
                    unset($v['sort']);
                }
                if(I('post.order')=='week'){
                    foreach ($list as $k) {
                        $a = date('m/d', strtotime(substr($k['dt'], 0, -2) . 'W' . substr($k['dt'], -2)));
                        $c = date("m/d", strtotime("$a -2 day"));
                        $b = date("m/d", strtotime("$a +4 day"));
                        $k['dt'] = $k['dt'] . '周 <br>(' . $c . '-' . $b . ')';
                        $arr[] = $k;
                    }
                }else{
                    $arr=$list;
                }
                //分页显示
//                $header = ['时间','主站点','市场','国家','设备','数据类型','商城销售额','105销售额','<nobr>扣除退款<br/>销售额</nobr>','<nobr>实际收款<br/>销售额</nobr>','总订单数','<nobr>老用户<br/>订单数</nobr>'
//                    ,'<nobr>新用户<br/>订单数</nobr>','<nobr>老用户<br/>占比%</nobr>','<nobr>新用户<br/>成本</nobr>','注册数','<nobr>注册转<br/>化率%</nobr>','注册成本','<nobr>APP<br/>下载数</nobr>','<nobr>下载转<br/>化率%</nobr>','下载成本','营销费用','营销占比%','客单价',
//                    '<nobr>一年购<br/>买次数</nobr>','<nobr>一年购<br/>买金额</nobr>','CPA占比%'];
                //列表排序显示
                $sortImg = $sort; //排序图标
                $sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列' ;//排序提示
                $sort = $sort == 'desc' ? 1 : 0 ;//排序方式
                $order = I('post.order');
                $this->assign('sort',$sort);
                $this->assign('order',$order);
                $this->assign('sortBy',$sortBy);
                $this->assign('sortImg',$sortImg);
                $this->assign('sortType',$sortAlt);
//                $this->assign('header',$header);
                $this->assign('solidLeftHeaderCol',6);
                $page = $p->show();
                $this->assign('page',$page);
                $this->assign('list',$arr);
                $data=$this->fetch('Echarts:form_sales_forecast');
            }else{
                $data='当前天暂无数据';
            }
            echo $data;
            die;
        }
    }
    /**
     * 销售预估 task：45270
     * @author jiang 2017-11-23 10:33
     */
    function sales_forecast_line()
    {
        $site_arr = I('post.site_tp');
        $market_arr = I('post.market');
        $country_arr = I('post.country_nm');
        $type_arr = I('post.type');
        $trmnl_tmp_arr = I('post.trmnl_tp');
        foreach ($trmnl_tmp_arr as $v){
            $trmnl_arr[$v] = $v;
        }
        $query_date = $this->get_query_forecast_date();
        //站点
        if($site_arr != ''){
            if(in_array('total',$site_arr)){
                unset($site_arr[0]);
                if(!empty($site_arr)){
                    $map['a.site_tp'] = array('IN',$site_arr);
                }
            }else{
                if(in_array('detail',$site_arr)){
                    unset($site_arr[0]);
                    if(!empty($site_arr)){
                        $map['a.site_tp'] = array('IN',$site_arr);
                    }
                }else{
                    $map['a.site_tp'] = array('IN',$site_arr);
                }
            }
        }
        //市场
        if($market_arr != ''){
            if(in_array('total',$market_arr)){
                unset($market_arr[0]);
                if(!empty($market_arr)){
                    $map['a.market'] = array('IN',$market_arr);
                }
            }else{
                if(in_array('detail',$market_arr)){
                    unset($market_arr[0]);
                    if(!empty($market_arr)){
                        $map['a.market'] = array('IN',$market_arr);
                    }
                }else{
                    $map['a.market'] = array('IN',$market_arr);
                }
            }
        }
        //国家
        if($country_arr != ''){
            if(in_array('total',$country_arr)){
                unset($country_arr[0]);
                if(!empty($country_arr)){
                    $map['a.short_nm'] = array('IN',$country_arr);
                }
            }else{
                if(in_array('detail',$country_arr)){
                    unset($country_arr[0]);
                    if(!empty($country_arr)){
                        $map['a.short_nm'] = array('IN',$country_arr);
                    }
                }else{
                    $map['a.short_nm'] = array('IN',$country_arr);
                }
            }
        }
        $map_reckon_month= $map;
        //设备
        if($trmnl_arr != ''){
            if(in_array('total',$trmnl_arr)) {
                unset($trmnl_arr['total']);
                if(!empty($trmnl_arr)){
                    $map['a.trmnl_tp'] = array('IN',$trmnl_arr);
                }
            }else{
                if(in_array('detail',$trmnl_arr)){
                    unset($trmnl_arr['detail']);
                }
                if(!empty($trmnl_arr)){
                    if(in_array('app',$trmnl_arr)){
                        $trmnl_wh_arr_tmp[] = 'ios';
                        $trmnl_wh_arr_tmp[] = 'android';
                        unset($trmnl_arr['app']);
                    }
                    if(in_array('web',$trmnl_arr)){
                        $trmnl_wh_arr_tmp[] = 'M';
                        $trmnl_wh_arr_tmp[] = 'PC';
                        unset($trmnl_arr['web']);
                    }
                    if(!empty($trmnl_arr)){
                        foreach ($trmnl_arr as $v){
                            $trmnl_wh_arr_tmp[] = $v;
                        }
                    }
                    $trmnl_wh_arr = array_unique($trmnl_wh_arr_tmp);
                    $map['a.trmnl_tp'] = array('IN',$trmnl_wh_arr);
                }
            }
        }
        if( I('post.order')=='month' ){
            $map['a.static_month'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
            $map_reckon_month['a.static_month'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
            $map_upload_real = $map;
            $map_upload_target = $map;
            $map_upload_target['a.country_nm'] = $map['a.short_nm'];
            unset($map_upload_real['a.short_nm']);
            unset($map_upload_real['a.market']);
            unset($map_upload_target['a.short_nm']);
            unset($map_upload_target['a.market']);
            $group = "a.static_month";
            $model_real = M('pub_sale_analysis_m a', 'dm_', 'Mysql_WH');
            $model_reckon = M('ord_pre_old_ord_m a', 'dm_', 'Mysql_WH');
            $model_target = M('pub_sale_forecast_target_m a', 'dm_', 'Mysql_WH');
            //实际按月
            $field_real="concat(substr(a.static_month,'1',4),'-',substr(a.static_month,'5',2)) as dt,
                    '实际' as type,
                    round(sum(a.supplier_pay_amt),0) as supplier_pay_amt,
                    round(sum(a.pay_amt),0) as pay_amt,
                    round(sum(a.rfnd_out_pay_amt),0) as rfnd_out_pay_amt,
                    round(sum(a.rfnd_cod_out_pay_amt),0) as rfnd_cod_out_pay_amt,
                    round(sum(a.supplier_order_cnt),0) as supplier_order_cnt,
                    sum(a.supplier_old_user_ord_cnt) as supplier_old_user_ord_cnt,
                    sum(a.supplier_new_user_ord_cnt) as supplier_new_user_ord_cnt,
                    case when sum(a.supplier_order_cnt)>0 then round(sum(a.supplier_old_user_ord_cnt)/sum(a.supplier_order_cnt)*100,2) else 0 end as old_user_rate,
                    case when sum(a.supplier_new_user_ord_cnt)>0 then round(sum(b.market_cost)/sum(a.supplier_new_user_ord_cnt),2) else 0 end as new_user_cost,
                    sum(a.rgst_cnt) as rgst_cnt,
                    case when sum(a.rgst_cnt)>0 then round(sum(a.supplier_new_user_ord_cnt)/sum(a.rgst_cnt)*100,2) else 0 end as rgst_conversion_rate,
                    case when sum(a.rgst_cnt)>0 then round(sum(b.market_cost)/sum(a.rgst_cnt),2) else 0 end as rgst_cost,
                    sum(b.app_downloads) as app_downloads,
                    case when sum(b.app_downloads)>0 then round(sum(a.supplier_new_user_ord_cnt)/sum(b.app_downloads)*100,2) else 0 end as download_conversion,
                    case when sum(b.app_downloads)>0 then round(sum(b.market_cost)/sum(b.app_downloads),2) else 0 end as download_cost,
                    round(sum(b.market_cost),2) as market_cost,
                    case when sum(a.supplier_pay_amt)>0 then round(sum(b.market_cost)/sum(a.supplier_pay_amt)*100,2) else 0 end as market_rate,
                    case when sum(a.supplier_order_cnt)>0 then round(sum(a.supplier_pay_amt)/sum(a.supplier_order_cnt),2) else 0 end as customer_price,
                    avg(b.one_year_purchase) as one_year_purchase,
                    round(avg(b.one_year_purchase_amount),2) as one_year_purchase_amount,
                    case when sum(b.one_year_purchase_amount)>0 then round((case when sum(a.supplier_new_user_ord_cnt)>0 then round(sum(b.market_cost)/sum(a.supplier_new_user_ord_cnt),2) else 0 end)/sum(b.one_year_purchase_amount)*100,2) else 0 end as cpa_rate";
            //按月实际上传数据取数据
            $file_upload = "substr(dt,'1',6) as static_month,site_tp,country_nm,trmnl_tp,sum(app_downloads) as app_downloads,
                sum(market_cost) as market_cost,avg(one_year_purchase) as one_year_purchase,avg(one_year_purchase_amount) as one_year_purchase_amount";
            //预估按月
            $field_reckon="concat(substr(a.static_month,'1',4),'-',substr(a.static_month,'5',2)) as dt,
                    '预估' as type,
                    round((sum(a.pre_ord_cnt)+sum(b.new_user_ord_cnt))*avg(a.guest_unit_price),0) as supplier_pay_amt,                 
                    '0' as pay_amt,
                    '0' as rfnd_out_pay_amt,
                    '0' as rfnd_cod_out_pay_amt,                    
                    round((sum(a.pre_ord_cnt)+sum(b.new_user_ord_cnt)),0) as supplier_order_cnt,                  
                    sum(a.pre_ord_cnt) as supplier_old_user_ord_cnt,
                    sum(b.new_user_ord_cnt) as supplier_new_user_ord_cnt,
                    case when (sum(a.pre_ord_cnt)+sum(b.new_user_ord_cnt))>0 then round(sum(a.pre_ord_cnt)/(sum(a.pre_ord_cnt)+sum(b.new_user_ord_cnt))*100,2) else 0 end as old_user_rate,
                    round(avg(b.new_user_cost),2) as new_user_cost,
                    '0' as rgst_cnt,
                    '0' as rgst_conversion_rate,
                    '0' as rgst_cost,
                    '0' as app_downloads,
                    '0' as download_conversion,
                    '0' as download_cost,
                    round(sum(b.new_user_ord_cnt)*avg(b.new_user_cost),2) as market_cost,
                    round((sum(b.new_user_ord_cnt)*avg(b.new_user_cost))/((sum(a.pre_ord_cnt)+sum(b.new_user_ord_cnt))*avg(a.guest_unit_price))*100,2) as market_rate,
                    round(avg(a.guest_unit_price),2) as customer_price,
                    round(avg(b.one_year_purchase),1) as one_year_purchase,
                    round(avg(b.one_year_purchase_amount),2) as one_year_purchase_amount,
                    case when (sum(b.one_year_purchase_amount)>0 and sum(b.one_year_purchase_amount) is not null) then round(avg(b.new_user_cost)/sum(b.one_year_purchase_amount),2) else 0 end as cpa_rate
                ";
            //目标按月
            $field_target="concat(substr(a.static_month,'1',4),'-',substr(a.static_month,'5',2)) as dt,
                    '目标' as type,
                    round(sum(a.supplier_pay_amt),0) as supplier_pay_amt,
                    '0' as pay_amt,
                    '0' as rfnd_out_pay_amt,
                    '0' as rfnd_cod_out_pay_amt,
                    round(sum(a.supplier_order_cnt),0) as supplier_order_cnt,
                    sum(a.old_user_ord_cnt) as supplier_old_user_ord_cnt,
                    sum(a.new_user_ord_cnt) as supplier_new_user_ord_cnt,
                    round(sum(a.old_user_rate),2) as old_user_rate,
                    round(sum(a.new_user_cost),2) as new_user_cost,
                    sum(a.rgst_cnt) as rgst_cnt,
                    round(sum(a.rgst_conversion_rate)*100,2) as rgst_conversion_rate,
                    sum(a.rgst_cost) as rgst_cost,
                    sum(a.app_downloads) as app_downloads,
                    round(sum(a.download_conversion)*100,2) as download_conversion,
                    round(sum(a.download_cost),2) as download_cost,
                    round(sum(a.market_cost),2) as market_cost,
                    round(sum(a.market_rate),2) as market_rate,
                    round(sum(a.customer_price),2) as customer_price,
                    avg(b.one_year_purchase) as one_year_purchase,
                    round(avg(b.one_year_purchase_amount),2) as one_year_purchase_amount,
                    round(sum(a.cpa_rate),2) as cpa_rate";
            $model_upload_real = M('pub_sale_forecast_real_d a', 'dm_', 'Mysql_WH');
            $upload_real_sql = $model_upload_real->field($file_upload)->where($map_upload_real)->group("substr(static_month,'1',6),site_tp,country_nm,trmnl_tp")->select(false);
            $join_t = "left join {$upload_real_sql} b ON a.static_month = b.static_month and lower(a.site_tp)=lower(b.site_tp) and lower(a.short_nm)=lower(b.country_nm) and lower(a.trmnl_tp)=lower(b.trmnl_tp)";
            $join_reckon = "left join dm_pub_sale_forecast_reckon_m b ON a.static_month = b.dt and lower(a.site_tp)=lower(b.site_tp) and lower(a.short_nm)=lower(b.country_nm)";
            $join_target = "left join dm_pub_sale_forecast_reckon_m b ON a.static_month = b.dt and lower(a.site_tp)=lower(b.site_tp) and lower(a.country_nm)=lower(b.country_nm)";
            $union_arr = array();
            if(in_array('total',$type_arr)){
                $union_arr[] = $model_real->field($field_real)->where($map)->group($group)->join($join_t)->select(false);
                $union_arr[] = $model_reckon->field($field_reckon)->where($map_reckon_month)->join($join_reckon)->group($group)->select(false);
                $union_arr[] = $model_target->field($field_target)->where($map_upload_target)->group($group)->join($join_target)->select(false);
            }else{
                if(in_array('实际',$type_arr)){
                    $real_sql = $model_real->field($field_real)->where($map)->group($group)->join($join_t)->select(false);
                    $union_arr[] = $real_sql;
                }
                if(in_array('预估',$type_arr)) {
                    $reckon_sql = $model_reckon->field($field_reckon)->where($map_reckon_month)->join($join_reckon)->group($group)->select(false);
                    $union_arr[] = $reckon_sql;
                }
                if(in_array('目标',$type_arr)) {
                    $target_sql = $model_target->field($field_target)->where($map_upload_target)->group($group)->join($join_target)->select(false);
                    $union_arr[] = $target_sql;
                }
            }

        } else if ( I('post.order')=='week' ) {
            $map['a.static_week'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
            $group = "a.static_week";
            //目标按天字段特殊处理
            $map_target = $map;
            if($map['a.short_nm'] != ''){
                $map_target['a.country_nm'] = $map['a.short_nm'];
                unset($map_target['a.short_nm']);
            }
            if($map_target['a.market'] != ''){
                $map_target['c.market'] = $map_target['a.market'];
                unset($map_target['a.market']);
            }
            $model_real = M('pub_sale_analysis_w a', 'dm_', 'Mysql_WH');
            $model_reckon = M('pub_sale_pre_w a', 'dm_', 'Mysql_WH');
            $model_target = M('pub_sale_forecast_target_d a', 'dm_', 'Mysql_WH');
            $field_real="concat(substr(a.static_week,'1',4),'-',substr(a.static_week,'5',2)) as dt,
                    '实际' as type,
                    round(sum(a.supplier_pay_amt),0) as supplier_pay_amt,
                    round(sum(a.pay_amt),0) as pay_amt,
                    round(sum(a.rfnd_out_pay_amt),0) as rfnd_out_pay_amt,
                    round(sum(a.rfnd_cod_out_pay_amt),0) as rfnd_cod_out_pay_amt,
                    round(sum(a.supplier_order_cnt),0) as supplier_order_cnt,
                    sum(a.supplier_old_user_ord_cnt) as supplier_old_user_ord_cnt,
                    sum(a.supplier_new_user_ord_cnt) as supplier_new_user_ord_cnt,
                    case when sum(a.supplier_order_cnt)>0 then round(sum(a.supplier_old_user_ord_cnt)/sum(a.supplier_order_cnt)*100,2) else 0 end as old_user_rate,
                    case when sum(a.supplier_new_user_ord_cnt)>0 then round(sum(b.market_cost)/sum(a.supplier_new_user_ord_cnt),2) else 0 end as new_user_cost,
                    sum(a.rgst_cnt) as rgst_cnt,
                    case when sum(a.rgst_cnt)>0 then round(sum(a.supplier_new_user_ord_cnt)/sum(a.rgst_cnt)*100,2) else 0 end as rgst_conversion_rate,
                    case when sum(a.rgst_cnt)>0 then round(sum(b.market_cost)/sum(a.rgst_cnt),2) else 0 end as rgst_cost,
                    sum(b.app_downloads) as app_downloads,
                    case when sum(b.app_downloads)>0 then round(sum(a.supplier_new_user_ord_cnt)/sum(b.app_downloads)*100,2) else 0 end as download_conversion,
                    case when sum(b.app_downloads)>0 then round(sum(b.market_cost)/sum(b.app_downloads),2) else 0 end as download_cost,
                    round(sum(b.market_cost),2) as market_cost,
                    case when sum(a.supplier_pay_amt)>0 then round(sum(b.market_cost)/sum(a.supplier_pay_amt)*100,2) else 0 end as market_rate,
                    case when sum(a.supplier_order_cnt)>0 then round(sum(a.supplier_pay_amt)/sum(a.supplier_order_cnt),2) else 0 end as customer_price,
                    avg(b.one_year_purchase) as one_year_purchase,
                    round(avg(b.one_year_purchase_amount),2) as one_year_purchase_amount,
                    case when sum(b.one_year_purchase_amount)>0 then round((case when sum(a.supplier_new_user_ord_cnt)>0 then round(sum(b.market_cost)/sum(a.supplier_new_user_ord_cnt),2) else 0 end)/sum(b.one_year_purchase_amount)*100,2) else 0 end as cpa_rate";
            $field_reckon="concat(substr(a.static_week,'1',4),'-',substr(a.static_week,'5',2)) as dt,
                    '预估' as type,                    
                    round(sum(a.pre_supplier_pay_amt),0) as supplier_pay_amt,                    
                    '0' as pay_amt,
                    '0' as rfnd_out_pay_amt,
                    '0' as rfnd_cod_out_pay_amt,                    
                    round(sum(a.pre_supplier_order_cnt),0) as supplier_order_cnt,                    
                    '0' as supplier_old_user_ord_cnt,
                    '0' as supplier_new_user_ord_cnt,
                    '0' as old_user_rate,
                    '0' as new_user_cost,
                    '0' as rgst_cnt,
                    '0' as rgst_conversion_rate,
                    '0' as rgst_cost,
                    '0' as app_downloads,
                    '0' as download_conversion,
                    '0' as download_cost,
                    '0' as market_cost,
                    '0' as market_rate,
                    '0' as customer_price,
                    '0' as one_year_purchase,
                    '0' as one_year_purchase_amount,
                    '0' as cpa_rate
                ";
            //目标按天
            $field_target="concat(substr(a.static_week,'1',4),'-',substr(a.static_week,'5',2)) as dt,
                    '目标' as type,
                    round(sum(a.supplier_pay_amt),0) as supplier_pay_amt,
                    '0' as pay_amt,
                    '0' as rfnd_out_pay_amt,
                    '0' as rfnd_cod_out_pay_amt,
                    round(sum(a.supplier_order_cnt),0) as supplier_order_cnt,
                    sum(a.old_user_ord_cnt) as supplier_old_user_ord_cnt,
                    sum(a.new_user_ord_cnt) as supplier_new_user_ord_cnt,
                    round(sum(a.old_user_rate),2) as old_user_rate,
                    round(sum(a.new_user_cost),2) as new_user_cost,
                    sum(a.rgst_cnt) as rgst_cnt,
                    round(sum(a.rgst_conversion_rate)*100,2) as rgst_conversion_rate,
                    sum(a.rgst_cost) as rgst_cost,
                    sum(a.app_downloads) as app_downloads,
                    round(sum(a.download_conversion)*100,2) as download_conversion,
                    round(sum(a.download_cost),2) as download_cost,
                    round(sum(a.market_cost),2) as market_cost,
                    round(sum(a.market_rate),2) as market_rate,
                    round(sum(a.customer_price),2) as customer_price,
                    avg(b.one_year_purchase) as one_year_purchase,
                    round(avg(b.one_year_purchase_amount),2) as one_year_purchase_amount,
                    round(sum(a.cpa_rate),2) as cpa_rate";
            $join_t = "left join dm_pub_sale_forecast_real_d b ON a.static_week = b.static_week and lower(a.site_tp)=lower(b.site_tp) and lower(a.short_nm)=lower(b.country_nm) and lower(a.trmnl_tp)=lower(b.trmnl_tp)";
            $join_target = "left join dm_pub_sale_forecast_real_d b ON a.dt = b.dt and lower(a.site_tp)=lower(b.site_tp) and lower(a.country_nm)=lower(b.country_nm) and lower(a.trmnl_tp)=lower(b.trmnl_tp)
                    left join dw_pub_site_mkt_country_td c ON lower(a.country_nm)=lower(c.short_nm)";
            $union_arr = array();
            if(in_array('total',$type_arr)){
                $union_arr[] = $model_real->field($field_real)->where($map)->group($group)->join($join_t)->select(false);
                $union_arr[] = $model_reckon->field($field_reckon)->where($map)->group($group)->select(false);
                $union_arr[] = $model_target->field($field_target)->where($map_target)->group($group)->join($join_target)->select(false);
            }else{
                if(in_array('实际',$type_arr)){
                    $real_sql = $model_real->field($field_real)->where($map)->group($group)->join($join_t)->select(false);
                    $union_arr[] = $real_sql;
                }
                if(in_array('预估',$type_arr)) {
                    $reckon_sql = $model_reckon->field($field_reckon)->where($map)->group($group)->select(false);
                    $union_arr[] = $reckon_sql;
                }
                if(in_array('目标',$type_arr)) {
                    $target_sql = $model_target->field($field_target)->where($map_target)->group($group)->join($join_target)->select(false);
                    $union_arr[] = $target_sql;
                }
            }
        } else {
            $map_reckon = $map;
            $map_target = $map;
            $map_reckon['a.dt'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
            $map['a.pay_dt'] = array(array('EGT',$query_date['start_date']),array('ELT',$query_date['end_date']),'and');
            $group = "a.dt";
            $group_real = "a.pay_dt";
            //目标按天字段特殊处理
            if($map['a.short_nm'] != ''){
                $map_target['a.country_nm'] = $map['a.short_nm'];
                unset($map_target['a.short_nm']);
            }
            if($map_target['a.market'] != ''){
                $map_target['c.market'] = $map_target['a.market'];
                unset($map_target['a.market']);
            }
            $model_real = M('pub_sale_analysis_d a', 'dm_', 'Mysql_WH');
            $model_reckon = M('pub_sale_pre_d a', 'dm_', 'Mysql_WH');
            $model_target = M('pub_sale_forecast_target_d a', 'dm_', 'Mysql_WH');
            $field_real="date(a.pay_dt) as dt,
                    '实际' as type,
                    round(sum(a.supplier_pay_amt),0) as supplier_pay_amt,
                    round(sum(a.pay_amt),0) as pay_amt,
                    round(sum(a.rfnd_out_pay_amt),0) as rfnd_out_pay_amt,
                    round(sum(a.rfnd_cod_out_pay_amt),0) as rfnd_cod_out_pay_amt,
                    round(sum(a.supplier_order_cnt),0) as supplier_order_cnt,
                    sum(a.supplier_old_user_ord_cnt) as supplier_old_user_ord_cnt,
                    sum(a.supplier_new_user_ord_cnt) as supplier_new_user_ord_cnt,
                    case when sum(a.supplier_order_cnt)>0 then round(sum(a.supplier_old_user_ord_cnt)/sum(a.supplier_order_cnt)*100,2) else 0 end as old_user_rate,
                    case when sum(a.supplier_new_user_ord_cnt)>0 then round(sum(b.market_cost)/sum(a.supplier_new_user_ord_cnt),2) else 0 end as new_user_cost,
                    sum(a.rgst_cnt) as rgst_cnt,
                    case when sum(a.rgst_cnt)>0 then round(sum(a.supplier_new_user_ord_cnt)/sum(a.rgst_cnt)*100,2) else 0 end as rgst_conversion_rate,
                    case when sum(a.rgst_cnt)>0 then round(sum(b.market_cost)/sum(a.rgst_cnt),2) else 0 end as rgst_cost,
                    sum(b.app_downloads) as app_downloads,
                    case when sum(b.app_downloads)>0 then round(sum(a.supplier_new_user_ord_cnt)/sum(b.app_downloads)*100,2) else 0 end as download_conversion,
                    case when sum(b.app_downloads)>0 then round(sum(b.market_cost)/sum(b.app_downloads),2) else 0 end as download_cost,
                    round(sum(b.market_cost),2) as market_cost,
                    case when sum(a.supplier_pay_amt)>0 then round(sum(b.market_cost)/sum(a.supplier_pay_amt)*100,2) else 0 end as market_rate,
                    case when sum(a.supplier_order_cnt)>0 then round(sum(a.supplier_pay_amt)/sum(a.supplier_order_cnt),2) else 0 end as customer_price,
                    avg(b.one_year_purchase) as one_year_purchase,
                    round(avg(b.one_year_purchase_amount),2) as one_year_purchase_amount,
                    case when sum(b.one_year_purchase_amount)>0 then round((case when sum(a.supplier_new_user_ord_cnt)>0 then round(sum(b.market_cost)/sum(a.supplier_new_user_ord_cnt),2) else 0 end)/sum(b.one_year_purchase_amount)*100,2) else 0 end as cpa_rate
                ";
            $field_reckon="date(a.dt) as dt,
                    '预估' as type,                    
                    round(sum(a.pre_supplier_pay_amt),0) as supplier_pay_amt,                    
                    '0' as pay_amt,
                    '0' as rfnd_out_pay_amt,
                    '0' as rfnd_cod_out_pay_amt,                    
                    round(sum(a.pre_supplier_order_cnt),0) as supplier_order_cnt,                    
                    '0' as supplier_old_user_ord_cnt,
                    '0' as supplier_new_user_ord_cnt,
                    '0' as old_user_rate,
                    '0' as new_user_cost,
                    '0' as rgst_cnt,
                    '0' as rgst_conversion_rate,
                    '0' as rgst_cost,
                    '0' as app_downloads,
                    '0' as download_conversion,
                    '0' as download_cost,
                    '0' as market_cost,
                    '0' as market_rate,
                    '0' as customer_price,
                    '0' as one_year_purchase,
                    '0' as one_year_purchase_amount,
                    '0' as cpa_rate
                ";
            //目标按天
            $field_target="date(a.dt) as dt,
                    '目标' as type,
                    round(sum(a.supplier_pay_amt),0) as supplier_pay_amt,
                    '0' as pay_amt,
                    '0' as rfnd_out_pay_amt,
                    '0' as rfnd_cod_out_pay_amt,
                    round(sum(a.supplier_order_cnt),0) as supplier_order_cnt,
                    sum(a.old_user_ord_cnt) as supplier_old_user_ord_cnt,
                    sum(a.new_user_ord_cnt) as supplier_new_user_ord_cnt,
                    round(sum(a.old_user_rate),2) as old_user_rate,
                    round(sum(a.new_user_cost),2) as new_user_cost,
                    sum(a.rgst_cnt) as rgst_cnt,
                    round(sum(a.rgst_conversion_rate)*100,2) as rgst_conversion_rate,
                    sum(a.rgst_cost) as rgst_cost,
                    sum(a.app_downloads) as app_downloads,
                    round(sum(a.download_conversion)*100,2) as download_conversion,
                    round(sum(a.download_cost),2) as download_cost,
                    round(sum(a.market_cost),2) as market_cost,
                    round(sum(a.market_rate),2) as market_rate,
                    round(sum(a.customer_price),2) as customer_price,
                    avg(b.one_year_purchase) as one_year_purchase,
                    round(avg(b.one_year_purchase_amount),2) as one_year_purchase_amount,
                    round(sum(a.cpa_rate),2) as cpa_rate";
            $join_t = "left join dm_pub_sale_forecast_real_d b ON a.pay_dt = b.dt and lower(a.site_tp)=lower(b.site_tp) and lower(a.short_nm)=lower(b.country_nm) and lower(a.trmnl_tp)=lower(b.trmnl_tp)";
            $join_target = "left join dm_pub_sale_forecast_real_d b ON a.dt = b.dt and lower(a.site_tp)=lower(b.site_tp) and lower(a.country_nm)=lower(b.country_nm) and lower(a.trmnl_tp)=lower(b.trmnl_tp)
                    left join dw_pub_site_mkt_country_td c ON lower(a.country_nm)=lower(c.short_nm)";
            $union_arr = array();
            if(in_array('total',$type_arr)){
                $union_arr[] = $model_real->field($field_real)->where($map)->group($group_real)->join($join_t)->select(false);
                $union_arr[] = $model_reckon->field($field_reckon)->where($map_reckon)->group($group)->select(false);
                $union_arr[] = $model_target->field($field_target)->where($map_target)->group($group)->join($join_target)->select(false);
            }else{
                if(in_array('实际',$type_arr)){
                    $real_sql = $model_real->field($field_real)->where($map)->group($group_real)->join($join_t)->select(false);
                    $union_arr[] = $real_sql;
                }
                if(in_array('预估',$type_arr)) {
                    $reckon_sql = $model_reckon->field($field_reckon)->where($map_reckon)->group($group)->select(false);
                    $union_arr[] = $reckon_sql;
                }
                if(in_array('目标',$type_arr)) {
                    $target_sql = $model_target->field($field_target)->where($map_target)->group($group)->join($join_target)->select(false);
                    $union_arr[] = $target_sql;
                }
            }
        }//end if('day')

        $field = "dt,type,supplier_pay_amt,pay_amt,rfnd_out_pay_amt,rfnd_cod_out_pay_amt,                    
                    supplier_order_cnt,supplier_old_user_ord_cnt,supplier_new_user_ord_cnt,old_user_rate,new_user_cost,
                    rgst_cnt,rgst_conversion_rate,rgst_cost,app_downloads,download_conversion,download_cost,
                    market_cost,market_rate,customer_price,one_year_purchase,one_year_purchase_amount,
                    cpa_rate";
        foreach ($union_arr as $k=>$v)
        {
            if($k == 0){
                $union_str = $v;
            }else{
                $union_str .= "union ".$v;
            }
        }
        $union_str = "(".$union_str.")";
        $list = $model_real->field($field)->table($union_str.' aa')->group('dt,type')->order('dt desc')->select();
        if(in_array('total',$type_arr)){
            $legend = array('商城销售额-实际','商城销售额-预估','商城销售额-目标',
                '105销售额-实际','105销售额-预估','105销售额-目标',
                '扣除退款销售额-实际','扣除退款销售额-预估','扣除退款销售额-目标',
                '实际收款销售额-实际','实际收款销售额-预估','实际收款销售额-目标',
                '总订单数-实际','总订单数-预估','总订单数-目标',
                '老用户订单数-实际','老用户订单数-预估','老用户订单数-目标',
                '新用户订单数-实际','新用户订单数-预估','新用户订单数-目标',
                '老用户占比-实际','老用户占比-预估','老用户占比-目标',
                '新用户成本-实际','新用户成本-预估','新用户成本-目标',
                '注册数-实际','注册数-预估','注册数-目标',
                '注册转化率-实际','注册转化率-预估','注册转化率-目标',
                '注册成本-实际','注册成本-预估','注册成本-目标',
                'APP下载数-实际','APP下载数-预估','APP下载数-目标',
                '下载转化率-实际','下载转化率-预估','下载转化率-目标',
                '下载成本-实际','下载成本-预估','下载成本-目标',
                '营销费用-实际','营销费用-预估','营销费用-目标',
                '营销占比-实际','营销占比-预估','营销占比-目标',
                '客单价-实际','客单价-预估','客单价-目标',
                '一年购买次数-实际','一年购买次数-预估','一年购买次数-目标',
                '一年购买金额-实际','一年购买金额-预估','一年购买金额-目标',
                'CPA占比-实际','CPA占比-预估','CPA占比-目标'
            );
            $unselected = array('商城销售额-预估','商城销售额-目标',
                '105销售额-实际','105销售额-预估','105销售额-目标',
                '扣除退款销售额-实际','扣除退款销售额-预估','扣除退款销售额-目标',
                '实际收款销售额-实际','实际收款销售额-预估','实际收款销售额-目标',
                '总订单数-实际','总订单数-预估','总订单数-目标',
                '老用户订单数-实际','老用户订单数-预估','老用户订单数-目标',
                '新用户订单数-实际','新用户订单数-预估','新用户订单数-目标',
                '老用户占比-实际','老用户占比-预估','老用户占比-目标',
                '新用户成本-实际','新用户成本-预估','新用户成本-目标',
                '注册数-实际','注册数-预估','注册数-目标',
                '注册转化率-实际','注册转化率-预估','注册转化率-目标',
                '注册成本-实际','注册成本-预估','注册成本-目标',
                'APP下载数-实际','APP下载数-预估','APP下载数-目标',
                '下载转化率-实际','下载转化率-预估','下载转化率-目标',
                '下载成本-实际','下载成本-预估','下载成本-目标',
                '营销费用-实际','营销费用-预估','营销费用-目标',
                '营销占比-实际','营销占比-预估','营销占比-目标',
                '客单价-实际','客单价-预估','客单价-目标',
                '一年购买次数-实际','一年购买次数-预估','一年购买次数-目标',
                '一年购买金额-实际','一年购买金额-预估','一年购买金额-目标',
                'CPA占比-实际','CPA占比-预估','CPA占比-目标'
            );
        }else{
            $legend = array();
            $unselected = array();
            if(in_array('实际',$type_arr)){
                $legend_tmp[0] = array('商城销售额-实际','105销售额-实际','扣除退款销售额-实际','实际收款销售额-实际','总订单数-实际','老用户订单数-实际',
                    '新用户订单数-实际','老用户占比-实际','新用户成本-实际','注册数-实际','注册转化率-实际','注册成本-实际','APP下载数-实际','下载转化率-实际',
                    '下载成本-实际','营销费用-实际','营销占比-实际','客单价-实际','一年购买次数-实际','一年购买金额-实际','CPA占比-实际');
                $unselected_tmp[0] = array('105销售额-实际','扣除退款销售额-实际','实际收款销售额-实际','总订单数-实际','老用户订单数-实际',
                    '新用户订单数-实际','老用户占比-实际','新用户成本-实际','注册数-实际','注册转化率-实际','注册成本-实际','APP下载数-实际','下载转化率-实际',
                    '下载成本-实际','营销费用-实际','营销占比-实际','客单价-实际','一年购买次数-实际','一年购买金额-实际','CPA占比-实际');
                $legend = array_merge($legend,$legend_tmp[0]);
                $unselected = array_merge($unselected,$unselected_tmp[0]);
            }
            if(in_array('预估',$type_arr)) {
                $legend_tmp[1] = array('商城销售额-预估','105销售额-预估','扣除退款销售额-预估','实际收款销售额-预估','总订单数-预估','老用户订单数-预估',
                    '新用户订单数-预估','老用户占比-预估','新用户成本-预估','注册数-预估','注册转化率-预估','注册成本-预估','APP下载数-预估','下载转化率-预估',
                    '下载成本-预估','营销费用-预估','营销占比-预估','客单价-预估','一年购买次数-预估','一年购买金额-预估','CPA占比-预估');
                $unselected_tmp[1] = array('105销售额-预估','扣除退款销售额-预估','实际收款销售额-预估','总订单数-预估','老用户订单数-预估',
                    '新用户订单数-预估','老用户占比-预估','新用户成本-预估','注册数-预估','注册转化率-预估','注册成本-预估','APP下载数-预估','下载转化率-预估',
                    '下载成本-预估','营销费用-预估','营销占比-预估','客单价-预估','一年购买次数-预估','一年购买金额-预估','CPA占比-预估');
                $legend = array_merge($legend,$legend_tmp[1]);
                $unselected = array_merge($unselected,$unselected_tmp[1]);
            }
            if(in_array('目标',$type_arr)) {
                $legend_tmp[2] = array('商城销售额-目标','105销售额-目标','扣除退款销售额-目标','实际收款销售额-目标','总订单数-目标','老用户订单数-目标',
                    '新用户订单数-目标','老用户占比-目标','新用户成本-目标','注册数-目标','注册转化率-目标','注册成本-目标','APP下载数-目标','下载转化率-目标',
                    '下载成本-目标','营销费用-目标','营销占比-目标','客单价-目标','一年购买次数-目标','一年购买金额-目标','CPA占比-目标');
                $unselected_tmp[2] = array('商城销售额-目标','105销售额-目标','扣除退款销售额-目标','实际收款销售额-目标','总订单数-目标','老用户订单数-目标',
                    '新用户订单数-目标','老用户占比-目标','新用户成本-目标','注册数-目标','注册转化率-目标','注册成本-目标','APP下载数-目标','下载转化率-目标',
                    '下载成本-目标','营销费用-目标','营销占比-目标','客单价-目标','一年购买次数-目标','一年购买金额-目标','CPA占比-目标');
                $legend = array_merge($legend,$legend_tmp[2]);
                $unselected = array_merge($unselected,$unselected_tmp[2]);
            }
        }
        $series = array();
        if(count($list) == 0){
            $data='当前暂无数据';
            echo $data;die;
        }
        $date=PublicAction::get_date();
        $tmp_list = array();
        foreach($list as $k=>$v) {
            if($v['type'] == '实际'){
                $tmp_list['商城销售额-实际'][$v['dt']] = $v['supplier_pay_amt'];
                $tmp_list['105销售额-实际'][$v['dt']] = $v['pay_amt'];
                $tmp_list['扣除退款销售额-实际'][$v['dt']] = $v['rfnd_out_pay_amt'];
                $tmp_list['实际收款销售额-实际'][$v['dt']] = $v['rfnd_cod_out_pay_amt'];
                $tmp_list['总订单数-实际'][$v['dt']] = $v['supplier_order_cnt'];
                $tmp_list['老用户订单数-实际'][$v['dt']] = $v['supplier_old_user_ord_cnt'];
                $tmp_list['新用户订单数-实际'][$v['dt']] = $v['supplier_new_user_ord_cnt'];
                $tmp_list['老用户占比-实际'][$v['dt']] = $v['old_user_rate'];
                $tmp_list['新用户成本-实际'][$v['dt']] = $v['new_user_cost'];
                $tmp_list['注册数-实际'][$v['dt']] = $v['rgst_cnt'];
                $tmp_list['注册数-实际'][$v['dt']] = $v['rgst_cnt'];
                $tmp_list['注册转化率-实际'][$v['dt']] = $v['rgst_conversion_rate'];
                $tmp_list['注册成本-实际'][$v['dt']] = $v['rgst_cost'];
                $tmp_list['APP下载数-实际'][$v['dt']] = $v['app_downloads'];
                $tmp_list['下载转化率-实际'][$v['dt']] = $v['download_conversion'];
                $tmp_list['下载成本-实际'][$v['dt']] = $v['download_cost'];
                $tmp_list['营销费用-实际'][$v['dt']] = $v['market_cost'];
                $tmp_list['营销占比-实际'][$v['dt']] = $v['market_rate'];
                $tmp_list['客单价-实际'][$v['dt']] = $v['customer_price'];
                $tmp_list['一年购买次数-实际'][$v['dt']] = $v['one_year_purchase'];
                $tmp_list['一年购买金额-实际'][$v['dt']] = $v['one_year_purchase_amount'];
                $tmp_list['CPA占比-实际'][$v['dt']] = $v['cpa_rate'];
            }elseif($v['type'] == '预估'){
                $tmp_list['商城销售额-预估'][$v['dt']] = $v['supplier_pay_amt'];
                $tmp_list['105销售额-预估'][$v['dt']] = $v['pay_amt'];
                $tmp_list['扣除退款销售额-预估'][$v['dt']] = $v['rfnd_out_pay_amt'];
                $tmp_list['实际收款销售额-预估'][$v['dt']] = $v['rfnd_cod_out_pay_amt'];
                $tmp_list['总订单数-预估'][$v['dt']] = $v['supplier_order_cnt'];
                $tmp_list['老用户订单数-预估'][$v['dt']] = $v['supplier_old_user_ord_cnt'];
                $tmp_list['新用户订单数-预估'][$v['dt']] = $v['supplier_new_user_ord_cnt'];
                $tmp_list['老用户占比-预估'][$v['dt']] = $v['old_user_rate'];
                $tmp_list['新用户成本-预估'][$v['dt']] = $v['new_user_cost'];
                $tmp_list['注册数-预估'][$v['dt']] = $v['rgst_cnt'];
                $tmp_list['注册数-预估'][$v['dt']] = $v['rgst_cnt'];
                $tmp_list['注册转化率-预估'][$v['dt']] = $v['rgst_conversion_rate'];
                $tmp_list['注册成本-预估'][$v['dt']] = $v['rgst_cost'];
                $tmp_list['APP下载数-预估'][$v['dt']] = $v['app_downloads'];
                $tmp_list['下载转化率-预估'][$v['dt']] = $v['download_conversion'];
                $tmp_list['下载成本-预估'][$v['dt']] = $v['download_cost'];
                $tmp_list['营销费用-预估'][$v['dt']] = $v['market_cost'];
                $tmp_list['营销占比-预估'][$v['dt']] = $v['market_rate'];
                $tmp_list['客单价-预估'][$v['dt']] = $v['customer_price'];
                $tmp_list['一年购买次数-预估'][$v['dt']] = $v['one_year_purchase'];
                $tmp_list['一年购买金额-预估'][$v['dt']] = $v['one_year_purchase_amount'];
                $tmp_list['CPA占比-预估'][$v['dt']] = $v['cpa_rate'];
            }elseif($v['type'] == '目标'){
                $tmp_list['商城销售额-目标'][$v['dt']] = $v['supplier_pay_amt'];
                $tmp_list['105销售额-目标'][$v['dt']] = $v['pay_amt'];
                $tmp_list['扣除退款销售额-目标'][$v['dt']] = $v['rfnd_out_pay_amt'];
                $tmp_list['实际收款销售额-目标'][$v['dt']] = $v['rfnd_cod_out_pay_amt'];
                $tmp_list['总订单数-目标'][$v['dt']] = $v['supplier_order_cnt'];
                $tmp_list['老用户订单数-目标'][$v['dt']] = $v['supplier_old_user_ord_cnt'];
                $tmp_list['新用户订单数-目标'][$v['dt']] = $v['supplier_new_user_ord_cnt'];
                $tmp_list['老用户占比-目标'][$v['dt']] = $v['old_user_rate'];
                $tmp_list['新用户成本-目标'][$v['dt']] = $v['new_user_cost'];
                $tmp_list['注册数-目标'][$v['dt']] = $v['rgst_cnt'];
                $tmp_list['注册数-目标'][$v['dt']] = $v['rgst_cnt'];
                $tmp_list['注册转化率-目标'][$v['dt']] = $v['rgst_conversion_rate'];
                $tmp_list['注册成本-目标'][$v['dt']] = $v['rgst_cost'];
                $tmp_list['APP下载数-目标'][$v['dt']] = $v['app_downloads'];
                $tmp_list['下载转化率-目标'][$v['dt']] = $v['download_conversion'];
                $tmp_list['下载成本-目标'][$v['dt']] = $v['download_cost'];
                $tmp_list['营销费用-目标'][$v['dt']] = $v['market_cost'];
                $tmp_list['营销占比-目标'][$v['dt']] = $v['market_rate'];
                $tmp_list['客单价-目标'][$v['dt']] = $v['customer_price'];
                $tmp_list['一年购买次数-目标'][$v['dt']] = $v['one_year_purchase'];
                $tmp_list['一年购买金额-目标'][$v['dt']] = $v['one_year_purchase_amount'];
                $tmp_list['CPA占比-目标'][$v['dt']] = $v['cpa_rate'];
            }
        }
        if (I('post.order')=='week') {
            array_pop($date[1]);
            array_pop($date[2]);
            $xAxis = $date[1];
            $date = $date[2];
            $this-> xAxis=$xAxis;
        } else {
            if(I('post.order')=='day'){
                array_pop($date);
            }
            $this-> xAxis=$date;
        }
        foreach ($tmp_list as $k => $v) {
            foreach ($date as $d) {
                if (!empty($v[$d])) {
                    $member = $v[$d];
                }else{
                    $member = 0;
                }
                $series[$k][$d] = $member;
            }
        }
        $this->legend=array_unique($legend);
        $this->xAxis=$date;
        $this->series=$series;

        //默认展示隐藏部分
        $this->assign('unselected',$unselected);
        $data=$this->fetch('Echarts:echarts_line_sales_forecast');
        echo $data;die;
    }
    /**
     * 提前备货售罄率
     * author @chenmin 20171228
     */
    public function display_pre_stock_out(){
        $cat_model = M('dw_pub_sku_category_td', null, 'Mysql_WH');
        if(I('post.type') != '') {
            $flag['sku_cate_id'] = I('post.type');
            $return_flag = $cat_model->field('distinct cate_lvl')->where($flag)->select();
            $tmp_str =I('post.type');
            if('1' == $return_flag[0]['cate_lvl']){
                $a_flag['sku_cate_1_id'] = I('post.type');
                $arr_flag = $cat_model->field('distinct sku_cate_2_id')->where($a_flag)->select();
                foreach($arr_flag as $key){
                    if($key['sku_cate_2_id'] != ''){
                        $tmp_str .= ','.$key['sku_cate_2_id'];
                    }
                }
            }
            $map['sku_cate_id'] = array('in',$tmp_str);
        }
        //处理生成条件
        if(I('prd_add_time_egt') !=''){
            $map['prd_add_time'][] =array('EGT',I('prd_add_time_egt'));
        }
        if(I('prd_add_time_elt') !='') {
            $map['prd_add_time'][] =array('ELT',I('prd_add_time_elt'));
        }
        if(I('goods_sns') !='') {
            $map['goods_sn'] =I('goods_sns');
        }
        if(I('sale_flags') == '0'){
            $map['sale_flag']=0;
        }
        if(I('sale_flags') == '1'){
            $map['sale_flag']=1;
        }
        if(I('recycle_flags') == '0'){
            $map['recycle_flag']=0;
        }
        if(I('recycle_flags') == '1'){
            $map['recycle_flag']=1;
        }
        if(I('buyer_nm') !='') {
            $map['buyer_nm']=array('in',I('post.buyer_nm'));
        }
        if(I('prd_status_nms') !='0'){
            $map['prd_status_nm'] =I('prd_status_nms');
        }
        if(I('pre_sale_time_elt') !='' and I('pre_sale_time_egt') !='' and I('post.null_sta')=='false'){
            $map['prd_frst_sale_date'][] =array('EGT',I('pre_sale_time_egt'));
            $map['prd_frst_sale_date'][] =array('ELT',I('pre_sale_time_elt'));
        }elseif(I('pre_sale_time_elt') !=''  and I('post.null_sta')=='false'){
            $map['prd_frst_sale_date'][] =array('ELT',I('pre_sale_time_elt'));
        }elseif(I('pre_sale_time_egt') !='' and I('post.null_sta')=='false'){
            $map['prd_frst_sale_date'][] =array('EGT',I('pre_sale_time_egt'));
        }elseif(I('pre_sale_time_elt')!='' or I('pre_sale_time_egt')!='' and I('post.null_sta')=='true'){
            $map['prd_frst_sale_date']='qfef';
        }elseif(I('pre_sale_time_elt')=='' and I('pre_sale_time_egt')=='' and I('post.null_sta')=='true'){
            $map['prd_frst_sale_date']=array('EXP',' is null');
        }
        if(I('pre_sale_day_elt') !='') {
            $map['pre_sale_day'][] =array('ELT',I('pre_sale_day_elt'));
        }
        if(I('pre_sale_day_egt') !='') {
            $map['pre_sale_day'][] =array('EGT',I('pre_sale_day_egt'));
        }
        //处理售罄
        if(I('out_day_type') == 'sale'){
            if(I('out_day_elt') !='') {
                $map['sale_out_day'][] =array('ELT',I('out_day_elt'));
            }
            if(I('out_day_egt') !='') {
                $map['sale_out_day'][] =array('EGT',I('out_day_egt'));
            }
            if(I('out_day_egt') =='' && I('out_day_elt') ==''){
                $where = array();
                $where['sale_out_day'][] =array('EGT',0);
                $where['sale_out_day'][] =array('ELT',0);
                $where['_logic'] = 'or';
                $map['_complex'][] =$where;
            }
        }elseif(I('out_day_type') == 'pre'){
            if(I('out_day_elt') !='') {
                $map['pre_out_day'][] =array('ELT',I('out_day_elt'));
            }
            if(I('out_day_egt') !='') {
                $map['pre_out_day'][] =array('EGT',I('out_day_egt'));
            }
            if(I('out_day_egt') =='' && I('out_day_elt') ==''){
                $where = array();
                $where['pre_out_day'][] =array('EGT',0);
                $where['pre_out_day'][] =array('ELT',0);
                $where['_logic'] = 'or';
                $map['_complex'][] =$where;
            }
        }else{
            if(I('out_day_elt') !='' && I('out_day_egt') =='') {
                $where = array();
                $where['pre_out_day'] =array('ELT',I('out_day_elt'));
                $where['sale_out_day'] =array('ELT',I('out_day_elt'));
                $where['_logic'] = 'or';
                $map['_complex'][] =$where;
            }
            if(I('out_day_elt') =='' && I('out_day_egt') !='') {
                $where = array();
                $where['pre_out_day'] =array('EGT',I('out_day_egt'));
                $where['sale_out_day'] =array('EGT',I('out_day_egt'));
                $where['_logic'] = 'or';
                $map['_complex'][] =$where;
            }
            if(I('out_day_elt') !='' && I('out_day_egt') !='') {
                $where = array();
                $where['pre_out_day'] =array(array('EGT',I('out_day_egt')),array('ELT',I('out_day_elt')));
                $where['sale_out_day'] =array(array('EGT',I('out_day_egt')),array('ELT',I('out_day_elt')));
                $where['_logic'] = 'or';
                $map['_complex'][] =$where;
            }
        }
        $map['site_tp']=I('post.site_tp');
        if(I('post.supplier_nm')!=''){
            $map['supplier_nm']=array('like',I('post.supplier_nm').'%');
        }
        if(I('post.cate_1_nm')!=''){
            $map['cate_1_nm'] = I('post.cate_1_nm');
        }
        $model = M('dm_pub_sku_stock_ord_out_d',null,'Mysql_WH');
        $field = "sku_cate_nm,goods_sn,case when img_url LIKE 'images%'
        then 'http://img.'||if(site_tp='emmastyle','makemechic',site_tp)||'.com/'||img_url
        else img_url end as img_url,
        case when sale_flag='1' then '√' else '×' end as sale_flag,
        case when recycle_flag='1' then '√' else '×' end as recycle_flag,
        buyer_nm,round(cost_rmb_amt,0) as cost_rmb_amt,round(price,0) as price,round(special_price,0) as special_price,prd_add_time,prd_status_nm,
        prd_status_time,to_days(prd_status_time)-to_days(prd_add_time) as days,
        prd_user_name,cate_1_nm,supplier_cd,stored_cnt,prd_frst_sale_date,pre_sale_day,sale_out_day,pre_out_day,sale_cnt7,total_cnt,stk_cnt,
        case when sum(uv)>0 then concat(round(site_total_cnt/uv*100,2),'%') else 0 end as cvs_rate,sum(pcs_cnt) as pcs_cnt,
        sum(tobe_onshelf_cnt) as tobe_onshelf_cnt,sum(shpp_stk_cnt) as shpp_stk_cnt,max(produce_team) as produce_team";
        $form = "form_pre_stock_out";
        $group ="goods_sn";
        //字段排序 默认主键
        if (isset($_REQUEST ['sortBy'])) {
            $order = $_REQUEST ['sortBy'];
        } else {
            $order = !empty($sortBy) ? $sortBy : $model->getPk();
        }
        //排序默认倒序排列
        //sort 0：倒序 非0：正序
        if (isset($_REQUEST ['_sort'])) {
            $sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
        } else {
            $sort = $asc ? 'asc' : 'desc';
        }
        //取得记录总数
        if ($group != '') {
            $subQuery = $model->field($field)->where($map)->group($group)->select(false);
            $count = $model->table($subQuery.' a')->count('1');
        } else {
            $count = $model->where($map)->count('1');
        }
        if(I('post.is_export')=='1'){
            //导出开始
            $epage='800';
            header("Content-type:application/octet-stream");
            header("Accept-Ranges:bytes");
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename=" . "提前备货售罄率导出".".xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            $title=['分类','SKU','图片','上架状态','回收站状态','买手','成本','原价','特价','首单下单时间','首单跟单状态','跟单状态最新时间','生产时效','加工厂','一级渠道','供应商编号','入库数量(总)','上架时间(分)','上架天数(分)','首单售罄天数(总)','预测售罄天数(总)','近7天销量(分)','总销量(总)','库存','转化率(分)','待采购数量','待上架数量','在途数量','生产组别'];
            if(!empty($title)){
                foreach($title as $k=>$v){
                    $title[$k]=iconv('UTF-8','GB2312',$v);
                }
                $title=implode("\t",$title);
                echo "$title\n";
            }
            //获取分页数
            $lim=floor($count/$epage);
            if ($lim == 0) {
                $lim = 1;
                $epage = $count;
            }
            for($time = 0;$time <= $lim; $time++) {
                //设置limit限制
                $now = $time*$epage;
                if ($lim - $time < 1) {
                    $epage = $count - $now;
                }
                $limit = "$now,$epage";
                //目标数组
                $data = $model->field($field)->where($map)->group($group)->order('goods_sn desc')->limit($limit)->select();
                if(!empty($data)){
                    foreach($data as $key=>$val){
                        unset($val['numrow']);
                        foreach ($val as $ck => $cv) {
                            $data[$key][$ck]=iconv("UTF-8", "GB2312", $cv);
                        }
                        $data[$key]=implode("\t", $data[$key]);
                    }
                    echo implode("\n",$data);
                    echo ("\n");
                }
            }
            exit;
        }else{
            if ($count>0) {
                import('@.ORG.Util.Page_o');
                //创建分页对象
                if ($_REQUEST ['listRows'] == 'undefined' || $_REQUEST ['listRows'] =='') {
                    $listRows = '100';
                } else {
                    $listRows = $_REQUEST ['listRows'];
                }
                $p = new Page($count, $listRows);
                $voList = $model->field($field)->where($map)->group($group)->order($order." ".$sort)->limit($p->firstRow.','.$p->listRows)->select();
                foreach ($map as $key => $val) {
                    if (!is_array($val)) {
                        $p->parameter.="$key=".urldecode($val)."&";
                    }
                }
                //分页显示
                $page = $p->show();
                //列表排序显示
                $sortImg = $sort; //排序图标
                $sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列' ;//排序提示
                $sort = $sort == 'desc' ? 1 : 0 ;//排序方式

                //模板赋值
                $this->assign('list',$voList);
                $this->assign('sort',$sort);
                $this->assign('order',$order);
                $this->assign('sortImg',$sortImg);
                $this->assign('sortType',$sortAlt);
                $this->assign('page',$page);

                $data = $this->fetch('Echarts:'.$form);
                echo $data;
            }else{
                echo'当前天暂无数据';
            }
            return;
        }
    }
    /**
     * 提前备货售罄率 汇总
     * author @chenmin 20171228
     */
    public function total_display_pre_stock_out(){
        //处理生成条件
        if(I('prd_add_time_egt') !=''){
            $map['prd_add_time'][] =array('EGT',I('prd_add_time_egt'));
        }
        if(I('prd_add_time_elt') !='') {
            $map['prd_add_time'][] =array('ELT',I('prd_add_time_elt'));
        }
        if(I('goods_sns') !='') {
            $map['goods_sn'] =I('goods_sns');
        }
        if(I('sale_flags') == '0'){
            $map['sale_flag']=0;
        }
        if(I('sale_flags') == '1'){
            $map['sale_flag']=1;
        }
        if(I('recycle_flags') == '0'){
            $map['recycle_flag']=0;
        }
        if(I('recycle_flags') == '1'){
            $map['recycle_flag']=1;
        }

        if(I('post.buyer_nm') !='') {
            $map['buyer_nm'] =array('in',I('post.buyer_nm'));
        }
        if(I('prd_status_nms') !='0'){
            $map['prd_status_nm'] =I('prd_status_nms');
        }

        if(I('pre_sale_time_elt') !='' and I('pre_sale_time_egt') !='' and I('post.null_sta')=='false'){
            $map['prd_frst_sale_date'][] =array('EGT',I('pre_sale_time_egt'));
            $map['prd_frst_sale_date'][] =array('ELT',I('pre_sale_time_elt'));
        }elseif(I('pre_sale_time_elt') !=''  and I('post.null_sta')=='false'){
            $map['prd_frst_sale_date'][] =array('ELT',I('pre_sale_time_elt'));
        }elseif(I('pre_sale_time_egt') !='' and I('post.null_sta')=='false'){
            $map['prd_frst_sale_date'][] =array('EGT',I('pre_sale_time_egt'));
        }elseif(I('pre_sale_time_elt')!='' or I('pre_sale_time_egt')!='' and I('post.null_sta')=='true'){
            $map['prd_frst_sale_date']='qfef';
        }elseif(I('pre_sale_time_elt')=='' and I('pre_sale_time_egt')=='' and I('post.null_sta')=='true'){
            $map['prd_frst_sale_date']=array('EXP',' is null');
        }
        if(I('pre_sale_day_elt') !='') {
            $map['pre_sale_day'][] =array('ELT',I('pre_sale_day_elt'));
        }
        if(I('pre_sale_day_egt') !='') {
            $map['pre_sale_day'][] =array('EGT',I('pre_sale_day_egt'));
        }
        //处理售罄
        if(I('out_day_type') == 'sale'){
            if(I('out_day_elt') !='') {
                $map['sale_out_day'][] =array('ELT',I('out_day_elt'));
            }
            if(I('out_day_egt') !='') {
                $map['sale_out_day'][] =array('EGT',I('out_day_egt'));
            }
            if(I('out_day_egt') =='' && I('out_day_elt') ==''){
                $where = array();
                $where['sale_out_day'][] =array('EGT',0);
                $where['sale_out_day'][] =array('ELT',0);
                $where['_logic'] = 'or';
                $map['_complex'][] =$where;
            }
        }elseif(I('out_day_type') == 'pre'){
            if(I('out_day_elt') !='') {
                $map['pre_out_day'][] =array('ELT',I('out_day_elt'));
            }
            if(I('out_day_egt') !='') {
                $map['pre_out_day'][] =array('EGT',I('out_day_egt'));
            }
            if(I('out_day_egt') =='' && I('out_day_elt') ==''){
                $where = array();
                $where['pre_out_day'][] =array('EGT',0);
                $where['pre_out_day'][] =array('ELT',0);
                $where['_logic'] = 'or';
                $map['_complex'][] =$where;
            }
        }else{
            if(I('out_day_elt') !='' && I('out_day_egt') =='') {
                $where = array();
                $where['pre_out_day'] =array('ELT',I('out_day_elt'));
                $where['sale_out_day'] =array('ELT',I('out_day_elt'));
                $where['_logic'] = 'or';
                $map['_complex'][] =$where;
            }
            if(I('out_day_elt') =='' && I('out_day_egt') !='') {
                $where = array();
                $where['pre_out_day'] =array('EGT',I('out_day_egt'));
                $where['sale_out_day'] =array('EGT',I('out_day_egt'));
                $where['_logic'] = 'or';
                $map['_complex'][] =$where;
            }
            if(I('out_day_elt') !='' && I('out_day_egt') !='') {
                $where = array();
                $where['pre_out_day'] =array(array('EGT',I('out_day_egt')),array('ELT',I('out_day_elt')));
                $where['sale_out_day'] =array(array('EGT',I('out_day_egt')),array('ELT',I('out_day_elt')));
                $where['_logic'] = 'or';
                $map['_complex'][] =$where;
            }
        }
        $map['site_tp']=I('post.site_tp');
        if(I('post.supplier_nm')!=''){
            $map['supplier_nm']=array('like',I('post.supplier_nm').'%');
        }
        //获取统计数据
        $model = M('dm_pub_sku_stock_ord_out_d',null,'Mysql_WH');
        $sku_count = $model->where($map)->count('goods_sn');
        $sku_sale_count = $model->where($map)->where("sale_flag =1")->count('goods_sn');
        $sku_recycle_count = $model->where($map)->where("recycle_flag =1")->count('goods_sn');
        //跟单状态

        $option = array(
            'field'=>'prd_status_nm'
        );
        $res = $model->distinct('prd_status_nm')->select($option);
        foreach($res as $k=> $v){
            //$prd_status_nm[]=$v['prd_status_nm'];
            $prd_status_nm_count[$v['prd_status_nm']] = $model->where($map)->where("prd_status_nm ='".$v['prd_status_nm']."'")->count('prd_status_nm');
        }

        //售罄时间段内（总销量统计）
        $sale_out_day_count20 = $model->where($map)->where("sale_out_day <= '20' and total_cnt >= '100'")->count();
        $pre_out_day_count20 = $model->where($map)->where("pre_out_day <= '20'")->count();
        $sale_out_day_count20_30 = $model->where($map)->where("sale_out_day > '20' and sale_out_day <='30' and total_cnt>= '100'")->count();
        $pre_out_day_count20_30 = $model->where($map)->where("pre_out_day > '20' and pre_out_day <='30'")->count();
        $sale_out_day_count30_60 = $model->where($map)->where("sale_out_day > '30' and sale_out_day <='60' and total_cnt>= '100'")->count();
        $pre_out_day_count30_60 = $model->where($map)->where("pre_out_day > '30' and pre_out_day <='60'")->count();
        $sale_out_day_count60_90 = $model->where($map)->where("sale_out_day > '60' and sale_out_day <='90' and total_cnt>= '100'")->count();
        $pre_out_day_count60_90 = $model->where($map)->where("pre_out_day > '60' and pre_out_day <='90'")->count();
        $sale_out_day_count90 = $model->where($map)->where("sale_out_day > '90' and total_cnt>='100'")->count();
        $pre_out_day_count90 = $model->where($map)->where("pre_out_day > '90'")->count();

        $sale_cnt7_count = $model->where($map)->sum('sale_cnt7');
        $total_cnt_count = $model->where($map)->sum('total_cnt');
        $stk_cnt_count = $model->where($map)->sum('stk_cnt');

        $clk_cnt_count = $model->where($map)->sum('clk_cnt');

        $evg_cvs_rate = round(($total_cnt_count/$clk_cnt_count)*100,2).'%';

        //拼接统计数据 返回前台展示


        $backspace = "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
        $total_statistc_string = "";

        $total_statistc_string .= "<b>SKU总数：</b>$sku_count<br/>";
        $total_statistc_string .= "<b>上架状态为√SKU总数：</b>$sku_sale_count<br/>";
        $total_statistc_string .= "<b>回收站状态为√SKU总数：</b>$sku_recycle_count<br/>";
        $total_statistc_string .= "<b>跟单状态:</b> <br/>";
        foreach($prd_status_nm_count as $k=>$v){
            if($k != ''){
                $total_statistc_string .= "$backspace $k:$v<br/>";
            }
        }
        $total_statistc_string .= "<b>售罄时间段内（SKU数量统计）</b> <br/>";
        $total_statistc_string .= "$backspace 售罄天数：（ 实际售罄（SKU数） ： 预测售罄 ）<br/>";
        $total_statistc_string .= "$backspace 小于等于20天的：（$sale_out_day_count20 : $pre_out_day_count20 ）<br/>";
        $total_statistc_string .= "$backspace 大于20天，小于等于30天的：($sale_out_day_count20_30 ：$pre_out_day_count20_30 )<br/>";
        $total_statistc_string .= "$backspace 大于30天，小于等于60天的：($sale_out_day_count30_60 ： $pre_out_day_count30_60 )<br/>";
        $total_statistc_string .= "$backspace 大于60天，小于等于90天的：($sale_out_day_count60_90 ： $pre_out_day_count60_90 )<br/>";
        $total_statistc_string .= "$backspace 大于90天的：($sale_out_day_count90 ：$pre_out_day_count90 )<br/>";
        $total_statistc_string .= "<b>近7天销售量总和：</b>$sale_cnt7_count<br/>";
        $total_statistc_string .= "<b>总销量：</b>$total_cnt_count<br/>";
        $total_statistc_string .= "<b>总库存：</b>$stk_cnt_count<br/>";
        $total_statistc_string .= "<b>平均转化率：</b>$evg_cvs_rate<br/>";

        echo $total_statistc_string;
        exit;
    }
}
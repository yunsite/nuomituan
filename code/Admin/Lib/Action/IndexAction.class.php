<?php
class IndexAction extends CommonAction {
	
	// 框架首页
	public function index() {
		if (isset ( $_SESSION [C ( 'USER_AUTH_KEY' )] )) {
			//显示菜单项
			$menu = array ();
			$model = M("AdminMenu");
            if(session("administrator")){
                //echo 'dd';
            }else{
                //echo 'bb';
                $uid = getMemberId();
                $role = getRole($uid);
                //继续。。 
                
                //获取用户
            }
            $list = $model -> where('display=1')->order(array('fid'=>'asc','sort'=>'asc'))->select();
            $menu = fetchMenu($list,0);
            $menu_html = displayMenu($menu,false);
			$this->assign ( 'menu', $menu_html);
		}
		C ( 'SHOW_RUN_TIME', false ); // 运行时间显示
		C ( 'SHOW_PAGE_TRACE', false );
		$this->display ();
	}
}
?>
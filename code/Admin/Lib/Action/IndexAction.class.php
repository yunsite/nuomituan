<?php
class IndexAction extends CommonAction {
	
	// 框架首页
	public function index() {
		if (isset ( $_SESSION [C ( 'USER_AUTH_KEY' )] )) {
			//显示菜单项
			$menu = array ();
			//读取数据库模块列表生成菜单项
			
			$node = M ( "Node" );
			$id = $node->getField ( "id" );
			$where ['level'] = 2;
			$where ['status'] = 1;
			$where ['pid'] = $id;
			$list = $node->where ( $where )->field ( 'id,name,group_id,title' )->order ( 'sort asc' )->select ();
			$accessList = $_SESSION ['_ACCESS_LIST'];
			foreach ( $list as $key => $module ) {
				if (isset ( $accessList [strtoupper ( APP_NAME )] [strtoupper ( $module ['name'] )] ) || $_SESSION ['administrator']) {
					//设置模块访问权限
					$module ['access'] = 1;
					$menu [$module['group_id']][] = $module;
				}
			}
            //去掉默认操作和公共操作
            unset($menu[0]);
            //添加分组的访问权限
            foreach ($menu as $key => $value) {
                $menu[$key]['access'] = '0';
                foreach ($value as $k => $v) {
                    if($v['access']){
                        $menu[$key]['access'] = 1;
                        break;
                    }
                }
            }
			//print_r($menu);
			if (! empty ( $_GET ['tag'] )) {
				$this->assign ( 'menuTag', $_GET ['tag'] );
			}
			$this->assign ( 'menu', $menu );
            //读取通知
            $notice_model = M("Notice");
            $notice_list = $notice_model -> limit(10)->select();
            $this -> assign("notice_list",$notice_list);
		}
		C ( 'SHOW_RUN_TIME', false ); // 运行时间显示
		C ( 'SHOW_PAGE_TRACE', false );
		$this->display ();
	}
}
?>
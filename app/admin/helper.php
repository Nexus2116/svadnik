<?php

	class HTML {

		public static $base = '/admin';

		public static function url($url) {
			return self::$base . $url;
		}

		public static function menu($parent) {
			$tree = \App::view('tree');
			$show = 'style="display:none;"';
			if($parent == 0)
				$show = '';
			$str = '<ul class="sidebarArticlesMenu" '.$show.'>';
			if(!isset($tree[$parent]))
				return null;
			foreach ($tree[$parent] as $node) {
				if($node->url != 'news'){
					$pageId = 'id="pageid_' . $node->id . '"';
					//if($node->childs <= 10)
						$str .= '<li data-id="'.$node->id.'"><a data-ctrl="Page" href="'.self::url('/'.$node->type.'/edit/id/' . $node->id).'"><span>'.$node->name.'</span>';
					/*else {
						$str .= '<li data-id="'.$node->id.'" class="open"><a href="#"><span>'.$node->name;
						$str .= '<div class="content-item-list"></div></span>';
					}*/
		            $str .= '<div data-ctrl="Articles" data-act="options" url="'.self::url('/articles/options/id/' . $node->id).'" class="content-item-settings"></div>
		            		<div data-ctrl="Articles" data-act="delete" url="'.self::url('/articles/delete/id/' . $node->id).'" class="content-item-remove"></div>
		            		<div data-ctrl="Articles" data-act="options" url="'.self::url('/articles/options/parent_id/' . $node->id).'" class="content-item-add-div"></div></a>';

					if($node->childs != 0) {
				        $str .= '<div class="multi-open-but"><div class="multi-open-but-icon"></div></div>';
				        $str .= HTML::menu($node->id);
					}
					$str .=' </li>';
				}
			}
			if($parent != 0)
				$str .= '<li><a href="'.HTML::url('/articles/options/parent_id/' . $node->parent_id).'" data-ctrl="Articles" data-act="create" class="content-item-add"></a></li>';
			$str .= '</ul>';
			return $str;
		}

		public static function activeTab($tab) {
			echo $tab == \App::view('activeTab') ? 'active' : '';
		}

		public static function select($data, $current = null, $class = null, $style = null, $action = null, $name='type') {
			$act = $action != null ? 'data-act="'.$action.'"' : '';?>
			<div class="bm-select-list-wrap <?=$class?>" style="<?=$style?>">
                <input type="hidden" name="<?=$name?>" id="pageType" value="<?=$current?>">
                <div class="bm-select-title" data-ctrl="Selector">
                	<?echo isset($data[$current]) ? $data[$current] : reset($data)?>
                </div>
                <div class="bm-select-list">
                	<?foreach ($data as $key => $value) {?>
	                    <div class="bm-select-list-item" value="<?=$key?>">
	                    	<?=$value?>
	                    </div>
                   	<?}?>
                </div>
            </div>
		<?}

		public static function radio($obj, $action = null, $name = null, $main = true) {
			$checked = $obj->published == 1 ? 'on' : 'off';
			$data_act = $action == null ? '' : 'data-act="'.$action.'"';?>

            <div class="bm-radio <?if($main) echo "main";?> <?=$checked?>" data-ctrl="Radio" data-itemid="<?=$obj->id?>" <?=$data_act?>>
            	<?if($name != null) {?>
            		<input type="hidden" class="radioValue" name="<?=$name?>" value="<?=$value?>">
            	<?}?>
                <div class="bm-radio-off"></div>
                <div class="bm-radio-on"></div>
                <div class="bm-radio-switcher"></div>
            </div>
		<?}

		public static function radioUsers($obj, $type) {
			if($type == 'newsletter')
				$checked = $obj->newsletter == 1 ? 'on' : 'off';
			if($type == 'pro')
				$checked = $obj->pro == 1 ? 'on' : 'off';?>

            <div class="bm-radio main <?=$checked?>" data-ctrl="Radio" data-itemid="<?=$obj->id?>" data-act="check">
        		<input type="hidden" class="radioValue" name="<?=$type?>" value="">
                <div class="bm-radio-off"></div>
                <div class="bm-radio-on"></div>
                <div class="bm-radio-switcher"></div>
            </div>
		<?}

		public static function checkbox($id = 0, $name = "", $checked = false, $action = null) {
			$check = $checked == true ? 'active' : '';
			$value = $checked == true ? 1 : 0;
			$act = $action != null ? 'data-act="' . $action . '"' : '';?>
			<div class="bm-checkbox <?=$check?>" data-ctrl="Checkbox" <?=$act?> data-itemid="<?=$id?>">
				<input type="hidden" name="<?=$name?>" value="<?=$value?>" class="checkValue">
                <div class="bm-checkbox-choice"></div>
            </div>
		<?}

		public function image($article, $key = 'image') {
			$str = '';
			if($article->$key != null)
				$str = 'style="background:#ddd url(/public/upload/admin/'.$article->$key.') 50% 50%; background-size:cover"';?>
			<div>
				<div class="bm-dragndrop" <?=$str?>>
					<input type="hidden" value="<?=$key?>">
					<?if($article->$key == null) {?>
					    <div class="bm-dragndrop-img"></div>
					    <div class="bm-dragndrop-caption">Загрузить</div>
				    <?}?>
				    <div class="bm-uploaded" id="image-field-<?=$key?>"></div>
				</div>
				<a data-ctrl="Image" data-act="remove" data-id="<?=$article->id?>" data-key="<?=$key?>" href="/admin/articles/removeimage">Удалить</a>
			</div>
		<?}

		public static function access($parent) {
			$tree = \App::view('tree');
			
			$str = '<ul>';
			if(!isset($tree[$parent]))
				return null;
			foreach ($tree[$parent] as $node) {
				$pageId = 'data-articleid="' . $node->id . '"';
				$str .= '<li '.$pageId.'>
		            <div class="bm-checkbox" data-ctrl="Checkbox">
		            	<div class="bm-checkbox-choice"></div>
		            </div>
		            <span>'.$node->name.'</span>';
		            if($node->childs != 0)
			        	$str .= HTML::access($node->id);
		        $str .= '</li>';
			}
			$str .= '</ul>';
			return $str;
		}

	}
	
?>
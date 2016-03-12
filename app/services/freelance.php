<?php

namespace Service;
	
	class Freelance {

		public function index() {

			$user = \Model\Users::where('id', \App::session('user')->id)->first();
			\App::view('user', $user);

			$catalog = \Model\Articles::where('url', 'services-catalog')->first();
			$catalog_parent = \Model\Articles::where('parent_id', $catalog->id)->get();
			\App::view('catalog', $catalog_parent);

			$servicesUser = \Model\Service::where('userid', \App::session('user')->id)->where('typeserv', 'service')->where('deleted', null)->get();
			foreach ($servicesUser as $key => $item) {
				$ids[] = $item['tagid'];
			}

			if(!empty($ids)){
				$services =  \Capsule\Db::table('services')
	            ->join('articles', 'services.tagid', '=', 'articles.id')
	            ->where('typeserv', 'service')
	            ->where('userid', \App::session('user')->id)
	            ->where('services.deleted', null)
	            ->get();
				\App::view('servicesUser', $services);
			}
			

			$servicesUserPhoto = \Model\Service::where('userid', \App::session('user')->id)->where('deleted', null)->where('typeserv', 'photos')->get();
			\App::view('servicesUserPhoto', $servicesUserPhoto);
			$servicesUserVideo = \Model\Service::where('userid', \App::session('user')->id)->where('deleted', null)->where('typeserv', 'video')->get();
			\App::view('servicesUserVideo', $servicesUserVideo);
			$servicesUserPresent = \Model\Service::where('userid', \App::session('user')->id)->where('deleted', null)->where('typeserv', 'present')->get();
			\App::view('servicesUserPresent', $servicesUserPresent);




			$project_arr = explode(',', $user->projects);
			$project = \Model\Projects::whereIn('id', $project_arr)->get();
			\App::view('project', $project);

			$calendar = \Model\Reserve_day::where('userid', $user->id)->get();
			foreach($calendar as $item)
				$cal[] = $item->date;
			if(!empty($cal)){
				$calendarStr = implode(',', $cal);
				\App::view('calendarStr', $calendarStr);
			}



		}

		public function index_post(){
			if(isset($_POST['mode'])){
				if( $_POST['mode'] == 2){
					$update = array('firstname' => $_POST['firstname']);

					$user = (object) array();
					$user->id = \App::session('user')->id;
					$user->firstname = $_POST['firstname'];
					$user->role = 0;
					\App::session('user', $user);
				}
				elseif($_POST['mode'] == 3)
					$update = array('password' => md5($_POST['new-password']));
			}
			if(isset($_POST['data'])){
				$count=0;
				$iterator = 1;
				$group = array();
				foreach($_POST['data'] as $item) {
					if($count > 5){
						$arr[] = $item;
						if($iterator == 3) {
							$price['price'] = $arr[0]['value'];
							$price['projprice'] = $arr[1]['value'];
							$price['id'] = $arr[2]['value'];
							$group[] = $price;
							$arr = array();
							$iterator = 0;
						}
						$iterator++;
					}else{
						$update[$item['name']] = $item['value'];
					}
					$count++;
				}
				array_shift($update);

				\Model\Service::where('userid' , \App::session('user')->id)->where('typeserv', 'service')->update(array('deleted'=>1));
				foreach($group as $item){
					if($item['price'] != 'Цена за час (рублей)' && $item['projprice'] != 'Цена за проект (рублей)'){
						$service = \Model\Service::where('userid' , \App::session('user')->id)->where('tagid', $item['id'])->first();
					
						if(empty($service)){
							$services = new \Model\Service;
							$services->userid = \App::session('user')->id;
							$services->tagid = $item['id'];
							$services->price = $item['price'];
							$services->projprice = $item['projprice'];
							$services->typeserv = 'service';
							$services->deleted = null;
							$services->save();
						}else{
							\Model\Service::where('userid' , \App::session('user')->id)->where('tagid' , $item['id'])->update(array(
								'deleted'=>null,
								'projprice' => $item['projprice'],
								'price' => $item['price']
								));
						}	
					}
				}

			}

			\Model\Users::where('id', \App::session('user')->id)->update($update);
			if(isset($_POST['mode'])){
				\Core\Response::navigate('/edit');
			}
			exit;
		}

		public function calendar(){
			if(isset($_GET['calendar']))
				$calendar = \Model\Reserve_day::where('userid' , \App::session('user')->id)->where('date', $_GET['calendar'])->count();
				if($calendar){
					\Model\Reserve_day::where('userid' , \App::session('user')->id)->where('date', $_GET['calendar'])->delete();
					echo json_encode($_GET['calendar']);
				}
				else{
					$calendar = new \Model\Reserve_day;
					$calendar->userid = \App::session('user')->id;
					$calendar->date = $_GET['calendar'];
					$calendar->save();
				}

			exit;
		}

		public function delFile_post(){
			if(isset(\App::session('user')->id))
				\Model\Service::where('userid', \App::session('user')->id)->where('id', $_POST['id'])->delete();
			exit;
		}

	}
?>
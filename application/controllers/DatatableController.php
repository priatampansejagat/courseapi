<?php
defined('BASEPATH') or exit('No direct script access allowed');

// include('fungsi.php');

class DatatableController extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->helper('url');
		// $this->load->library(['MySession']);
		$this->load->model(['AccountModel']);

		// $this->globalfunction= new GlobalFunction();		
	}

	public function get_data(){

		$jsonPOST = file_get_contents('php://input');
		$dataReceived = json_decode($jsonPOST, true);


		if ($dataReceived['ihateapple'] == 'mentor') {

			$userCond = array('role_id' => AS_MENTOR, 'deleted' => ACTIVE);
			$dbResult = $this->BasicQuery->selectAllResult('user',$userCond);
			
			$this->success('berhasil', $dbResult);

		}else if ($dataReceived['ihateapple'] == 'single_user') {

			$user_id = $dataReceived['user_id'];

			$userCond = array('id' => $user_id);
			$dbResult['user'] = $this->BasicQuery->selectAll('user',$userCond);

			$detailCond = array('id_user' => $user_id);
			$dbResult['user_detail'] = $this->BasicQuery->selectAll('detail_user',$detailCond);

			$this->success('berhasil', $dbResult);

		}else if ($dataReceived['ihateapple'] == 'course') {

			$courseCond = array('status' => 1);

			if (isset($dataReceived['role'])) {
				 if ($dataReceived['role'] == AS_MENTOR){
					$user_id = $dataReceived['user_id'];
					$courseCond = array('status' => 1, 'mentor_id' => $user_id);
				}
			}

			$dbResult = $this->BasicQuery->selectAllResult('course',$courseCond);
			

			foreach ($dbResult as $key => $value) {
				$userCond = array('id' => $value['mentor_id'],'role_id' => AS_MENTOR, 'deleted' => ACTIVE);
				$dbResult[$key]['mentor'] = $this->BasicQuery->selectAll('user',$userCond);
			}
			

			$this->success('berhasil', $dbResult);

		}else if ($dataReceived['ihateapple'] == 'single_course') {

			$courseCond = array('id' => $dataReceived['id'], 'status' => 1);
			$dbResult = $this->BasicQuery->selectAll('course',$courseCond);

			$userCond = array('id' => $dbResult['mentor_id'],'role_id' => AS_MENTOR, 'deleted' => ACTIVE);
			$dbResult['mentor'] = $this->BasicQuery->selectAll('user',$userCond);
			
			$userdetailCond = array('id_user' => $dbResult['mentor_id']);
			$dbResult['mentor']['detail'] = $this->BasicQuery->selectAll('detail_user',$userdetailCond);

			$this->success('berhasil', $dbResult);

		}else if ($dataReceived['ihateapple'] == 'course_chapter') {

			$courseCond = array('course_id' => $dataReceived['course_id']);
			$dbResult = $this->BasicQuery->selectAllResult('course_chapter',$courseCond);


			$this->success('berhasil', $dbResult);

		}else if ($dataReceived['ihateapple'] == 'course_member') {

			// course_member
			$condition = $dataReceived['condition'];
			$dbResult = $this->BasicQuery->selectAllResult('course_member',$condition);

			// user data
			foreach ($dbResult as $key => $value) {

				$user_cond = array("id" => $value['user_id']);
				$dbResult[$key]['detail'] = $this->BasicQuery->selectAll('user',$user_cond);

				$pay_cond = array("id" => $value['payment_id']);
				$dbResult[$key]['payment'] = $this->BasicQuery->selectAll('payment',$pay_cond);

				if ($dbResult[$key]['payment']['status'] == 0) {
					$dbResult[$key]['confirmed'] = 'registered';
				}else if ($dbResult[$key]['payment']['status'] == 1) {
					$dbResult[$key]['confirmed'] = 'paid';
				}else if ($dbResult[$key]['payment']['status'] == 2) {
					$dbResult[$key]['confirmed'] = 'confirmed';
				}else{
					$dbResult[$key]['confirmed'] = 'declined';
				}

			}

			$this->success('berhasil', $dbResult);

		}else if ($dataReceived['ihateapple'] == 'mycourse') {

			// prepare data
			$user_id = $dataReceived['user_id'];

			// get course member
			$courseMemberCond = array('user_id' => $user_id);
			$dbResult = $this->BasicQuery->selectAllResult('course_member',$courseMemberCond);
			$data=array();

			// get detaucourse
			foreach ($dbResult as $key => $value) {
				$courseCond = array('id' => $value['course_id'], 'status' => 1);
				$countCourse = $this->BasicQuery->countAllResult('course',$courseCond);

				if ($countCourse != 0) {
					$data[$key] = $dbResult[$key];

					$data[$key]['course_detail'] = $this->BasicQuery->selectAll('course',$courseCond);

					// get mentor
					$userCond = array('id' => $data[$key]['course_detail']['mentor_id'],'role_id' => AS_MENTOR, 'deleted' => ACTIVE);
					$data[$key]['mentor'] = $this->BasicQuery->selectAll('user',$userCond);
				}
			}
			
			$this->success('berhasil', $data);

		}else if ($dataReceived['ihateapple'] == 'mycourse_room') {

			// prepare data
			$user_id = $dataReceived['user_id'];
			$course_id = $dataReceived['course_id'];
			$chapter_id = '';
			$single_chapter = false;

			// ngecek apakah minta 1 chapter aja atau list chapter
			if (isset($dataReceived['single_chapter']) && isset($dataReceived['chapter_id'])) {
				if ($dataReceived['single_chapter'] == true) {
					$single_chapter = true;
					$chapter_id = $dataReceived['chapter_id'];
				}
			}

			// cek apakah user sudah approved
			$courseMemberCond = array('user_id' => $user_id, "course_id" => $course_id);
			$dbResult['course_member'] = $this->BasicQuery->selectAll('course_member',$courseMemberCond);
			if ($dbResult['course_member']['confirmed'] == 1) {

				$courseCond = array('id' => $course_id);
				$dbResult['course_detail'] = $this->BasicQuery->selectAll('course',$courseCond);

				// list/semua chapter
				$chapterCond = array('course_id' => $course_id);

				// single chapter
				if ($single_chapter == true) {
					$chapterCond = array('course_id' => $course_id, 'id' => $chapter_id);
				}
				
				$dbResult['course_chapter'] = $this->BasicQuery->selectAllResult('course_chapter',$chapterCond);

				$userCond = array('id' => $dbResult['course_detail']['mentor_id'],'role_id' => AS_MENTOR, 'deleted' => ACTIVE);
				$dbResult['mentor'] = $this->BasicQuery->selectAll('user',$userCond);

				$this->success('berhasil', $dbResult);

			}else{
				$this->failed('User not approved by admin',$dbResult);
			}

		}else if ($dataReceived['ihateapple'] == 'payment_incomplete_adm') {

			$payCond = array('status' => 0);
			$dbResult = $this->BasicQuery->selectAllResult('payment',$payCond);

			$this->success('berhasil', $dbResult);

		}else if ($dataReceived['ihateapple'] == 'payment_paid_adm') {

			$payCond = array('status' => 1);
			$dbResult = $this->BasicQuery->selectAllResult('payment',$payCond);

			$this->success('berhasil', $dbResult);

		}else if ($dataReceived['ihateapple'] == 'payment_unpaid') {

			$payCond = array('status' => 0, 'id_user' => $dataReceived['id_user']);
			$dbResult = $this->BasicQuery->selectAllResult('payment',$payCond);

			$this->success('berhasil', $dbResult);

		}else if ($dataReceived['ihateapple'] == 'payment_all') {

			$payCond = array('id_user' => $dataReceived['id_user']);
			$dbResult = $this->BasicQuery->selectAllResult('payment',$payCond);

			$this->success('berhasil', $dbResult);

		}else if ($dataReceived['ihateapple'] == 'event') {

			$eventCond = array('status' => 1);
			$dbResult = $this->BasicQuery->selectAllResult('event',$eventCond);

			$this->success('berhasil', $dbResult);

		}else if ($dataReceived['ihateapple'] == 'single_event') {

			$event_id = $dataReceived['event_id'];

			$bridgeCond = array('event_id' => $event_id);
			$bridge = $this->BasicQuery->selectAllResult('bridge_event_course',$bridgeCond);

			$eventCond = array('id' => $event_id);
			$dbResult['event_info'] = $this->BasicQuery->selectAll('event',$eventCond);

			foreach ($bridge as $key => $value) {
				$courseCond = array('id' => $value['course_id']);
				$dbResult['course_list'][$key] = $this->BasicQuery->selectAll('course',$courseCond);

				$mentorCond = array('id' => $dbResult['course_list'][$key]['mentor_id'], 'deleted' => ACTIVE);
				$dbResult['course_list'][$key]['mentor_detail'] = $this->BasicQuery->selectAll('user',$mentorCond);
				
				$userdetailCond = array('id_user' => $dbResult['course_list'][$key]['mentor_id']);
				$dbResult['course_list'][$key]['mentor_detail']['additional'] = $this->BasicQuery->selectAll('detail_user',$userdetailCond);
			}

			$this->success('berhasil', $dbResult);

		}else if ($dataReceived['ihateapple'] == 'event_member') {

			// course_member
			$condition = $dataReceived['condition'];
			$dbResult = $this->BasicQuery->selectAllResult('event_member',$condition);

			// user data
			foreach ($dbResult as $key => $value) {

				$user_cond = array("id" => $value['user_id']);
				$dbResult[$key]['detail'] = $this->BasicQuery->selectAll('user',$user_cond);

				$pay_cond = array("id" => $value['payment_id']);
				$dbResult[$key]['payment'] = $this->BasicQuery->selectAll('payment',$pay_cond);

				if ($dbResult[$key]['payment']['status'] == 0) {
					$dbResult[$key]['confirmed'] = 'registered';
				}else if ($dbResult[$key]['payment']['status'] == 1) {
					$dbResult[$key]['confirmed'] = 'paid';
				}else if ($dbResult[$key]['payment']['status'] == 2) {
					$dbResult[$key]['confirmed'] = 'confirmed';
				}else{
					$dbResult[$key]['confirmed'] = 'declined';
				}

			}

			$this->success('berhasil', $dbResult);

		}else if ($dataReceived['ihateapple'] == 'event_gallery') {

			$event_id = $dataReceived['event_id'];

			$galleryCond = array('event_id' => $event_id, 'status' => 1);
			$dbResult['gallery_list'] = $this->BasicQuery->selectAllResult('event_gallery',$galleryCond);

			$this->success('berhasil', $dbResult);

		}else if ($dataReceived['ihateapple'] == 'myevent') {

			// prepare data
			$user_id = $dataReceived['user_id'];

			// get course member
			$eventMemberCond = array('user_id' => $user_id, 'confirmed' => 1);
			$dbResult = $this->BasicQuery->selectAllResult('event_member',$eventMemberCond);

			// get data event
			foreach ($dbResult as $key => $value) {
				$eventCond = array('id' => $value['event_id']);
				$dbResult[$key]['event'] = $this->BasicQuery->selectAll('event',$eventCond);

				// get course yang terdaftar pada event
				// slect bridge terlebih dahulu
				$bridgeCond = array('event_id' => $value['event_id']);
				$bridgeDB = $this->BasicQuery->selectAllResult('bridge_event_course',$bridgeCond);

				foreach ($bridgeDB as $bridgeKey => $bridgeValue) {
					$courseCond = array('id' => $bridgeValue['course_id']);
					$dbResult[$key]['event']['course'][$bridgeKey] = $this->BasicQuery->selectAll('course',$courseCond);

					// get mentor
					$userCond = array('id' => $dbResult[$key]['event']['course'][$bridgeKey]['mentor_id'],'role_id' => AS_MENTOR);
					$dbResult[$key]['event']['course'][$bridgeKey]['mentor'] = $this->BasicQuery->selectAll('user',$userCond);
				}

				
			}
			
			$this->success('berhasil', $dbResult);

		}else if ($dataReceived['ihateapple'] == 'myevent_room') {

			// prepare data
			$user_id = $dataReceived['user_id'];
			$event_id = $dataReceived['event_id'];
			$course_id = $dataReceived['course_id']; // yg diklik
			$chapter_id = '';
			$single_chapter = false;

			// ngecek apakah minta 1 chapter aja atau list chapter
			if (isset($dataReceived['single_chapter']) && isset($dataReceived['chapter_id'])) {
				if ($dataReceived['single_chapter'] == true) {
					$single_chapter = true;
					$chapter_id = $dataReceived['chapter_id'];
				}
			}

			// cek apakah user sudah approved
			$eventMemberCond = array('user_id' => $user_id, "event_id" => $event_id);
			$dbResult['event_member'] = $this->BasicQuery->selectAll('event_member',$eventMemberCond);
			if ($dbResult['event_member']['confirmed'] == 1) {

				$courseCond = array('id' => $course_id);
				$dbResult['course_detail'] = $this->BasicQuery->selectAll('course',$courseCond);

				// list/semua chapter
				$chapterCond = array('course_id' => $course_id);

				// single chapter
				if ($single_chapter == true) {
					$chapterCond = array('course_id' => $course_id, 'id' => $chapter_id);
				}
				
				$dbResult['course_chapter'] = $this->BasicQuery->selectAllResult('course_chapter',$chapterCond);

				$userCond = array('id' => $dbResult['course_detail']['mentor_id'],'role_id' => AS_MENTOR);
				$dbResult['mentor'] = $this->BasicQuery->selectAll('user',$userCond);

				$this->success('berhasil', $dbResult);

			}else{
				$this->failed('User not approved by admin',$dbResult);
			}

		}else if ($dataReceived['ihateapple'] == 'assignment_all') {

			// prepare data
			$course_id = $dataReceived['course_id'];
			$data=array();

			/* DATA YANG DIPERLUKAN = ASSIGNMENT DARI COURSE BIASA DAN EVENT */

			// select dulu semua course member 
			$course_member_cond = array('course_id' => $course_id);
			$db_coursemember = $this->BasicQuery->selectAllResult('course_member', $course_member_cond);

			// info user
			foreach ($db_coursemember as $key => $value) {
				$data['registered_user']['basic'][$key] = $this->BasicQuery->selectAll('user', array('id' => $value['user_id']));

				$assignmentCond = array(	'user_id' => $value['user_id'],
											'course_id' => $course_id,
											'event_id' => NULL		
											);
				$data['registered_user']['basic'][$key]['assignment'] = $this->BasicQuery->selectAll('user_assignment', $assignmentCond);
			}

			// select dulu semua event member 
			$bridgeCond = array('course_id' => $course_id);
			$bridge = $this->BasicQuery->selectAllResult('bridge_event_course',$bridgeCond);

			$list_event = array();
			foreach ($bridge as $key => $value) {
				$event_member_cond = array('event_id' => $value['event_id']);
				$db_eventmember = $this->BasicQuery->selectAllResult('event_member', $event_member_cond);

				foreach ($db_eventmember as $keyeventmember => $valueeventmember) {
					$data['registered_user']['event'][$key]['user'][$keyeventmember] = $this->BasicQuery->selectAll('user', array('id' => $valueeventmember['user_id']));

					$assignmentCond = array(	'user_id' => $valueeventmember['user_id'],
												'course_id' => $course_id,
												'event_id' => $valueeventmember['event_id']		
											);
					$data['registered_user']['event'][$key]['user'][$keyeventmember]['assignment'] = $this->BasicQuery->selectAll('user_assignment', $assignmentCond);
				}

				$data['registered_user']['event'][$key]['info'] = $this->BasicQuery->selectAll('event', array('id' => $value['event_id']));
			}

			$this->success('berhasil', $data);

		}else if ($dataReceived['ihateapple'] == 'cert_status') {

			// prepare data
			$user_id = $dataReceived['user_id'];
			$event_id = $dataReceived['event_id'];
			$course_id = $dataReceived['course_id']; 

			// cek
			$cert_cond = array(	"user_id" => $user_id,
								"course_id" => $course_id,
								"event_id" => $event_id
							);
			$dbResult = $this->BasicQuery->selectAll("user_certificate", $cert_cond);

			$this->success('berhasil', $dbResult);

		}else if ($dataReceived['ihateapple'] == 'cert_status_ver2') {

			// prepare data
			$user_id = $dataReceived['user_id'];
			$event_id = $dataReceived['event_id'];
			$course_id = $dataReceived['course_id']; 

			// cek
			$cert_cond = array(	"user_id" => $user_id,
								"course_id" => $course_id,
								"event_id" => $event_id
							);
			$dbResult = $this->BasicQuery->selectAll("user_certificate", $cert_cond);

			

			if ($dbResult == null) {
				$this->failed('failed',$dbResult);
			}else{
				if ($dbResult['status'] == ACTIVE) {
					$this->success('success', $dbResult);
				}else{
					$this->failed('failed',$dbResult);
				}
				
			}

		}else if ($dataReceived['ihateapple'] == 'zoom_event') {

			// prepare data
			$event_id = $dataReceived['event_id'];

			// cek
			$zoom_cond = array(	
								"event_id" => $event_id
							);

			$count_zoom = $this->BasicQuery->countAllResult('zoom_meetings',$zoom_cond);
			if ($count_zoom == 0) {
				$this->failed('no data',null);
			}else{
				$dbResult = $this->BasicQuery->selectAll("zoom_meetings", $zoom_cond);
				$this->success('success', $dbResult['join_url']);
			}

		}else if ($dataReceived['ihateapple'] == 'zoom_event_alldata') {

			// prepare data
			$event_id = $dataReceived['event_id'];

			// cek
			$zoom_cond = array(	
								"event_id" => $event_id
							);

			$count_zoom = $this->BasicQuery->countAllResult('zoom_meetings',$zoom_cond);
			if ($count_zoom == 0) {
				$this->success('no data',null);
			}else{
				$dbResult = $this->BasicQuery->selectAll("zoom_meetings", $zoom_cond);
				$this->success('success', $dbResult);
			}

		}

		
	}

	public function success($message, $content = null){
		$obj=new stdClass;
		$obj->status = 200;
		$obj->proc = 'true';
		$obj->message = $message;
		$obj->data = $content;

		echo (json_encode($obj));
	}

	public function failed($message, $content = null){
		$obj=new stdClass;
		$obj->status = 500;
		$obj->proc = 'false';
		$obj->message = $message;
		$obj->data = $content;
		
		echo (json_encode($obj));
	}

	
}

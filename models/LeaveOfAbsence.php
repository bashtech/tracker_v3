<?php

class LeaveOfAbsence extends Application {

	public $member_id;
	public $date_end;
	public $reason;
	public $approved;
	public $approved_by;
	public $comment;
	public $game_id;

	static $table = 'loa';
	static $id_field = 'member_id';

	public static function findAll($game_id) {
		return self::find(array('game_id' => $game_id));
	}

	public static function count_expired($gid) {
		return count(self::find(array("date_end <" => 'NOW()', 'game_id' => $gid)));
	}

	public static function count_pending($gid) {
		return count(self::find(array('game_id' => $gid, 'approved' => 0)));
	}

	public static function _create() {

		$id = $_POST['id'];
		$member_id = $member_info['forum_id'];

		$data = NULL;
		$date = date('Y-m-d', strtotime($_POST['date']));
		$reason = $_POST['reason'];
		$comment = htmlentities($_POST['comment'], ENT_QUOTES);
		$name = get_member_name($id);

		// validate member id and get name
		if ($name != false) {
			if (strtotime($date) > strtotime('now')) {
			// validate submission
				if ( $loa = ( addLoa($id, $date, $reason, $comment) ) ) {
				// if submission failed
					if ( $loa['success'] == false ) {
						$data = array('success' => false, 'message' => $loa['message']);
					} else {
						$data = array('success' => true, 'Request successfully submitted!', 'id' => $id, 'name' => $name, 'date' => date('M d, Y', strtotime($date)), 'reason' => $reason);
					}
				} else {
					$data = array('success' => false, 'message' => $loa['message']);
				}
			} else {
				$data = array('success' => false, 'message' => "Date cannot be before today's date.");
			}
		} else {
			$data = array('success' => false, 'message' => 'Invalid member id');
		}
	}


	public static function remove() {

		$id = $_POST['id'];
		$member_id = $member_info['forum_id'];

		if ($userRole < 2) {
			$data = array('success' => false, 'message' => "You are not authorized to perform that action.");
		} else {
			// revoking an LOA
			$id = $_POST['id'];
			if ( $revoked = ( revoke_loa($id) ) ) {
				if ( $revoked['success'] == false ) {
					$data = array('success' => false, 'message' => $revoked['message']);
				} else {
					$data = array('success' => true, 'message' => "Leave of absence successfully removed.");
				}
			}

		}
	}

	public static function approve() {

		$id = $_POST['id'];
		$member_id = $member_info['forum_id'];

		if ($userRole < 2) {
			$data = array('success' => false, 'message' => "You are not authorized to perform that action.");
		} else {
			// is LOA member id the same as user member id?
			if ($member_id != $id) {
				if ( $approved = approve_loa($id, $member_id) ) {
					$data = array('success' => true, 'message' => "Leave of absence successfully approved.");
				} else {
					$data = array('success' => false, 'message' => $loa['message']);
				}
			} else {
				$data = array('success' => false, 'message' => 'You can\'t approve your own leave of absence!');
			}

		}
	}

}


/*





// removing loa
if (isset($_POST['remove'])) {
	
// approving an LOA
} else if (isset($_POST['approve'])) {
	if ($userRole < 2) {
		$data = array('success' => false, 'message' => "You are not authorized to perform that action.");

	} else {
		// is LOA member id the same as user member id?
		if ($member_id != $id) {
			if ( $approved = approve_loa($id, $member_id) ) {
				$data = array('success' => true, 'message' => "Leave of absence successfully approved.");
			} else {
				$data = array('success' => false, 'message' => $loa['message']);
			}
		} else {
			$data = array('success' => false, 'message' => 'You can\'t approve your own leave of absence!');
		}

	}

// adding an LOA	
} else {

	$data = NULL;
	$date = date('Y-m-d', strtotime($_POST['date']));
	$reason = $_POST['reason'];
	$comment = htmlentities($_POST['comment'], ENT_QUOTES);
	$name = get_member_name($id);

		// validate member id and get name
	if ($name != false) {
		if (strtotime($date) > strtotime('now')) {
			// validate submission
			if ( $loa = ( addLoa($id, $date, $reason, $comment) ) ) {
				// if submission failed
				if ( $loa['success'] == false ) {
					$data = array('success' => false, 'message' => $loa['message']);
				} else {
					$data = array('success' => true, 'Request successfully submitted!', 'id' => $id, 'name' => $name, 'date' => date('M d, Y', strtotime($date)), 'reason' => $reason);
				}
			} else {
				$data = array('success' => false, 'message' => $loa['message']);
			}
		} else {
			$data = array('success' => false, 'message' => "Date cannot be before today's date.");
		}
	} else {
		$data = array('success' => false, 'message' => 'Invalid member id');
	}
}


echo json_encode($data);*/

<?php
$accessToken = "5LUYgpAMCZCXCjV1icPuEe/owEeB09pZE6ehutRvFZR1lnm2ENTzheQp1tTsbHTQ9CVze0kd42rup9heu/r4swt4RC+gGJQ07HUEYyqU0LgKiWCICWpa8/70NxlSJw5+qrMWUEG5QECKd9oVHaYMQgdB04t89/1O/w1cDnyilFU=";//copy Channel access token ตอนที่ตั้งค่ามาใส่
// Get POST body content
$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);
if (!is_null($events['events'])) {
	// Loop through each event
	foreach ($events['events'] as $event) {
		// Reply only when message sent is in 'text' format
		if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
			// Get text sent
		 	$text = $event['message']['text'];				
			//Get Group ID
			$groupId = $event['source']['groupId'];
			//Get User ID
			$userId = $event['source']['userId'];
			// Get replyToken
			$replyToken = $event['replyToken'];
            $status = "true";
			// Build message to reply back
			$server = 'us-cdbr-iron-east-05.cleardb.net';
			$username = 'b809e2f36f0522';
			$password = '01a9a1f5';
			$db = 'heroku_a0500905d74bead';
			$pdo = new PDO("mysql:host=$server;dbname=$db", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));  
			$Select_Status = "SELECT * FROM bot_speak WHERE bot_groupid = :groupId";
			$Query_Status = $pdo->prepare($Select_Status);
			$Query_Status->execute(Array(
				":groupId" => $groupId
			));
			$rowCount = $Query_Status->rowCount();
				if($rowCount >= 1){
					$Fetch_Status = $Query_Status->fetch(PDO::FETCH_ASSOC);
					if($groupId != '' && $userId != ''){
						if(strpos($text, 'สวัสดี') !== false || strpos($text, 'โย่') !== false){
							$headers_gp = array('Authorization: Bearer ' . $access_token);
				           	$url_gp = 'https://api.line.me/v2/bot/group/'.$groupId.'/member/'.$userId.'';
		                    $ch_gp = curl_init($url_gp);
		                    curl_setopt($ch_gp, CURLOPT_RETURNTRANSFER, true);
		     	            curl_setopt($ch_gp, CURLOPT_HTTPHEADER, $headers_gp);
		                    curl_setopt($ch_gp, CURLOPT_FOLLOWLOCATION, 1);
		                    $result_gp = curl_exec($ch_gp);
				           	$result_decode = json_decode($result_gp);
		                    curl_close($ch_gp);
				           	$Name = $result_decode->displayName;
				           	if($userId == 'U72c641a79b2f1a785a7b362df99931ae'){
				           		$Display_Name = "โฟร์ท";
				           		$messages = [
				           			[
				           				'type' => 'text',
				           				'text' => 'สวัสดีครับ'.$Display_Name
				           			],
				           			[
				           				'type' => 'text',
				           				'text' => 'มีอะไรให้รับใช้ครับ'
				           			]
				           		];
				           	}
						}else if($reply != ''){
							$messages = [
				           			[
				           				'type' => 'text',
				           				'text' => $reply
				           			]
				           	];
						}else if($text == 'รายงานสภาพอากาศวันนี้'){

						}else{

						}
					}else{
						// User -> Bot
					}
				}else{
					if($text == 'Start Jake'){
						$Update_Status = "UPDATE bot_speak SET bot_status = 'true' WHERE bot_groupid = :group_id";
						$Query_Update = $pdo->prepare($Update_Status);
						$Query_Update->execute(Array(
							":group_id" => $groupId
						));
						$messages = [
		           			[
		           				'type' => 'text',
		           				'text' => 'กำลังเปิดระบบ...'
		           			],
		           			[
		           				'type' => 'text',
		           				'text' => 'Jake กลับมาแล้วครับ'
		        			]
				        ];
					}else if(strpos($text, "Jake พูด") !== false || strpos($text, "Jakeพูด") !== false){
						$Update_Status = "UPDATE bot_speak SET bot_status = 'true' WHERE bot_groupid = :group_id";
						$Query_Update = $pdo->prepare($Update_Status);
						$Query_Update->execute(Array(
							":group_id" => $groupId
						));
						$messages = [
		           			[
		           				'type' => 'text',
		           				'text' => 'กำลังเปิดระบบ...'
		           			],
		           			[
		           				'type' => 'text',
		           				'text' => 'Jake กลับมาแล้วครับ'
		        			]
				        ];
					}
				}
			}else{
				$Insert_Status = "INSERT INTO `bot_speak` (`idbot_speak`, `bot_status`, `bot_groupid`) VALUES (:ID, :bot_status, :bot_groupid);";
				$Query_Insert = $pdo->prepare($Insert_Status);
				$Query_Insert->execute(Array(
					":ID" => NULL,
					":bot_status" => $status,
					":bot_groupid" => $groupId
				));
			}
			// Make a POST Request to Messaging API to reply to sender
			$url = 'https://api.line.me/v2/bot/message/reply';
			$data = [
				'replyToken' => $replyToken,
				'messages' => [$messages][0],
			];
			$post = json_encode($data);
			$headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			$result = curl_exec($ch);
			curl_close($ch);
			echo $result . "\r\n";
		}
	}
}
echo "OK";
?>
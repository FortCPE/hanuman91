<?php
$access_token = '5LUYgpAMCZCXCjV1icPuEe/owEeB09pZE6ehutRvFZR1lnm2ENTzheQp1tTsbHTQ9CVze0kd42rup9heu/r4swt4RC+gGJQ07HUEYyqU0LgKiWCICWpa8/70NxlSJw5+qrMWUEG5QECKd9oVHaYMQgdB04t89/1O/w1cDnyilFU=';
// Get POST body content
$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);

date_default_timezone_set("Asia/Bangkok");
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
            $server = 'us-cdbr-iron-east-03.cleardb.net';
            $username = 'b1b05596d11b27';
            $password = '42f8f3ba';
            $db = 'heroku_aa26b13d6c49109';
            $pdo = new PDO("mysql:host=$server;dbname=$db", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")); 

            $query_connection = $pdo->prepare("SELECT * FROM bot_status WHERE group_id = :group_id");
            $query_connection->execute(Array(
                ":group_id" => $groupId
            ));
            $rowCount = $query_connection->rowCount();
            if($rowCount >= 1){
                $date = date('d/m/Y');

                $var_date = date('Y-m-d'); // Query ออกมาได้เลยครับ

                $thai_day_arr=array("อาทิตย์","จันทร์","อังคาร","พุธ","พฤหัสบดี","ศุกร์","เสาร์");
                function thai_date($time){
                 global $thai_day_arr;
                 $thai_date_return="วัน".$thai_day_arr[date("w",$time)];
                 return $thai_date_return;
                }

                $var_date=strtotime("$var_date"); 
                $var_date= thai_date($var_date);
                $fetch_connection = $query_connection->fetch(PDO::FETCH_ASSOC);
                if($fetch_connection['status'] == 'true'){
                    if($groupId != '' && $userId != ''){
                        if($text == 'Bot Shutdown'){
                            $Update_Status = "UPDATE bot_status SET status = 'false' WHERE group_id = :group_id";
                            $Query_Update = $pdo->prepare($Update_Status);
                            $Query_Update->execute(Array(
                                ":group_id" => $groupId
                            ));
                            $messages = [
                                    [
                                        'type' => 'text',
                                        'text' => '[System] กำลังทำการปิดตัวเอง...'
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => '[System] ปิดระบบแล้วครับ'
                                    ]
                            ];
                        }else if(strpos($text, "ลงเวลาเรียน@") !== false){
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
                            if(strpos($text, "ลงเวลาเรียน@") !== false){
                                $result_val = explode("@", $text);
                            }
                            $query_check = $pdo->prepare("SELECT * FROM `bot_customer` WHERE `user_id` = :user_id");
                            $query_check->execute(Array(
                                ":user_id" => $userId
                            ));
                            $row_check = $query_check->rowCount();
                                $delete_connection = $pdo->prepare("DELETE FROM `bot_customer` WHERE `user_id` = :user_id AND `today` = :today");
                                $delete_connection->execute(Array(
                                    ":user_id" => $userId,
                                    ":today" => date("Y-m-d")
                                ));
                                if($result_val[1] == "09"){
                                    $insert_connection = $pdo->prepare("INSERT INTO `bot_customer` (`id`, `time`, `user_id`, `today`) VALUES (:id, :time_today, :user_id, :today)");
                                    $insert_connection->execute(Array(
                                        ":id" => NULL,
                                        ":time_today" => "9:30",
                                        ":user_id" => $userId,
                                        ":today" => date("Y-m-d")
                                    ));
                                }else if($result_val[1] == "11"){
                                    $insert_connection = $pdo->prepare("INSERT INTO `bot_customer` (`id`, `time`, `user_id`, `today`) VALUES (:id, :time_today, :user_id, :today)");
                                    $insert_connection->execute(Array(
                                        ":id" => NULL,
                                        ":time_today" => "11:00",
                                        ":user_id" => $userId,
                                        ":today" => date("Y-m-d")
                                    ));
                                }else if($result_val[1] == "15"){
                                    $insert_connection = $pdo->prepare("INSERT INTO `bot_customer` (`id`, `time`, `user_id`, `today`) VALUES (:id, :time_today, :user_id, :today)");
                                    $insert_connection->execute(Array(
                                        ":id" => NULL,
                                        ":time_today" => "15:00",
                                        ":user_id" => $userId,
                                        ":today" => date("Y-m-d")
                                    ));
                                }else if($result_val[1] == "16"){
                                    $insert_connection = $pdo->prepare("INSERT INTO `bot_customer` (`id`, `time`, `user_id`, `today`) VALUES (:id, :time_today, :user_id, :today)");
                                    $insert_connection->execute(Array(
                                        ":id" => NULL,
                                        ":time_today" => "16:30",
                                        ":user_id" => $userId,
                                        ":today" => date("Y-m-d")
                                    ));
                                }else if($result_val[1] == "18"){
                                    $insert_connection = $pdo->prepare("INSERT INTO `bot_customer` (`id`, `time`, `user_id`, `today`) VALUES (:id, :time_today, :user_id, :today)");
                                    $insert_connection->execute(Array(
                                        ":id" => NULL,
                                        ":time_today" => "18:00",
                                        ":user_id" => $userId,
                                        ":today" => date("Y-m-d")
                                    ));
                                }else if($result_val[1] == "19"){
                                    $insert_connection = $pdo->prepare("INSERT INTO `bot_customer` (`id`, `time`, `user_id`, `today`) VALUES (:id, :time_today, :user_id, :today)");
                                    $insert_connection->execute(Array(
                                        ":id" => NULL,
                                        ":time_today" => "19:30",
                                        ":user_id" => $userId,
                                        ":today" => date("Y-m-d")
                                    ));
                                }
                                $messages = [
                                    [
                                        'type' => 'text',
                                        'text' => '[@] ขอชื่อผู้จองหน่อยค่ะ'
                                    ]
                                ];
                        }else if(strpos($text, "ยกเลิก") !== false){
                            $delete_connection = $pdo->prepare("DELETE FROM `bot_customer` WHERE `user_id` = :user_id AND `today` = :today");
                            $delete_connection->execute(Array(
                                ":user_id" => $userId,
                                ":today" => date("Y-m-d")
                            ));
                            $query_connection = $pdo->prepare("SELECT * FROM bot_customer WHERE `time` = '9:30' AND `today` = :today");
                            $query_connection->execute(Array(
                                ":today" => date("Y-m-d")
                            ));
                            while ($fetch_connection = $query_connection->fetch(PDO::FETCH_ASSOC)) {
                                $text_first .= ' '.$fetch_connection['name'];
                            }
                            $query_connection = $pdo->prepare("SELECT * FROM bot_customer WHERE `time` = '11:00' AND `today` = :today");
                            $query_connection->execute(Array(
                                ":today" => date("Y-m-d")
                            ));
                            while ($fetch_connection = $query_connection->fetch(PDO::FETCH_ASSOC)) {
                                $text_second .= ' '.$fetch_connection['name'];
                            }
                            $query_connection = $pdo->prepare("SELECT * FROM bot_customer WHERE `time` = '15:00' AND `today` = :today");
                            $query_connection->execute(Array(
                                ":today" => date("Y-m-d")
                            ));
                            while ($fetch_connection = $query_connection->fetch(PDO::FETCH_ASSOC)) {
                                $text_third .= ' '.$fetch_connection['name'];
                            }
                            $query_connection = $pdo->prepare("SELECT * FROM bot_customer WHERE `time` = '16:30' AND `today` = :today");
                            $query_connection->execute(Array(
                                ":today" => date("Y-m-d")
                            ));
                            while ($fetch_connection = $query_connection->fetch(PDO::FETCH_ASSOC)) {
                                $text_fouth .= ' '.$fetch_connection['name'];
                            }
                            $query_connection = $pdo->prepare("SELECT * FROM bot_customer WHERE `time` = '18:00' AND `today` = :today");
                            $query_connection->execute(Array(
                                ":today" => date("Y-m-d")
                            ));
                            while ($fetch_connection = $query_connection->fetch(PDO::FETCH_ASSOC)) {
                                $text_fifth .= ' '.$fetch_connection['name'];
                            }
                            $query_connection = $pdo->prepare("SELECT * FROM bot_customer WHERE `time` = '19:30' AND `today` = :today");
                            $query_connection->execute(Array(
                                ":today" => date("Y-m-d")
                            ));
                            while ($fetch_connection = $query_connection->fetch(PDO::FETCH_ASSOC)) {
                                $text_sixth .= ' '.$fetch_connection['name'];
                            }
                            $messages = [
                                [
                                    'type' => 'text',
                                    'text' => 'จองเวลาเรียนผ่านไลน์นี้ได้เลยนะคะ
อัพเดต'.$var_date.' '.$date.'
09:30'.$text_first.'
11:00'.$text_second.'
15:00'.$text_third.'
16:30'.$text_fouth.'
18:00'.$text_fifth.'
19:30'.$text_sixth.'
**เพื่อความสะดวกสบายของสมาชิกโปรดจองเวลาเรียนก่อนเข้าใช้บริการทุกครั้ง*จองเวลาเรียนผ่านไลน์นี้ได้เลยนะคะ
ขอสงวนสิทธิ์ตามลำดับการจองก่อนหลังนะคะ'
                                ]
                            ];
                        }else if(strpos($text, "จอง") !== false || strpos($text, "9:30") !== false || strpos($text, "09:30") !== false || strpos($text, "9.30") !== false || strpos($text, "09.30") !== false || strpos($text, "0930") !== false || strpos($text, "930") !== false || strpos($text, "11:00") !== false || strpos($text, "11.00") !== false || strpos($text, "15:00") !== false || strpos($text, "15.00") !== false || strpos($text, "16:30") !== false || strpos($text, "16.30") !== false || strpos($text, "18.00") !== false || strpos($text, "18:00") !== false || strpos($text, "19:30") !== false || strpos($text, "19.30") !== false || strpos($text, "1930") !== false || strpos($text, "1800") !== false || strpos($text, "1630") !== false || strpos($text, "1500") !== false || strpos($text, "1100") !== false){
                            $messages = [
                                [
                                      "type" => "template",
                                      "altText" => "this is a carousel template",
                                      "template" => [
                                          "type" => "carousel",
                                          "columns" => [
                                              [
                                                "thumbnailImageUrl" => "https://hanuman91.herokuapp.com/boxing.jpeg",
                                                "imageBackgroundColor" => "#FFFFFF",
                                                "title" => "จองเวลาเรียน",
                                                "text" => "ช่วงเช้า-บ่าย",
                                                "actions" => [
                                                    [
                                                        "type" => "message",
                                                        "label" => "เวลา 09:30 น.",
                                                        "text" => "ลงเวลาเรียน@09"
                                                    ],
                                                    [
                                                        "type" => "message",
                                                        "label" => "เวลา 11:00 น.",
                                                        "text" => "ลงเวลาเรียน@11"
                                                    ],
                                                    [
                                                        "type" => "message",
                                                        "label" => "เวลา 15:00 น.",
                                                        "text" => "ลงเวลาเรียน@15"
                                                    ]
                                                ]
                                              ],
                                              [
                                                "thumbnailImageUrl" => "https://hanuman91.herokuapp.com/boxing.jpeg",
                                                "imageBackgroundColor" => "#000000",
                                                "title" => "จองเวลาเรียน",
                                                "text" => "ช่วงเย็น-ค่ำ",
                                                "actions" => [
                                                    [
                                                        "type" => "message",
                                                        "label" => "เวลา 16:30 น.",
                                                        "text" => "ลงเวลาเรียน@16"
                                                    ],
                                                    [
                                                        "type" => "message",
                                                        "label" => "เวลา 18:00 น.",
                                                        "text" => "ลงเวลาเรียน@18"
                                                    ],
                                                    [
                                                        "type" => "message",
                                                        "label" => "เวลา 19:30 น.",
                                                        "text" => "ลงเวลาเรียน@19"
                                                    ]
                                                ]
                                              ]
                                          ],
                                          "imageAspectRatio" => "rectangle",
                                          "imageSize" => "cover"
                                      ]
                                ]
                            ];  
                        }else{
                            $check_name = $pdo->prepare("SELECT * FROM bot_customer WHERE user_id = :user_id AND today = :today");
                            $check_name->execute(Array(
                                ":user_id" => $userId,
                                ":today" => date("Y-m-d")
                            ));
                            if($check_name->rowCount() == 1){
                                $fetch_check = $check_name->fetch(PDO::FETCH_ASSOC);
                                if($fetch_check['name'] == "" || $fetch_check['name'] == null){
                                    $update_name = $pdo->prepare("UPDATE `bot_customer` SET `name` = :name WHERE `user_id` = :user_id");
                                    $result = $update_name->execute(Array(
                                        ":name" => $text,
                                        ":user_id" => $userId
                                    ));
                                    if($result){
                                        $query_connection = $pdo->prepare("SELECT * FROM bot_customer WHERE `time` = '9:30' AND `today` = :today");
                                        $query_connection->execute(Array(
                                            ":today" => date("Y-m-d")
                                        ));
                                        while ($fetch_connection = $query_connection->fetch(PDO::FETCH_ASSOC)) {
                                            $text_first .= ' '.$fetch_connection['name'];
                                        }
                                        $query_connection = $pdo->prepare("SELECT * FROM bot_customer WHERE `time` = '11:00' AND `today` = :today");
                                        $query_connection->execute(Array(
                                            ":today" => date("Y-m-d")
                                        ));
                                        while ($fetch_connection = $query_connection->fetch(PDO::FETCH_ASSOC)) {
                                            $text_second .= ' '.$fetch_connection['name'];
                                        }
                                        $query_connection = $pdo->prepare("SELECT * FROM bot_customer WHERE `time` = '15:00' AND `today` = :today");
                                        $query_connection->execute(Array(
                                            ":today" => date("Y-m-d")
                                        ));
                                        while ($fetch_connection = $query_connection->fetch(PDO::FETCH_ASSOC)) {
                                            $text_third .= ' '.$fetch_connection['name'];
                                        }
                                        $query_connection = $pdo->prepare("SELECT * FROM bot_customer WHERE `time` = '16:30' AND `today` = :today");
                                        $query_connection->execute(Array(
                                            ":today" => date("Y-m-d")
                                        ));
                                        while ($fetch_connection = $query_connection->fetch(PDO::FETCH_ASSOC)) {
                                            $text_fouth .= ' '.$fetch_connection['name'];
                                        }
                                        $query_connection = $pdo->prepare("SELECT * FROM bot_customer WHERE `time` = '18:00' AND `today` = :today");
                                        $query_connection->execute(Array(
                                            ":today" => date("Y-m-d")
                                        ));
                                        while ($fetch_connection = $query_connection->fetch(PDO::FETCH_ASSOC)) {
                                            $text_fifth .= ' '.$fetch_connection['name'];
                                        }
                                        $query_connection = $pdo->prepare("SELECT * FROM bot_customer WHERE `time` = '19:30' AND `today` = :today");
                                        $query_connection->execute(Array(
                                            ":today" => date("Y-m-d")
                                        ));
                                        while ($fetch_connection = $query_connection->fetch(PDO::FETCH_ASSOC)) {
                                            $text_sixth .= ' '.$fetch_connection['name'];
                                        }
                                        $messages = [
                                            [
                                                'type' => 'text',
                                                'text' => 'จองเวลาเรียนผ่านไลน์นี้ได้เลยนะคะ
อัพเดต'.$var_date.' '.$date.'
09:30'.$text_first.'
11:00'.$text_second.'
15:00'.$text_third.'
16:30'.$text_fouth.'
18:00'.$text_fifth.'
19:30'.$text_sixth.'
**เพื่อความสะดวกสบายของสมาชิกโปรดจองเวลาเรียนก่อนเข้าใช้บริการทุกครั้ง*จองเวลาเรียนผ่านไลน์นี้ได้เลยนะคะ
ขอสงวนสิทธิ์ตามลำดับการจองก่อนหลังนะคะ'
                                            ]
                                        ];
                                    }
                                }
                            }
                        }
                    }else{
                        
                    }

                }else{
                    if($text == 'Bot Start'){
                        $Update_Status = "UPDATE bot_status SET status = 'true' WHERE group_id = :group_id";
                        $Query_Update = $pdo->prepare($Update_Status);
                        $Query_Update->execute(Array(
                            ":group_id" => $groupId
                        ));
                        $messages = [
                            [
                                'type' => 'text',
                                'text' => '[System] กำลังเปิดระบบ...'
                            ],
                            [
                                'type' => 'text',
                                'text' => '[System] พร้อมทำงาน'
                            ]
                        ];
                    }
                }
            }else{
                $Insert_Status = "INSERT INTO `bot_status` (`id`, `status`, `group_id`) VALUES (:id, :status, :group_id);";
                $Query_Insert = $pdo->prepare($Insert_Status);
                $Query_Insert->execute(Array(
                    ":id" => NULL,
                    ":status" => $status,
                    ":group_id" => $groupId
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

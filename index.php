<?php
$access_token = '5LUYgpAMCZCXCjV1icPuEe/owEeB09pZE6ehutRvFZR1lnm2ENTzheQp1tTsbHTQ9CVze0kd42rup9heu/r4swt4RC+gGJQ07HUEYyqU0LgKiWCICWpa8/70NxlSJw5+qrMWUEG5QECKd9oVHaYMQgdB04t89/1O/w1cDnyilFU=';
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
                            $Display_Name = "โฟร์ท";
                            $messages = [
                                [
                                    'type' => 'text',
                                    'text' => 'สวัสดีครับ '.$Name
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'มีอะไรให้รับใช้ครับ'
                                ]
                            ];
                        }else if($text == 'Bot Shutdown'){
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
                        }else if(strpos($text, "จอง") !== false){
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
                            $result_val = explode("จอง", $text);
                            $query_check = $pdo->prepare("SELECT * FROM `bot_customer` WHERE `user_id` = :user_id");
                            $query_check->execute(Array(
                                ":user_id" => $userId
                            ));
                            $row_check = $query_check->rowCount();
                            if($row_check >= 1){
                                $delete_connection = $pdo->prepare("DELETE FROM `bot_customer` WHERE `user_id` = :user_id");
                                $delete_connection->execute(Array(
                                    ":user_id" => $userId
                                ));
                            }else{
                                if($result_val[1] == "09:30" || $result_val[1] == "9:30" || 
                                   $result_val[1] == " 09:30" || $result_val[1] == " 9:30"
                                ){
                                    $insert_connection = $pdo->prepare("INSERT INTO `bot_customer` (`id`, `name`, `time`, `user_id`) VALUES (:id, :name, :time_today, :user_id)");
                                    $insert_connection->execute(Array(
                                        ":id" => NULL,
                                        ":name" => $result_val[0],
                                        ":time_today" => "9:30",
                                        ":user_id" => $userId
                                    ));
                                }else if($result_val[1] == "11:00" || $result_val[1] == " 11:00"
                                ){
                                    $insert_connection = $pdo->prepare("INSERT INTO `bot_customer` (`id`, `name`, `time`, `user_id`) VALUES (:id, :name, :time_today, :user_id)");
                                    $insert_connection->execute(Array(
                                        ":id" => NULL,
                                        ":name" => $result_val[0],
                                        ":time_today" => "11:00",
                                        ":user_id" => $userId
                                    ));
                                }else if($result_val[1] == "15:00" || $result_val[1] == " 15:00"
                                ){
                                    $insert_connection = $pdo->prepare("INSERT INTO `bot_customer` (`id`, `name`, `time`, `user_id`) VALUES (:id, :name, :time_today, :user_id)");
                                    $insert_connection->execute(Array(
                                        ":id" => NULL,
                                        ":name" => $result_val[0],
                                        ":time_today" => "15:00",
                                        ":user_id" => $userId
                                    ));
                                }else if($result_val[1] == "16:30" || $result_val[1] == " 16:30"
                                ){
                                    $insert_connection = $pdo->prepare("INSERT INTO `bot_customer` (`id`, `name`, `time`, `user_id`) VALUES (:id, :name, :time_today, :user_id)");
                                    $insert_connection->execute(Array(
                                        ":id" => NULL,
                                        ":name" => $result_val[0],
                                        ":time_today" => "16:30",
                                        ":user_id" => $userId
                                    ));
                                }else if($result_val[1] == "18:00" || $result_val[1] == " 18:00"
                                ){
                                    $insert_connection = $pdo->prepare("INSERT INTO `bot_customer` (`id`, `name`, `time`, `user_id`) VALUES (:id, :name, :time_today, :user_id)");
                                    $insert_connection->execute(Array(
                                        ":id" => NULL,
                                        ":name" => $result_val[0],
                                        ":time_today" => "18:00",
                                        ":user_id" => $userId
                                    ));
                                }else if($result_val[1] == "19:30" || $result_val[1] == " 19:30"
                                ){
                                    $insert_connection = $pdo->prepare("INSERT INTO `bot_customer` (`id`, `name`, `time`, `user_id`) VALUES (:id, :name, :time_today, :user_id)");
                                    $insert_connection->execute(Array(
                                        ":id" => NULL,
                                        ":name" => $result_val[0],
                                        ":time_today" => "19:30",
                                        ":user_id" => $userId
                                    ));
                                }
                            }
                            
                            
                            $query_connection = $pdo->prepare("SELECT * FROM bot_customer WHERE `time` = '9:30'");
                            $query_connection->execute();
                            while ($fetch_connection = $query_connection->fetch(PDO::FETCH_ASSOC)) {
                                $text_first .= ' '.$fetch_connection['name'];
                            }
                            $query_connection = $pdo->prepare("SELECT * FROM bot_customer WHERE `time` = '11:00'");
                            $query_connection->execute();
                            while ($fetch_connection = $query_connection->fetch(PDO::FETCH_ASSOC)) {
                                $text_second .= ' '.$fetch_connection['name'];
                            }
                            $query_connection = $pdo->prepare("SELECT * FROM bot_customer WHERE `time` = '15:00'");
                            $query_connection->execute();
                            while ($fetch_connection = $query_connection->fetch(PDO::FETCH_ASSOC)) {
                                $text_third .= ' '.$fetch_connection['name'];
                            }
                            $query_connection = $pdo->prepare("SELECT * FROM bot_customer WHERE `time` = '16:30'");
                            $query_connection->execute();
                            while ($fetch_connection = $query_connection->fetch(PDO::FETCH_ASSOC)) {
                                $text_fouth .= ' '.$fetch_connection['name'];
                            }
                            $query_connection = $pdo->prepare("SELECT * FROM bot_customer WHERE `time` = '18:00'");
                            $query_connection->execute();
                            while ($fetch_connection = $query_connection->fetch(PDO::FETCH_ASSOC)) {
                                $text_fifth .= ' '.$fetch_connection['name'];
                            }
                            $query_connection = $pdo->prepare("SELECT * FROM bot_customer WHERE `time` = '19:30'");
                            $query_connection->execute();
                            while ($fetch_connection = $query_connection->fetch(PDO::FETCH_ASSOC)) {
                                $text_sixth .= ' '.$fetch_connection['name'];
                            }
                            $messages = [
                                [
                                    'type' => 'text',
                                    'text' => 'จองเวลาเรียนผ่านไลน์นี้ได้เลยนะคะ
อัพเดต'.$var_date.' '.$date.'
09.30'.$text_first.'
11:00'.$text_second.'
15.00'.$text_third.'
16.30'.$text_fouth.'
18.00'.$text_fifth.'
19:30'.$text_sixth.'
**เพื่อความสะดวกสบายของสมาชิกโปรดจองเวลาเรียนก่อนเข้าใช้บริการทุกครั้ง*จองเวลาเรียนผ่านไลน์นี้ได้เลยนะคะ
ขอสงวนสิทธิ์ตามลำดับการจองก่อนหลังนะคะ'
                                ]
                            ];
                        }else if(strpos($text, "เมนู") !== false || strpos($text, "menu") !== false){
                            $messages = [
                                [
                                      "type" => "template",
                                      "altText" => "this is a carousel template",
                                      "template" => [
                                          "type" => "carousel",
                                          "columns" => [
                                              [
                                                "thumbnailImageUrl" => "https://codesign-studio.in.th/img/jake.jpg",
                                                "imageBackgroundColor" => "#FFFFFF",
                                                "title" => "ตัวเลือก",
                                                "text" => "กรุณาเลือก",
                                                "actions" => [
                                                    [
                                                        "type" => "message",
                                                        "label" => "Jake คืออะไร",
                                                        "text" => "นายเป็นใคร"
                                                    ],
                                                    [
                                                        "type" => "message",
                                                        "label" => "สอน Jake ยังไง",
                                                        "text" => "วิธีสอน Jake"
                                                    ],
                                                    [
                                                        "type" => "message",
                                                        "label" => "สภาพอากาศของวันนี้",
                                                        "text" => "รายงานสภาพอากาศวันนี้"
                                                    ]
                                                ]
                                              ],
                                              [
                                                "thumbnailImageUrl" => "https://codesign-studio.in.th/img/jake2.jpg",
                                                "imageBackgroundColor" => "#000000",
                                                "title" => "ตัวเลือก",
                                                "text" => "กรุณาเลือก",
                                                "actions" => [
                                                    [
                                                        "type" => "message",
                                                        "label" => "พยากรณ์อากาศวันพรุ่งนี้",
                                                        "text" => "ช่วยพยากรณ์ที"
                                                    ],
                                                    [
                                                        "type" => "message",
                                                        "label" => "จำนวนเบอร์เพื่อนๆ",
                                                        "text" => "เบอร์เพื่อนทั้งหมด"
                                                    ],
                                                    [
                                                        "type" => "message",
                                                        "label" => "ราคาทองวันนี้",
                                                        "text" => "ราคาทองวันนี้ กรุณารอ 5-10 วินาที"
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
                            if(strpos($text, 'บ้า')){
                               $reply = "ใครบ้า";
                            }else if(strpos($text, 'มึง')){
                               $reply = "พูดจาหยาบคายนะมึง";    
                            }else if(strpos($text, 'กู')){
                               $reply = "พูดไม่เพราะเลย";   
                            }else if(strpos($text, 'ฝันดี')){
                               $reply = "ฝันดีเหมือนกันนะ";
                            }else{
                             $numbers = range(1, 12);
                            shuffle($numbers);
                            foreach ($numbers as $number) {
                                if($number == 1){
                                    $reply = "จริงหรอจ้ะ";
                                }else if($number == 2){
                                    $reply = "ใช่หรอ";
                                }else if($number == 3){
                                    $reply = "ไม่รู้";
                                }else if($number == 4){
                                    $reply = "อ๋อๆ";
                                }else if($number == 5){
                                    $reply = "โอเคๆ";
                                }else if($number == 6){
                                    $reply = "แล้วแต่เลย";
                                }else if($number == 7){
                                    $reply = "ไม่รู้ว้อย";
                                }else if($number == 8){
                                    $reply = "เจ้กรักทุกคนนะ";
                                }else if($number == 9){
                                    $reply = "เจ้ก คิดถึงฟินจัง ._.";
                                }else if($number == 10){
                                    $reply = "หิวว้อยยย";
                                }else if($number == 11){
                                    $reply = "ต้องการคนดูแล";
                                }else if($number == 12){
                                    $reply = "อิอิ";
                                } 
                            }
                            }
                            $messages = [
                                    [
                                        'type' => 'text',
                                        'text' => $reply
                                    ]
                            ];
                        }
                    }else{
                        if(strpos($text, 'สวัสดี') !== false || strpos($text, 'โย่') !== false || strpos($text, 'เห้') !== false){
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
                            }else{
                                $messages = [
                                    [
                                        'type' => 'text',
                                        'text' => 'สวัสดีครับ'
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => 'มีอะไรให้รับใช้ครับ'
                                    ]
                                ];
                            }
                        }
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

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
                if($Fetch_Status['bot_status'] == 'true'){
                $Select_Train = "SELECT * FROM bot_train WHERE textbot_train = :text_bot AND group_id = :group_id";
                $Query_Train = $pdo->prepare($Select_Train);
                $Query_Train->execute(Array(
                    ":text_bot" => $text,
                    ":group_id" => $groupId
                ));
                $Count_Train = $Query_Train->rowCount() - 1;
                $i = 0;
                $nums = range($i, $Count_Train);
                shuffle($nums);
                foreach ($nums as $num) {
                   $Fetch_Train[] = $Query_Train->fetch(PDO::FETCH_ASSOC);
                   $reply = $Fetch_Train[$num]['replybot_train'];
                }
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
                        
                    }else if($reply != ''){
                        $messages = [
                                [
                                    'type' => 'text',
                                    'text' => $reply
                                ]
                        ];
                    }else if($text == 'รายงานสภาพอากาศวันนี้'){
                        
                    }else if(strpos($text, 'อากาศ') !== false){
                        if(strpos($text, 'กรุงเทพ') !== false){
                            $temp_url = "https://query.yahooapis.com/v1/public/yql?format=json&q=select+%2A+from+weather.forecast+where+woeid%3D1225448";
                            $temp = curl_init();  
                            curl_setopt($temp,CURLOPT_URL,$temp_url);
                            curl_setopt($temp,CURLOPT_RETURNTRANSFER,true);                          
                            $output=curl_exec($temp);
                            curl_close($temp);
                            $temp_result = json_decode($output);
                            $Cel = ($temp_result->query->results->channel->item->condition->temp-32)*5/9;
                            $messages = [
                                [
                                    'type' => 'text',
                                    'text' => 'วันนี้ : '.date('d/m/Y H:i:s',strtotime($temp_result->query->results->channel->lastBuildDate))
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'สถานที่ : '.$temp_result->query->results->channel->location->city
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'อุณหภูมิอยู่ที่ : '.(int)($Cel).' องศา'
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'สภาพอากาศ : '.$temp_result->query->results->channel->item->condition->text
                                ]
                            ];
                        }else if(strpos($text, 'นนทบุรี') !== false){
                            $temp_url = "https://query.yahooapis.com/v1/public/yql?format=json&q=select+%2A+from+weather.forecast+where+woeid%3D1226072";
                            $temp = curl_init();  
                            curl_setopt($temp,CURLOPT_URL,$temp_url);
                            curl_setopt($temp,CURLOPT_RETURNTRANSFER,true);                          
                            $output=curl_exec($temp);
                            curl_close($temp);
                            $temp_result = json_decode($output);
                            $Cel = ($temp_result->query->results->channel->item->condition->temp-32)*5/9;
                            $messages = [
                                [
                                    'type' => 'text',
                                    'text' => 'วันนี้ : '.date('d/m/Y H:i:s',strtotime($temp_result->query->results->channel->lastBuildDate))
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'สถานที่ : '.$temp_result->query->results->channel->location->city
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'อุณหภูมิอยู่ที่ : '.(int)($Cel).' องศา'
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'สภาพอากาศ : '.$temp_result->query->results->channel->item->condition->text
                                ]
                            ];
                        }else if(strpos($text, 'นครนายก') !== false){
                            $temp_url = "https://query.yahooapis.com/v1/public/yql?format=json&q=select+%2A+from+weather.forecast+where+woeid%3D90501154";
                            $temp = curl_init();  
                            curl_setopt($temp,CURLOPT_URL,$temp_url);
                            curl_setopt($temp,CURLOPT_RETURNTRANSFER,true);                          
                            $output=curl_exec($temp);
                            curl_close($temp);
                            $temp_result = json_decode($output);
                            $Cel = ($temp_result->query->results->channel->item->condition->temp-32)*5/9;
                            $messages = [
                                [
                                    'type' => 'text',
                                    'text' => 'วันนี้ : '.date('d/m/Y H:i:s',strtotime($temp_result->query->results->channel->lastBuildDate))
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'สถานที่ : '.$temp_result->query->results->channel->location->city
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'อุณหภูมิอยู่ที่ : '.(int)($Cel).' องศา'
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'สภาพอากาศ : '.$temp_result->query->results->channel->item->condition->text
                                ]
                            ];
                        }else if(strpos($text, 'กาญจนบุรี') !== false){
                            $temp_url = "https://query.yahooapis.com/v1/public/yql?format=json&q=select+%2A+from+weather.forecast+where+woeid%3D1225985";
                            $temp = curl_init();  
                            curl_setopt($temp,CURLOPT_URL,$temp_url);
                            curl_setopt($temp,CURLOPT_RETURNTRANSFER,true);                          
                            $output=curl_exec($temp);
                            curl_close($temp);
                            $temp_result = json_decode($output);
                            $Cel = ($temp_result->query->results->channel->item->condition->temp-32)*5/9;
                            $messages = [
                                [
                                    'type' => 'text',
                                    'text' => 'วันนี้ : '.date('d/m/Y H:i:s',strtotime($temp_result->query->results->channel->lastBuildDate))
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'สถานที่ : '.$temp_result->query->results->channel->location->city
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'อุณหภูมิอยู่ที่ : '.(int)($Cel).' องศา'
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'สภาพอากาศ : '.$temp_result->query->results->channel->item->condition->text
                                ]
                            ];
                        }else if(strpos($text, 'ราชบุรี') !== false){
                            $temp_url = "https://query.yahooapis.com/v1/public/yql?format=json&q=select+%2A+from+weather.forecast+where+woeid%3D1225614";
                            $temp = curl_init();  
                            curl_setopt($temp,CURLOPT_URL,$temp_url);
                            curl_setopt($temp,CURLOPT_RETURNTRANSFER,true);                          
                            $output=curl_exec($temp);
                            curl_close($temp);
                            $temp_result = json_decode($output);
                            $Cel = ($temp_result->query->results->channel->item->condition->temp-32)*5/9;
                            $messages = [
                                [
                                    'type' => 'text',
                                    'text' => 'วันนี้ : '.date('d/m/Y H:i:s',strtotime($temp_result->query->results->channel->lastBuildDate))
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'สถานที่ : '.$temp_result->query->results->channel->location->city
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'อุณหภูมิอยู่ที่ : '.(int)($Cel).' องศา'
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'สภาพอากาศ : '.$temp_result->query->results->channel->item->condition->text
                                ]
                            ];
                        }else if(strpos($text, 'ประจวบคีรีขันธ์') !== false){
                            $temp_url = "https://query.yahooapis.com/v1/public/yql?format=json&q=select+%2A+from+weather.forecast+where+woeid%3D1226118";
                            $temp = curl_init();  
                            curl_setopt($temp,CURLOPT_URL,$temp_url);
                            curl_setopt($temp,CURLOPT_RETURNTRANSFER,true);                          
                            $output=curl_exec($temp);
                            curl_close($temp);
                            $temp_result = json_decode($output);
                            $Cel = ($temp_result->query->results->channel->item->condition->temp-32)*5/9;
                            $messages = [
                                [
                                    'type' => 'text',
                                    'text' => 'วันนี้ : '.date('d/m/Y H:i:s',strtotime($temp_result->query->results->channel->lastBuildDate))
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'สถานที่ : '.$temp_result->query->results->channel->location->city
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'อุณหภูมิอยู่ที่ : '.(int)($Cel).' องศา'
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'สภาพอากาศ : '.$temp_result->query->results->channel->item->condition->text
                                ]
                            ];
                        }else if(strpos($text, 'เชียงราย') !== false){
                            $temp_url = "https://query.yahooapis.com/v1/public/yql?format=json&q=select+%2A+from+weather.forecast+where+woeid%3D1225134";
                            $temp = curl_init();  
                            curl_setopt($temp,CURLOPT_URL,$temp_url);
                            curl_setopt($temp,CURLOPT_RETURNTRANSFER,true);                          
                            $output=curl_exec($temp);
                            curl_close($temp);
                            $temp_result = json_decode($output);
                            $Cel = ($temp_result->query->results->channel->item->condition->temp-32)*5/9;
                            $messages = [
                                [
                                    'type' => 'text',
                                    'text' => 'วันนี้ : '.date('d/m/Y H:i:s',strtotime($temp_result->query->results->channel->lastBuildDate))
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'สถานที่ : '.$temp_result->query->results->channel->location->city
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'อุณหภูมิอยู่ที่ : '.(int)($Cel).' องศา'
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'สภาพอากาศ : '.$temp_result->query->results->channel->item->condition->text
                                ]
                            ];
                        }else if(strpos($text, 'เชียงใหม่') !== false){
                            $temp_url = "https://query.yahooapis.com/v1/public/yql?format=json&q=select+%2A+from+weather.forecast+where+woeid%3D1225955";
                            $temp = curl_init();  
                            curl_setopt($temp,CURLOPT_URL,$temp_url);
                            curl_setopt($temp,CURLOPT_RETURNTRANSFER,true);                          
                            $output=curl_exec($temp);
                            curl_close($temp);
                            $temp_result = json_decode($output);
                            $Cel = ($temp_result->query->results->channel->item->condition->temp-32)*5/9;
                            $messages = [
                                [
                                    'type' => 'text',
                                    'text' => 'วันนี้ : '.date('d/m/Y H:i:s',strtotime($temp_result->query->results->channel->lastBuildDate))
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'สถานที่ : '.$temp_result->query->results->channel->location->city
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'อุณหภูมิอยู่ที่ : '.(int)($Cel).' องศา'
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'สภาพอากาศ : '.$temp_result->query->results->channel->item->condition->text
                                ]
                            ];
                        }else if(strpos($text, 'น่าน') !== false){
                            $temp_url = "https://query.yahooapis.com/v1/public/yql?format=json&q=select+%2A+from+weather.forecast+where+woeid%3D1226063";
                            $temp = curl_init();  
                            curl_setopt($temp,CURLOPT_URL,$temp_url);
                            curl_setopt($temp,CURLOPT_RETURNTRANSFER,true);                          
                            $output=curl_exec($temp);
                            curl_close($temp);
                            $temp_result = json_decode($output);
                            $Cel = ($temp_result->query->results->channel->item->condition->temp-32)*5/9;
                            $messages = [
                                [
                                    'type' => 'text',
                                    'text' => 'วันนี้ : '.date('d/m/Y H:i:s',strtotime($temp_result->query->results->channel->lastBuildDate))
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'สถานที่ : '.$temp_result->query->results->channel->location->city
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'อุณหภูมิอยู่ที่ : '.(int)($Cel).' องศา'
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'สภาพอากาศ : '.$temp_result->query->results->channel->item->condition->text
                                ]
                            ];
                        }else{
                        $messages = [
                            [
                                  "type" => "template",
                                  "altText" => "this is a carousel template",
                                  "template" => [
                                      "type" => "carousel",
                                      "columns" => [
                                          [
                                            "thumbnailImageUrl" => "https://codesign-studio.in.th/img/cold.jpg",
                                            "imageBackgroundColor" => "#FFFFFF",
                                            "title" => "กรุณาเลือกจังหวัด",
                                            "text" => "ภาคกลาง",
                                            "actions" => [
                                                [
                                                    "type" => "message",
                                                    "label" => "กรุงเทพมหานคร",
                                                    "text" => "รายงานสภาพอากาศของกรุงเทพ"
                                                ],
                                                [
                                                    "type" => "message",
                                                    "label" => "นนทบุรี",
                                                    "text" => "รายงานสภาพอากาศของนนทบุรี"
                                                ],
                                                [
                                                    "type" => "message",
                                                    "label" => "นครนายก",
                                                    "text" => "รายงานสภาพอากาศของนครนายก"
                                                ]
                                            ]
                                          ],
                                          [
                                            "thumbnailImageUrl" => "https://codesign-studio.in.th/img/rainy.jpg",
                                            "imageBackgroundColor" => "#000000",
                                            "title" => "กรุณาเลือกจังหวัด",
                                            "text" => "ภาคตะวันตก",
                                            "actions" => [
                                                [
                                                    "type" => "message",
                                                    "label" => "กาญจนบุรี",
                                                    "text" => "รายงานสภาพอากาศของกาญจนบุรี"
                                                ],
                                                [
                                                    "type" => "message",
                                                    "label" => "ราชบุรี",
                                                    "text" => "รายงานสภาพอากาศของราชบุรี"
                                                ],
                                                [
                                                    "type" => "message",
                                                    "label" => "ประจวบคีรีขันธ์",
                                                    "text" => "รายงานสภาพอากาศของประจวบคีรีขันธ์"
                                                ]
                                            ]
                                          ],
                                          [
                                            "thumbnailImageUrl" => "https://codesign-studio.in.th/img/sunny.jpg",
                                            "imageBackgroundColor" => "#FFFFFF",
                                            "title" => "กรุณาเลือกจังหวัด",
                                            "text" => "ภาคเหนือ",
                                            "actions" => [
                                                [
                                                    "type" => "message",
                                                    "label" => "เชียงราย",
                                                    "text" => "รายงานสภาพอากาศของเชียงราย"
                                                ],
                                                [
                                                    "type" => "message",
                                                    "label" => "เชียงใหม่",
                                                    "text" => "รายงานสภาพอากาศของเชียงใหม่"
                                                ],
                                                [
                                                    "type" => "message",
                                                    "label" => "น่าน",
                                                    "text" => "รายงานสภาพอากาศของน่าน"
                                                ]
                                            ]
                                          ]
                                      ],
                                      "imageAspectRatio" => "rectangle",
                                      "imageSize" => "cover"
                                  ]
                            ]
                        ];                          
                        }

                    }else if(strpos($text, 'add:') !== false){
                        $phone = explode(":", $text);
                        $Insert_phone = "INSERT INTO `bot_phone` (`idbot_phone`, `num_botphone`, `owner_botphone`, `group_id`) VALUES (:ID, :num_botphone, :owner_botphone, :group_id);";
                        $Query_phone = $pdo->prepare($Insert_phone);
                        $Query_phone->execute(Array(
                            ":ID" => NULL,
                            ":num_botphone" => $phone[2],
                            ":owner_botphone" => $phone[1],
                            ":group_id" => $groupId
                        ));
                        $messages = [
                            [
                               "type"=>"text", 
                               "text"=>"เพิ่มเบอร์ของคุณ ".$phone[1]." แล้วครับ"
                            ],
                            [
                               "type"=>"text", 
                               "text"=>"ยินดีให้บริการครับ"
                            ]
                        ];
                    }else if(strpos($text, 'remove:') !== false){
                        $phone = explode(":", $text);
                        $Delete_phone = "DELETE FROM `bot_phone` WHERE `owner_botphone` = :owner_botphone AND `group_id` = :group_id";
                        $Query_phone = $pdo->prepare($Delete_phone);
                        $Query_phone->execute(Array(
                            ":owner_botphone" => $phone[1],
                            ":group_id" => $groupId
                        ));
                        $messages = [
                            [
                               "type"=>"text", 
                               "text"=>"ลบเบอร์ของคุณ ".$phone[1]." แล้วครับ"
                            ],
                            [
                               "type"=>"text", 
                               "text"=>"ยินดีให้บริการครับ"
                            ]
                        ];
                    }else if(strpos($text, 'ขอเบอร์') !== false || strpos($text, 'เบอร์') !== false || strpos($text, 'เบอ') !== false){
                        $Select_Phone = "SELECT * FROM bot_phone WHERE group_id = :group_id";
                        $Query_Phone = $pdo->prepare($Select_Phone);
                        $Query_Phone->execute(Array(
                            ":group_id" => $groupId
                        ));
                        if(strpos($text, 'ทั้งหมด') !== false){
                            $messages = [
                                [
                                   "type"=>"text", 
                                   "text"=>"เบอร์ทั้งหมด : ".$Query_Phone->rowCount(). " เบอร์"
                                ],
                                [
                                   "type"=>"text", 
                                   "text"=>"เพิ่มเบอร์ (add:ชื่อ:เบอร์)"
                                ],
                                [
                                   "type"=>"text", 
                                   "text"=>"ลบเบอร์ (remove:ชื่อ)"
                                ]
                            ];
                        }else{
                            while ($Fetch_Phone = $Query_Phone->fetch(PDO::FETCH_ASSOC)) {
                                if(strpos($text, $Fetch_Phone['owner_botphone']) !== false){
                                $messages = [
                                    [
                                       "type"=>"text", 
                                       "text"=>"เบอร์ของคุณ ".$Fetch_Phone['owner_botphone']." คือ ".$Fetch_Phone['num_botphone']
                                    ],
                                    [
                                       "type"=>"text", 
                                       "text"=>"ยินดีให้บริการครับ"
                                    ]
                                ];
                                }                       
                            }
                        }
                    }else if($text == 'Shutdown Jake'){
                        $Update_Status = "UPDATE bot_speak SET bot_status = 'false' WHERE bot_groupid = :group_id";
                        $Query_Update = $pdo->prepare($Update_Status);
                        $Query_Update->execute(Array(
                            ":group_id" => $groupId
                        ));
                        $messages = [
                                [
                                    'type' => 'text',
                                    'text' => 'กำลังทำการปิดตัวเอง'
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'Jake ไปละนะครับ ไว้เจอกันใหม่'
                                ]
                        ];
                    }else if(strpos($text, "Jake เงียบ") !== false || strpos($text, "Jakeเงียบ") !== false || strpos($text, "Jake หุบ") !== false || strpos($text, "Jake หยุด") !== false || strpos($text, "หุบ") !== false){
                        $Update_Status = "UPDATE bot_speak SET bot_status = 'false' WHERE bot_groupid = :group_id";
                        $Query_Update = $pdo->prepare($Update_Status);
                        $Query_Update->execute(Array(
                            ":group_id" => $groupId
                        ));
                        $messages = [
                                [
                                    'type' => 'text',
                                    'text' => 'กำลังทำการปิดตัวเอง'
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'Jake ไปละนะครับ ไว้เจอกันใหม่'
                                ]
                        ];
                    }else if(strpos($text, 'train:') !== false){
                        $train = explode(":", $text);
                        $Insert_train = "INSERT INTO bot_train (idbot_train, textbot_train, replybot_train, trainer_id, group_id) VALUES (:idbot_train, :textbot_train, :replybot_train, :trainer_id, :group_id); ";
                        $Query_Insert = $pdo->prepare($Insert_train);
                        $Query_Insert->execute(Array(
                            ":idbot_train" => NULL,
                            ":textbot_train" => $train[1],
                            ":replybot_train" => $train[2],
                            ":trainer_id" => $userId,
                            ":group_id" => $groupId
                        ));
                        $messages = [
                                [
                                    'type' => 'text',
                                    'text' => 'เรียนรู้คำนี้แล้วครับ'
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'ขอบคุณที่สอนนะครับ'
                                ]
                        ];
                    }else if(strpos($text, 'delete:') !== false){
                        $delete_text = explode(":", $text);
                        $Delete_train = "DELETE FROM `bot_train` WHERE `textbot_train` = :textbot_train AND `trainer_id` = :trainer_id AND `group_id` = :group_id";
                        $Query_Delete = $pdo->prepare($Delete_train);
                        $Query_Delete->execute(Array(
                            ":textbot_train" => $delete_text[1],
                            ":trainer_id" => $userId,
                            ":group_id" => $groupId
                        ));
                        $messages = [
                                [
                                    'type' => 'text',
                                    'text' => 'ลบการสอนแล้วครับ'
                                ]
                        ];
                    }else if(strpos($text, "แนะนำตัว") !== false){
                        $messages = [
                                [
                                    'type' => 'text',
                                    'text' => 'สวัสดีครับผมชื่อ Jake'
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'ผมเป็นบอทเอาไว้พูดคุยเล่น และ อำนวยความสะดวก'
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'ผมสามารถเรียนรู้คำได้จากคุณ, จดจำเบอร์มือถือ และ รายงานสภาพอากาศได้ด้วย'
                                ]
                        ];
                    }else if(strpos($text, "template") !== false){
                        $messages = [
                                [
                                  "type"=> "template",
                                  "altText"=> "this is a confirm template",
                                  "template" => [
                                      "type" => "confirm",
                                      "text" => "Are you sure?",
                                      "actions" => [
                                          [
                                            "type" => "message",
                                            "label" => "Yes",
                                            "text" => "yes"
                                          ],
                                          [
                                            "type" => "message",
                                            "label" => "No",
                                            "text" => "no"
                                          ]
                                      ]
                                    ]
                                ]
                        ];
                    }else if(strpos($text, "วิธีสอน") !== false){
                        $messages = [
                                [
                                    'type' => 'text',
                                    'text' => 'วิธีสอน Jake'
                                ],
                                [
                                    'type' => 'text',
                                    'text' => '(เรียนรู้คำ) train:คำที่ส่ง:คำที่ตอบกลับ'
                                ],
                                [
                                    'type' => 'text',
                                    'text' => '(ลบคำ) delete:คำที่ส่ง'
                                ]
                        ];
                    }else if(strpos($text, "พยากรณ์") !== false){
                        if(strpos($text, 'กรุงเทพ') !== false){
                            $temp_url = "https://query.yahooapis.com/v1/public/yql?format=json&q=select+%2A+from+weather.forecast+where+woeid%3D1225448";
                            $temp = curl_init();  
                            curl_setopt($temp,CURLOPT_URL,$temp_url);
                            curl_setopt($temp,CURLOPT_RETURNTRANSFER,true);                          
                            $output=curl_exec($temp);
                            curl_close($temp);
                            $temp_result = json_decode($output);
                            $Cel = ($temp_result->query->results->channel->item->condition->temp-32)*5/9;
                            foreach($temp_result->query->results->channel->item->forecast as $value){
                                $minCel = ($value->low-32)*5/9;
                                $maxCel = ($value->high-32)*5/9;
                                $messages = [
                                    [
                                        'type' => 'text',
                                        'text' => 'วันที่ : '.$value->date
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => 'อุณหภูมิต่ำสุด : '.(int)$minCel.' องศา'
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => 'อุณหภูมิสูงสุด : '.(int)($maxCel).' องศา'
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => 'สภาพอากาศ : '.$value->text
                                    ]
                                ];
                            }
                            
                        }else if(strpos($text, 'นนทบุรี') !== false){
                            $temp_url = "https://query.yahooapis.com/v1/public/yql?format=json&q=select+%2A+from+weather.forecast+where+woeid%3D1226072";
                            $temp = curl_init();  
                            curl_setopt($temp,CURLOPT_URL,$temp_url);
                            curl_setopt($temp,CURLOPT_RETURNTRANSFER,true);                          
                            $output=curl_exec($temp);
                            curl_close($temp);
                            $temp_result = json_decode($output);
                            $Cel = ($temp_result->query->results->channel->item->condition->temp-32)*5/9;
                            foreach($temp_result->query->results->channel->item->forecast as $value){
                                $minCel = ($value->low-32)*5/9;
                                $maxCel = ($value->high-32)*5/9;
                                $messages = [
                                    [
                                        'type' => 'text',
                                        'text' => 'วันที่ : '.$value->date
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => 'อุณหภูมิต่ำสุด : '.(int)$minCel.' องศา'
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => 'อุณหภูมิสูงสุด : '.(int)($maxCel).' องศา'
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => 'สภาพอากาศ : '.$value->text
                                    ]
                                ];
                            }
                        }else if(strpos($text, 'นครนายก') !== false){
                            $temp_url = "https://query.yahooapis.com/v1/public/yql?format=json&q=select+%2A+from+weather.forecast+where+woeid%3D90501154";
                            $temp = curl_init();  
                            curl_setopt($temp,CURLOPT_URL,$temp_url);
                            curl_setopt($temp,CURLOPT_RETURNTRANSFER,true);                          
                            $output=curl_exec($temp);
                            curl_close($temp);
                            $temp_result = json_decode($output);
                            $Cel = ($temp_result->query->results->channel->item->condition->temp-32)*5/9;
                            foreach($temp_result->query->results->channel->item->forecast as $value){
                                $minCel = ($value->low-32)*5/9;
                                $maxCel = ($value->high-32)*5/9;
                                $messages = [
                                    [
                                        'type' => 'text',
                                        'text' => 'วันที่ : '.$value->date
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => 'อุณหภูมิต่ำสุด : '.(int)$minCel.' องศา'
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => 'อุณหภูมิสูงสุด : '.(int)($maxCel).' องศา'
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => 'สภาพอากาศ : '.$value->text
                                    ]
                                ];
                            }
                        }else if(strpos($text, 'กาญจนบุรี') !== false){
                            $temp_url = "https://query.yahooapis.com/v1/public/yql?format=json&q=select+%2A+from+weather.forecast+where+woeid%3D1225985";
                            $temp = curl_init();  
                            curl_setopt($temp,CURLOPT_URL,$temp_url);
                            curl_setopt($temp,CURLOPT_RETURNTRANSFER,true);                          
                            $output=curl_exec($temp);
                            curl_close($temp);
                            $temp_result = json_decode($output);
                            $Cel = ($temp_result->query->results->channel->item->condition->temp-32)*5/9;
                            foreach($temp_result->query->results->channel->item->forecast as $value){
                                $minCel = ($value->low-32)*5/9;
                                $maxCel = ($value->high-32)*5/9;
                                $messages = [
                                    [
                                        'type' => 'text',
                                        'text' => 'วันที่ : '.$value->date
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => 'อุณหภูมิต่ำสุด : '.(int)$minCel.' องศา'
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => 'อุณหภูมิสูงสุด : '.(int)($maxCel).' องศา'
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => 'สภาพอากาศ : '.$value->text
                                    ]
                                ];
                            }
                        }else if(strpos($text, 'ราชบุรี') !== false){
                            $temp_url = "https://query.yahooapis.com/v1/public/yql?format=json&q=select+%2A+from+weather.forecast+where+woeid%3D1225614";
                            $temp = curl_init();  
                            curl_setopt($temp,CURLOPT_URL,$temp_url);
                            curl_setopt($temp,CURLOPT_RETURNTRANSFER,true);                          
                            $output=curl_exec($temp);
                            curl_close($temp);
                            $temp_result = json_decode($output);
                            $Cel = ($temp_result->query->results->channel->item->condition->temp-32)*5/9;
                            foreach($temp_result->query->results->channel->item->forecast as $value){
                                $minCel = ($value->low-32)*5/9;
                                $maxCel = ($value->high-32)*5/9;
                                $messages = [
                                    [
                                        'type' => 'text',
                                        'text' => 'วันที่ : '.$value->date
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => 'อุณหภูมิต่ำสุด : '.(int)$minCel.' องศา'
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => 'อุณหภูมิสูงสุด : '.(int)($maxCel).' องศา'
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => 'สภาพอากาศ : '.$value->text
                                    ]
                                ];
                            }
                        }else if(strpos($text, 'ประจวบคีรีขันธ์') !== false){
                            $temp_url = "https://query.yahooapis.com/v1/public/yql?format=json&q=select+%2A+from+weather.forecast+where+woeid%3D1226118";
                            $temp = curl_init();  
                            curl_setopt($temp,CURLOPT_URL,$temp_url);
                            curl_setopt($temp,CURLOPT_RETURNTRANSFER,true);                          
                            $output=curl_exec($temp);
                            curl_close($temp);
                            $temp_result = json_decode($output);
                            $Cel = ($temp_result->query->results->channel->item->condition->temp-32)*5/9;
                            foreach($temp_result->query->results->channel->item->forecast as $value){
                                $minCel = ($value->low-32)*5/9;
                                $maxCel = ($value->high-32)*5/9;
                                $messages = [
                                    [
                                        'type' => 'text',
                                        'text' => 'วันที่ : '.$value->date
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => 'อุณหภูมิต่ำสุด : '.(int)$minCel.' องศา'
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => 'อุณหภูมิสูงสุด : '.(int)($maxCel).' องศา'
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => 'สภาพอากาศ : '.$value->text
                                    ]
                                ];
                            }
                        }else if(strpos($text, 'เชียงราย') !== false){
                            $temp_url = "https://query.yahooapis.com/v1/public/yql?format=json&q=select+%2A+from+weather.forecast+where+woeid%3D1225134";
                            $temp = curl_init();  
                            curl_setopt($temp,CURLOPT_URL,$temp_url);
                            curl_setopt($temp,CURLOPT_RETURNTRANSFER,true);                          
                            $output=curl_exec($temp);
                            curl_close($temp);
                            $temp_result = json_decode($output);
                            $Cel = ($temp_result->query->results->channel->item->condition->temp-32)*5/9;
                            foreach($temp_result->query->results->channel->item->forecast as $value){
                                $minCel = ($value->low-32)*5/9;
                                $maxCel = ($value->high-32)*5/9;
                                $messages = [
                                    [
                                        'type' => 'text',
                                        'text' => 'วันที่ : '.$value->date
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => 'อุณหภูมิต่ำสุด : '.(int)$minCel.' องศา'
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => 'อุณหภูมิสูงสุด : '.(int)($maxCel).' องศา'
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => 'สภาพอากาศ : '.$value->text
                                    ]
                                ];
                            }
                        }else if(strpos($text, 'เชียงใหม่') !== false){
                            $temp_url = "https://query.yahooapis.com/v1/public/yql?format=json&q=select+%2A+from+weather.forecast+where+woeid%3D1225955";
                            $temp = curl_init();  
                            curl_setopt($temp,CURLOPT_URL,$temp_url);
                            curl_setopt($temp,CURLOPT_RETURNTRANSFER,true);                          
                            $output=curl_exec($temp);
                            curl_close($temp);
                            $temp_result = json_decode($output);
                            $Cel = ($temp_result->query->results->channel->item->condition->temp-32)*5/9;
                            foreach($temp_result->query->results->channel->item->forecast as $value){
                                $minCel = ($value->low-32)*5/9;
                                $maxCel = ($value->high-32)*5/9;
                                $messages = [
                                    [
                                        'type' => 'text',
                                        'text' => 'วันที่ : '.$value->date
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => 'อุณหภูมิต่ำสุด : '.(int)$minCel.' องศา'
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => 'อุณหภูมิสูงสุด : '.(int)($maxCel).' องศา'
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => 'สภาพอากาศ : '.$value->text
                                    ]
                                ];
                            }
                        }else if(strpos($text, 'น่าน') !== false){
                            $temp_url = "https://query.yahooapis.com/v1/public/yql?format=json&q=select+%2A+from+weather.forecast+where+woeid%3D1226063";
                            $temp = curl_init();  
                            curl_setopt($temp,CURLOPT_URL,$temp_url);
                            curl_setopt($temp,CURLOPT_RETURNTRANSFER,true);                          
                            $output=curl_exec($temp);
                            curl_close($temp);
                            $temp_result = json_decode($output);
                            $Cel = ($temp_result->query->results->channel->item->condition->temp-32)*5/9;
                            foreach($temp_result->query->results->channel->item->forecast as $value){
                                $minCel = ($value->low-32)*5/9;
                                $maxCel = ($value->high-32)*5/9;
                                $messages = [
                                    [
                                        'type' => 'text',
                                        'text' => 'วันที่ : '.$value->date
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => 'อุณหภูมิต่ำสุด : '.(int)$minCel.' องศา'
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => 'อุณหภูมิสูงสุด : '.(int)($maxCel).' องศา'
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => 'สภาพอากาศ : '.$value->text
                                    ]
                                ];
                            }
                        }else{
                        $messages = [
                            [
                                  "type" => "template",
                                  "altText" => "this is a carousel template",
                                  "template" => [
                                      "type" => "carousel",
                                      "columns" => [
                                          [
                                            "thumbnailImageUrl" => "https://codesign-studio.in.th/img/cold.jpg",
                                            "imageBackgroundColor" => "#FFFFFF",
                                            "title" => "กรุณาเลือกจังหวัด",
                                            "text" => "ภาคกลาง",
                                            "actions" => [
                                                [
                                                    "type" => "message",
                                                    "label" => "กรุงเทพมหานคร",
                                                    "text" => "พยากรณ์ของกรุงเทพ"
                                                ],
                                                [
                                                    "type" => "message",
                                                    "label" => "นนทบุรี",
                                                    "text" => "พยากรณ์ของนนทบุรี"
                                                ],
                                                [
                                                    "type" => "message",
                                                    "label" => "นครนายก",
                                                    "text" => "พยากรณ์ของนครนายก"
                                                ]
                                            ]
                                          ],
                                          [
                                            "thumbnailImageUrl" => "https://codesign-studio.in.th/img/rainy.jpg",
                                            "imageBackgroundColor" => "#000000",
                                            "title" => "กรุณาเลือกจังหวัด",
                                            "text" => "ภาคตะวันตก",
                                            "actions" => [
                                                [
                                                    "type" => "message",
                                                    "label" => "กาญจนบุรี",
                                                    "text" => "พยากรณ์ของกาญจนบุรี"
                                                ],
                                                [
                                                    "type" => "message",
                                                    "label" => "ราชบุรี",
                                                    "text" => "พยากรณ์ของราชบุรี"
                                                ],
                                                [
                                                    "type" => "message",
                                                    "label" => "ประจวบคีรีขันธ์",
                                                    "text" => "พยากรณ์ของประจวบคีรีขันธ์"
                                                ]
                                            ]
                                          ],
                                          [
                                            "thumbnailImageUrl" => "https://codesign-studio.in.th/img/sunny.jpg",
                                            "imageBackgroundColor" => "#FFFFFF",
                                            "title" => "กรุณาเลือกจังหวัด",
                                            "text" => "ภาคเหนือ",
                                            "actions" => [
                                                [
                                                    "type" => "message",
                                                    "label" => "เชียงราย",
                                                    "text" => "พยากรณ์ของเชียงราย"
                                                ],
                                                [
                                                    "type" => "message",
                                                    "label" => "เชียงใหม่",
                                                    "text" => "พยากรณ์ของเชียงใหม่"
                                                ],
                                                [
                                                    "type" => "message",
                                                    "label" => "น่าน",
                                                    "text" => "พยากรณ์ของน่าน"
                                                ]
                                            ]
                                          ]
                                      ],
                                      "imageAspectRatio" => "rectangle",
                                      "imageSize" => "cover"
                                  ]
                            ]
                        ];      
                    }

                    }else if(strpos($text, 'ราคาทองวันนี้') !== false){
                        $url = "https://www.goldtraders.or.th/";
                        $ch = curl_init($url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                        curl_setopt($ch, CURLOPT_VERBOSE, 1);
                        curl_setopt($ch, CURLOPT_HEADER, 1);
                        // ...

                        $response = curl_exec($ch);

                        // Then, after your curl_exec call:
                        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                        $header = substr($response, 0, $header_size);
                        $body = substr($response, $header_size);
                        $explode = explode(" ", $body);
                        $a = str_replace('size="6">', "", $explode[1425]);
                        $b = str_replace('size="6">', "", $explode[1481]);
                        $c = str_replace('size="6">', "", $explode[1615]);
                        $d = str_replace('size="6">', "", $explode[1671]);                      
                        $messages = [
                                    [
                                        'type' => 'text',
                                        'text' => 'วันนี้ราคารับซื้อทองคำแท่งอยู่ที่ : '.str_replace('</font></b></span>', "", $a)
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => 'วันนี้ราคาขายออกทองคำแท่งอยู่ที่ : '.str_replace('</font></b></span>', "", $b)
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => 'วันนี้ราคารับซื้อทองรูปพรรณอยู่ที่ : '.str_replace('</font></b></span>', "", $c)
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => 'วันนี้ราคาขายออกทองรูปพรรณอยู่ที่ : '.str_replace('</font></b></span>', "", $d)
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

                        // $messages = [
                        //  [
                        //        "type" => "template",
                        //        "altText" => "this is a carousel template",
                        //        "template" => [
                        //            "type" => "carousel",
                        //            "columns" => [
                        //                [
                        //                  "thumbnailImageUrl" => "https://codesign-studio.in.th/img/jake.jpg",
                        //                  "imageBackgroundColor" => "#FFFFFF",
                        //                  "title" => "ตัวเลือก",
                        //                  "text" => "กรุณาเลือก",
                        //                  "actions" => [
                        //                        [
                        //                          "type" => "message",
                        //                          "label" => "Jake คืออะไร",
                        //                          "text" => "นายเป็นใคร"
                        //                        ],
                        //                        [
                        //                          "type" => "message",
                        //                          "label" => "สอน Jake ยังไง",
                        //                          "text" => "วิธีสอน Jake"
                        //                        ],
                        //                        [
                        //                          "type" => "message",
                        //                          "label" => "สภาพอากาศของวันนี้",
                        //                          "text" => "รายงานสภาพอากาศวันนี้"
                        //                        ]
                        //                  ]
                        //                ],
                        //                [
                        //                  "thumbnailImageUrl" => "https://codesign-studio.in.th/img/jake.jpg",
                        //                  "imageBackgroundColor" => "#000000",
                        //                  "title" => "ตัวเลือก",
                        //                  "text" => "กรุณาเลือก",
                        //                  "actions" => [
                        //                      [
                        //                          "type" => "message",
                        //                          "label" => "จำนวนเบอร์เพื่อนๆ",
                        //                          "text" => "เบอร์เพื่อนทั้งหมด"
                        //                      ],
                        //                      [
                        //                          "type" => "message",
                        //                          "label" => "พยากรณ์อากาศ",
                        //                          "text" => "ช่วยพยากรณ์อากาศที"
                        //                      ]
                        //                  ]
                        //                ]
                        //            ],
                        //            "imageAspectRatio" => "rectangle",
                        //            "imageSize" => "cover"
                        //        ]
                        //  ]
                        // ];       

                    }else if(strpos($text, 'นายเป็นใคร') !== false){
                        $messages = [
                                [
                                    'type' => 'text',
                                    'text' => 'สวัสดีครับผมชื่อ Jake'
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'ผมเป็นบอทเอาไว้พูดคุยเล่น และ อำนวยความสะดวก'
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'ผมสามารถเรียนรู้คำได้จากคุณ, จดจำเบอร์มือถือ และ รายงานสภาพอากาศได้ด้วย'
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
                // $delete_status = "DELETE FROM `bot_speak` WHERE `bot_groupid` = :id";
                // $query_delete = $pdo->prepare($delete_status);
                // $query_delete->execute(Array(
                //  ":id" => $groupId
                // ));
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

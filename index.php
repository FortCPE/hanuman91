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

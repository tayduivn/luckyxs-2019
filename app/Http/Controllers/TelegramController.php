<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use App\Models\Channel;
use App\Models\BetType;
use App\Models\Bet;
use App\Models\Message;
use App\User;
use Exception;
use Auth, Session;
class TelegramController extends Controller
{
    private $channelList;
    private $channelListKey;
    private $arrExpert = ['dp', 'dc', '2d'];
    private $channelByDay = 
    [
        '1' => [
            'tp',
            'dt',
            'cm'
        ],
        '2' => [
            'bt',
            'vt',
            'bli'
        ],
        '3' => [
            'dn',
            'ct',
            'st'
        ],
        '4' => [
            'tn',
            'ag',
            'bth'
        ],
        '5' => [
            'vl',
            'bd',
            'tv'
        ],
        '6' => [
            'tp',
            'la',
            'bp',
            'hg'
        ],
        '7' => [
            'tg',
            'kg',
            'dl'
        ],

    ];
    private $betTypeList;
    private $betTypeListKey;

    /**
     * Show the profile for the given user.
     *
     * @param  
     * @return View
     */
    public function __construct(){
        $this->channelList = Channel::pluck('code', 'id')->toArray();
        $this->channelListKey = array_flip($this->channelList); 
        $this->betTypeList = BetType::pluck('keyword', 'id')->toArray();   
        $this->betTypeListKey = array_flip($this->betTypeList);    
    }
    public function __invoke()
    {
        $config = [
            'telegram' => config('botman.telegram')
        ];
        // Load the driver(s) you want to use
        DriverManager::loadDriver(\BotMan\Drivers\Telegram\TelegramDriver::class);
        // Create an instance
        $botman = BotManFactory::create($config);
        // Give the bot something to listen for.
        $botman->hears('/pass', function (BotMan $bot) {
            $bot->reply('Bạn Anh Hoàng.');
        });

        $botman->hears('/hi', function (BotMan $bot) {
            $bot->reply('Hi cc.');
        });

        $botman->hears('((.|\n)*)', function (BotMan $bot, $message) {
            
            try{
                $userId = $bot->getUser()->getId();

                $userExists = User::where('tel_id', '=', $userId)->count() >= 1;
                if (!$userExists) {
                    $user = new User();
                    $user->username = $message;
                    $user->password = '';
                    $user->tel_id   = $userId;
                    $user->save();
                }

                $message_id = Message::create(['tel_id' => $userId, 'content' => $message])->id;            
            
            
                $mess = $this->processMessage($message, $message_id);
                $bot->reply('OK: ' . $mess);
            }catch(\Throwable $e){

                $bot->reply("Tin ko hieu: ".$message);
               // $bot->reply($e->getMessage());
            }    

        });
        // Start listening
        $botman->listen();
    }
    public function index()
    { 
        Session::forget('arrSo');        
        $message = "2đ: 3356 b2. 356 x3 đ.x1. 1668 b5,5 b1 b4 x3 đ.x2. 3752 b2 b1 b1 x3 đ.x2. 56 b1. 071 b2 x20. 668 đ.b1. 4348 b1 b1 x10. 23-39 đáx0,5.  Dc:3723 b2 b1 x10. 68 b4.  D.phu:1668 b29. 733 b1 x5.   T23";
        echo $message;
        
        $userDetail = Auth::user();
        $message_id = Message::create(['tel_id' => $userDetail->tel_id, 'content' => $message])->id;
        echo "<h3>".$message."</h3>";        
       // try{
            $mess = $this->processMessage($message, $message_id);
            //dd($mess);
        //}catch(\Exception $ex){
          // echo ("Tin ko hieu: ".$message);         
        //}
        if(isset($mess) && $mess != ''){            
            echo ('OK: ' . $mess);
        }
    }
    function processMessage($message, $message_id){   
        $message .= ".";                  
        
        $message = $this->regMess($message); 
        
        $tmpArr = explode(" ", $message);
        $countAmount = $countChannel = $countBetType = 0;
        $amountArr = $channelArr = $betTypeArr = [];    
        
        foreach($tmpArr as $k => $value){
            
            if($this->isChannel($value)){
                $countChannel++;
                $channelArr[] = $k;
            }

        }
        // nếu tin nhắn ko có đài thì mặc định là dc
        // TH chi co 1
        $betArr = [];        
       
        if(count($channelArr) > 0){            
            if($channelArr[0] == 0 && isset($channelArr[1]) && $channelArr[1] == 1){                
                $tmpArr[0] = '2d';
                unset($tmpArr[1]);
                $message = implode(" ", $tmpArr);
                $tmpArr = explode(" ", $message);
                $countAmount = $countChannel = $countBetType = 0;
                $amountArr = $channelArr = $betTypeArr = [];    
                
                foreach($tmpArr as $k => $value){
                    
                    if($this->isChannel($value)){
                        $countChannel++;
                        $channelArr[] = $k;
                    }

                }
            }

            // dai phia sau
            if(end($channelArr) == count($tmpArr)-1){
           
                foreach($channelArr as $key => $value){  
                   
                    $start = isset($channelArr[$key-1]) ? $channelArr[$key-1]+1 : 0;
                    $position =   $value+1;
                    $betArr[] = array_slice($tmpArr, $start, $position);
                }
                //dd($betArr);
            }else{
                foreach($channelArr as $key => $value){           
                    $position =   isset($channelArr[$key+1]) ? $channelArr[$key+1] : count($tmpArr);
                    $start = $key > 0 ? $value : 0;
                    $betArr[] = array_slice($tmpArr, $start, $position-$start);
                    
                }    
            }            
        }else{
            $betArr[] = $tmpArr;
        }   
        //dd($betArr);
        foreach($betArr as $arr){
            $betArrDetail[] = $this->parseBetToChannel($arr);
        }
        $betDetail = [];     
        //dd($message);
        //dd($betArrDetail);
        foreach($betArrDetail as $k => $betChannelDetail){
            $tmp2 = $this->parseDetail($betChannelDetail, $message);            
            $betDetail = array_merge($betDetail, $tmp2);
        }
        //dd($betDetail);
        $this->insertDB($betDetail, $message_id);
        return $message;
    }
    function regMess($message){
        //$message = strtolower($message);
        $message = $this->stripUnicode($message);
       // dd($message);
        $message = strtolower($message);
        
        
        $message = str_replace("dáx0,5.", 'dx9990n', $message);
        $message = str_replace(" b5,5 ", 'bl9995n', $message);
        
        //dd($message);
        $message = preg_replace('/[ ]+/', '.', $message);
        $message = preg_replace('/[\r\n]+/', '.', $message);
        $message = preg_replace('/[+]+/', '.', $message);
        $message = preg_replace('/[.]+/', '.', $message);
        $message = preg_replace('/([0-9]+)m/', '${1}n', $message);
       
        $message = str_replace("keo.den", 'k', $message);
        $message = str_replace("keo", 'k', $message);  

        $message = str_replace("2₫.", '2d.', $message);

        $message = str_replace("d.phu", 'dp', $message);
        $message = str_replace("dai phu", 'dp', $message);
        $message = str_replace("d.p", 'dp', $message);
       // dd($message);
        $message = str_replace("dch", 'dc', $message);
         $message = str_replace("dai chanh", 'dc', $message);
        $message = str_replace("dai chinh", 'dc', $message);
        $message = str_replace("chah", 'dc', $message);
        $message = str_replace("d.c", 'dc', $message);

         $message = str_replace("xiu chu", 'xc', $message);
        $message = str_replace("x.chu", 'xc', $message);
        $message = str_replace("x.c", 'xc', $message);
        $message = str_replace("xiu", 'xc', $message);
        
        $message = str_replace("ab", 'dd', $message);         
        $message = str_replace("trieu", 'tr', $message);
        $message = str_replace("dbl", 'db', $message);                
       
        $message = preg_replace('/.đ0.([0-5,{1}])./', '.db9990${1}n.', $message);
       
        $message = preg_replace('/([1-9,{1}])tr/', '${1}000n', $message);
       
        $message = preg_replace('/([0-9]+)con/', '', $message);
       
        $message = preg_replace('/([0-9]+)([k])([0-9]+)/', '${1} ${2} ${3} ', $message);
       
        $message = preg_replace('/([0-9,{3,}])([abcdefghijklmopqrstuvwxyz]+)([0-9]+)([abcdefghijklmopqrstuvwxyz]+)/', '${1}${2}${3}n${4}', $message);
        $message = preg_replace('/([0-9,{1}])([n])([5])/', '999${1}n', $message);        
        $message = preg_replace('/([0-9,{3,}]+).05([abcdefghijklmopqrstuvwxyz]+)/', '${1}bl9990n${2}', $message);
        
        $message = preg_replace('/([ ])xc/', 'x', $message);
        $message = preg_replace('/d.x([0-9]+)/', 'dxc${1}', $message);        
        $message = preg_replace('/dx([0-9]+)/', 'dxc${1}', $message);
        
        $message = preg_replace('/dax([0-9]+)(.)/', 'dx${1}n${2}', $message);
        $message = preg_replace('/đá([0-9]+)(.)/', 'da${1}n${2}', $message);
        
        $message = str_replace("dax0,5.", 'dx9990n.', $message);        
        $message = str_replace("db0.25", 'db999025n', $message);
        $message = str_replace("dxc2.5.", 'dxc9992n.', $message);
        $message = str_replace("db0.5.", 'db9990n.', $message);

        $message = str_replace("daoxc", "dxc", $message);  
        $message = str_replace("xcdao", 'dxc', $message);
        $message = str_replace("xc.dao", 'dxc', $message);

        $message = str_replace("d.b0,5.", 'db9990n.', $message);
        $message = str_replace(".b05.", '.b9990n.', $message);
        $message = str_replace("bd0.5", '.db9990n.', $message);        
        $message = str_replace("b0,5", '.b9990n.', $message);        
        $message = str_replace(".b0.5", '.b9990n', $message);        
        $message = str_replace('dav.x', 'dxv', $message);

        $message = preg_replace('/([.])([abcdefghijklmopqrstuvwxyz]+)([0-9]+)([.])/', '$1$2$3n$4', $message);
        $message = preg_replace('/([.])([abcdefghijklmopqrstuvwxyz]+)([0-9]+)([.])/', '$1$2$3n$4', $message);       
        $message = preg_replace('/(05)(\s)(db)/', '9990ndb', $message);
        $message = preg_replace('/(05)(\s)(bl)/', '9990ndb', $message);
        $message = preg_replace('/(05)([a-z]+)/', '9990n${2}', $message);
        $message = str_replace("02bdao", '99902ndb', $message);
        $message = str_replace("db02", 'db99902n', $message);
        $message = str_replace("d.b", 'db', $message);
        $message = str_replace("b2,5", ' b 9992n ', $message);
        $message = str_replace("b2,5.", ' b 9992n ', $message);
        $message = str_replace("0,5", '0.5', $message);     
        $message = str_replace("1,5", '1.5', $message);
        $message = str_replace("2,5", '2.5', $message);
        $message = str_replace("3,5", '3.5', $message);
        $message = str_replace('xx', 'x', $message);       
        
        // end 500 dong            
        $message = (preg_replace('/([ .])([abcdefghijklmopqrstuvwxyz]+)(\d)(\.5)([ .])/',
         ' ${1}${2}999${3}n${5}', $message));  // b2.5.
        $message = (preg_replace('/([abcdefghijklmopqrstuvwxyz]+)(\d)(\.5)([abcdefghijklmopqrstuvwxyz]+)/',
        ' ${1} 999${2}n${4} ', $message));  // b2.5.        
        $message = (preg_replace('/([ .])(\d)(\.5)([abcdefghijklmopqrstuvwxyz]+)/', '${1}999${2}n${4}', $message));                     
        $message = (preg_replace('/([tT])([0-9]+)/', ' ', $message));
        //dd($message);
       
        $message = $this->formatMessage($message);
        $message = (preg_replace('/([ ]+)/', ' ', $message)); // remove nhieu khoang trang thanh 1 
        $message = preg_replace('/([0-9,{2,}]+)([abcdefghijklmopqrstuvwxyz]+)([0-9,{1,}]+)([n])/', '$1$2$3$4 ', $message);
        //2nb 10nx
        $message = preg_replace('/([ ])([0-9]+)([n])([abcdefghijklmopqrstuvwxyz]+)([ ])/', '$1$4 $2$3$5', $message);// 2nb => b 2n x 10n 
        $message = preg_replace('/([ ])([0-9]+)([n])([abcdefghijklmopqrstuvwxyz]+)$/', '$1$4 $2$3', $message);// 2nb => b 2n x 10n 
        
        $message = preg_replace('/([abcdefghijklmopqrstuvwxyz]+)([ ])([0-9]+)([n])/', '$1$2$3$4 ', $message);
        $message = preg_replace('/([ ])([abcdefghijklmopqrstuvwxyz]+)([0-9]+)([n])/', '$1$2$3$4 ', $message);        
        $message = preg_replace('/([0-9,{1,}]+)([n])([abcdefghijklmopqrstuvwxyz]+)/', ' $3$1$2 ', $message);                
        $message = preg_replace('/([0-9]+)([n])/', ' $1$2 ', $message);        
        $message = (preg_replace('/([0-9]{2,})([abcdefghijklmopqrstuvwxyz]{2,})/', '$1 $2', $message));        
        $message = (preg_replace('/([0-9]{2,})([abcdefghijklmopqrstuvwxyz])/', '$1 $2', $message));
        
        $message = $this->formatMessage($message);
        $message = str_replace("n n", "n", $message);  
         $message = (preg_replace('/([0-9]+)([ ])([abcdefghijklmopqrstuvwxyz]+)([0-9]+)([ ])/', '$1$2$3 $4n$5', $message)); 
        return $message;
    }
    public function stripUnicode($str) {
        if (!$str)
            return false;
        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ằ|Ẳ|Ẵ|Ặ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'd' => 'đ',
            'D' => 'Đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
            '' => '?',
            '-' => '/'
        );
        foreach ($unicode as $khongdau => $codau) {
            $arr = explode("|", $codau);
            $str = str_replace($arr, $khongdau, $str);
        }
        return $str;
    }
    function parseDetail($betArrDetail, $message){  
         $bettttt = []; 
        // dd($betArrDetail);
        foreach($betArrDetail as $channel => $arr){
            $countII = 0;
            var_dump($channel);
            foreach($arr as $tmp){
                
                $channel_bet = $channel;
                $price = str_replace("n", "", array_pop($tmp)); // lay so tien va xoa luon
                $price = $price == 0 ? 0.5 : $price;
                $price = $price == 99902 ? 0.2 : $price;
                $price = $price == 99903 ? 0.3 : $price;
                $price = $price == 99904 ? 0.4 : $price;
                $price = $price == 999025 ? 0.25 : $price;
                $price = $price == 999035 ? 0.35 : $price;
                $price = $price == 9990 ? 0.5 : $price;
                $price = $price == 9991 ? 1.5 : $price;
                $price = $price == 9992 ? 2.5 : $price;
                $price = $price == 9993 ? 3.5 : $price;
                $price = $price == 9994 ? 4.5 : $price;
                $price = $price == 9995 ? 5.5 : $price;
                $str = implode(' ', $tmp);
                $arr_number = [];
                // start xu ly keo
                $is_keo = 0;
                if(isset($tmp[1]) && $tmp[1] == 'k'){
                    $is_keo = 1;
                    $number1 = $tmp[0];
                    $number2 = $tmp[2];
                    $first1 = substr($number1, 0, 1);
                    $first2 = substr($number2, 0, 1);
                    $end1 = substr($number1, -1);
                    $end2 = substr($number2, -1);
                    if($end1 == $end2){
                        for($ki = $first1; $ki<=$first2; $ki++){
                            $arr_number[] = str_replace($first1, $ki, $number1);
                        }
                    }
                    if($first1 == $first2){
                        $lengthA = strlen($number1);
                        for($ki = $number1; $ki <= $number2; $ki++){
                            $arr_number[] = str_pad($ki, $lengthA, "0", STR_PAD_LEFT);;
                        }                        
                    }
                }
                if(isset($tmp[4]) && $tmp[4] == 'k'){
                    $is_keo = 1;
                    $number1 = $tmp[3];
                    $number2 = $tmp[5];
                    $first1 = substr($number1, 0, 1);
                    $first2 = substr($number2, 0, 1);
                    $end1 = substr($number1, -1);
                    $end2 = substr($number2, -1);
                    if($end1 == $end2){
                        for($ki = $first1; $ki<=$first2; $ki++){
                            $arr_number[] = str_replace($first1, $ki, $number1);
                        }
                    }
                    if($first1 == $first2){
                        for($ki = $end1; $ki <= $end2; $ki++){
                            $arr_number[] = str_replace($end1, $ki, $number1);
                        }                        
                    }
                }
                if(isset($tmp[7]) && $tmp[7] == 'k'){
                    $is_keo = 1;
                    $number1 = $tmp[6];
                    $number2 = $tmp[8];
                    $first1 = substr($number1, 0, 1);
                    $first2 = substr($number2, 0, 1);
                    $end1 = substr($number1, -1);
                    $end2 = substr($number2, -1);
                    if($end1 == $end2){
                        for($ki = $first1; $ki<=$first2; $ki++){
                            $arr_number[] = str_replace($first1, $ki, $number1);
                        }
                    }
                    if($first1 == $first2){
                        for($ki = $end1; $ki <= $end2; $ki++){
                            $arr_number[] = str_replace($end1, $ki, $number1);
                        }                        
                    }
                }
                // end xu ly keo 
                $bet_type = null;              
                foreach($tmp as $k1 => $tmp1){                 
                    if (preg_match('/[a-z*]/', $tmp1, $matches)){                        
                       $bet_type = $tmp1;    
                    }
                    if($is_keo == 0){
                        if($tmp1 > 0 || $tmp1 == "00" || $tmp1 == "000" || $tmp1 == "0000"){
                            $arr_number[] = $tmp1; 
                        }
                    }
                }

                if(empty($arr_number) && isset($numberArr)){
                    $arr_number = $numberArr[$countII-1];
                }
                $numberArr[$countII] = $arr_number;
                
                //var_dump("<pre>", $numberArr);
                //dd($arr_number);
                // truong hop tao lao : loai de nam sau so tien
                if(!$bet_type && count($arr) == 1){
                    $tmpMess = explode(" ",$message);
                    foreach($tmpMess as $tmpValue){
                        if (preg_match('/^[a-z]+$/i', $tmpValue, $matches)){                        
                           $bet_type = $tmpValue;
                           break;        
                        }
                    }                  
                }
                //dd($arr_number[0]);
               // if($arr_number[0] == 392){
               //  dd($betTypeSelected);die;
               //      dd($bet_type);
               // }
               // var_dump($bet_type);
                if(!$bet_type){        
                    if(strlen($arr_number[0]) == 3){
                        if(isset($betTypeSelected[$countII-1]) && $betTypeSelected[$countII-1]=='x'){
                            $bet_type = 'x';
                        }elseif(isset($betTypeSelected[$countII-1]) && $betTypeSelected[$countII-1]=='dxc'){
                            $bet_type = 'dxc';
                        }else{
                            $bet_type = 'bl';
                        }
                    }elseif(strlen($arr_number[0]) == 4){
                        $bet_type = 'bl';
                    }                                    
                }
                if(!$bet_type){
                    $bet_type = $betTypeSelected[$countII-1];
                }
                if($bet_type == 'dx' && strlen($arr_number[0]) == 3){
                    $bet_type = 'dxc';
                }
                if($bet_type == 'dv' || $bet_type == 'dxv'){
                    
                    $bettttt[] = [
                        'channel' => $channel_bet,
                        'bet_type' => $this->formatBetType($bet_type),
                        'number' => $arr_number,
                        'price' => $price
                    ];
                }elseif($bet_type == 'da' || $bet_type == 'dx'){
                    // array:1 [▼
                    //   0 => "4989"
                    // ]
                    if(count($arr_number) == 1 && strlen($arr_number[0]) == 4){
                        $arr_number = [substr($arr_number[0],0,2), substr($arr_number[0],-2)];
                    }
                    if(count($arr_number)%2==0){                        
                        $ii = 0;
                        $arrNumberNew = [];
                        foreach($arr_number as $tmpNumber){
                             $ii++;
                            $tmpArr[] = $tmpNumber;
                            if($ii%2 == 0 && $ii > 0){
                                $arrNumberNew[] = $tmpArr;
                                $tmpArr = [];
                            }
                           
                        }
                        if(!empty($arrNumberNew)){
                            foreach($arrNumberNew as $arrNumber){
                                $bettttt[] = [
                                    'channel' => $channel_bet,
                                    'bet_type' => $this->formatBetType($bet_type),
                                    'number' => $arrNumber,
                                    'price' => $price
                                ]; 
                            }
                        }
                    }else{ // truong hop "da" nhung chi co 3 so vd : da 12 24 23 => dang le la da vong moi dung
                        
                        $arrNumberNew = $this->getCapSoDaVong($arr_number);
                        
                        if(!empty($arrNumberNew)){
                            foreach($arrNumberNew as $arrNumber){
                                $bettttt[] = [
                                    'channel' => $channel_bet,
                                    'bet_type' => $this->formatBetType($bet_type),
                                    'number' => $arrNumber,
                                    'price' => $price
                                ]; 
                            }
                        }
                    }
                    

                   
                }else{

                    if(($bet_type == 'd' && count($arr_number) == 1 && strlen($arr_number[0]) > 2) || $bet_type == 'db'){
                        $bet_type = 'bd';
                    }
                     
                    foreach($arr_number as $numberBet){
                        $bettttt[] = [
                            'channel' => $channel_bet,
                            'bet_type' => $this->formatBetType($bet_type),
                            'number' => $numberBet,
                            'price' => $price
                        ];
                    } 
                }
                $betTypeSelected[$countII] = $bet_type;
                $countII++;          
            }
            //dd($betTypeSelected);
        }
        return $bettttt;
    }
    function insertDB($betDetail, $message_id){
        //dd($betDetail);        
        foreach($betDetail as $k => $oneBet){  
            //dd($oneBet);die;
            $bet_type = $oneBet['bet_type'];
            $arrSo = Session::get('arrSo');
            if(!$arrSo){
                $arrSo = [];
            }    
              
            //try{
                if(!is_array($oneBet['number'])){
                    $keyCacheSession =  $oneBet['channel']."-".$oneBet['number'];  
                    if(!isset($arrSo[$keyCacheSession])){
                        $arrSo[$keyCacheSession] = 1;
                    }else{
                        $arrSo[$keyCacheSession] += 1;
                    }    
                }                
            //}catch(\Exception $ex){
              //  dd($oneBet['number']);
            //}           
            Session::put('arrSo', $arrSo);          
            
            $channelArr = $this->getChannelId($oneBet['channel']);
            $bet_type_id = $this->getBetTypeId($bet_type); 
            //dd($bet_type);
            if(!in_array($bet_type, ['dv', 'dx', 'dxv', 'da', 'dxc', 'bd'])){
                // check truong hop 4 con, 3 con
                //dd($oneBet);   
                //dd($betDetail);             
                if(!is_array($oneBet['number'])){
                    foreach($betDetail as $k1 => $oneBet1){ 
                        if($k1 < $k && $oneBet['number'] == $oneBet1['number'] && $oneBet['bet_type'] == $oneBet1['bet_type'] 
                            && 
                            (
                                !isset($arrSo[$keyCacheSession]) 
                                ||
                                ( isset($arrSo[$keyCacheSession]) 
                                    && 
                                    (
                                        $arrSo[$keyCacheSession] < 3 
                                            && strlen($oneBet['number']) == 3
                                    ) || (
                                        $arrSo[$keyCacheSession] < 4 
                                            && strlen($oneBet['number']) == 4)
                                    ) 

                                
                            )
                            
                        ){         
                                    
                                    
                            if(strlen($oneBet['number']) == 4){
                                if($arrSo[$keyCacheSession] == 2){
                                    
                                    //dd($oneBet);                                    
                                    //$oneBet['number'] = substr($oneBet['number'], -3);    
                                    if($bet_type == 'x' || $bet_type == 'bl'){
                                        $oneBet['number'] = substr($oneBet['number'], -3);
                                        //dd($oneBet['number']);       
                                    }else{
                                        $oneBet['number'] = substr($oneBet['number'], -2);
                                    } 
                                }elseif($arrSo[$keyCacheSession] == 3){   

                                    if($bet_type == 'x'){
                                        $oneBet['number'] = substr($oneBet['number'], -3);        
                                    }else{
                                        $oneBet['number'] = substr($oneBet['number'], -2);
                                    }                                    
                                }                       
                                
                            }elseif(strlen($oneBet['number']) == 3 ){
                               
                                if($arrSo[$keyCacheSession] > 1 && $bet_type != 'x'){
                                   $oneBet['number'] = substr($oneBet['number'], -2);
                                }
                                
                            }
                            
                        }

                        
                    }
                }            
                
                $this->processNormal($oneBet, $bet_type_id, $channelArr, $message_id);
                
            }elseif($bet_type == 'da' || $bet_type == 'dx'){ 
                if(empty($channelArr)){
                    continue;
                }
                $countDv1 = 0;
                $refer_bet_id = null;
                $str_channel = Channel::getChannelName($channelArr);
                foreach($channelArr as $channel_id){
                    $countDv1++;
                    $number1 = $this->formatNumber($oneBet['number'][0]);
                    $arr = [
                        'channel_id' => $channel_id,
                        'bet_type_id' => $bet_type_id,
                        'message_id' => $message_id,
                        'price' => $oneBet['price'],
                        'number_1' => $number1,
                        'number_2' => $this->formatNumber($oneBet['number'][1]),
                        'refer_bet_id' => $countDv1 > 1 ? $refer_bet_id : null,
                        'total' => $oneBet['price']*36, // 2 dai x 18 lo x 2 so = 72 lo
                        'is_main' => $refer_bet_id > 0 ? 0 : 1,
                        'bet_day' => date('Y-m-d'),
                        'str_channel' => $str_channel,
                        'len' => strlen($number1),
                    ];
                    
                    $rs = Bet::create($arr);
                    if($countDv1 == 1){
                        $refer_bet_id = $rs->id;
                    }
                }
               // đá , đá xiên 
            }elseif($bet_type == 'dv' || $bet_type == 'dxv'){
                $this->processDvDxv($oneBet, $bet_type_id, $channelArr, $message_id);
            } // đá vòng
            elseif($bet_type == 'dxc'){
                $this->processDxc($oneBet, $bet_type_id, $channelArr, $message_id);
                
            }elseif($bet_type == 'bd'){
              
                $this->processBaoLoDao($oneBet, $bet_type_id, $channelArr, $message_id);
                
            }
        }
    }
    function processNormal($oneBet, $bet_type_id, $channelArr, $message_id){
        $countDv = 0;
            $refer_bet_id = null;
        foreach($channelArr as $channel_id){                    
            if(empty($channelArr)){
                continue;
            }
            $countDv++;
            $str_channel = Channel::getChannelName($channelArr);
            //dd($str_channel);
            if($bet_type_id == 9 && strlen($oneBet['number']) == 4){
                $oneBet['number'] = substr($oneBet['number'], 1, 3);
            }
            if(($bet_type_id == 1 || $bet_type_id == 2 || $bet_type_id == 3 ) && strlen($oneBet['number']) > 2){                
                $oneBet['number'] = substr($oneBet['number'], -2);                
            }
            $number1 = $this->formatNumber($oneBet['number']);
            $arr = [
                'channel_id' => $channel_id,
                'bet_type_id' => $bet_type_id,
                'message_id' => $message_id,
                'price' => $oneBet['price'],
                'number_1' => $number1,
                'refer_bet_id' => $countDv > 1 ? $refer_bet_id : null,
                'is_main' => $refer_bet_id > 0 ? 0 : 1,
                'str_channel' => $str_channel,
                'total' => $this->calTotal($bet_type_id, $oneBet['price'],$oneBet['number']),
                'bet_day' => date('Y-m-d'),
                'len' => strlen($number1),               
            ];                    
           
            $rs = Bet::create($arr);
                    if($countDv == 1){
                        $refer_bet_id = $rs->id;
                    }
        }
    }
    function processBaoLoDao($oneBet, $bet_type_id, $channelArr, $message_id){
        //dd($bet_type_id);
        $arrTatCaSo = $this->getTatCaSoDao($oneBet['number']);
        //dd($arrTatCaSo);
        $arrTatCaSo = array_unique($arrTatCaSo);
        
        if(!empty($arrTatCaSo)){
            $countDv = 0;
            $refer_bet_id = null;
            $str_channel = Channel::getChannelName($channelArr);
            foreach($arrTatCaSo as $number){
                foreach($channelArr as $channel_id){                    
                    if(empty($channelArr)){
                        continue;
                    }
                    $countDv++;
                    $number1 = $this->formatNumber($number);
                    $arr = [
                        'channel_id' => $channel_id,
                        'bet_type_id' => $bet_type_id, // bao lo
                        'message_id' => $message_id,
                        'price' => $oneBet['price'],
                        'number_1' =>  $number1,
                        'is_main' => $refer_bet_id > 0 ? 0 : 1,
                        'refer_bet_id' => $countDv > 1 ? $refer_bet_id : null,
                        'str_channel' => $str_channel,
                        'total' => $this->calTotal($bet_type_id, $oneBet['price'],  $number),
                        'bet_day' => date('Y-m-d'),
                        'len' => strlen($number1)
                    ];                    
                   
                    $rs = Bet::create($arr);
                    if($countDv == 1){
                        $refer_bet_id = $rs->id;
                    }
                }
            }
        }
    }
    function processDxc($oneBet, $bet_type_id, $channelArr, $message_id){
        //dd($bet_type_id);
        if(strlen($oneBet['number']) == 4){
            $oneBet['number'] = substr($oneBet['number'], -3);            
        }
        $arrTatCaSo = $this->getTatCaSoDao($oneBet['number']);
        $arrTatCaSo = array_unique($arrTatCaSo);
        if(!empty($arrTatCaSo)){
            $countDv = 0;
            $refer_bet_id = null;
            $str_channel = Channel::getChannelName($channelArr);
            foreach($arrTatCaSo as $number){
                foreach($channelArr as $channel_id){                    
                    if(empty($channelArr)){
                        continue;
                    }
                    $countDv++;
                    $number1 = $this->formatNumber($number);
                    $arr = [
                        'channel_id' => $channel_id,
                        'bet_type_id' => $bet_type_id, //xiu chu
                        'message_id' => $message_id,
                        'price' => $oneBet['price'],
                        'refer_bet_id' => $countDv > 1 ? $refer_bet_id : null,
                        'number_1' =>  $number1,
                        'is_main' => $refer_bet_id > 0 ? 0 : 1,
                        'total' => $this->calTotal($bet_type_id, $oneBet['price'],  $number),
                        'bet_day' => date('Y-m-d'),
                        'str_channel' => $str_channel,
                        'len' => strlen($number1)             
                    ];                    
                   
                    $rs = Bet::create($arr);
                    if($countDv == 1){
                        $refer_bet_id = $rs->id;
                    }
                }
            }
        }
    }
    function formatSoDa($arr){
        $arr2 = [];
        foreach($arr as $tmp){
            $arr2[]= substr($tmp, -2);
        }
        return $arr2;
    }
    function processDvDxv($oneBet, $bet_type_id, $channelArr, $message_id){        
        if(count($oneBet['number']) == 1){
            for($i = 0; $i < strlen($oneBet['number'][0]); $i++){
                if($i%2 == 0){
                    $arrNumber[] = substr($oneBet['number'][0], $i, 2);
                }
            }
            $oneBet['number'] = $arrNumber;
        }        
        $arrCapSoDaVong = $this->getCapSoDaVong($oneBet['number']);

      //  dd($arrCapSoDaVong);
        if(!empty($arrCapSoDaVong)){
           
            $oneBet['number']= $this->formatSoDa($oneBet['number']);
            $str_number = implode('-', $oneBet['number']);
            $str_channel = Channel::getChannelName($channelArr);
            $countDv = 0;
            $refer_bet_id = null;
           // dd($arrCapSoDaVong);
            foreach($arrCapSoDaVong as $capSoArr){
                foreach($channelArr as $channel_id){
                    $countDv++;
                    $arr = [
                        'channel_id' => $channel_id,
                        'bet_type_id' => $bet_type_id,
                        'message_id' => $message_id,
                        'price' => $oneBet['price'],
                        'number_1' => substr($this->formatNumber($capSoArr[0]), -2),
                        'number_2' => substr($this->formatNumber($capSoArr[1]), -2),
                        'refer_bet_id' => $countDv > 1 ? $refer_bet_id : null,
                        'total' => $oneBet['price']*36, // 1 dai x 18 lo x 2 so = 72 lo
                        'is_main' => $refer_bet_id > 0 ? 0 : 1,
                        'bet_day' => date('Y-m-d'),
                        'str_number' => $str_number,
                        'str_channel' => $str_channel,
                        'len' => 2,
                    ];
                    
                    $rs = Bet::create($arr);
                    if($countDv == 1){
                        $refer_bet_id = $rs->id;
                    }
                }
            }
        }
    }
    function getCapSoDaVong($arr) {
        $rs = array();
        for ($i = 0; $i < count($arr) - 1; $i++) {
            for ($j = $i + 1; $j < count($arr); $j++) {
                $rs[] = [$arr[$i], $arr[$j]];
            }
        }

        return $rs;
    }
    function getTatCaSoDao($string) {
        $results = [];

        if (strlen($string) == 1) {
            array_push($results, $string);
            return $results;
        }

        for ($i = 0; $i < strlen($string); $i++) {
            $firstChar = $string[$i];
            $charsLeft = substr($string, 0, $i) . substr($string, $i + 1);
            $innerPermutations = $this->getTatCaSoDao($charsLeft);
            
            for ($j = 0; $j < count($innerPermutations); $j++) {
                
                array_push($results, $firstChar . $innerPermutations[$j]);
                
            }
        }
        return $results;
    }

    function parseBetToChannel($arr){  
        
        ///dd($arr);
        $patternChannel = '/[a-z]+/';            
        if (preg_match_all($patternChannel, $arr[0], $matches)){            
            $channel = $arr[0]; // dc, dp, 2d, vl, tp, kg...       
            $arrNew = array_slice($arr, 1, count($arr));
        }else{
            $channel = $arr[count($arr)-1];
            $arrNew = array_slice($arr, 0, -1);    
        }
        //dd( $channel, $arrNew);
        foreach($arrNew as $k => $v){
            // if (preg_match_all('/[a-z][0-9,{1,}]/', $v, $matches)){
            //     $v = preg_replace('/([a-z])([0-9,{1,}])/', '${2}n', $v);                
            // }
            $patternAmount = '/[0-9]*[n]/';            
            if (preg_match_all($patternAmount, $v, $matches)){
                $betTypeKey[] = $k;             
            }
        }
        //echo "<hr><pre>";
//        print_r($arrNew);die;
        if(empty($betTypeKey)){
            $tmpStr = end($arrNew);
            $tmpStr = preg_replace('/([a-z])([0-9,{1,}])/', '$1 ${2}n', $tmpStr);
            $tmpArrNew = explode(" ", $tmpStr);
            if(count($tmpArrNew) > 1){
                array_pop($arrNew);
                $arrNew = array_merge($arrNew, $tmpArrNew);
            }
            foreach($arrNew as $k => $v){
                // if (preg_match_all('/[a-z][0-9,{1,}]/', $v, $matches)){
                //     $v = preg_replace('/([a-z])([0-9,{1,}])/', '${2}n', $v);                
                // }
                $patternAmount = '/[0-9]*[n]/';            
                if (preg_match_all($patternAmount, $v, $matches)){
                    $betTypeKey[] = $k;             
                }
            }
        }     
        foreach($betTypeKey as $key => $value){

            $end =  $value+1;
            
            $start = $key > 0 ? $betTypeKey[$key-1]+1 : 0; 
                //var_dump($end, $start);
               // echo "<hr><pre>";
            $tmp3 = array_slice($arrNew, $start, $end-$start);
           // dd($tmp3);
            if(!empty($tmp3)){
                $tmp2[] = $tmp3;
            }            
            
        }  
        return [$channel => $tmp2];

    }
    function formatMessage($message){    
       
        $message = str_replace("d.phu", "dp", $message);
        $message = str_replace("duoi", "dui", $message);
        $message = str_replace("D.phu", "dp", $message);
        $message = str_replace("D.Phu", "dp", $message);
        $message = str_replace("dbao", "db", $message);   
        $message = str_replace("bao", "bl", $message);      
        $message = str_replace("...", " ", $message);
        $message = str_replace(":", " ", $message);
        $message = str_replace("..", " ", $message);
        $message = str_replace(".", " ", $message);
        $message = str_replace(";", " ", $message);
        $message = str_replace("---", " ", $message);
        $message = str_replace("--", " ", $message);
        $message = str_replace("-", " ", $message);
        $message = str_replace("___", " ", $message);
        $message = str_replace("__", " ", $message);
        $message = str_replace("_", " ", $message);
        $message = str_replace(",,,", " ", $message);
        $message = str_replace(",,", " ", $message);
        $message = str_replace(",", " ", $message);
        $message = str_replace("   ", " ", $message);
        $message = str_replace("  ", " ", $message);
        $message = str_replace(" ", " ", $message);
       
        $message = str_slug($message, " ");
        $message = strtolower($message);
        
        $message = str_replace("lay tin nay", "", $message);
        $message = str_replace("bd", "db", $message);
        $message = str_replace("daoxc", "dxc", $message);
        $message = str_replace("daox", "dxc", $message);
        $message = str_replace("bdao", "db", $message);        
        $message = str_replace("xdao", "dxc", $message);
        $message = str_replace("xd", "dxc", $message);
        $message = str_replace("chanh", "dc", $message);
        $message = str_replace("dacap", "da", $message);
        $message = str_replace("da vong", "dv", $message);
        $message = str_replace("2 đài", "2d", $message);
        $message = str_replace("2 dai", "2d", $message);
        $message = str_replace("2dai", "2d", $message);
        $message = str_replace("phu", "dp", $message);        
        $message = str_replace("fu", "dp", $message);
        $message = str_replace("ch", "dc", $message);    
            
        $message = str_replace("dav", "dv", $message);
        
        
        return $message;
    }
        
    function isAmount($value){
        $flag = false;
        if(strpos($value, 'n')){
            $value = str_replace("n", "", $value);
            if($value > 0){
                $flag = true;
            }
        }
        return $flag;
    }

    function isChannel($value){

        if(in_array($value, $this->channelList) || in_array($value, $this->arrExpert)){
            return true;
        }else{
            return false;
        }
    }

    function isBetType($value){        
        return in_array($value, $this->betTypeList);
    }
    function getBetTypeId($bet_type){  
        
        $rs  = BetType::where('keyword', $bet_type)->first();
        if($rs){
           return $rs->id;
        }else{
            $bet_type = (preg_replace('/([0-9]*)([a-z])/', '$2', $bet_type));
            $bet_type = $this->formatBetType($bet_type);
            $rs  = BetType::where('keyword', $bet_type)->first();
            //if($rs){
               return $rs->id;
            // }else{
            //     dd("11111", $bet_type);
            // }
        }
    }
    function formatNumber($number){
        return $number = (preg_replace('/([0-9]*)([a-z])/', '$1', $number));
    }
    function getChannelId($channel = ''){
        $channelSelected = [];
        $today = date('N');
        if($channel == '2d'){
            $channelSelected = [$this->channelListKey[$this->channelByDay[$today][0]], $this->channelListKey[$this->channelByDay[$today][1]]];
        }elseif($channel == 'dc' || $channel == ""){
            $channelSelected = [$this->channelListKey[$this->channelByDay[$today][0]]];
        }elseif($channel == 'dp'){
            $channelSelected = [$this->channelListKey[$this->channelByDay[$today][1]]];
        }else{
            $channelSelected = [$this->channelListKey[$channel]];
            //dd($channelSelected);
        }

        return $channelSelected;
    }
    function formatBetType($bet_type){
        switch ($bet_type) {
            case 'd':
                $bet_type = 'da';
                break;
            case 'b' : 
                $bet_type = 'bl';
                break;
            case 'xc' : 
                $bet_type = 'x';
                break;    
            default:
                # code...
                break;
        }
        return $bet_type;
    }
    function calTotal($bet_type_id, $price, $number){
        $length = strlen($number);
      
        switch ($bet_type_id) {
            case 9: // xiu, xiu chu 2 lô;
                $total = $price*2;
                break;
            case 10: // dao xiu 2 lô;
                $total = $price*2;
                break;
            case 2: // dau 1 lo
                $total = $price;
                break;
            case 3: // duoi 1 lo
                $total = $price;
                break;
            case 1: // dau duoi 2 lo
                $total = $price*2;
                break;    
            case 4: // bao lo
                if($length == 2){
                    $total = $price*18;    
                }
                if($length == 3){
                    $total = $price*17;    
                }
                if($length == 4){
                    $total = $price*16;    
                }
                
                break;    
            case 13: // bao lo
                
                if($length == 3){
                    $total = $price*17;    
                }
                if($length == 4){
                    $total = $price*16;    
                }
                
                break;
            default:
                //dd($bet_type_id);
                # code...
                break;
        }
        
        
        if(!isset($total)){
            //dd($bet_type_id);
        }
        return $total;
    }
}
<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\BetType;
use App\Models\Bet;
use App\Models\Message;
use App\Models\MessageMau;
use Illuminate\Http\Request;
use Auth, Session;
class TestController extends Controller
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
    //preg_replace('/([ .])(\d)(\.5)([a-z])/', '${1}999n${4}', $input_lines);
    public function messagesList(){
        $user = Auth::user();
        $tel_id = $user->tel_id;
        $messageList = Message::where('tel_id', $tel_id)->whereDate('created_at', '=', date('Y-m-d'))->orderBy('id', 'desc')->get();
        return view('messages.list', compact('messageList'));
    }
    public function mau(){
       
        $messageList = MessageMau::where('status', 3)->get();
        return view('messages.mau', compact('messageList'));
    }
    public function messagesDetail(Request $request){
        $user = Auth::user();
        $tel_id = $user->tel_id;
        $message_id = $request->id;
        $maxId = Message::orderBy('id', 'desc')->first()->id;
        $detail = Message::where('tel_id', $tel_id)->where('id', $maxId)->first();        
        //$betList = Bet::where('message_id', $maxId)->get();
        $betList = Bet::where('message_id', $maxId)->where('is_main', 1)->get();
        return view('messages.detail', compact('detail', 'betList'));
    }
    public function index()
    {        
        //$message = "00.01.03.04.05.06.07.08.09.20.dd da300n,dp t3";
        Session::forget('arrSo');
        
        #46, 49, 52
        
        //$message = "2đ: 3752.3356 b2 b1 b1 x3 đ.x1. 668 đ.b1. 943 b1 x3. 96.33 b2,5. 019 x15. Dc:39.79.38.78 b2. 37.73 b1. 34 b2,5. 634 đ.b0,5. 6635.6131 b1. 723 b1 x10. 3447 b2 b1 x10. D.phu:38 b50. T25";
        //$message = "dc 1668 b7 b1 b1 x3 đ.x21 ";
        //$message = "2đ:713.013.299.239.071.075.621.731.371.256.311.133.304 x5. 0831.4999.0439.2913 b2 b2 x10 dd10 đáv x1. 571.035.675.021.756.033.711.804 x5 dd5. 1668 b7 b1 b1 x3 đ.x2. 668 đ.b1.  D.phu:1668 b25.   T11";
        $message = "2d . 2767b5nxc20n .9377b2n db02 .2019b2n.b1n. 530.570.b0.5xc5n. Chanh . 2018.6232.b2n.b1n, 1115 db1n. 1119.2228 db0.5. Phu . 1317.b1n.b1nxc4n, 5959b2n.b2nxc8n. 559.379b2nxc8n. 234b2nxc10n, 739.726.b5nxc10n, T4";
        
        $userDetail = Auth::user();
        $message_id = Message::create(['tel_id' => $userDetail->tel_id, 'content' => $message])->id;
        echo "<h3>".$message."</h3>";
        //$message = "T6.hn 77;88;99 đa vòng 15n";
        // 500 dong
        ////$message = preg_replace('/([0-9]+)([a-z^n]+)/','${1}${2} ', $message);//2326b 
        //dd($message);
        $message = preg_replace('/[ ]+/', '.', $message);
        $message = preg_replace('/[.]+/', '.', $message);
        $message = str_replace(".b05.", '.b9990n.', $message);     
        $message = preg_replace('/([.])([abcdefghijklmopqrstuvwxyz]+)([0-9]+)([.])/', '$1$2$3n$4', $message);//.dd5.
        $message = preg_replace('/([.])([abcdefghijklmopqrstuvwxyz]+)([0-9]+)([.])/', '$1$2$3n$4', $message);//.dd5.
        //dd($message);
        $message = preg_replace('/(05)(\s)(db)/', '9990ndb', $message);
        $message = preg_replace('/(05)(\s)(bl)/', '9990ndb', $message);
        $message = preg_replace('/(05)([a-z]+)/', '9990n${2}', $message);
        $message = str_replace("02bdao", '99902ndb', $message);
        $message = str_replace("db02", 'db99902n', $message);
        $message = str_replace("đ.b", 'db', $message);
        $message = str_replace("b2,5", ' b 9992n ', $message);
        $message = str_replace("b2,5.", ' b 9992n ', $message);
        $message = str_replace("0,5", '0.5', $message);     
        $message = str_replace("1,5", '1.5', $message);
        $message = str_replace("2,5", '2.5', $message);
        $message = str_replace("3,5", '3.5', $message);
        $message = str_replace('đá', 'da', $message);               
        // end 500 dong    
        //dd($message);     
        $message = (preg_replace('/([ .])([abcdefghijklmopqrstuvwxyz]+)(\d)(\.5)([ .])/',
         ' ${1}${2}999${3}n${5}', $message));  // b2.5.
        $message = (preg_replace('/([abcdefghijklmopqrstuvwxyz]+)(\d)(\.5)([abcdefghijklmopqrstuvwxyz]+)/',
        ' ${1} 999${2}n${4} ', $message));  // b2.5.
        //dd($message);
        //$message = (preg_replace('/([abcdefghijklmopqrstuvwxyz]+)(\d)(\.5)([ .])/', '${1}${2}999${3}n${5}', $message));  // b2.5.     

        $message = (preg_replace('/([ .])(\d)(\.5)([abcdefghijklmopqrstuvwxyz]+)/', '${1}999${2}n${4}', $message));
        //dd($message); 
        //$message = (preg_replace('/([abcdefghijklmopqrstuvwxyz]+)([0-9]+)([ .])/', '${1} ${2}n ', $message));
        
        $message = str_replace('đ.x', 'dxc', $message);
        $message = str_replace('xx', 'x', $message);
        $message = str_replace('d.x', 'dxc', $message);
        $message = str_replace('đáv x', 'dxv', $message);
        $message = str_replace('dav x', 'dxv', $message);
        $message = str_replace('dáv x', 'dxv', $message);            
        $message = (preg_replace('/([tT])([0-9]+)/', ' ', $message));
        //dd($message);
       
        $message = $this->formatMessage($message);
        $message = (preg_replace('/([ ]+)/', ' ', $message)); // remove nhieu khoang trang thanh 1 
        $message = preg_replace('/([0-9,{2,}]+)([abcdefghijklmopqrstuvwxyz]+)([0-9,{1,}]+)([n])/', '$1$2$3$4 ', $message);
        //dd($message); 
        //2nb 10nx
        $message = preg_replace('/([ ])([0-9]+)([n])([abcdefghijklmopqrstuvwxyz]+)([ ])/', '$1$4 $2$3$5', $message);// 2nb => b 2n x 10n 
        $message = preg_replace('/([ ])([0-9]+)([n])([abcdefghijklmopqrstuvwxyz]+)$/', '$1$4 $2$3', $message);// 2nb => b 2n x 10n 
        //dd($message); 
        $message = preg_replace('/([abcdefghijklmopqrstuvwxyz]+)([ ])([0-9]+)([n])/', '$1$2$3$4 ', $message);
        //dd($message);
        $message = preg_replace('/([ ])([abcdefghijklmopqrstuvwxyz]+)([0-9]+)([n])/', '$1$2$3$4 ', $message);
        //dd($message);
        $message = preg_replace('/([0-9,{1,}]+)([n])([abcdefghijklmopqrstuvwxyz]+)/', ' $3$1$2 ', $message);        
        //dd($message);  
        $message = preg_replace('/([0-9]+)([n])/', ' $1$2 ', $message);
        // dd($message);  
        $message = (preg_replace('/([0-9]{2,})([abcdefghijklmopqrstuvwxyz]{2,})/', '$1 $2', $message));
        //dd($message);
        $message = (preg_replace('/([0-9]{2,})([abcdefghijklmopqrstuvwxyz])/', '$1 $2', $message));
        //dd($message);  
        
        //dd($message); 
        $message = $this->formatMessage($message);
        $message = str_replace("n n", "n", $message);        
        echo ($message);    
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
        //echo "<br>";
        if(count($channelArr) > 0){
            foreach($channelArr as $key => $value){           
                $position =   isset($channelArr[$key+1]) ? $channelArr[$key+1] : count($tmpArr);
                $start = $key > 0 ? $value : 0;
                $betArr[] = array_slice($tmpArr, $start, $position-$start);
                
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
        
        Session::forget('arrSo');
    }
    function parseDetail($betArrDetail, $message){  
         $bettttt = []; 
        // dd($betArrDetail);
        foreach($betArrDetail as $channel => $arr){
            $countII = 0;
            foreach($arr as $tmp){
                
                $channel_bet = $channel;
                $price = str_replace("n", "", array_pop($tmp)); // lay so tien va xoa luon
                $price = $price == 0 ? 0.5 : $price;
                $price = $price == 99902 ? 0.2 : $price;
                $price = $price == 9990 ? 0.5 : $price;
                $price = $price == 9991 ? 1.5 : $price;
                $price = $price == 9992 ? 2.5 : $price;
                $price = $price == 9993 ? 3.5 : $price;
                $price = $price == 9994 ? 4.5 : $price;
                $price = $price == 9995 ? 5.5 : $price;
                $str = implode(' ', $tmp);
                $arr_number = [];
                foreach($tmp as $k1 => $tmp1){                 
                    if (preg_match('/[a-z*]/', $tmp1, $matches)){                        
                       $bet_type = $tmp1;        
                    }
                    if($tmp1 > 0 || $tmp1 == "00" || $tmp1 == "000" || $tmp1 == "0000"){
                        $arr_number[] = $tmp1; 
                    }
                }
                if(empty($arr_number) && isset($numberArr)){
                    $arr_number = $numberArr[$countII-1];
                }
                $numberArr[$countII] = $arr_number;
                //var_dump("<pre>", $numberArr);
                //dd($arr_number);
                // truong hop tao lao : loai de nam sau so tien
                if(!isset($bet_type) && count($arr) == 1){
                    $tmpMess = explode(" ",$message);
                    foreach($tmpMess as $tmpValue){
                        if (preg_match('/^[a-z]+$/i', $tmpValue, $matches)){                        
                           $bet_type = $tmpValue;
                           break;        
                        }
                    }                  
                }
                
                if(!isset($bet_type) && strlen($arr_number[0]) == 4){
                    
                    $bet_type = 'bl';
                }
                if(!isset($bet_type)){
                    dd($arr_number);
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
                $countII++;          
            }
            
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
              
            try{
                if(!is_array($oneBet['number'])){
                    $keyCacheSession =  $oneBet['channel']."-".$oneBet['number'];  
                    if(!isset($arrSo[$keyCacheSession])){
                        $arrSo[$keyCacheSession] = 1;
                    }else{
                        $arrSo[$keyCacheSession] += 1;
                    }    
                }                
            }catch(\Exception $ex){
                dd($oneBet['number']);
            }           
            Session::put('arrSo', $arrSo);
            echo "<pre>";
            var_dump("arrSo", $arrSo);
            echo "</pre>";
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
                                    if($keyCacheSession == 'dp-0704'){
                                      // dd(2, $oneBet, $bet_type);
                                    }
                                    //dd($oneBet);
                                    echo "<pre>aaaaaaa";
                                    var_dump($oneBet);
                                    //$oneBet['number'] = substr($oneBet['number'], -3);    
                                    if($bet_type == 'x' || $bet_type == 'bl'){
                                        $oneBet['number'] = substr($oneBet['number'], -3);
                                        //dd($oneBet['number']);       
                                    }else{
                                        $oneBet['number'] = substr($oneBet['number'], -2);
                                    } 
                                }elseif($arrSo[$keyCacheSession] == 3){
                                    if($keyCacheSession == 'dp-0704'){
                                        //dd($oneBet);
                                    }
                                    if($bet_type == 'x' || $bet_type == 'bl'){
                                        $oneBet['number'] = substr($oneBet['number'], -3);        
                                    }else{
                                        $oneBet['number'] = substr($oneBet['number'], -2);
                                    }                                    
                                }                       
                                
                            }elseif(strlen($oneBet['number']) == 3 && $bet_type != 'x'){
                                $oneBet['number'] = substr($oneBet['number'], -2);
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
                    $arr = [
                        'channel_id' => $channel_id,
                        'bet_type_id' => $bet_type_id,
                        'message_id' => $message_id,
                        'price' => $oneBet['price'],
                        'number_1' => $this->formatNumber($oneBet['number'][0]),
                        'number_2' => $this->formatNumber($oneBet['number'][1]),
                        'refer_bet_id' => $countDv1 > 1 ? $refer_bet_id : null,
                        'total' => $oneBet['price']*36, // 2 dai x 18 lo x 2 so = 72 lo
                        'is_main' => $refer_bet_id > 0 ? 0 : 1,
                        'bet_day' => date('Y-m-d'),
                        'str_channel' => $str_channel
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
            $arr = [
                'channel_id' => $channel_id,
                'bet_type_id' => $bet_type_id,
                'message_id' => $message_id,
                'price' => $oneBet['price'],
                'number_1' => $this->formatNumber($oneBet['number']),
                'refer_bet_id' => $countDv > 1 ? $refer_bet_id : null,
                'is_main' => $refer_bet_id > 0 ? 0 : 1,
                'str_channel' => $str_channel,
                'total' => $this->calTotal($bet_type_id, $oneBet['price'],$oneBet['number']),
                'bet_day' => date('Y-m-d')                   
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
                    $arr = [
                        'channel_id' => $channel_id,
                        'bet_type_id' => $bet_type_id, // bao lo
                        'message_id' => $message_id,
                        'price' => $oneBet['price'],
                        'number_1' =>  $this->formatNumber($number),
                        'is_main' => $refer_bet_id > 0 ? 0 : 1,
                        'refer_bet_id' => $countDv > 1 ? $refer_bet_id : null,
                        'str_channel' => $str_channel,
                        'total' => $this->calTotal($bet_type_id, $oneBet['price'],  $number),
                        'bet_day' => date('Y-m-d')    
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
                    $arr = [
                        'channel_id' => $channel_id,
                        'bet_type_id' => $bet_type_id, //xiu chu
                        'message_id' => $message_id,
                        'price' => $oneBet['price'],
                        'refer_bet_id' => $countDv > 1 ? $refer_bet_id : null,
                        'number_1' =>  $this->formatNumber($number),
                        'is_main' => $refer_bet_id > 0 ? 0 : 1,
                        'total' => $this->calTotal($bet_type_id, $oneBet['price'],  $number),
                        'bet_day' => date('Y-m-d'),
                        'str_channel' => $str_channel                 
                    ];                    
                   
                    $rs = Bet::create($arr);
                    if($countDv == 1){
                        $refer_bet_id = $rs->id;
                    }
                }
            }
        }
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

        
        if(!empty($arrCapSoDaVong)){
            $str_number = implode('-', $oneBet['number']);
            $str_channel = Channel::getChannelName($channelArr);
            $countDv = 0;
            $refer_bet_id = null;
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
                        'str_channel' => $str_channel 
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
        
        //dd($arr);
        $patternChannel = '/[a-z]/';            
        if (preg_match_all($patternChannel, $arr[0], $matches)){            
            $channel = $arr[0]; // dc, dp, 2d, vl, tp, kg...       
            $arrNew = array_slice($arr, 1, count($arr));
        }else{
            $channel = $arr[count($arr)-1];
            $arrNew = array_slice($arr, 0, -1);    
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
        //echo "<hr><pre>";
        //print_r($arrNew);
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
        var_dump("<hr>", $bet_type);
        $rs  = BetType::where('keyword', $bet_type)->first();
        if($rs){
           return $rs->id;
        }else{
            $bet_type = (preg_replace('/([0-9]*)([a-z])/', '$2', $bet_type));
            $bet_type = $this->formatBetType($bet_type);
            $rs  = BetType::where('keyword', $bet_type)->first();
            if($rs){
               return $rs->id;
            }else{
                dd("11111", $bet_type);
            }
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
                dd($bet_type_id);
                # code...
                break;
        }
        
        var_dump($number);
        if(!isset($total)){
            dd($bet_type_id);
        }
        return $total;
    }
}
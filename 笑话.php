<?php
/**
  * wechat php test
  */

//define your token
define("TOKEN", "weixin");
$wechatObj = new wechatCallbackapiTest();
//$wechatObj->valid();
$wechatObj->responseMsg();
class wechatCallbackapiTest
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){
                
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
				$type = $postObj->MsgType;
				$customrevent = $postObj->Event;
				$latitude  = $postObj->Location_X;
				$longitude = $postObj->Location_Y;
                $keyword = trim($postObj->Content);
                $time = time();
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";             
				switch ($type)
			{   case "event";
				if ($customrevent=="subscribe")
				    {$contentStr = "感谢你的关注\n回复1查看联系方式\n回复2查看最新资讯\n回复3查看法律文书";}
				break;
				case "image";
				$contentStr = "你的图片很棒！";
				break;
				case "location";
				$placeurl="https://maps.googleapis.com/maps/api/place/search/xml?location={$latitude},{$longitude}&radius=3000&sensor=false&key=AIzaSyAoKznDSiW_PzkiRapITcwT-UzwbeMkN1I&language=zh-CN&keyword=餐馆";
		$apistr=file_get_contents($placeurl);
		$apiobj=simplexml_load_string($apistr);
		$nameobj=$apiobj->result[0]->name;
		$addobj=$apiobj->result[0]->vicinity;
		$rateobj=$apiobj->result[0]->rating;
		$contentStr = "你周围餐馆有{$nameobj},地址{$addobj}，google评分{$rateobj}";
		
				break;
				case "link" ;
				$contentStr = "你的链接有病毒吧！";
				break;
				case "text";
				if ($keyword=="笑话")
				{$jokeurl="http://api.94qing.com/?type=joke&msg=";//笑话API
                $contentStr=file_get_contents($jokeurl);
				$contentStr = str_replace("\n","",$contentStr);
				}//读入文件						
				break;					
			default;
			$contentStr ="此项功能尚未开发";	
			}
				$msgType="text";
				$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
                

        }else {
        	echo "";
        	exit;
        }
    }
		
	private function checkSignature()
	{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];	
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}

?>
<?php
	
   $host = ".kss.ksyun.com/";
   $proto = "http://";
   $signableQueryString = array(
        'acl', 'delete', 'lifecycle', 'location', 'logging', 'notification',
        'partNumber', 'policy', 'requestPayment', 'torrent', 'uploadId',
        'uploads', 'versionId', 'versioning', 'versions', 'website',
        'response-cache-control', 'response-content-disposition',
        'response-content-encoding', 'response-content-language',
        'response-content-type', 'response-expires', 'restore', 'tagging', 'cors'
    );
	
    function args_deal($request){
		$query = "?";
		foreach($request as $k=>$v){
			$query.=$k;
			if((strlen($v)!=0 && $v != "") || ($v=='0')){
				$v = rawurlencode($v);
				$query.="=".$v.'&';
			}
			else{
				$query.='&';	
			}
		}
		return substr($query,0,-1);
	}
   
    $signableHeaders = array('Content-MD5', 'Content-Type');
    
    function url(array $credentials){
    	global $host;
    	global $proto;
    	$query = args_deal($credentials["query"]);
    	$sign = sign_create($credentials);
    	$sign = rawurlencode($sign);
    	$object = rawurlencode($credentials['object']);
    	$url = $proto.$credentials['bucket'].$host.$object.$query.'&Signature='.$sign;
    	echo $url;
    	
    }

    function sign_create(array $credentials)
    {
        $stringToSign = createCanonicalizedString($credentials, $credentials["query"]["Expires"]);
        $sign = signString($stringToSign, $credentials["crendit"]["access_key"]);
        return $sign;  
    }

    function signString($string, $credentials)
    {
        return base64_encode(hash_hmac('sha1', $string, $credentials, true));
    }

    function createCanonicalizedString(array $req, $expires = null)
    {
    	global $signableQueryString;
        $buffer = $req['method'] . "\n\n\n";
        $buffer.= $expires."\n";
        $bucket = array_key_exists("bucket", $req) ? $req["bucket"]:null;
        $buffer .= $bucket ? "/{$bucket}" : '';
        
        $object = array_key_exists("object", $req) ? $req["object"]:null;
        if ($object != null){
        	$object = rawurlencode($object);
        }
        $buffer .= $object ? "/{$object}" : '';
      
        $query = $req["query"];
        $first = true;
        foreach ($signableQueryString as $key) {
            if ( array_key_exists($key, $query)  ) {
            	$value = $query[$key];
                $buffer .= $first ? '?' : '&';
                $first = false;
                $buffer .= $key;
                if ($value !== "") {
                    $buffer .= "={$value}";
                }
            }
        }
        return $buffer;    
    }
  
    #This is a demo to use signature api
    # First create a array var named $listall, in this array, add your crendit message 
    # in the query field, you choose useful query args to add
    # 'Expires' mark the expires time , in the example, we choose one day
    $listall = array(
			'crendit' => array(
			'access_id' => "your access id",
			'access_key' => "your access key",
			),
			'method' => 'GET',
			'bucket' => 'your bucket name',
			'object' => 'your file name',
			'header' => array(),
			'query' => array('response-content-disposition' =>'attachment; filename=fname.ext',
			'response-content-type' => 'text/html',
			'Expires' => $t = time() + (3600*24),
			'KSSAccessKeyId' => "your access id",
			)
           );
           
    echo url($listall);
    
   
    

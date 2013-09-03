<?php
# $access_id is your AccessId
# $access_key is your AccessSecretKey
# $key is upload filename
# $bucket_name is your bucketname
# $host is server address
# $url is $host/$bucketname
# $policy is the rule you make for customers
# $policy has success_action_redirect to allow your redirect your address
# $sign is credentials

# policy must include expiration and condition key, condition bucket
# form item except　"KSSAccessKeyId", "Signature", "file", "Policy", "bucket", "key",
# must be include in the policy

$access_id = "your AccessId";
$access_key = "your AccessSecretKey";
$bucket_name = "your bucket name";
$redirect = "http://ksyun.com/";
$host = "http://".$bucket_name.".kss.ksyun.com/";
$key = "your file name";

function iso8601($time=false) {
    if ($time === false) $time = time();
    $date = date('Y-m-d\TH:i:s\.Z', $time);
    return (substr($date, 0, strlen($date)-2).'Z');
}

function def_policy(){
	global $key;
	global $redirect;
	$t = time() + (3600*24);
	$exp = iso8601($t);
	$policy = "{\"expiration\":\"$exp\",
\"conditions\": [
{\"bucket\": \"yourbucketname\"},
[\"starts-with\", \"\$key\", \"$key\"],
[\"content-length-range\", 0,  5000000000 ],
{\"success_action_redirect\": \"$redirect\"},
[\"starts-with\", \"\$Content-Type\", \"text/html\" ],
{\"acl\":\"public-read\"}
]
}";

	return $policy;
}

function cal_sign($policy){
	global $access_key;
	$sign = base64_encode(hash_hmac('sha1',$policy, $access_key, true));
	return $sign;
};

$policy = def_policy();
$policy = base64_encode($policy);
$sign = cal_sign($policy);

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<form action="<?php echo $host;?>" method="post" enctype="multipart/form-data">
	Key to upload: <input type="input" name="key" value="<?php echo $key;?>"/><br />
	<input type="hidden" name="KSSAccessKeyId" value="<?php echo $access_id;?>"/>
	<input type="hidden" name="Policy" value="<?php echo $policy;?>"/>
	<input type="hidden" name="acl" value="public-read" />
	<input type="hidden" name="Signature" value="<?php echo $sign;?>"  />
	<input type="hidden" name="success_action_redirect" value="<?php echo $redirect;?>"  />
	<input type="hidden" name="Content-Type" value="text/html" />
	File: <input type="file" name="file" /> <br />
	<input type="submit" name="submit" value="Upload to KSS S3" />
</form>
</html>

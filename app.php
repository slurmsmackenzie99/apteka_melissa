<?php

// let's assume CLIENT-SIDE sends a JSON POST request to our app with this body
$received = array(
    "payed" => true,
    "orderNo" => "ABC123",
    "shop" => array("id" => "A", "type" => 12),
    "yourName" => "Paweł Zmarlak"
);

// OUR APP RECOGNIZES WHICH SHOP TO SEND REQUEST TO, APPLIES THE LOGIC AND SENDS REQUEST TO OUR STORE 
$postRequest = array(
    "payed" => $received["payed"],
    "orderNo" => $received["orderNo"],
    "yourName" => $received["yourName"]
);

// recognize which shop to send request to (A, B or C)
$shop_type = $received["shop"]["id"];

// initialize req url address
$ch = curl_init("https://www.apteka-melissa.pl/rekrutacja/sklep/$shop_type");

// specify HTTP method
curl_setopt($ch, CURLOPT_POST, 1);

// payload (apply multidimensional array translator)
curl_setopt($ch, CURLOPT_POSTFIELDS, build_post_fields($postRequest));
curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// auth headers
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'api-id: rekrutacja',
    'api-key: AAV$BM%FIH^SAX#2CK8JU47QU$3L$J!3Q&9BVYIJWAND#W3'
));

// see the response
$fp = fopen("res.txt", "w");
curl_setopt($ch, CURLOPT_FILE, $fp);
curl_setopt($ch, CURLOPT_HEADER, 1);


// get response
$response = curl_exec($ch);
// decode response
$result = json_decode($response);

// perform the req
curl_exec($ch);

//error management
if (curl_error($ch)) {
    fwrite($fp, curl_error($ch));
}
curl_close($ch);
fclose($fp);

//function used to build strings from multidimensional arrays (even though the current, specified payload is two-dimensional)
function build_post_fields( $data,$existingKeys='',&$returnArray=[]){
    if(($data instanceof CURLFile) or !(is_array($data) or is_object($data))){
        $returnArray[$existingKeys]=$data;
        return $returnArray;
    }
    else{
        foreach ($data as $key => $item) {
            build_post_fields($item,$existingKeys?$existingKeys."[$key]":$key,$returnArray);
        }
        return $returnArray;
    }
}
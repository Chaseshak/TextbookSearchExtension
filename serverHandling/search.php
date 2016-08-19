<?php
// Holds constants for API Keys (cfg.php is not on github due to private information)
require "cfg.php";

if(!isset($_POST['ISBN'])){
    die("You do not have permission to view this page!");
}

$isbn = $_POST['ISBN'];

$amazon_request_url = generateAmazonRequest($isbn);

//echo "Request: $amazon_request_url";


$content = parseAmazonResponse($amazon_request_url);

//echo $content;

$p = xml_parser_create();
xml_parse_into_struct($p, $content, $vals, $index);
xml_parser_free($p);
echo "Index array\n";
print_r($index);
echo "\nVals array\n";
print_r($vals);

// HELPER QUERY FUNCTIONS \\

function parseAmazonResponse($url){
    // create curl resource
    $ch = curl_init();

    // set url
    curl_setopt($ch, CURLOPT_URL, "$url");

    //return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // $output contains the output string
    $output = curl_exec($ch);

    // close curl resource to free up system resources
    curl_close($ch);

    return $output;
}

function generateAmazonRequest($isbn){
    // Your AWS Access Key ID, as taken from the AWS Your Account page
    $aws_access_key_id = AWS_ACCESS_ID;

    // Your AWS Secret Key corresponding to the above ID, as taken from the AWS Your Account page
    $aws_secret_key = AWS_SECRET_KEY;

    // The region you are interested in
    $endpoint = "webservices.amazon.com";

    $uri = "/onca/xml";

    $params = array(
        "Service" => "AWSECommerceService",
        "Operation" => "ItemLookup",
        "AWSAccessKeyId" => AWS_ACCESS_ID,
        "AssociateTag" => ASSOCIATE_TAG,
        "ItemId" => "$isbn",
        "IdType" => "ISBN",
        "ResponseGroup" => "Images,ItemAttributes,OfferFull,OfferListings,Offers",
        "SearchIndex" => "Books"
    );

    // Set current timestamp if not set
    if (!isset($params["Timestamp"])) {
        $params["Timestamp"] = gmdate('Y-m-d\TH:i:s\Z');
    }

    // Sort the parameters by key
    ksort($params);

    $pairs = array();

    foreach ($params as $key => $value) {
        array_push($pairs, rawurlencode($key)."=".rawurlencode($value));
    }

    // Generate the canonical query
    $canonical_query_string = join("&", $pairs);

    // Generate the string to be signed
    $string_to_sign = "GET\n".$endpoint."\n".$uri."\n".$canonical_query_string;

    // Generate the signature required by the Product Advertising API
    $signature = base64_encode(hash_hmac("sha256", $string_to_sign, $aws_secret_key, true));

    // Generate the signed URL
    $request_url = 'http://'.$endpoint.$uri.'?'.$canonical_query_string.'&Signature='.rawurlencode($signature);

    return $request_url;
}

?>
#!/usr/bin/php
<?php
$cf_update = false;
$cf_content = file_get_contents("https://ipv4.icanhazip.com");
$cf_content = trim($cf_content);
$ip_file = __DIR__ . "/current_ip.txt";
$ip_file_content = file_get_contents($ip_file);
$ip_file_content = trim($ip_file_content);
$cf_account_name = "<cloudflare acount username>";
$cf_api_key = "<cloudflare acount global api-key>";

//Email settings for sending email notification when IP has been updated
$email_to = "recipient@example.com";
$email_subject = "Your IP has been updated";
$email_headers = "From: sender@example.com";
$email_body = "IP has been updated to: " . $cf_content;
$email_send = true;

if (!$cf_content){
    print_r("Failed to get ip from remote server. | " . date("Y-m-d h:i:s") . PHP_EOL);

    exit();
}

if ($cf_content !== $ip_file_content){
    $cf_update = true;
    file_put_contents($ip_file, $cf_content);
}

//Use this command to get the DNS Record ID for your domain:
//curl -X GET "https://api.cloudflare.com/client/v4/zones/<cloudflare_zone_id>/dns_records?type=A&name=<domain_name>&page=1&per_page=20&order=type&direction=desc&match=all" \
//     -H "X-Auth-Email: <cloudflare acount username>" \
//     -H "X-Auth-Key: <cloudflare acount global api-key>" \
//     -H "Content-Type: application/json"

//Zone ID's
$cf_zone_array = array(
    "<dns_zone_id_1>", //domain1.com
    "<dns_zone_id_2>", //domain2.com
    "<dns_zone_id_3>", //domain3.com
    "<dns_zone_id_4>", //domain4.com
);

//DNS Record ID's
$cf_dns_array = array(
    "<dns_record_id_1>" => "domain1.com",
    "<dns_record_id_2>" => "domain2.com",
    "<dns_record_id_3>" => "domain3.com",
    "<dns_record_id_4>" => "domain4.com"
);

if ($cf_update == true){

    $count=-1;

    foreach($cf_dns_array as $dns_id => $dns_name) {

        $count++;

        $url = "https://api.cloudflare.com/client/v4/zones/" . (string)$cf_zone_array[$count] . "/dns_records/" . (string)$dns_id . "";

        $data = array(
            "type" => (string)"A",
            "name" => (string)$dns_name,
            "content" => (string)$cf_content,
            "proxiable" => (bool)true,
            "proxied" => (bool)true
        );

        $data = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "X-Auth-Email: " . $cf_account_name . "",
            "X-Auth-Key: " . $cf_api_key . "",
            "Content-Type: application/json")
        );
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($ch);

        if (!$response || curl_errno($ch)) {
            print_r( curl_error($ch) . " | " . date("Y-m-d h:i:s") . PHP_EOL);
        }

        curl_close($ch);

        print_r("IP updated to " . $cf_content . " for domain " . $dns_name . " | " . date("Y-m-d h:i:s") . PHP_EOL);

        print_r($response . " | " . date("Y-m-d h:i:s") . PHP_EOL);

        //Example of the Curl Command -  You can run this in a terminal for testing:
        //curl -X PUT "https://api.cloudflare.com/client/v4/zones/<cloudflare_zone_id>/dns_records/<cloudflare_dns_id>" \
		//     -H "X-Auth-Email: <cloudflare acount username>" \
		//     -H "X-Auth-Key: <cloudflare acount global api-key>" \
		//     -H "Content-Type: application/json" \
        //     --data '{"type":"A","name":"<domain_name>","content":"<new_ip_address>","proxiable":true,"proxied":true,"ttl":1}'

    }
 
 	//Send email notification
 	if ($email_send == true){
		if (mail($email_to, $email_subject, $email_body, $email_headers)) {
			print_r("Email notification sent to " . $email_to  . " | " . date("Y-m-d h:i:s") . PHP_EOL);
		} else {
			print_r("Email notification error."  . " | " . date("Y-m-d h:i:s") . PHP_EOL);
		}
 	}

    exit();

} else {

    print_r("No update needed. | ". date("Y-m-d h:i:s") . PHP_EOL);

    exit();

}
?>
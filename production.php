<?php

header('Content-Type: application/json; charset=utf-8');


// CURL 1
$handle = curl_init();
curl_setopt($handle, CURLOPT_URL, "https://api.sello.io/v5/orders?filter[is_delivered]=false&size=1&sort=created_at&sort_direction=desc");
curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
curl_setopt($handle, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json',
    'Authorization: a64a5d5010c22a0a911a22edc3f4b610:a620fdf6f620c272edc390dac409d23c91f624eae86acb773b24617cf2d80e4e'
]);

$response = curl_exec($handle);
$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

$orders   = array();

$r = json_decode($response);


foreach ($r->orders as $order ) {
    array_push($orders, $order->id);
}


// END CURL 1
$new_orders = array();

// CURL 2
$orders_txt = fopen('orders.txt', 'r');
// OR SET 'a' instead of 'w' if you want to append
if(!(file_exists('orders.txt'))){
    $orders_txt = fopen('orders.txt', 'r');
}

foreach ($orders as $id ) {
    $handle2 = curl_init();
    curl_setopt($handle2, CURLOPT_URL, "https://api.sello.io/v5/orders/".$id."/rows");
    curl_setopt($handle2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle2, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: a64a5d5010c22a0a911a22edc3f4b610:a620fdf6f620c272edc390dac409d23c91f624eae86acb773b24617cf2d80e4e'
    ]);

    $response2 = curl_exec($handle2);
    $code2 = curl_getinfo($handle2, CURLINFO_HTTP_CODE);
    $r2 = json_decode($response2);
    // GETTING AFTERBUY PRODUCT ID
    $file = 'orders.txt';
    $searchfor = $id;
    // get the file contents, assuming the file to be readable (and exist)
    $contents = file_get_contents($file);
    // escape special characters in the query
    $pattern = preg_quote($searchfor, '/');
    // finalise the regular expression, matching the whole line
    $pattern = "/^.*$pattern.*\$/m";
    // search, and store all matching occurences in $matches
    if(preg_match($pattern, $contents)){
        continue;
    }else{
        array_push($new_orders, $id);
    }
}


if (empty($new_orders)) {
    die();
}else{
$orders_txt = fopen('orders.txt', 'a');
// OR SET 'a' instead of 'w' if you want to append
if(!(file_exists('orders.txt'))){
    $orders_txt = fopen('orders.txt', 'a');
}

foreach ($new_orders as $id ) {
    // CURL 1
    $handle = curl_init();
    curl_setopt($handle, CURLOPT_URL, "https://api.sello.io/v5/orders/".$id);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: a64a5d5010c22a0a911a22edc3f4b610:a620fdf6f620c272edc390dac409d23c91f624eae86acb773b24617cf2d80e4e'
    ]);

    $response = curl_exec($handle);
    $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);


    $r = json_decode($response);

    

    $handle1 = curl_init();
    curl_setopt($handle1, CURLOPT_URL, "https://api.sello.io/v5/orders/".$id."/rows");
    curl_setopt($handle1, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle1, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: a64a5d5010c22a0a911a22edc3f4b610:a620fdf6f620c272edc390dac409d23c91f624eae86acb773b24617cf2d80e4e'
    ]);

    $response1 = curl_exec($handle1);
    $code1 = curl_getinfo($handle1, CURLINFO_HTTP_CODE);
    $r1 = json_decode($response1);



    // KL 1
    $reference = $r1[0]->reference;
	$quantity  = $r1[0]->quantity;

    $Versandart     = "Standart";
    $Versandkosten  = 0;


    if(preg_match('/.*(-1401|-1402)$/', $reference)){
       	$Versandart 	= "Standard_TemperedGlass";
        $Versandkosten  = 1;
        
    }

    // KL 2

    $customer_first_name   = $r->customer_first_name;
    $customer_last_name    = $r->customer_last_name;
    $customer_address      = $r->customer_address;
    $customer_city 	 	   = $r->customer_city;
    $customer_country_code = $r->customer_country_code;
    $customer_zip          = $r->customer_zip;
    $customer_alias        = $r->customer_alias;
    $customer_telephone    = $r->customer_mobile;
    $customer_address_2    = $r->customer_address_2;
    $created_at            = $r->created_at;
    $updated_at            = $r->updated_at;

    $total_eur             = $r->total_eur;
    $shipping_cost         = $r->shipping_cost;
    $orderId               = 20254824;
    $time_created_at       = strtotime($created_at);
    $time_created_at       = date("d.m.Y H:i:s", $time_created_at); 

    $time_updated_at       = strtotime($updated_at);
    $time_updated_at       = date("d.m.Y", $time_updated_at); 

    // $total_cost            = $price + $shipping_cost;





    $afterbuy = curl_init();
    curl_setopt($afterbuy, CURLOPT_URL, "https://api.afterbuy.de/afterbuy/ABInterface.aspx");
    curl_setopt($afterbuy, CURLOPT_HTTPHEADER, [
        'Accept: text/xml',
        'Accept-Encoding: UTF-8', 
        'Content-Type: text/xml; charset=UTF-8',
        'Authorization: a64a5d5010c22a0a911a22edc3f4b610:a620fdf6f620c272edc390dac409d23c91f624eae86acb773b24617cf2d80e4e'
    ]);

    // GETTING AFTERBUY PRODUCT ID
    $file = 'excellist.txt';
    $searchfor = $reference;
	echo $searchfor."<br>";
    // the following line prevents the browser from parsing this as HTML.
    header('Content-Type: text/plain');

    // get the file contents, assuming the file to be readable (and exist)
    $contents = file_get_contents($file);
    // escape special characters in the query
    $pattern = preg_quote($searchfor, '/');
    // finalise the regular expression, matching the whole line
    $pattern = "/^.*$pattern.*\$/m";
    // search, and store all matching occurences in $matches
    if(preg_match_all($pattern, $contents, $matches)){
       $befproductID = implode("\n", $matches[0]);
       $befproductID = explode('	', $befproductID);
       $productID 	 = $befproductID[1]; 
    }
    else{
       echo "No matches found";
    }

    $xml = '<?xml version="1.0" encoding="utf-8"?>
    <Request>
      <AfterbuyGlobal>
     <AccountToken>332B6031-74E8-468E-8B39-C04CE25D2D5C</AccountToken><br/>
    <PartnerToken>fc225b3a-5a0b-4ab0-99c4-01a69f74f131</PartnerToken>
        <CallName>GetShopProducts</CallName>
        <DetailLevel>2</DetailLevel>
        <ErrorLanguage>DE</ErrorLanguage>
      </AfterbuyGlobal>
      <DataFilter>
      <Filter>
        <FilterName>ProductID</FilterName>
        <FilterValues>
          <FilterValue>'.$productID.'</FilterValue>
        </FilterValues>
       </Filter>
       </DataFilter>
      <MaxShopItems>1</MaxShopItems>
    </Request>';

    //curl_setopt($afterbuy, CURLOPT_SSL_VERIFYPEER, true)
    curl_setopt($afterbuy, CURLOPT_POSTFIELDS ,$xml);
    curl_setopt($afterbuy, CURLOPT_RETURNTRANSFER ,true);
    $response3 = curl_exec($afterbuy);
    $r3        = json_decode($response3);
    $code3 = curl_getinfo($afterbuy, CURLINFO_HTTP_CODE);


    // var_dump($response3);
    // title
    $befName = explode('<Name>', $response3);
    $Name3    = explode('</Name>', $befName[1]);
    $Name2   = $Name3[0];
    $Name1 = explode('<![CDATA[', $Name2);
    $Name = explode(']]>', $Name1[1]);
    $title = $Name[0];
    // echo $title;
    
    // price
    $befPrice = explode('<BuyingPrice>', $response3);
    $Price    = explode('</BuyingPrice>', $befPrice[1]);
    $price    = $Price[0];
    // echo $price;

    // productID
    $befProductID = explode('<Anr>', $response3);
    $ProductID    = explode('</Anr>', $befProductID[1]);
    $productID    = $ProductID[0];
    // echo $productID;

    // tax
    $befTax = explode('<TaxRate>', $response3);
    $Tax    = explode('</TaxRate>', $befTax[1]);
    $tax    = $Tax[0];
    // echo $tax;

    // quantity
    $befQuantity = explode('<Quantity>', $response3);
    $Quantity    = explode('</Quantity>', $befQuantity[1]);
    //$quantity    = $Quantity[0];
    // echo $quantity;

    // weight
    $befWeight = explode('<Weight>', $response3);
    $Weight    = explode('</Weight>', $befWeight[1]);
    $weight    = $Weight[0];
    // echo $weight;

    // QUERY OR POSTFIELD

    $data_afterbuy = array(
        // K
        'Action'            => 'new',
        'Lieferanschrift'   => 1,
        'Partnerid'         => 112292,
        'PartnerPass'       => 'qtRTKN(rSXBT1ehrmOiHb77b',
        'UserID'            => 'rush-trading',
        'Kemail'            => 'sverige@rush-trading.de',
        'KFirma'            => 'Rush Trading International Handelsbolag',
        'KVorname'          => 'Olof',
        'KNachname'         => 'Martinsson',
        'KStrasse'          => 'Randersvägen 24A',
        'KPLZ'              => 21745,
        'KOrt'              => 'Malmö',
        'KLand'             => 'SE',
        'Ktelefon'          => '+4915258131375',
      	'Kundenerkennung'   => 1,
        'PosAnz'            => 1,
        'ZahlartenAufschlag'=> 0,
        'NoVersandCalc'     => 0,
        'Zahlart'           => 'PayPal - Standard',
        'SoldCurrency'      => 'EUR',
        'Bestandart'        => 'Shop',
        'BuyDate'           => $time_created_at,
        'PayDate'           => $time_updated_at,

        

        // KL 1
        'Artikelname_1'     => $title,
        'ArtikelEPreis_1'   => $price,
        'Artikelnr_1'       => $productID,
        'ArtikelMwSt_1'     => 0,
        'ArtikelMenge_1'    => $quantity,
        'ArtikelGewicht_1'  => $weight,
        //'ArtikelStammID_1'  => $orderId,

        // KL 2
        'KLVorname'         => $customer_first_name,
        'KLNachname'        => $customer_last_name,
        'KLStrasse'         => $customer_address,
        'Kbenutzername'     => $customer_alias,
        'KLLand'            => $customer_country_code,
        // 'KLBundesland'       => $customer_city,
        'KLPLZ'             => $customer_zip,
        'KLOrt'             => $customer_city,
        'KLFirma'           => $customer_address_2,
        'KLTelefon'         => $customer_telephone,

         // SOMETHING NOT K OR KL
        'Versandart'        => $Versandart,
        'Versandkosten'     => $Versandkosten,
    );

    print_r($data_afterbuy);
    $afterbuy = curl_init();
    curl_setopt($afterbuy, CURLOPT_URL, "https://api.afterbuy.de/afterbuy/ShopInterfaceUTF8.aspx");
    //curl_setopt($afterbuy, CURLOPT_SSL_VERIFYPEER, true)
    curl_setopt($afterbuy, CURLOPT_POSTFIELDS ,http_build_query($data_afterbuy));
    curl_setopt($afterbuy, CURLOPT_RETURNTRANSFER ,true);
    $response3 = curl_exec($afterbuy);
    $r3        = json_decode($response3);
    $code3 = curl_getinfo($afterbuy, CURLINFO_HTTP_CODE);
	var_dump($response3);	
  
    // WRITE TO TXT
    $befAID = explode('<AID>', $response3);
    $AID    = explode('</AID>', $befAID[1]);


    

    fwrite($orders_txt, $id."-".$AID[0]."\n");
    // file_put_contents('orders.txt', $id."-".$AID[0].PHP_EOL, FILE_APPEND);
    
    
    // END WRITE TO TXT
    curl_close($afterbuy);
}
fclose($orders_txt);
}
// END CURL 2
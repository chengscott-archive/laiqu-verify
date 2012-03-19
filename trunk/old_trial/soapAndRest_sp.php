<?php
/*TODO: 下文内容是根据PHP开发书籍p874页开始摘录的,
 * 里面涉及到了REST 和 SOAP的使用，值的参考.
 * 后面的页面OO的涉及也值得学习*/

// Perform a query to get a page full of products from a browse node // Switch between XML/HTTP and SOAP in constants.php
// Returns an array of Products
function browseNodeSearch($browseNode, $page, $mode) {
    $this->Service = "AWSECommerceService"; 
    $this->Operation = "ItemSearch"; $this->AWSAccessKeyId = DEVTAG;
    $this->AssociateTag = ASSOCIATEID;
    $this->BrowseNode = $browseNode; $this->ResponseGroup = "Large";
    $this->SearchIndex= $mode;
    $this->Sort= "salesrank";
    $this->TotalPages= $page;
    if(METHOD=='SOAP') {
        $soapclient = new nusoap_client( 'http://ecs.amazonaws.com/AWSECommerceService/AWSECommerceService.wsdl', 'wsdl');

        $soap_proxy = $soapclient->getProxy();

        $request = array ('Service' => $this->Service,
                    'Operation' => $this->Operation, 'BrowseNode' => $this->BrowseNode, 
                    'ResponseGroup' => $this->ResponseGroup, 'SearchIndex' => $this->SearchIndex, 
                    'Sort' => $this->Sort, 'TotalPages' => $this->TotalPages);

        $parameters = array('AWSAccessKeyId' => DEVTAG, 'AssociateTag' => ASSOCIATEID, 'Request'=>array($request));

        // perform actual soap query
        $result = $soap_proxy->ItemSearch($parameters);
        if(isSOAPError(jjkjkkjjhhkkjkjjkdd@aj@aj@aj$result)) { 
            return false;
        }
        $this->totalResults = $result['TotalResults'];
        foreach($result['Items']['Item'] as $product) {
            $this->products[] = new Product($product);
        }
        unset($soapclient);
        unset($soap_proxy);
    } else {
        // form URL and call parseXML to download and parse it
        ￼$this->url = "http://ecs.amazonaws.com/onca/xml?". "Service=".$this->Service. "&Operation=".$this->Operation. "&AssociateTag=".$this->AssociateTag. "&AWSAccessKeyId=".$this->AWSAccessKeyId. "&BrowseNode=".$this->BrowseNode. "&ResponseGroup=".$this->ResponseGroup.
        "&SearchIndex=".$this->SearchIndex. "&Sort=".$this->Sort. "&TotalPages=".$this->TotalPages;
        $this->parseXML();
    }
        return $this->products;
}
//Using REST to Make a Request and Retrieve a Result
// Parse the XML into Product object(s)
function parseXML() 
{
    // suppress errors because this will fail sometimes 
    $xml = @simplexml_load_file($this->url);
    if(!$xml) {
    //try a second time in case just server busy 
        $xml = @simplexml_load_file($this->url); 
        if(!$xml) 
        {
            return false; 
        }
    }
    $this->totalResults = (integer)$xml->TotalResults; 
    foreach($xml->Items->Item as $productXML) {
        $this->products[] = new Product($productXML); 
    }

}

/*Further Reading
A million books and online resources are available on the topics of XML and Web Services. A great place to start is always at the W3C.You can look at the XML Working Group page at http://www.w3.org/XML/Core/ and the Web Services Activity page at http://www.w3.org/2002/ws/ just as a beginning.*/
?>

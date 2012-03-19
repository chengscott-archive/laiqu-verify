<?php

require_once "HTTP/Request2.php";

class RESTClient 
{

    //private $root_url = "";
    private $user_name = "";
    private $password = "";
    private $response = "";
    private $responseBody = "";
    private $req = null;
    private $curr_url = null;

    public function __construct($url = "", $user_name = "", $password = "") {
        //$this->root_url = $this->curr_url = $root_url;
        $this->user_name = $user_name;
        $this->password = $password;
        if ($url != "") {
            $this->createRequest($url, "GET");
            $this->sendRequest();
        }
        return true;
    }

    public function createRequest($url, $method = 'GET', $arr = null) {
        //$this->curr_url = $url;
        $this->req =& new HTTP_Request2($url);
        $this->curr_url = $this->req->getUrl();
        if ($this->user_name != "" && $this->password != "") {
           $this->req->setAuth($this->user_name, $this->password);
        }        

        switch($method) {
            case "GET":
                $this->req->setMethod(HTTP_Request2::METHOD_GET);
                break;
            case "POST":
                $this->req->setMethod(HTTP_Request2::METHOD_POST);
                $this->addPostParameter($arr);
                break;
            case "PUT":
                $this->req->setMethod(HTTP_Request2::METHOD_PUT);
                // to-do
                break;
            case "DELETE":
                $this->req->setMethod(HTTP_Request2::METHOD_DELETE);
                // to-do
                break;
        }
    }

    private function addPostParameter($arr) {
        if ($arr != null) {
            $this->req->addPostParameter($arr);
        }
    }

    public function sendRequest() {
        $this->response = $this->req->send();

        //if (PEAR::isError($this->response)) {
            //echo $this->response->getMessage();
            //die();
        //} else {
        $this->responseBody = $this->response->getBody();
    }

    public function getResponse() {
        return $this->responseBody;
    }

    public function getUrl() {
        return $this->curr_url;
    }

    public function setUrl($url) {
        if (!$this->req)
            return false;
        $this->req->setUrl($url);
    }

    public function setHeader($header)
    {
        if($this->req)
            $this->req->setHeader($header);
    }
}
?>

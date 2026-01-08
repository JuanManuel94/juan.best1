<?php

class Router
{
    private $host;
    private $port;
    private $user;
    private $password;
    private $token;

    public $connected = false;

    public function __construct($host, $port, $user, $password, $try_connect = true)
    {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
        $this->token = base64_encode("$user:$password");

        if ($try_connect && $this->APIQuickTest()->success) {
            $this->connected = $this->APIGetSystemResources()->success;
        }
    }


    // /IP/FIREWALL

    //USE: Obtain all address list from firewall
    public function APIListFirewallAddress()
    {
        return $this->RequestBuilder("ip/firewall/address-list", "GET");
    }

    //USE: Obtain an address if exists in firewall
    public function APIGetFirewallAddress($address, $list)
    {
        $body = (object) array();
        $body->{".query"} = ["address=$address", "list=$list"];
        return $this->RequestBuilder("ip/firewall/address-list/print", "POST", $body, ["Content-Type: application/json"]);
    }

    //USE: Add an address to a firewall list
    public function APIAddFirewallAddress($address, $list, $comment)
    {
        $res = (object) array();

        if (count($this->APIGetFirewallAddress($address, $list)->data) == 0) {
            $body = (object) array();

            $body->address = $address;
            $body->list = $list;
            $body->comment = $comment;

            $res = $this->RequestBuilder("ip/firewall/address-list", "PUT", $body, ["Content-Type: application/json"]);
        } else {
            $res->success = false;
            $res->message = "Address already exists";
        }

        return $res;
    }

    //USE: Delete an address from a firewall list
    public function APIRemoveFirewallAddress($address, $list)
    {
        $res = (object) array();

        $addl = $this->APIGetFirewallAddress($address, $list);

        if ($addl->success) {
            if (count($addl->data) > 0) {
                $id = $addl->data[0]->{".id"};
                $res = $this->RequestBuilder("ip/firewall/address-list/$id", "DELETE");
            } else {
                $res->success = false;
                $res->message = "Address does not exists in list";
            }
        } else {
            $res = $addl;
        }

        return $res;
    }

    // /QUEUES

    //USE: Obtain simple queues list
    public function APIListQueuesSimple()
    {
        return $this->RequestBuilder("queue/simple", "GET");
    }

    //USE: Obtain a simple queue by address
    public function APIGetQueuesSimple($address)
    {
        $body = (object) array();
        $body->{".query"} = ["target=$address/32"];
        return $this->RequestBuilder("queue/simple/print", "POST", $body, ["Content-Type: application/json"]);
    }

    //USE: Obtain a simple queue by address
    public function APIAddQueuesSimple($name, $address, $maxlimit)
    {
        $res = (object) array();

        if (count($this->APIGetQueuesSimple($address)->data) == 0) {
            $body = (object) array();

            $body->name = $name;
            $body->target = $address;
            $body->{"max-limit"} = $maxlimit;

            $res = $this->RequestBuilder("queue/simple", "PUT", $body, ["Content-Type: application/json"]);
        } else {
            $res->success = false;
            $res->message = "Address already exists";
        }

        return $res;
    }
    //USE: Modify a simple queue by id
    public function APIModifyQueuesSimple($id, $name, $address, $maxlimit)
    {
        $res = (object) array();

        if (!is_null($this->RequestBuilder("queue/simple/$id", "GET")->data->name)) {
            $body = (object) array();

            $body->name = $name;
            $body->target = $address;
            $body->{"max-limit"} = $maxlimit;

            $res = $this->RequestBuilder("queue/simple/$id", "PATCH", $body, ["Content-Type: application/json"]);
        } else {
            $res->success = false;
            $res->message = "Address does not exists";
        }

        return $res;
    }

    //USE: Delete  a simple queue by address
    public function APIDeleteQueuesSimple($address)
    {
        $res = (object) array();

        $sq = $this->APIGetQueuesSimple($address)->data;
        if (count($sq) != 0) {
            $id = $sq[0]->{".id"};

            $res = $this->RequestBuilder("queue/simple/$id", "DELETE", ["Content-Type: application/json"]);
        } else {
            $res->success = false;
            $res->message = "Address does not exists";
        }

        return $res;
    }

    // /SYSTEM

    //USE: Obtain system resources
    public function APIGetSystemResources()
    {
        return $this->RequestBuilder("system/resource", "GET");
    }

    // /PPP

    //USE: Obtain simple queues list
    public function APIListPPPSecrets()
    {
        return $this->RequestBuilder("ppp/secret", "GET");
    }

    // Nueva función para obtener el PPP Secret por nombre
    public function APIGetPPPSecretByName($name)
    {
        $body = (object) array();
        $body->{".query"} = ["name=$name"];
        return $this->RequestBuilder("ppp/secret/print", "POST", $body, ["Content-Type: application/json"]);
    }


    //USE: Obtain a simple queue by address
    public function APIGetPPPSecret($address)
    {
        $body = (object) array();
        $body->{".query"} = ["remote-address=$address"];
        return $this->RequestBuilder("ppp/secret/print", "POST", $body, ["Content-Type: application/json"]);
    }

    //USE: Obtain a simple queue by address
    public function APIAddPPPSecret($name, $address, $password, $localAddress, $profile = null)
    {
        $res = (object) array();

        if (count($this->APIGetPPPSecret($address)->data) == 0) {
            $body = (object) array();
            $body->name = $name;
            $body->{"remote-address"} = $address;
            $body->{"local-address"} = $localAddress;
            $body->password = $password;
            $body->service = "pppoe";

            if (isset($profile) && $profile <> '') {
                $body->profile = $profile;
            }

            $res = $this->RequestBuilder("ppp/secret", "PUT", $body, ["Content-Type: application/json"]);
        } else {
            $res->success = false;
            $res->message = "Address already exists";
        }

        return $res;
    }
    //USE: Modify a simple queue by id
    public function APIModifyPPPSecret($id, $name, $address, $password, $localAddress, $profile = null)
    {
        $body = (object) array();
        $body->name = $name;
        $body->{"remote-address"} = $address;
        $body->{"local-address"} = $localAddress;
        $body->password = $password;

        if (isset($profile) && $profile <> '') {
            $body->profile = $profile;
        }

        return $this->RequestBuilder("ppp/secret/$id", "PATCH", $body, ["Content-Type: application/json"]);
    }

    //USE: Delete  a simple queue by address
    public function APIDeletePPPSecret($address)
    {
        $res = (object) array();

        $sq = $this->APIGetPPPSecret($address)->data;
        if (count($sq) != 0) {
            $id = $sq[0]->{".id"};

            $res = $this->RequestBuilder("ppp/secret/$id", "DELETE", ["Content-Type: application/json"]);
        } else {
            $res->success = false;
            $res->message = "Address does not exists";
        }

        return $res;
    }

    //USE: Obtain a simple queue by address
    public function APIGetPPPActive()
    {
        $body = (object) array();
        $body->{".query"} = ["service=pppoe"];
        return $this->RequestBuilder("ppp/active/print", "POST", $body, ["Content-Type: application/json"]);
    }

    //
    //Utils
    //

    //USE: To build all router requests
    public function RequestBuilder($uri, $method, $body = null, $aheaders = [])
    {
        $res = (object) array();

        $curl = curl_init();

        $headers = ["Authorization: Basic $this->token"];

        $headers = array_merge($headers, $aheaders);

        curl_setopt_array($curl, [
            CURLOPT_PORT => $this->port,
            CURLOPT_URL => "http://$this->host:$this->port/rest/$uri",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => json_encode($body) ?? "",
            CURLOPT_HTTPHEADER => $headers,
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            $res->success = false;
            $res->message = $err;
        } else {
            $jres = json_decode($response);
            $res->success = !isset($jres->error);
            $res->data = $jres;
        }

        return $res;
    }

    //USE: To test router connection
    public function APIQuickTest()
    {
        $res = (object) array();

        $connection = @fsockopen($this->host, $this->port, $errno, $errstr, 1);

        if ($connection) {

            $res->success = true;
        } else {
            $res->success = false;
            $res->message = "Could not connnect.";
        }

        return $res;
    }
}

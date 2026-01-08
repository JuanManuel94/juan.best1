<?php
class Controllers
{
    public function __construct()
    {
        $this->views = new Views();
        $this->loadModel();
    }

    public function loadModel()
    {
        $model = get_class($this) . "Model";
        $modelFile = "Models/" . $model . ".php";
        if (file_exists($modelFile)) {
            require_once($modelFile);
            $this->model = new $model();
        }
    }

    public function json($data, $status = 200)
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($status);
        echo json_encode($data);
    }

    public function send($data)
    {
        echo $data;
    }
}

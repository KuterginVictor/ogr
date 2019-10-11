<?php

abstract class Controller
{
    /**
     * Name for url.
     *
     * @var string
     */
    public $controllerName = '';

    /**
     * Request method.
     *
     * @var mixed|string
     */
    protected $method = '';

    /**
     * URI list.
     *
     * @var array
     */
    public $request_uri = [];

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PATCH");
        header("Content-Type: application/json");

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->request_uri = explode('/', trim($uri, '/'));
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Handle method.
     *
     * @return mixed
     */
    public function handle()
    {
        $model = $this->request_uri[0] ?? '';
        if ($this->controllerName != $model) {
            throw new RuntimeException('API path not found.', 404);
        }

        $action = $this->getAction();
        if ($action) {
            return $this->$action();
        } else {
            throw new RuntimeException('Invalid Method', 404);
        }

    }

    /**
     * Return Actions by Method.
     *
     * @return string|null
     */
    protected function getAction()
    {
        switch ($this->method) {
            case 'GET':
                return 'show';
                break;
            case 'POST':
                return 'store';
                break;
            case 'PATCH':
                return 'update';
                break;
            default:
                return null;
        }
    }

    /**
     * Response JSON.
     *
     * @param $data
     * @param  int  $status
     * @return false|string
     */
    protected function response($data, $status = 500)
    {
        http_response_code($status);
        return json_encode($data);
    }

    abstract protected function show();

    abstract protected function store();

    abstract protected function update();
}


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
    public $uri_array = [];

    /**
     * URI.
     *
     * @var mixed|string
     */
    public $uri = '';

    /**
     * Request data.
     *
     * @var array
     */
    public $request = [];

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PATCH");
        header("Content-Type: application/json");

        $this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->uri_array = explode('/', trim($this->uri, '/'));
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Handle method.
     *
     * @return mixed
     * @throws Exception
     */
    public function handle()
    {

        if (!$this->validate_uri()) {
            throw new RuntimeException('API path not found.', 404);
        }

        if (!$this->validate_format()) {
            throw new RuntimeException('Invalid data format.', 406);
        }

        $this->prepareData();

        $action = $this->getAction();
        if ($action) {
            return $this->$action();
        } else {
            throw new RuntimeException('Invalid Method', 405);
        }

    }

    /**
     * URI validation function.
     *
     * @return bool|string
     * @throws Exception
     */
    protected function validate_uri()
    {
        switch ($this->method) {
            case 'GET':
                $match = preg_match('/^\/'.$this->controllerName.'\/[1-9]\d*\/?$/', $this->uri);
                return (bool) $match;
                break;
            case 'POST':
                $match = preg_match('/^\/'.$this->controllerName.'\/?$/', $this->uri);
                return (bool) $match;
                break;
            case 'PATCH':
                $match = preg_match('/^\/'.$this->controllerName.'\/[1-9]\d*\/?$/', $this->uri);
                return (bool) $match;
                break;
            default:
                return false;
        }
    }

    /**
     * Data format validation.
     *
     * @return bool
     */
    protected function validate_format()
    {
        $data = file_get_contents("php://input");

        if (empty($data)) {
            return true;
        }

        json_decode($data);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * Read data.
     */
    protected function prepareData()
    {
        $this->request = json_decode(file_get_contents("php://input"), true);
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


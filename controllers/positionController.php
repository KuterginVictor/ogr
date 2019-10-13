<?php

require_once "controller.php";
require_once __DIR__."/../database.php";

class PositionController extends Controller
{
    /**
     * Name for url.
     *
     * @var string
     */
    public $controllerName = 'position';

    /**
     * Show Action.
     *
     * @return false|string
     * @throws Exception
     */
    public function show()
    {
        $id = $this->uri_array[1];

        $db = DB::getInstance();

        $result = $db->prepare("SELECT * FROM positions WHERE id=:id");
        $result->execute([':id' => $id]);
        $position = $result->fetch();

        if (!$position) {
            return $this->response('ID Position not found.', 400);
        }

        $position['value'] = json_decode($position['value']);
        return $this->response($position, 200);
    }

    /**
     * Store Action.
     *
     * @return false|string
     * @throws Exception
     */
    public function store()
    {
        if (!$this->validateData()) {
            return $this->response('Data is not valid.', 400);
        }

        $data = $this->preparePosition($this->request['value']);

        $db = DB::getInstance();

        $query = $db->prepare("INSERT INTO positions (value) VALUES (:value)");
        $query->execute([':value' => json_encode($data)]);
        $id = $db->lastInsertId();

        header("Location: /position/$id");
        return $this->response('Position saved.', 201);
    }

    /**
     * Update Action.
     *
     * @return false|string
     * @throws Exception
     */
    public function update()
    {
        $id = $this->uri_array[1];

        if (!$this->validateData()) {
            return $this->response('Data is not valid.', 400);
        }

        $data = $this->preparePosition($this->request['value']);

        $db = DB::getInstance();
        $query = $db->prepare("UPDATE positions SET value=:value WHERE id=:id");
        $query->execute([':value' => json_encode($data), ':id' => $id]);
        if (!$query->rowCount()) {
            return $this->response('ID Position not found.', 400);
        }

        return $this->response('Position updated.', 200);
    }

    /**
     * Validate request data.
     *
     * @return bool
     */
    private function validateData()
    {

        if (!isset($this->request['value'])) {
            return false;
        }

        foreach ($this->request['value'] as $value) {
            $match = preg_match('/^[WB][KQRBNP][A-H][1-8]$/', strtoupper($value));
            if (!$match) {
                return false;
            }
        }
        return true;
    }

    /**
     * Prepare positions for storing.
     *
     * @param $data
     * @return mixed
     */
    private function preparePosition($data)
    {
        $data = array_map(function ($a) {
            return strtoupper($a);
        }, $data);

        return $data;
    }
}
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
        $id = $this->request_uri[1] ?? '';
        if (intval($id) > 0) {
            $db = DB::getInstance();

            $result = $db->prepare("SELECT * FROM positions WHERE id=:id");
            $result->execute([':id' => $id]);
            $position = $result->fetch();

            if ($position) {
                return $this->response($position, 200);
            } else {
                return $this->response('ID position not found.', 400);
            }

        }
        return $this->response('Wrong ID parameter.', 400);
    }

    /**
     * Store Action.
     *
     * @return false|string
     * @throws Exception
     */
    public function store()
    {
        $data = $this->getData();
        if ($this->validateData($data)) {
            $db = DB::getInstance();

            $query = $db->prepare("INSERT INTO positions (value) VALUES (:value)");
            $query->execute([':value' => $data['position']]);
            $id = $db->lastInsertId();

            header("Location: /position/$id");
            return $this->response('Position saved.', 201);
        }
        return $this->response('Wrong data.', 400);
    }

    /**
     * Update Action.
     *
     * @return false|string
     * @throws Exception
     */
    public function update()
    {
        $id = $this->request_uri[1] ?? '';
        if (intval($id) > 0) {
            $data = $this->getData();
            if ($this->validateData($data)) {
                $db = DB::getInstance();
                $query = $db->prepare("UPDATE positions SET value=:value WHERE id=:id");
                $query->execute([':value' => $data['position'], ':id' => $id]);
                if ($query->rowCount()) {
                    return $this->response('Position updated.', 200);
                } else {
                    return $this->response('Position not found.', 400);
                }
            }
            return $this->response('Wrong position.', 400);
        }
        return $this->response('Wrong ID parameter.', 400);
    }

    /**
     * Get data for POST|PATCH methods.
     *
     * @return mixed
     */
    private function getData()
    {
        return json_decode(file_get_contents("php://input"), true);
    }

    /**
     * Validate postition data.
     *
     * @param $data
     * @return bool
     */
    private function validateData($data)
    {
        if (isset($data['position'])) {
            $is_match = preg_match('/^[a-hA-H][1-8]$/', $data['position']);
            if ($is_match) {
                return true;
            }
        }
        return false;
    }
}
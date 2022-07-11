<?php

require_once('db.php');

class Hero {
    private $db;
    function __construct()
    {
        $this->db = new mysqli(HOST, USER, PASS, DB);
        if ($this->db->connect_error) {
            die("Koneksi Gagal");
        }
    }
    function __destruct()
    {
        $this->db->close();
    }

    function validate($data) 
    {
        if ( ! is_null($data['nama_hero']) && ! filter_var($data['nama_hero'], FILTER_SANITIZE_STRING)) {
            http_response_code(400);
            die('Hero Name must be filled with string format.');
        }
        if ( ! is_null($data['skill1_name']) && ! filter_var($data['skill1_name'], FILTER_SANITIZE_STRING)) {
            http_response_code(400);
            die('1st Skill Name must be filled with string format.');
        }
        if ( ! is_null($data['skill2_name']) && ! filter_var($data['skill2_name'], FILTER_SANITIZE_STRING)) {
            http_response_code(400);
            die('2nd Skill Name must be filled with string format.');
        }
        if ( ! is_null($data['ulti_name']) && ! filter_var($data['ulti_name'], FILTER_SANITIZE_STRING)) {
            http_response_code(400);
            die('Ultimate Name must be filled with string format.');
        }
    }

    function read($sort = 'ASC', $cari = null, $begin = 0)
    {
        $sql = "SELECT * FROM hero WHERE nama_hero LIKE '%{$cari}%' ORDER BY nama_hero {$sort} LIMIT {$begin}, 8";
        $result = $this->db->query($sql);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            array_push($data, $row);
        }

        header("Content-Type: application/json");
        echo json_encode($data);
    }

    function detail($id_hero)
    {
        $sql = "SELECT * FROM hero WHERE id_hero = {$id_hero}";
        $result = $this->db->query($sql);
        $data = $result->fetch_assoc();

        header("Content-Type: application/json");
        echo json_encode($data);
    }

    function create($data) 
    {
        foreach ($data as $key => $value) {
            $value = is_array($value) ? trim(implode(',', $value)) : trim($value);
            $data[$key] = (strlen($value) > 0 ? $value : NULL);
        }
        Hero::validate($data);
        $query = "INSERT INTO hero VALUES(NULL,?,?,?,?,?,?,?,?)";
        $sql = $this->db->prepare($query);
        $sql->bind_param(
            'sissssss',
            $data['nama_hero'],
            $data['role'],
            $data['skill1_name'],
            $data['skill1_desc'],
            $data['skill2_name'],
            $data['skill2_desc'],
            $data['ulti_name'],
            $data['ulti_desc']
        );
        try {
            $sql->execute();
        } catch (\Exception $e) {
            $sql->close();
            http_response_code(500);
            die($e->getMessage());
        }
        $id_hero = $sql->insert_id;
        $sql->close();
        return $id_hero;
    }

    function update($data) 
    {
        foreach ($data as $key => $value) {
            $value = is_array($value) ? trim(implode(',', $value)) : trim($value);
            $data[$key] = (strlen($value) > 0 ? $value : NULL);
        }
        Hero::validate($data);
        $query = "UPDATE hero SET nama_hero = ?, role = ?, skill1_name = ?, skill1_desc = ?, skill2_name = ?, skill2_desc = ?, ulti_name = ?, ulti_desc = ? WHERE id_hero=?";
        $sql = $this->db->prepare($query);
        $sql->bind_param(
            'sissssssi',
            $data['nama_hero'],
            $data['role'],
            $data['skill1_name'],
            $data['skill1_desc'],
            $data['skill2_name'],
            $data['skill2_desc'],
            $data['ulti_name'],
            $data['ulti_desc'],
            $data['id_hero']
        );
        try {
            $sql->execute();
        } catch (\Exception $e) {
            $sql->close();
            http_response_code(500);
            die($e->getMessage());
        }
        $sql->close();
    }

    function delete($id_hero)
    {
        $query = "DELETE FROM hero WHERE id_hero = {$id_hero}";
        $sql = $this->db->prepare($query);
        try {
            $sql->execute();
        } catch (\Exception $e) {
            $sql->close();
            http_response_code(500);
            die($e->getMessage());
        }
        $sql->close();
    }
}

$film = new Hero();
switch ($_GET['action']) {
    case 'create':
        $id_hero = $film->create($_POST);
        move_uploaded_file($_FILES['art']['tmp_name'], "img/{$id_hero}.jpg");
        break;
    case 'detail':
        $film->detail($_GET['id_hero']);
        break;
    case 'update':
        $film->update($_POST);
        move_uploaded_file($_FILES['art']['tmp_name'], "img/{$_POST['id_hero']}.jpg");
        break;
    case 'delete':
        $film->delete($_GET['id_hero']);
        unlink("img/{$_GET['id_hero']}.jpg");
        header("Location: " . $_SERVER['HTTP_REFERER']);
        break;
    default:
        $film->read((isset($_GET['sort']) ? $_GET['sort'] : 'ASC'), (isset($_GET['cari']) ? $_GET['cari'] : null), (isset($_GET['begin']) ? $_GET['begin'] : 0));
        break;
}
?>
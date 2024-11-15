<?php
session_start();

require_once 'google-config.php';
include 'db.php'; 

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (!isset($token['error'])) {
        $client->setAccessToken($token['access_token']);
        
        // Ambil informasi user
        $google_service = new Google\Service\Oauth2($client);
        $data = $google_service->userinfo->get();

        // Ambil data user dari respon Google
        $email = $data['email'];
        $name = $data['name'];
        $google_id = $data['id'];

        // Cek jika user sudah terdaftar
        $query = "SELECT * FROM users WHERE google_id = $1";
        $result = pg_query_params($conn, $query, array($google_id));

        if ($result && pg_num_rows($result) > 0) {
            // User sudah ada, langsung login
            $row = pg_fetch_assoc($result);
            $_SESSION['id'] = $row['id'];
        } else {
            // Jika belum terdaftar, simpan data baru ke database
            $insert_query = "INSERT INTO users (fullname, email, google_id, is_verified) VALUES ($1, $2, $3, TRUE) RETURNING id";
            $result = pg_query_params($conn, $insert_query, array($name, $email, $google_id));
            
            if ($result) {
                $row = pg_fetch_assoc($result);
                $_SESSION['id'] = $row['id'];
            } else {
                echo "Failed to save user data: " . pg_last_error($conn);
                exit();
            }
        }

        header('Location: index-2.php'); 
        exit();
    } else {
        echo "Token Error: " . $token['error'];
        exit();
    }
} else {
    header('Location: login.php');
    exit();
}
?>
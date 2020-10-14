<?php

function get_uid($token)
{
    $active = 1;
    $CONFIG = include "config.php";
    $mysqli = new \mysqli($CONFIG['mysql']['host'], $CONFIG['mysql']['user'], $CONFIG['mysql']['pass'], $CONFIG['mysql']['dbname']);
    if ($mysqli->connect_errno)
        return false;

    if (! ($stmt = $mysqli->prepare('SELECT * FROM users WHERE token = ? AND active = ?')))
        return false;
    if (! ($stmt->bind_param('si', $token, $active)))
        return false;
    if (! $stmt->execute())
        return false;
    if (! ($result = $stmt->get_result()))
        return false;

    $uid = $result->fetch_assoc()['id'];
    $stmt->close();
    $mysqli->close();
    return $uid;
}

function get_username($uid)
{
    $active = 1;
    $CONFIG = include "config.php";
    $mysqli = new \mysqli($CONFIG['mysql']['host'], $CONFIG['mysql']['user'], $CONFIG['mysql']['pass'], $CONFIG['mysql']['dbname']);
    if ($mysqli->connect_errno)
        return false;

    if (! ($stmt = $mysqli->prepare('SELECT * FROM users WHERE id = ? AND active = ?')))
        return false;
    if (! ($stmt->bind_param('ii', $uid, $active)))
        return false;
    if (! $stmt->execute())
        return false;
    if (! ($result = $stmt->get_result()))
        return false;

    $username = $result->fetch_assoc()['username'];
    $stmt->close();
    $mysqli->close();
    return $username;
}

function check_valid_token($token)
{
    $active = 1;
    $CONFIG = include "config.php";
    $mysqli = new \mysqli($CONFIG['mysql']['host'], $CONFIG['mysql']['user'], $CONFIG['mysql']['pass'], $CONFIG['mysql']['dbname']);
    if ($mysqli->connect_errno)
        return false;

    if (! ($stmt = $mysqli->prepare('SELECT * FROM users WHERE token = ? AND active = ?')))
        return false;
    if (! ($stmt->bind_param('si', $token, $active)))
        return false;
    if (! $stmt->execute())
        return false;
    if (! $stmt->get_result())
        return false;

    $stmt->close();
    $mysqli->close();
    return true;
}

function is_file_owner($token, $filename)
{
    if (! ($uid = get_uid($token)))
        return false;
    $CONFIG = include "config.php";
    $mysqli = new \mysqli($CONFIG['mysql']['host'], $CONFIG['mysql']['user'], $CONFIG['mysql']['pass'], $CONFIG['mysql']['dbname']);
    if ($mysqli->connect_errno)
        return false;

    if (! ($stmt = $mysqli->prepare('SELECT * FROM files WHERE filename = ?')))
        return false;
    if (! ($stmt->bind_param('s', $filename)))
        return false;
    if (! $stmt->execute())
        return false;
    if (! ($result = $stmt->get_result()))
        return false;

    if ($result->fetch_assoc()['uploader'] != $uid)
        return false;

    $stmt->close();
    $mysqli->close();
    return true;
}

function log_file($token, $filename)
{
    if (! ($uid = get_uid($token)))
        return false;
    $CONFIG = include "config.php";
    $mysqli = new \mysqli($CONFIG['mysql']['host'], $CONFIG['mysql']['user'], $CONFIG['mysql']['pass'], $CONFIG['mysql']['dbname']);
    if ($mysqli->connect_errno)
        return false;

    if (! ($stmt = $mysqli->prepare('INSERT INTO files(id, filename, uploader) VALUES (NULL, ?, ?)')))
        return false;
    if (! ($stmt->bind_param('si', $filename, $uid)))
        return false;
    if (! $stmt->execute())
        return false;

    $stmt->close();
    $mysqli->close();
    return true;
}

function delete_file($filename)
{
    $CONFIG = include "config.php";
    $mysqli = new \mysqli($CONFIG['mysql']['host'], $CONFIG['mysql']['user'], $CONFIG['mysql']['pass'], $CONFIG['mysql']['dbname']);
    if ($mysqli->connect_errno)
        return false;

    if (! ($stmt = $mysqli->prepare('DELETE FROM files WHERE filename = ?')))
        return false;
    if (! ($stmt->bind_param('s', $filename)))
        return false;
    if (! $stmt->execute())
        return false;

    $stmt->close();
    $mysqli->close();
    return true;
}

function get_files()
{
    $CONFIG = include "config.php";
    $mysqli = new \mysqli($CONFIG['mysql']['host'], $CONFIG['mysql']['user'], $CONFIG['mysql']['pass'], $CONFIG['mysql']['dbname']);
    if ($mysqli->connect_errno)
        return false;

    if (! $query = $mysqli->query("SELECT * FROM files"))
        return false;

    $files = $query->fetch_all(MYSQLI_ASSOC);
    $query->close();
    $mysqli->close();
    return $files;
}
<?php

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
    return true;
}

function log_file($token, $filename)
{
    $active = 1;
    $CONFIG = include "config.php";
    $mysqli = new \mysqli($CONFIG['mysql']['host'], $CONFIG['mysql']['user'], $CONFIG['mysql']['pass'], $CONFIG['mysql']['dbname']);
    if ($mysqli->connect_errno)
        return false;

    $uid = - 1;
    if (! ($usrstmt = $mysqli->prepare('SELECT * FROM users WHERE token = ? AND active = ?')))
        return false;
    if (! ($usrstmt->bind_param('si', $token, $active)))
        return false;
    if (! $usrstmt->execute())
        return false;
    if (! ($result = $usrstmt->get_result()))
        return false;
    $uid = $result->fetch_assoc()['id'];
    $usrstmt->close();

    if (! ($filestmt = $mysqli->prepare('INSERT INTO files(id, filename, uploader) VALUES (NULL, ?, ?)')))
        return false;
    if (! ($filestmt->bind_param('si', $filename, $uid)))
        return false;
    if (! $filestmt->execute())
        return false;

    $filestmt->close();
    return true;
}

function get_username($uid)
{
    $active = 1;
    $CONFIG = include "config.php";
    $mysqli = new \mysqli($CONFIG['mysql']['host'], $CONFIG['mysql']['user'], $CONFIG['mysql']['pass'], $CONFIG['mysql']['dbname']);
    if ($mysqli->connect_errno)
        return false;

    if (! ($usrstmt = $mysqli->prepare('SELECT * FROM users WHERE id = ? AND active = ?')))
        return false;
    if (! ($usrstmt->bind_param('ii', $uid, $active)))
        return false;
    if (! $usrstmt->execute())
        return false;
    if (! ($result = $usrstmt->get_result()))
        return false;
    $username = $result->fetch_assoc()['username'];
    $usrstmt->close();
    return $username;
}

function delete_file($token, $filename)
{
    $active = 1;
    $CONFIG = include "config.php";
    $mysqli = new \mysqli($CONFIG['mysql']['host'], $CONFIG['mysql']['user'], $CONFIG['mysql']['pass'], $CONFIG['mysql']['dbname']);
    if ($mysqli->connect_errno)
        return false;
    $uid = - 1;
    if (! ($usrstmt = $mysqli->prepare('SELECT * FROM users WHERE token = ? AND active = ?')))
        return false;
    if (! ($usrstmt->bind_param('si', $token, $active)))
        return false;
    if (! $usrstmt->execute())
        return false;
    if (! ($result = $usrstmt->get_result()))
        return false;
    $uid = $result->fetch_assoc()['id'];
    $usrstmt->close();

    if (! ($filestmt = $mysqli->prepare('DELETE FROM files WHERE filename = ? AND uploader = ?')))
        return false;
    if (! ($filestmt->bind_param('si', $filename, $uid)))
        return false;
    if (! $filestmt->execute())
        return false;
    if (! $filestmt->get_result())
        return false;

    $filestmt->close();
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

    return $query->fetch_all(MYSQLI_ASSOC);
}
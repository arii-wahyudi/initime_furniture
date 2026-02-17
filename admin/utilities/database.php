<?php

/**
 * Database utility functions for common CRUD operations
 * Centralizes mysqli prepare/bind/execute patterns
 */

/**
 * Execute a SELECT query and return results
 * @param mysqli $conn Database connection
 * @param string $query SQL query with ? placeholders
 * @param array $params Values to bind (with types as keys, e.g., ['i' => $id, 's' => $name])
 * @param string $types Type string for mysqli_stmt_bind_param (e.g., 'is', 'ssi')
 * @return array|null Single row as associative array, or null if no result
 */
function db_fetch_one($conn, $query, $params, $types)
{
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) return null;

    $bind_values = array_merge([$types], array_values($params));
    call_user_func_array('mysqli_stmt_bind_param', array_merge([$stmt], $bind_values));

    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return null;
    }

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    mysqli_stmt_close($stmt);

    return $row;
}

/**
 * Execute a SELECT query and return all results
 * @param mysqli $conn Database connection
 * @param string $query SQL query with ? placeholders
 * @param array $params Values to bind
 * @param string $types Type string for mysqli_stmt_bind_param
 * @return array Array of rows
 */
function db_fetch_all($conn, $query, $params, $types)
{
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) return [];

    $bind_values = array_merge([$types], array_values($params));
    call_user_func_array('mysqli_stmt_bind_param', array_merge([$stmt], $bind_values));

    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return [];
    }

    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    mysqli_free_result($result);
    mysqli_stmt_close($stmt);

    return $rows;
}

/**
 * Execute an INSERT query
 * @param mysqli $conn Database connection
 * @param string $query SQL query with ? placeholders
 * @param array $params Values to bind
 * @param string $types Type string for mysqli_stmt_bind_param
 * @return int Inserted ID, or 0 on failure
 */
function db_insert($conn, $query, $params, $types)
{
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) return 0;

    $bind_values = array_merge([$types], array_values($params));
    call_user_func_array('mysqli_stmt_bind_param', array_merge([$stmt], $bind_values));

    $success = mysqli_stmt_execute($stmt);
    $id = $success ? mysqli_insert_id($conn) : 0;
    mysqli_stmt_close($stmt);

    return $id;
}

/**
 * Execute an UPDATE query
 * @param mysqli $conn Database connection
 * @param string $query SQL query with ? placeholders
 * @param array $params Values to bind
 * @param string $types Type string for mysqli_stmt_bind_param
 * @return int Affected rows
 */
function db_update($conn, $query, $params, $types)
{
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) return 0;

    $bind_values = array_merge([$types], array_values($params));
    call_user_func_array('mysqli_stmt_bind_param', array_merge([$stmt], $bind_values));

    $success = mysqli_stmt_execute($stmt);
    $affected = $success ? mysqli_stmt_affected_rows($stmt) : 0;
    mysqli_stmt_close($stmt);

    return $affected;
}

/**
 * Execute a DELETE query
 * @param mysqli $conn Database connection
 * @param string $query SQL query with ? placeholders
 * @param array $params Values to bind
 * @param string $types Type string for mysqli_stmt_bind_param
 * @return int Affected rows
 */
function db_delete($conn, $query, $params, $types)
{
    return db_update($conn, $query, $params, $types);
}

/**
 * Upsert (insert or update) a setting
 * @param mysqli $conn Database connection
 * @param string $name Setting name
 * @param string $value Setting value
 * @return bool Success
 */
function db_upsert_setting($conn, $name, $value)
{
    $row = db_fetch_one($conn, "SELECT id FROM settings WHERE nama_setting = ? LIMIT 1", [$name], 's');

    if ($row) {
        return db_update($conn, "UPDATE settings SET isi = ? WHERE id = ?", [$value, $row['id']], 'si') > 0;
    } else {
        return db_insert($conn, "INSERT INTO settings (nama_setting, isi) VALUES (?, ?)", [$name, $value], 'ss') > 0;
    }
}

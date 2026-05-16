<?php

function db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            db_config('host'),
            (int) db_config('port', 3306),
            db_config('dbname'),
            db_config('charset', 'utf8mb4')
        );

        $pdo = new PDO($dsn, db_config('username'), db_config('password'), [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    return $pdo;
}

function db_query(string $sql, array $params = []): PDOStatement
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

function db_all(string $sql, array $params = []): array
{
    return db_query($sql, $params)->fetchAll();
}

function db_one(string $sql, array $params = []): ?array
{
    $data = db_query($sql, $params)->fetch();
    return $data ?: null;
}

function db_execute(string $sql, array $params = []): bool
{
    return db_query($sql, $params)->rowCount() >= 0;
}

function db_insert(string $table, array $data): int
{
    $columns = array_keys($data);
    $placeholders = array_map(fn($c) => ':' . $c, $columns);

    $sql = sprintf(
        'INSERT INTO %s (%s) VALUES (%s)',
        $table,
        implode(', ', $columns),
        implode(', ', $placeholders)
    );

    db_query($sql, $data);
    return (int) db()->lastInsertId();
}

function db_update(string $table, array $data, string $where, array $whereParams = []): bool
{
    $set = [];
    foreach ($data as $column => $value) {
        $set[] = $column . ' = :u_' . $column;
    }

    $params = [];
    foreach ($data as $column => $value) {
        $params['u_' . $column] = $value;
    }

    $params = array_merge($params, $whereParams);

    $sql = sprintf('UPDATE %s SET %s WHERE %s', $table, implode(', ', $set), $where);
    db_query($sql, $params);
    return true;
}

function db_delete(string $table, string $where, array $params = []): bool
{
    $sql = sprintf('DELETE FROM %s WHERE %s', $table, $where);
    db_query($sql, $params);
    return true;
}

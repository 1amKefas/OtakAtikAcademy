<?php
$username = 'system'; // ganti sesuai Oracle
$password = '12345678'; // ganti sesuai Oracle
$host = '(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=localhost)(PORT=1521))(CONNECT_DATA=(SID=xe)))';

$conn = @oci_connect($username, $password, $host);
if (!$conn) {
    $e = oci_error();
    die("Koneksi gagal: " . $e['message'] . "\n");
}

echo "Koneksi berhasil!\n";

// Cek semua tabel di user schema
$sql = 'SELECT table_name FROM user_tables';
$stid = oci_parse($conn, $sql);
oci_execute($stid);

echo "Tabel di schema:\n";
while ($row = oci_fetch_assoc($stid)) {
    echo $row['TABLE_NAME'] . "\n";
}

oci_free_statement($stid);
oci_close($conn);

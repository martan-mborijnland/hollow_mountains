<?php declare(strict_types=1);

namespace App\Utility;

use PDO;
use PDOException;
use PDOStatement;
use App\Core\Configuration;
use App\Utility\Session;



class Functions
{
    public static function checkPermissions(array $permissions): bool
    {
        return in_array(Session::get('user')['rol'], $permissions);
    }

    public static function jsRedirect(string $url): void
    {
        echo "<script>window.location.href = '" . $url . "'</script>";
    }

    public static function drawTable($headers, $rows, $direction='horizontal'): void
    {
        if ($direction === 'horizontal') {
            echo "<table><thead><tr>";
            foreach ($headers as $header) {
                echo "<th>". str_replace('_', ' ', ucfirst($header)) ."</th>";
            }
            echo "</tr></thead><tbody>";
            foreach ($rows as $row) {    
                echo "<tr>";
                foreach ($headers as $header) {
                    echo "<td>". $row[$header] ."</td>";
                }
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else if ($direction === 'vertical') {
            echo "<table><thead>";
            foreach ($rows as $row) {
                foreach ($headers as $header) {
                    echo "<tr>";
                    echo "<th>". str_replace('_', ' ', ucfirst($header)) ."</th>";
                    echo "<td>".  $row[$header] ."</td>";
                    echo "</tr>";
                }
            }
            echo "</tbody></table>";
        }
    }

    public static function convertToTitle(string $string): string
    {
        return ucwords(str_replace('.', ' ', implode(' ', preg_split('/(?=[A-Z])/', $string))));
    }
}
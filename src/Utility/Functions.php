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
        try {
            return in_array(Session::get('user')['rol'], $permissions);
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function jsRedirect(string $url): void
    {
        echo "<script>window.location.href = '" . $url . "'</script>";
        echo "Als je dit ziet, ga je naar: <a href='" . $url . "'>" . $url . "</a>";
        die();
    }

    public static function drawTable($headers, $rows, $direction='horizontal'): void
    {
        if ($direction === 'horizontal') {
            echo "<table><thead><tr>";
            foreach ($headers as $header) {
                echo "<th>". str_replace('_', ' ', ucfirst($header)) ."</th>";
            }
            echo "</tr></thead><tbody>";
            
            if (empty($rows)) {
                echo "<tr><td colspan=\"" . count($headers) . "\">No data available</td></tr>";
            } else {
                foreach ($rows as $row) {    
                    echo "<tr>";
                    foreach ($headers as $header) {
                        echo "<td>". $row[$header] ."</td>";
                    }
                    echo "</tr>";
                }
            }
            
            echo "</tbody></table>";
        } else if ($direction === 'vertical') {
            echo "<table><tbody>";
            
            if (empty($rows)) {
                foreach ($headers as $header) {
                    echo "<tr>";
                    echo "<th>". str_replace('_', ' ', ucfirst($header)) ."</th>";
                    echo "<td>No data available</td>";
                    echo "</tr>";
                }
            } else {
                foreach ($rows as $row) {
                    foreach ($headers as $header) {
                        echo "<tr>";
                        echo "<th>". str_replace('_', ' ', ucfirst($header)) ."</th>";
                        echo "<td>". $row[$header] ."</td>";
                        echo "</tr>";
                    }
                }
            }
            
            echo "</tbody></table>";
        }
    }

    public static function convertToTitle(string $string): string
    {
        return ucwords(str_replace('.', ' ', implode(' ', preg_split('/(?=[A-Z])/', $string))));
    }

    public static function print_p($data): void
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }

    public static function displayError(?string $message): void
    {
        if (empty($message) || $message === null || $message === '') {
            return;
        }

        echo '<span class="error">' . $message . '<p class="close" onclick="this.parentElement.remove();">x</p></span>';
    }

    public static function displaySuccess(?string $message): void
    {
        if (empty($message) || $message === null || $message === '') {
            return;
        }

        echo '<span class="success">' . $message . '<p class="close" onclick="this.parentElement.remove();">x</p></span>';
    }

    public static function drawSidebar($options): void
    {
        $currentPage = $_GET['page'];

        echo '<div class="sidebar">';
        echo '<ul>';
        foreach ($options as $option) {
            if ($option['page'] === $currentPage) {
                echo '<li class="active"><a href="?page=' . $option['page'] . '">' . $option['label'] . '</a></li>';
            } else {
                echo '<li><a href="?page=' . $option['page'] . '">' . $option['label'] . '</a></li>';
            }
        }
        echo '</ul>';
        echo '</div>';
    }
}
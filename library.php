<?php

require 'vendor/symfony/yaml-3.4.37/Inline.php';
require 'vendor/symfony/yaml-3.4.37/Parser.php';
require 'vendor/symfony/yaml-3.4.37/Yaml.php';
require 'vendor/symfony/yaml-3.4.37/Exception/ExceptionInterface.php';
require 'vendor/symfony/yaml-3.4.37/Exception/RuntimeException.php';
require 'vendor/symfony/yaml-3.4.37/Exception/DumpException.php';
require 'vendor/symfony/yaml-3.4.37/Exception/ParseException.php';

use Symfony\Component\Yaml\Yaml;

class SubstrSearch {
    const SETTINGS_FILE_NAME = 'substr-settings.yaml';

    protected static $settings = [];

    public static function read_settings($file_name = null) {
        // Можно передать другое имя файла для тестирования
        if (is_null($file_name))
            $file_name = self::SETTINGS_FILE_NAME;
        self::$settings = Yaml::parseFile(self::SETTINGS_FILE_NAME);
        // var_dump(self::$settings);
    }

    public function search($file_name, $substr) {
        $this -> check_constraints($file_name);

        $file = fopen($file_name, 'r');
        $result = [];
        $prev_line = '';
        $line_index = 0;

        while (!feof($file)) {
            $data = fread($file, 1000);
            $paragraphs = explode("\n", $data);
            $paragraphs[0] = $prev_line . $paragraphs[0];

            $last_index = count($paragraphs) - 1;
            $prev_line = $paragraphs[$last_index];
            unset($paragraphs[$last_index]);

            foreach ($paragraphs as $k => $v) {
                $r = $this -> search_substr_in_str($substr, $v, $k + $line_index);
                if (!is_null($r))
                    $result[] = $r;
            }

            $line_index += $last_index;
        }

        $r = $this -> search_substr_in_str($substr, $prev_line, $line_index);
        if (!is_null($r))
            $result[] = $r;

        return $result;
    }

    protected function search_substr_in_str($substr, $str, $line_index) {
        $pos = mb_strpos($str, $substr, 0);
        if ($pos !==false) {
            $col = mb_substr_count($str, $substr);
            
            return [
                'index' => $line_index,
                'substr' => $substr,
                'str' => $str,
                'strpos' => $pos,
                'substr-count' => $col
            ];
        }

        return null;
    }

    protected function check_constraints($file_name) {
        $file_size = filesize($file_name);
        if ($file_size > self::$settings['max-file-size'])
            throw new InvalidArgumentException('Exceeds max file size');

        $mime = mime_content_type($file_name);
        if (!in_array($mime, self::$settings['mime-types']))
            throw new InvalidArgumentException("Unsupported MIME type: {$mime}");
    }
}

SubstrSearch::read_settings();
